<?php

use Webbooks\Security\DisableApiUsers;

new DisableApiUsers();

add_action( 'after_setup_theme', 'webbooks_setup_theme_i18n' );
function webbooks_setup_theme_i18n(): void {
	$locales = webbooks_get_i18n_locale_candidates();
	$paths   = webbooks_get_i18n_mofile_candidates( $locales );

	foreach ( $paths as $mofile ) {
		if ( ! file_exists( $mofile ) ) {
			continue;
		}

		load_textdomain( 'webbooks', $mofile );
		return;
	}
}

/**
 * Build locale candidates for translation lookup.
 *
 * Polylang can return short locales (e.g. "uk"), while theme files
 * are stored using full locale format (e.g. "webbooks-uk_UA.mo").
 *
 * @return string[]
 */
function webbooks_get_i18n_locale_candidates(): array {
	$candidates = array();

	$locales = array_filter(
		array_unique(
			array(
				determine_locale(),
				get_locale(),
			)
		)
	);

	foreach ( $locales as $locale ) {
		$normalized = str_replace( '-', '_', (string) $locale );

		$candidates[] = $normalized;

		if ( ! str_contains( $normalized, '_' ) ) {
			$candidates[] = webbooks_map_short_locale( $normalized );
		}
	}

	return array_values( array_unique( array_filter( $candidates ) ) );
}

/**
 * Map short locale codes to full WordPress locales used in this theme.
 */
function webbooks_map_short_locale( string $locale ): string {
	$map = array(
		'en' => 'en_US',
		'pl' => 'pl_PL',
		'ru' => 'ru_RU',
		'uk' => 'uk_UA',
	);

	return $map[ $locale ] ?? $locale;
}

/**
 * Get possible translation file paths for a locale list.
 *
 * @param string[] $locales
 * @return string[]
 */
function webbooks_get_i18n_mofile_candidates( array $locales ): array {
	$paths = array();

	foreach ( $locales as $locale ) {
		$paths[] = WEBBOOKS_PATH . '/languages/webbooks-' . $locale . '.l10n.php';
		$paths[] = WEBBOOKS_PATH . '/languages/' . $locale . '.l10n.php';
		$paths[] = WEBBOOKS_PATH . '/languages/webbooks-' . $locale . '.mo';
		$paths[] = WEBBOOKS_PATH . '/languages/' . $locale . '.mo';
	}

	return array_values( array_unique( $paths ) );
}

register_nav_menus(
	array(
		'top'    => 'Верхнее',
		'bottom' => 'Внизу',
	)
);

add_theme_support( 'post-thumbnails' );
add_theme_support( 'title-tag' );
set_post_thumbnail_size( 250, 150 );
add_image_size( 'big-thumb', 390, 440, false );
add_image_size( 'big-thumb-main', 390, 440, false );
add_image_size( 'small-thumb', 100, 100, true );

register_sidebar(
	array(
		'name'          => 'Колонка слева',
		'id'            => 'left-sidebar',
		'description'   => 'Обычная колонка в сайдбаре',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => "</div>\n",
		'before_title'  => '<span class="widgettitle">',
		'after_title'   => "</span>\n",
	)
);

function pagination(): void {
	global $wp_query;
	$big = 999999999;
	echo paginate_links(
		array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'type'      => 'list',
			'prev_text' => 'Назад',
			'next_text' => 'Вперед',
			'total'     => $wp_query->max_num_pages,
			'show_all'  => false,
			'end_size'  => 15,
			'mid_size'  => 15,
		)
	);
}
