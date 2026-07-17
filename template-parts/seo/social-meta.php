<?php
/**
 * Open Graph and Twitter fallback metadata.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args
 */

$title       = (string) ( $args['title'] ?? '' );
$description = (string) ( $args['description'] ?? '' );
$image       = (string) ( $args['image'] ?? '' );
$url         = (string) ( $args['url'] ?? '' );
$site_name   = (string) ( $args['site_name'] ?? '' );
$type        = (string) ( $args['type'] ?? 'website' );
?>
<meta property="og:type" content="<?php echo esc_attr( $type ); ?>" />
<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
<meta property="og:description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>" />
<meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
<meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
<meta property="og:image" content="<?php echo esc_url( $image ); ?>" />
<meta name="twitter:card" content="<?php echo esc_attr( '' !== $image ? 'summary_large_image' : 'summary' ); ?>" />
<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
<meta name="twitter:description" content="<?php echo esc_attr( wp_strip_all_tags( $description ) ); ?>" />
<meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" />
