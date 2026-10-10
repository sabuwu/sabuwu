#!/usr/bin/env bash
set -euo pipefail
umask 022
if [[ ! -f wp-includes/version.php ]]; then
    echo 'Populating local WordPress core from the pinned official image.'
    bundled_hello=true
    [[ -f wp-content/plugins/hello.php ]] && bundled_hello=false
    bundled_config=true
    [[ -f wp-config-docker.php ]] && bundled_config=false
    cp -rn /usr/src/wordpress/. /var/www/html/
    # Omit the unused bundled sample, preserving any pre-existing author file.
    [[ "$bundled_hello" == false ]] || rm -f wp-content/plugins/hello.php
    [[ "$bundled_config" == false ]] || rm -f wp-config-docker.php
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

