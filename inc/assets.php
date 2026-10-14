<?php

add_action( 'wp_enqueue_scripts', 'theme_register_scripts', 1 );

/**
 * Preload only the primary local text font used above the fold.
 *
 * JavaScript Vite modules defer by browser specification. The decorative logo
 * font stays lazy so it does not compete with styles, LCP images or Inter.
 */
add_action( 'wp_head', 'webbooks_preload_critical_font', 1 );
function webbooks_preload_critical_font(): void {
	if ( is_admin() || is_page_template( 'portfolio-good.php' ) ) {
		return;
	}

	$fonts = glob( get_template_directory() . '/dist/assets/InterVariable.*.woff2' );
	if ( false === $fonts || 1 !== count( $fonts ) ) {
		return;
	}

	$font_url = get_template_directory_uri() . '/dist/assets/' . basename( $fonts[0] );
	echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="font" type="font/woff2" crossorigin fetchpriority="high">' . "\n";
}

function theme_register_scripts(): void {
	wp_register_script(
		'functions-js',
		esc_url( trailingslashit( get_template_directory_uri() ) . 'assets/js/functions.js' ),
		array(),
		webbooks_file_version( 'assets/js/functions.js' ),
		true
	);

	wp_localize_script(
		'functions-js',
		'webbooksConfig',
		array(
			'admin_ajax' => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( WEBBOOKS_AJAX_NONCE ),
			'language'   => \Webbooks\Localization\Polylang::currentLanguageSlug(),
			'i18n'       => array( 'preview_loading' => __( 'Loading...', 'webbooks' ) ),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'additional_theme_scripts', 1 );
function additional_theme_scripts(): void {
	wp_localize_script(
		'functions-js',
		'webbooksAjax',
		array(
			'admin_ajax'     => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( WEBBOOKS_AJAX_NONCE ),
			'download_nonce' => wp_create_nonce( WEBBOOKS_DOWNLOAD_NONCE ),
			'home_url'       => home_url(),
			'search'         => array(
				'minimumLength' => WEBBOOKS_SEARCH_MINIMUM_LENGTH,
			),
			'messages'       => array(
				'loading' => __( 'Loading results...', 'webbooks' ),
				'found'   => __( 'Found results: %d', 'webbooks' ),
				'minimum' => sprintf( __( 'Type at least %d characters to search.', 'webbooks' ), WEBBOOKS_SEARCH_MINIMUM_LENGTH ),
				'error'   => __( 'Could not load results. Please try again.', 'webbooks' ),
				'expired' => __( 'Your session has expired. Reload the page to search again.', 'webbooks' ),
				'retry'   => __( 'Try again', 'webbooks' ),
				'reload'  => __( 'Reload page', 'webbooks' ),
			),
		)
	);
}

add_action( 'wp_enqueue_scripts', 'webbooks_enqueue_assets', 10 );
function webbooks_enqueue_assets(): void {
	if ( is_admin() ) {
		return;
	}
	if ( is_404() ) {
		wp_enqueue_style( 'webbooks-not-found', WEBBOOKS_URL . '/assets/css/not-found.css', array(), webbooks_file_version( 'assets/css/not-found.css' ) );
	}

	if ( is_page_template( 'portfolio-good.php' ) ) {
		wp_enqueue_style(
			'webbooks-portfolio',
			get_template_directory_uri() . '/assets/portfolio/css/main.css',
			array(),
			webbooks_file_version( 'assets/portfolio/css/main.css' )
		);
		wp_enqueue_style(
			'webbooks-portfolio-modern',
			get_template_directory_uri() . '/assets/portfolio/css/portfolio-modern.css',
			array( 'webbooks-portfolio' ),
			webbooks_file_version( 'assets/portfolio/css/portfolio-modern.css' )
		);
		wp_enqueue_style(
			'webbooks-consent',
			get_template_directory_uri() . '/assets/css/consent.css',
			array( 'webbooks-portfolio-modern' ),
			webbooks_file_version( 'assets/css/consent.css' )
		);

		$manifest = webbooks_get_vite_manifest();
		$portfolio_bundle = $manifest['src/portfolio.js'] ?? null;
		if ( is_array( $portfolio_bundle ) && isset( $portfolio_bundle['file'] ) ) {
			wp_enqueue_script( 'webbooks-portfolio', WEBBOOKS_URL . '/dist/' . $portfolio_bundle['file'], array(), webbooks_file_version( 'dist/' . $portfolio_bundle['file'] ), true );
		} else {
			wp_enqueue_script( 'webbooks-portfolio', WEBBOOKS_URL . '/src/portfolio.js', array(), webbooks_file_version( 'src/portfolio.js' ), true );
		}
		wp_script_add_data( 'webbooks-portfolio', 'type', 'module' );
		$support_bundle = $manifest['src/author-support.js'] ?? null;
		if ( is_array( $support_bundle ) && isset( $support_bundle['file'] ) ) {
			wp_enqueue_script( 'webbooks-author-support', WEBBOOKS_URL . '/dist/' . $support_bundle['file'], array(), webbooks_file_version( 'dist/' . $support_bundle['file'] ), true );
			wp_script_add_data( 'webbooks-author-support', 'type', 'module' );
			webbooks_localize_author_support( 'webbooks-author-support' );
		}

		return;
	}

	wp_enqueue_script( 'functions-js' );

	$manifest   = webbooks_get_vite_manifest();
	$mainBundle = $manifest['src/main.js'] ?? null;

	if ( $mainBundle ) {
		foreach ( webbooks_vite_css( $manifest, 'src/main.js' ) as $cssFile ) {
				wp_enqueue_style(
					'webbooks-bundle-css-' . md5( $cssFile ),
					get_template_directory_uri() . '/dist/' . ltrim( $cssFile, '/' ),
					array(),
					webbooks_file_version( 'dist/' . ltrim( $cssFile, '/' ) )
				);
		}

		wp_enqueue_script(
			'webbooks-bundle',
			get_template_directory_uri() . '/dist/' . ltrim( $mainBundle['file'], '/' ),
			array( 'functions-js' ),
			webbooks_file_version( 'dist/' . ltrim( $mainBundle['file'], '/' ) ),
			true
		);

		wp_script_add_data( 'webbooks-bundle', 'type', 'module' );
		webbooks_localize_author_support( 'webbooks-bundle' );
		$gallery_bundle = $manifest['src/gallery.js'] ?? null;
		if ( is_singular( 'post' ) && is_array( $gallery_bundle ) && isset( $gallery_bundle['file'] ) ) {
			foreach ( webbooks_vite_css( $manifest, 'src/gallery.js' ) as $css_file ) {
				wp_enqueue_style( 'webbooks-bundle-css-' . md5( $css_file ), get_template_directory_uri() . '/dist/' . $css_file, array(), webbooks_file_version( 'dist/' . $css_file ) );
			}
			wp_enqueue_script( 'webbooks-gallery', get_template_directory_uri() . '/dist/' . $gallery_bundle['file'], array( 'webbooks-bundle' ), webbooks_file_version( 'dist/' . $gallery_bundle['file'] ), true );
			wp_script_add_data( 'webbooks-gallery', 'type', 'module' );
		}
	} else {
		// Fallback for environments without build step.
		wp_enqueue_style( 'webbooks-bootstrap5', get_template_directory_uri() . '/assets/vendor/bootstrap/bootstrap.min.css', array(), webbooks_file_version( 'assets/vendor/bootstrap/bootstrap.min.css' ) );
		wp_enqueue_style( 'webbooks-style', get_template_directory_uri() . '/style.css', array(), webbooks_file_version( 'style.css' ) );
		wp_enqueue_style( 'webbooks-search-ui', get_template_directory_uri() . '/assets/css/search-ui.css', array( 'webbooks-style' ), webbooks_file_version( 'assets/css/search-ui.css' ) );
		wp_enqueue_style( 'webbooks-consent', get_template_directory_uri() . '/assets/css/consent.css', array( 'webbooks-style' ), webbooks_file_version( 'assets/css/consent.css' ) );
		wp_enqueue_style( 'webbooks-navigation-ui', get_template_directory_uri() . '/assets/css/navigation-ui.css', array( 'webbooks-bootstrap5-theme' ), webbooks_file_version( 'assets/css/navigation-ui.css' ) );
		wp_enqueue_style( 'webbooks-bootstrap5-theme', get_template_directory_uri() . '/assets/css/bootstrap5-theme.css', array( 'webbooks-search-ui' ), webbooks_file_version( 'assets/css/bootstrap5-theme.css' ) );
		wp_enqueue_script( 'webbooks-bootstrap5', get_template_directory_uri() . '/assets/vendor/bootstrap/bootstrap.bundle.min.js', array(), webbooks_file_version( 'assets/vendor/bootstrap/bootstrap.bundle.min.js' ), true );
		wp_add_inline_script( 'webbooks-bootstrap5', 'window.WebBooksUI={showModal:function(s){var e=document.querySelector(s);if(e)bootstrap.Modal.getOrCreateInstance(e).show();},showCollapse:function(s){var e=document.querySelector(s);if(e)bootstrap.Collapse.getOrCreateInstance(e,{toggle:false}).show();}};' );
		wp_enqueue_script( 'webbooks-runtime-fallback', get_template_directory_uri() . '/src/fallback-runtime.js', array( 'functions-js' ), webbooks_file_version( 'src/fallback-runtime.js' ), true );
		wp_script_add_data( 'webbooks-runtime-fallback', 'type', 'module' );
	}
}

/** Collect CSS from shared Vite chunks before the entry's own overrides. */
function webbooks_vite_css( array $manifest, string $entry ): array {
	$visited = array();
	$styles = array();
	$walk = static function ( string $key ) use ( &$walk, &$visited, &$styles, $manifest ): void {
		if ( isset( $visited[ $key ] ) || ! isset( $manifest[ $key ] ) ) { return; }
		$visited[ $key ] = true;
		foreach ( $manifest[ $key ]['imports'] ?? array() as $import ) { $walk( $import ); }
		foreach ( $manifest[ $key ]['css'] ?? array() as $css ) { $styles[ $css ] = $css; }
	};
	$walk( $entry );
	return array_values( $styles );
}

add_filter( 'script_loader_tag', 'webbooks_preserve_bundle_module_type', 20, 3 );
function webbooks_preserve_bundle_module_type( string $tag, string $handle, string $src ): string {
	if ( ! in_array( $handle, array( 'webbooks-bundle', 'webbooks-gallery', 'webbooks-author-support', 'webbooks-portfolio', 'webbooks-runtime-fallback' ), true ) ) {
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

add_filter( 'autoptimize_filter_js_exclude', 'webbooks_exclude_bundle_from_autoptimize' );
function webbooks_exclude_bundle_from_autoptimize( string $excluded ): string {
	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-bundle' );
	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-bundle-js' );
	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-portfolio' );

	return webbooks_append_exclusion_item( $excluded, '/dist/' );
}

add_filter( 'autoptimize_filter_css_exclude', 'webbooks_exclude_bundle_css_from_autoptimize' );
function webbooks_exclude_bundle_css_from_autoptimize( string $excluded ): string {
	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-bundle-' );

	return webbooks_append_exclusion_item( $excluded, '/dist/assets/' );
}

add_filter( 'rocket_exclude_js', 'webbooks_exclude_bundle_from_wp_rocket' );
function webbooks_exclude_bundle_from_wp_rocket( array $excluded ): array {
	$excluded[] = 'webbooks-bundle';
	$excluded[] = 'webbooks-bundle-js';
	$excluded[] = 'webbooks-portfolio';
	$excluded[] = '/dist/';

	return array_values( array_unique( $excluded ) );
}

add_filter( 'rocket_defer_js_exclusions', 'webbooks_exclude_bundle_from_wp_rocket' );
add_filter( 'rocket_minify_excluded_external_js', 'webbooks_exclude_bundle_from_wp_rocket' );
add_filter( 'rocket_delay_js_exclusions', 'webbooks_exclude_bundle_from_wp_rocket' );
add_filter( 'rocket_exclude_css', 'webbooks_exclude_bundle_css_from_wp_rocket' );
add_filter( 'rocket_rucss_excluded_selectors', 'webbooks_exclude_bundle_css_from_wp_rocket' );

function webbooks_exclude_bundle_css_from_wp_rocket( array $excluded ): array {
	$excluded[] = 'webbooks-bundle-';
	$excluded[] = '/dist/assets/';

	return array_values( array_unique( $excluded ) );
}

add_filter( 'litespeed_optimize_js_excludes', 'webbooks_exclude_bundle_from_litespeed' );
add_filter( 'litespeed_optm_js_defer_exc', 'webbooks_exclude_bundle_from_litespeed' );
add_filter( 'litespeed_optm_js_delay_exc', 'webbooks_exclude_bundle_from_litespeed' );
function webbooks_exclude_bundle_from_litespeed( array|string $excluded ): array|string {
	if ( is_array( $excluded ) ) {
		$excluded[] = 'webbooks-bundle';
		$excluded[] = 'webbooks-bundle-js';
		$excluded[] = 'webbooks-portfolio';
		$excluded[] = '/dist/';

		return array_values( array_unique( $excluded ) );
	}

	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-bundle' );
	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-bundle-js' );
	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-portfolio' );

	return webbooks_append_exclusion_item( $excluded, '/dist/' );
}

add_filter( 'litespeed_optm_css_exc', 'webbooks_exclude_bundle_css_from_litespeed' );
add_filter( 'litespeed_optm_ucss_exc', 'webbooks_exclude_bundle_css_from_litespeed' );
function webbooks_exclude_bundle_css_from_litespeed( array|string $excluded ): array|string {
	if ( is_array( $excluded ) ) {
		$excluded[] = 'webbooks-bundle-';
		$excluded[] = '/dist/assets/';

		return array_values( array_unique( $excluded ) );
	}

	$excluded = webbooks_append_exclusion_item( $excluded, 'webbooks-bundle-' );

	return webbooks_append_exclusion_item( $excluded, '/dist/assets/' );
}

function webbooks_append_exclusion_item( string $excluded_list, string $item ): string {
	$items = array_filter( array_map( 'trim', explode( ',', $excluded_list ) ) );

	if ( ! in_array( $item, $items, true ) ) {
		$items[] = $item;
	}

	return implode( ',', $items );
}


add_action( 'admin_notices', 'webbooks_vite_manifest_admin_notice' );
function webbooks_vite_manifest_admin_notice(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! webbooks_is_vite_manifest_missing() ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	echo esc_html__( 'Webbooks: Vite manifest is missing (dist/.vite/manifest.json). The theme is running in fallback mode.', 'webbooks' );
	echo '</p></div>';
}

add_action( 'wp_enqueue_scripts', 'webbooks_enqueue_external_services', 20 );
function webbooks_enqueue_external_services(): void {
	if ( is_admin() || is_page_template( 'portfolio-good.php' ) ) {
		return;
	}

	if ( is_singular() && comments_open() && webbooks_is_recaptcha_configured() && apply_filters( 'webbooks_enable_recaptcha', true ) ) {
		wp_enqueue_script( 'google-recaptcha-api', 'https://www.google.com/recaptcha/api.js', array(), null, true );
	}

	$ad_client = (string) webbooks_site_setting( 'ad_client' );
	if ( webbooks_has_tracking_consent() && preg_match( '/^ca-pub-\d+$/', $ad_client ) && apply_filters( 'webbooks_enable_google_ads', ! is_user_logged_in() ) ) {
		wp_enqueue_script( 'google-adsbygoogle', 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', array(), null, false );
		wp_script_add_data( 'google-adsbygoogle', 'strategy', 'async' );
		wp_add_inline_script( 'google-adsbygoogle', '(adsbygoogle=window.adsbygoogle||[]).push(' . wp_json_encode( array( 'google_ad_client' => $ad_client, 'enable_page_level_ads' => true ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ');', 'after' );
	}

	$ga4_measurement_id = webbooks_get_ga4_measurement_id();
	if ( webbooks_has_tracking_consent() && $ga4_measurement_id !== '' && ! defined( 'MONSTERINSIGHTS_VERSION' ) && ! function_exists( 'MonsterInsights' ) && apply_filters( 'webbooks_enable_google_analytics', ! is_user_logged_in() ) ) {
		wp_enqueue_script(
			'webbooks-ga4',
			'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode( $ga4_measurement_id ),
			array(),
			null,
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

function webbooks_get_ga4_measurement_id(): string {
	$measurement_id = defined( 'WEBBOOKS_GA4_MEASUREMENT_ID' ) ? (string) WEBBOOKS_GA4_MEASUREMENT_ID : '';
	$measurement_id = strtoupper( trim( (string) apply_filters( 'webbooks_ga4_measurement_id', $measurement_id ) ) );

	return preg_match( '/^G-[A-Z0-9]+$/', $measurement_id ) === 1 ? $measurement_id : '';
}

function webbooks_get_vite_manifest(): array {
	static $manifest = null;

	if ( $manifest !== null ) {
		return $manifest;
	}

	$manifestPath = get_template_directory() . '/dist/.vite/manifest.json';

	if ( ! file_exists( $manifestPath ) ) {
		$manifest = array();
		return $manifest;
	}

	$decoded  = json_decode( (string) file_get_contents( $manifestPath ), true );
	$manifest = is_array( $decoded ) ? $decoded : array();

	return $manifest;
}

function webbooks_is_vite_manifest_missing(): bool {
	$manifestPath = get_template_directory() . '/dist/.vite/manifest.json';

	return ! file_exists( $manifestPath );
}

/**
 * Return the cache-busting tag generated by the most recent Vite build.
 *
 * @return string Build tag or the theme version when no generated tag exists.
 */
function webbooks_build_tag(): string {
	static $build_tag = null;

	if ( null !== $build_tag ) {
		return $build_tag;
	}

	$build_tag_path = WEBBOOKS_PATH . '/dist/.vite/build-tag';
	if ( is_readable( $build_tag_path ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reads a local, theme-generated build marker.
		$candidate = trim( (string) file_get_contents( $build_tag_path ) );
		if ( preg_match( '/^[A-Za-z0-9._-]+$/', $candidate ) === 1 ) {
			$build_tag = $candidate;

			return $build_tag;
		}
	}

	$build_tag = WEBBOOKS_VERSION;

	return $build_tag;
}

/**
 * Return a version for a theme asset.
 *
 * Vite assets use the generated build tag so a successful rebuild invalidates
 * cached CSS and JavaScript even when Vite keeps content-identical filenames.
 *
 * @param string $relativePath Path relative to the theme root.
 * @return string Asset version.
 */
function webbooks_file_version( string $relativePath ): string {
	if ( str_starts_with( ltrim( $relativePath, '/' ), 'dist/' ) ) {
		return webbooks_build_tag();
	}

	$filePath = get_template_directory() . '/' . ltrim( $relativePath, '/' );

	return file_exists( $filePath )
		? (string) filemtime( $filePath )
		: WEBBOOKS_VERSION;
}

/** Public support endpoint configuration; payment details stay behind explicit AJAX requests. */
function webbooks_localize_author_support( string $handle ): void {
	$language = \Webbooks\Localization\Polylang::currentLanguageSlug() ?? substr( determine_locale(), 0, 2 );
	wp_localize_script( $handle, 'webbooksSupportConfig', array(
		'adminAjax' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( WEBBOOKS_AJAX_NONCE ),
		'motivationMessages' => \Webbooks\Support\AuthorSupport::motivationMessages( $language ),
		'i18n' => array(
			'loading' => __( 'Loading payment details…', 'webbooks' ),
			'noCards' => __( 'Payment details are not available yet.', 'webbooks' ),
			'error' => __( 'Unable to load the details. Please try again.', 'webbooks' ),
			'copy' => __( 'Copy card number', 'webbooks' ),
			'copied' => __( 'Copied', 'webbooks' ),
		),
	) );
}
