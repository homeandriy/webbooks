<?php
/**
 * Страница архивов записей (archive.php)
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
			<div class="row">
				<div class="col-lg-12 section-title ">
					<h4>
						<?php
						if ( is_day() ) :
							printf( 'Архив по днях: %s', get_the_date() );
							elseif ( is_month() ) :
								printf( 'Архивы по месяцам: %s', get_the_date( 'F Y' ) );
							elseif ( is_year() ) :
								printf( 'Годовой архив: %s', get_the_date( 'Y' ) );
							else :
								'Архивы';
						endif;
							?>
					</h4>
				</div>
				<div class="content-loop col-12"><div class="row">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<?php get_template_part( 'template/loop' ); ?>
					<?php endwhile; ?>
				<?php else : ?>
					<h2>Нет записей.</h2>
				<?php endif; ?>
				</div></div>
			</div>
			<!-- ./ Latest Listings Section -->
			<!-- Start Featured Listings Slider -->
			<div class="bg-brown-lighten bdr-b">
			<?php get_template_part( 'template-parts/book-carousel' ); ?>
		</div>
		<!-- ./ Featured Listings Slider -->
		</div>
	</section>
	<!-- right col -->
</aside>
<?php
get_footer();
