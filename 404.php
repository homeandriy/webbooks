<?php
/**
 * Страница 404 ошибки (404.php)
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
					<h1> Ничего не найдено, но может Вас заинтересую вот эти книги(новости) <a class="btn btn-default btn-sm pull-right" href="<?php get_home_url();?>/allpost">Смотреть по категориям &raquo;</a></h1>
				</div>
				<div class="contetnt-loop">
			
						<?php $RandPost = new WP_Query(  array ( 'orderby' => 'rand', 'posts_per_page' => '3' ) );
							if ( $RandPost->have_posts() ) {
								while ( $RandPost->have_posts() ) {
									$RandPost->the_post();
						 ?>	
							<?php get_template_part('loop'); // для отображения каждой записи берем шаблон loop.php ?>

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