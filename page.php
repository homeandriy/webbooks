<?php
/**
 * Шаблон обычной страницы (page.php)
 *
 * @package WordPress
 * @subpackage webbooks
 */

get_header();
get_sidebar();
?>
<aside class="right-section page-layout">
	<section class="content page-content">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php
				the_post();
				$recommended_books = new WP_Query(
					array(
						'posts_per_page' => 6,
						'post_status'    => 'publish',
						'orderby'        => 'rand',
						'post_type'      => 'post',
					)
				);
				?>
				<div class="section bg-brown-lighten">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12 col-md-8 col-lg-8">
								<h1 class="post-title entry-title" id="title"><?php the_title(); ?></h1>
							</div>
						</div>
					</div>
				</div>

				<div class="main-section">
					<div class="container-fluid mrg-tb">
						<div class="row">
							<div class="col-sm-12 col-md-9 col-lg-9">
								<article <?php post_class( 'panel panel-default' ); ?>>
									<div class="panel-body entry-content page-entry-content">
										<?php the_content(); ?>
									</div>
								</article>

								<div class="relation-section mrg-t">
									<div class="row">
										<div class="col-sm-12 col-md-12 col-lg-12">
											<div class="panel">
												<div class="panel-heading panel-warning">
													<div class="panel-title text-center"><strong><?php esc_html_e( 'Recommended books:', 'webbooks' ); ?></strong></div>
												</div>
												<div class="panel-body">
													<?php if ( $recommended_books->have_posts() ) : ?>
														<?php $i = 0; ?>
														<?php while ( $recommended_books->have_posts() ) : ?>
															<?php $recommended_books->the_post(); ?>
															<?php if ( 0 === $i % 3 ) : ?>
																<div class="row">
															<?php endif; ?>
															<div class="col-sm-6 col-md-4 col-lg-4">
																<div class="thumbnail">
																	<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="thumbnail" loading="lazy">
																	<div class="caption">
																		<h3><?php echo esc_html( get_the_title() ); ?></h3>
																		<p><?php echo wp_kses_post( wp_trim_words( get_the_content(), 20, ' ...' ) ); ?></p>
																		<p><a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn btn-success" role="button"><?php esc_html_e( 'View', 'webbooks' ); ?></a></p>
																	</div>
																</div>
															</div>
															<?php if ( 2 === $i % 3 || $i + 1 === (int) $recommended_books->post_count ) : ?>
																</div>
															<?php endif; ?>
															<?php ++$i; ?>
														<?php endwhile; ?>
													<?php endif; ?>
													<?php wp_reset_postdata(); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</section>
</aside>
<?php
get_footer();
