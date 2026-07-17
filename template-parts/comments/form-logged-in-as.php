<?php
/**
 * Logged-in comment form context.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args Template arguments.
 */

$user_display_name = is_string( $args['user_display_name'] ?? null ) ? $args['user_display_name'] : '';
?>
<p class="logged-in-as col-12 mb-0">
	<?php esc_html_e( 'You are logged in as', 'webbooks' ); ?>
	<a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>"><?php echo esc_html( $user_display_name ); ?></a>.
	<a href="<?php echo esc_url( wp_logout_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Log out?', 'webbooks' ); ?></a>
</p>
