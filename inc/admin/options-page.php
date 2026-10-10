<?php
/**
 * Theme options registration and admin page rendering.
 *
 * @package Webbooks
 */

add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Register theme options and their sanitization callback.
 */
function theme_options_init(): void {
	register_setting(
		'wpuniq_options',
		'wpuniq_theme_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'webbooks_sanitize_theme_options',
			'default'           => array(),
		)
	);
}

/**
 * Sanitize saved theme options.
 *
 * @param mixed $options Submitted option values.
 * @return array<string, mixed> Sanitized option values.
 */
function webbooks_sanitize_theme_options( mixed $options ): array {
	$options      = is_array( $options ) ? $options : array();
	$author_name  = sanitize_text_field( $options['author_name'] ?? '' );
	$telegram_url = \Webbooks\Support\AuthorSupport::sanitizeTelegramUrl(
		sanitize_text_field( $options['telegram_url'] ?? '' )
	);

	if ( '' !== (string) ( $options['telegram_url'] ?? '' ) && '' === $telegram_url ) {
		add_settings_error(
			'wpuniq_theme_options',
			'webbooks-invalid-telegram-url',
			__( 'Enter a valid https://t.me/ Telegram URL.', 'webbooks' )
		);
	}

	return array(
		'author_name'   => '' !== $author_name ? $author_name : 'Andrii',
		'telegram_url'  => $telegram_url,
		'payment_cards' => webbooks_sanitize_payment_cards( $options['payment_cards'] ?? array() ),
	);
}

/**
 * Validate the repeatable payment card setting before it reaches the database.
 *
 * @param mixed $cards Submitted payment cards.
 * @return array<int, array{card_number: string, card_type: string}>
 */
function webbooks_sanitize_payment_cards( mixed $cards ): array {
	if ( ! is_array( $cards ) ) {
		return array();
	}

	$sanitized  = array();
	$card_types = \Webbooks\Support\AuthorSupport::cardTypes();

	foreach ( array_slice( $cards, 0, 10 ) as $card ) {
		if ( ! is_array( $card ) ) {
			continue;
		}

		$number = preg_replace( '/\D+/', '', (string) ( $card['card_number'] ?? '' ) ) ?? '';
		$type   = sanitize_key( (string) ( $card['card_type'] ?? '' ) );
		if ( '' === $number ) {
			continue;
		}

		if ( ! \Webbooks\Support\AuthorSupport::isValidCardNumber( $number ) || ! isset( $card_types[ $type ] ) ) {
			add_settings_error(
				'wpuniq_theme_options',
				'webbooks-invalid-payment-card',
				__( 'Each payment card must have a valid number and card type.', 'webbooks' )
			);

			continue;
		}

		$sanitized[] = array(
			'card_number' => $number,
			'card_type'   => $type,
		);
	}

	return $sanitized;
}

/**
 * Register the theme options admin menu page.
 */
function theme_options_add_page(): void {
	add_menu_page(
		__( 'Theme Settings', 'webbooks' ),
		__( 'Theme Settings', 'webbooks' ),
		'edit_theme_options',
		'theme_options',
		'theme_options_do_page'
	);
}

/**
 * Render the theme options admin page.
 */
function theme_options_do_page(): void {
	$options          = get_option( 'wpuniq_theme_options', array() );
	$options          = is_array( $options ) ? $options : array();
	$settings_updated = filter_input( INPUT_GET, 'settings-updated', FILTER_VALIDATE_BOOLEAN );
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template escapes every dynamic value and renders Settings API markup.
	echo webbooks_render_template_part(
		'template-parts/admin/options-page',
		array(
			'options'          => $options,
			'settings_updated' => (bool) $settings_updated,
		)
	);
}
