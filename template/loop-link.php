<?php
/**
 * Запись в цикле (loop.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
?>
<div class="list-group">
  <div  class="list-group-item ">
  	<div class="row">
  		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
  			<?php 
  				$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );						          				
  			?>
  			  <img width="390" height="440" class="media-object"  src="<?= $url; ?>" alt="...">
  		</div>
  	
  		<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
  			<h4 class="list-group-item-heading"> <a href="<?= get_the_permalink(); ?>"><?= get_the_title()  ?></a> </h4>
  			<p class="list-group-item-text">
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
  		<div class="next-reed">
  			<p>
  				<a href="<?= get_the_permalink(); ?>" class="btn navbar-btn btn-info navbar-right">Дальше</a>
  			</p>
  			<a href="<?= get_bloginfo('url') ?>/download/?key=<?= $linkToDownloadarray; ?>&count=<?= $post->ID;?>&cat=<?= $post_x[0]->cat_ID  ?>" class="btn btn-primary btn-sm" target="_blank">Скачать</a>
  		</div>
  	</div>					            
  </div>
</div>
