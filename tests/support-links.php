<?php
declare(strict_types=1);

// Isolate optional plugins while retaining real WordPress page and cache behavior.
define('WP_PLUGIN_DIR', '/tmp/webbooks-support-tests-no-plugins');
define('WP_USE_THEMES', false);
$GLOBALS['support_test_language'] = 'uk';
$GLOBALS['support_test_page_languages'] = [];
function pll_current_language(string $field = 'slug'): string
{
    return $GLOBALS['support_test_language'];
}
function pll_get_post_language(int $id, string $field = 'slug'): string
{
    return $GLOBALS['support_test_page_languages'][$id] ?? '';
}
$_SERVER['HTTP_HOST'] = 'webbooks.test';
require '/var/www/html/wp-load.php';
if (DB_NAME !== 'webbooks_tests') {
    throw new RuntimeException('Disposable test database required.');
}

use Webbooks\Support\AuthorSupport;

require_once '/theme/src/Localization/Polylang.php';
require_once '/theme/src/Support/AuthorSupport.php';
AuthorSupport::register();

$assertions = 0;
$ids = [];
$assert = static function (bool $condition, string $message) use (&$assertions): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    ++$assertions;
};
try {
    foreach (['uk' => 'pidtrymaty-avtora', 'ru' => 'podderzhat-avtora', 'pl' => 'wesprzyj-autora'] as $lang => $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            throw new RuntimeException('Test slugs must be absent from disposable database.');
        }
        $id = wp_insert_post(['post_type' => 'page', 'post_name' => $slug, 'post_title' => $slug, 'post_status' => 'draft'], true);
        if (is_wp_error($id)) {
            throw new RuntimeException($id->get_error_message());
        }
        $ids[$lang] = $id;
        $GLOBALS['support_test_page_languages'][$id] = $lang;
    }
    wp_update_post(['ID' => $ids['uk'], 'post_status' => 'publish']);
    $uk = get_permalink($ids['uk']);
    foreach (['uk', 'ru', 'pl'] as $lang) {
        $GLOBALS['support_test_language'] = $lang;
        $assert(AuthorSupport::publishedPageUrl() === $uk, "$lang gets the published fallback.");
    }
    $assert(get_post_status($ids['ru']) === 'draft' && get_post_status($ids['pl']) === 'draft', 'Draft translations remain drafts.');
    wp_update_post(['ID' => $ids['ru'], 'post_status' => 'publish']);
    $GLOBALS['support_test_language'] = 'ru';
    $assert(AuthorSupport::publishedPageUrl() === get_permalink($ids['ru']), 'Publishing translation invalidates fallback and selects translation.');
    wp_update_post(['ID' => $ids['uk'], 'post_status' => 'draft']);
    $GLOBALS['support_test_language'] = 'pl';
    $assert(AuthorSupport::publishedPageUrl() === '', 'Withdrawing fallback invalidates all cached language links.');
    $GLOBALS['support_test_language'] = 'ru';
    $assert(AuthorSupport::publishedPageUrl() === get_permalink($ids['ru']), 'Published translation remains available without fallback.');
    wp_update_post(['ID' => $ids['ru'], 'post_status' => 'draft']);
    $assert(AuthorSupport::publishedPageUrl() === '', 'No draft URL is exposed.');
    echo "PASS: $assertions support-link assertions\n";
} finally {
    foreach ($ids as $id) {
        wp_delete_post($id, true);
    }
}
