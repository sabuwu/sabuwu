#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
url="$("$script_dir/wp.sh" --skip-plugins --skip-themes option get home)"
temporary="$(mktemp)"
trap 'rm -f -- "$temporary"' EXIT
for endpoint in / /wp-login.php /wp-admin/; do
    status="$(curl -sS --max-time 20 -o "$temporary" -w '%{http_code}' "${url%/}$endpoint")"
    case "$status" in 200|301|302) ;; *) echo "$endpoint: HTTP $status" >&2; exit 1 ;; esac
    if [[ "$endpoint" == /wp-login.php ]]; then grep -q 'id="user_login"' "$temporary"; fi
    if grep -qiE 'PHP Fatal error|Error establishing a database connection' "$temporary"; then exit 1; fi
    printf '%s HTTP %s\n' "$endpoint" "$status"
done
"$script_dir/wp.sh" eval 'global $wpdb; if ((string) $wpdb->get_var("SELECT 1") !== "1") { WP_CLI::error("Database integration failed"); } WP_CLI::success("WordPress database integration");'
"$script_dir/wp.sh" cron event list
"$script_dir/wp.sh" cron event run --due-now
"$script_dir/wp.sh" plugin list
"$script_dir/wp.sh" theme list
"$script_dir/wp.sh" core verify-checksums
"$script_dir/wp.sh" plugin verify-checksums --all --strict
"$script_dir/compose.sh" ps

