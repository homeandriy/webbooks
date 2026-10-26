<?php
declare(strict_types=1);

// Run only against the disposable Docker database configured in tests/compose.yml.
define('WP_USE_THEMES', false);
require '/var/www/html/wp-load.php';
if (DB_NAME !== 'webbooks_tests') {
    throw new RuntimeException('Refusing to modify a non-test database.');
}
require_once '/theme/plugins/webbooks-git-updates/webbooks-git-updates.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/template.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

use Webbooks\GitUpdates\Updater;

$assertions = 0;
function verify(bool $condition, string $message): void
{
    global $assertions;
    ++$assertions;
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
delete_option('webbooks_git_updates_settings');
delete_option('webbooks_git_updates_state');
delete_option('webbooks_git_updates_lock');
Updater::activate();
verify((bool) wp_next_scheduled('webbooks_git_updates_check'), 'Activation schedules checks.');
$current = wp_get_theme('webbooks')->get('Version');
$new = '99.0.0';
$mode = 'current';
$calls = 0;
$mock = static function (mixed $pre, array $args, string $url) use (&$mode, &$calls, $current, $new): mixed {
    if (!str_starts_with($url, 'https://api.github.com/repos/homeandriy/webbooks/')) {
        return $pre;
    }
    ++$calls;
    if ($mode === 'network') {
        return new WP_Error('network', 'secret-token-must-not-be-logged');
    }
    if ($mode === 'rate') {
        return ['response' => ['code' => 403], 'body' => '{"message":"rate limit"}'];
    }
    $version = $mode === 'current' ? $current : $new;
    $data = ['sha' => str_repeat('a', 40)];
    $code = 200;
    if (str_contains($url, '/contents/')) {
        $data = ['encoding' => 'base64', 'content' => base64_encode("/*\nVersion: $version\n*/")];
    } elseif (str_contains($url, '/releases/')) {
        if ($mode === 'pending') {
            $code = 404;
            $data = [];
        } else {
            $data = ['tag_name' => 'v' . $new, 'draft' => false, 'prerelease' => $mode === 'prerelease', 'assets' => [[
                'name' => 'webbooks-theme-release.zip', 'size' => 1234,
                'browser_download_url' => ($mode === 'hostile' ? 'https://evil.test/' : 'https://github.com/homeandriy/webbooks/releases/download/v' . $new . '/') . 'webbooks-theme-release.zip',
            ]]];
        }
    }
    return ['response' => ['code' => $code], 'body' => json_encode($data)];
};
add_filter('pre_http_request', $mock, 10, 3);

Updater::check();
$transient = Updater::updates((object) ['response' => ['another-theme' => ['new_version' => '2.0']]]);
verify(isset($transient->no_update['webbooks']), 'Current theme is not offered an update.');
verify(isset($transient->response['another-theme']), 'Other themes are untouched.');

$mode = 'pending';
Updater::check();
$state = get_option('webbooks_git_updates_state');
verify($state['remote']['version'] === $new && !$state['remote']['package'], 'New Git version with no ZIP is detected.');
wp_set_current_user(1);
ob_start();
Updater::notice();
$html = ob_get_clean();
verify(str_contains($html, '99.0.0') && !str_contains($html, 'upgrade-theme&'), 'Pending version notice has no install action.');

$mode = 'ready';
Updater::check();
$transient = Updater::updates((object) []);
verify($transient->response['webbooks']['new_version'] === $new, 'Release asset is offered through WordPress.');
verify(Updater::automaticUpdates(true, (object) ['theme' => 'webbooks']) === false, 'Theme installation stays manual.');
verify(Updater::automaticUpdates(true, (object) ['theme' => 'another']) === true, 'Other automatic updates are preserved.');
ob_start();
Updater::notice();
$html = ob_get_clean();
verify(str_contains($html, 'upgrade-theme') && str_contains($html, '_wpnonce'), 'Install action includes a nonce.');

$root = sys_get_temp_dir() . '/webbooks-updater-' . wp_generate_uuid4();
$source = $root . '/webbooks/';
wp_mkdir_p($source . 'dist/.vite');
file_put_contents($source . 'style.css', "/*\nTheme Name: Webbooks\nVersion: $new\nRequires PHP: 8.2\nRequires at least: 6.5\n*/");
file_put_contents($source . 'functions.php', '<?php');
file_put_contents($source . 'dist/.vite/manifest.json', '{}');
file_put_contents($source . 'dist/.vite/build-tag', 'test');
$extra = ['theme' => 'webbooks', 'action' => 'update', 'type' => 'theme'];
verify(Updater::validateSource($source, $root, new stdClass(), $extra) === $source, 'Built release with matching version passes.');
file_put_contents($source . 'style.css', "/*\nVersion: 1.0.0\n*/");
verify(is_wp_error(Updater::validateSource($source, $root, new stdClass(), $extra)), 'Wrong archive version is rejected.');
verify(Updater::validateSource($source, $root, new stdClass(), ['theme' => 'another']) === $source, 'Other theme packages are untouched.');

foreach (['hostile', 'prerelease'] as $mode) {
    Updater::check();
    verify(empty(Updater::updates((object) [])->response['webbooks']), "$mode asset is rejected.");
}
foreach (['rate', 'network'] as $mode) {
    Updater::check();
    verify(empty(Updater::updates((object) [])->response['webbooks']), "$mode error clears install availability.");
}
verify(!str_contains(json_encode(get_option('webbooks_git_updates_state')), 'secret-token'), 'Transport details are not logged.');

$mode = 'ready';
Updater::check();
$state = get_option('webbooks_git_updates_state');
$state['last_success'] = time() - 2 * DAY_IN_SECONDS;
update_option('webbooks_git_updates_state', $state);
verify(empty(Updater::updates((object) [])->response['webbooks']), 'Stale metadata cannot install.');
Updater::check();
$before = $calls;
add_option('webbooks_git_updates_lock', time(), '', false);
Updater::check();
verify($calls === $before, 'Concurrent check is blocked.');
delete_option('webbooks_git_updates_lock');

$invalid = Updater::sanitize(['enabled' => 1, 'branch' => '../bad']);
verify($invalid['branch'] === 'main', 'Invalid branch is rejected.');
$disabled = Updater::sanitize(['branch' => 'main']);
update_option('webbooks_git_updates_settings', $disabled);
verify(!wp_next_scheduled('webbooks_git_updates_check'), 'Disabling stops scheduled checks.');
$before = $calls;
Updater::check();
verify($calls === $before, 'Disabled plugin performs no requests.');
update_option('webbooks_git_updates_settings', Updater::sanitize(['enabled' => 1, 'branch' => 'main']));
$mode = 'ready';
for ($i = 0; $i < 52; ++$i) {
    Updater::check();
}
verify(count(get_option('webbooks_git_updates_state')['events']) === 50, 'Event history is bounded.');
Updater::settings();
ob_start();
Updater::settingsPage();
$settings = ob_get_clean();
verify(str_contains($settings, 'options.php') && str_contains($settings, 'wb-branch'), 'Settings screen renders its form.');
ob_start();
Updater::statisticsPage();
$stats = ob_get_clean();
verify(str_contains($stats, 'admin-post.php') && str_contains($stats, '99.0.0'), 'Statistics screen renders state.');
wp_set_current_user(0);
ob_start();
Updater::notice();
verify(ob_get_clean() === '', 'Visitors do not see administrator notices.');
Updater::deactivate();
verify(!wp_next_scheduled('webbooks_git_updates_check'), 'Deactivation clears schedule.');
remove_filter('pre_http_request', $mock, 10);
echo "PASS: $assertions updater assertions\n";
