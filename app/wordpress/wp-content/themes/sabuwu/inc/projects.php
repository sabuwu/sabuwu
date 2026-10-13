<?php
/**
 * Native project publishing, metadata and portfolio queries.
 *
 * @package Sabuwu
 */

defined('ABSPATH') || exit;

/**
 * Metadata shown in the project editor and exposed by the REST API.
 */
function sabuwu_project_fields(): array
{
    $fields = [
        '_sabuwu_tags' => [
            'label' => __('Tecnologias', 'sabuwu'),
            'type' => 'text',
            'description' => __('Separe as tecnologias por vírgulas: React, TypeScript, PostgreSQL.', 'sabuwu'),
        ],
        '_sabuwu_demo_url' => [
            'label' => __('URL da demonstração', 'sabuwu'),
            'type' => 'url',
            'description' => __('Opcional. Link para a aplicação ou documentação pública.', 'sabuwu'),
        ],
        '_sabuwu_repository_url' => [
            'label' => __('URL do repositório', 'sabuwu'),
            'type' => 'url',
            'description' => __('Opcional. Link para o código do projeto.', 'sabuwu'),
        ],
        '_sabuwu_case_url' => [
            'label' => __('URL do estudo de caso', 'sabuwu'),
            'type' => 'url',
            'description' => __('Opcional. Deixe vazio para usar a página deste projeto como estudo de caso.', 'sabuwu'),
        ],
    ];

    for ($number = 1; $number <= 3; $number++) {
        $fields['_sabuwu_metric_' . $number . '_value'] = [
            /* translators: %d: Metric number. */
            'label' => sprintf(__('Métrica %d — valor', 'sabuwu'), $number),
            'type' => 'text',
            'description' => __('Opcional. Exemplo: −35%, <120ms ou 24/7.', 'sabuwu'),
        ];
        $fields['_sabuwu_metric_' . $number . '_label'] = [
            /* translators: %d: Metric number. */
            'label' => sprintf(__('Métrica %d — descrição', 'sabuwu'), $number),
            'type' => 'text',
            'description' => __('Explique o resultado e apresente somente números verificáveis.', 'sabuwu'),
        ];
    }

    return $fields;
}

function sabuwu_sanitize_project_text($value): string
{
    return is_scalar($value) ? sanitize_text_field((string) $value) : '';
}

function sabuwu_sanitize_project_tags($value): string
{
    $tags = array_map('trim', explode(',', sabuwu_sanitize_project_text($value)));
    $tags = array_filter($tags, static function (string $tag): bool {
        return $tag !== '';
    });

    return implode(', ', array_unique($tags));
}

function sabuwu_sanitize_project_url($value): string
{
    return is_scalar($value) ? esc_url_raw((string) $value, ['http', 'https']) : '';
}

/**
 * REST reads use the public post's normal visibility checks. Only people who
 * can edit the particular project may add, change or delete its metadata.
 */
function sabuwu_can_edit_project_meta($allowed, $meta_key, $post_id, $user_id): bool
{
    return get_post_type((int) $post_id) === 'sabuwu_project'
        && user_can((int) $user_id, 'edit_post', (int) $post_id);
}

function sabuwu_register_project_type(): void
{
    register_post_type('sabuwu_project', [
        'labels' => [
            'name' => __('Projetos', 'sabuwu'),
            'singular_name' => __('Projeto', 'sabuwu'),
            'menu_name' => __('Projetos', 'sabuwu'),
            'all_items' => __('Todos os projetos', 'sabuwu'),
            'add_new' => __('Adicionar projeto', 'sabuwu'),
            'add_new_item' => __('Adicionar novo projeto', 'sabuwu'),
            'edit_item' => __('Editar projeto', 'sabuwu'),
            'new_item' => __('Novo projeto', 'sabuwu'),
            'view_item' => __('Ver projeto', 'sabuwu'),
            'search_items' => __('Buscar projetos', 'sabuwu'),
            'not_found' => __('Nenhum projeto encontrado.', 'sabuwu'),
            'not_found_in_trash' => __('Nenhum projeto na lixeira.', 'sabuwu'),
            'featured_image' => __('Imagem do projeto', 'sabuwu'),
            'set_featured_image' => __('Definir imagem do projeto', 'sabuwu'),
            'remove_featured_image' => __('Remover imagem do projeto', 'sabuwu'),
            'use_featured_image' => __('Usar como imagem do projeto', 'sabuwu'),
            'attributes' => __('Atributos do projeto', 'sabuwu'),
            'item_published' => __('Projeto publicado.', 'sabuwu'),
            'item_updated' => __('Projeto atualizado.', 'sabuwu'),
        ],
        'description' => __('Trabalhos publicados no portfólio.', 'sabuwu'),
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-portfolio',
        'has_archive' => false,
        'rewrite' => ['slug' => 'projeto', 'with_front' => false],
        // Custom-fields support is required for registered metadata in REST.
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes', 'custom-fields'],
    ]);

    foreach (sabuwu_project_fields() as $key => $field) {
        $sanitize = $key === '_sabuwu_tags'
            ? 'sabuwu_sanitize_project_tags'
            : ($field['type'] === 'url' ? 'sabuwu_sanitize_project_url' : 'sabuwu_sanitize_project_text');

        register_post_meta('sabuwu_project', $key, [
            'type' => 'string',
            'label' => $field['label'],
            'description' => $field['description'],
            'single' => true,
            'default' => '',
            'show_in_rest' => true,
            'sanitize_callback' => $sanitize,
            'auth_callback' => 'sabuwu_can_edit_project_meta',
            'revisions_enabled' => true,
        ]);
    }
}
add_action('init', 'sabuwu_register_project_type');

function sabuwu_add_project_meta_box(): void
{
    add_meta_box(
        'sabuwu-project-details',
        __('Dados do projeto no portfólio', 'sabuwu'),
        'sabuwu_render_project_meta_box',
        'sabuwu_project',
        'normal',
        'default',
        ['__block_editor_compatible_meta_box' => true]
    );
}
add_action('add_meta_boxes_sabuwu_project', 'sabuwu_add_project_meta_box');

function sabuwu_render_project_meta_box(WP_Post $post): void
{
    wp_nonce_field('sabuwu_save_project', '_sabuwu_project_nonce');
    echo '<p>' . esc_html__('Use o resumo para a descrição no portfólio e o editor para o estudo de caso completo. Em Atributos do projeto, a opção Ordem define a posição: números menores aparecem primeiro.', 'sabuwu') . '</p>';

    foreach (sabuwu_project_fields() as $key => $field) {
        $description_id = $key . '_description';
        echo '<p><label for="' . esc_attr($key) . '"><strong>' . esc_html($field['label']) . '</strong></label><br>';
        echo '<input class="widefat" id="' . esc_attr($key) . '" name="sabuwu_project[' . esc_attr($key) . ']" type="' . esc_attr($field['type']) . '" value="' . esc_attr(get_post_meta($post->ID, $key, true)) . '" aria-describedby="' . esc_attr($description_id) . '">';
        echo '<span class="description" id="' . esc_attr($description_id) . '">' . esc_html($field['description']) . '</span></p>';
    }
}

function sabuwu_save_project_meta(int $post_id, WP_Post $post): void
{
    if ($post->post_type !== 'sabuwu_project'
        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        || wp_is_post_autosave($post_id)
        || wp_is_post_revision($post_id)
        || !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    if (!isset($_POST['_sabuwu_project_nonce'])
        || !is_string($_POST['_sabuwu_project_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_sabuwu_project_nonce'])), 'sabuwu_save_project')
        || !isset($_POST['sabuwu_project'])
        || !is_array($_POST['sabuwu_project'])
    ) {
        return;
    }

    $submitted = wp_unslash($_POST['sabuwu_project']);
    foreach (sabuwu_project_fields() as $key => $field) {
        // Missing fields are left untouched, including saves through Gutenberg.
        if (!array_key_exists($key, $submitted) || !is_scalar($submitted[$key])) {
            continue;
        }

        $value = $key === '_sabuwu_tags'
            ? sabuwu_sanitize_project_tags($submitted[$key])
            : ($field['type'] === 'url' ? sabuwu_sanitize_project_url($submitted[$key]) : sabuwu_sanitize_project_text($submitted[$key]));

        if ($value === '') {
            delete_post_meta($post_id, $key);
        } else {
            // WordPress metadata writes expect slashed input.
            update_post_meta($post_id, $key, wp_slash($value));
        }
    }
}
add_action('save_post_sabuwu_project', 'sabuwu_save_project_meta', 10, 2);

/**
 * Return published, unprotected projects without modifying the main query.
 * Demonstration projects are used only until real projects are published.
 */
function sabuwu_get_projects(): array
{
    $query = new WP_Query([
        'post_type' => 'sabuwu_project',
        'post_status' => 'publish',
        'has_password' => false,
        'posts_per_page' => -1,
        'orderby' => ['menu_order' => 'ASC', 'date' => 'DESC'],
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
    ]);

    if (!$query->posts) {
        return sabuwu_setting('projects_show_demo') ? sabuwu_default_projects() : [];
    }

    $projects = [];
    foreach ($query->posts as $post) {
        $tags = explode(',', (string) get_post_meta($post->ID, '_sabuwu_tags', true));
        $tags = array_values(array_filter(array_map('trim', $tags), static function (string $tag): bool {
            return $tag !== '';
        }));

        $metrics = [];
        for ($number = 1; $number <= 3; $number++) {
            $value = (string) get_post_meta($post->ID, '_sabuwu_metric_' . $number . '_value', true);
            $label = (string) get_post_meta($post->ID, '_sabuwu_metric_' . $number . '_label', true);
            if ($value !== '' || $label !== '') {
                $metrics[] = ['value' => $value, 'label' => $label];
            }
        }

        $description = $post->post_excerpt !== ''
            ? wp_strip_all_tags(strip_shortcodes($post->post_excerpt))
            : wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 42, '…');
        $case_url = sabuwu_sanitize_project_url(get_post_meta($post->ID, '_sabuwu_case_url', true));

        $projects[] = [
            'id' => $post->ID,
            'title' => get_the_title($post),
            'description' => $description,
            'tags' => $tags,
            'metrics' => $metrics,
            'demo_url' => sabuwu_sanitize_project_url(get_post_meta($post->ID, '_sabuwu_demo_url', true)),
            'repository_url' => sabuwu_sanitize_project_url(get_post_meta($post->ID, '_sabuwu_repository_url', true)),
            'case_url' => $case_url !== '' ? $case_url : get_permalink($post),
        ];
    }

    return $projects;
}

/**
 * Add project permalinks when activating this theme, without flushing on
 * ordinary requests or changing a site that already has the required rules.
 */
function sabuwu_activate_project_rewrites(): void
{
    sabuwu_register_project_type();
    global $wp_rewrite;
    if (!$wp_rewrite->using_permalinks()) {
        return;
    }

    $rules = get_option('rewrite_rules', []);
    if (is_array($rules)) {
        foreach ($rules as $query) {
            if (is_string($query) && strpos($query, 'sabuwu_project=') !== false) {
                return;
            }
        }
    }

    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'sabuwu_activate_project_rewrites');
