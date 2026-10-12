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
$related_test_terms = array();
$css_manifest = array(
	'entry' => array( 'imports' => array( 'shared', 'shared' ), 'css' => array( 'entry.css' ) ),
	'shared' => array( 'imports' => array( 'entry' ), 'css' => array( 'shared.css' ) ),
);
$assert( webbooks_vite_css( $css_manifest, 'entry' ) === array( 'shared.css', 'entry.css' ), 'Vite shared CSS is ordered, deduplicated and cycle-safe' );
$assert( webbooks_category_root( 'books' ) === 18 && webbooks_category_root( 'articles' ) === 19, 'Sidebar preserves the established Books and Articles roots without site settings' );
$original_site_settings = get_option( 'webbooks_site_settings', null );
try {
	update_option( 'webbooks_site_settings', array( 'partner_url' => '', 'partner_image' => '' ) );
	$banner = webbooks_partner_banner();
	$assert( str_contains( $banner, 'cityhost.ua/?partner=user28504' ) && str_contains( $banner, 'banner_240x400.jpg' ), 'Sidebar keeps its default partner banner when site settings are blank' );
	$css = (string) file_get_contents( WEBBOOKS_PATH . '/style.css' );
	$assert( str_contains( $css, 'background-color: var(--color-brand-header-bg, #434350)' ), 'Logo retains a dark header fallback when CSS variables are unavailable' );
} finally {
	if ( $original_site_settings === null ) {
		delete_option( 'webbooks_site_settings' );
	} else {
		update_option( 'webbooks_site_settings', $original_site_settings );
	}
}
$pagination_html = webbooks_render_ajax_pagination( 3, 2 );
$assert( strpos( $pagination_html, 'class="page-item active"' ) !== false && strpos( $pagination_html, 'page-numbers page-link current' ) !== false, 'Bootstrap 5 AJAX pagination marks its current page' );
$assert( strpos( $pagination_html, 'data-page="3"' ) !== false && webbooks_render_ajax_pagination( 1, 1 ) === '', 'Bootstrap 5 pagination retains AJAX targets and hides a single page' );
$create = static function ( string $title, string $content, string $author = '', string $status = 'publish' ) use ( &$ids ): int {
	$id = wp_insert_post( array( 'post_title' => $title, 'post_content' => $content, 'post_status' => $status ), true );
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	$ids[] = $id;
	if ( $author !== '' ) {
		update_post_meta( $id, 'autor', $author );
		update_post_meta( $id, 'format', 'pdf' );
	}
	return $id;
};
$baseline_book_total = ( new \Webbooks\Search\SearchService() )->filter( '', '', '', 1 )->found_posts;
try {
	$book = $create( 'Unrelated title', 'No matching content', 'Uniqueauthorneedle' );
	$article = $create( 'Article', 'Uniqueauthorneedle appears only in content' );
	update_post_meta( $article, 'autor', 'Article author' );
	$assert( ! \Webbooks\Book\BookMeta::isDownloadableBook( $article ) && \Webbooks\Book\BookMeta::isDownloadableBook( $book ), 'Articles with author metadata remain articles; downloadable formats identify books' );
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
	$assert( str_starts_with( $catalog_html, '<div class="row">' ) && str_contains( $catalog_html, 'col-12 col-md-6 content_block' ), 'AJAX catalogs retain the Bootstrap row and two-column card widths' );
	$assert( $total === $baseline_book_total + 1 && strpos( $catalog_html, 'book-cover-default.webp' ) !== false && strpos( $catalog_html, 'Publication year:' ) === false, 'Compact catalog has accurate total and a neutral cover placeholder' );
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
	$assert( str_contains( $card, 'book-cover-default.webp' ), 'Books without a featured image use the generated default cover' );
	$gallery = webbooks_render_book_gallery( get_post( $book ) );
	$assert( str_contains( $gallery, 'book-cover-default.webp' ) && ! str_contains( $gallery, 'class="swiper' ), 'Empty gallery displays the default cover without ACF or slideshow controls' );
	update_post_meta( $book, 'gallery', array( array( 'ID' => array( 'malformed' ), 'url' => 'https://example.test/cover.png?one=1&two=2', 'alt' => '" onerror="alert(1)' ) ) );
	$first_gallery = webbooks_render_book_gallery( get_post( $book ) );
	$second_gallery = webbooks_render_book_gallery( get_post( $book ) );
	$assert( str_contains( $first_gallery, '&quot; onerror=&quot;' ) && ! str_contains( $first_gallery, 'alt="" onerror=' ), 'Gallery attribute injection is escaped and malformed attachment IDs are ignored' );
	$assert( $first_gallery !== $second_gallery && str_contains( $second_gallery, 'book-gallery' ), 'Multiple galleries render with independent IDs' );
	$assert( str_contains( $first_gallery, 'swiper-zoom-container' ) && str_contains( $first_gallery, '<dialog' ) && ! str_contains( $first_gallery, 'fancybox' ), 'Book gallery renders Swiper Zoom and native dialog markup' );
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
	$related_arguments = array( 'post__in' => array( $book ), 'posts_per_page' => 6 );
	$related_id_queries = 0;
	$related_cached_value = array();
	$capture_related_cache = static function ( string $key, mixed $value, int $expiration ) use ( &$related_cached_value ): void {
		if ( str_starts_with( $key, 'webbooks_related_' ) ) {
			$related_cached_value = array( $key, $value, $expiration );
		}
	};
	$count_related_queries = static function ( WP_Query $query ) use ( &$related_id_queries ): void {
		if ( $query->get( 'fields' ) === 'ids' ) {
			++$related_id_queries;
		}
	};
	add_action( 'pre_get_posts', $count_related_queries );
	add_action( 'set_transient', $capture_related_cache, 10, 3 );
	try {
		$related = webbooks_related_query( $related_arguments );
		$assert( $related->post_count === 1 && $related->get( 'no_found_rows' ), 'Related posts hydrate cached IDs without pagination totals' );
		$assert( $related_cached_value[1] === array( $book ) && $related_cached_value[2] === 600, 'Related cache stores only IDs with a ten-minute expiration' );
		$before = $related_id_queries;
		wp_cache_flush();
		$assert( webbooks_related_query( $related_arguments )->post_count === 1 && $related_id_queries === $before, 'Related transient survives an object-cache flush without repeating the ID query' );
		delete_transient( $related_cached_value[0] );
		$assert( webbooks_related_query( $related_arguments )->post_count === 1 && $related_id_queries === $before + 1, 'Evicted related transient is rebuilt automatically' );
		$before = $related_id_queries;
		wp_update_post( array( 'ID' => $book, 'post_status' => 'draft' ) );
		$assert( webbooks_related_query( $related_arguments )->post_count === 0 && $related_id_queries === $before + 1, 'Unpublishing a book invalidates the same cached related list' );
		$before = $related_id_queries;
		webbooks_related_query( $related_arguments );
		$assert( $related_id_queries === $before, 'Empty related lists are cached rather than queried again' );
		wp_update_post( array( 'ID' => $book, 'post_status' => 'publish' ) );
		$assert( webbooks_related_query( $related_arguments )->post_count === 1, 'Publishing a book refreshes an empty related list' );
		$term = wp_insert_term( 'Related cache test ' . wp_generate_uuid4(), 'category' );
		if ( is_wp_error( $term ) ) {
			throw new RuntimeException( $term->get_error_message() );
		}
		$term_id = (int) $term['term_id'];
		$related_test_terms[] = $term_id;
		$category_arguments = array( 'category__in' => array( $term_id ), 'post__in' => array( $book ) );
		$assert( webbooks_related_query( $category_arguments )->post_count === 0, 'Unassigned category starts with an empty related list' );
		wp_set_post_categories( $book, array( $term_id ) );
		$assert( webbooks_related_query( $category_arguments )->post_count === 1, 'Assigning a category invalidates its cached empty list' );
		$before = $related_id_queries;
		wp_update_term( $term_id, 'category', array( 'name' => 'Renamed related cache test' ) );
		webbooks_related_query( $category_arguments );
		$assert( $related_id_queries === $before + 1, 'Editing a category invalidates related queries' );
		wp_set_post_categories( $book, array( 1 ) );
		$assert( webbooks_related_query( $category_arguments )->post_count === 0, 'Removing a category assignment refreshes related results' );
		$deleted_book = $create( 'Related deletion test', '', 'Author' );
		$delete_arguments = array( 'post__in' => array( $deleted_book ) );
		webbooks_related_query( $delete_arguments );
		$before = $related_id_queries;
		wp_delete_post( $deleted_book, true );
		$assert( webbooks_related_query( $delete_arguments )->post_count === 0 && $related_id_queries === $before + 1, 'Deleting a book invalidates its cached related list' );
	} finally {
		remove_action( 'pre_get_posts', $count_related_queries );
		remove_action( 'set_transient', $capture_related_cache, 10 );
	}
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
	$assert( \Webbooks\Support\AuthorSupport::isValidCardNumber( '4111111111111111' ) && ! \Webbooks\Support\AuthorSupport::isValidCardNumber( '4111111111111112' ), 'Support card validation retains Luhn checks' );
	$assert( \Webbooks\Support\AuthorSupport::sanitizeMonobankJarUrl( 'https://send.monobank.ua/jar/test' ) !== '' && \Webbooks\Support\AuthorSupport::sanitizeMonobankJarUrl( 'https://send.monobank.ua.example.test/jar/test' ) === '', 'Support payment URLs require the exact trusted host' );
	$assert( \Webbooks\Support\AuthorSupport::sanitizeTelegramUrl( 'http://t.me/test' ) === '' && \Webbooks\Support\AuthorSupport::sanitizeTelegramUrl( 'https://t.me/test' ) !== '', 'Deferred Telegram links require HTTPS' );
	$assert( has_action( 'wp_ajax_webbooks_author_support_cards', array( \Webbooks\Support\AuthorSupport::class, 'paymentCards' ) ) !== false, 'Merged support payment handler is registered' );
	echo 'Verified WordPress ' . get_bloginfo( 'version' ) . ' / PHP ' . PHP_VERSION . "\n";
} finally {
	foreach ( $related_test_terms as $term_id ) {
		wp_delete_term( $term_id, 'category' );
	}
	foreach ( $ids as $id ) {
		wp_delete_post( $id, true );
	}
	restore_error_handler();
}
