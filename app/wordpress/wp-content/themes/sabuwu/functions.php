<?php
/** Native WordPress integration for the portfolio. */
defined('ABSPATH') || exit;

require_once get_theme_file_path('/inc/customizer.php');
require_once get_theme_file_path('/inc/projects.php');

add_action('after_setup_theme', static function (): void {
    load_theme_textdomain('sabuwu', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', ['height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true]);
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');
    register_nav_menus(['primary' => __('Navegação principal', 'sabuwu')]);
    if (!isset($GLOBALS['content_width'])) {
        $GLOBALS['content_width'] = 920;
    }
});

add_action('wp_enqueue_scripts', static function (): void {
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('sabuwu', get_stylesheet_uri(), [], $version);
    wp_enqueue_script('sabuwu', get_theme_file_uri('/assets/js/portfolio.js'), [], $version, ['in_footer' => true, 'strategy' => 'defer']);
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
});

function sabuwu_is_portfolio(): bool {
    return sabuwu_is_portfolio_home()
        || is_page(['projetos', 'perfil', 'experiencia', 'contato']);
}

function sabuwu_is_portfolio_home(): bool {
    return (is_front_page() && !is_home()) || is_page_template('page-portfolio.php');
}

add_filter('language_attributes', static function (string $attributes): string {
    return sabuwu_is_portfolio()
        ? (string) preg_replace('/\blang="[^"]*"/', 'lang="' . esc_attr(sabuwu_setting('content_language')) . '"', $attributes)
        : $attributes;
});

function sabuwu_section_url(string $id): string {
    if ($id === 'inicio') {
        return home_url('/#inicio');
    }
    if ($id === 'blog') {
        return sabuwu_blog_url();
    }

    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'name' => $id,
        'numberposts' => 1,
    ]);
    return $pages ? get_permalink($pages[0]) : home_url('/' . rawurlencode($id) . '/');
}

function sabuwu_blog_url(): string {
    if (get_option('show_on_front') !== 'page') {
        return home_url('/');
    }
    $page_id = (int) get_option('page_for_posts');
    return $page_id ? (get_permalink($page_id) ?: home_url('/blog/')) : home_url('/blog/');
}

/** Create the native posts page while preserving existing reading settings. */
function sabuwu_setup_blog_page(): void {
    if (get_option('show_on_front') !== 'page' || get_option('page_for_posts')) {
        return;
    }
    $pages = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'name' => 'blog',
        'post__not_in' => [(int) get_option('page_on_front')],
        'numberposts' => 1,
        'fields' => 'ids',
    ]);
    $page_id = $pages ? $pages[0] : wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => __('Blog', 'sabuwu'),
        'post_name' => 'blog',
        'post_content' => '',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    ], true);
    if (!is_wp_error($page_id) && $page_id) {
        update_option('page_for_posts', (int) $page_id);
    }
}
add_action('after_switch_theme', 'sabuwu_setup_blog_page');

function sabuwu_default_navigation(): void {
    $links = ['projetos' => __('Projetos', 'sabuwu'), 'blog' => __('Blog', 'sabuwu'), 'perfil' => __('Perfil', 'sabuwu'), 'experiencia' => __('Experiência', 'sabuwu'), 'contato' => __('Contato', 'sabuwu')];
    echo '<ul class="nav-list">';
    foreach ($links as $id => $label) {
        printf('<li><a href="%s">%s</a></li>', esc_url(sabuwu_section_url($id)), esc_html($label));
    }
    echo '</ul>';
}

add_filter('nav_menu_link_attributes', static function (array $attributes): array {
    $href = $attributes['href'] ?? '';
    $host = wp_parse_url($href, PHP_URL_HOST);
    $home_host = wp_parse_url(home_url('/'), PHP_URL_HOST);
    $fragment = wp_parse_url($href, PHP_URL_FRAGMENT);
    $section_ids = ['projetos', 'blog', 'perfil', 'experiencia', 'contato'];

    if ((!$host || strtolower((string) $host) === strtolower((string) $home_host))
        && in_array($fragment, $section_ids, true)
    ) {
        $attributes['href'] = sabuwu_section_url($fragment);
    }

    return $attributes;
});

/** Plain text with intentional line breaks, escaped before markup is added. */
function sabuwu_lines(string $text): string {
    return nl2br(esc_html($text));
}

/** External destinations open safely; in-site anchors stay in the current tab. */
function sabuwu_link_attributes(string $url): string {
    $host = wp_parse_url($url, PHP_URL_HOST);
    return $host && strtolower($host) !== strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST))
        ? ' target="_blank" rel="noopener noreferrer"' : '';
}

function sabuwu_project_links(array $project): void {
    $links = ['demo_url' => __('Live demo ↗', 'sabuwu'), 'repository_url' => __('GitHub ↗', 'sabuwu'), 'case_url' => __('Case study ↗', 'sabuwu')];
    $has_links = false;
    foreach ($links as $key => $label) {
        if (!empty($project[$key])) {
            if (!$has_links) {
                echo '<div class="link-row">';
                $has_links = true;
            }
            printf('<a href="%s"%s>%s</a>', esc_url($project[$key]), sabuwu_link_attributes($project[$key]), esc_html($label));
        }
    }
    if ($has_links) {
        echo '</div>';
    }
}

add_action('wp_head', static function (): void {
    echo '<meta name="theme-color" content="#0d0d0d">' . "\n";
    // SEO plugins own their description; avoid a duplicate tag when Yoast is active.
    if (sabuwu_is_portfolio_home() && !defined('WPSEO_VERSION')) {
        printf('<meta name="description" content="%s">' . "\n", esc_attr(sabuwu_setting('seo_description')));
    }
});

function sabuwu_portfolio_title(string $title): string {
    return sabuwu_is_portfolio_home() && sabuwu_setting('seo_title')
        ? (string) sabuwu_setting('seo_title') : $title;
}
add_filter('pre_get_document_title', 'sabuwu_portfolio_title', 20);
foreach (['wpseo_title', 'wpseo_opengraph_title', 'wpseo_twitter_title'] as $hook) {
    add_filter($hook, 'sabuwu_portfolio_title', 20);
}
function sabuwu_portfolio_description(string $description): string {
    return sabuwu_is_portfolio_home() && sabuwu_setting('seo_description')
        ? (string) sabuwu_setting('seo_description') : $description;
}
foreach (['wpseo_metadesc', 'wpseo_opengraph_desc', 'wpseo_twitter_description'] as $hook) {
    add_filter($hook, 'sabuwu_portfolio_description', 20);
}
add_filter('wpseo_locale', static function (string $locale): string {
    return sabuwu_is_portfolio() ? str_replace('-', '_', sabuwu_setting('content_language')) : $locale;
}, 20);

// Existing installations also need pages and rewrite rules after a theme update.
add_action('admin_init', static function (): void {
    $version = wp_get_theme()->get('Version');
    if (get_option('sabuwu_theme_version') !== $version && current_user_can('manage_options')) {
        $pages = [
            'projetos' => __('Projetos', 'sabuwu'),
            'perfil' => __('Perfil', 'sabuwu'),
            'experiencia' => __('Experiência', 'sabuwu'),
            'contato' => __('Contato', 'sabuwu'),
        ];
        foreach ($pages as $slug => $title) {
            $published = get_posts([
                'post_type' => 'page',
                'post_status' => 'publish',
                'name' => $slug,
                'numberposts' => 1,
                'fields' => 'ids',
            ]);
            if (!$published) {
                wp_insert_post([
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'post_title' => $title,
                    'post_name' => $slug,
                    'post_content' => '',
                ]);
            }
        }
        sabuwu_setup_blog_page();
        sabuwu_activate_project_rewrites();
        update_option('sabuwu_theme_version', $version, false);
    }
});

