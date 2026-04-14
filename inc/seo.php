<?php

function webbooks_is_seo_plugin_active(): bool
{
    return defined('WPSEO_VERSION')
        || class_exists('WPSEO_Frontend')
        || defined('RANK_MATH_VERSION')
        || class_exists('RankMath')
        || defined('AIOSEO_VERSION')
        || class_exists('\\AIOSEO\\Plugin\\Common\\Main')
        || defined('SEOPRESS_VERSION')
        || class_exists('\\The_SEO_Framework\\Load');
}

function webbooks_is_download_template_page(): bool
{
    if (!is_page()) {
        return false;
    }

    if (is_page_template('download.php')) {
        return true;
    }

    return is_page('download');
}

add_action('wp_head', 'webbooks_add_hreflang_links', 1);
function webbooks_add_hreflang_links(): void
{
    if (webbooks_is_seo_plugin_active() || !function_exists('pll_the_languages')) {
        return;
    }

    $languages = pll_the_languages(['raw' => 1, 'hide_if_empty' => 0, 'hide_if_no_translation' => 0]);
    if (empty($languages) || !is_array($languages)) {
        return;
    }

    foreach ($languages as $language) {
        if (empty($language['url']) || empty($language['slug'])) {
            continue;
        }

        printf('<link rel="alternate" hreflang="%1$s" href="%2$s" />' . PHP_EOL, esc_attr($language['slug']), esc_url($language['url']));
    }
}

add_action('wp_head', 'webbooks_add_social_meta_fallback', 5);
function webbooks_add_social_meta_fallback(): void
{
    if (webbooks_is_seo_plugin_active()) {
        return;
    }

    global $post;
    $title = wp_get_document_title();
    $description = get_bloginfo('description');
    $image = '';
    $url = home_url('/');

    if (is_singular() && $post instanceof WP_Post) {
        $title = get_the_title($post);
        $description = has_excerpt($post) ? $post->post_excerpt : wp_trim_words(wp_strip_all_tags($post->post_content), 30, '...');
        $url = get_permalink($post);
        if (has_post_thumbnail($post)) {
            $image = get_the_post_thumbnail_url($post, 'full');
        }
    }

    if (empty($image)) {
        $image = get_template_directory_uri() . '/screenshot.png';
    }

    printf('<meta property="og:type" content="%s" />' . "\n", esc_attr(is_singular() ? 'article' : 'website'));
    printf('<meta property="og:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta property="og:description" content="%s" />' . "\n", esc_attr(wp_strip_all_tags($description)));
    printf('<meta property="og:url" content="%s" />' . "\n", esc_url($url));
    printf('<meta property="og:site_name" content="%s" />' . "\n", esc_attr(get_bloginfo('name')));
    printf('<meta property="og:image" content="%s" />' . "\n", esc_url($image));
    printf('<meta name="twitter:card" content="%s" />' . "\n", esc_attr($image ? 'summary_large_image' : 'summary'));
    printf('<meta name="twitter:title" content="%s" />' . "\n", esc_attr($title));
    printf('<meta name="twitter:description" content="%s" />' . "\n", esc_attr(wp_strip_all_tags($description)));
    printf('<meta name="twitter:image" content="%s" />' . "\n", esc_url($image));
}

add_action('wp_head', 'webbooks_add_archive_meta_description', 6);
function webbooks_add_archive_meta_description(): void
{
    if (webbooks_is_seo_plugin_active() || !is_archive()) {
        return;
    }

    $term = null;
    if (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
    }

    if (!$term instanceof WP_Term) {
        return;
    }

    $description = trim(wp_strip_all_tags((string) term_description($term, $term->taxonomy)));
    if ($description === '') {
        $description = sprintf(
            /* translators: %s: Term name. */
            __('Collection of materials in the "%s" section.', 'webbooks'),
            $term->name
        );
    }

    $template = __(
        '%1$s Read more books and collections in the "%2$s" category on %3$s.',
        'webbooks'
    );

    $meta_description = sprintf(
        $template,
        $description,
        $term->name,
        get_bloginfo('name')
    );

    $paged = (int) get_query_var('paged');
    if ($paged > 1) {
        $meta_description .= ' ' . sprintf(
            /* translators: %d: Current archive page number. */
            __('Page %d.', 'webbooks'),
            $paged
        );
    }

    printf(
        '<meta name="description" content="%s" />' . "\n",
        esc_attr(wp_trim_words($meta_description, 35, '...'))
    );
}

add_action('wp_head', 'webbooks_add_download_noindex_meta', 2);
function webbooks_add_download_noindex_meta(): void
{
    if (!webbooks_is_download_template_page()) {
        return;
    }

    echo '<meta name="robots" content="noindex,nofollow,noarchive" />' . "\n";
}

add_action('template_redirect', 'webbooks_add_download_robots_header', 1);
function webbooks_add_download_robots_header(): void
{
    if (!webbooks_is_download_template_page() || headers_sent()) {
        return;
    }

    header('X-Robots-Tag: noindex, nofollow', true);
}
