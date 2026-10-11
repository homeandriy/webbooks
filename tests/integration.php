<?php
/** Run only in the isolated database created by tests/compose.yml. */
if ( DB_NAME !== 'webbooks_tests' || ! defined( 'WEBBOOKS_TEST_RUNNER' ) ) {
	throw new RuntimeException( 'Use the isolated Docker integration runner.' );
}
error_reporting( E_ALL );
set_error_handler( static function ( int $severity, string $message, string $file, int $line ): bool {
	if ( error_reporting() & $severity ) {
		throw new ErrorException( $message, 0, $severity, $file, $line );
	}
	return false;
} );
$assert = static function ( bool $condition, string $message ): void {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
	echo "PASS: $message\n";
};
$ids = array();
$create = static function ( string $title, string $content, string $author = '', string $status = 'publish' ) use ( &$ids ): int {
	$id = wp_insert_post( array( 'post_title' => $title, 'post_content' => $content, 'post_status' => $status ), true );
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	$ids[] = $id;
	if ( $author !== '' ) {
		update_post_meta( $id, 'autor', $author );
	}
	return $id;
};
try {
	$book = $create( 'Unrelated title', 'No matching content', 'Uniqueauthorneedle' );
	$article = $create( 'Article', 'Uniqueauthorneedle appears only in content' );
	$create( 'Private', '', 'Uniqueauthorneedle', 'private' );
	$create( 'Draft', '', 'Uniqueauthorneedle', 'draft' );
	for ( $i = 0; $i < 85; $i++ ) {
		$create( 'Uniqueauthorneedle article ' . $i, '' );
	}
	$service = new \Webbooks\Search\SearchService();
	$result = $service->search( 'Uniqueauthorneedle' );
	$assert( array_column( $result['books'], 'id' ) === array( $book ), 'Author-only book survives more than 80 article matches; private and draft excluded' );
	$assert( count( $result['articles'] ) === 12 && $result['total'] === 87, 'Separate display limits and accurate total' );
	$catalog = $service->filter( '', '', '', 1 );
	$assert( in_array( $book, wp_list_pluck( $catalog->posts, 'ID' ), true ) && ! in_array( $article, wp_list_pluck( $catalog->posts, 'ID' ), true ), 'Optional catalog filters retain books and exclude articles' );
	$total = 0;
	$catalog_html = category_query( '', '', '', false, 1, $total );
	$assert( $total === (int) $catalog->found_posts && strpos( $catalog_html, 'book-placeholder.svg' ) !== false && strpos( $catalog_html, 'Publication year:' ) === false, 'Compact catalog has accurate total and a neutral cover placeholder' );
	$wildcard = $create( 'Literal %_needle', '', 'Author' );
	$result = $service->search( '%_needle' );
	$assert( array_column( $result['books'], 'id' ) === array( $wildcard ), 'SQL LIKE wildcards are escaped' );
	$result = $service->search( "' OR 1=1 --" );
	$assert( $result['total'] === 0, 'Search text cannot change SQL predicates' );
	$assert( ( new WP_Query( array( 'post__in' => array( $book ), 'post_status' => 'publish' ) ) )->post_count === 1, 'Search filter removed after query' );
	update_post_meta( $book, 'language', 'ua' );
	update_post_meta( $book, 'complexity', array( 'invalid' ) );
	$meta = \Webbooks\Book\BookMeta::getNormalizedMeta( $book );
	$assert( $meta['language_code'] === 'uk' && is_string( $meta['complexity'] ), 'Metadata works without ACF and rejects array-valued text' );
	$assert( \Webbooks\Domain\Book\Language::EN->value === 'en', 'PSR-4 enum autoload' );
	ob_start();
	get_template_part( 'template-parts/cards/book-card', null, array( 'book' => get_post( $book ) ) );
	$card = ob_get_clean();
	$assert( str_contains( $card, 'Unrelated title' ) && ! str_contains( $card, 'data-original=' ), 'Card renders without ACF or global post and uses native images' );
	$gallery = webbooks_render_book_gallery( get_post( $book ) );
	$assert( $gallery === '', 'Empty gallery renders safely without ACF' );
	update_post_meta( $book, 'gallery', array( array( 'ID' => array( 'malformed' ), 'url' => 'https://example.test/cover.png?one=1&two=2', 'alt' => '" onerror="alert(1)' ) ) );
	$first_gallery = webbooks_render_book_gallery( get_post( $book ) );
	$second_gallery = webbooks_render_book_gallery( get_post( $book ) );
	$assert( str_contains( $first_gallery, '&quot; onerror=&quot;' ) && ! str_contains( $first_gallery, 'alt="" onerror=' ), 'Gallery attribute injection is escaped and malformed attachment IDs are ignored' );
	$assert( $first_gallery !== $second_gallery && str_contains( $second_gallery, 'book-gallery' ), 'Multiple galleries render with independent IDs' );
	delete_post_meta( $book, 'gallery' );
	$assert( apply_filters( 'post_gallery', 'existing gallery', array(), 1 ) === 'existing gallery', 'Core gallery filter preserved' );
	$comment = wp_insert_comment( array( 'comment_post_ID' => $book, 'comment_author' => 'Guest', 'comment_author_email' => 'guest@example.test', 'comment_content' => 'Pending', 'comment_approved' => 0 ) );
	$_SERVER['REMOTE_ADDR'] = '192.0.2.10';
	webbooks_mark_comment_rate_limit( $comment, 0 );
	$assert( webbooks_is_comment_rate_limited( '192.0.2.10', 'guest@example.test' ), 'Pending comment establishes cooldown' );
	$assert( has_action( 'widgets_init', 'webbooks_register_sidebars' ) !== false, 'Sidebar registered at widgets_init' );
	$assert( webbooks_is_comment_rate_limited( '192.0.2.10', 'rotated@example.test' ), 'Changing email cannot bypass the IP cooldown' );
	update_post_meta( $book, '_views_count', 11 );
	$assert( \Webbooks\Statistics\Views::record( $book, '192.0.2.20' ) === 12, 'First visit migrates the legacy counter' );
	$assert( \Webbooks\Statistics\Views::record( $book, '192.0.2.20' ) === 12, 'Repeated visit is deduplicated' );
	$assert( \Webbooks\Statistics\Views::record( $book, '192.0.2.21' ) === 13, 'Independent visitor increments atomically' );
	$private = $create( 'Hidden', '', 'Author', 'private' );
	$assert( ! \Webbooks\Statistics\Views::isPublic( $private ) && ! \Webbooks\Statistics\Views::isPublic( 999999999 ), 'Missing and private posts reject statistics and downloads' );
	update_post_meta( $book, 'download_pcloud', array( 'malformed' ) );
	update_post_meta( $book, 'download_mega', 'javascript:alert(1)' );
	update_post_meta( $book, 'download', 'https://example.test/book.pdf?one=1&two=2' );
	$sources = \Webbooks\Book\DownloadLinks::sources( $book );
	$assert( count( $sources ) === 1 && $sources[0]['link'] === 'https://example.test/book.pdf?one=1&two=2', 'Download service rejects malformed values and unsafe schemes' );
	wp_update_post( array( 'ID' => $book, 'post_password' => 'secret' ) );
	$assert( ! \Webbooks\Statistics\Views::isPublic( $book ) && ! \Webbooks\Book\DownloadLinks::sources( $book ), 'Password-protected posts do not expose download URLs' );
	wp_update_post( array( 'ID' => $book, 'post_password' => '' ) );
	$related = webbooks_related_query( array( 'post__in' => array( $book ), 'posts_per_page' => 6 ) );
	$assert( $related->post_count === 1 && $related->get( 'no_found_rows' ), 'Related posts hydrate cached IDs without pagination totals' );
	wp_update_post( array( 'ID' => $book, 'post_status' => 'draft' ) );
	$assert( webbooks_related_query( array( 'post__in' => array( $book ) ) )->post_count === 0, 'Unpublishing a book invalidates related-post results' );
	wp_update_post( array( 'ID' => $book, 'post_status' => 'publish' ) );
	$legacy_comment = wp_insert_comment( array( 'comment_post_ID' => $book, 'comment_content' => 'Legacy', 'comment_approved' => 1 ) );
	$uk_comment = wp_insert_comment( array( 'comment_post_ID' => $book, 'comment_content' => 'Ukrainian', 'comment_approved' => 1 ) );
	$ru_comment = wp_insert_comment( array( 'comment_post_ID' => $book, 'comment_content' => 'Russian', 'comment_approved' => 1 ) );
	update_comment_meta( $uk_comment, 'webbooks_comment_lang', 'uk' );
	update_comment_meta( $ru_comment, 'webbooks_comment_lang', 'ru' );
	$localized = get_comments( array( 'post_id' => $book, 'status' => 'approve', 'meta_query' => webbooks_comment_language_meta_query( 'uk' ), 'number' => 2 ) );
	$assert( count( $localized ) === 2 && ! in_array( $ru_comment, array_column( $localized, 'comment_ID' ) ), 'Language filtering happens before pagination and preserves legacy comments' );
	$anonymous = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/users' ) );
	$assert( $anonymous->get_status() === 401, 'Anonymous REST users access is restricted' );
	wp_set_current_user( 1 );
	$authenticated = rest_do_request( new WP_REST_Request( 'GET', '/wp/v2/users' ) );
	$assert( $authenticated->get_status() === 200, 'Authenticated REST clients keep the core users endpoint' );
	wp_set_current_user( 0 );
	echo 'Verified WordPress ' . get_bloginfo( 'version' ) . ' / PHP ' . PHP_VERSION . "\n";
} finally {
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	restore_error_handler();
}
