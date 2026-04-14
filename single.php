<?php
/**
 * Шаблон отдельной записи (single.php)
 * @package WordPress
 * @subpackage webbooks
 */

get_header();

global $post;
$current_category = get_the_category($post->ID);
$category_id      = ! isset($current_category[0]) ? 18 : $current_category[0]->cat_ID;
$books_disclaimer = __( 'Все книги представленные на сайте только в ознакомительных целях. Любое их использование Вами допускается только в ознакомительных целях. Если Вы планируете их использовать в дальнейшем, то Вы обязаны приобрести их у правообладателей. Администрация сайта не несет ответственность за их использование Вами', 'webbooks' );
$book_meta_fallback = __( 'Не вказано', 'webbooks' );
?>
<?php get_sidebar(); ?>
<aside class="right-section">
    <section class="content">
        <?php while (have_posts()): ?>
            <?php the_post(); ?>
			<?php
			$book_meta = \Webbooks\Book\BookMeta::getNormalizedMeta( $post->ID );
			$book_author = $book_meta['author'] !== '' ? $book_meta['author'] : $book_meta_fallback;
			$book_year_display = $book_meta['year'] !== '' ? $book_meta['year'] : $book_meta_fallback;
			$book_format = $book_meta['format'] !== '' ? $book_meta['format'] : $book_meta_fallback;
			$book_language = $book_meta['language'] !== '' ? $book_meta['language'] : $book_meta_fallback;
			$book_pages_display = $book_meta['pages'] !== null ? (string) $book_meta['pages'] : $book_meta_fallback;
			$book_schema = \Webbooks\Book\BookMeta::getBookSchema( $post->ID );
			?>
			<script type="application/ld+json"><?php echo wp_json_encode( $book_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
			<!-- Start Page Heading -->
			<div class="section bg-brown-lighten ">
				<div class="container-fluid">
					<div class="col-sm-12 col-md-8 col-lg-8">
						<h1 <?php post_class('post-title entry-title'); ?> id="title"><?php the_title(); ?></h1>
					</div>
				</div>
			</div>
			<!-- ./ Page Header -->
			<!-- Start Main Section -->
			<div class="bg-brown-lighten bdr-b container-fluid">
				<!-- Start Nav Tabs -->
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<li role="presentation" class="active"><a href="#description-section" aria-controls="description-section" role="tab" data-toggle="tab"><?php esc_html_e( 'Описания', 'webbooks' ); ?></a></li>
					<li role="presentation"><a href="#download-section" aria-controls="download-section" role="tab" data-toggle="tab"><?php esc_html_e( 'Описание/Скачать', 'webbooks' ); ?></a></li>
					<li role="presentation"><a href="#user-comments-section" aria-controls="user-comments-section" role="tab" data-toggle="tab"><?php esc_html_e( 'Обсуждения', 'webbooks' ); ?></a></li>
					<li role="presentation"><a href="#book-warning-section" aria-controls="book-warning-section" role="tab" data-toggle="tab"><?php esc_html_e( 'Пожаловатся', 'webbooks' ); ?></a></li>
				</ul>
			<!-- ./  Nav Tabs -->
			</div>
			<!-- Start Tab Panels -->
			<div class="main-section">
				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-12 col-md-9 col-lg-9">
							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane fade active in" id="description-section">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">                                        
											<p>                                            
												<?php echo wp_kses_post( get_the_content() ); ?>
                                            </p>
                                            <p>
												<?php if ( ! empty( get_post_meta( $post->ID, 'buy', true ) ) ) : ?>
													<a href="<?= esc_url( get_post_meta( $post->ID, 'buy', true ) ) ?>" class="btn btn-primary btn-sm" target="_blank"><span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span><?php esc_html_e( 'Купить книгу', 'webbooks' ); ?></a>
												<?php endif; ?>
											</p>
                                            <div class="mrg-t">
                                                <a href='https://gmhost.com.ua/?partner=29021' title='Хостинг GM Host' target='_blank' rel="nofollow">
                                                    <img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/banner_servers_728_90.jpg' ) ?>" title='Хостинг GM Host' border='0' alt='Hosting GM Host' loading="lazy"/>
                                                </a>
                                            </div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="download-section">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">
											<!-- Table -->
											<table class="table table-hover">
												<tbody>
													<tr>
														<td><?php esc_html_e( 'Автор книги:', 'webbooks' ); ?></td>
														<td><?= esc_html( $book_author ) ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Год выхода:', 'webbooks' ); ?></td>
														<td><?= esc_html( $book_year_display ); ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Жанр:', 'webbooks' ); ?></td><td>
															 <?php 
															 if ( function_exists( 'yoast_breadcrumb' ) ) {
																yoast_breadcrumb();
															 }
															 ?>
														</td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Издательство:', 'webbooks' ); ?></td>
														<td><?= esc_html( get_post_meta( $post->ID, 'create', true ) ) ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Язык:', 'webbooks' ); ?></td>
														<td><?= esc_html( $book_language ) ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Статус:', 'webbooks' ); ?></td>
														<td><?= esc_html( \Webbooks\Book\BookMeta::getComplexity( trim( get_field( 'complexity' ) ) ) ) ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Формат:', 'webbooks' ); ?></td>
														<td><?= esc_html( $book_format ) ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Количество страниц:', 'webbooks' ); ?></td>
														<td><?= esc_html( $book_pages_display ) ?></td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'Ссылка на скачивание:', 'webbooks' ); ?></td>
														<td>
                                                            <?=apply_filters('get_download_link', $post, $category_id)?>
															<?php
															if ( ! empty( get_post_meta( $post->ID, 'buy', true ) ) ) : ?>
                                                                <a
                                                                        href="<?= esc_url( get_post_meta( $post->ID, 'buy', true ) ) ?>"
                                                                        class="btn btn-primary btn-sm"
                                                                        target="_blank"
                                                                >
                                                                    <span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span> <?php esc_html_e( 'Купить', 'webbooks' ); ?>
                                                                </a>
															<?php
															endif; ?>
														</td>
													</tr>
													<tr>
														<td><?php esc_html_e( 'На сайт предоставил:', 'webbooks' ); ?></td>
														<td>
															<time datetime="<?php the_time('o-m-d') ?>" class="post-date updated"><?php the_time(apply_filters('themify_loop_date', 'M j, Y H:i')) ?></time>
															<!-- Для решения ошибки с author -->
															<span class="vcard author">
															  <span class="fn">Andrii</span>
															</span>
														</td>
													</tr>
												</tbody>
											</table>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i>
                                                    <?php echo esc_html( $books_disclaimer ); ?>
                                                    <a href="<?= esc_url( home_url() ) ?>"><?= esc_html( get_bloginfo( 'name' ) ); ?></a>
												</strong>
											</p>                                   
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="user-comments-section">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">
                                            <?php if (comments_open() ) : ?>
											    <?php comments_template(); ?>
                                            <?php endif; ?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i>
                                                    <?php echo esc_html( $books_disclaimer ); ?>
													<a href="<?= esc_url( home_url() ); ?>"><?= esc_html( get_bloginfo( 'name' ) ); ?></a>
												</strong>
											</p>                                   
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="book-warning-section">
									<div class="panel panel-default bdr-t-none">
										<!-- Default panel contents -->
										<div class="panel-body">
                                            <?php if (function_exists('do_shortcode') ) : ?>
											    <?= do_shortcode('[ninja_form id=3]'); ?>
                                            <?php endif; ?>
											<p>
												<strong>
													<i class="fa fa-exclamation-circle"></i>
                                                    <?php echo esc_html( $books_disclaimer ); ?>
													<a href="<?= esc_url( home_url() ); ?>"><?= esc_html( get_bloginfo( 'name' ) ); ?></a>
												</strong>
											</p>                                   
										</div>
										<div class="collapse" id="collapseExample"><div class="panel-body"></div></div>
										<!-- List group -->                                
									</div>
								</div>
                            </div>
                            <!-- ./ Tab Panels -->
                            <div class="relation-section mrg-t">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12">
                                        <div class="panel">
                                            <div class="panel-heading panel-warning">
                                                <div class="panel-title text-center"><strong><?php esc_html_e( 'Книги с этого раздела:', 'webbooks' ); ?></strong></div>
                                            </div>
                                            <div class="panel-body">
                                                <?php   $related = new WP_Query(
	                                                [
		                                                'posts_per_page' => 6,
		                                                'post_status'    => 'publish',
		                                                'orderby'        => 'rand',
		                                                'category__in'   => $category_id,
		                                                'post__not_in'   => [ $post->ID ],

	                                                ]
                                                ); ?>
                                                <?php $i = 0; ?>
                                                <?php while ( $related->have_posts() ) : ?>
                                                    <?php $related->the_post(); ?>
                                                    <?php if ($i === 0 || $i === 3): ?>
                                                    <div class="row">
                                                    <?php endif; ?>
                                                    <div class="col-sm-6 col-md-4 col-lg-4">
                                                        <div class="thumbnail">
                                                            <img src="<?= esc_url( get_the_post_thumbnail_url() ) ?>" alt="<?= esc_attr( get_the_title() ) ?>" class="thumbnail" loading="lazy">
                                                            <div class="caption">
                                                                <h3><?= esc_html( get_the_title() ) ?></h3>
                                                                <p><?= wp_kses_post( wp_trim_words( get_the_content(), 20, ' ...' ) ); ?></p>
                                                                <p><a href="<?= esc_url( get_the_permalink() ) ?>" class="btn btn-success" role="button"><?php esc_html_e( 'Перейти', 'webbooks' ); ?></a></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if ($i === 2 || $i === 5): ?>
                                                    </div>
                                                    <?php endif; ?>
                                                <?php $i++; ?>
                                                <?php endwhile; ?>
                                                <?php wp_reset_postdata(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="relation-section mrg-t">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel">
                                            <div class="panel-heading">
                                                <div class="panel-title text-center"><strong><?php esc_html_e( 'Наши партнери', 'webbooks' ); ?></strong></div>
                                            </div>
                                            <div class="panel-body">
                                                <a href="https://gmhost.ua/?partner=29021" target="_blank">
                                                    <img src="<?= esc_url( get_banner_src() ); ?>" class="image" loading="lazy" alt="GM Host Banner">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						</div>
						<!-- ./ Tab Panels -->
						<!-- Start Photo Gallery -->
						<div class="col-sm-12 col-md-3 col-lg-3">
                            <div class="panel panel-default mrg-t">
                                <div class="panel-body">
                                    <?= wp_kses_post( apply_filters( 'post_gallery', $post ) ) ?>
                                </div>
                            </div>
                            <div class="info-block">
                                <h3><?php esc_html_e( 'Смотри также:', 'webbooks' ); ?></h3>
                                <?php
                                $related = new WP_Query(
	                                [
		                                'posts_per_page' => 6,
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
                                            <a href="<?= esc_url( get_the_permalink() );?>" class="list-group-item">
                                                <h4 class="list-group-item-heading">
                                                    <i class="fa fa-arrow-circle-right"></i>
                                                    <?= esc_html( get_the_title() ); ?>
                                                </h4>
                                                <p class="list-group-item-text">
                                                    <?= wp_kses_post( wp_trim_words( get_the_content(), 10, ' ...' ) ); ?>
                                                </p>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                                <?php wp_reset_postdata();?>
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
