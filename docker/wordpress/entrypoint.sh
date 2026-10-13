#!/usr/bin/env bash
set -euo pipefail
umask 022
if [[ ! -f wp-includes/version.php ]]; then
    echo 'Populating local WordPress core from the pinned official image.'
    bundled_extras=()
    for extra in wp-config-docker.php wp-content/plugins/hello.php \
        wp-content/plugins/akismet wp-content/themes/twentytwentythree \
        wp-content/themes/twentytwentyfour; do
        [[ -e "$extra" || -L "$extra" ]] || bundled_extras+=("$extra")
    done
    cp -rn /usr/src/wordpress/. /var/www/html/
    # Keep the latest bundled fallback theme. Optional Akismet comes from the
    # manifest; omit unused bundled extras, preserving any pre-existing files.
    for extra in "${bundled_extras[@]}"; do rm -rf -- "$extra"; done
fi
[[ -f wp-config.php ]] || cp /opt/portfolio/app/wordpress/wp-config.php wp-config.php
[[ -f .htaccess ]] || cp /opt/portfolio/docker/wordpress/htaccess .htaccess
mkdir -p wp-content/uploads wp-content/plugins
if ! wp --skip-plugins --skip-themes core is-installed; then
    php /opt/portfolio/scripts/install-wordpress.php
fi
/opt/portfolio/scripts/install-plugins.sh --container
wp eval-file /opt/portfolio/scripts/configure-wordpress.php
if [[ -f /opt/portfolio/database/seeds/content.json ]] && \
   [[ "$(wp --skip-plugins --skip-themes option get portfolio_seed_pending 2>/dev/null || true)" == 1 ]]; then
    wp eval-file /opt/portfolio/scripts/content-snapshot.php import /opt/portfolio/database/seeds/content.json
    wp --skip-plugins --skip-themes option delete portfolio_seed_pending
    wp --skip-plugins --skip-themes option update portfolio_seed_imported 1
fi
exec "$@"

