<?php
/**
 * Шаблон поиска (search.php)
 *
 * @package WordPress
 * @subpackage webbooks
 */

get_header();
?>
<?php get_sidebar(); ?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-md-12 section-title">
					<h4>
						<?php
						/* translators: %s: Search query. */
						printf( esc_html__( 'Search results for: %s', 'webbooks' ), esc_html( get_search_query() ) );
						?>
						<a class="btn btn-secondary btn-sm float-end" href="<?php echo esc_url( home_url( '/allpost/' ) ); ?>">
							<?php esc_html_e( 'See all listings', 'webbooks' ); ?> &raquo;
						</a>
					</h4>
				</div>
				<div class="content-loop">
					<?php if ( have_posts() ) : ?>
						<?php while ( have_posts() ) : ?>
							<?php the_post(); ?>
							<?php get_template_part( 'template/loop' ); ?>
						<?php endwhile; ?>
					<?php else : ?>
						<h2><?php esc_html_e( 'No posts found.', 'webbooks' ); ?></h2>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php \Webbooks\Theme\Setup::pagination(); ?>
	</section>
</aside>
<?php get_footer(); ?>
