<?php
/*
Template Name: Triqui Ajax Post
 */
?>
<?php
$post = get_post($_POST['id']);
?>
<?php if ($post): ?>
	<?php setup_postdata($post);?>
	<div <?php post_class()?> id="post-<?php the_ID();?>">
		<h2><?php the_title();?></h2>
		<small><?php the_time('F jS, Y')?> <!-- by <?php the_author()?> --></small>

		<div class="entry">
			<?php the_content('Read the rest of this entry &raquo;');?>
		</div>

		<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />');?> Posted in <?php the_category(', ')?> | <?php edit_post_link('Edit', '', ' | ');?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;');?></p>
	</div>
<?php endif;?>