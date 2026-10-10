#!/usr/bin/env bash
set -euo pipefail
wp --skip-plugins --skip-themes eval 'global $wpdb; if (!is_blog_installed() || (string) $wpdb->get_var("SELECT 1") !== "1") { exit(1); }' >/dev/null
curl -fsS --max-time 10 http://127.0.0.1:8080/wp-login.php | grep 'id="user_login"' >/dev/null

