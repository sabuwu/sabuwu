<?php
/** Generate private credentials without baking a public password into a clone. */
declare(strict_types=1);

$envPath = '/bootstrap/.env';
if (!is_file($envPath) || !is_writable($envPath)) {
    fwrite(STDERR, "Create a writable .env first: cp .env.example .env\n");
    exit(1);
}
$uid = (int) (getenv('APP_UID') !== false ? getenv('APP_UID') : 1000);
$gid = (int) (getenv('APP_GID') !== false ? getenv('APP_GID') : 1000);
if ($uid < 1 || $gid < 1) {
    fwrite(STDERR, "APP_UID and APP_GID must be non-root IDs.\n");
    exit(1);
}
$keys = ['DB_PASSWORD', 'WORDPRESS_ADMIN_PASSWORD', 'AUTH_KEY', 'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT'];
$content = file_get_contents($envPath);
umask(0077);
foreach (array_merge($keys, ['DB_ROOT_PASSWORD']) as $key) {
    $directory = $key === 'DB_ROOT_PASSWORD' ? '/run/database-secrets' : '/run/portfolio-secrets';
    $path = "$directory/$key";
    $value = getenv($key) ?: '';
    if ($value === '' && is_file($path)) {
        $value = trim(file_get_contents($path));
    }
    if ($value === '') {
        $value = bin2hex(random_bytes(32));
    }
    if (strlen($value) < 20 || preg_match('/change[-_ ]?me|password|replace[-_ ]?me/i', $value)
        || preg_match('/[\r\n\x00\'"\\\\]/', $value)) {
        fwrite(STDERR, "$key must be a strong, single-line value (20+ characters, no quotes or backslashes).\n");
        exit(1);
    }
    file_put_contents($path, $value . "\n", LOCK_EX);
    chmod($path, 0600);
    if ($key !== 'DB_ROOT_PASSWORD') {
        chown($path, $uid);
        chgrp($path, $gid);
    }
    // Single quotes prevent Compose interpolation of characters such as $. Preserve comments.
    $line = "$key='$value'";
    $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
    $content = preg_match($pattern, $content)
        ? preg_replace_callback($pattern, static fn() => $line, $content)
        : $content . "\n$line\n";
}
file_put_contents($envPath, $content, LOCK_EX);
chmod($envPath, 0600);
echo "Private credentials ready; saved in .env and protected volumes.\n";

