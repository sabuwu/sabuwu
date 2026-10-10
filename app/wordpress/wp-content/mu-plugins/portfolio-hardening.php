<?php
/**
 * Plugin Name: Sabuwu Portfolio Hardening
 * Description: Project-owned hardening, local email policy and cron diagnostics.
 * Version: 1.0.0
 * Author: sabu
 */
defined('ABSPATH') || exit;
add_filter('xmlrpc_enabled', '__return_false');
add_filter('pings_open', '__return_false');
add_filter('wp_headers', static function (array $headers): array {
    unset($headers['X-Pingback']);
    return $headers;
});
add_filter('xmlrpc_methods', static function (array $methods): array {
    unset($methods['pingback.ping'], $methods['pingback.extensions.getPingbacks']);
    return $methods;
});
add_filter('login_errors', static fn(): string => __('Não foi possível autenticar. Verifique suas credenciais.', 'sabuwu'));
add_filter('auto_update_plugin', '__return_false');
add_filter('auto_update_theme', '__return_false');
add_filter('pre_wp_mail', static function ($result) {
    if (wp_get_environment_type() !== 'production' && !getenv('SMTP_HOST')) {
        return false; // Local sites must not send real email accidentally.
    }
    return $result;
});
add_action('portfolio_cron_probe', static function (): void {
    update_option('portfolio_cron_last_probe', time(), false);
});

