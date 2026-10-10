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
$payment_cards    = is_array( $options['payment_cards'] ?? null ) ? $options['payment_cards'] : array();
$card_types       = \Webbooks\Support\AuthorSupport::cardTypes();
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
	<?php settings_errors( 'wpuniq_theme_options' ); ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'wpuniq_options' ); ?>

		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="webbooks-author-name"><?php esc_html_e( 'Author name', 'webbooks' ); ?></label></th>
				<td>
					<input class="regular-text" type="text" name="wpuniq_theme_options[author_name]" id="webbooks-author-name" value="<?php echo esc_attr( $options['author_name'] ?? 'Andrii' ); ?>">
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="webbooks-telegram-url"><?php esc_html_e( 'Telegram contact URL', 'webbooks' ); ?></label></th>
				<td>
					<input class="regular-text" type="url" name="wpuniq_theme_options[telegram_url]" id="webbooks-telegram-url" value="<?php echo esc_attr( $options['telegram_url'] ?? 'https://t.me/webbooks_support_bot?start=site' ); ?>" placeholder="https://t.me/username">
					<p class="description"><?php esc_html_e( 'The public page loads this URL only after the visitor clicks a contact button.', 'webbooks' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Payment cards', 'webbooks' ); ?></th>
				<td>
					<div id="webbooks-payment-cards" data-invalid-message="<?php echo esc_attr__( 'Enter a valid payment card number.', 'webbooks' ); ?>">
						<p class="description">
							<?php esc_html_e( 'Card numbers are validated with the Luhn (Mod 10) checksum before saving. This checks the number format only, not whether the card is active or funded.', 'webbooks' ); ?>
						</p>
						<div data-webbooks-payment-card-list>
							<?php foreach ( $payment_cards as $index => $payment_card ) : ?>
								<?php
								$card_number = preg_replace( '/\D+/', '', (string) ( $payment_card['card_number'] ?? '' ) ) ?? '';
								$card_type   = sanitize_key( (string) ( $payment_card['card_type'] ?? 'other' ) );
								?>
								<div class="webbooks-payment-card-row">
									<input class="regular-text" type="text" inputmode="numeric" autocomplete="cc-number" name="wpuniq_theme_options[payment_cards][<?php echo esc_attr( (string) $index ); ?>][card_number]" value="<?php echo esc_attr( trim( chunk_split( $card_number, 4, ' ' ) ) ); ?>" data-webbooks-card-number>
									<select name="wpuniq_theme_options[payment_cards][<?php echo esc_attr( (string) $index ); ?>][card_type]">
										<?php foreach ( $card_types as $type_key => $type_label ) : ?>
											<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $card_type, $type_key ); ?>><?php echo esc_html( $type_label ); ?></option>
										<?php endforeach; ?>
									</select>
									<button type="button" class="button-link-delete" data-webbooks-remove-payment-card><?php esc_html_e( 'Remove', 'webbooks' ); ?></button>
								</div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button" data-webbooks-add-payment-card><?php esc_html_e( 'Add card', 'webbooks' ); ?></button>
					</div>
				</td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
	<template id="webbooks-payment-card-template">
		<div class="webbooks-payment-card-row">
			<input class="regular-text" type="text" inputmode="numeric" autocomplete="cc-number" name="wpuniq_theme_options[payment_cards][__index__][card_number]" value="" data-webbooks-card-number>
			<select name="wpuniq_theme_options[payment_cards][__index__][card_type]">
				<?php foreach ( $card_types as $type_key => $type_label ) : ?>
					<option value="<?php echo esc_attr( $type_key ); ?>"><?php echo esc_html( $type_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="button" class="button-link-delete" data-webbooks-remove-payment-card><?php esc_html_e( 'Remove', 'webbooks' ); ?></button>
		</div>
	</template>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		var container = document.getElementById('webbooks-payment-cards');
		var template = document.getElementById('webbooks-payment-card-template');
		if (!container || !template) {
			return;
		}

		var formatCardNumber = function (value) {
			return value.replace(/\D/g, '').slice(0, 19).replace(/(.{4})/g, '$1 ').trim();
		};
		var isValidCardNumber = function (value) {
			var digits = value.replace(/\D/g, '');
			var sum = 0;
			var alternate = false;
			if (!/^\d{12,19}$/.test(digits)) {
				return false;
			}
			for (var index = digits.length - 1; index >= 0; index -= 1) {
				var digit = Number(digits.charAt(index));
				if (alternate) {
					digit *= 2;
					digit = digit > 9 ? digit - 9 : digit;
				}
				sum += digit;
				alternate = !alternate;
			}
			return sum % 10 === 0;
		};

		container.addEventListener('input', function (event) {
			if (event.target.matches('[data-webbooks-card-number]')) {
				event.target.value = formatCardNumber(event.target.value);
				event.target.setCustomValidity('');
			}
		});
		container.addEventListener('click', function (event) {
			if (event.target.matches('[data-webbooks-remove-payment-card]')) {
				event.target.closest('.webbooks-payment-card-row').remove();
			}
			if (event.target.matches('[data-webbooks-add-payment-card]')) {
				var list = container.querySelector('[data-webbooks-payment-card-list]');
				var index = list.children.length;
				list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__index__', String(index)));
			}
		});
		container.closest('form').addEventListener('submit', function (event) {
			var invalid = false;
			container.querySelectorAll('[data-webbooks-card-number]').forEach(function (input) {
				var hasValue = input.value.replace(/\D/g, '').length > 0;
				var valid = !hasValue || isValidCardNumber(input.value);
				input.setCustomValidity(valid ? '' : container.dataset.invalidMessage);
				invalid = invalid || !valid;
			});
			if (invalid) {
				event.preventDefault();
				container.closest('form').reportValidity();
			}
		});
	});
</script>
