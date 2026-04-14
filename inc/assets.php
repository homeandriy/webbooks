<?php

add_action('wp_enqueue_scripts', 'theme_register_scripts', 1);
function theme_register_scripts(): void {
    wp_register_script(
        'functions-js',
        esc_url(trailingslashit(get_template_directory_uri()) . 'assets/js/functions.js'),
        ['jquery'],
        webbooks_file_version('assets/js/functions.js'),
        true
    );

    wp_localize_script('functions-js', 'php_array', [
        'admin_ajax' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce(GENERAL_NONCE),
    ]);
}

add_action('wp_enqueue_scripts', 'additional_theme_scripts', 1);
function additional_theme_scripts(): void {
    wp_register_script(
        'ajax-filter',
        esc_url(trailingslashit(get_template_directory_uri()) . 'assets/js/ajax-filter.js'),
        ['jquery'],
        webbooks_file_version('assets/js/ajax-filter.js'),
        true
    );

    wp_localize_script('ajax-filter', 'js_attributes', [
        'admin_ajax' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce(GENERAL_NONCE),
        'download_nonce' => wp_create_nonce(DOWNLOAD_BOOK_NONCE),
        'home_url' => home_url(),
    ]);
}

add_action('wp_enqueue_scripts', 'webbooks_enqueue_assets', 10);
function webbooks_enqueue_assets(): void {
    if (is_admin()) {
        return;
    }

    if (is_page(846)) {
        wp_enqueue_style(
            'webbooks-portfolio',
            get_template_directory_uri() . '/portfolio/assets/css/main.css',
            [],
            webbooks_file_version('portfolio/assets/css/main.css')
        );

        wp_enqueue_script('jquery');
        wp_enqueue_script('webbooks-portfolio-respond', get_template_directory_uri() . '/portfolio/assets/js/ie/respond.min.js', [], webbooks_file_version('portfolio/assets/js/ie/respond.min.js'), true);
        wp_enqueue_script('webbooks-portfolio-skel', get_template_directory_uri() . '/portfolio/assets/js/skel.min.js', ['jquery'], webbooks_file_version('portfolio/assets/js/skel.min.js'), true);
        wp_enqueue_script('webbooks-portfolio-util', get_template_directory_uri() . '/portfolio/assets/js/util.js', ['jquery', 'webbooks-portfolio-skel'], webbooks_file_version('portfolio/assets/js/util.js'), true);
        wp_enqueue_script('webbooks-portfolio-scrollzer', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrollzer.min.js', ['jquery'], webbooks_file_version('portfolio/assets/js/jquery.scrollzer.min.js'), true);
        wp_enqueue_script('webbooks-portfolio-scrolly', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrolly.min.js', ['jquery'], webbooks_file_version('portfolio/assets/js/jquery.scrolly.min.js'), true);
        wp_enqueue_script('webbooks-portfolio-main', get_template_directory_uri() . '/portfolio/assets/js/main.js', ['jquery', 'webbooks-portfolio-util', 'webbooks-portfolio-scrollzer', 'webbooks-portfolio-scrolly'], webbooks_file_version('portfolio/assets/js/main.js'), true);

        return;
    }

    wp_enqueue_script('functions-js');

    $manifest = webbooks_get_vite_manifest();
    $mainBundle = $manifest['src/main.js'] ?? null;

    if ($mainBundle) {
        if (!empty($mainBundle['css']) && is_array($mainBundle['css'])) {
            foreach ($mainBundle['css'] as $index => $cssFile) {
                wp_enqueue_style(
                    'webbooks-bundle-' . $index,
                    get_template_directory_uri() . '/dist/' . ltrim($cssFile, '/'),
                    [],
                    webbooks_file_version('dist/' . ltrim($cssFile, '/'))
                );
            }
        }

        wp_enqueue_script(
            'webbooks-bundle',
            get_template_directory_uri() . '/dist/' . ltrim($mainBundle['file'], '/'),
            ['jquery', 'functions-js', 'ajax-filter'],
            webbooks_file_version('dist/' . ltrim($mainBundle['file'], '/')),
            true
        );

        wp_script_add_data('webbooks-bundle', 'type', 'module');
    } else {
        // Fallback for environments without build step.
        wp_enqueue_style('webbooks-style', get_template_directory_uri() . '/style.css', [], webbooks_file_version('style.css'));
        wp_enqueue_script('ajax-filter');
    }

    if ($mainBundle && apply_filters('webbooks_enable_lazyload_init', true)) {
        wp_enqueue_script(
            'webbooks-lazyload-init',
            get_template_directory_uri() . '/assets/js/lazyload-init.js',
            ['jquery', 'webbooks-bundle'],
            webbooks_file_version('assets/js/lazyload-init.js'),
            true
        );
    }
}

add_action('admin_notices', 'webbooks_vite_manifest_admin_notice');
function webbooks_vite_manifest_admin_notice(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (!webbooks_is_vite_manifest_missing()) {
        return;
    }

    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('Webbooks: Vite manifest is missing (dist/.vite/manifest.json). The theme is running in fallback mode.', 'webbooks');
    echo '</p></div>';
}

add_action('wp_enqueue_scripts', 'webbooks_enqueue_external_services', 20);
function webbooks_enqueue_external_services(): void {
    if (is_admin() || is_page(846)) {
        return;
    }

    if (apply_filters('webbooks_enable_recaptcha', true)) {
        wp_enqueue_script('google-recaptcha-api', 'https://www.google.com/recaptcha/api.js', [], null, true);
    }

    if (apply_filters('webbooks_enable_google_ads', !is_user_logged_in())) {
        wp_enqueue_script('google-adsbygoogle', 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', [], null, false);
        wp_script_add_data('google-adsbygoogle', 'async', true);
        wp_add_inline_script('google-adsbygoogle', '(adsbygoogle=window.adsbygoogle||[]).push({google_ad_client:"ca-pub-1952021322373690",enable_page_level_ads:true});', 'after');
    }

    if (apply_filters('webbooks_enable_google_analytics', !is_user_logged_in())) {
        wp_enqueue_script('google-analytics', 'https://www.google-analytics.com/analytics.js', [], null, true);
        wp_script_add_data('google-analytics', 'async', true);
        wp_add_inline_script('google-analytics', "window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments);};ga.l=1*new Date();ga('create','UA-57400123-2','auto');ga('send','pageview');", 'before');
    }
}

function webbooks_get_vite_manifest(): array {
    static $manifest = null;

    if ($manifest !== null) {
        return $manifest;
    }

    $manifestPath = get_template_directory() . '/dist/.vite/manifest.json';

    if (!file_exists($manifestPath)) {
        $manifest = [];
        return $manifest;
    }

    $decoded = json_decode((string) file_get_contents($manifestPath), true);
    $manifest = is_array($decoded) ? $decoded : [];

    return $manifest;
}

function webbooks_is_vite_manifest_missing(): bool {
    $manifestPath = get_template_directory() . '/dist/.vite/manifest.json';

    return !file_exists($manifestPath);
}

function webbooks_file_version(string $relativePath): string {
    $filePath = get_template_directory() . '/' . ltrim($relativePath, '/');

    return file_exists($filePath)
        ? (string) filemtime($filePath)
        : WEBBOOKS_VERSION;
}
