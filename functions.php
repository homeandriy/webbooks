<?php

const WEBBOOKS_VERSION = '1.7.5';
const DOWNLOAD_BOOK_NONCE = 'download_book_nonce';
const GENERAL_NONCE = 'myajax-nonce';

define('WEBBOOKS_PATH', get_stylesheet_directory());
define('WEBBOOKS_URL', get_stylesheet_directory_uri());

require_once WEBBOOKS_PATH . '/options_page.php';
require_once WEBBOOKS_PATH . '/src/Domain/Book/Language.php';
require_once WEBBOOKS_PATH . '/src/Domain/Book/Complexity.php';

if (file_exists(WEBBOOKS_PATH . '/vendor/autoload.php')) {
    require_once WEBBOOKS_PATH . '/vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'Webbooks\\';
        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file = WEBBOOKS_PATH . '/src/' . str_replace('\\', '/', $relative) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    });
}

$modules = [
    '/inc/setup.php',
    '/inc/assets.php',
    '/inc/helpers.php',
    '/inc/seo.php',
    '/inc/structured-data.php',
    '/inc/ajax/search.php',
    '/inc/ajax/download.php',
    '/inc/comments-security.php',
];

foreach ($modules as $module) {
    require_once WEBBOOKS_PATH . $module;
}
