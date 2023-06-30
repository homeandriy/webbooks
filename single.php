<?php
/**
 * Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage your-clean-template
 */

get_header();
?>
<?php get_sidebar(); ?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
    <section class="content" itemscope itemtype="http://schema.org/Book">
        <?php
        global $post;
            $current_category = get_the_category( $post->ID );
            $category_id      = !isset($current_category[0]) ? 18 : $current_category[0]->cat_ID;
        ?>
        <?php while (have_posts()): ?>
            <?php the_post(); ?>
			<!-- Start Page Heading -->
			<div class="section bg-brown-lighten ">
				<div class="container-fluid">
					<div class=" col-md-8">
						<h1 <?php post_class('post-title entry-title'); ?> id="title" itemprop="name"><?php the_title(); ?> </h1>
					</div>
				</div>
			</div>
			<!-- ./ Page Header -->
			<!-- Start Main Section -->
			<div class="bg-brown-lighten bdr-b container-fluid">
				<!-- Start Nav Tabs -->
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<li role="presentation" class="active"><a href="#description" aria-controls="description" role="tab" data-toggle="tab">Описания</a></li>
					<li role="presentation"><a href="#comments1" aria-controls="comments1" role="tab" data-toggle="tab">Описание/Скачать</a></li>
					<li role="presentation"><a href="#com_list" aria-controls="com_list" role="tab" data-toggle="tab">Обсуждения</a></li> 
					<li role="presentation"><a href="#bad_book" aria-controls="bad_book" role="tab" data-toggle="tab">Пожаловатся</a></li> 
				</ul>
			<!-- ./  Nav Tabs -->
			</div>
			<!-- Start Tab Panels -->
			<div class="main-section">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-9">
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane fade active in" id="description">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">                                        
											<p itemprop="description">                                            
												<?php the_content(); ?>

												<?php if ( ! empty( get_post_meta( $post->ID, 'buy', true ) ) ) : ?>
													<a href="<?= get_post_meta( $post->ID, 'buy', true )?>" class="btn btn-primary btn-sm" target="_blank"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>  Купить книгу </a>
												<?php endif; ?>
											</p>
										</div>
										<a href='https://gmhost.com.ua/?partner=29021' title='Хостинг GM Host' target='_blank'>
                                            <img src='<?= get_stylesheet_directory_uri(); ?>/img/banner_servers_728_90.jpg' title='Хостинг GM Host' border='0' alt='Hosting GM Host'/>
                                        </a>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="comments1">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">
											<!-- Table -->
											<table class="table">
												<tbody>
													<tr>
														<td>Автор книги</td>
														<td itemprop="author">
															 <?= get_post_meta( $post->ID, 'autor', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Год выхода:
														</td>
														<td itemprop="copyrightYear">
															 <?= get_post_meta( $post->ID, 'year', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Жанр:
														</td>
														<td itemprop="genre">
															 <?php 
															 if ( function_exists( 'yoast_breadcrumb' ) ) {
																yoast_breadcrumb();
															 }
															 ?>
														</td>
													</tr>
													<tr>
														<td>
															 Издательство:
														</td>
														<td>
															 <?= get_post_meta( $post->ID, 'create', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Язык:
														</td>
														<td itemprop="inLanguage">
                                                            <?= Class_Book::get_language(get_field('language')); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Статус:
														</td>
														<td>
															 <?= Class_Book::get_complexity(trim(get_field( "complexity"))); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Формат:
														</td>
														<td>
															 <?= get_post_meta( $post->ID, 'format', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Количество страниц:
														</td>
														<td itemprop="numberOfPages">
															 <?= get_post_meta( $post->ID, 'number_page', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Ссылка на скачивание
														</td>
														<td>
                                                            <?= apply_filters('get_download_link', $post, $category_id); ?>

															<?php
															if ( ! empty( get_post_meta( $post->ID, 'buy', true ) ) ) : ?>
                                                                <a
                                                                        href="<?= get_post_meta( $post->ID, 'buy', true ) ?>"
                                                                        class="btn btn-primary btn-sm"
                                                                        target="_blank">
                                                                    <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Купить
                                                                </a>
															<?php
															endif; ?>
														</td>
													</tr>
													<tr>
														<td>
															 На сайт предоставил
														</td>
														<td>
															<time datetime="<?php the_time('o-m-d') ?>" class="post-date updated"><?php the_time(apply_filters('themify_loop_date', 'M j, Y H:i')) ?></time>
															<!-- Для решения ошибки с author -->
															<span class="vcard author">
															  <span class="fn"><?= get_the_author(); ?></span>
															</span>
														</td>
													</tr>
												</tbody>
											</table>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i> Все книги представленные на сайте <a href="<?= home_url(); ?>"><?= get_bloginfo('name'); ?></a> только в ознакомительных целях.
													Любое их использование Вами допускается только в ознакомительных целях. Если Вы планируете их использовать в дальнейшем, 
													то Вы обязаны приобрести их у правообладателей. Администрация сайта не несет ответственность за их использование Вами
												</strong>
											</p>                                   
										</div>
										<div class="collapse" id="collapseExample"><div class="panel-body"></div></div>
										<!-- List group -->                                
									</div>
								</div>

								<div role="tabpanel" class="tab-pane fade" id="com_list">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">
                                            <?php if (comments_open() ) : ?>
											    <?php comments_template(); ?>
                                            <?php endif; ?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i> Все книги представленные на сайте 
													<a href="<?= home_url(); ?>"><?= get_bloginfo('name'); ?></a> 
													только в ознакомительных целях. 
													Любое их использование Вами допускается только в ознакомительных целях. Если Вы планируете их использовать в дальнейшем, 
													то Вы обязаны приобрести их у правообладателей. Администрация сайта не несет ответственность за их использование Вами
												</strong>
											</p>                                   
										</div>
										<div class="collapse" id="collapseExample"><div class="panel-body"></div></div>
										<!-- List group -->                                
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="bad_book">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">
                                            <?php if (function_exists('do_shortcode') ) : ?>
											    <?= do_shortcode('[ninja_form id=3]'); ?>
                                            <?php endif; ?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i> Все книги представленные на сайте 
													<a href="<?= home_url(); ?>"><?= get_bloginfo('name'); ?></a>
													только в ознакомительных целях. 
													Любое их использование Вами допускается только в ознакомительных целях. Если Вы планируете их использовать в дальнейшем, 
													то Вы обязаны приобрести их у правообладателей. Администрация сайта не несет ответственность за их использование Вами
												</strong>
											</p>                                   
										</div>
										<div class="collapse" id="collapseExample"><div class="panel-body"></div></div>
										<!-- List group -->                                
									</div>
								</div>
                            </div>
						</div>   <!-- ./ col-md-6 -->
						<!-- ./ Tab Panels -->
						<!-- Start Photo Gallery -->
						<div class="col-md-3">
                            <div class="panel panel-default mrg-t">
                                <?= apply_filters('post_gallery', $post); ?>
                            </div>
                            <div class="info-block">
                                <h3>Смотри также:</h3>
                                <?php
                                $related = new WP_Query(
                                    [
                                        'posts_per_page' => 5,
                                        'post_status'    => 'publish',
                                        'orderby'        => 'rand',
                                        'category__in'   => $category_id,
                                        'post__not_in'   => [ $post->ID ],

                                    ]
                                ); ?>

                                <?php if ( $related->have_posts() ) : ?>
                                    <?php while ( $related->have_posts() ) : ?>
                                        <?php $related->the_post(); ?>
                                        <div class="list-group">
                                            <a href="<?= get_the_permalink(); ?>" class="list-group-item">
                                                <h4 class="list-group-item-heading">
                                                    <i class="fa fa-arrow-circle-right"></i>
                                                    <?= get_the_title(); ?>
                                                </h4>
                                                <p class="list-group-item-text">
                                                    <?= wp_trim_words( get_the_content(), 10, ' ...' ); ?>
                                                </p>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
				        </div>
			        </div>
		        </div>
            </div>
	    <?php endwhile;?>
    </section>
	<!-- right col -->
</aside>
<?php
get_footer();
