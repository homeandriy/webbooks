<?php
/**
 * Comment submission security, rate limiting, and language filtering.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Comment;

use WP_Comment;
use Webbooks\Localization\Polylang;

/**
 * Secures public comment submission and applies per-language comment filtering.
 */
final class CommentSecurity {
	public const NONCE_ACTION  = 'webbooks_comment_submit';
	public const NONCE_NAME    = 'webbooks_comment_nonce';
	public const PRIVACY_FIELD = 'webbooks_comment_privacy';

	/** Register comment security hooks. */
	public static function register(): void {
		add_action( 'admin_notices', array( self::class, 'recaptchaAdminNotice' ) );
		add_filter( 'preprocess_comment', array( self::class, 'validate' ) );
		add_action( 'comment_post', array( self::class, 'markRateLimit' ), 10, 2 );
		add_filter( 'comments_array', array( self::class, 'filterByCurrentLanguage' ) );
		add_filter( 'get_comments_number', array( self::class, 'filterNumberByLanguage' ), 10, 2 );
	}

	/**
	 * Display a configuration notice when reCAPTCHA keys are unavailable.
	 */
	public static function recaptchaAdminNotice(): void {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) || self::isRecaptchaConfigured() ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Notice template escapes dynamic values.
		echo webbooks_render_template_part(
			'template-parts/admin/notice',
			array(
				'type'    => 'warning',
				'message' => __(
					'Comments are disabled. Please configure GC_V2_PUBLIC and GC_V2_SECRET constants in wp-config.php.',
					'webbooks'
				),
			)
		);
	}

	/**
	 * Get the configured reCAPTCHA site key.
	 */
	public static function getRecaptchaSiteKey(): string {
		$from_const = defined( 'GC_V2_PUBLIC' ) ? (string) constant( 'GC_V2_PUBLIC' ) : '';

		return (string) apply_filters( 'webbooks_recaptcha_site_key', trim( $from_const ) );
	}

	/**
	 * Get the configured reCAPTCHA secret key.
	 */
	private static function getRecaptchaSecretKey(): string {
		$from_const = defined( 'GC_V2_SECRET' ) ? (string) constant( 'GC_V2_SECRET' ) : '';

		return (string) apply_filters( 'webbooks_recaptcha_secret_key', trim( $from_const ) );
	}

	/**
	 * Determine whether reCAPTCHA is configured.
	 */
	public static function isRecaptchaConfigured(): bool {
		return '' !== self::getRecaptchaSiteKey() && '' !== self::getRecaptchaSecretKey();
	}


	/**
	 * Validate comment input before WordPress creates the comment.
	 *
	 * @param  array<string, mixed> $comment_data  Comment fields.
	 *
	 * @return array<string, mixed> Sanitized comment fields.
	 */
	public static function validate( array $comment_data ): array {
		if ( is_admin() ) {
			return $comment_data;
		}

		if ( ! self::isRecaptchaConfigured() ) {
			wp_die(
				esc_html__( 'Comments are currently disabled by site configuration.', 'webbooks' ),
				esc_html__( 'Comments unavailable', 'webbooks' ),
				array(
					'response'  => 503,
					'back_link' => true,
				)
			);
		}

		$nonce = sanitize_text_field( (string) filter_input( INPUT_POST, self::NONCE_NAME ) );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
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
			$privacy_accepted = sanitize_text_field( (string) filter_input( INPUT_POST, self::PRIVACY_FIELD ) );

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
		if ( ! self::verifyRecaptcha( $captcha_response ) ) {
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
			$ip    = self::getRequestIp();
			$email = sanitize_email( (string) ( $comment_data['comment_author_email'] ?? '' ) );

			if ( self::isRateLimited( $ip, $email ) ) {
				$cooldown = max( 1, (int) apply_filters( 'webbooks_comment_rate_limit_seconds', 30 ) );
				wp_die(
					esc_html(
						sprintf(
							// translators: %d: Seconds before next allowed comment.
							__(
								'Too many comments. Please wait %d seconds before posting again.',
								'webbooks'
							),
							$cooldown
						)
					),
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


	/**
	 * Persist a rate-limit marker after a guest comment is accepted.
	 *
	 * @param  int        $comment_id  Comment ID.
	 * @param  int|string $comment_approved  Comment approval status.
	 */
	public static function markRateLimit( int $comment_id, int|string $comment_approved ): void {
		if ( 0 === (int) $comment_approved || 'spam' === $comment_approved || 'trash' === $comment_approved ) {
			return;
		}

		$comment = get_comment( $comment_id );
		if ( ! $comment instanceof WP_Comment ) {
			return;
		}

		if ( 0 === (int) $comment->user_id ) {
			$ip       = self::getRequestIp();
			$email    = sanitize_email( (string) $comment->comment_author_email );
			$key      = self::rateLimitKey( $ip, $email );
			$cooldown = max( 1, (int) apply_filters( 'webbooks_comment_rate_limit_seconds', 30 ) );
			set_transient( $key, time(), $cooldown );
		}

		$lang = Polylang::currentLanguageSlug();
		if ( null !== $lang ) {
			add_comment_meta( $comment_id, 'webbooks_comment_lang', $lang, true );
		}
	}


	/**
	 * Keep only comments stored for the current Polylang language.
	 *
	 * @param  array<int, WP_Comment> $comments  Comments to filter.
	 *
	 * @return array<int, WP_Comment> Comments for the current language.
	 */
	public static function filterByCurrentLanguage( array $comments ): array {
		if ( is_admin() ) {
			return $comments;
		}

		$current_lang = Polylang::currentLanguageSlug();
		if ( null === $current_lang ) {
			return $comments;
		}

		return array_values(
			array_filter(
				$comments,
				static function ( mixed $comment ) use ( $current_lang ): bool {
					if ( ! $comment instanceof WP_Comment ) {
						return false;
					}

					$comment_lang = (string) get_comment_meta(
						(int) $comment->comment_ID,
						'webbooks_comment_lang',
						true
					);
					if ( '' === $comment_lang ) {
						return false;
					}

					return $comment_lang === $current_lang;
				}
			)
		);
	}


	/**
	 * Return the number of comments stored for the current Polylang language.
	 *
	 * @param  string|int $count  Existing comment count.
	 * @param  int        $post_id  Post ID.
	 *
	 * @return string|int Localized comment count.
	 */
	public static function filterNumberByLanguage( string|int $count, int $post_id ): string|int {
		if ( is_admin() ) {
			return $count;
		}

		$current_lang = Polylang::currentLanguageSlug();
		if ( null === $current_lang ) {
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
	 * @param  string $ip  Visitor IP address.
	 * @param  string $email  Visitor email address.
	 */
	private static function isRateLimited( string $ip, string $email ): bool {
		$key = self::rateLimitKey( $ip, $email );

		return get_transient( $key ) !== false;
	}

	/**
	 * Build the transient key used for comment rate limiting.
	 *
	 * @param  string $ip  Visitor IP address.
	 * @param  string $email  Visitor email address.
	 */
	private static function rateLimitKey( string $ip, string $email ): string {
		return 'webbooks_comment_rl_' . md5( strtolower( trim( $ip ) ) . '|' . strtolower( trim( $email ) ) );
	}

	/**
	 * Get and sanitize the visitor IP address.
	 */
	private static function getRequestIp(): string {
		return sanitize_text_field( wp_unslash( (string) ( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' ) ) );
	}

	/**
	 * Verify the visitor reCAPTCHA response with Google's verification API.
	 *
	 * @param  string $captcha_response  Visitor reCAPTCHA response.
	 */
	private static function verifyRecaptcha( string $captcha_response ): bool {
		$secret_key = self::getRecaptchaSecretKey();
		if ( empty( $secret_key ) || empty( $captcha_response ) ) {
			return false;
		}

		$ip      = self::getRequestIp();
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
}
