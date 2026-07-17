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
 * @return array<string, string> Sanitized option values.
 */
function webbooks_sanitize_theme_options( mixed $options ): array {
	$options = is_array( $options ) ? $options : array();

	return array(
		'field_1'     => sanitize_text_field( $options['field_1'] ?? '' ),
		'field_2'     => sanitize_text_field( $options['field_2'] ?? '' ),
		'field_3'     => sanitize_text_field( $options['field_3'] ?? '' ),
		'field_4'     => sanitize_text_field( $options['field_4'] ?? '' ),
		'hello_text'  => sanitize_textarea_field( $options['hello_text'] ?? '' ),
		'sidebar_pos' => in_array( $options['sidebar_pos'] ?? '', array( 'left', 'right' ), true ) ? $options['sidebar_pos'] : 'left',
		'show_baner'  => empty( $options['show_baner'] ) ? '0' : '1',
	);
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
