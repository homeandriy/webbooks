<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
get_header();  ?>
<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb">
			<div class="row">
				<div class="col-md-12 section-title">
					<h4><?php single_cat_title('Вы находитесь в категории: ');?></h4>
				</div>
			    <?php if ( have_posts() ): ?>
                    <?php while ( have_posts() ) : ?>
                        <?php the_post();?>
					    <p><?php the_content(); ?></p>
			        <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <!-- ./ Latest Listings Section -->
            <!-- Start Featured Listings Slider -->
            <div class="bg-brown-lighten bdr-b">
                <div class="rolled">
                    <div class="roller-viewport">
                        <div class="roller-canister">
                            <?php $query = new WP_Query(  array ( 'orderby' => 'rand', 'posts_per_page' => '12' ) ); ?>
                            <?php if ( $query->have_posts() ) : ?>
                                <?php while ( $query->have_posts() ) : ?>
                                    <?php $query->the_post(); ?>
                                    <div class="roller-item">
                                        <div class="">
                                            <div class="">
                                                <span class="featured-icon text-orange"><i class="mdi-action-stars"></i></span>
                                                <?php the_post_thumbnail();  ?>
                                                <div class="featured-content">
                                                    <a href="<?php the_permalink(); ?>">
                                                        <h4><?php the_title(); ?></h4>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <?php wp_reset_postdata();?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ./ Featured Listings Slider -->
    </section>
    <!-- right col -->
</aside>
<?php get_footer();
