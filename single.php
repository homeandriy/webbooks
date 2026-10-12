<?php
/**
 * Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage webbooks
 */

get_header();

global $post;
$current_category   = get_the_category( $post->ID );
$category_id        = ! isset( $current_category[0] ) ? webbooks_category_root( 'books' ) : $current_category[0]->cat_ID;
$books_disclaimer   = __( 'All books presented on this site are for informational purposes only. Any use by you is allowed for review purposes only. If you plan to use them further, you must purchase them from the rights holders. The site administration is not responsible for your use of these materials.', 'webbooks' );
$book_meta_fallback = __( 'Not specified', 'webbooks' );
?>
<?php get_sidebar(); ?>
<aside class="right-section">
	<section class="content">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<?php
			$book_meta          = \Webbooks\Book\BookMeta::getNormalizedMeta( $post->ID );
			$book_author        = $book_meta['author'] !== '' ? $book_meta['author'] : $book_meta_fallback;
			$book_year_display  = $book_meta['year'] !== '' ? $book_meta['year'] : $book_meta_fallback;
			$book_format        = $book_meta['format'] !== '' ? $book_meta['format'] : $book_meta_fallback;
			$book_language      = $book_meta['language'] !== '' ? $book_meta['language'] : $book_meta_fallback;
			$book_pages_display = $book_meta['pages'] !== null ? (string) $book_meta['pages'] : $book_meta_fallback;
			$book_schema        = \Webbooks\Book\BookMeta::getBookSchema( $post->ID );
			?>
			<script type="application/ld+json"><?php echo wp_json_encode( $book_schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ); ?></script>
			<!-- Start Page Heading -->
			<div class="section bg-brown-lighten ">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 col-lg-8 col-xl-8">
							<h1 class="post-title entry-title" id="title"><?php the_title(); ?></h1>
						</div>
					</div>
				</div>
			</div>
			<!-- ./ Page Header -->
			<!-- Start Main Section -->
			<div class="bg-brown-lighten bdr-b container-fluid">
				<!-- Start Nav Tabs -->
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<li class="nav-item" role="presentation"><a href="#description-section" aria-controls="description-section" class="nav-link active" id="description-section-tab" role="tab" data-bs-toggle="tab" aria-selected="true"><?php esc_html_e( 'Description', 'webbooks' ); ?></a></li>
					<li class="nav-item" role="presentation"><a href="#download-section" aria-controls="download-section" class="nav-link" id="download-section-tab" role="tab" data-bs-toggle="tab" aria-selected="false"><?php esc_html_e( 'Description/Download', 'webbooks' ); ?></a></li>
					<li class="nav-item" role="presentation"><a href="#user-comments-section" aria-controls="user-comments-section" class="nav-link" id="user-comments-section-tab" role="tab" data-bs-toggle="tab" aria-selected="false"><?php esc_html_e( 'Discussions', 'webbooks' ); ?></a></li>
					<li class="nav-item" role="presentation"><a href="#book-warning-section" aria-controls="book-warning-section" class="nav-link" id="book-warning-section-tab" role="tab" data-bs-toggle="tab" aria-selected="false"><?php esc_html_e( 'Report', 'webbooks' ); ?></a></li>
				</ul>
			<!-- ./  Nav Tabs -->
			</div>
			<!-- Start Tab Panels -->
			<div class="main-section">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12 col-lg-9 col-xl-9 book-reading-column">
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane fade active show" id="description-section" aria-labelledby="description-section-tab" tabindex="0">
									<div class="card wb-panel  bdr-t-none">
										<!-- Default card wb-panel contents -->
										<div class="card-body book-reading-content">
											<?php the_content(); ?>
											<p>
												<?php if ( ! empty( get_post_meta( $post->ID, 'buy', true ) ) ) : ?>
													<a href="<?php echo esc_url( get_post_meta( $post->ID, 'buy', true ) ); ?>" class="btn btn-primary book-buy-button" target="_blank" rel="noopener noreferrer"><span class="fa fa-shopping-cart" aria-hidden="true"></span><span><?php esc_html_e( 'Buy book', 'webbooks' ); ?></span></a>
												<?php endif; ?>
											</p>
											<div class="mrg-t">
																<?php echo wp_kses_post( webbooks_partner_banner() ); ?>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="download-section" aria-labelledby="download-section-tab" tabindex="0">
									<div class="card wb-panel  bdr-t-none">
										<!-- Default card wb-panel contents -->
										<div class="card-body">
											<!-- Table -->
											<table class="table table-hover">
												<tbody>
													<tr>
														<td><?php esc_html_e( 'Author:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_author ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Publication year:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_year_display ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Category:', 'webbooks' ); ?></td><td>
															<?php
															if ( function_exists( 'yoast_breadcrumb' ) ) {
																yoast_breadcrumb();
															}
															?>
														</td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Publisher:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_meta['publisher'] ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Language:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_language ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Status:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_meta['complexity'] ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Format:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_format ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Page count:', 'webbooks' ); ?></td>
														<td><?php echo esc_html( $book_pages_display ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Download link:', 'webbooks' ); ?></td>
														<td>
																<?php echo wp_kses_post( apply_filters( 'get_download_link', $post, $category_id ) ); ?>
														</td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Submitted by:', 'webbooks' ); ?></td>
														<td>
															<time datetime="<?php the_time( 'o-m-d' ); ?>" class="post-date updated"><?php the_time( apply_filters( 'themify_loop_date', 'M j, Y H:i' ) ); ?></time>
															<!-- Для решения ошибки с author -->
															<span class="vcard author">
																<span class="fn">Andrii</span>
															</span>
														</td>
													</tr>
												</tbody>
											</table>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i>
													<?php echo esc_html( $books_disclaimer ); ?>
													<a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
												</strong>
											</p>                                   
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="user-comments-section" aria-labelledby="user-comments-section-tab" tabindex="0">
									<div class="card wb-panel  bdr-t-none">
										<!-- Default card wb-panel contents -->
										<div class="card-body">
											<?php if ( comments_open() ) : ?>
												<?php comments_template(); ?>
											<?php endif; ?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i>
													<?php echo esc_html( $books_disclaimer ); ?>
													<a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
												</strong>
											</p>                                   
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="book-warning-section" aria-labelledby="book-warning-section-tab" tabindex="0">
									<div class="card wb-panel  bdr-t-none">
										<!-- Default card wb-panel contents -->
										<div class="card-body">
											<?php
											$report_form = absint( webbooks_site_setting( 'report_form' ) ) ?: 3;

											if ( shortcode_exists( 'ninja_form' ) ) {
												echo do_shortcode( '[ninja_form id=' . $report_form . ']' );
											}
											?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i>
													<?php echo esc_html( $books_disclaimer ); ?>
													<a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>
												</strong>
											</p>                                   
										</div>
										<div class="collapse" id="collapseExample"><div class="card-body"></div></div>
										<!-- List group -->                                
									</div>
								</div>
							</div>
							<!-- ./ Tab Panels -->
							<div class="relation-section mrg-t">
								<div class="row">
									<div class="col-md-12 col-lg-12 col-xl-12">
										<div class="card wb-panel">
											<div class="card-header text-bg-warning">
												<div class="card-title text-center"><strong><?php esc_html_e( 'Books from this category:', 'webbooks' ); ?></strong></div>
											</div>
											<div class="card-body">
												<?php
												$related = webbooks_related_query( array(
														'posts_per_page' => 6,
														'post_status'    => 'publish',
														'orderby'        => 'date',
														'category__in'   => $category_id,
														'post__not_in'   => array( $post->ID ),

													) );
												?>
												<?php $i = 0; ?>
												<?php while ( $related->have_posts() ) : ?>
													<?php $related->the_post(); ?>
													<?php if ( $i === 0 || $i === 3 ) : ?>
													<div class="row">
													<?php endif; ?>
													<div class="col-md-6 col-lg-4 col-xl-4">
														<div class="card theme-thumbnail">
															<?php if ( has_post_thumbnail() ) {
																the_post_thumbnail( 'medium', array( 'class' => 'img-fluid', 'loading' => 'lazy' ) );
															} else { ?>
																<img src="<?php echo esc_url( webbooks_book_placeholder_url() ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="card theme-thumbnail" loading="lazy">
															<?php } ?>
															<div class="card-body">
																<h3><?php echo esc_html( get_the_title() ); ?></h3>
																<p><?php echo wp_kses_post( wp_trim_words( get_the_content(), 20, ' ...' ) ); ?></p>
																<p><a href="<?php echo esc_url( get_the_permalink() ); ?>" class="btn btn-success" role="button"><?php esc_html_e( 'View', 'webbooks' ); ?></a></p>
															</div>
														</div>
													</div>
													<?php if ( $i === 2 || $i === 5 || $i === $related->post_count - 1 ) : ?>
													</div>
													<?php endif; ?>
													<?php ++$i; ?>
												<?php endwhile; ?>
												<?php wp_reset_postdata(); ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="relation-section mrg-t">
								<div class="row">
									<div class="col-lg-12">
										<div class="card wb-panel">
											<div class="card-header">
												<div class="card-title text-center"><strong><?php esc_html_e( 'Our partners', 'webbooks' ); ?></strong></div>
											</div>
											<div class="card-body">
													<?php echo wp_kses_post( webbooks_partner_banner() ); ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- ./ Tab Panels -->
						<!-- Start Photo Gallery -->
						<div class="col-md-12 col-lg-3 col-xl-3">
							<div class="card wb-panel  mrg-t">
								<div class="card-body">
										<?php echo wp_kses_post( webbooks_render_book_gallery( $post ) ); ?>
								</div>
							</div>
							<div class="info-block">
								<h3><?php esc_html_e( 'See also:', 'webbooks' ); ?></h3>
								<?php
								$related = webbooks_related_query( array(
										'posts_per_page' => 6,
										'post_status'    => 'publish',
										'orderby'        => 'date',
										'category__in'   => $category_id,
										'post__not_in'   => array( $post->ID ),

									) );
								?>
								<?php if ( $related->have_posts() ) : ?>
									<?php while ( $related->have_posts() ) : ?>
										<?php $related->the_post(); ?>
										<div class="list-group">
											<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="list-group-item">
												<h4 class="book-heading">
													<i class="fa fa-arrow-circle-right"></i>
													<?php echo esc_html( get_the_title() ); ?>
												</h4>
												<p class="list-group-item-text">
													<?php echo wp_kses_post( wp_trim_words( get_the_content(), 10, ' ...' ) ); ?>
												</p>
											</a>
										</div>
									<?php endwhile; ?>
								<?php endif; ?>
								<?php wp_reset_postdata(); ?>
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
