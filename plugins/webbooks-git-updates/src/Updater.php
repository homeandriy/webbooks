<?php
declare(strict_types=1);

namespace Webbooks\GitUpdates;

use WP_Error;

defined('ABSPATH') || exit;

final class Updater
{
    private const OPTION = 'webbooks_git_updates_settings';
    private const STATE = 'webbooks_git_updates_state';
    private const CRON = 'webbooks_git_updates_check';
    private const THEME = 'webbooks';
    private const REPOSITORY = 'homeandriy/webbooks';

    public static function register(): void
    {
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_init', [self::class, 'settings']);
        add_action('admin_notices', [self::class, 'notice']);
        add_action('admin_post_webbooks_git_check', [self::class, 'manualCheck']);
        add_action(self::CRON, [self::class, 'check']);
        add_filter('pre_set_site_transient_update_themes', [self::class, 'updates'], 100);
        add_filter('site_transient_update_themes', [self::class, 'updates'], 100);
        add_filter('auto_update_theme', [self::class, 'automaticUpdates'], 100, 2);
        add_filter('upgrader_source_selection', [self::class, 'validateSource'], 10, 4);
        add_action('upgrader_process_complete', [self::class, 'installed'], 10, 2);
    }

    public static function activate(): void
    {
        if (!is_multisite() && self::config()['enabled'] && !wp_next_scheduled(self::CRON)) {
            wp_schedule_event(time() + 60, 'twicedaily', self::CRON);
        }
    }

    public static function deactivate(): void
    {
        wp_clear_scheduled_hook(self::CRON);
    }

    private static function config(): array
    {
        return wp_parse_args((array) get_option(self::OPTION, []), ['enabled' => 1, 'branch' => 'main']);
    }

    private static function state(): array
    {
        return (array) get_option(self::STATE, []);
    }

    public static function settings(): void
    {
        register_setting('webbooks_git_updates', self::OPTION, [
            'type' => 'array', 'sanitize_callback' => [self::class, 'sanitize'], 'show_in_rest' => false,
        ]);
    }

    public static function sanitize(mixed $input): array
    {
        $input = is_array($input) ? $input : [];
        $branch = trim((string) ($input['branch'] ?? 'main'));
        if (!preg_match('~\A[A-Za-z0-9][A-Za-z0-9._/-]{0,100}\z~D', $branch) || str_contains($branch, '..')) {
            add_settings_error(self::OPTION, 'branch', __('Некоректна назва гілки.', 'webbooks-git-updates'));
            return self::config();
        }
        $settings = ['enabled' => empty($input['enabled']) ? 0 : 1, 'branch' => $branch];
        if ($settings !== self::config()) {
            $state = self::state();
            unset($state['remote']);
            update_option(self::STATE, $state, false);
            wp_clear_scheduled_hook(self::CRON);
            if ($settings['enabled']) {
                wp_schedule_event(time() + 60, 'twicedaily', self::CRON);
            }
        }
        return $settings;
    }

    private static function event(string $type, string $message, array $values = []): void
    {
        $state = array_merge(self::state(), $values);
        $state['events'] = array_slice(array_merge([
            ['time' => time(), 'type' => $type, 'message' => $message],
        ], (array) ($state['events'] ?? [])), 0, 50);
        $state[$type] = (int) ($state[$type] ?? 0) + 1;
        update_option(self::STATE, $state, false);
    }

    /** API requests stay on a fixed host. Tokens never enter package URLs. */
    private static function github(string $path): array|WP_Error
    {
        $headers = ['Accept' => 'application/vnd.github+json', 'User-Agent' => 'Webbooks-Git-Updates/1.0'];
        if (defined('WEBBOOKS_GITHUB_TOKEN') && WEBBOOKS_GITHUB_TOKEN !== '') {
            $headers['Authorization'] = 'Bearer ' . WEBBOOKS_GITHUB_TOKEN;
        }
        $response = wp_remote_get('https://api.github.com/repos/' . self::REPOSITORY . $path, [
            'headers' => $headers, 'timeout' => 20, 'redirection' => 0, 'limit_response_size' => 1048576,
        ]);
        if (is_wp_error($response)) {
            return new WP_Error('github_network', __('Не вдалося з’єднатися з GitHub.', 'webbooks-git-updates'));
        }
        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            return new WP_Error('github_http', sprintf(__('GitHub повернув HTTP %d. Перевірте доступ і ліміт API.', 'webbooks-git-updates'), $code), $code);
        }
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return is_array($body) ? $body : new WP_Error('github_json', __('Некоректна відповідь GitHub.', 'webbooks-git-updates'));
    }

    public static function check(): void
    {
        if (!self::config()['enabled'] || !wp_get_theme(self::THEME)->exists()) {
            return;
        }
        // Atomic lock prevents overlapping manual and scheduled checks.
        if (!add_option('webbooks_git_updates_lock', time(), '', false)) {
            if ((int) get_option('webbooks_git_updates_lock') <= time() - 300) {
                delete_option('webbooks_git_updates_lock');
            }
            return;
        }
        try {
            self::fetch();
        } finally {
            delete_option('webbooks_git_updates_lock');
        }
    }

    private static function fetch(): void
    {
        $state = self::state();
        $values = ['last_checked' => time(), 'checks' => (int) ($state['checks'] ?? 0) + 1];
        $commit = self::github('/commits/' . rawurlencode(self::config()['branch']));
        if (is_wp_error($commit) || !preg_match('/\A[0-9a-f]{40}\z/D', (string) ($commit['sha'] ?? ''))) {
            self::event('errors', is_wp_error($commit) ? $commit->get_error_message() : __('Некоректний Git commit.', 'webbooks-git-updates'), $values + ['error' => true]);
            return;
        }
        $sha = $commit['sha'];
        $file = self::github('/contents/style.css?ref=' . $sha);
        $css = !is_wp_error($file) && ($file['encoding'] ?? '') === 'base64' ? base64_decode((string) ($file['content'] ?? ''), true) : false;
        if (!is_string($css) || !preg_match('/^[ \t*]*Version:[ \t]*([0-9]+\.[0-9]+\.[0-9]+)[ \t]*\r?$/mi', $css, $match)) {
            self::event('errors', __('Не вдалося прочитати версію style.css у Git.', 'webbooks-git-updates'), $values + ['error' => true]);
            return;
        }
        $version = $match[1];
        $remote = ['version' => $version, 'sha' => $sha, 'package' => '', 'branch' => self::config()['branch']];
        $message = sprintf(__('Перевірено Git: версія %s.', 'webbooks-git-updates'), $version);
        if (version_compare($version, wp_get_theme(self::THEME)->get('Version'), '>')) {
            $release = self::github('/releases/tags/v' . $version);
            if (is_wp_error($release) && $release->get_error_data() !== 404) {
                self::event('errors', $release->get_error_message(), $values + ['error' => true, 'remote' => $remote]);
                return;
            }
            if (!is_wp_error($release) && empty($release['draft']) && empty($release['prerelease']) && ($release['tag_name'] ?? '') === 'v' . $version) {
                foreach ((array) ($release['assets'] ?? []) as $asset) {
                    $url = (string) ($asset['browser_download_url'] ?? '');
                    $expected = 'https://github.com/' . self::REPOSITORY . '/releases/download/v' . $version . '/webbooks-theme-release.zip';
                    if (($asset['name'] ?? '') === 'webbooks-theme-release.zip' && $url === $expected && ($asset['size'] ?? 0) > 0) {
                        $remote['package'] = $url;
                        break;
                    }
                }
            }
            $message = $remote['package'] ? sprintf(__('Доступна нова версія %s для встановлення.', 'webbooks-git-updates'), $version) : sprintf(__('У Git є версія %s. Очікується готовий ZIP у GitHub Release.', 'webbooks-git-updates'), $version);
        }
        self::event('successful_checks', $message, $values + ['remote' => $remote, 'error' => false, 'last_success' => time()]);
    }

    private static function available(): ?array
    {
        $state = self::state();
        $remote = $state['remote'] ?? null;
        if (!self::config()['enabled'] || !is_array($remote) || !empty($state['error']) || ($state['last_success'] ?? 0) < time() - DAY_IN_SECONDS || ($remote['branch'] ?? '') !== self::config()['branch'] || !wp_get_theme(self::THEME)->exists()) {
            return null;
        }
        return version_compare($remote['version'], wp_get_theme(self::THEME)->get('Version'), '>') ? $remote : null;
    }

    public static function updates(mixed $transient): mixed
    {
        if (!is_object($transient) || !self::config()['enabled'] || !wp_get_theme(self::THEME)->exists()) {
            return $transient;
        }
        unset($transient->response[self::THEME], $transient->no_update[self::THEME]);
        $remote = self::available();
        $item = ['theme' => self::THEME, 'new_version' => wp_get_theme(self::THEME)->get('Version'), 'url' => 'https://github.com/' . self::REPOSITORY, 'package' => '', 'requires' => '6.5', 'requires_php' => '8.2'];
        if ($remote && $remote['package']) {
            $item['new_version'] = $remote['version'];
            $item['package'] = $remote['package'];
            $transient->response[self::THEME] = $item;
        } else {
            $transient->no_update[self::THEME] = $item;
        }
        return $transient;
    }

    public static function automaticUpdates(?bool $update, object $item): ?bool
    {
        return self::config()['enabled'] && ($item->theme ?? '') === self::THEME ? false : $update;
    }

    public static function validateSource(string|WP_Error $source, string $remoteSource, object $upgrader, array $extra): string|WP_Error
    {
        if (is_wp_error($source) || ($extra['theme'] ?? '') !== self::THEME || ($extra['action'] ?? '') !== 'update' || !self::config()['enabled']) {
            return $source;
        }
        $source = trailingslashit($source);
        $remote = self::available();
        $headers = is_file($source . 'style.css') ? get_file_data($source . 'style.css', ['Version' => 'Version', 'PHP' => 'Requires PHP', 'WP' => 'Requires at least']) : [];
        if (!$remote || !$remote['package'] || basename(untrailingslashit($source)) !== self::THEME || ($headers['Version'] ?? '') !== $remote['version'] || !is_file($source . 'functions.php') || !is_file($source . 'dist/.vite/manifest.json') || !is_file($source . 'dist/.vite/build-tag')) {
            self::event('errors', __('Встановлення зупинено: ZIP не містить очікуваної зібраної версії Webbooks.', 'webbooks-git-updates'));
            return new WP_Error('webbooks_invalid_package', __('Некоректний релізний ZIP Webbooks.', 'webbooks-git-updates'));
        }
        if ((!empty($headers['PHP']) && version_compare(PHP_VERSION, $headers['PHP'], '<')) || (!empty($headers['WP']) && version_compare(get_bloginfo('version'), $headers['WP'], '<'))) {
            return new WP_Error('webbooks_incompatible_package', __('Реліз потребує новішої версії PHP або WordPress.', 'webbooks-git-updates'));
        }
        return $source;
    }

    public static function installed(object $upgrader, array $extra): void
    {
        $themes = $extra['themes'] ?? [$extra['theme'] ?? ''];
        if (($extra['type'] ?? '') !== 'theme' || ($extra['action'] ?? '') !== 'update' || !in_array(self::THEME, $themes, true)) {
            return;
        }
        $result = $upgrader->result ?? false;
        if (is_wp_error($result) || !$result) {
            self::event('errors', __('WordPress не завершив встановлення теми.', 'webbooks-git-updates'));
            return;
        }
        wp_clean_themes_cache();
        $expected = self::state()['remote']['version'] ?? '';
        if (!$expected || wp_get_theme(self::THEME)->get('Version') !== $expected) {
            self::event('errors', __('Встановлена версія не збігається з очікуваним релізом.', 'webbooks-git-updates'));
            return;
        }
        self::event('installations', sprintf(__('Встановлено Webbooks %s.', 'webbooks-git-updates'), wp_get_theme(self::THEME)->get('Version')), ['last_install' => time()]);
    }

    public static function menu(): void
    {
        add_menu_page('Webbooks Git', 'Webbooks Git', 'manage_options', 'webbooks-git-updates', [self::class, 'settingsPage'], 'dashicons-update');
        add_submenu_page('webbooks-git-updates', __('Налаштування', 'webbooks-git-updates'), __('Налаштування', 'webbooks-git-updates'), 'manage_options', 'webbooks-git-updates', [self::class, 'settingsPage']);
        add_submenu_page('webbooks-git-updates', __('Статистика', 'webbooks-git-updates'), __('Статистика', 'webbooks-git-updates'), 'manage_options', 'webbooks-git-statistics', [self::class, 'statisticsPage']);
    }

    public static function manualCheck(): void
    {
        self::authorize();
        check_admin_referer('webbooks_git_check');
        if ((int) (self::state()['last_checked'] ?? 0) < time() - 60) {
            self::check();
        }
        wp_safe_redirect(admin_url('admin.php?page=webbooks-git-statistics'));
        exit;
    }

    private static function authorize(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Недостатньо прав.', 'webbooks-git-updates'));
        }
    }

    public static function notice(): void
    {
        $remote = self::available();
        if (!current_user_can('update_themes') || !$remote) {
            return;
        }
        echo self::render('notice', [
            'version' => $remote['version'],
            'install_url' => $remote['package']
                ? wp_nonce_url(admin_url('update.php?action=upgrade-theme&theme=webbooks'), 'upgrade-theme_webbooks')
                : '',
        ]);
    }

    public static function settingsPage(): void
    {
        self::authorize();
        echo self::render('settings', [
            'config' => self::config(),
            'option' => self::OPTION,
        ]);
    }

    public static function statisticsPage(): void
    {
        self::authorize();
        $state = self::state();
        $rows = [
            __('Перевірка увімкнена', 'webbooks-git-updates') => self::config()['enabled'] ? __('Так', 'webbooks-git-updates') : __('Ні', 'webbooks-git-updates'),
            __('Останній результат', 'webbooks-git-updates') => $state['events'][0]['message'] ?? '—',
            __('Встановлена версія', 'webbooks-git-updates') => wp_get_theme(self::THEME)->get('Version'),
            __('Версія в Git', 'webbooks-git-updates') => $state['remote']['version'] ?? '—',
            __('Commit', 'webbooks-git-updates') => $state['remote']['sha'] ?? '—',
            __('Перевірок', 'webbooks-git-updates') => $state['checks'] ?? 0,
            __('Успішних перевірок', 'webbooks-git-updates') => $state['successful_checks'] ?? 0,
            __('Помилок', 'webbooks-git-updates') => $state['errors'] ?? 0,
            __('Завершених встановлень', 'webbooks-git-updates') => $state['installations'] ?? 0,
            __('Остання перевірка', 'webbooks-git-updates') => self::date((int) ($state['last_checked'] ?? 0)),
            __('Наступна перевірка', 'webbooks-git-updates') => self::date((int) wp_next_scheduled(self::CRON)),
        ];
        $events = array_map(
            static fn (array $event): array => [
                'date' => self::date((int) $event['time']),
                'message' => $event['message'],
            ],
            (array) ($state['events'] ?? [])
        );
        echo self::render('statistics', ['rows' => $rows, 'events' => $events]);
    }

    /** Templates escape dynamic values at the point of output. */
    private static function render(string $template, array $data): string
    {
        ob_start();
        try {
            require dirname(__DIR__) . '/templates/' . $template . '.php';
            return (string) ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }

    private static function date(int $timestamp): string
    {
        return $timestamp ? wp_date('Y-m-d H:i:s', $timestamp) : '—';
    }
}
