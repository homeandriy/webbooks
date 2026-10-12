<?php
/** Installation settings are separate from the presentation/theme options. */
function webbooks_site_setting( string $key, mixed $default = '' ): mixed {
	$settings = get_option( 'webbooks_site_settings', array() );
	$value = is_array( $settings ) ? ( $settings[ $key ] ?? $default ) : $default;
	return apply_filters( 'webbooks_site_setting_' . $key, $value );
}
function webbooks_category_root( string $kind ): int {
	$id = absint( webbooks_site_setting( $kind . '_category' ) );
	if ( ! $id ) {
		$term = get_category_by_slug( $kind );
		$id = $term instanceof WP_Term ? $term->term_id : 0;
	}
	if ( ! $id ) {
		// The production taxonomy predates these configurable settings and uses localized slugs.
		// Keep its original roots as a last fallback, then let Polylang resolve the current language.
		$id = array( 'books' => 18, 'articles' => 19 )[ $kind ] ?? 0;
	}
	if ( $id && function_exists( 'pll_get_term' ) ) {
		$id = (int) ( pll_get_term( $id ) ?: $id );
	}
	return $id;
}
function webbooks_download_page_url(): string {
	$id = absint( webbooks_site_setting( 'download_page' ) );
	if ( $id && get_post_status( $id ) === 'publish' ) {
		return \Webbooks\Localization\Polylang::translatedPostUrl( $id );
	}
	$pages = get_posts( array( 'post_type' => 'page', 'numberposts' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'download.php' ) );
	return $pages ? \Webbooks\Localization\Polylang::translatedPostUrl( $pages[0]->ID ) : \Webbooks\Localization\Polylang::homeUrl();
}
add_action( 'admin_init', static function (): void {
	register_setting( 'webbooks_site', 'webbooks_site_settings', array( 'type' => 'array', 'default' => array(), 'sanitize_callback' => static function ( mixed $values ): array {
		$values = is_array( $values ) ? $values : array();
		$result = array();
		foreach ( array( 'books_category', 'articles_category', 'download_page', 'report_form' ) as $key ) {
			$result[ $key ] = is_scalar( $values[ $key ] ?? null ) ? absint( $values[ $key ] ) : 0;
		}
		foreach ( array( 'ad_client', 'ad_slot', 'verification_google', 'verification_bing', 'verification_yandex' ) as $key ) {
			$result[ $key ] = is_scalar( $values[ $key ] ?? null ) ? sanitize_text_field( $values[ $key ] ) : '';
		}
		foreach ( array( 'partner_url', 'partner_image', 'copyright_url' ) as $key ) {
			$result[ $key ] = is_scalar( $values[ $key ] ?? null ) ? esc_url_raw( $values[ $key ] ) : '';
		}
		return $result;
	} ) );
} );
add_action( 'admin_menu', static function (): void {
	add_options_page( __( 'Webbooks site', 'webbooks' ), 'Webbooks', 'manage_options', 'webbooks-site', static function (): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		echo '<div class="wrap"><h1>' . esc_html__( 'Webbooks site settings', 'webbooks' ) . '</h1><form method="post" action="options.php">';
		settings_fields( 'webbooks_site' );
		echo '<table class="form-table">';
		$labels = array( 'books_category' => __( 'Books category ID', 'webbooks' ), 'articles_category' => __( 'Articles category ID', 'webbooks' ), 'download_page' => __( 'Download page ID', 'webbooks' ), 'report_form' => __( 'Ninja Forms report form ID', 'webbooks' ), 'ad_client' => 'AdSense client', 'ad_slot' => 'AdSense slot', 'verification_google' => 'Google verification', 'verification_bing' => 'Bing verification', 'verification_yandex' => 'Yandex verification', 'partner_url' => __( 'Partner URL', 'webbooks' ), 'partner_image' => __( 'Partner image URL', 'webbooks' ), 'copyright_url' => __( 'Copyright URL', 'webbooks' ) );
		foreach ( $labels as $key => $label ) {
			echo '<tr><th><label for="wb-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td><input class="regular-text" id="wb-' . esc_attr( $key ) . '" name="webbooks_site_settings[' . esc_attr( $key ) . ']" value="' . esc_attr( (string) webbooks_site_setting( $key ) ) . '"></td></tr>';
		}
		echo '</table>';
		submit_button();
		echo '</form></div>';
	} );
} );
