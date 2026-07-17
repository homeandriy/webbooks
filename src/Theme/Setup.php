<?php
/**
 * Theme setup, translations, menus, sidebars, and pagination.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Theme;

use Webbooks\Security\DisableApiUsers;

/**
 * Registers theme supports, translations, menus, sidebars, and pagination.
 */
final class Setup {

	/**
	 * Register theme setup hooks.
	 */
	public static function register(): void {
		new DisableApiUsers();
		add_action( 'after_setup_theme', array( self::class, 'setupI18n' ) );
		add_action( 'after_setup_theme', array( self::class, 'registerThemeFeatures' ) );
	}

	/**
	 * Load the first available theme translation file.
	 */
	public static function setupI18n(): void {
		$locales = self::getI18nLocaleCandidates();
		$paths   = self::getI18nMofileCandidates( $locales );

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
	private static function getI18nLocaleCandidates(): array {
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
				$candidates[] = self::mapShortLocale( $normalized );
			}
		}

		return array_values( array_unique( array_filter( $candidates ) ) );
	}

	/**
	 * Map short locale codes to full WordPress locales used in this theme.
	 *
	 * @param string $locale Short locale code.
	 * @return string Full WordPress locale.
	 */
	private static function mapShortLocale( string $locale ): string {
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
	 * @param string[] $locales Locale candidates.
	 * @return string[]
	 */
	private static function getI18nMofileCandidates( array $locales ): array {
		$paths = array();

		foreach ( $locales as $locale ) {
			$paths[] = WEBBOOKS_PATH . '/languages/webbooks-' . $locale . '.l10n.php';
			$paths[] = WEBBOOKS_PATH . '/languages/' . $locale . '.l10n.php';
			$paths[] = WEBBOOKS_PATH . '/languages/webbooks-' . $locale . '.mo';
			$paths[] = WEBBOOKS_PATH . '/languages/' . $locale . '.mo';
		}

		return array_values( array_unique( $paths ) );
	}

	/**
	 * Register WordPress theme features.
	 */
	public static function registerThemeFeatures(): void {
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
	}

	/**
	 * Output pagination links for the main query.
	 */
	public static function pagination(): void {
		global $wp_query;
		$big = '999999999';
		echo wp_kses_post(
			paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format'    => '?paged=%#%',
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'type'      => 'list',
					'prev_text' => esc_html__( 'Previous', 'webbooks' ),
					'next_text' => esc_html__( 'Next', 'webbooks' ),
					'total'     => $wp_query->max_num_pages,
					'show_all'  => false,
					'end_size'  => 15,
					'mid_size'  => 15,
				)
			)
		);
	}
}
