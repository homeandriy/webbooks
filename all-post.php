<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage webbooks
 * Template Name: My Custom Page Template
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
					<h4>Все посты </h4>
				</div>
				<?php
				$all_posts = new WP_Query(  array ( 'orderby' => 'title', 'posts_per_page' => '-1' ) );
				?>
				<?php if ( $all_posts->have_posts() ): ?>
				    <?php while ( $all_posts->have_posts() ): ?>
				        <?php $all_posts->the_post(); ?>
				        <!--Post -->
                        <div class="col-sm-6 col-md-3">
                            <div class="card" id="post-<?php the_ID();?>" <?php post_class();?>>
                                <div class="card-image">
                                    <a href="<?php the_permalink();?>">
                                        <?php the_post_thumbnail('big-thumb');?>
                                        <span class="card-img-label"></span>
                                    </a>
                                </div>
                                <div class="card-content mCustomScrollbar" data-mcs-theme="dark">
                                    <h5><a href="<?php the_permalink();?>" class="card-title "><?php the_title();?></a></h5>
                                    <p >
                                        <?php the_excerpt();?>
                                    </p>
                                </div>
                                <div class="card-action">
                                    <a href="#" id="<?php the_ID();?>" class="load-post">Превью книги</a>

                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
				<?php endif; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
        <!-- ./ Latest Listings Section -->
    </section>
    <!-- right col -->
</aside>
<?php get_footer();
