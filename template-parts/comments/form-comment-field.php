<?php
/**
 * Comment textarea, security fields and emoji picker.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args Template arguments.
 */

$nonce              = is_string( $args['nonce'] ?? null ) ? $args['nonce'] : '';
$recaptcha_site_key = is_string( $args['recaptcha_site_key'] ?? null ) ? $args['recaptcha_site_key'] : '';
$show_privacy       = (bool) ( $args['show_privacy'] ?? false );
$emoji              = array( '😀', '😁', '😂', '😍', '👍', '🔥', '👏', '🤔', '😎', '🙏' );
?>
<p class="comment-form-comment col-12 mb-0">
	<label class="form-label" for="comment">
		<?php esc_html_e( 'Comment:', 'webbooks' ); ?> <span class="required" aria-hidden="true">*</span>
	</label>
	<textarea id="comment" class="form-control" name="comment" rows="8" required></textarea>
</p>
<div class="comment-emoji-picker col-12" aria-label="<?php esc_attr_e( 'Emoji picker', 'webbooks' ); ?>">
	<?php foreach ( $emoji as $item ) : ?>
		<button type="button" class="btn btn-outline-secondary btn-sm comment-emoji-btn" data-emoji="<?php echo esc_attr( $item ); ?>" aria-label="<?php echo esc_attr( $item ); ?>"><?php echo esc_html( $item ); ?></button>
	<?php endforeach; ?>
</div>
<div class="comment-form-security col-12">
	<?php echo wp_kses_post( $nonce ); ?>
	<?php if ( $show_privacy ) : ?>
		<div class="form-check">
			<input id="<?php echo esc_attr( \Webbooks\Comment\CommentSecurity::PRIVACY_FIELD ); ?>" class="form-check-input" name="<?php echo esc_attr( \Webbooks\Comment\CommentSecurity::PRIVACY_FIELD ); ?>" type="checkbox" value="1" required>
			<label class="form-check-label" for="<?php echo esc_attr( \Webbooks\Comment\CommentSecurity::PRIVACY_FIELD ); ?>">
				<?php esc_html_e( 'I agree to the privacy policy.', 'webbooks' ); ?>
			</label>
		</div>
	<?php endif; ?>
	<?php if ( '' !== $recaptcha_site_key ) : ?>
		<div class="comment-captcha mt-3">
			<div class="g-recaptcha" data-sitekey="<?php echo esc_attr( $recaptcha_site_key ); ?>"></div>
		</div>
	<?php endif; ?>
</div>
