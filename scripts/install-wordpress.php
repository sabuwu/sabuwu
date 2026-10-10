<?php
/** Native installer: private password never enters argv, prompts or logs. */
if (PHP_SAPI !== 'cli') { exit(1); }
define('WP_INSTALLING', true);
$url = parse_url(getenv('WORDPRESS_URL') ?: '');
if (!$url || !isset($url['host']) || !in_array($url['scheme'] ?? '', ['http', 'https'], true)) {
    fwrite(STDERR, "Invalid WORDPRESS_URL.\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = $url['host'] . (isset($url['port']) ? ':' . $url['port'] : '');
$_SERVER['SERVER_NAME'] = $url['host'];
$_SERVER['SERVER_PORT'] = $url['port'] ?? ($url['scheme'] === 'https' ? 443 : 80);
$_SERVER['HTTPS'] = $url['scheme'] === 'https' ? 'on' : 'off';
$_SERVER['PHP_SELF'] = rtrim($url['path'] ?? '', '/') . '/index.php';
$_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'];
$_SERVER['REQUEST_URI'] = $url['path'] ?? '/';
$_SERVER['SCRIPT_FILENAME'] = '/var/www/html/index.php';
// Suppress the installation email, matching WP-CLI's --skip-email behavior.
function wp_new_blog_notification($title, $url, $user_id, $password) {}
require '/var/www/html/wp-load.php';
if (is_blog_installed()) { exit(0); }
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
$password = portfolio_env('WORDPRESS_ADMIN_PASSWORD');
$username = portfolio_env('WORDPRESS_ADMIN_USER');
$email = portfolio_env('WORDPRESS_ADMIN_EMAIL');
if (strlen($password) < 20 || !$username || sanitize_user($username, true) !== $username || !is_email($email)) {
    fwrite(STDERR, "Invalid administrator configuration.\n");
    exit(1);
}
$result = wp_install(portfolio_env('WORDPRESS_TITLE'), $username, $email, false, '', wp_slash($password));
unset($password);
global $wpdb;
if (is_wp_error($result) || $wpdb->last_error || !is_blog_installed()) {
    fwrite(STDERR, "WordPress installation failed; check database connectivity and configuration.\n");
    exit(1);
}
update_option('portfolio_seed_pending', 1, false);
echo "WordPress installed successfully.\n";
