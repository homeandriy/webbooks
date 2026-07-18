<?php
/**
 * AJAX handlers for book previews, catalog filtering, and site search.
 *
 * @package Webbooks
 */

declare(strict_types=1);

namespace Webbooks\Ajax;

use WP_Post;
use WP_Query;
use Webbooks\Localization\Polylang;

if ( ! extension_loaded( 'mbstring' ) ) {
	throw new \RuntimeException( 'The Webbooks theme requires the PHP mbstring extension. Install and enable it before loading the theme.' );
}

/**
 * Handles catalog, preview, search, and view-count AJAX requests.
 */
final class SearchController {

	/**
	 * Register WordPress AJAX actions.
	 */
	public static function register(): void {
		add_action( 'wp_ajax_theme_post_example', array( self::class, 'postPreview' ) );
		add_action( 'wp_ajax_nopriv_theme_post_example', array( self::class, 'postPreview' ) );
		add_action( 'wp_ajax_main_search_on_site', array( self::class, 'catalogSearch' ) );
		add_action( 'wp_ajax_nopriv_main_search_on_site', array( self::class, 'catalogSearch' ) );
		add_action( 'wp_ajax_global_search', array( self::class, 'globalSearch' ) );
		add_action( 'wp_ajax_nopriv_global_search', array( self::class, 'globalSearch' ) );
		add_action( 'wp_ajax_postview_count_set', array( self::class, 'incrementPostViewCount' ) );
		add_action( 'wp_ajax_nopriv_postview_count_set', array( self::class, 'incrementPostViewCount' ) );
		add_action( 'wp_ajax_postview_count_get', array( self::class, 'getPostViewCount' ) );
		add_action( 'wp_ajax_nopriv_postview_count_get', array( self::class, 'getPostViewCount' ) );
	}

	/**
	 * Return rendered modal content for a public post.
	 */
	public static function postPreview(): void {
		$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
		}

		$post_id = absint( filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT ) );
		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
		}

		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
			wp_send_json_error( array( 'message' => esc_html__( 'The requested post is unavailable.', 'webbooks' ) ), 404 );
		}

		$theme_post_query = new WP_Query(
			Polylang::withLanguageQueryArg(
				array(
					'p'           => $post_id,
					'post_status' => 'publish',
				)
			)
		);
		$output           = '';
		while ( $theme_post_query->have_posts() ) {
			$theme_post_query->the_post();
			$output .= webbooks_render_template_part( 'template-parts/ajax/post-preview-modal' );
		}
		wp_reset_postdata();
		wp_send_json_success( array( 'html' => $output ) );
	}

	/**
	 * Decode the JSON filter payload submitted by the AJAX client.
	 *
	 * @return array<string, mixed> Filter values.
	 */
	private static function getAjaxPayload(): array {
		$raw_param = filter_input( INPUT_POST, 'var', FILTER_DEFAULT );
		if ( is_array( $raw_param ) ) {
			return $raw_param;
		}

		if ( is_string( $raw_param ) && '' !== $raw_param ) {
			$decoded = json_decode( wp_unslash( $raw_param ), true );
			if ( is_array( $decoded ) ) {
				return $decoded;
			}
		}

		return array();
	}

	/**
	 * Return catalog filter results as AJAX markup.
	 */
	public static function catalogSearch(): void {
		$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
		}

		$param = self::getAjaxPayload();
		$paged = isset( $param['paged'] ) ? max( 1, (int) $param['paged'] ) : 1;
		wp_send_json_success(
			array(
				'html' => self::renderCatalog(
					sanitize_text_field( $param['category'] ?? '' ),
					sanitize_text_field( $param['statusbook'] ?? '' ),
					sanitize_text_field( $param['language'] ?? '' ),
					! empty( $param['selectToLink'] ),
					$paged
				),
			)
		);
	}

	/**
	 * Render filtered and paginated book cards.
	 *
	 * @param string $cat            Category slug.
	 * @param string $statusbook     Complexity filter.
	 * @param string $language       Language filter.
	 * @param bool   $select_to_link Whether cards should show download links.
	 * @param int    $paged          Current page number.
	 * @return string Rendered catalog markup.
	 */
	private static function renderCatalog( string $cat, string $statusbook, string $language, bool $select_to_link, int $paged = 1 ): string {
		$complexity_enum = \Webbooks\Domain\Book\Complexity::fromNullable( $statusbook );
		$language_enum   = \Webbooks\Domain\Book\Language::fromNullable( $language );
		$request_lang    = filter_input( INPUT_POST, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_lang    = Polylang::resolveLanguageSlug( is_string( $request_lang ) ? $request_lang : null );

		if ( null !== $current_lang ) {
			\Webbooks\Theme\Setup::loadI18nForLocale( $current_lang );
		}

		$args = array(
			'posts_per_page'      => 12,
			'paged'               => $paged,
			'post_status'         => 'publish',
			'orderby'             => 'comment_count',
			'category_name'       => $cat,
			'ignore_sticky_posts' => true,
		);

		// Only add meta_query conditions for filters that were explicitly set.
		$meta_conditions = array( 'relation' => 'AND' );
		if ( '' !== $statusbook ) {
			$meta_conditions[] = array(
				'key'     => 'complexity',
				'value'   => $complexity_enum?->value ?? $statusbook,
				'compare' => '=',
			);
		}
		if ( '' !== $language ) {
			$meta_conditions[] = array(
				'key'     => 'language',
				'value'   => $language_enum?->value ?? $language,
				'compare' => '=',
			);
		}
		if ( count( $meta_conditions ) > 1 ) {
			// The explicitly selected catalog filters are stored as post meta.
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			$args['meta_query'] = $meta_conditions;
		}

		$args = Polylang::withLanguageQueryArg( $args, $current_lang );

		$cache_key     = 'webbooks_cat_query_' . md5(
			wp_json_encode(
				array(
					'category'     => $cat,
					'complexity'   => $statusbook,
					'language'     => $language,
					'site_lang'    => $current_lang,
					'page'         => $paged,
					'selectToLink' => $select_to_link,
				)
			)
		);
		$cached_output = get_transient( $cache_key );
		if ( false !== $cached_output ) {
			return (string) $cached_output;
		}

		$query = new WP_Query( $args );
		ob_start();
		if ( $query->have_posts() ) {
			echo '<div class="row">';
			while ( $query->have_posts() ) {
				$query->the_post();
				get_template_part( 'template-parts/cards/book-card', null, array( 'selectToLink' => $select_to_link ) );
			}
			echo '</div>';
			echo wp_kses_post( self::renderPagination( $query->max_num_pages, $paged, 'main_search_on_site' ) );
		} else {
			echo wp_kses_post( webbooks_render_template_part( 'template-parts/ajax/no-results' ) );
		}
		wp_reset_postdata();

		$output = ob_get_clean();
		set_transient( $cache_key, $output, 15 * MINUTE_IN_SECONDS );
		return $output;
	}

	/**
	 * Identify book records by their downloadable file format.
	 *
	 * Article records can also have an author meta field, so that field is not a
	 * reliable type discriminator.
	 *
	 * @param int $post_id Post ID.
	 * @return bool Whether the post represents a downloadable book.
	 */
	private static function isBookPost( int $post_id ): bool {
		$format = strtolower( trim( (string) get_post_meta( $post_id, 'format', true ) ) );

		return in_array( $format, array( 'azw', 'azw3', 'djvu', 'doc', 'docx', 'epub', 'fb2', 'mobi', 'pdf' ), true );
	}

	/**
	 * Render safe pagination markup for an AJAX result set.
	 *
	 * @param int    $max_pages    Total number of result pages.
	 * @param int    $current_page Current page number.
	 * @param string $action       AJAX action name.
	 * @return string Pagination markup.
	 */
	private static function renderPagination( int $max_pages, int $current_page, string $action = 'main_search_on_site' ): string {
		if ( $max_pages <= 1 ) {
			return '';
		}
		$pagination_links = paginate_links(
			array(
				'base'      => '#page=%#%',
				'format'    => '',
				'current'   => $current_page,
				'total'     => $max_pages,
				'type'      => 'array',
				'prev_next' => true,
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
			)
		);
		if ( empty( $pagination_links ) || ! is_array( $pagination_links ) ) {
			return '';
		}

		$output = '<nav class="ajax-pagination-wrap"><ul class="pagination ajax-pagination">';
		foreach ( $pagination_links as $link ) {
			$is_current  = strpos( $link, 'current' ) !== false;
			$page        = preg_match( '/page=([0-9]+)/', $link, $matches ) ? (int) $matches[1] : 0;
			$replacement = 'href="#" data-page="' . $page . '" data-ajax-action="' . esc_attr( $action ) . '"';
			$link        = str_replace( 'href=\'#page=' . $page . '\'', $replacement, $link );
			$link        = str_replace( 'href="#page=' . $page . '"', $replacement, $link );
			$output     .= $is_current ? '<li class="active">' . $link . '</li>' : '<li>' . $link . '</li>';
		}

		return $output . '</ul></nav>';
	}

	/**
	 * Return grouped book and article search results as AJAX markup.
	 */
	public static function globalSearch(): void {
		$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
		}

		$post_param  = self::getAjaxPayload();
		$search_term = trim( sanitize_text_field( $post_param['StrTosearch'] ?? '' ) );

		if ( mb_strlen( $search_term, 'UTF-8' ) < 4 ) {
			wp_send_json_success( array( 'html' => '' ) );
		}

		$max_results_per_group = 12;
		$request_lang          = filter_input( INPUT_POST, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$current_lang          = Polylang::resolveLanguageSlug( is_string( $request_lang ) ? $request_lang : null );
		if ( null !== $current_lang ) {
			\Webbooks\Theme\Setup::loadI18nForLocale( $current_lang );
		}
		$query_args      = array(
			'post_type'           => array( 'post' ),
			'posts_per_page'      => 80,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			's'                   => $search_term,
			'orderby'             => 'relevance',
			'no_found_rows'       => true,
		);
		$query_args      = Polylang::withLanguageQueryArg( $query_args, $current_lang );
		$all_posts_query = new WP_Query( $query_args );

		$books    = array();
		$articles = array();
		$needle   = mb_strtolower( $search_term, 'UTF-8' );

		if ( $all_posts_query->have_posts() ) {
			while ( $all_posts_query->have_posts() ) {
				$all_posts_query->the_post();
				$post_id            = get_the_ID();
				$title              = (string) get_the_title();
				$content            = (string) get_the_content( null, false, $post_id );
				$book_author        = (string) get_post_meta( $post_id, 'autor', true );
				$normalized_title   = mb_strtolower( $title, 'UTF-8' );
				$normalized_author  = mb_strtolower( $book_author, 'UTF-8' );
				$normalized_content = mb_strtolower( wp_strip_all_tags( $content ), 'UTF-8' );
				$is_book            = self::isBookPost( $post_id );

				if ( $is_book ) {
					if ( false !== mb_strpos( $normalized_title, $needle, 0, 'UTF-8' ) || false !== mb_strpos( $normalized_author, $needle, 0, 'UTF-8' ) ) {
						$books[] = array(
							'id'        => $post_id,
							'title'     => $title,
							'permalink' => (string) get_permalink( $post_id ),
							'author'    => $book_author,
							'publisher' => trim( (string) get_post_meta( $post_id, 'create', true ) ),
							'language'  => \Webbooks\Book\BookMeta::getLanguage( function_exists( 'get_field' ) ? trim( (string) get_field( 'language', $post_id ) ) : '' ),
							'thumbnail' => (string) ( get_the_post_thumbnail_url( $post_id, 'thumbnail' ) ? get_the_post_thumbnail_url( $post_id, 'thumbnail' ) : get_template_directory_uri() . '/screenshot.png' ),
						);
					}
					continue;
				}

				if ( false !== mb_strpos( $normalized_title, $needle, 0, 'UTF-8' ) || false !== mb_strpos( $normalized_content, $needle, 0, 'UTF-8' ) ) {
					$articles[] = array(
						'id'        => $post_id,
						'title'     => $title,
						'permalink' => (string) get_permalink( $post_id ),
						'thumbnail' => (string) ( get_the_post_thumbnail_url( $post_id, 'thumbnail' ) ? get_the_post_thumbnail_url( $post_id, 'thumbnail' ) : get_template_directory_uri() . '/screenshot.png' ),
					);
				}
			}
			wp_reset_postdata();
		}

		$books    = array_slice( $books, 0, $max_results_per_group );
		$articles = array_slice( $articles, 0, $max_results_per_group );
		$total    = count( $books ) + count( $articles );

		wp_send_json_success(
			array(
				'html' => webbooks_render_template_part(
					'template-parts/ajax/global-search-results',
					array(
						'books'    => $books,
						'articles' => $articles,
						'total'    => $total,
					)
				),
			)
		);
	}

	/**
	 * Increment and return a public post view count.
	 */
	public static function incrementPostViewCount(): void {
		$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
		}

		$id_p = absint( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
		if ( ! $id_p ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
		}
		$post = get_post( $id_p );
		if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
			wp_send_json_error( array( 'message' => esc_html__( 'The requested post is unavailable.', 'webbooks' ) ), 404 );
		}

		$views_count = (int) get_post_meta( $id_p, '_views_count', true ) + 1;
		update_post_meta( $id_p, '_views_count', $views_count );
		wp_send_json_success( array( 'count' => $views_count ) );
	}

	/**
	 * Return a public post view count.
	 */
	public static function getPostViewCount(): void {
		$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
		if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
		}

		$post_id = absint( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
		}
		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
			wp_send_json_error( array( 'message' => esc_html__( 'The requested post is unavailable.', 'webbooks' ) ), 404 );
		}

		wp_send_json_success( array( 'count' => (int) get_post_meta( $post_id, '_views_count', true ) ) );
	}
}
