<?php

add_action( 'wp_ajax_theme_post_example', 'theme_post_example_init' );
add_action( 'wp_ajax_nopriv_theme_post_example', 'theme_post_example_init' );
function theme_post_example_init(): void {
	$nonce = webbooks_ajax_text( $_POST['nonce'] ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$post_id = absint( webbooks_ajax_text( $_POST['id'] ?? '' ) );
	if ( ! \Webbooks\Statistics\Views::isPublic( $post_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid post ID.', 'webbooks' ) ), 400 );
	}

	$theme_post_query = new WP_Query( array( 'p' => $post_id, 'post_status' => 'publish', 'has_password' => false ) );
	ob_start();
	while ( $theme_post_query->have_posts() ) :
		$theme_post_query->the_post(); ?>
		<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title mCustomScrollbar" id="myModalLabel"><?php the_title(); ?></h4></div>
		<div class="modal-body" style="height:400px; overflow-y:scroll;" data-mcs-theme="dark"><?php the_content(); ?></div>
		<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e( 'Close', 'webbooks' ); ?></button><a href="<?php the_permalink(); ?>" type="button" class="btn btn-primary"><?php esc_html_e( 'Read full', 'webbooks' ); ?></a></div>
		<?php
	endwhile;
	$output = ob_get_clean();
	wp_reset_postdata();
	wp_send_json_success( array( 'html' => $output ) );
}

function webbooks_ajax_text( mixed $value ): string {
	return is_scalar( $value ) ? sanitize_text_field( wp_unslash( (string) $value ) ) : '';
}

add_action( 'wp_ajax_main_search_on_site', 'main_search_on_site' );
add_action( 'wp_ajax_nopriv_main_search_on_site', 'main_search_on_site' );
function webbooks_get_ajax_var_payload(): array {
	$raw_param = $_POST['var'] ?? null;
	if ( is_array( $raw_param ) ) {
		return wp_unslash( $raw_param );
	}

	if ( is_string( $raw_param ) && $raw_param !== '' && strlen( $raw_param ) <= 4096 ) {
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
	$nonce = webbooks_ajax_text( $_POST['nonce'] ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$param = webbooks_get_ajax_var_payload();
	$paged = isset( $param['paged'] ) && is_scalar( $param['paged'] ) ? max( 1, (int) $param['paged'] ) : 1;
	$total = 0;
	$html = category_query(
		webbooks_ajax_text( $param['category'] ?? '' ),
		webbooks_ajax_text( $param['statusbook'] ?? '' ),
		webbooks_ajax_text( $param['language'] ?? '' ),
		! empty( $param['selectToLink'] ),
		$paged,
		$total
	);
	wp_send_json_success( array( 'html' => $html, 'total' => $total ) );
}

function category_query( string $cat, string $statusbook, string $language, bool $selectToLink, int $paged = 1, ?int &$total = null ): string {
	$current_lang = '';
	if ( function_exists( 'pll_current_language' ) ) {
		$request_lang = isset( $_REQUEST['lang'] ) && is_string( $_REQUEST['lang'] ) ? sanitize_key( wp_unslash( $_REQUEST['lang'] ) ) : '';
		$current_lang = $request_lang !== '' ? $request_lang : (string) pll_current_language( 'slug' );
	}
	$service = new \Webbooks\Search\SearchService();
	$query = $service->filter( $cat, $statusbook, $language, $paged, $current_lang );
	$total = (int) $query->found_posts;
	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/cards/book-card', null, array( 'selectToLink' => $selectToLink, 'book' => get_post() ) );
		}
		echo webbooks_render_ajax_pagination( $query->max_num_pages, $paged, 'main_search_on_site' );
	} else {
		echo '<h2>' . esc_html__( 'No results found for the selected criteria.', 'webbooks' ) . '</h2>';
	}
	wp_reset_postdata();

	$output = ob_get_clean();
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
	$nonce = webbooks_ajax_text( $_POST['nonce'] ?? '' );
	if ( ! wp_verify_nonce( $nonce, WEBBOOKS_AJAX_NONCE ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid security token.', 'webbooks' ) ), 403 );
	}

	$post_param  = webbooks_get_ajax_var_payload();
	$search_term = trim( webbooks_ajax_text( $post_param['StrTosearch'] ?? '' ) );

	$minimum_length = ! empty( $post_param['explicit'] ) ? 1 : 4;
	if ( webbooks_str_len( $search_term ) < $minimum_length || webbooks_str_len( $search_term ) > 200 ) {
		wp_send_json_success( array( 'html' => '' ) );
	}

	$current_lang = '';
	if ( function_exists( 'pll_current_language' ) ) {
		$request_lang = isset( $_REQUEST['lang'] ) && is_string( $_REQUEST['lang'] ) ? sanitize_key( wp_unslash( $_REQUEST['lang'] ) ) : '';
		$current_lang = $request_lang !== '' ? $request_lang : (string) pll_current_language( 'slug' );
	}
	$service = new \Webbooks\Search\SearchService();
	$results = $service->search( $search_term, $current_lang );
	ob_start();
	get_template_part( 'template-parts/search-results', null, $results );
	wp_send_json_success( array( 'html' => ob_get_clean(), 'total' => (int) $results['total'] ) );
}
