<?php
/** Portable editorial snapshot: no users, credentials, plugin state or sessions. */
if (!defined('WP_CLI') || !WP_CLI) { exit(1); }
global $wpdb;
$mode = $args[0] ?? 'export';
$tables = ['posts', 'postmeta', 'terms', 'term_taxonomy', 'term_relationships', 'termmeta'];
$options = ['blogname', 'blogdescription', 'timezone_string', 'show_on_front', 'page_on_front',
    'page_for_posts', 'posts_per_page', 'permalink_structure', 'default_comment_status',
    'default_ping_status', 'stylesheet', 'template', 'theme_mods_sabuwu'];
if ($mode === 'export') {
    $snapshot = ['format' => 'sabuwu-content-v1', 'source_url' => home_url(), 'tables' => [], 'options' => []];
    foreach ($tables as $table) {
        $rows = $wpdb->get_results("SELECT * FROM {$wpdb->$table} ORDER BY 1", ARRAY_A);
        if ($table === 'posts') {
            $rows = array_values(array_filter($rows, static fn($row) => !in_array($row['post_type'], ['revision', 'auto-draft'], true)
                && !in_array($row['post_status'], ['auto-draft', 'private', 'trash'], true) && $row['post_password'] === ''));
            $postIds = array_column($rows, 'ID');
            foreach ($rows as &$row) {
                $row['post_author'] = '0';
                $row['post_password'] = '';
                $row['comment_count'] = '0';
                $row['pinged'] = '';
                $row['to_ping'] = '';
            }
            unset($row);
        }
        if ($table === 'postmeta') {
            $rows = array_values(array_filter($rows, static fn($row) => in_array($row['post_id'], $postIds, true)
                && !in_array($row['meta_key'], ['_edit_lock', '_edit_last'], true)
                && !preg_match('/password|secret|token|api[_-]?key|nonce|session/i', $row['meta_key'])));
        }
        if ($table === 'term_relationships') {
            $rows = array_values(array_filter($rows, static fn($row) => in_array($row['object_id'], $postIds, true)));
        }
        if ($table === 'termmeta') {
            $rows = array_values(array_filter($rows, static fn($row) => !preg_match('/password|secret|token|api[_-]?key|nonce|session/i', $row['meta_key'])));
        }
        $snapshot['tables'][$table] = $rows;
    }
    foreach ($options as $option) {
        $value = get_option($option, null);
        if ($value !== null) { $snapshot['options'][$option] = $value; }
    }
    echo wp_json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    return;
}
if ($mode !== 'import' || empty($args[1])) { WP_CLI::error('Expected export or import <snapshot.json>.'); }
$snapshot = json_decode(file_get_contents($args[1]), true, 512, JSON_THROW_ON_ERROR);
if (($snapshot['format'] ?? '') !== 'sabuwu-content-v1') { WP_CLI::error('Unsupported snapshot.'); }
foreach ($tables as $table) {
    if (!isset($snapshot['tables'][$table]) || !is_array($snapshot['tables'][$table])) { WP_CLI::error('Incomplete content snapshot.'); }
}
$administrators = get_users(['role' => 'administrator', 'number' => 1, 'fields' => 'ID']);
if (!$administrators) { WP_CLI::error('Create an administrator before importing editorial content.'); }
$authorId = (string) $administrators[0];
$wpdb->query('START TRANSACTION');
try {
    foreach (array_reverse($tables) as $table) {
        if ($wpdb->query("DELETE FROM {$wpdb->$table}") === false) { throw new RuntimeException('Cannot clear content table.'); }
    }
    foreach ($tables as $table) {
        foreach ($snapshot['tables'][$table] as $row) {
            if ($table === 'posts') { $row['post_author'] = $authorId; }
            if ($wpdb->insert($wpdb->$table, $row) === false) { throw new RuntimeException('Cannot restore ' . $table . ': ' . $wpdb->last_error); }
        }
    }
    foreach ($options as $option) {
        if (array_key_exists($option, $snapshot['options'])) { update_option($option, $snapshot['options'][$option]); }
    }
    $wpdb->query('COMMIT');
} catch (Throwable $error) {
    $wpdb->query('ROLLBACK');
    WP_CLI::error($error->getMessage());
}
wp_cache_flush();
$source = $snapshot['source_url'] ?? '';
if ($source !== '' && $source !== home_url()) {
    WP_CLI::runcommand('search-replace ' . escapeshellarg($source) . ' ' . escapeshellarg(home_url()) . ' --all-tables-with-prefix --skip-columns=guid');
}
flush_rewrite_rules();
WP_CLI::success('Editorial content restored; users and private configuration preserved.');

