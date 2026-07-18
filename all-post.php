<?php
/**
 * Template Name: Webbooks Custom Page Template
 *
 * Paginated catalog of the latest posts.
 *
 * @package Webbooks
 */

get_header();

$page_number = 1;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public pagination parameter.
if ( isset( $_GET['page'] ) && is_scalar( $_GET['page'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public pagination parameter.
	$page_number = max( 1, absint( wp_unslash( (string) $_GET['page'] ) ) );
}

$all_posts_query_args = \Webbooks\Localization\Polylang::withLanguageQueryArg(
	array(
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => 12,
		'paged'          => $page_number,
	)
);

$all_posts = new WP_Query( $all_posts_query_args );
$page_url  = get_permalink( get_queried_object_id() );
?>

<?php get_sidebar(); ?>

<aside class="right-section">
	<section class="content">
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-12 section-title">
					<h1 class="h4 mb-0">
						<?php esc_html_e( 'All posts', 'webbooks' ); ?>
					</h1>
				</div>
				<div class="col-12">
					<div class="content-loop">
						<div class="row" data-all-post-results>
							<?php if ( $all_posts->have_posts() ) : ?>
								<?php while ( $all_posts->have_posts() ) : ?>
									<?php $all_posts->the_post(); ?>
									<?php get_template_part( 'template/loop' ); ?>
								<?php endwhile; ?>
							<?php else : ?>
								<div class="col-12">
									<p class="mb-0">
										<?php esc_html_e( 'No posts found.', 'webbooks' ); ?>
									</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<?php if ( $all_posts->max_num_pages > 1 ) : ?>
				<nav class="all-post-navigation d-flex flex-wrap justify-content-center gap-2" data-all-post-navigation aria-label="<?php esc_attr_e( 'Posts navigation', 'webbooks' ); ?>">
					<?php if ( $page_number > 1 ) : ?>
						<a class="btn btn-outline-secondary" href="<?php echo esc_url( $page_url ); ?>">
							<?php esc_html_e( 'Back to first page', 'webbooks' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $page_number < $all_posts->max_num_pages ) : ?>
						<a class="btn btn-info" data-all-post-load-more href="<?php echo esc_url( add_query_arg( 'page', $page_number + 1, $page_url ) ); ?>">
							<?php esc_html_e( 'Load more', 'webbooks' ); ?>
							<i class="fa fa-arrow-right" aria-hidden="true"></i>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>
		</div>
	</section>
</aside>

<?php
wp_reset_postdata();
get_footer();
