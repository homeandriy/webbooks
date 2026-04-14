<?php

use Webbooks\Book\DownloadLinks;

add_action('wp_ajax_return_link_to_book', [DownloadLinks::class, 'returnLinkToBook']);
add_action('wp_ajax_nopriv_return_link_to_book', [DownloadLinks::class, 'returnLinkToBook']);

add_filter('get_download_link', 'get_download_link', 10, 2);
function get_download_link(WP_Post $post, int $category_id = 0): string
{
    $link_to_download = get_post_meta($post->ID, 'download', true);
    if (!empty($link_to_download)) {
        $link_to_download_key_path = parse_url($link_to_download, PHP_URL_PATH);
    } else {
        $link_to_download_key_path = $post->post_name;
    }

    return sprintf(
        '<a href="%s?key=%s&count=%d&cat=%d" class="%s" target="_blank">%s</a>',
        home_url('/download'),
        $link_to_download_key_path,
        $post->ID,
        $category_id,
        'btn btn-primary btn-sm',
        esc_html_x('Download', 'button', 'webbooks')
    );
}
