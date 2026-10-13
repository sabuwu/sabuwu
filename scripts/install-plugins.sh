#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
if [[ "${1:-}" != --container ]]; then
    exec "$script_dir/compose.sh" run --rm -T --no-deps --entrypoint /opt/portfolio/scripts/install-plugins.sh wp-cli --container
fi
# Activation still runs the target plugin's hooks; unrelated plugins need not
# load repeatedly while downloading the locked official dependencies.
wp_plugin() { wp --skip-plugins --skip-themes plugin "$@"; }
while read -r plugin version scope; do
    [[ -z "$plugin" || "$plugin" == \#* ]] && continue
    required=1
    case "$scope" in
        all) ;;
        development)
            case "${WP_ENVIRONMENT_TYPE:-development}" in
                local|development) ;;
                *) required=0 ;;
            esac
            ;;
        akismet)
            required="$(wp --skip-plugins --skip-themes eval 'echo (getenv("AKISMET_API_KEY") || (defined("WPCOM_API_KEY") && WPCOM_API_KEY) || get_option("wordpress_api_key")) ? "1" : "0";')"
            ;;
        smtp)
            required="$(wp --skip-plugins --skip-themes eval '$options = get_option("wp_mail_smtp", []); $mailer = $options["mail"]["mailer"] ?? "mail"; echo (getenv("SMTP_HOST") || (defined("WPMS_ON") && WPMS_ON && defined("WPMS_MAILER") && WPMS_MAILER !== "mail") || ($mailer !== "" && $mailer !== "mail")) ? "1" : "0";')"
            ;;
        *) echo "Unknown plugin scope: $scope ($plugin)" >&2; exit 1 ;;
    esac
    if [[ "$required" == 0 ]]; then
        # Load the active plugin so its deactivation cleanup hooks execute.
        if wp_plugin is-active "$plugin"; then wp --skip-themes plugin deactivate "$plugin"; fi
        # QM's CLI drop-in returns before defining QM_DB, so its own cleanup
        # leaves the symlink behind. Remove only the link to this dependency.
        if [[ "$plugin" == query-monitor && -L wp-content/db.php ]]; then
            qm_dropin="$(readlink -f wp-content/plugins/query-monitor/wp-content/db.php 2>/dev/null || true)"
            current_dropin="$(readlink -f wp-content/db.php 2>/dev/null || true)"
            if [[ -n "$qm_dropin" && "$current_dropin" == "$qm_dropin" ]]; then
                rm -- wp-content/db.php
            fi
        fi
        continue
    fi
    installed="$(wp_plugin get "$plugin" --field=version 2>/dev/null || true)"
    if [[ "$installed" != "$version" ]]; then
        wp_plugin install "https://downloads.wordpress.org/plugin/${plugin}.${version}.zip" --force
    fi
    if ! wp_plugin is-active "$plugin"; then wp_plugin activate "$plugin"; fi
done < /opt/portfolio/config/plugins.lock

