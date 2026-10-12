<?php
/**
 * Category archive.
 *
 * @package Webbooks
 */

declare(strict_types=1);

$category          = get_queried_object();
$books_root        = webbooks_category_root( 'books' );
$articles_root     = webbooks_category_root( 'articles' );
$category_id       = $category instanceof WP_Term ? (int) $category->term_id : 0;
$is_books_category = $category_id === $books_root || term_is_ancestor_of( $books_root, $category_id, 'category' );
$is_articles_category = $category_id === $articles_root || term_is_ancestor_of( $articles_root, $category_id, 'category' );

if ( $is_books_category ) {
	$empty_state = array(
		'kind'         => 'books',
		'title'        => __( 'No books in this section yet', 'webbooks' ),
		'description'  => __( 'Try another book category or return later for new additions.', 'webbooks' ),
		'action_url'   => get_category_link( $books_root ),
		'action_label' => __( 'Browse all books', 'webbooks' ),
	);
} elseif ( $is_articles_category ) {
	$empty_state = array(
		'kind'         => 'articles',
		'title'        => __( 'No articles in this section yet', 'webbooks' ),
		'description'  => __( 'Try another article category or return later for new materials.', 'webbooks' ),
		'action_url'   => get_category_link( $articles_root ),
		'action_label' => __( 'Browse all articles', 'webbooks' ),
	);
} else {
	$empty_state = array(
		'kind'         => 'content',
		'title'        => __( 'This section is currently empty', 'webbooks' ),
		'description'  => __( 'Choose another section or come back later for new publications.', 'webbooks' ),
		'action_url'   => home_url( '/' ),
		'action_label' => __( 'Back to homepage', 'webbooks' ),
	);
}

get_header();
get_sidebar();
?>
<aside class="right-section">
	<section class="content">
		<div class="container-fluid mrg-tb">
			<?php get_template_part( 'template/header-filter' ); ?>
			<div class="row">
				<div class="col-12 section-title archive-header">
					<p class="archive-header__eyebrow"><i class="fa <?php echo esc_attr( $is_books_category ? 'fa-book' : 'fa-file-text-o' ); ?>" aria-hidden="true"></i> <?php esc_html_e( 'Category', 'webbooks' ); ?></p>
					<h1 class="archive-header__title"><?php echo esc_html( single_cat_title( '', false ) ); ?></h1>
				</div>
				<div class="content-loop col-12">
					<div class="row">
						<?php if ( have_posts() ) : ?>
							<?php while ( have_posts() ) : the_post(); ?>
								<?php get_template_part( 'template/loop' ); ?>
							<?php endwhile; ?>
						<?php else : ?>
							<?php get_template_part( 'template-parts/content/empty-state', null, $empty_state ); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php pagination(); ?>
		</div>
		<div class="bg-brown-lighten bdr-b">
			<div class="container-fluid">
				<h2 class="h5">
					<?php esc_html_e( 'Latest books', 'webbooks' ); ?>
					<a class="float-end" href="<?php echo esc_url( \Webbooks\Localization\Polylang::pageUrl( 'allpost' ) ); ?>"><?php esc_html_e( 'Browse all books', 'webbooks' ); ?></a>
				</h2>
			</div>
			<?php get_template_part( 'template-parts/book-carousel' ); ?>
		</div>
	</section>
</aside>
<?php get_footer(); ?>
