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
	?>

	<div class="wrap">
		<?php
		if ( $settings_updated ) :
			?>
			<div id="message" class="updated">
				<p><strong>
				<?php
						esc_html_e( 'Settings saved', 'webbooks' );
				?>
				</strong></p>
			</div>
			<?php
		endif;
		?>
	</div>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'wpuniq_options' );
		?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_1]">Поле1:</label></th>
				<td><input type="text" name="wpuniq_theme_options[field_1]" id="wpuniq_theme_options[field_1]"
							value="<?php echo esc_attr( $options['field_1'] ?? '' ); ?>"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_2]">Поле2:</label></th>
				<td><input type="text" name="wpuniq_theme_options[field_2]" id="wpuniq_theme_options[field_2]"
							value="<?php echo esc_attr( $options['field_2'] ?? '' ); ?>"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_3]">Поле3:</label></th>
				<td><input type="text" name="wpuniq_theme_options[field_3]" id="wpuniq_theme_options[field_3]"
							value="<?php echo esc_attr( $options['field_3'] ?? '' ); ?>"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_4]">Поле4:</label></th>
				<td><input type="text" name="wpuniq_theme_options[field_4]" id="wpuniq_theme_options[field_4]"
							value="<?php echo esc_attr( $options['field_4'] ?? '' ); ?>"/></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[hello_text]">Приветствие посетителям сайта:</label></th>
				<td><textarea name="wpuniq_theme_options[hello_text]"
								id="wpuniq_theme_options[hello_text]"><?php echo esc_textarea( $options['hello_text'] ?? '' ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[sidebar_pos]">Расположение сайдбара:</label></th>
				<td><select name="wpuniq_theme_options[sidebar_pos]" id="wpuniq_theme_options[sidebar_pos]">
						<option value="left"
						<?php
						selected( $options['sidebar_pos'] ?? 'left', 'left' );
						?>
						>Слева
						</option>
						<option value="right"
						<?php
						selected( $options['sidebar_pos'] ?? 'left', 'right' );
						?>
						>Справа
						</option>
					</select></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[show_baner]">Показывать банер:</label></th>
				<td><input type="checkbox" name="wpuniq_theme_options[show_baner]" id="wpuniq_theme_options[show_baner]"
							value="1"
							<?php
							checked( $options['show_baner'] ?? '0', '1' );
							?>
					/></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" value="Применить"/></td>
			</tr>
		</table>
	</form>
	<?php
}
