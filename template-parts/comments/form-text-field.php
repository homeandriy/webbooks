<?php
/**
 * Comment form text field.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args Template arguments.
 */

$field_id     = is_string( $args['id'] ?? null ) ? $args['id'] : '';
$name         = is_string( $args['name'] ?? null ) ? $args['name'] : '';
$label        = is_string( $args['label'] ?? null ) ? $args['label'] : '';
$value        = is_string( $args['value'] ?? null ) ? $args['value'] : '';
$autocomplete = is_string( $args['autocomplete'] ?? null ) ? $args['autocomplete'] : '';
$field_type   = is_string( $args['type'] ?? null ) ? $args['type'] : 'text';
?>
<p class="comment-form-<?php echo esc_attr( $name ); ?> col-12 col-md-6 mb-0">
	<label class="form-label" for="<?php echo esc_attr( $field_id ); ?>">
		<?php echo esc_html( $label ); ?> <span class="required" aria-hidden="true">*</span>
	</label>
	<input id="<?php echo esc_attr( $field_id ); ?>" class="form-control" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $field_type ); ?>" value="<?php echo esc_attr( $value ); ?>" autocomplete="<?php echo esc_attr( $autocomplete ); ?>" required>
</p>
