<?php
/**
 * Theme settings admin page.
 *
 * @package WordPress
 * @subpackage webbooks
 *
 * @var array<string, mixed> $args Template data.
 */

$options          = is_array( $args['options'] ?? null ) ? $args['options'] : array();
$settings_updated = ! empty( $args['settings_updated'] );
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Theme Settings', 'webbooks' ); ?></h1>

	<?php if ( $settings_updated ) : ?>
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The notice template escapes its dynamic values.
		echo webbooks_render_template_part(
			'template-parts/admin/notice',
			array(
				'type'    => 'success',
				'message' => __( 'Settings saved', 'webbooks' ),
			)
		);
		?>
	<?php endif; ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'wpuniq_options' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_1]"><?php esc_html_e( 'Field 1', 'webbooks' ); ?></label></th>
				<td><input class="regular-text" type="text" name="wpuniq_theme_options[field_1]" id="wpuniq_theme_options[field_1]" value="<?php echo esc_attr( $options['field_1'] ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_2]"><?php esc_html_e( 'Field 2', 'webbooks' ); ?></label></th>
				<td><input class="regular-text" type="text" name="wpuniq_theme_options[field_2]" id="wpuniq_theme_options[field_2]" value="<?php echo esc_attr( $options['field_2'] ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_3]"><?php esc_html_e( 'Field 3', 'webbooks' ); ?></label></th>
				<td><input class="regular-text" type="text" name="wpuniq_theme_options[field_3]" id="wpuniq_theme_options[field_3]" value="<?php echo esc_attr( $options['field_3'] ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[field_4]"><?php esc_html_e( 'Field 4', 'webbooks' ); ?></label></th>
				<td><input class="regular-text" type="text" name="wpuniq_theme_options[field_4]" id="wpuniq_theme_options[field_4]" value="<?php echo esc_attr( $options['field_4'] ?? '' ); ?>"></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[hello_text]"><?php esc_html_e( 'Welcome message', 'webbooks' ); ?></label></th>
				<td><textarea class="large-text" name="wpuniq_theme_options[hello_text]" id="wpuniq_theme_options[hello_text]" rows="5"><?php echo esc_textarea( $options['hello_text'] ?? '' ); ?></textarea></td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[sidebar_pos]"><?php esc_html_e( 'Sidebar position', 'webbooks' ); ?></label></th>
				<td>
					<select name="wpuniq_theme_options[sidebar_pos]" id="wpuniq_theme_options[sidebar_pos]">
						<option value="left" <?php selected( $options['sidebar_pos'] ?? 'left', 'left' ); ?>><?php esc_html_e( 'Left', 'webbooks' ); ?></option>
						<option value="right" <?php selected( $options['sidebar_pos'] ?? 'left', 'right' ); ?>><?php esc_html_e( 'Right', 'webbooks' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="wpuniq_theme_options[show_baner]"><?php esc_html_e( 'Show banner', 'webbooks' ); ?></label></th>
				<td><input type="checkbox" name="wpuniq_theme_options[show_baner]" id="wpuniq_theme_options[show_baner]" value="1" <?php checked( $options['show_baner'] ?? '0', '1' ); ?>></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>
