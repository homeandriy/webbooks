<?php
/**
 * Lightweight consent handling for optional analytics and advertising services.
 *
 * @package Webbooks
 */

declare(strict_types=1);

/**
 * Read the visitor's stored analytics preference.
 *
 * @return 'accepted'|'rejected'|'pending'
 */
function webbooks_cookie_consent_state(): string {
	if ( ! isset( $_COOKIE[ WEBBOOKS_CONSENT_COOKIE ] ) ) {
		return 'pending';
	}

	$state = sanitize_key( (string) wp_unslash( $_COOKIE[ WEBBOOKS_CONSENT_COOKIE ] ) );

	return in_array( $state, array( 'accepted', 'rejected' ), true ) ? $state : 'pending';
}

/**
 * Whether optional tracking services may be included in the response.
 */
function webbooks_has_tracking_consent(): bool {
	return 'accepted' === webbooks_cookie_consent_state();
}

/**
 * Remove trackers registered by plugins before WordPress prints script tags.
 *
 * CookieYes is intentionally not handled here: after this theme feature is
 * deployed, deactivate that plugin to avoid keeping two consent interfaces.
 */
function webbooks_remove_unconsented_tracking_scripts(): void {
	if ( webbooks_has_tracking_consent() ) {
		return;
	}

	$handles = apply_filters(
		'webbooks_tracking_script_handles',
		array(
			'monsterinsights-frontend-script',
			'webbooks-ga4',
			'google-adsbygoogle',
		)
	);

	if ( ! is_array( $handles ) ) {
		return;
	}

	foreach ( $handles as $handle ) {
		if ( is_string( $handle ) && '' !== $handle ) {
			wp_dequeue_script( $handle );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'webbooks_remove_unconsented_tracking_scripts', PHP_INT_MAX );
add_action( 'wp_print_scripts', 'webbooks_remove_unconsented_tracking_scripts', PHP_INT_MAX );
add_action( 'wp_print_footer_scripts', 'webbooks_remove_unconsented_tracking_scripts', 1 );

/**
 * Render the preference banner and a compact control for changing the choice.
 */
function webbooks_render_cookie_consent(): void {
	if ( is_admin() ) {
		return;
	}

	$state = webbooks_cookie_consent_state();
	?>
	<section
		id="webbooks-consent"
		class="webbooks-consent"
		data-webbooks-cookie-consent
		data-consent-state="<?php echo esc_attr( $state ); ?>"
		aria-labelledby="webbooks-consent-title"
		<?php echo 'pending' === $state ? '' : ' hidden'; ?>
	>
		<div class="webbooks-consent__content">
			<h2 class="webbooks-consent__title" id="webbooks-consent-title"><?php esc_html_e( 'Cookie preferences', 'webbooks' ); ?></h2>
			<p class="webbooks-consent__text"><?php esc_html_e( 'We use optional analytics cookies to understand which materials are useful. You can continue without analytics.', 'webbooks' ); ?></p>
			<p class="webbooks-consent__note"><?php esc_html_e( 'You can change this choice at any time.', 'webbooks' ); ?></p>
		</div>
		<div class="webbooks-consent__actions">
			<button class="webbooks-consent__button webbooks-consent__button--secondary" type="button" data-webbooks-consent-action="rejected"><?php esc_html_e( 'Continue without analytics', 'webbooks' ); ?></button>
			<button class="webbooks-consent__button webbooks-consent__button--primary" type="button" data-webbooks-consent-action="accepted"><?php esc_html_e( 'Allow analytics', 'webbooks' ); ?></button>
		</div>
	</section>
	<?php
}

add_action( 'wp_footer', 'webbooks_render_cookie_consent', 1 );

/**
 * Render the consent control in the sidebar after a visitor has chosen.
 */
function webbooks_render_cookie_consent_control(): void {
	if ( is_admin() || 'pending' === webbooks_cookie_consent_state() ) {
		return;
	}
	?>
	<button class="webbooks-consent__manage" type="button" data-webbooks-consent-manage aria-controls="webbooks-consent" aria-expanded="false">
		<?php esc_html_e( 'Configure cookies', 'webbooks' ); ?>
	</button>
	<?php
}
