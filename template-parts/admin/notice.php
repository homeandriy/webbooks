<?php
/**
 * Admin notice template.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args
 */

$notice_type    = sanitize_html_class( (string) ( $args['type'] ?? 'warning' ) );
$notice_message = (string) ( $args['message'] ?? '' );
?>
<div class="notice notice-<?php echo esc_attr( $notice_type ); ?>"><p><?php echo esc_html( $notice_message ); ?></p></div>
