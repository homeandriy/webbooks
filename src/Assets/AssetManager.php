<?php
/**
 * Theme asset registration, Vite bundle loading, and optimizer integration.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Assets;

/**
 * Registers theme assets and integration exclusions for optimization plugins.
 */
final class AssetManager {
	/** Register theme asset hooks. */
	public static function register(): void {
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueueAssets' ), 10 );
		add_filter( 'script_loader_tag', array( self::class, 'preserveBundleModuleType' ), 20, 2 );
		add_filter( 'autoptimize_filter_js_exclude', array( self::class, 'excludeBundleFromAutoptimize' ) );
		add_filter( 'autoptimize_filter_css_exclude', array( self::class, 'excludeBundleCssFromAutoptimize' ) );
		add_filter( 'rocket_exclude_js', array( self::class, 'excludeBundleFromWpRocket' ) );
		add_filter( 'rocket_defer_js_exclusions', array( self::class, 'excludeBundleFromWpRocket' ) );
		add_filter( 'rocket_minify_excluded_external_js', array( self::class, 'excludeBundleFromWpRocket' ) );
		add_filter( 'rocket_delay_js_exclusions', array( self::class, 'excludeBundleFromWpRocket' ) );
		add_filter( 'rocket_exclude_css', array( self::class, 'excludeBundleCssFromWpRocket' ) );
		add_filter( 'rocket_rucss_excluded_selectors', array( self::class, 'excludeBundleCssFromWpRocket' ) );
		add_filter( 'litespeed_optimize_js_excludes', array( self::class, 'excludeBundleFromLitespeed' ) );
		add_filter( 'litespeed_optm_js_defer_exc', array( self::class, 'excludeBundleFromLitespeed' ) );
		add_filter( 'litespeed_optm_js_delay_exc', array( self::class, 'excludeBundleFromLitespeed' ) );
		add_filter( 'litespeed_optm_css_exc', array( self::class, 'excludeBundleCssFromLitespeed' ) );
		add_filter( 'litespeed_optm_ucss_exc', array( self::class, 'excludeBundleCssFromLitespeed' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueueFontAssets' ), 11 );
		add_filter( 'wp_resource_hints', array( self::class, 'fontResourceHints' ), 10, 2 );
		add_action( 'admin_notices', array( self::class, 'viteManifestAdminNotice' ) );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueueExternalServices' ), 20 );
	}

	/**
	 * Add configuration data before the Vite module is evaluated.
	 */
	private static function localizeBundle(): void {
		wp_localize_script(
			'webbooks-bundle',
			'webbooksConfig',
			array(
				'admin_ajax'     => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( WEBBOOKS_AJAX_NONCE ),
				'download_nonce' => wp_create_nonce( WEBBOOKS_DOWNLOAD_NONCE ),
				'home_url'       => home_url(),
				'i18n'           => array(
					'preview_loading'           => __( 'Loading…', 'webbooks' ),
					'invalid_download_link'     => __( 'Invalid download link.', 'webbooks' ),
					'back_to_homepage'          => __( 'Back to homepage', 'webbooks' ),
					'try_again'                 => __( 'Try again', 'webbooks' ),
					'preparing_download_link'   => __( 'Preparing download link…', 'webbooks' ),
					/* translators: %d: Number of seconds remaining before the download link is available. */
					'seconds_remaining'         => __( 'Seconds remaining: %d', 'webbooks' ),
					'checking_download_link'    => __( 'Checking download link…', 'webbooks' ),
					'seconds_zero'              => __( 'Seconds: 0', 'webbooks' ),
					'download_link_unavailable' => __( 'Unable to retrieve the download link.', 'webbooks' ),
					'network_error'             => __( 'A network error occurred. Please try again.', 'webbooks' ),
					'nonce_expired'             => __(
						'Your security token has expired. Refresh the page and try again.',
						'webbooks'
					),
					'network_or_server_error'   => __(
						'There is a network or server problem. Click "Try again".',
						'webbooks'
					),
					'download_link_ready'       => __( 'Download link is ready.', 'webbooks' ),
				),
			)
		);
	}


	/**
	 * Enqueue either the Vite build or the safe fallback assets.
	 */
	public static function enqueueAssets(): void {
		if ( is_admin() ) {
			return;
		}

		if ( is_page( WEBBOOKS_PORTFOLIO_PAGE_ID ) ) {
			wp_enqueue_style(
				'webbooks-portfolio',
				get_template_directory_uri() . '/portfolio/assets/css/main.css',
				array(),
				self::fileVersion( 'portfolio/assets/css/main.css' )
			);

			wp_enqueue_script(
				'webbooks-portfolio',
				get_template_directory_uri() . '/portfolio/assets/js/portfolio.js',
				array(),
				self::fileVersion( 'portfolio/assets/js/portfolio.js' ),
				true
			);

			return;
		}

		$manifest    = self::getViteManifest();
		$main_bundle = $manifest['src/main.js'] ?? null;

		if ( $main_bundle ) {
			if ( ! empty( $main_bundle['css'] ) && is_array( $main_bundle['css'] ) ) {
				foreach ( $main_bundle['css'] as $index => $css_file ) {
					wp_enqueue_style(
						'webbooks-bundle-' . $index,
						get_template_directory_uri() . '/dist/' . ltrim( $css_file, '/' ),
						array(),
						self::fileVersion( 'dist/' . ltrim( $css_file, '/' ) )
					);
				}
			}

			wp_enqueue_script(
				'webbooks-bundle',
				get_template_directory_uri() . '/dist/' . ltrim( $main_bundle['file'], '/' ),
				array( 'jquery' ),
				self::fileVersion( 'dist/' . ltrim( $main_bundle['file'], '/' ) ),
				true
			);

			self::localizeBundle();
			wp_script_add_data( 'webbooks-bundle', 'type', 'module' );
		} else {
			// Fallback for environments without build step.
			wp_enqueue_style(
				'webbooks-style',
				get_template_directory_uri() . '/style.css',
				array(),
				self::fileVersion( 'style.css' )
			);
		}
	}


	/**
	 * Preserve the module script type when optimizers rewrite markup.
	 *
	 * @param  string $tag  Script HTML tag.
	 * @param  string $handle  Registered script handle.
	 *
	 * @return string Updated script HTML tag.
	 */
	public static function preserveBundleModuleType( string $tag, string $handle ): string {
		if ( 'webbooks-bundle' !== $handle ) {
			return $tag;
		}

		// Force module type even if a 3rd-party optimizer/CDN rewrites it.
		if ( preg_match( '/\stype=("|\')(.*?)\1/i', $tag ) === 1 ) {
			$tag = preg_replace( '/\stype=("|\')(.*?)\1/i', ' type="module"', $tag, 1 ) ?? $tag;
		} elseif ( strpos( $tag, 'type=' ) === false ) {
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}

		// Prevent Cloudflare Rocket Loader from rewriting this module script.
		if ( strpos( $tag, 'data-cfasync=' ) === false ) {
			$tag = str_replace( '<script ', '<script data-cfasync="false" ', $tag );
		}

		return $tag;
	}


	/**
	 * Exclude the Vite JavaScript bundle from Autoptimize transformations.
	 *
	 * @param  string $excluded  Existing exclusion list.
	 *
	 * @return string Updated exclusion list.
	 */
	public static function excludeBundleFromAutoptimize( string $excluded ): string {
		$excluded = self::appendExclusionItem( $excluded, 'webbooks-bundle' );
		$excluded = self::appendExclusionItem( $excluded, 'webbooks-bundle-js' );

		return self::appendExclusionItem( $excluded, '/dist/' );
	}


	/**
	 * Exclude Vite styles from Autoptimize transformations.
	 *
	 * @param  string $excluded  Existing exclusion list.
	 *
	 * @return string Updated exclusion list.
	 */
	public static function excludeBundleCssFromAutoptimize( string $excluded ): string {
		$excluded = self::appendExclusionItem( $excluded, 'webbooks-bundle-' );

		return self::appendExclusionItem( $excluded, '/dist/assets/' );
	}


	/**
	 * Exclude the Vite JavaScript bundle from WP Rocket transformations.
	 *
	 * @param  array<int, string> $excluded  Excluded script patterns.
	 *
	 * @return array<int, string> Updated excluded script patterns.
	 */
	public static function excludeBundleFromWpRocket( array $excluded ): array {
		$excluded[] = 'webbooks-bundle';
		$excluded[] = 'webbooks-bundle-js';
		$excluded[] = '/dist/';

		return array_values( array_unique( $excluded ) );
	}


	/**
	 * Exclude Vite styles from WP Rocket transformations.
	 *
	 * @param  array<int, string> $excluded  Excluded stylesheet patterns.
	 *
	 * @return array<int, string> Updated excluded stylesheet patterns.
	 */
	public static function excludeBundleCssFromWpRocket( array $excluded ): array {
		$excluded[] = 'webbooks-bundle-';
		$excluded[] = '/dist/assets/';

		return array_values( array_unique( $excluded ) );
	}


	/**
	 * Exclude the Vite JavaScript bundle from LiteSpeed transformations.
	 *
	 * @param  array<int, string>|string $excluded  Excluded script patterns.
	 *
	 * @return array<int, string>|string Updated excluded script patterns.
	 */
	public static function excludeBundleFromLitespeed( array|string $excluded ): array|string {
		if ( is_array( $excluded ) ) {
			$excluded[] = 'webbooks-bundle';
			$excluded[] = 'webbooks-bundle-js';
			$excluded[] = '/dist/';

			return array_values( array_unique( $excluded ) );
		}

		$excluded = self::appendExclusionItem( $excluded, 'webbooks-bundle' );
		$excluded = self::appendExclusionItem( $excluded, 'webbooks-bundle-js' );

		return self::appendExclusionItem( $excluded, '/dist/' );
	}


	/**
	 * Exclude Vite styles from LiteSpeed transformations.
	 *
	 * @param  array<int, string>|string $excluded  Excluded stylesheet patterns.
	 *
	 * @return array<int, string>|string Updated excluded stylesheet patterns.
	 */
	public static function excludeBundleCssFromLitespeed( array|string $excluded ): array|string {
		if ( is_array( $excluded ) ) {
			$excluded[] = 'webbooks-bundle-';
			$excluded[] = '/dist/assets/';

			return array_values( array_unique( $excluded ) );
		}

		$excluded = self::appendExclusionItem( $excluded, 'webbooks-bundle-' );

		return self::appendExclusionItem( $excluded, '/dist/assets/' );
	}

	/**
	 * Append an item to a comma-separated optimizer exclusion list.
	 *
	 * @param  string $excluded_list  Existing comma-separated exclusion list.
	 * @param  string $item  Item to add.
	 *
	 * @return string Updated exclusion list.
	 */
	private static function appendExclusionItem( string $excluded_list, string $item ): string {
		$items = array_filter( array_map( 'trim', explode( ',', $excluded_list ) ) );

		if ( ! in_array( $item, $items, true ) ) {
			$items[] = $item;
		}

		return implode( ',', $items );
	}


	/**
	 * Enqueue externally hosted theme fonts.
	 */
	public static function enqueueFontAssets(): void {
		if ( is_admin() || is_page( WEBBOOKS_PORTFOLIO_PAGE_ID ) ) {
			return;
		}

		wp_enqueue_style(
			'webbooks-font-questrial',
			'https://fonts.googleapis.com/css2?family=Questrial&display=swap',
			array(),
			WEBBOOKS_VERSION
		);
	}


	/**
	 * Add preconnect hints for the Google Fonts origins.
	 *
	 * @param  array<int, string|array<string, string>> $urls  Existing resource hints.
	 * @param  string                                   $relation_type  Requested hint relation.
	 *
	 * @return array<int, string|array<string, string>> Updated resource hints.
	 */
	public static function fontResourceHints( array $urls, string $relation_type ): array {
		if ( is_admin() || is_page( WEBBOOKS_PORTFOLIO_PAGE_ID ) ) {
			return $urls;
		}

		if ( 'preconnect' === $relation_type ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array(
				'href'        => 'https://fonts.gstatic.com',
				'crossorigin' => 'anonymous',
			);
		}

		return $urls;
	}


	/**
	 * Notify site administrators when the Vite manifest is unavailable.
	 */
	public static function viteManifestAdminNotice(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! self::isViteManifestMissing() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Notice template escapes dynamic values.
		echo webbooks_render_template_part(
			'template-parts/admin/notice',
			array(
				'type'    => 'warning',
				'message' => __(
					'Webbooks: Vite manifest is missing (dist/.vite/manifest.json). The theme is running in fallback mode.',
					'webbooks'
				),
			)
		);
	}


	/**
	 * Enqueue enabled third-party frontend services.
	 */
	public static function enqueueExternalServices(): void {
		if ( is_admin() || is_page( WEBBOOKS_PORTFOLIO_PAGE_ID ) ) {
			return;
		}

		if ( apply_filters( 'webbooks_enable_recaptcha', true ) ) {
			wp_enqueue_script(
				'google-recaptcha-api',
				'https://www.google.com/recaptcha/api.js',
				array(),
				WEBBOOKS_VERSION,
				true
			);
		}

		if ( apply_filters( 'webbooks_enable_google_ads', ! is_user_logged_in() ) ) {
			wp_enqueue_script(
				'google-adsbygoogle',
				'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js',
				array(),
				WEBBOOKS_VERSION,
				false
			);
			wp_script_add_data( 'google-adsbygoogle', 'async', true );
			wp_add_inline_script(
				'google-adsbygoogle',
				'(adsbygoogle=window.adsbygoogle||[]).push({google_ad_client:"ca-pub-1952021322373690",enable_page_level_ads:true});',
				'after'
			);
		}

		$ga4_measurement_id = self::getGa4MeasurementId();
		if ( '' !== $ga4_measurement_id && apply_filters(
			'webbooks_enable_google_analytics',
			! is_user_logged_in()
		) ) {
			wp_enqueue_script(
				'webbooks-ga4',
				'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $ga4_measurement_id ),
				array(),
				WEBBOOKS_VERSION,
				false
			);
			wp_script_add_data( 'webbooks-ga4', 'strategy', 'async' );
			wp_add_inline_script(
				'webbooks-ga4',
				'window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config",' . wp_json_encode( $ga4_measurement_id ) . ');',
				'before'
			);
		}
	}

	/**
	 * Get a validated GA4 measurement ID from configuration or a filter.
	 */
	private static function getGa4MeasurementId(): string {
		$measurement_id = defined( 'WEBBOOKS_GA4_MEASUREMENT_ID' ) ? (string) WEBBOOKS_GA4_MEASUREMENT_ID : '';
		$measurement_id = strtoupper(
			trim(
				(string) apply_filters(
					'webbooks_ga4_measurement_id',
					$measurement_id
				)
			)
		);

		return 1 === preg_match( '/^G-[A-Z0-9]+$/', $measurement_id ) ? $measurement_id : '';
	}

	/**
	 * Read and cache the Vite build manifest.
	 *
	 * @return array<string, mixed> Vite manifest entries.
	 */
	private static function getViteManifest(): array {
		static $manifest = null;

		if ( null !== $manifest ) {
			return $manifest;
		}

		$manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			$manifest = array();

			return $manifest;
		}

		// The manifest is a local theme file, not a remote resource.
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$decoded  = json_decode( (string) file_get_contents( $manifest_path ), true );
		$manifest = is_array( $decoded ) ? $decoded : array();

		return $manifest;
	}

	/**
	 * Determine whether the Vite manifest is unavailable.
	 */
	private static function isViteManifestMissing(): bool {
		$manifest_path = get_template_directory() . '/dist/.vite/manifest.json';

		return ! file_exists( $manifest_path );
	}

	/**
	 * Return a cache-busting version for a theme asset.
	 *
	 * @param  string $relative_path  Asset path relative to the theme directory.
	 *
	 * @return string Asset version.
	 */
	private static function fileVersion( string $relative_path ): string {
		$file_path = get_template_directory() . '/' . ltrim( $relative_path, '/' );

		return file_exists( $file_path )
			? (string) filemtime( $file_path )
			: WEBBOOKS_VERSION;
	}
}
