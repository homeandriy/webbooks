#!/usr/bin/env bash
set -euo pipefail
# WP-CLI's bundled libraries currently emit PHP 8.5 deprecations. Suppress them
# only during installation; the standalone theme runner below uses E_ALL.
wp() { php -d error_reporting=8191 /usr/local/bin/wp "$@"; }
wp --allow-root core download --version="${WP_VERSION:-latest}" --force
wp --allow-root config create --dbname=webbooks_tests --dbuser=webbooks_tests --dbpass=isolated-test-password --dbhost=db --skip-check --force
wp --allow-root config set WP_DEBUG true --raw
wp --allow-root config set WP_DEBUG_DISPLAY true --raw
if ! wp --allow-root core is-installed; then
    wp --allow-root core install --url=http://webbooks.test --title='Webbooks tests' --admin_user=test_admin --admin_password=isolated-admin-password --admin_email=test@example.test --skip-email
fi
ln -sfn /theme wp-content/themes/webbooks
wp --allow-root plugin deactivate advanced-custom-fields polylang ninja-forms --skip-plugins --skip-themes || true
wp --allow-root theme activate webbooks
php -d error_reporting=32767 /theme/tests/bootstrap.php
if [[ "${WITH_PLUGINS:-0}" == 1 ]]; then
    wp --allow-root plugin install advanced-custom-fields polylang ninja-forms
    wp --allow-root plugin activate advanced-custom-fields polylang ninja-forms
    wp --allow-root plugin list --fields=name,status,version
    php -d error_reporting=32767 /theme/tests/bootstrap.php
fi
if [[ "${PREVIEW:-0}" == 1 ]]; then
    php /theme/tests/preview-fixtures.php
fi
