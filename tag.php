<?php
/**
 * tag template (tag.php)
 * @package WordPress
 * @subpackage webbooks
 */
get_header(); ?>
<section>
	<h1><?php printf('Посты с тэгом: %s', single_tag_title('', false)); ?></h1>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<?php get_template_part('template/loop'); ?>
	<?php endwhile;
	else: echo '<h2>Нет записей.</h2>'; endif;?>
	<?php pagination();?>
</section>
<?php get_sidebar(); ?>
<?php get_footer();
