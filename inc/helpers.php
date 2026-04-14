<?php

function get_short_description(string $content, int $words_count): string
{
    return wp_trim_words($content, $words_count, '...');
}

add_filter('post_gallery', 'get_image_gallery', 10, 1);
function get_image_gallery(WP_Post|string $post): ?string
{
    if (is_string($post)) {
        return null;
    }

    ob_start();
    include_once WEBBOOKS_PATH . '/template/image-gallery.php';
    return ob_get_clean();
}

function get_banner_src(): string
{
    $links = [
        'https://gmhost.ua/wp-content/uploads/2023/02/baner_8.jpg',
        'https://gmhost.ua/wp-content/uploads/2023/02/baner_6.jpg',
        'https://gmhost.ua/wp-content/uploads/2023/02/baner_5.jpg',
        'https://gmhost.ua/wp-content/uploads/2023/02/baner_4.jpg',
        'https://gmhost.ua/wp-content/uploads/2023/02/baner_2.jpg',
        'https://gmhost.ua/wp-content/uploads/2023/02/baner_1.jpg',
    ];

    return $links[array_rand($links, 1)];
}
