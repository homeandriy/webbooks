<?php
/**
 * Страница архивов записей (archive.php)
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
            <div class="row">
                <div class="col-md-12 section-title ">
                    <h4>
                        <?php
                        if (is_day()) : printf('Архив по днях: %s', get_the_date());
                            elseif (is_month()) : printf('Архивы по месяцам: %s', get_the_date('F Y'));
                            elseif (is_year()) : printf('Годовой архив: %s', get_the_date('Y'));
                            else : 'Архивы';
                        endif; ?>
                    </h4>
                </div>
                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) :?>
                        <?php the_post();?>
                        <?php get_template_part('template/loop');?>
                    <?php endwhile; ?>
                <?php else: ?>
                    <h2>Нет записей.</h2>
                <?php endif; ?>
			</div>
            <!-- ./ Latest Listings Section -->
			<!-- Start Featured Listings Slider -->
			<div class="bg-brown-lighten bdr-b">
				<div class="rolled">
					<div class="roller-viewport">
						<div class="roller-canister">								
							<?php
								$query = new WP_Query(  array ( 'orderby' => 'rand', 'posts_per_page' => '12' ) );
                            ?>
                            <?php if ( $query->have_posts() ) : ?>
								<?php while ( $query->have_posts() ) : ?>
                                    <?php $query->the_post(); ?>
                                    <div class="roller-item">
                                        <div class="">
                                            <div class="">
                                                <span class="featured-icon text-orange"><i class="mdi-action-stars"></i></span>
                                                <?php the_post_thumbnail();  ?>
                                                <div class="featured-content">
                                                    <a href="#">
                                                        <h4><?php the_title(); ?></h4>
                                                        <span>Description goes here.</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <!-- ./ Featured Listings Slider -->
        </div>
    </section>
    <!-- right col -->
</aside>
<?php get_footer();
