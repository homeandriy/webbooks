FROM php:8.5-cli
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev gettext zip unzip \
    && docker-php-ext-install mysqli mbstring \
    && echo 'memory_limit=512M' > /usr/local/etc/php/conf.d/tests.ini \
    && curl -fsSL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp \
    && chmod +x /usr/local/bin/wp
WORKDIR /var/www/html
COPY run-integration.sh /usr/local/bin/run-integration
ENTRYPOINT ["bash", "/usr/local/bin/run-integration"]
