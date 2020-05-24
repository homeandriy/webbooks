<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage your-clean-template
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
					<h4><?php single_cat_title('Вы находитесь в категории: ');?> </h4>
				</div>
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); // старт цикла ?>
				
					
					<p><?php the_content(); // контент ?></p>
				
			<?php endwhile; // конец цикла ?>
					</div>
				</div>
								<!-- ./ Latest Listings Section -->
				<!-- Start Featured Listings Slider -->
				<div class="bg-brown-lighten bdr-b">
					<div class="container-fluid">
						<h5>Featured Listings <a class="pull-right" href="">Your Listing Here</a></h5>
					</div>
					<div class="rolled">
						<div class="roller-viewport">
							<div class="roller-canister">								
								<?php 

									

									$query = new WP_Query(  array ( 'orderby' => 'rand', 'posts_per_page' => '12' ) );

									// Цикл
									if ( $query->have_posts() ) {
										while ( $query->have_posts() ) {
											$query->the_post();
											?>
												<div class="roller-item">
													<div class="">
														<div class="">
															<span class="featured-icon text-orange"><i class="mdi-action-stars"></i></span>
															<?php the_post_thumbnail();  ?>
															<div class="featured-content">
																<a href="<?php the_permalink(); ?>">
																	<h4><?php the_title(); ?></h4>
																	<span>Description goes here.</span>
																</a>
															</div>
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
				</div>
				<!-- ./ Featured Listings Slider -->
			</section>
			<!-- right col -->
		</aside>
		<!-- <section class="container main-con">
			<div class="row">
				<div class="col-xs-12 col-md-8">
					<?php if (have_posts()): while (have_posts()): the_post(); // если посты есть - запускаем цикл wp ?>
														<?php get_template_part('loop'); // для отображения каждой записи берем шаблон loop.php ?>
													<?php endwhile; // конец цикла
		else:echo '<h2>Нет записей.</h2>';endif; // если записей нет, напишим "простите" ?>
					<?php pagination(); // пагинация, функция нах-ся в function.php ?>
				</div>
				<div class="col-xs-6 col-md-4">
					<?php get_sidebar(); // подключаем sidebar.php ?>
				</div>
			</div>
		</section> -->
		<?php get_footer(); // подключаем footer.php ?>