<?php
/**
 * Single comment markup.
 *
 * @package Webbooks
 *
 * @var array<string, mixed> $args
 */

$comment     = $args['comment'] ?? null;
$classes     = (string) ( $args['classes'] ?? '' );
$reply_markup = (string) ( $args['reply_markup'] ?? '' );
if ( ! $comment instanceof WP_Comment ) {
	return;
}
?>
<li id="li-comment-<?php echo (int) $comment->comment_ID; ?>" class="<?php echo esc_attr( $classes ); ?>">
	<div id="comment-<?php echo (int) $comment->comment_ID; ?>">
		<?php echo get_avatar( $comment, 64 ); ?>
		<p class="meta"><?php esc_html_e( 'Author:', 'webbooks' ); ?> <?php echo esc_html( get_comment_author( $comment ) ); ?> · <?php echo esc_html( get_comment_date( 'd.m.Y H:i', $comment ) ); ?></p>
		<?php if ( '0' === (string) $comment->comment_approved ) : ?><em class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'webbooks' ); ?></em><?php endif; ?>
		<?php comment_text( $comment ); ?>
		<?php echo wp_kses_post( $reply_markup ); ?>
	</div>
