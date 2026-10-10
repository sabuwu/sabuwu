<?php
define( 'WP_CACHE', true );

/** Application configuration; secrets are environment values or private files. */
function portfolio_env(string $name, string $default = ''): string {
    $value = getenv($name);
    if ($value !== false && $value !== '') {
        return $value;
    }
    $file = getenv($name . '_FILE') ?: '/run/portfolio-secrets/' . $name;
    return is_readable($file) ? trim(file_get_contents($file)) : $default;
}
function portfolio_bool(string $name, bool $default = false): bool {
    return filter_var(portfolio_env($name, $default ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN);
}

define('DB_NAME', portfolio_env('DB_NAME', 'wordpress'));
define('DB_USER', portfolio_env('DB_USER', 'wordpress'));
define('DB_PASSWORD', portfolio_env('DB_PASSWORD'));
define('DB_HOST', portfolio_env('DB_HOST', 'database:3306'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
foreach (['AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY',
          'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'] as $key) {
    $secret = portfolio_env($key);
    if (strlen($secret) < 20) {
        throw new RuntimeException('Missing secure WordPress key: ' . $key);
    }
    define($key, $secret);
}
$table_prefix = portfolio_env('DB_TABLE_PREFIX', 'wp_');
if (!preg_match('/^[A-Za-z0-9_]+$/', $table_prefix)) {
    throw new RuntimeException('Invalid database table prefix.');
}
$environment = portfolio_env('WP_ENVIRONMENT_TYPE', 'development');
if (!in_array($environment, ['local', 'development', 'staging', 'production'], true)) {
    throw new RuntimeException('Invalid WP_ENVIRONMENT_TYPE.');
}
define('WP_ENVIRONMENT_TYPE', $environment);
define('WP_DEBUG', WP_ENVIRONMENT_TYPE !== 'production' && portfolio_bool('WORDPRESS_DEBUG'));
define('WP_DEBUG_LOG', WP_DEBUG ? '/tmp/wordpress-debug.log' : false);
define('WP_DEBUG_DISPLAY', false);
define('DISABLE_WP_CRON', true);
define('DISALLOW_FILE_EDIT', true);
// Production HTTP cannot install/edit code; the local manifest installer uses WP-CLI.
define('DISALLOW_FILE_MODS', WP_ENVIRONMENT_TYPE === 'production' && !(defined('WP_CLI') && WP_CLI));
define('AUTOMATIC_UPDATER_DISABLED', true);
define('FORCE_SSL_ADMIN', portfolio_bool('WORDPRESS_FORCE_SSL_ADMIN'));
if (portfolio_bool('TRUST_REVERSE_PROXY') && ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
define('WP_HOME', portfolio_env('WORDPRESS_URL', 'http://localhost:8088'));
define('WP_SITEURL', WP_HOME);
if (portfolio_env('AKISMET_API_KEY') !== '') {
    define('WPCOM_API_KEY', portfolio_env('AKISMET_API_KEY'));
}
if (portfolio_env('SMTP_HOST') !== '') {
    define('WPMS_ON', true);
    define('WPMS_MAILER', 'smtp');
    define('WPMS_SMTP_HOST', portfolio_env('SMTP_HOST'));
    define('WPMS_SMTP_PORT', (int) portfolio_env('SMTP_PORT', '587'));
    define('WPMS_SMTP_USER', portfolio_env('SMTP_USER'));
    define('WPMS_SMTP_PASS', portfolio_env('SMTP_PASSWORD'));
    define('WPMS_SMTP_AUTH', portfolio_env('SMTP_USER') !== '');
    define('WPMS_SSL', portfolio_env('SMTP_SECURE', 'tls'));
    define('WPMS_MAIL_FROM', portfolio_env('SMTP_FROM_EMAIL'));
    define('WPMS_MAIL_FROM_NAME', portfolio_env('SMTP_FROM_NAME', 'Sabuwu'));
}
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
require_once ABSPATH . 'wp-settings.php';

