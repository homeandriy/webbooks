<?php
/**
 * Шаблон обычной страницы (page.php)
 * @package WordPress
 * @subpackage your-clean-template
 * Template Name: download-book
 */
get_header();

?>
<?php get_sidebar();?>
<aside class="right-section">
	<!-- Main content - Includes Featured Listings + Latest Listings -->
	<section class="content">

		<!-- Start Latest Listings Section -->
		<div class="container-fluid mrg-tb white-bg">
			<div class="row">
				<div class="col-md-12 section-title ">
					<h1 class="post-title entry-title">Скачивание <strong>"<?php echo get_the_title($_GET['count']) ?>"</strong>. Пожалуйста ожидайте, скоро появится ссылка:</h1>
				</div>       			                           
               	<div class="container-fluid" >
                       	<!-- seconds --> 
                       	<h2 class="post-title entry-title text-center text-uppercase" id="countdown">Осталось секунд: <span></span></h2>
                       	<h2><div id="countdown"></div></h2>
                       	
                       	<div class="panel-body">
                       		<?php //comment_form (array(), $_GET['count']); ?>
                       	</div>


                       	<div class="text-center">
							<a href='http://citydomain.com.ua/?partner=user28504' title='Хостинг CityHost.ua' target='_blank'><img src='https://cityhost.ua/upload_img/ref_banners/cityhost_ua_970x120.jpg' title='Хостинг СитиХост' border='0' alt='Hosting CityHost'/></a>
                       	</div>
               	</div>
           	  	<div>
           	  		<h2 id="link" class="text-center"></h2>
           	  	</div>
           	  	<div  class="col-md-12 section-title ">
	               	<div class="list-group">
		          		<a href="<?php echo get_the_permalink($_GET['count']); ?>" class="list-group-item active" target="_blank">
			          		<div class="row">
			          			<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
			          				<?php 
			          				$url = wp_get_attachment_url( get_post_thumbnail_id($_GET['count']) );
			          				?>
			          			  	<img width="128" height="180" class="media-object"  src="<?php echo $url; ?>" alt="...">
			          			</div>
			          	
			          			<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
			          				<h4 class="list-group-item-heading"><?php echo get_the_title($_GET['count'])  ?></h4>
			          			</div>
		          			</div>
			          	</a>
			        </div>
		        </div>
           	  	<div  class="col-md-12 section-title ">
           	  		<h3 class="post-title entry-title">Также вам должно понравится: (откроется в новой вкладке)</h3>
					<?php 
						// Поулчить текущую категорию, для виборки
						if (isset($_GET['cat'])  or $_GET['cat'] <> "") 
						{
							$ThisID =  $_GET['cat'];
						}
						else
						{
							$ThisID = 69;
						}
						
						$argsToQuery1 = array
						(
						    'posts_per_page' => 5,
						    'orderby' => 'title',
						    'category__in' => $ThisID,
						    'post_status' => 'publish',
						    'orderby' => 'rand',
						);
						$queryInDownload = new WP_Query( $argsToQuery1 );
						// Цикл
						if ( $queryInDownload->have_posts() )
						{
						    while ( $queryInDownload->have_posts() )
						    {
						        $queryInDownload->the_post();
						         
						        ?>
						        <div class="list-group">
						          <a href="<?php echo get_the_permalink(); ?>" class="list-group-item active" target="_blank">
						          	<div class="row">
						          		<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
						          			<?php 
						          				$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );						          				
						          			?>
						          			  <img width="128" height="180" class="media-object"  src="<?php echo $url; ?>" alt="...">
						          		</div>
						          	
						          		<div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
						          			<h4 class="list-group-item-heading"><?php echo get_the_title()  ?></h4>
						          			<p class="list-group-item-text" style="color: #fff!important">
						          			<?php 
								                $currentShortNews1 = get_the_content();
								                $currentShortNews1 = strip_tags($currentShortNews1);
								                $currentShortNews1 = substr($currentShortNews1, 0, 500);
								                $currentShortNews1 = rtrim($currentShortNews1, "!,.-");
								                $currentShortNews1 = substr($currentShortNews1, 0, strrpos($currentShortNews1, ' '));                                            
								                echo $currentShortNews1."… ";
								             ?>
						          			</p>
						          		</div>
						          	</div>					            
						          </a>
						        </div>						        
						        
						        <?php                                 
					    	}
						}
						else 
						{
						    // Постов не найдено
						}
					 	?>	
               	  	</div>
                </div>
				
			</div>
		</div>		
	</section>
	<!-- right col -->
</aside>

		
		<?php get_footer(); // подключаем footer.php ?>