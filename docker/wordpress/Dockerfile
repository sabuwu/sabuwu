FROM wordpress:7.1.3-php8.3-apache@sha256:8ecd8d8e374167ea22235acf30f80d1165b82f07ffa5bcef90c1cad8f7be251f

ARG APP_UID=1000
ARG APP_GID=1000
RUN groupmod -o -g "$APP_GID" www-data \
    && usermod -o -u "$APP_UID" -g "$APP_GID" www-data \
    && sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/\*:80/*:8080/' /etc/apache2/sites-available/000-default.conf \
    && mkdir -p /tmp/apache2 /var/www/.wp-cli/cache \
    && chown -R www-data:www-data /tmp/apache2 /var/www /var/run/apache2 /var/log/apache2 \
    && a2enmod headers rewrite \
    && curl -fsSL --retry 3 https://github.com/wp-cli/wp-cli/releases/download/v2.12.0/wp-cli-2.12.0.phar -o /usr/local/bin/wp \
    && echo 'ce34ddd838f7351d6759068d09793f26755463b4a4610a5a5c0a97b68220d85c  /usr/local/bin/wp' | sha256sum -c - \
    && chmod 755 /usr/local/bin/wp

COPY . /opt/portfolio
RUN cp /opt/portfolio/docker/php/portfolio.ini /usr/local/etc/php/conf.d/portfolio.ini \
    && cp /opt/portfolio/docker/wordpress/security.conf /etc/apache2/conf-enabled/portfolio-security.conf \
    && find /opt/portfolio/database/seeds -type f -name '*.json' -exec chmod 644 {} +

ENV APACHE_RUN_DIR=/tmp/apache2 APACHE_PID_FILE=/tmp/apache2/apache2.pid \
    APACHE_LOCK_DIR=/tmp/apache2 WP_CLI_CACHE_DIR=/var/www/.wp-cli/cache
WORKDIR /var/www/html
USER www-data
EXPOSE 8080
ENTRYPOINT ["/opt/portfolio/docker/wordpress/entrypoint.sh"]
CMD ["apache2-foreground"]

