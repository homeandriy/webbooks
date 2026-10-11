<?php
/**
 * Tag archive template.
 *
 * @package WordPress
 * @subpackage webbooks
 */

get_header();
?>

<?php get_sidebar(); ?>

<aside class="right-section">
	<section class="content">
		<div class="container-fluid mrg-tb">
			<?php get_template_part( 'header-filter' ); ?>
			<div class="row">
				<div class="col-12 section-title archive-header">
					<h1 class="archive-header__title">
						<span class="archive-header__eyebrow">
							<i class="fa fa-tag" aria-hidden="true"></i>
						</span>
							<?php
							/* translators: %s: Tag name. */
							printf( esc_html__( 'Posts tagged: %s', 'webbooks' ), esc_html( single_tag_title( '', false ) ) );
							?>
						</h1>
				</div>
				<div class="col-12">
					<div class="content-loop">
						<div class="row">
							<?php if ( have_posts() ) : ?>
								<?php while ( have_posts() ) : ?>
									<?php the_post(); ?>
									<?php get_template_part( 'template/loop' ); ?>
								<?php endwhile; ?>
							<?php else : ?>
								<div class="col-12">
									<p class="mb-0">
										<?php esc_html_e( 'This section is currently empty.', 'webbooks' ); ?>
									</p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
			<?php Webbooks\Theme\Setup::pagination(); ?>
		</div>

		<div class="bg-brown-lighten bdr-b">
			<div class="container-fluid">
				<h5>
					<?php esc_html_e( 'Most popular', 'webbooks' ); ?>
					<a class="float-end" href="<?php echo esc_url( \Webbooks\Localization\Polylang::pageUrl( 'allpost' ) ); ?>">
						<?php esc_html_e( 'View all posts here', 'webbooks' ); ?>
					</a>
				</h5>
			</div>
			<div class="featured-slider">
				<?php
				$featured_posts = new WP_Query(
					\Webbooks\Localization\Polylang::withLanguageQueryArg(
						array(
							'orderby'        => 'rand',
							'posts_per_page' => 12,
						)
					)
				);
				?>
				<?php if ( $featured_posts->have_posts() ) : ?>
					<?php while ( $featured_posts->have_posts() ) : ?>
						<?php $featured_posts->the_post(); ?>
						<div class="featured-slide">
							<span class="featured-icon text-orange">
								<i class="fa fa-bar-chart" aria-hidden="true"></i>
							</span>
							<?php the_post_thumbnail( 'big-thumb' ); ?>
							<div class="featured-content">
								<h4>
									<a href="<?php the_permalink(); ?>">
										<?php the_title(); ?>
									</a>
								</h4>
							</div>
						</div>
					<?php endwhile; ?>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
</aside>

<?php
get_footer();
