<?php
/**
 * Homepage: the most recent books and articles.
 *
 * @package Webbooks
 */

declare(strict_types=1);

$book_formats = \Webbooks\Book\BookMeta::DOWNLOADABLE_FORMATS;
$books_root   = webbooks_category_root( 'books' );
$articles_root = webbooks_category_root( 'articles' );
$books_url    = get_category_link( $books_root );
$articles_url = get_category_link( $articles_root );

if ( is_wp_error( $books_url ) ) {
	$books_url = \Webbooks\Localization\Polylang::pageUrl( 'allpost' );
}
if ( is_wp_error( $articles_url ) ) {
	$articles_url = home_url( '/' );
}

$latest_books = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array(
			array(
				'key'     => 'format',
				'value'   => $book_formats,
				'compare' => 'IN',
			),
		),
	)
);

$latest_articles = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 6,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'meta_query'          => array(
			'relation' => 'OR',
			array(
				'key'     => 'format',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'format',
				'value'   => $book_formats,
				'compare' => 'NOT IN',
			),
		),
	)
);

get_header();
get_sidebar();
?>
<aside class="right-section">
	<section class="content">
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-12 section-title">
					<?php get_template_part( 'template/header-filter' ); ?>
				</div>
			</div>

			<?php
			get_template_part(
				'template-parts/home/latest-section',
				null,
				array(
					'query'         => $latest_books,
					'kind'          => 'books',
					'title'         => __( 'Latest books', 'webbooks' ),
					'heading_tag'   => 'h1',
					'archive_url'   => $books_url,
					'archive_label' => __( 'Browse all books', 'webbooks' ),
				)
			);
			get_template_part(
				'template-parts/home/latest-section',
				null,
				array(
					'query'         => $latest_articles,
					'kind'          => 'articles',
					'title'         => __( 'Latest articles', 'webbooks' ),
					'archive_url'   => $articles_url,
					'archive_label' => __( 'Browse all articles', 'webbooks' ),
				)
			);
			?>
		</div>
	</section>
</aside>
<?php get_footer(); ?>
