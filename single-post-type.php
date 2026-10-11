<?php
/**
 * Шаблон отдельной записи (single.php)
 * Template Name Posts: single-post(NOT BOOK)
 * Template Name: Article
 * Template Post Type: post
 * @package WordPress
 * @subpackage webbooks
 */

$current_category    = get_the_category();
$id_current_category = $current_category[0]->term_id ?? webbooks_category_root( 'books' );

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
							<div class="col-md-12 col-lg-8 col-xl-8">
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
						<li class="nav-item" role="presentation"><a href="#description" aria-controls="description" class="nav-link active" id="description-tab" role="tab" data-bs-toggle="tab" aria-selected="true"><?php esc_html_e( 'Description', 'webbooks' ); ?></a></li>
						<li class="nav-item" role="presentation"><a href="#comments1" aria-controls="comments1" class="nav-link" id="comments1-tab" role="tab" data-bs-toggle="tab" aria-selected="false"><?php esc_html_e( 'Description/Download', 'webbooks' ); ?></a></li>
					</ul>
					<!-- ./  Nav Tabs -->
				</div>
				<!-- Start Tab Panels -->
				<div class="main-section">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-9">
								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane fade active show" id="description" aria-labelledby="description-tab" tabindex="0">
										<div class="card wb-panel  bdr-t-none">
											<!-- Default card wb-panel contents -->
											<div class="card-body">
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
											<div class="card-header bdr-t">
												<?php esc_html_e( 'Comments', 'webbooks' ); ?>
											</div>
											<div class="card-body">
												<?php comments_template(); ?>
											</div>
										</div>
									</div>
									<div role="tabpanel" class="tab-pane fade" id="comments1" aria-labelledby="comments1-tab" tabindex="0">
										<div class="card wb-panel  bdr-t-none">
											<!-- Default card wb-panel contents -->
											<div class="card-body">
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
																$complexity = \Webbooks\Book\BookMeta::getFieldText( get_the_ID(), 'complexity' );
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
																<time datetime="<?php the_time( 'o-m-d' ); ?>" class="post-date updated"><?php the_time( apply_filters( 'themify_loop_date', 'M j, Y H:i' ) ); ?></time>
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
												<div class="card-body">
													<form>
														<div class="mb-3">
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
							</div>   <!-- ./ col-lg-6 -->
							<!-- ./ Tab Panels -->
							<!-- Start Photo Gallery -->
							<div class="col-lg-3">
								<div class="card wb-panel  mrg-t">
								<!-- Default card wb-panel contents -->
								<?php echo webbooks_render_book_gallery( $post ); ?>
								</div>
								<div class="info-block">
												<h3><?php esc_html_e( 'See also:', 'webbooks' ); ?></h3>
									<?php
									$request_relation_post = array(
										'posts_per_page' => 5,
										'post_status'    => 'publish',
										'orderby'        => 'date',
										'category__in'   => $id_current_category,
									);
									$related_posts         = webbooks_related_query( $request_relation_post );
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
													<h4 class="book-heading"><i class="fa fa-arrow-circle-right">
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
