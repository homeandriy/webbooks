<?php
/**
 * Шаблон отдельной записи (single.php)
 * Template Name Posts: single-post(NOT BOOK)
 * @package WordPress
 * @subpackage webbooks
 */

$current_category    = get_the_category();
$id_current_category = $current_category[0]->term_id;

get_header();?>
	<?php get_sidebar(); ?>
	<aside class="right-section">
		<!-- Main content - Includes Featured Listings + Latest Listings -->
		<section class="content">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<!-- Start Page Heading -->
				<div class="section bg-brown-lighten ">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12 col-md-8 col-lg-8">
								<h1 class="post-title entry-title"><?php the_title(); ?></h1>
							</div>
						</div>
					</div>
				</div>
				<!-- ./ Page Header -->
				<!-- Start Main Section -->
				<div class="bg-brown-lighten bdr-b container-fluid">
					<!-- Start Nav Tabs -->
					<ul class="nav nav-tabs" role="tablist" id="myTab">
						<li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab"><?php esc_html_e( 'Description', 'webbooks' ); ?></a></li>
						<li role="presentation"><a href="#comments1" aria-controls="comments1" role="tab" data-toggle="tab"><?php esc_html_e( 'Description/Download', 'webbooks' ); ?></a></li>
					</ul>
					<!-- ./  Nav Tabs -->
				</div>
				<!-- Start Tab Panels -->
				<div class="main-section">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-9">
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane fade active in" id="description">
										<div class="panel panel-default bdr-t-none">
											<!-- Default panel contents -->
											<div class="panel-body">
												<div>
													<?php the_content(); ?>
													<div>
														<?php if ( function_exists( 'evc_buttons_code' ) ) : ?>
																					<h3><?php esc_html_e( 'Did you like this article or book? Share it with your friends:', 'webbooks' ); ?></h3>
															<?php echo evc_buttons_code(); ?>
														<?php endif; ?>
													</div>
												</div>
											</div>
											<div class="panel-heading bdr-t">
												<?php esc_html_e( 'Comments', 'webbooks' ); ?> <button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											</div>
											<div class="panel-body">
												<?php comments_template(); ?>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="comments1">
										<div class="panel panel-default bdr-t-none">
											<!-- Default panel contents -->
											<div class="panel-body">
												<!-- Table -->
												<table class="table">
													<tbody>
														<tr>
															<td><?php esc_html_e( 'Category:', 'webbooks' ); ?></td>
															<td>
															<?php
															if ( function_exists( 'yoast_breadcrumb' ) ) {
																yoast_breadcrumb();
															}
															?>
															</td>
														</tr>
														<tr>
															<td><?php esc_html_e( 'Status:', 'webbooks' ); ?></td>
															<td>
															<?php
																$complexity = trim( get_field( 'complexity' ) );
																echo \Webbooks\Book\BookMeta::getComplexity( $complexity );
															?>
															</td>
														</tr>
														<tr>
															<td><?php esc_html_e( 'Original article link (if specified or if this article is not authored by us)', 'webbooks' ); ?></td>
															<td>
																<?php
																	$download_link             = get_post_meta( $post->ID, 'download', true );
																	$download_link             = parse_url( $download_link, PHP_URL_PATH );
																	$collection_download_links = explode( '/', $download_link );

																	$collection_download_links = $collection_download_links[ count( $collection_download_links ) - 1 ];
																?>
																<a href="<?php echo esc_url( home_url( '/download/?key=' . rawurlencode( $collection_download_links ) . '&count=' . (int) $post->ID . '&cat=' . (int) $id_current_category ) ); ?>" class="btn btn-primary btn-sm" target="_blank"><?php esc_html_e( 'Download', 'webbooks' ); ?></a>
															</td>
														</tr>
														<tr>
															<td><?php esc_html_e( 'Submitted by', 'webbooks' ); ?></td>
															<td>
																<!-- Для решения ошибки с updated -->
																<time datetime="<?php the_time( 'o-m-d' ); ?>" class="post-date updated"><?php the_time( apply_filters( 'themify_loop_date', 'M j, Y H:i' ) ); ?></time>
																<!-- Для решения ошибки с author -->
																<span class="vcard author">
																	<span class="fn"><?php the_author_posts_link(); ?></span>
																</span>
															</td>
														</tr>
													</tbody>
												</table>
												<h2>
													<i class="fa fa-exclamation-circle"></i>
													<?php
													echo wp_kses_post(
														sprintf(
															/* translators: %s: Site link. */
															__( 'Articles published on %s are provided with source links. The site administration is not responsible for your use of these materials.', 'webbooks' ),
															'<a href="' . esc_url( home_url() ) . '">' . esc_html( get_bloginfo( 'name' ) ) . '</a>'
														)
													);
													?>
												</h2>
											</div>
											<div class="collapse" id="collapseExample">
												<div class="panel-body">
													<form>
														<div class="form-group">
															<label for="enter-comment"><?php esc_html_e( 'Comment', 'webbooks' ); ?></label>
															<textarea id="enter-comment" class="form-control" rows="4" placeholder="<?php echo esc_attr__( 'Enter comment', 'webbooks' ); ?>"></textarea>
														</div>
														<button type="submit" class="btn btn-primary btn-sm"><?php esc_html_e( 'Submit', 'webbooks' ); ?></button>
													</form>
												</div>
											</div>
											<!-- List group -->
										</div>
									</div>
								</div>
							</div>   <!-- ./ col-md-6 -->
							<!-- ./ Tab Panels -->
							<!-- Start Photo Gallery -->
							<div class="col-md-3">
								<div class="panel panel-default mrg-t">
								<!-- Default panel contents -->
									<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
										<ol class="carousel-indicators">
											<li data-target="#carousel-example-generic" data-slide-to="0" class=""></li>
											<li data-target="#carousel-example-generic" data-slide-to="1" class=""></li>
											<li data-target="#carousel-example-generic" data-slide-to="2"></li>
											<li data-target="#carousel-example-generic" data-slide-to="3" class="active"></li>
										</ol>
										<div class="carousel-inner" role="listbox">
											<div class="item active">
												<?php the_post_thumbnail( 'big-thumb-main' ); ?>
											</div>
										</div>
										<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
											<span class="fa fa-angle-left" aria-hidden="true"></span>
													<span class="sr-only"><?php esc_html_e( 'Previous', 'webbooks' ); ?></span>
										</a>
										<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
											<span class="fa fa-angle-right" aria-hidden="true"></span>
													<span class="sr-only"><?php esc_html_e( 'Next', 'webbooks' ); ?></span>
										</a>
									</div>
								</div>
								<div class="info-block">
												<h3><?php esc_html_e( 'See also:', 'webbooks' ); ?></h3>
									<?php
									$request_relation_post = array(
										'posts_per_page' => 5,
										'post_status'    => 'publish',
										'orderby'        => 'rand',
										'category__in'   => $id_current_category,
									);
									$related_posts         = new WP_Query( $request_relation_post );
									?>
									<?php
									if ( $related_posts->have_posts() ) :
										?>
										<?php
										while ( $related_posts->have_posts() ) :
											?>
											<?php
											$related_posts->the_post();
											?>
											<div class="list-group">
												<a href="<?php echo get_the_permalink(); ?>" class="list-group-item">
													<h4 class="list-group-item-heading"><i class="fa fa-arrow-circle-right">
															</i> <?php echo get_the_title(); ?>
													</h4>
													<p class="list-group-item-text"><?php echo wp_trim_words( get_the_content(), 40, '...' ); ?></p>
												</a>
											</div>
											<?php
										endwhile;
										?>
										<?php
									endif;
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		</section>
		<!-- right col -->
	</aside>
<?php
get_footer();
