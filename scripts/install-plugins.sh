#!/usr/bin/env bash
set -euo pipefail
script_dir="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
if [[ "${1:-}" != --container ]]; then
    exec "$script_dir/compose.sh" run --rm -T --no-deps --entrypoint /opt/portfolio/scripts/install-plugins.sh wp-cli --container
fi
# Activation still runs the target plugin's hooks; unrelated plugins need not
# load repeatedly while downloading the locked official dependencies.
wp_plugin() { wp --skip-plugins --skip-themes plugin "$@"; }
while read -r plugin version environment; do
    [[ -z "$plugin" || "$plugin" == \#* ]] && continue
    if [[ "$environment" == development && "${WP_ENVIRONMENT_TYPE:-development}" == production ]]; then
        # Load the active plugin so its deactivation cleanup hooks execute.
        if wp_plugin is-active "$plugin"; then wp --skip-themes plugin deactivate "$plugin"; fi
        # QM's CLI drop-in returns before defining QM_DB, so its own cleanup
        # leaves the symlink behind. Remove only the link to this dependency.
        if [[ "$plugin" == query-monitor && -L wp-content/db.php ]] && \
           [[ "$(readlink -f wp-content/db.php)" == "$(readlink -f wp-content/plugins/query-monitor/wp-content/db.php)" ]]; then
            rm -- wp-content/db.php
        fi
        continue
    fi
    installed="$(wp_plugin get "$plugin" --field=version 2>/dev/null || true)"
    if [[ "$installed" != "$version" ]]; then
        wp_plugin install "https://downloads.wordpress.org/plugin/${plugin}.${version}.zip" --force
    fi
    if ! wp_plugin is-active "$plugin"; then wp_plugin activate "$plugin"; fi
done < /opt/portfolio/config/plugins.lock

