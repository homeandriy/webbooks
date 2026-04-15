<?php

add_action( 'wp_ajax_theme_post_example', 'theme_post_example_init' );
add_action( 'wp_ajax_nopriv_theme_post_example', 'theme_post_example_init' );
function theme_post_example_init(): void {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$post_id = absint( filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT ) );
	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
	}

	$theme_post_query = new WP_Query( array( 'p' => $post_id ) );
	ob_start();
	while ( $theme_post_query->have_posts() ) :
		$theme_post_query->the_post(); ?>
		<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title mCustomScrollbar" id="myModalLabel"><?php the_title(); ?></h4></div>
		<div class="modal-body" style="height:400px; overflow-y:scroll;" data-mcs-theme="dark"><?php the_content(); ?></div>
		<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e( 'Close', 'webbooks' ); ?></button><a href="<?php the_permalink(); ?>" type="button" class="btn btn-primary"><?php esc_html_e( 'Read full', 'webbooks' ); ?></a></div>
		<?php
	endwhile;
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

add_action( 'wp_ajax_main_search_on_site', 'main_search_on_site' );
add_action( 'wp_ajax_nopriv_main_search_on_site', 'main_search_on_site' );
function webbooks_get_ajax_var_payload(): array {
	$raw_param = filter_input( INPUT_POST, 'var', FILTER_DEFAULT );
	if ( is_array( $raw_param ) ) {
		return $raw_param;
	}

	if ( is_string( $raw_param ) && $raw_param !== '' ) {
		$decoded = json_decode( wp_unslash( $raw_param ), true );
		if ( is_array( $decoded ) ) {
			return $decoded;
		}
	}

	return array();
}

function webbooks_str_to_lower( string $value ): string {
	if ( function_exists( 'mb_strtolower' ) ) {
		return (string) mb_strtolower( $value );
	}

	return strtolower( $value );
}

function webbooks_str_pos( string $haystack, string $needle ) {
	if ( function_exists( 'mb_strpos' ) ) {
		return mb_strpos( $haystack, $needle );
	}

	return strpos( $haystack, $needle );
}

function webbooks_str_len( string $value ): int {
	if ( function_exists( 'mb_strlen' ) ) {
		return (int) mb_strlen( $value );
	}

	return strlen( $value );
}

function main_search_on_site(): void {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$param = webbooks_get_ajax_var_payload();
	$paged = isset( $param['paged'] ) ? max( 1, (int) $param['paged'] ) : 1;
	wp_send_json_success(
		array(
			'html' => category_query(
				sanitize_text_field( $param['category'] ?? '' ),
				sanitize_text_field( $param['statusbook'] ?? '' ),
				sanitize_text_field( $param['language'] ?? '' ),
				! empty( $param['selectToLink'] ),
				$paged
			),
		)
	);
}

function category_query( string $cat, string $statusbook, string $language, bool $selectToLink, int $paged = 1 ): string {
	$complexity_enum = \Domain\Book\Complexity::fromNullable( $statusbook );
	$language_enum   = \Domain\Book\Language::fromNullable( $language );
	$current_lang    = '';
	if ( function_exists( 'pll_current_language' ) ) {
		$request_lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : '';
		$current_lang = '' !== $request_lang ? $request_lang : pll_current_language( 'slug' );
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
	if ( $statusbook !== '' ) {
		$meta_conditions[] = array(
			'key'     => 'complexity',
			'value'   => $complexity_enum?->value ?? $statusbook,
			'compare' => '=',
		);
	}
	if ( $language !== '' ) {
		$meta_conditions[] = array(
			'key'     => 'language',
			'value'   => $language_enum?->value ?? $language,
			'compare' => '=',
		);
	}
	if ( count( $meta_conditions ) > 1 ) {
		$args['meta_query'] = $meta_conditions;
	}

	if ( ! empty( $current_lang ) ) {
		$args['lang'] = $current_lang;
	}

	$cache_key     = 'webbooks_cat_query_' . md5(
		wp_json_encode(
			array(
				'category'     => $cat,
				'complexity'   => $statusbook,
				'language'     => $language,
				'site_lang'    => $current_lang,
				'page'         => $paged,
				'selectToLink' => $selectToLink,
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
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/cards/book-card', null, array( 'selectToLink' => $selectToLink ) );
		}
		echo webbooks_render_ajax_pagination( $query->max_num_pages, $paged, 'main_search_on_site' );
	} else {
		echo '<h2>' . esc_html__( 'No results found for the selected criteria.', 'webbooks' ) . '</h2>';
	}
	wp_reset_postdata();

	$output = ob_get_clean();
	set_transient( $cache_key, $output, 15 * MINUTE_IN_SECONDS );
	return $output;
}

function webbooks_render_ajax_pagination( int $max_pages, int $current_page, string $action = 'main_search_on_site' ): string {
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

add_action( 'wp_ajax_global_search', 'global_search_int' );
add_action( 'wp_ajax_nopriv_global_search', 'global_search_int' );
function global_search_int(): void {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$post_param  = webbooks_get_ajax_var_payload();
	$search_term = trim( sanitize_text_field( $post_param['StrTosearch'] ?? '' ) );

	if ( webbooks_str_len( $search_term ) < 4 ) {
		wp_send_json_success( array( 'html' => '' ) );
	}

	$max_results_per_group = 12;
	$current_lang          = '';
	if ( function_exists( 'pll_current_language' ) ) {
		$request_lang = isset( $_REQUEST['lang'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['lang'] ) ) : '';
		$current_lang = '' !== $request_lang ? $request_lang : pll_current_language( 'slug' );
	}
	$query_args            = array(
		'post_type'           => array( 'post' ),
		'posts_per_page'      => 80,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		's'                   => $search_term,
		'orderby'             => 'relevance',
		'no_found_rows'       => true,
	);
	if ( ! empty( $current_lang ) ) {
		$query_args['lang'] = $current_lang;
	}
	$all_posts_query = new WP_Query( $query_args );

	$books    = array();
	$articles = array();
	$needle   = webbooks_str_to_lower( $search_term );

	if ( $all_posts_query->have_posts() ) {
		while ( $all_posts_query->have_posts() ) {
			$all_posts_query->the_post();
			$post_id            = get_the_ID();
			$title              = (string) get_the_title();
			$content            = (string) get_the_content( null, false, $post_id );
			$book_author        = (string) get_post_meta( $post_id, 'autor', true );
			$normalized_title   = webbooks_str_to_lower( $title );
			$normalized_author  = webbooks_str_to_lower( $book_author );
			$normalized_content = webbooks_str_to_lower( wp_strip_all_tags( $content ) );
			$is_book            = $book_author !== '';

			if ( $is_book ) {
				if ( false !== webbooks_str_pos( $normalized_title, $needle ) || false !== webbooks_str_pos( $normalized_author, $needle ) ) {
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

			if ( false !== webbooks_str_pos( $normalized_title, $needle ) || false !== webbooks_str_pos( $normalized_content, $needle ) ) {
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

	ob_start();
	?>
	<div class="search-results-total">
		<?php esc_html_e( 'Found results:', 'webbooks' ); ?> <?php echo (int) $total; ?>
	</div>
	<?php if ( ! empty( $books ) ) : ?>
		<div class="search-section-heading"><strong><?php esc_html_e( 'Books', 'webbooks' ); ?></strong></div>
		<?php foreach ( $books as $book_item ) : ?>
			<a class="search-result-item search-result-item--book" href="<?php echo esc_url( $book_item['permalink'] ); ?>">
				<span class="search-result-item__media"><img src="<?php echo esc_url( $book_item['thumbnail'] ); ?>" alt="<?php echo esc_attr( $book_item['title'] ); ?>" loading="lazy"></span>
				<span class="search-result-item__content">
					<span class="search-result-item__title"><?php echo esc_html( $book_item['title'] ); ?></span>
					<span class="search-result-item__meta">
						<span><?php echo esc_html__( 'Author:', 'webbooks' ); ?> <?php echo esc_html( $book_item['author'] ); ?></span>
						<span><?php echo esc_html__( 'Publisher:', 'webbooks' ); ?> <?php echo esc_html( $book_item['publisher'] ); ?></span>
						<span><?php echo esc_html__( 'Language:', 'webbooks' ); ?> <?php echo esc_html( $book_item['language'] ); ?></span>
					</span>
				</span>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( ! empty( $articles ) ) : ?>
		<div class="search-section-heading"><strong><?php esc_html_e( 'Blog articles', 'webbooks' ); ?></strong></div>
		<?php foreach ( $articles as $article_item ) : ?>
			<a class="search-result-item search-result-item--article" href="<?php echo esc_url( $article_item['permalink'] ); ?>">
				<span class="search-result-item__media"><img src="<?php echo esc_url( $article_item['thumbnail'] ); ?>" alt="<?php echo esc_attr( $article_item['title'] ); ?>" loading="lazy"></span>
				<span class="search-result-item__content">
					<span class="search-result-item__title"><?php echo esc_html( $article_item['title'] ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	<?php endif; ?>

	<?php if ( $total === 0 ) : ?>
		<div class="search-result-empty"><?php esc_html_e( 'Nothing found', 'webbooks' ); ?></div>
	<?php endif; ?>
	<?php
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}

function set_post_count_view(): void {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$id_p = absint( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
	if ( ! $id_p ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
	}

	$views_count = (int) get_post_meta( $id_p, '_views_count', true ) + 1;
	update_post_meta( $id_p, '_views_count', $views_count );
	wp_send_json_success( array( 'count' => $views_count ) );
}

function get_count_post_view(): void {
	$nonce = sanitize_text_field( filter_input( INPUT_POST, 'nonce' ) ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$post_id = absint( filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT ) );
	if ( ! $post_id ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
	}

	wp_send_json_success( array( 'count' => (int) get_post_meta( $post_id, '_views_count', true ) ) );
}

add_action( 'wp_ajax_postview_count_set', 'set_post_count_view' );
add_action( 'wp_ajax_nopriv_postview_count_set', 'set_post_count_view' );
add_action( 'wp_ajax_postview_count_get', 'get_count_post_view' );
add_action( 'wp_ajax_nopriv_postview_count_get', 'get_count_post_view' );
