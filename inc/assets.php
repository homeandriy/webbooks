<?php

add_action('wp_footer', 'add_scripts');
function add_scripts(): void {
    if (is_admin()) {
        return;
    }

    if (!is_page(846)) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('bootstrapjs_4', get_template_directory_uri() . '/assets/js/jquery.mCustomScrollbar.concat.min.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('jquery_lazy_load', get_stylesheet_directory_uri() . '/assets/js/jquery.lazyload.min.js', ['jquery'], WEBBOOKS_VERSION, true);
        wp_enqueue_script('bootstrapjs', get_template_directory_uri() . '/assets/js/bootstrap.min.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('bootstrapjs_2', get_template_directory_uri() . '/assets/js/jquery.fs.roller.min.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('countdown360', get_template_directory_uri() . '/assets/js/jquery.elevatezoom.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('bootstrapjs_3', get_template_directory_uri() . '/assets/js/custom.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('bootstrapjs_1', get_template_directory_uri() . '/assets/js/theme.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('fancybox', get_template_directory_uri() . '/assets/js/jquery.fancybox.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('slick', get_template_directory_uri() . '/assets/js/slick.js', '', WEBBOOKS_VERSION, true);
        wp_enqueue_script('load-filter');
        wp_enqueue_script('ajax-filter');
        return;
    }

    wp_enqueue_script('bootstrapjs-14', get_template_directory_uri() . '/portfolio/assets/js/ie/respond.min.js', '', WEBBOOKS_VERSION, true);
    wp_enqueue_script('bootstrapjs-13', get_template_directory_uri() . '/portfolio/assets/js/util.js', '', WEBBOOKS_VERSION, true);
    wp_enqueue_script('bootstrapjs-12', get_template_directory_uri() . '/portfolio/assets/js/skel.min.js', '', WEBBOOKS_VERSION, true);
    wp_enqueue_script('bootstrapjs-11', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrollzer.min.js', '', WEBBOOKS_VERSION, true);
    wp_enqueue_script('bootstrapjs-10', get_template_directory_uri() . '/portfolio/assets/js/jquery.scrolly.min.js', '', WEBBOOKS_VERSION, true);
    wp_enqueue_script('bootstrapjs-15', get_template_directory_uri() . '/portfolio/assets/js/main.js', '', WEBBOOKS_VERSION, true);
}

add_action('wp_print_styles', 'add_styles');
function add_styles(): void {
    if (is_admin()) {
        return;
    }

    if (!is_page(846)) {
        wp_enqueue_style('sparkling-bootstrap1', get_template_directory_uri() . '/style.css');
        wp_enqueue_style('sparkling-bootstrap2', get_template_directory_uri() . '/assets/css/material-design-icons.min.css');
        wp_enqueue_style('sparkling-bootstrap3', get_template_directory_uri() . '/assets/css/jquery.fs.roller.min.css');
        wp_enqueue_style('sparkling-bootstrap4', get_template_directory_uri() . '/assets/css/jquery.mCustomScrollbar.min.css');
        wp_enqueue_style('sparkling-bootstrap5', get_template_directory_uri() . '/assets/css/font-awesome.min.css');
        wp_enqueue_style('fancybox', get_template_directory_uri() . '/assets/css/jquery.fancybox.css');
        wp_enqueue_style('slick-theme', get_template_directory_uri() . '/assets/css/slick-theme.css');
        wp_enqueue_style('slick', get_template_directory_uri() . '/assets/css/slick.css');
        return;
    }

    wp_enqueue_style('sparkling-bootstrap25252', get_template_directory_uri() . '/portfolio/assets/css/main.css');
}

add_action('wp_enqueue_scripts', 'theme_register_scripts', 1);
function theme_register_scripts(): void {
    wp_register_script('functions-js', esc_url(trailingslashit(get_template_directory_uri()) . '/assets/js/functions.js'), ['jquery'], WEBBOOKS_VERSION, true);
    wp_localize_script('functions-js', 'php_array', ['admin_ajax' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce(GENERAL_NONCE)]);
}

add_action('wp_enqueue_scripts', 'additional_theme_scripts', 1);
function additional_theme_scripts(): void {
    wp_register_script('ajax-filter', esc_url(trailingslashit(get_template_directory_uri()) . '/assets/js/ajax-filter.js'), ['jquery'], WEBBOOKS_VERSION, true);
    wp_localize_script('ajax-filter', 'js_attributes', [
        'admin_ajax' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce(GENERAL_NONCE),
        'download_nonce' => wp_create_nonce(DOWNLOAD_BOOK_NONCE),
        'home_url' => home_url(),
    ]);
}

add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
function theme_enqueue_scripts(): void {
    wp_enqueue_script('functions-js');
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
