<?php
/**
 * Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
// подключаем header.php
get_header();
?>
<?php get_sidebar(); ?>
	<aside class="right-section">
        <style>
            .wp-caption-text {
                color: #aaa;
                font-size: 12px;
                text-align : center;
            }
        </style>
	<!-- Main content - Includes Featured Listings + Latest Listings -->
		<section class="content" itemscope itemtype="http://schema.org/Book">
			<?php 
			global $post;
			$getcat = get_the_category( $post->ID );
			$category_id = !isset($getcat[0]) ? 18 : $getcat[0]->cat_ID;

			while (have_posts()): the_post(); ?>
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
													<a href="<?php echo get_post_meta( $post->ID, 'buy', true )?>" class="btn btn-primary btn-sm" target="_blank"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>  Купить книгу </a>
												<?php endif; ?>
											</p>
										</div>
										<a href='http://citydomain.com.ua/?partner=user28504' title='Хостинг CityHost.ua' target='_blank'><img src='https://cityhost.ua/upload_img/ref_banners/cityhost_ua_970x120.jpg' title='Хостинг СитиХост' border='0' alt='Hosting CityHost'/></a>
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
														<td>
															 Автор книги
														</td>
														<td itemprop="author">
															 <?php echo get_post_meta( $post->ID, 'autor', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Год выхода:
														</td>
														<td itemprop="copyrightYear">
															 <?php echo get_post_meta( $post->ID, 'year', true ); ?>
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
															 <?php echo get_post_meta( $post->ID, 'create', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Язык:
														</td>
														<td itemprop="inLanguage">
                                                            <?php echo get_language(get_field('language')); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Статус:
														</td>
														<td>
															 <?php echo get_complexity(trim(get_field( "complexity"))); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Формат:
														</td>
														<td>
															 <?php echo get_post_meta( $post->ID, 'format', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Количество страниц:
														</td>
														<td itemprop="numberOfPages">
															 <?php echo get_post_meta( $post->ID, 'number_page', true ); ?>
														</td>
													</tr>
													<tr>
														<td>
															 Ссылка на скачивание
														</td>
														<td>
                                                            <?php
                                                            $link_to_download = get_post_meta( $post->ID, 'download', true );
                                                            $link_to_download = parse_url( $link_to_download, PHP_URL_PATH );
                                                            $link_to_download_array = explode("/", $link_to_download);

                                                            $link_to_download_key_path = $link_to_download_array[count($link_to_download_array) - 1];

                                                            ?>
														    <a
                                                                    href="<?php echo get_bloginfo('url') ?>/download/?key=<?php echo $link_to_download_key_path; ?>&count=<?php echo $post->ID;?>&cat=<?php echo $category_id  ?>"
                                                                    class="btn btn-primary btn-sm"
                                                                    target="_blank">
                                                                Скачать
                                                            </a>

														<?php if(!empty(get_post_meta($post->ID, 'buy', true))) {?>
															<a href="<?php echo get_post_meta( $post->ID, 'buy', true )?>" class="btn btn-primary btn-sm" target="_blank"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> Купить</a>
														<?php } ?>
														   
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
                                                    <?php if (function_exists('wp_get_shortlink')) : ?>
													<tr>
														<td>
															 <div><span class="post-shortlink">Сокращенная ссылка:</span></div>
														</td>
														<td>
                                                            <input type='text' value='<?php echo wp_get_shortlink(get_the_ID()); ?>' onclick='this.focus(); this.select();'  class="form-control"/>
														</td>
													</tr>
													<?php endif; ?>
												</tbody>
											</table>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i> Все книги представленные на сайте <a href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name'); ?></a> только в ознакомительных целях.
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
													<a href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name'); ?></a> 
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
											    <?php echo do_shortcode('[ninja_form id=3]'); ?>
                                            <?php endif; ?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i> Все книги представленные на сайте 
													<a href="<?php echo home_url(); ?>"><?php echo get_bloginfo('name'); ?></a> 
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
                                <?php $gallery = get_field('gallery');
                                if (!empty($gallery)) : ?>
                                    <img id="zoom_03" src="<?php echo $gallery['0']['sizes']['thumbnail']; ?>"
                                         class="first" data-zoom-image="<?php echo $gallery['0']['url']; ?>"
                                         alt="<?php echo $gallery['0']['alt']; ?>"/>
                                    <div id="gallery_01">
                                        <?php foreach ($gallery as $image): ?>
                                            <div>
                                                <a href="#" data-image="<?php echo $image['sizes']['thumbnail']; ?>"
                                                   data-zoom-image="<?php echo $image['url']; ?>">
                                                    <img id="zoom_03"
                                                         src="<?php echo $image['sizes']['small-thumb']; ?>"
                                                         alt="<?php echo $image['alt']; ?>"/>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <img id="zoom_03"
                                         src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>"
                                         data-zoom-image="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>"/>
                                    <div id="gallery_01">
                                        <div>
                                            <a href="#"
                                               data-image="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'); ?>"
                                               data-zoom-image="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>">
                                                <img id="zoom_03"
                                                     src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'small-thumb'); ?>"
                                                     alt="<?php echo get_the_title(get_the_ID()); ?>"/>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                                endif; ?>
                            </div>
					<div class="info-block">
                        <h3>Смотри также:</h3>
                        <?php
                        $queryInSingle = new WP_Query(array(
                            'posts_per_page' => 5,
                            'post_status' => 'publish',
                            'orderby' => 'rand',
                            'category__in' => $category_id,
                            'post__not_in' => array($id_current_post),

                        ));
                        // Цикл
                        if ( $queryInSingle->have_posts() ) {
                            while ( $queryInSingle->have_posts() ) {
                                $queryInSingle->the_post(); ?>
                                <div class="list-group">
                                    <a href="<?php echo get_the_permalink(); ?>" class="list-group-item">
                                        <h4 class="list-group-item-heading">
                                            <i class="fa fa-arrow-circle-right"></i>
                                            <?php echo get_the_title(); ?>
                                        </h4>
                                        <p class="list-group-item-text">
                                            <?php
                                            echo wp_trim_words( get_the_content(), 10, ' ...' );
                                            ?>
                                        </p>
                                    </a>
                                </div>
                                <?php
                            }
                        }; ?>
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
?>