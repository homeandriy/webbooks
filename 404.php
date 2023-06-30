<?php
/**
 * Страница 404 ошибки (404.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
get_header(); ?>
<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-md-12 section-title">
					<h1> Ничего не найдено, но может Вас заинтересую вот эти книги(новости) <a class="btn btn-default btn-sm pull-right" href="<?php get_home_url();?>/allpost">Смотреть по категориям &raquo;</a></h1>
				</div>
				<div class="content-loop">			
                    <?php $some_random_post = new WP_Query(['orderby' => 'rand', 'posts_per_page' => '3' ] ); ?>
                    <?php if ( $some_random_post->have_posts() ) : ?>
                        <?php while ( $some_random_post->have_posts() ) : ?>
                            <?php $some_random_post->the_post();?>
                            <?php get_template_part('template/loop'); ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
					<?php wp_reset_postdata();?>
				</div>						
			</div>
		</div>		
	</section>
	<!-- right col -->
</aside>
<?php get_footer();

