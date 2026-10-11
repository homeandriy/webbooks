<?php
define( 'WEBBOOKS_TEST_RUNNER', true );
$_SERVER['HTTP_HOST'] = 'webbooks.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_NAME'] = 'webbooks.test';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
require '/var/www/html/wp-load.php';
require __DIR__ . '/integration.php';
