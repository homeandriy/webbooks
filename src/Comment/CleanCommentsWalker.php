<?php
/**
 * Minimal comment walker used by the public comment template.
 *
 * @package Webbooks
 */

namespace Webbooks\Comment;

/**
 * Renders the theme's nested comment markup.
 */
class CleanCommentsWalker extends \Walker_Comment {

	/**
	 * Start a nested comment list.
	 *
	 * @param string              $output Current markup.
	 * @param int                 $depth  Nesting depth.
	 * @param array<string,mixed> $args   Walker arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ): void {
		$output .= webbooks_render_template_part( 'template-parts/comments/list-open' );
	}

	/**
	 * End a nested comment list.
	 *
	 * @param string              $output Current markup.
	 * @param int                 $depth  Nesting depth.
	 * @param array<string,mixed> $args   Walker arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ): void {
		$output .= webbooks_render_template_part( 'template-parts/comments/list-close' );
	}

	/**
	 * Render a single comment.
	 *
	 * @param WP_Comment          $comment Comment object.
	 * @param int                 $depth   Nesting depth.
	 * @param array<string,mixed> $args    Walker arguments.
	 */
	protected function comment( $comment, $depth, $args ): void {
		$classes = implode( ' ', get_comment_class( '', $comment ) ) . ( get_the_author_meta( 'email' ) === $comment->comment_author_email ? ' author-comment' : '' );
		$reply_markup = get_comment_reply_link(
			array_merge(
				$args,
				array(
					'depth'      => $depth,
					'max_depth'  => $args['max_depth'] ?? 4,
					'reply_text' => esc_html__( 'Reply', 'webbooks' ),
					'login_text' => esc_html__( 'You must be logged in to reply.', 'webbooks' ),
				)
			)
		);

		echo webbooks_render_template_part(
			'template-parts/comments/comment',
			array(
				'comment'      => $comment,
				'classes'      => trim( $classes ),
				'reply_markup' => $reply_markup,
			)
		);
	}

	/**
	 * End a comment element.
	 *
	 * @param string              $output  Current markup.
	 * @param WP_Comment          $comment Comment object.
	 * @param int                 $depth   Nesting depth.
	 * @param array<string,mixed> $args    Walker arguments.
	 */
	public function end_el( &$output, $comment, $depth = 0, $args = array() ): void {
		$output .= webbooks_render_template_part( 'template-parts/comments/element-close' );
	}
}
