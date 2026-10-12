<?php
/**
 * Шаблон рубрики (category.php)
 * @package WordPress
 * @subpackage webbooks
 */
get_header();  ?>
<?php get_sidebar(); ?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb">
			<?php get_template_part( 'header-filter' ); ?>
			<div class="row">
				<div class="col-lg-12 section-title ">
					<h1 class="h3">
						<?php esc_html_e( 'You are in section:', 'webbooks' ); ?>
						<?php echo esc_html( single_cat_title( '', false ) ); ?>
					</h1>
				</div>
				<div class="content-loop col-12"><div class="row">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'template/loop' ); ?>
					<?php endwhile; ?>
				<?php else : ?>
					<h2><?php esc_html_e( 'This section is currently empty.', 'webbooks' ); ?></h2>
				<?php endif; ?>
				</div></div>
			</div>
			<?php pagination(); // пагинация, функция нах-ся в function.php ?>
		</div>
		<!-- ./ Latest Listings Section -->
		<!-- Start Featured Listings Slider -->
		<div class="bg-brown-lighten bdr-b">
			<div class="container-fluid">
				<h5>
					<?php esc_html_e( 'Latest books', 'webbooks' ); ?>
					<a class="float-end" href="<?php echo esc_url( \Webbooks\Localization\Polylang::pageUrl( 'allpost' ) ); ?>"><?php esc_html_e( 'View all posts here', 'webbooks' ); ?></a>
				</h5>
			</div>
			<?php get_template_part( 'template-parts/book-carousel' ); ?>
		</div>
		<!-- ./ Featured Listings Slider -->
	</section>
	<!-- right col -->
</aside>
<?php
get_footer();
