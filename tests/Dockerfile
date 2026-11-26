ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-cli
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libpng-dev libjpeg62-turbo-dev libwebp-dev libxml2-dev gettext zip unzip \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j2 mysqli mbstring gd \
    && echo 'memory_limit=512M' > /usr/local/etc/php/conf.d/tests.ini \
    && curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp
WORKDIR /var/www/html
COPY run-integration.sh /usr/local/bin/run-integration
ENTRYPOINT ["bash", "/usr/local/bin/run-integration"]

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
