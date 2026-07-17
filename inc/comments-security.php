<?php
/**
 * Comment submission security, rate limiting, and language filtering.
 *
 * @package Webbooks
 */

const WEBBOOKS_COMMENT_NONCE_ACTION  = 'webbooks_comment_submit';
const WEBBOOKS_COMMENT_NONCE_NAME    = 'webbooks_comment_nonce';
const WEBBOOKS_COMMENT_PRIVACY_FIELD = 'webbooks_comment_privacy';



add_action( 'admin_notices', 'webbooks_comments_recaptcha_admin_notice' );

/**
 * Display a configuration notice when reCAPTCHA keys are unavailable.
 */
function webbooks_comments_recaptcha_admin_notice(): void {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) || webbooks_is_recaptcha_configured() ) {
		return;
	}

	echo webbooks_render_template_part(
		'template-parts/admin/notice',
		array(
			'type'    => 'warning',
			'message' => __( 'Comments are disabled. Please configure GC_V2_PUBLIC and GC_V2_SECRET constants in wp-config.php.', 'webbooks' ),
		)
	);
}

/**
 * Get the configured reCAPTCHA site key.
 */
function webbooks_get_recaptcha_site_key(): string {
	$from_const = defined( 'GC_V2_PUBLIC' ) ? (string) constant( 'GC_V2_PUBLIC' ) : '';

	return (string) apply_filters( 'webbooks_recaptcha_site_key', trim( $from_const ) );
}

/**
 * Get the configured reCAPTCHA secret key.
 */
function webbooks_get_recaptcha_secret_key(): string {
	$from_const = defined( 'GC_V2_SECRET' ) ? (string) constant( 'GC_V2_SECRET' ) : '';

	return (string) apply_filters( 'webbooks_recaptcha_secret_key', trim( $from_const ) );
}

/**
 * Determine whether reCAPTCHA is configured.
 */
function webbooks_is_recaptcha_configured(): bool {
	return '' !== webbooks_get_recaptcha_site_key() && '' !== webbooks_get_recaptcha_secret_key();
}

add_filter( 'preprocess_comment', 'webbooks_validate_comment_security' );

/**
 * Validate comment input before WordPress creates the comment.
 *
 * @param array<string, mixed> $comment_data Comment fields.
 * @return array<string, mixed> Sanitized comment fields.
 */
function webbooks_validate_comment_security( array $comment_data ): array {
	if ( is_admin() ) {
		return $comment_data;
	}

	if ( ! webbooks_is_recaptcha_configured() ) {
		wp_die(
			esc_html__( 'Comments are currently disabled by site configuration.', 'webbooks' ),
			esc_html__( 'Comments unavailable', 'webbooks' ),
			array(
				'response'  => 503,
				'back_link' => true,
			)
		);
	}

	$nonce = sanitize_text_field( (string) filter_input( INPUT_POST, WEBBOOKS_COMMENT_NONCE_NAME ) );
	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, WEBBOOKS_COMMENT_NONCE_ACTION ) ) {
		wp_die(
			esc_html__( 'Security check failed. Please refresh the page and try again.', 'webbooks' ),
			esc_html__( 'Security error', 'webbooks' ),
			array(
				'response'  => 403,
				'back_link' => true,
			)
		);
	}

	$is_guest = ! is_user_logged_in();

	if ( $is_guest ) {
		$author           = sanitize_text_field( (string) ( $comment_data['comment_author'] ?? '' ) );
		$email            = sanitize_email( (string) ( $comment_data['comment_author_email'] ?? '' ) );
		$privacy_accepted = sanitize_text_field( (string) filter_input( INPUT_POST, WEBBOOKS_COMMENT_PRIVACY_FIELD ) );

		if ( '' === $author || '' === $email || ! is_email( $email ) ) {
			wp_die(
				esc_html__( 'Please provide a display name and a valid email address.', 'webbooks' ),
				esc_html__( 'Validation error', 'webbooks' ),
				array(
					'response'  => 400,
					'back_link' => true,
				)
			);
		}

		if ( '1' !== $privacy_accepted ) {
			wp_die(
				esc_html__( 'You must accept the privacy policy before posting a comment.', 'webbooks' ),
				esc_html__( 'Validation error', 'webbooks' ),
				array(
					'response'  => 400,
					'back_link' => true,
				)
			);
		}

		$clean_content = wp_strip_all_tags( (string) ( $comment_data['comment_content'] ?? '' ) );
		$clean_content = preg_replace( '~(https?://\S+|www\.\S+)~iu', '', $clean_content ) ?? '';
		$clean_content = trim( $clean_content );

		if ( '' === $clean_content ) {
			wp_die(
				esc_html__( 'Comment text is required.', 'webbooks' ),
				esc_html__( 'Validation error', 'webbooks' ),
				array(
					'response'  => 400,
					'back_link' => true,
				)
			);
		}

		$comment_data['comment_author']       = $author;
		$comment_data['comment_author_email'] = $email;
		$comment_data['comment_author_url']   = '';
		$comment_data['comment_content']      = $clean_content;
	}

	$captcha_response = sanitize_text_field( (string) filter_input( INPUT_POST, 'g-recaptcha-response' ) );
	if ( ! webbooks_verify_recaptcha( $captcha_response ) ) {
		wp_die(
			esc_html__( 'Captcha verification failed. Please confirm you are not a robot.', 'webbooks' ),
			esc_html__( 'Captcha error', 'webbooks' ),
			array(
				'response'  => 403,
				'back_link' => true,
			)
		);
	}

	if ( $is_guest ) {
		$ip    = webbooks_get_comment_request_ip();
		$email = sanitize_email( (string) ( $comment_data['comment_author_email'] ?? '' ) );

		if ( webbooks_is_comment_rate_limited( $ip, $email ) ) {
			$cooldown = max( 1, (int) apply_filters( 'webbooks_comment_rate_limit_seconds', 30 ) );
			wp_die(
				/* translators: %d: Seconds before next allowed comment. */
				esc_html( sprintf( __( 'Too many comments. Please wait %d seconds before posting again.', 'webbooks' ), $cooldown ) ),
				esc_html__( 'Rate limit reached', 'webbooks' ),
				array(
					'response'  => 429,
					'back_link' => true,
				)
			);
		}
	}

	return $comment_data;
}

add_action( 'comment_post', 'webbooks_mark_comment_rate_limit', 10, 2 );

/**
 * Persist a rate-limit marker after a guest comment is accepted.
 *
 * @param int        $comment_id       Comment ID.
 * @param int|string $comment_approved Comment approval status.
 */
function webbooks_mark_comment_rate_limit( int $comment_id, $comment_approved ): void {
	if ( 0 === (int) $comment_approved || 'spam' === $comment_approved || 'trash' === $comment_approved ) {
		return;
	}

	$comment = get_comment( $comment_id );
	if ( ! $comment instanceof WP_Comment ) {
		return;
	}

	if ( 0 === (int) $comment->user_id ) {
		$ip       = webbooks_get_comment_request_ip();
		$email    = sanitize_email( (string) $comment->comment_author_email );
		$key      = webbooks_comment_rate_limit_key( $ip, $email );
		$cooldown = max( 1, (int) apply_filters( 'webbooks_comment_rate_limit_seconds', 30 ) );
		set_transient( $key, time(), $cooldown );
	}

	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language( 'slug' );
		if ( ! empty( $lang ) ) {
			add_comment_meta( $comment_id, 'webbooks_comment_lang', sanitize_key( (string) $lang ), true );
		}
	}
}

add_filter( 'comments_array', 'webbooks_filter_comments_by_current_language' );

/**
 * Keep only comments stored for the current Polylang language.
 *
 * @param array<int, WP_Comment> $comments Comments to filter.
 * @return array<int, WP_Comment> Comments for the current language.
 */
function webbooks_filter_comments_by_current_language( array $comments ): array {
	if ( is_admin() || ! function_exists( 'pll_current_language' ) ) {
		return $comments;
	}

	$current_lang = (string) pll_current_language( 'slug' );
	if ( '' === $current_lang ) {
		return $comments;
	}

	return array_values(
		array_filter(
			$comments,
			static function ( $comment ) use ( $current_lang ) {
				if ( ! $comment instanceof WP_Comment ) {
					return false;
				}

				$comment_lang = (string) get_comment_meta( (int) $comment->comment_ID, 'webbooks_comment_lang', true );
				if ( '' === $comment_lang ) {
					return false;
				}

				return $comment_lang === $current_lang;
			}
		)
	);
}


add_filter( 'get_comments_number', 'webbooks_filter_comments_number_by_language', 10, 2 );

/**
 * Return the number of comments stored for the current Polylang language.
 *
 * @param string|int $count   Existing comment count.
 * @param int        $post_id Post ID.
 * @return string|int Localized comment count.
 */
function webbooks_filter_comments_number_by_language( $count, $post_id ) {
	if ( is_admin() || ! function_exists( 'pll_current_language' ) ) {
		return $count;
	}

	$current_lang = (string) pll_current_language( 'slug' );
	if ( '' === $current_lang ) {
		return $count;
	}

	$localized_count = get_comments(
		array(
			'post_id'    => (int) $post_id,
			'status'     => 'approve',
			'count'      => true,
			// The comment-language meta is the canonical data model for this filter.
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_key'   => 'webbooks_comment_lang',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'meta_value' => $current_lang,
		)
	);

	return (string) $localized_count;
}

/**
 * Determine whether an IP and email pair is currently rate limited.
 *
 * @param string $ip    Visitor IP address.
 * @param string $email Visitor email address.
 */
function webbooks_is_comment_rate_limited( string $ip, string $email ): bool {
	$key = webbooks_comment_rate_limit_key( $ip, $email );

	return get_transient( $key ) !== false;
}

/**
 * Build the transient key used for comment rate limiting.
 *
 * @param string $ip    Visitor IP address.
 * @param string $email Visitor email address.
 */
function webbooks_comment_rate_limit_key( string $ip, string $email ): string {
	return 'webbooks_comment_rl_' . md5( strtolower( trim( $ip ) ) . '|' . strtolower( trim( $email ) ) );
}

/**
 * Get and sanitize the visitor IP address.
 */
function webbooks_get_comment_request_ip(): string {
	return sanitize_text_field( wp_unslash( (string) ( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ) ) );
}

/**
 * Verify the visitor reCAPTCHA response with Google's verification API.
 *
 * @param string $captcha_response Visitor reCAPTCHA response.
 */
function webbooks_verify_recaptcha( string $captcha_response ): bool {
	$secret_key = webbooks_get_recaptcha_secret_key();
	if ( empty( $secret_key ) || empty( $captcha_response ) ) {
		return false;
	}

	$ip      = webbooks_get_comment_request_ip();
	$request = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => $secret_key,
				'response' => $captcha_response,
				'remoteip' => $ip,
			),
		)
	);

	if ( is_wp_error( $request ) ) {
		return false;
	}

	$status_code = (int) wp_remote_retrieve_response_code( $request );
	if ( 200 !== $status_code ) {
		return false;
	}

	$payload = json_decode( (string) wp_remote_retrieve_body( $request ), true );

	return is_array( $payload ) && ! empty( $payload['success'] );
}
