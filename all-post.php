<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage your-clean-template
 * Template Name: My Custom Page Template
 */
get_header(); // подключаем header.php ?>
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

				$AllPosts = new WP_Query(  array ( 'orderby' => 'title', 'posts_per_page' => '-1' ) );

				// Цикл
				if ( $AllPosts->have_posts() ) {
				while ( $AllPosts->have_posts() ) {
				$AllPosts->the_post();
				?>
				<!--Post -->
					<div class="col-sm-6 col-md-3">
					    <div class="card" id="post-<?php the_ID();?>" <?php post_class();?>>
					    	<div class="card-image">
					    		<a href="<?php the_permalink();?>">
					    			<?php the_post_thumbnail('big-thumb');?>
					    			<span class="card-img-label"><?php 
					    					$post_x = get_the_category();
				    						print_r($post_x[0]->cat_name);


					    			?></span>
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

				<?php
				}
				} else {
				// Постов не найдено
				}
				/* Возвращаем оригинальные данные поста. Сбрасываем $post. */
				wp_reset_postdata();
				?>	
				</div>				
					</div>
				</div>
								<!-- ./ Latest Listings Section -->
				
			</section>
			<!-- right col -->
		</aside>
		
		<?php get_footer(); // подключаем footer.php ?>