<?php
/**
 * Tag template (tag.php)
 *
 * @package WordPress
 * @subpackage webbooks
 */

get_header(); ?>
<section>
	<h1><?php /* translators: %s: Tag name. */ printf( esc_html__( 'Posts tagged: %s', 'webbooks' ), esc_html( single_tag_title( '', false ) ) ); ?></h1>
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<?php get_template_part( 'template/loop' ); ?>
			<?php
	endwhile;
	else :
		echo '<h2>' . esc_html__( 'No posts found.', 'webbooks' ) . '</h2>';
endif;
	?>
	<?php \Webbooks\Theme\Setup::pagination(); ?>
</section>
<?php get_sidebar(); ?>
<?php
get_footer();
