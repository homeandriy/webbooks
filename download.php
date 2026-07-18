<?php
/**
 * Шаблон обычной страницы (page.php)
 *
 * @package WordPress
 * @subpackage webbooks
 * Template Name: download-book
 */

get_header();

$download_post_id   = \Webbooks\Localization\Polylang::translatedPostId( absint( filter_input( INPUT_GET, 'count', FILTER_SANITIZE_NUMBER_INT ) ) );
$download_post      = 0 < $download_post_id ? get_post( $download_post_id ) : null;
$is_download_post   = $download_post instanceof WP_Post && 'publish' === $download_post->post_status;
$post_title         = $is_download_post ? get_the_title( $download_post ) : '';
$post_permalink     = $is_download_post ? get_permalink( $download_post ) : '';
$thumbnail_url      = $is_download_post ? get_the_post_thumbnail_url( $download_post, 'medium' ) : '';
$requested_category = absint( filter_input( INPUT_GET, 'cat', FILTER_SANITIZE_NUMBER_INT ) );
$category_id        = \Webbooks\Localization\Polylang::translatedTermId( 0 < $requested_category ? $requested_category : 69 );
?>
<?php get_sidebar(); ?>
<aside class="right-section">
	<section class="content">
		<div class="container-fluid mrg-tb white-bg">
			<div class="row">
				<div class="col-12 section-title">
					<h1 class="post-title entry-title text-center">
						<?php esc_html_e( 'Download', 'webbooks' ); ?>
						<strong>"<?php echo esc_html( $post_title ); ?>"</strong>.
						<br>
						<?php esc_html_e( 'Please wait, the download link will appear shortly:', 'webbooks' ); ?>
					</h1>
					<hr>
				</div>
				<div class="col-12">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div id="countdown" class="download-countdown" aria-live="polite"></div>
									</div>
									<div class="panel-body">
										<div class="text-center">
											<a href="https://cityhost.ua/?partner=user28504" title="Хостинг CityHost.ua" target="_blank" rel="noopener noreferrer">
												<img src="https://cityhost.ua/upload_img/ref_banners/banner_970x90.jpg" loading="lazy" title="Хостинг СитиХост" alt="Hosting CityHost">
											</a>
										</div>
										<div id="js-content"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12">
					<article class="download-book-card download-book-card--current">
						<a class="download-book-card__image-link" href="<?php echo esc_url( $post_permalink ); ?>" target="_blank" rel="noopener noreferrer">
							<img width="128" height="180" class="download-book-card__image" src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $post_title ); ?>" loading="lazy">
						</a>
						<div class="download-book-card__content">
							<h2 class="download-book-card__title">
								<a href="<?php echo esc_url( $post_permalink ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $post_title ); ?>
								</a>
							</h2>
						</div>
					</article>
				</div>
				<div class="col-12 section-title">
					<h3 class="post-title entry-title">
						<?php esc_html_e( 'You may also like (opens in a new tab):', 'webbooks' ); ?>
					</h3>
					<?php
					// Get the current category for related-book selection.
					$query_arguments             = \Webbooks\Localization\Polylang::withLanguageQueryArg(
						array(
							'posts_per_page' => 5,
							'category__in'   => $category_id,
							'post_status'    => 'publish',
							'orderby'        => 'rand',
						)
					);
					$related_books_for_downloads = new WP_Query( $query_arguments );
					?>

					<?php if ( $related_books_for_downloads->have_posts() ) : ?>
						<div class="row download-related-books">
							<?php while ( $related_books_for_downloads->have_posts() ) : ?>
								<?php $related_books_for_downloads->the_post(); ?>
								<?php $related_thumbnail_url = get_the_post_thumbnail_url( get_the_ID(), 'medium' ); ?>
							<div class="col-12 col-md-6">
								<article class="download-book-card">
									<a class="download-book-card__image-link" href="<?php echo esc_url( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">
										<img width="128" height="180" class="download-book-card__image" src="<?php echo esc_url( $related_thumbnail_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
									</a>
									<div class="download-book-card__content">
										<h4 class="download-book-card__title">
											<a href="<?php echo esc_url( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer">
												<?php echo esc_html( get_the_title() ); ?>
											</a>
										</h4>
										<p class="download-book-card__description">
											<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 28, '…' ) ); ?>
										</p>
										<a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-info download-book-card__action" target="_blank" rel="noopener noreferrer">
											<?php esc_html_e( 'Open book', 'webbooks' ); ?>
											<i class="fa fa-arrow-right" aria-hidden="true"></i>
										</a>
									</div>
								</article>
							</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	</section>
</aside>
<?php get_footer(); ?>
