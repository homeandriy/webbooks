<?php
/**
 * Comment form login requirement.
 *
 * @package Webbooks
 */

?>
<p class="must-log-in col-12 alert alert-info mb-0">
	<?php esc_html_e( 'You must be logged in to comment.', 'webbooks' ); ?>
	<a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Log in', 'webbooks' ); ?></a>
</p>
