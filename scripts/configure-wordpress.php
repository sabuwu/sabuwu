<?php
/** Idempotent defaults. Existing editorial content is never overwritten. */
if (!defined('WP_CLI') || !WP_CLI) {
    exit(1);
}
if (!get_option('portfolio_initialized')) {
    update_option('timezone_string', getenv('TZ') ?: 'America/Fortaleza');
    update_option('blogdescription', 'WordPress, PHP, Linux e infraestrutura reproduzível.');
    update_option('blog_public', 0); // Local installations should not be indexed.
    update_option('default_ping_status', 'closed');
    update_option('default_comment_status', 'closed');
    update_option('permalink_structure', '/%postname%/');
    $page = get_page_by_path('sobre');
    if (!$page) {
        $id = wp_insert_post([
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Sobre',
            'post_name' => 'sobre', 'post_content' => '<!-- wp:paragraph --><p>Sou sabu, programador e engenheiro DevOps. Este projeto demonstra WordPress, PHP, banco de dados, containers, Linux, Git e automação.</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Um ambiente que pode ser reproduzido</h2><!-- /wp:heading --><!-- wp:paragraph --><p>A aplicação, a infraestrutura e o conteúdo possuem responsabilidades separadas. O conteúdo editorial pode ser exportado e restaurado sem versionar credenciais.</p><!-- /wp:paragraph -->',
        ], true);
        if (is_wp_error($id)) {
            WP_CLI::error($id->get_error_message());
        }
    } else {
        $id = $page->ID;
    }
    update_option('show_on_front', 'page');
    update_option('page_on_front', $id);
    switch_theme('sabuwu');
    update_option('portfolio_initialized', 1, false);
    flush_rewrite_rules();
}
if (class_exists('AIOWPSecurity_Config')) {
    $config = AIOWPSecurity_Config::get_instance();
    $config->set_value('aiowps_enable_login_lockdown', '1');
    $config->set_value('aiowps_max_login_attempts', '5');
    $config->set_value('aiowps_retry_time_period', '15');
    $config->set_value('aiowps_lockout_time_length', '15');
    $config->set_value('aiowps_max_lockout_time_length', '15');
    $config->set_value('aiowps_enable_brute_force_attack_prevention', ''); // No secret login URL.
    $config->set_value('aiowps_enable_login_lockdown_email_notification', '');
    $config->save_config();
    // Two Factor is the single TOTP provider; disable AIOS's duplicate module.
    global $simba_two_factor_authentication;
    if (is_object($simba_two_factor_authentication)) {
        foreach (array_keys(wp_roles()->roles) as $role) {
            $simba_two_factor_authentication->update_option('tfa_' . $role, 0);
        }
        $simba_two_factor_authentication->update_option('tfa__super_admin', 0);
    }
    $messages = AIOWPSecurity_Settings_Tasks::enable_basic_firewall();
    if (!empty($messages['error'])) {
        WP_CLI::error($messages['error']);
    }
    if (!AIOWPSecurity_Utility_Htaccess::write_to_htaccess(false)) {
        WP_CLI::error('Cannot write reproducible Apache firewall rules.');
    }
    $firewall = AIOS_Firewall_Resource::request(AIOS_Firewall_Resource::CONFIG);
    // This broad rule also blocks legitimate code searches such as "GLOBALS".
    // Use the tested Apache rules locally; tune request signatures for production.
    if (!$firewall->set_value('aiowps_deny_bad_query_strings', false)) {
        WP_CLI::error('Cannot save PHP firewall configuration.');
    }
}
WP_CLI::success('Portfolio defaults and local login protection configured.');

