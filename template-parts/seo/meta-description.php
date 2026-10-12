<?php
/**
 * Meta description template.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args
 */

$description = (string) ( $args['description'] ?? '' );
?>
<meta name="description" content="<?php echo esc_attr( $description ); ?>" />
