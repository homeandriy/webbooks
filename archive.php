<?php
/**
 * Страница архивов записей (archive.php)
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
						if ( is_day() ) :
							/* translators: %s: Archive date. */
							printf( esc_html__( 'Daily archives: %s', 'webbooks' ), esc_html( get_the_date() ) );
							elseif ( is_month() ) :
								/* translators: %s: Archive month and year. */
								printf( esc_html__( 'Monthly archives: %s', 'webbooks' ), esc_html( get_the_date( 'F Y' ) ) );
							elseif ( is_year() ) :
								/* translators: %s: Archive year. */
								printf( esc_html__( 'Yearly archives: %s', 'webbooks' ), esc_html( get_the_date( 'Y' ) ) );
							else :
								esc_html_e( 'Archives', 'webbooks' );
						endif;
							?>
					</h4>
				</div>
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'template/loop' ); ?>
					<?php endwhile; ?>
				<?php else : ?>
					<h2><?php esc_html_e( 'No posts found.', 'webbooks' ); ?></h2>
				<?php endif; ?>
			</div>
			<!-- ./ Latest Listings Section -->
			<!-- Start Featured Listings Slider -->
			<div class="bg-brown-lighten bdr-b">
				<div class="featured-slider">
							<?php
								$query = new WP_Query(
									\Webbooks\Localization\Polylang::withLanguageQueryArg(
										array(
											'orderby' => 'rand',
											'posts_per_page' => '12',
										)
									)
								);
								?>
							<?php if ( $query->have_posts() ) : ?>
								<?php while ( $query->have_posts() ) : ?>
									<?php $query->the_post(); ?>
									<div class="featured-slide">
										<span class="featured-icon text-orange"><i class="mdi-action-stars" aria-hidden="true"></i></span>
										<?php the_post_thumbnail(); ?>
										<div class="featured-content">
											<a href="<?php echo esc_url( get_permalink() ); ?>">
												<h4><?php the_title(); ?></h4>
												<span><?php esc_html_e( 'Description goes here.', 'webbooks' ); ?></span>
											</a>
										</div>
									</div>
								<?php endwhile; ?>
							<?php endif; ?>
							<?php wp_reset_postdata(); ?>
				</div>
			</div>
		<!-- ./ Featured Listings Slider -->
		</div>
	</section>
	<!-- right col -->
</aside>
<?php
get_footer();
