<?php
/**
 * Comments template.
 *
 * @package Webbooks
 */

$recaptcha_configured = \Webbooks\Comment\CommentSecurity::isRecaptchaConfigured();
?>
<section id="comments" class="comments-area">
	<p class="comments-area__total h5 mb-4">
		<?php echo esc_html__( 'Total comments:', 'webbooks' ) . ' ' . (int) get_comments_number(); ?>
	</p>

	<?php if ( have_comments() ) : ?>
		<ul class="comment-list list-unstyled vstack gap-3">
			<?php
			wp_list_comments(
				array(
					'walker'      => new \Webbooks\Comment\CleanCommentsWalker(),
					'style'       => 'ul',
					'avatar_size' => 64,
					'max_depth'   => 4,
				)
			);
			?>
		</ul>

		<?php
		$comment_pagination = paginate_comments_links(
			array(
				'prev_text' => esc_html__( 'Previous', 'webbooks' ),
				'next_text' => esc_html__( 'Next', 'webbooks' ),
				'type'      => 'list',
				'echo'      => false,
			)
		);

		if ( is_string( $comment_pagination ) ) {
			echo webbooks_render_template_part( 'template-parts/navigation/pagination', array( 'links' => $comment_pagination ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template escapes pagination markup.
		}
		?>
	<?php endif; ?>

	<?php if ( comments_open() && $recaptcha_configured ) : ?>
		<?php
		$commenter           = wp_get_current_commenter();
		$logged_in_user      = wp_get_current_user();
		$user_display_name   = $logged_in_user instanceof WP_User ? $logged_in_user->display_name : '';
		$recaptcha_site_key  = \Webbooks\Comment\CommentSecurity::getRecaptchaSiteKey();
		$comment_nonce       = wp_nonce_field(
			\Webbooks\Comment\CommentSecurity::NONCE_ACTION,
			\Webbooks\Comment\CommentSecurity::NONCE_NAME,
			true,
			false
		);
		$fields              = array(
			'author' => webbooks_render_template_part(
				'template-parts/comments/form-text-field',
				array(
					'id'           => 'author',
					'name'         => 'author',
					'label'        => __( 'Name', 'webbooks' ),
					'value'        => $commenter['comment_author'],
					'autocomplete' => 'name',
					'type'         => 'text',
				)
			),
			'email'  => webbooks_render_template_part(
				'template-parts/comments/form-text-field',
				array(
					'id'           => 'email',
					'name'         => 'email',
					'label'        => __( 'Email', 'webbooks' ),
					'value'        => $commenter['comment_author_email'],
					'autocomplete' => 'email',
					'type'         => 'email',
				)
			),
		);
		$comment_form_fields = apply_filters( 'comment_form_default_fields', $fields );
		unset( $comment_form_fields['cookies'] );

		$comment_form_args = array(
			'fields'               => $comment_form_fields,
			'comment_field'        => webbooks_render_template_part(
				'template-parts/comments/form-comment-field',
				array(
					'nonce'              => $comment_nonce,
					'recaptcha_site_key' => $recaptcha_site_key,
					'show_privacy'       => ! is_user_logged_in(),
				)
			),
			'must_log_in'          => webbooks_render_template_part( 'template-parts/comments/form-must-log-in' ),
			'logged_in_as'         => webbooks_render_template_part( 'template-parts/comments/form-logged-in-as', array( 'user_display_name' => $user_display_name ) ),
			'comment_notes_before' => webbooks_render_template_part( 'template-parts/comments/form-notes' ),
			'class_form'           => 'comment-form row g-3',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'class_submit'         => 'btn btn-info comment-form__submit',
			'title_reply'          => esc_html__( 'Leave a comment', 'webbooks' ),
			/* translators: %s: Author name for reply target. */
			'title_reply_to'       => esc_html__( 'Reply to %s', 'webbooks' ),
			'title_reply_before'   => '<h2 id="reply-title" class="comment-reply-title h4 col-12 mb-0">',
			'title_reply_after'    => '</h2>',
			'cancel_reply_link'    => esc_html__( 'Cancel reply', 'webbooks' ),
			'cancel_reply_before'  => ' <small class="comment-reply-cancel">',
			'cancel_reply_after'   => '</small>',
			'label_submit'         => esc_html__( 'Submit comment', 'webbooks' ),
			'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			'submit_field'         => '<p class="form-submit col-12 mb-0">%1$s %2$s</p>',
		);
		comment_form( $comment_form_args );
		?>
	<?php elseif ( comments_open() ) : ?>
		<?php echo webbooks_render_template_part( 'template-parts/comments/form-unavailable' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template contains translated static markup. ?>
	<?php endif; ?>
</section>
