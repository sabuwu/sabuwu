<?php
/**
 * Integration checks against the real WordPress post, metadata and REST APIs.
 *
 * Run only on a local/development installation with the Sabuwu theme active:
 * docker compose exec -T wordpress wp --skip-plugins eval-file - < tests/portfolio.php
 *
 * Fixtures are isolated in an InnoDB transaction and removed in finally. The
 * script never changes theme settings or existing content. Autoincrement IDs
 * can advance even when the transaction is rolled back.
 */

if (!defined('WP_CLI') || !WP_CLI || !defined('ABSPATH')) {
    exit('Execute este arquivo pelo WP-CLI em uma instalação local.');
}

if (!in_array(wp_get_environment_type(), ['local', 'development'], true)) {
    WP_CLI::error('Os testes exigem um ambiente local ou development.');
}

if (!function_exists('sabuwu_get_projects')) {
    WP_CLI::error('Ative o tema Sabuwu antes de executar os testes.');
}

global $wpdb;

// Never rely on rollback for nontransactional WordPress tables.
$tables = [$wpdb->posts, $wpdb->postmeta, $wpdb->options, $wpdb->terms, $wpdb->term_taxonomy, $wpdb->term_relationships];
$placeholders = implode(', ', array_fill(0, count($tables), '%s'));
$table_status = $wpdb->get_results($wpdb->prepare("SHOW TABLE STATUS WHERE Name IN ($placeholders)", $tables));
if (count($table_status) !== count($tables)) {
    WP_CLI::error('Não foi possível verificar as tabelas antes de iniciar a transação.');
}
foreach ($table_status as $table) {
    if (strtolower((string) $table->Engine) !== 'innodb') {
        WP_CLI::error('Os testes exigem tabelas InnoDB para garantir o rollback.');
    }
}

$administrators = get_users(['role' => 'administrator', 'number' => 1, 'fields' => 'ID', 'orderby' => 'ID']);
if (!$administrators) {
    WP_CLI::error('Os testes exigem um administrador existente; nenhum usuário será criado.');
}

$original_user = get_current_user_id();
$original_post_data = $_POST;
$original_cache_suspension = wp_suspend_cache_addition();
$original_theme_mods = get_theme_mods();
$fixtures = [];
$checks = 0;
$failure = null;
$transaction_started = false;

$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

// Limit only this process's portfolio queries to fixtures, without hiding or
// updating any preexisting projects in the database.
$isolate_projects = static function (WP_Query $query) use (&$fixtures): void {
    if ($query->get('post_type') === 'sabuwu_project' && !$query->is_main_query()) {
        $query->set('post__in', $fixtures ?: [0]);
        $query->set('cache_results', false);
    }
};
$show_demo = static function (): bool { return true; };
$hide_demo = static function (): bool { return false; };

$save_editor = static function (int $post_id, array $fields, ?string $nonce): void {
    $before = $_POST;
    $_POST = ['sabuwu_project' => wp_slash($fields)];
    if ($nonce !== null) {
        $_POST['_sabuwu_project_nonce'] = $nonce;
    }
    try {
        $result = wp_update_post(['ID' => $post_id], true);
        if (is_wp_error($result)) {
            throw new RuntimeException('A gravação da fixture pelo editor falhou.');
        }
    } finally {
        $_POST = $before;
    }
};

try {
    if ($wpdb->query('START TRANSACTION') === false) {
        throw new RuntimeException('Não foi possível iniciar a transação dos testes.');
    }
    $transaction_started = true;
    wp_suspend_cache_addition(true);
    wp_set_current_user((int) $administrators[0]);

    $type = get_post_type_object('sabuwu_project');
    $assert($type !== null && $type->public && $type->show_in_rest, 'Projetos precisam ser públicos e estar disponíveis na REST API.');
    $assert(post_type_supports('sabuwu_project', 'editor') && post_type_supports('sabuwu_project', 'custom-fields'), 'O editor de blocos precisa do conteúdo e dos campos REST registrados.');
    $assert(post_type_supports('sabuwu_project', 'revisions') && post_type_supports('sabuwu_project', 'page-attributes'), 'Projetos precisam de revisões e ordenação nativa.');
    $registered_meta = get_registered_meta_keys('post', 'sabuwu_project');
    $assert(isset($registered_meta['_sabuwu_tags'], $registered_meta['_sabuwu_metric_1_value'])
        && $registered_meta['_sabuwu_tags']['show_in_rest']
        && $registered_meta['_sabuwu_metric_1_value']['revisions_enabled'], 'Metadados precisam de REST e revisão.');

    $create_draft = static function (string $title, int $order, int $days_ago, string $excerpt = '') use (&$fixtures): int {
        $date = gmdate('Y-m-d H:i:s', time() - $days_ago * DAY_IN_SECONDS);
        $id = wp_insert_post([
            'post_type' => 'sabuwu_project',
            'post_status' => 'draft',
            'post_title' => $title,
            'post_content' => '<p>Estudo de caso de integração com <strong>conteúdo</strong> público.</p>',
            'post_excerpt' => $excerpt,
            'post_date' => $date,
            'post_date_gmt' => $date,
            'menu_order' => $order,
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ], true);
        if (is_wp_error($id) || !$id) {
            throw new RuntimeException('Não foi possível criar uma fixture de projeto.');
        }
        $fixtures[] = (int) $id;
        return (int) $id;
    };

    $older = $create_draft('Sabuwu integration — older', 0, 4, '<b>Resumo</b> seguro');
    $newer = $create_draft('Sabuwu integration — newer', 0, 3);
    $last = $create_draft('Sabuwu integration — ordered last', 2, 2);
    $draft = $create_draft('Sabuwu integration — remains draft', -100, 1);
    $protected = $create_draft('Sabuwu integration — protected', -200, 1);

    add_action('pre_get_posts', $isolate_projects, 99);
    add_filter('theme_mod_sabuwu_projects_show_demo', $show_demo, 99);
    $assert(sabuwu_get_projects() === sabuwu_default_projects(), 'Rascunhos não devem substituir os exemplos.');
    add_filter('theme_mod_sabuwu_projects_show_demo', $hide_demo, 100);
    $assert(sabuwu_get_projects() === [], 'Desativar os exemplos deve deixar a lista vazia enquanto só houver rascunhos.');
    remove_filter('theme_mod_sabuwu_projects_show_demo', $hide_demo, 100);

    update_post_meta($older, '_sabuwu_tags', 'safe-before');
    $save_editor($older, ['_sabuwu_tags' => 'changed-without-nonce'], null);
    $assert(get_post_meta($older, '_sabuwu_tags', true) === 'safe-before', 'Salvar sem nonce não pode alterar metadados.');
    $save_editor($older, ['_sabuwu_tags' => 'changed-with-invalid-nonce'], 'invalid-test-nonce');
    $assert(get_post_meta($older, '_sabuwu_tags', true) === 'safe-before', 'Salvar com nonce inválido não pode alterar metadados.');

    wp_set_current_user(0);
    $save_editor($older, ['_sabuwu_tags' => 'changed-without-capability'], wp_create_nonce('sabuwu_save_project'));
    $assert(get_post_meta($older, '_sabuwu_tags', true) === 'safe-before', 'Um usuário sem permissão não pode editar os campos do projeto.');
    $assert(!current_user_can('edit_post_meta', $older, '_sabuwu_tags'), 'A permissão dos metadados protegidos deve negar escrita anônima.');

    wp_set_current_user((int) $administrators[0]);
    $assert(current_user_can('edit_post_meta', $older, '_sabuwu_tags'), 'O administrador deve poder editar os metadados protegidos.');
    $save_editor($older, [
        '_sabuwu_tags' => ' React, Python, React, , 0 ',
        '_sabuwu_demo_url' => 'javascript:alert(1)',
        '_sabuwu_repository_url' => 'https://example.invalid/repository?x=1&y=2',
        '_sabuwu_case_url' => '',
        '_sabuwu_metric_1_value' => '<120ms',
        '_sabuwu_metric_1_label' => '<script>alert(1)</script>latência <b>média</b>',
    ], wp_create_nonce('sabuwu_save_project'));
    $assert(get_post_meta($older, '_sabuwu_tags', true) === 'React, Python, 0', 'Tecnologias devem ser limpas e deduplicadas, preservando a string zero.');
    $assert(get_post_meta($older, '_sabuwu_demo_url', true) === '', 'URLs javascript devem ser rejeitadas.');
    $assert(get_post_meta($older, '_sabuwu_repository_url', true) === 'https://example.invalid/repository?x=1&y=2', 'URLs web válidas devem manter seus parâmetros.');
    $assert(get_post_meta($older, '_sabuwu_metric_1_label', true) === 'latência média', 'Rótulos devem remover scripts e marcação HTML.');
    $metric = get_post_meta($older, '_sabuwu_metric_1_value', true);
    $assert(htmlspecialchars_decode($metric, ENT_QUOTES) === '<120ms' && esc_html($metric) === '&lt;120ms', 'A métrica menor que 120ms deve permanecer legível e escapada.');

    $save_editor($older, ['_sabuwu_metric_2_label' => 'campo isolado'], wp_create_nonce('sabuwu_save_project'));
    $assert(get_post_meta($older, '_sabuwu_tags', true) === 'React, Python, 0', 'Campos ausentes em uma gravação não devem apagar os demais metadados.');
    $save_editor($older, ['_sabuwu_repository_url' => '', '_sabuwu_metric_2_label' => ''], wp_create_nonce('sabuwu_save_project'));
    $assert(!metadata_exists('post', $older, '_sabuwu_repository_url') && !metadata_exists('post', $older, '_sabuwu_metric_2_label'), 'Limpar um campo no editor deve remover seu metadado.');

    foreach ([$protected, $older] as $id) {
        $result = wp_update_post(['ID' => $id, 'post_status' => 'publish', 'post_password' => $id === $protected ? 'fixture-only' : ''], true);
        $assert(!is_wp_error($result), 'A publicação temporária da fixture falhou.');
    }
    $projects = sabuwu_get_projects();
    $assert(array_column($projects, 'id') === [$older], 'O primeiro projeto público deve substituir os exemplos, excluindo rascunhos e conteúdo protegido.');
    $assert($projects[0]['description'] === 'Resumo seguro' && $projects[0]['tags'] === ['React', 'Python', '0'], 'A lista deve apresentar resumo sem HTML e tecnologias estruturadas.');
    $assert($projects[0]['case_url'] === get_permalink($older), 'Sem URL externa, o estudo de caso deve usar a página nativa do projeto.');

    foreach ([$newer, $last] as $id) {
        $result = wp_update_post(['ID' => $id, 'post_status' => 'publish'], true);
        $assert(!is_wp_error($result), 'A publicação temporária para verificar ordenação falhou.');
    }
    $projects = sabuwu_get_projects();
    $assert(array_column($projects, 'id') === [$newer, $older, $last], 'A lista deve respeitar primeiro a ordem manual e depois a data mais recente.');
    $assert($projects[0]['description'] === 'Estudo de caso de integração com conteúdo público.', 'Projetos sem resumo devem usar texto limpo do conteúdo.');

    // Keep REST permission and public-read checks independent of query isolation.
    remove_action('pre_get_posts', $isolate_projects, 99);
    $route = '/wp/v2/' . ($type->rest_base ?: $type->name) . '/' . $older;
    wp_set_current_user(0);
    $request = new WP_REST_Request('POST', $route);
    $request->set_param('meta', ['_sabuwu_tags' => 'anonymous-write']);
    $response = rest_do_request($request);
    $assert(in_array($response->get_status(), [401, 403], true), 'A REST API deve rejeitar alterações anônimas.');
    $assert(get_post_meta($older, '_sabuwu_tags', true) === 'React, Python, 0', 'Uma solicitação REST rejeitada não deve mudar os dados.');
    $response = rest_do_request(new WP_REST_Request('GET', $route));
    $data = $response->get_data();
    $assert($response->get_status() === 200 && isset($data['meta']['_sabuwu_tags'])
        && $data['meta']['_sabuwu_tags'] === 'React, Python, 0', 'Leitura pública via REST deve incluir os campos registrados do projeto.');

    wp_set_current_user((int) $administrators[0]);
    $request = new WP_REST_Request('POST', $route);
    $request->set_param('meta', [
        '_sabuwu_tags' => 'Go, React, Go',
        '_sabuwu_case_url' => 'https://example.invalid/case-study',
        '_sabuwu_metric_2_value' => '0',
        '_sabuwu_metric_2_label' => '',
    ]);
    $response = rest_do_request($request);
    $assert($response->get_status() === 200 && get_post_meta($older, '_sabuwu_tags', true) === 'Go, React', 'A REST API deve aceitar e sanitizar a gravação autorizada dos metadados.');
    add_action('pre_get_posts', $isolate_projects, 99);
    $projects = sabuwu_get_projects();
    $updated = array_values(array_filter($projects, static function (array $project) use ($older): bool { return $project['id'] === $older; }))[0];
    $assert($updated['case_url'] === 'https://example.invalid/case-study', 'Uma URL de estudo de caso explícita deve substituir o permalink nativo.');
    $assert(in_array(['value' => '0', 'label' => ''], $updated['metrics'], true), 'Uma métrica de valor zero não pode desaparecer da lista.');

    require_once ABSPATH . WPINC . '/class-wp-customize-manager.php';
    $customize = new WP_Customize_Manager(['settings_previewed' => false]);
    sabuwu_customize_register($customize);
    $assert($customize->get_control('sabuwu_resume_url') !== null && $customize->get_control('sabuwu_projects_show_demo') !== null, 'O Personalizador deve registrar controles de currículo e exemplos.');
    $assert($customize->get_setting('sabuwu_resume_url')->sanitize('javascript:alert(1)') === ''
        && $customize->get_setting('sabuwu_resume_url')->sanitize('#') === '', 'O controle de currículo deve rejeitar protocolos inseguros e links vazios.');
    $resume_page = get_page_by_path('resume');
    $assert($resume_page instanceof WP_Post && $resume_page->post_status === 'publish'
        && locate_template('page-resume.php') !== '', 'O currículo deve existir como página pública com template próprio.');
    $assert(sabuwu_defaults()['resume_url'] === '/resume/', 'O destino padrão do currículo deve usar a página WordPress /resume/.');
    $assert($customize->get_setting('sabuwu_projects_show_demo')->sanitize('0') === false
        && $customize->get_setting('sabuwu_projects_show_demo')->sanitize('1') === true, 'O controle de exemplos deve sanitizar valores de checkbox corretamente.');
    $assert($customize->get_setting('sabuwu_hero_note')->sanitize("Primeira linha\n<script>alert(1)</script>Segunda linha") === "Primeira linha\nSegunda linha", 'Textos longos do Personalizador devem preservar linhas e remover scripts.');
    $assert(get_theme_mods() === $original_theme_mods, 'Os testes não devem modificar opções do tema.');
} catch (Throwable $error) {
    $failure = $error->getMessage();
} finally {
    remove_action('pre_get_posts', $isolate_projects, 99);
    remove_filter('theme_mod_sabuwu_projects_show_demo', $show_demo, 99);
    remove_filter('theme_mod_sabuwu_projects_show_demo', $hide_demo, 100);
    $_POST = $original_post_data;
    wp_set_current_user((int) $administrators[0]);

    // Deleting the known fixture IDs also removes their generated revisions.
    // Rollback is guaranteed even if cleanup itself throws an exception.
    try {
        if ($transaction_started) {
            foreach ($fixtures as $id) {
                wp_delete_post($id, true);
            }
        }
    } catch (Throwable $cleanup_error) {
        $failure = $failure ?? 'A limpeza das fixtures falhou; a transação será revertida.';
    } finally {
        if ($transaction_started) {
            if ($wpdb->query('ROLLBACK') === false) {
                $failure = 'Não foi possível confirmar o rollback da transação dos testes.';
            }
            wp_cache_flush();
        }
        wp_suspend_cache_addition($original_cache_suspension);
        wp_set_current_user($original_user);
    }
}

if ($failure !== null) {
    WP_CLI::error($failure);
}

WP_CLI::success(sprintf('%d verificações de integração passaram; fixtures removidas e transação revertida.', $checks));
