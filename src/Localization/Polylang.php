<?php
/**
 * Polylang integration helpers.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Localization;

use WP_Post;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Provides a safe, optional integration layer for Polylang.
 */
final class Polylang {

	/** Register Polylang integration hooks. */
	public static function register(): void {
		add_action( 'rest_api_init', array( self::class, 'registerRestRoutes' ) );
	}

	/** Register authenticated REST endpoints used by publication integrations. */
	public static function registerRestRoutes(): void {
		register_rest_route(
			'webbooks-ai/v1',
			'/polylang/translations',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( self::class, 'saveTranslationGroup' ),
				'permission_callback' => static fn(): bool => current_user_can( 'edit_posts' ),
				'args'                => array(
					'translations' => array(
						'required' => true,
						'type'     => 'object',
					),
				),
			)
		);
	}

	/**
	 * Assign languages and save a complete Polylang translation group.
	 *
	 * Polylang Free exposes these operations as PHP functions but not through
	 * the WordPress REST post endpoints. The bridge remains authenticated and
	 * checks edit capability for every supplied post.
	 *
	 * @param WP_REST_Request $request Authenticated REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public static function saveTranslationGroup( WP_REST_Request $request ): WP_REST_Response|WP_Error {
		if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
			return new WP_Error(
				'webbooks_polylang_unavailable',
				__( 'Polylang is not available for translation synchronization.', 'webbooks' ),
				array( 'status' => 501 )
			);
		}

		$translations = $request->get_param( 'translations' );
		if ( ! is_array( $translations ) || array_is_list( $translations ) || array() === $translations ) {
			return new WP_Error(
				'webbooks_invalid_translation_group',
				__( 'Translations must be a non-empty language-to-post-ID map.', 'webbooks' ),
				array( 'status' => 400 )
			);
		}

		$available_languages = self::languageSlugs();
		$normalized          = array();
		foreach ( $translations as $language => $post_id ) {
			$language = sanitize_key( (string) $language );
			$post_id  = absint( $post_id );
			$post     = get_post( $post_id );

			if ( '' === $language || ! in_array( $language, $available_languages, true ) || ! $post instanceof WP_Post || ! current_user_can( 'edit_post', $post_id ) ) {
				return new WP_Error(
					'webbooks_invalid_translation',
					__( 'A translation language or post is invalid, unavailable, or cannot be edited.', 'webbooks' ),
					array( 'status' => 400 )
				);
			}

			$normalized[ $language ] = $post_id;
		}

		foreach ( $normalized as $language => $post_id ) {
			pll_set_post_language( $post_id, $language );
		}
		pll_save_post_translations( $normalized );

		return rest_ensure_response(
			array(
				'translations' => $normalized,
			)
		);
	}

	/**
	 * Get the current Polylang language slug.
	 *
	 * @return string|null Current language slug, or null when Polylang is unavailable.
	 */
	public static function currentLanguageSlug(): ?string {
		if ( ! function_exists( 'pll_current_language' ) ) {
			return null;
		}

		$language = pll_current_language( 'slug' );

		return is_string( $language ) && '' !== $language ? sanitize_key( $language ) : null;
	}

	/**
	 * Get the current Polylang locale.
	 *
	 * @return string|null Current locale, or null when Polylang is unavailable.
	 */
	public static function currentLocale(): ?string {
		if ( ! function_exists( 'pll_current_language' ) ) {
			return null;
		}

		$locale = pll_current_language( 'locale' );

		return is_string( $locale ) && '' !== $locale ? str_replace( '-', '_', sanitize_text_field( $locale ) ) : null;
	}

	/**
	 * Get configured Polylang language slugs.
	 *
	 * @return string[] Language slugs.
	 */
	public static function languageSlugs(): array {
		if ( ! function_exists( 'pll_languages_list' ) ) {
			return array();
		}

		$languages = pll_languages_list( array( 'fields' => 'slug' ) );
		if ( ! is_array( $languages ) ) {
			return array();
		}

		return array_values(
			array_filter(
				array_map( 'sanitize_key', $languages )
			)
		);
	}

	/**
	 * Resolve an optional request language to a configured language slug.
	 *
	 * Invalid values always fall back to the current language. This prevents an
	 * arbitrary request value from affecting translations or custom queries.
	 *
	 * @param string|null $requested_language Requested language slug.
	 * @return string|null Valid language slug, or null when Polylang is unavailable.
	 */
	public static function resolveLanguageSlug( ?string $requested_language = null ): ?string {
		$current_language = self::currentLanguageSlug();
		if ( null === $requested_language || '' === $requested_language ) {
			return $current_language;
		}

		$requested_language = sanitize_key( $requested_language );

		return in_array( $requested_language, self::languageSlugs(), true ) ? $requested_language : $current_language;
	}

	/**
	 * Add the current language to custom query arguments.
	 *
	 * @param array<string, mixed> $args WP_Query arguments.
	 * @param string|null          $language Optional requested language slug.
	 * @return array<string, mixed> Language-scoped query arguments.
	 */
	public static function withLanguageQueryArg( array $args, ?string $language = null ): array {
		$language = self::resolveLanguageSlug( $language );
		if ( null !== $language ) {
			$args['lang'] = $language;
		}

		return $args;
	}

	/**
	 * Return a translated post ID, falling back to the source post ID.
	 *
	 * @param int         $post_id Source post ID.
	 * @param string|null $language Target language slug.
	 * @return int Translated or source post ID.
	 */
	public static function translatedPostId( int $post_id, ?string $language = null ): int {
		if ( $post_id <= 0 || ! function_exists( 'pll_get_post' ) ) {
			return $post_id;
		}

		$language           = self::resolveLanguageSlug( $language );
		$translated_post_id = null === $language
			? (int) pll_get_post( $post_id )
			: (int) pll_get_post( $post_id, $language );

		return $translated_post_id > 0 ? $translated_post_id : $post_id;
	}

	/**
	 * Return a permalink for a post in the current language.
	 *
	 * @param int         $post_id Source post ID.
	 * @param string|null $language Target language slug.
	 * @return string Permalink.
	 */
	public static function translatedPostUrl( int $post_id, ?string $language = null ): string {
		return (string) get_permalink( self::translatedPostId( $post_id, $language ) );
	}

	/**
	 * Return a translated term ID, falling back to the source term ID.
	 *
	 * @param int         $term_id Source term ID.
	 * @param string|null $language Target language slug.
	 * @return int Translated or source term ID.
	 */
	public static function translatedTermId( int $term_id, ?string $language = null ): int {
		if ( $term_id <= 0 || ! function_exists( 'pll_get_term' ) ) {
			return $term_id;
		}

		$language           = self::resolveLanguageSlug( $language );
		$translated_term_id = null === $language
			? (int) pll_get_term( $term_id )
			: (int) pll_get_term( $term_id, $language );

		return $translated_term_id > 0 ? $translated_term_id : $term_id;
	}

	/**
	 * Get the current-language home URL.
	 *
	 * @param string|null $language Target language slug.
	 * @return string Home URL.
	 */
	public static function homeUrl( ?string $language = null ): string {
		if ( function_exists( 'pll_home_url' ) ) {
			$language = self::resolveLanguageSlug( $language );

			return null === $language ? (string) pll_home_url() : (string) pll_home_url( $language );
		}

		return home_url( '/' );
	}

	/**
	 * Get a special page URL in the current language.
	 *
	 * @param string $path Source page path.
	 * @return string Current-language page URL, or a language home-relative fallback.
	 */
	public static function pageUrl( string $path ): string {
		$page = get_page_by_path( trim( $path, '/' ), OBJECT, 'page' );

		if ( $page instanceof WP_Post ) {
			return self::translatedPostUrl( $page->ID );
		}

		return trailingslashit( self::homeUrl() ) . ltrim( $path, '/' );
	}
}
