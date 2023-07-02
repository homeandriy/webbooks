<?php
/**
 * Шаблон рубрики (category.php)
 * @package WordPress
 * @subpackage webbooks
 */
get_header();  ?>
<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">
		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb">
			<?php get_template_part('header-filter') ?>
			<div class="row">
				<div class="col-md-12 section-title ">
					<h4><?php single_cat_title('Вы находитесь в разделе: ');?> <a class="btn btn-default btn-sm pull-right" href="<?php get_home_url();?>/allpost">Смотреть по категориям &raquo;</a></h4>
				</div>
				<div class="content-loop">
				<?php if (have_posts()): ?>
                    <?php while (have_posts()): ?>
                        <?php the_post(); ?>
						<?php get_template_part('template/loop'); ?>
					<?php endwhile; ?>
                <?php else: ?>
                    <h2>На данный момент раздел пуст.</h2>
                <?php endif; ?>
				</div>						
			</div>
		    <?php pagination(); // пагинация, функция нах-ся в function.php ?>
		</div>
		<!-- ./ Latest Listings Section -->
        <!-- Start Featured Listings Slider -->
		<div class="bg-brown-lighten bdr-b">
			<div class="container-fluid">
				<h5>Самое популярное<a class="pull-right" href="">Смотри все посты здесь</a></h5>
			</div>
			<div class="rolled">
				<div class="roller-viewport">
					<div class="roller-canister">
						<?php
							$query = new WP_Query(  ['orderby' => 'rand', 'posts_per_page' => '12' ] );
                        ?>
						<?php if ( $query->have_posts() ) : ?>
							<?php while ( $query->have_posts() ) : ?>
                                <?php $query->the_post(); ?>
                                <div class="roller-item">
                                    <div class="">
                                        <div class="">
                                            <span class="featured-icon text-orange"><i class="fa fa-bar-chart"></i></i></span>
                                            <?php the_post_thumbnail('big-thumb');  ?>
                                            <div class="featured-content">
                                                <a href="<?php the_permalink(); ?>"><h4><?php the_title(); ?></h4></a>
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
		<!-- ./ Featured Listings Slider -->
	</section>
	<!-- right col -->
</aside>
<?php get_footer();
