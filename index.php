<?php
/**
 * Главная страница (index.php)
 * @package WordPress
 * @subpackage webbooks
 */
get_header(); ?>

<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-md-12 section-title">
                    <?php get_template_part('template/header-filter'); ?>
                </div>
				<div class="content-loop">
					<div class="row">
						<?php if ( have_posts() ): ?>
                            <?php while ( have_posts() ): ?>
                                <?php the_post(); ?>
							    <?php get_template_part( 'template/loop' ); ?>
						    <?php endwhile; ?>
						<?php else: ?>
                            <h2>Нет записей.</h2>
                        <?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<!-- ./ Latest Listings Section -->
	</section>
	<!-- right col -->
</aside>
<!-- ./ Featured Listings Slider -->
<!-- Start Latest Listings Section -->
<?php get_footer(); ?>


