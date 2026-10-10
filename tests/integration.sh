#!/usr/bin/env bash
# Exercises real HTTP/database/cron boundaries without real user credentials.
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/../scripts" && pwd)"
url="$("$script_dir/wp.sh" --skip-plugins --skip-themes option get home)"
for endpoint in /wp-config.php /wp-config-sample.php /xmlrpc.php; do
    status="$(curl -sS --max-time 10 -o /dev/null -w '%{http_code}' "${url%/}$endpoint")"
    [[ "$status" == 403 ]] || { echo "Expected denied access for $endpoint; got $status" >&2; exit 1; }
done
"$script_dir/compose.sh" exec -T wordpress sh -c 'printf "<?php echo 12345;" > wp-content/uploads/portfolio-security-probe.php'
trap '"$script_dir/compose.sh" exec -T wordpress rm -f wp-content/uploads/portfolio-security-probe.php >/dev/null 2>&1 || true' EXIT
status="$(curl -sS --max-time 10 -o /dev/null -w '%{http_code}' "${url%/}/wp-content/uploads/portfolio-security-probe.php")"
[[ "$status" == 403 ]] || { echo "Uploads executed or exposed PHP: $status" >&2; exit 1; }
"$script_dir/wp.sh" eval 'if (!DISABLE_WP_CRON || !DISALLOW_FILE_EDIT) { WP_CLI::error("Required hardening missing"); } if (getenv("DB_ROOT_PASSWORD") || is_readable("/run/database-secrets/DB_ROOT_PASSWORD")) { WP_CLI::error("Application can read root password"); } WP_CLI::success("Required configuration and secret separation");'
"$script_dir/wp.sh" option delete portfolio_cron_last_probe >/dev/null 2>&1 || true
scheduled="$(date +%s)"
"$script_dir/wp.sh" cron event schedule portfolio_cron_probe now
# This test does not execute the event itself. The external cron must consume it.
for (( attempt=0; attempt<45; attempt++ )); do
    executed="$("$script_dir/wp.sh" --skip-plugins --skip-themes option get portfolio_cron_last_probe 2>/dev/null || true)"
    if [[ "$executed" =~ ^[0-9]+$ ]] && (( executed >= scheduled )); then
        printf 'External cron executed scheduled event at %s.\n' "$executed"
        exit 0
    fi
    sleep 2
done
echo 'External cron did not execute the event within 90 seconds.' >&2
exit 1

