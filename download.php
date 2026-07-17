<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage webbooks
 * Template Name: download-book
 */
get_header();

$post_id          = absint( filter_input( INPUT_GET, 'count', FILTER_SANITIZE_NUMBER_INT ) ?: 0 );
$post             = $post_id ? get_post( $post_id ) : null;
$is_download_post = $post instanceof WP_Post && $post->post_status === 'publish';
$post_title       = $is_download_post ? get_the_title( $post ) : '';
$post_permalink   = $is_download_post ? get_permalink( $post ) : '';
$thumbnail_url    = $is_download_post ? get_the_post_thumbnail_url( $post, 'medium' ) : '';
$category_id      = absint( filter_input( INPUT_GET, 'cat', FILTER_SANITIZE_NUMBER_INT ) ?: 69 );
?>
<?php get_sidebar(); ?>
<aside class="right-section">
	<section class="content">
		<div class="container-fluid mrg-tb white-bg">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-lg-12 section-title">
					<h1 class="post-title entry-title text-center">
						<?php esc_html_e( 'Download', 'webbooks' ); ?> <strong>"<?php echo esc_html( $post_title ); ?>"</strong>.<br>
						Пожалуйста, ждите, скоро появится ссылка:
					</h1>
					<hr>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div class="container-fluid">
						<div class="row">
							<div class="col-sm-12 col-md-12 col-lg-12">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div id="countdown" class="download-countdown" aria-live="polite"></div>
									</div>
									<div class="panel-body">
										<div class="text-center">
											<a href='https://cityhost.ua/?partner=user28504' title='Хостинг CityHost.ua' target='_blank' rel='noopener noreferrer'><img src='https://cityhost.ua/upload_img/ref_banners/banner_970x90.jpg' loading="lazy" title='Хостинг СитиХост' alt='Hosting CityHost'/></a>
										</div>
										<div id="js-content"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12">
					<div class="list-group">
						<a href="<?php echo esc_url( $post_permalink ); ?>" class="list-group-item active" target="_blank" rel="noopener noreferrer">
							<div class="row">
								<div class="col-12 col-sm-2 col-md-2 col-lg-2">
									<?php
									?>
									<img
										width="128"
										height="180"
										class="media-object"
										src="<?php echo esc_url( $thumbnail_url ); ?>"
										alt="<?php echo esc_attr( $post_title ); ?>"
										loading="lazy"
									>
								</div>
								<div class="col-12 col-sm-10 col-md-10 col-lg-10">
									<h4 class="list-group-item-heading"><?php echo esc_html( $post_title ); ?></h4>
								</div>
							</div>
						</a>
					</div>
				</div>
				<div class="col-sm-12 col-md-12 col-lg-12section-title ">
					<h3 class="post-title entry-title">Также вам должно понравится: (откроется в новой вкладке)</h3>
					<?php
					// Поулчить текущую категорию, для виборки
					$query_arguments             = array(
						'posts_per_page' => 5,
						'category__in'   => $category_id,
						'post_status'    => 'publish',
						'orderby'        => 'rand',
					);
					$related_books_for_downloads = new WP_Query( $query_arguments );
					?>

					<?php if ( $related_books_for_downloads->have_posts() ) : ?>
						<?php while ( $related_books_for_downloads->have_posts() ) : ?>
							<?php $related_books_for_downloads->the_post(); ?>
							<?php $url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ); ?>
							<div class="list-group">
								<a href="<?php echo esc_url( get_permalink() ); ?>" class="list-group-item active" target="_blank" rel="noopener noreferrer">
									<div class="row">
										<div class="col-12 col-sm-2 col-md-2 col-lg-2">
											<img width="128" height="180" class="media-object" src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
										</div>
										<div class="col-12 col-sm-10 col-md-10 col-lg-10">
											<h4 class="list-group-item-heading"><?php echo esc_html( get_the_title() ); ?></h4>
											<p class="list-group-item-text text-white">
												<?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 40, '... ' ) ); ?>
											</p>
										</div>
									</div>
								</a>
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
					<?php wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
	</section>
</aside>
<?php get_footer(); ?>
