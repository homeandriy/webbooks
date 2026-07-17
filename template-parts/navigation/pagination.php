<?php
/**
 * Main query pagination.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args Template arguments.
 */

$links = is_string( $args['links'] ?? null ) ? $args['links'] : '';

if ( '' === $links ) {
	return;
}
?>
<nav class="webbooks-pagination" aria-label="<?php esc_attr_e( 'Pagination', 'webbooks' ); ?>">
	<?php echo wp_kses_post( $links ); ?>
</nav>
