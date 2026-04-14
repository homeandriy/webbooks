<?php

use Webbooks\Security\DisableApiUsers;

new DisableApiUsers();

add_action('after_setup_theme', 'webbooks_setup_theme_i18n');
function webbooks_setup_theme_i18n(): void
{
    load_theme_textdomain('webbooks', WEBBOOKS_PATH . '/languages');
}

register_nav_menus([
    'top' => 'Верхнее',
    'bottom' => 'Внизу',
]);

add_theme_support('post-thumbnails');
add_theme_support('title-tag');
set_post_thumbnail_size(250, 150);
add_image_size('big-thumb', 390, 440, false);
add_image_size('big-thumb-main', 390, 440, false);
add_image_size('small-thumb', 100, 100, true);

register_sidebar([
    'name' => 'Колонка слева',
    'id' => 'left-sidebar',
    'description' => 'Обычная колонка в сайдбаре',
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget' => "</div>\n",
    'before_title' => '<span class="widgettitle">',
    'after_title' => "</span>\n",
]);

function pagination(): void {
    global $wp_query;
    $big = 999999999;
    echo paginate_links([
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'type' => 'list',
        'prev_text' => 'Назад',
        'next_text' => 'Вперед',
        'total' => $wp_query->max_num_pages,
        'show_all' => false,
        'end_size' => 15,
        'mid_size' => 15,
    ]);
}
