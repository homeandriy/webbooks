<?php
/**
 * Шаблон комментариев (comments.php)
 * Выводит список комментариев и форму добавления
 * @package WordPress
 * @subpackage webbooks
 */

$recaptchaConfigured = function_exists( 'webbooks_is_recaptcha_configured' ) ? webbooks_is_recaptcha_configured() : false;
?>
<div id="comments">
	<span><?php echo esc_html__( 'Total comments:', 'webbooks' ) . ' ' . (int) get_comments_number(); ?></span>
	<?php if ( have_comments() ) : ?>
	<ul class="comment-list">
		<?php
			$args = array(
				'walker'      => new \Webbooks\Comment\CleanCommentsWalker(),
				'style'       => 'ul',
				'avatar_size' => 64,
				'max_depth'   => 4,
			);
			wp_list_comments( $args );
			?>
	</ul>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<?php
			$args = array(
				'prev_text' => '«',
				'next_text' => '»',
			);
			paginate_comments_links( $args );
			?>
	<?php endif; ?>
	<?php endif; ?>

	<?php
	if ( comments_open() && $recaptchaConfigured ) {
		$current_user        = wp_get_current_user();
		$user_display_name   = $current_user instanceof WP_User ? $current_user->display_name : '';
		$commenter          = wp_get_current_commenter();
		$comment_nonce      = wp_nonce_field( WEBBOOKS_COMMENT_NONCE_ACTION, WEBBOOKS_COMMENT_NONCE_NAME, true, false );
		$recaptcha_site_key = function_exists( 'webbooks_get_recaptcha_site_key' ) ? webbooks_get_recaptcha_site_key() : '';
		$recaptcha_block    = '';
		$privacy_block      = '';
		$emoji              = array( '😀', '😁', '😂', '😍', '👍', '🔥', '👏', '🤔', '😎', '🙏' );
		$emoji_buttons      = '';

		foreach ( $emoji as $item ) {
			$emoji_buttons .= '<button type="button" class="comment-emoji-btn" data-emoji="' . esc_attr( $item ) . '">' . esc_html( $item ) . '</button>';
		}

		if ( ! empty( $recaptcha_site_key ) ) {
			$recaptcha_block = '<div class="comment-captcha"><div class="g-recaptcha" data-sitekey="' . esc_attr( $recaptcha_site_key ) . '"></div></div>';
		}

		if ( ! is_user_logged_in() ) {
			$privacy_block = '<p class="comment-form-privacy"><label><input id="' . esc_attr( WEBBOOKS_COMMENT_PRIVACY_FIELD ) . '" name="' . esc_attr( WEBBOOKS_COMMENT_PRIVACY_FIELD ) . '" type="checkbox" value="1" required> ' . esc_html__( 'I agree to the privacy policy.', 'webbooks' ) . '</label></p>';
		}

		$fields = array(
			'author' => '<label for="author">' . esc_html__( 'Name', 'webbooks' ) . ' <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" required></label>',
			'email'  => '<label for="email">Email <input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" required></label>',
		);

		$args = array(
			'fields'               => apply_filters( 'comment_form_default_fields', $fields ),
			'comment_field'        => '<label for="comment">' . esc_html__( 'Comment:', 'webbooks' ) . ' <textarea id="comment" class="form-control" name="comment" cols="45" rows="8" required></textarea></label><div class="comment-emoji-picker" aria-label="Emoji picker">' . $emoji_buttons . '</div>' . $comment_nonce . $privacy_block . $recaptcha_block,
			'must_log_in'          => '<p class="must-log-in">' . esc_html__( 'You must be logged in to comment.', 'webbooks' ) . ' <a href="' . esc_url( wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '">' . esc_html__( 'Log in', 'webbooks' ) . '</a></p>',
			'logged_in_as'         => '<p class="logged-in-as">'
				. esc_html__( 'You are logged in as', 'webbooks' )
				. ' <a href="' . esc_url( admin_url( 'profile.php' ) ) . '">' . esc_html( $user_display_name ) . '</a>. '
				. '<a href="' . esc_url( wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '">' . esc_html__( 'Log out?', 'webbooks' ) . '</a>'
				. '</p>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Your email address will not be published.', 'webbooks' ) . '</p>',
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'title_reply'          => esc_html__( 'Leave a comment', 'webbooks' ),
			/* translators: %s: Author name for reply target. */
			'title_reply_to'       => esc_html__( 'Reply to %s', 'webbooks' ),
			'cancel_reply_link'    => esc_html__( 'Cancel reply', 'webbooks' ),
			'label_submit'         => esc_html__( 'Submit comment', 'webbooks' ),
		);

		ob_start();
		comment_form( $args );
		$what_changes = array(
			'<small>'  => '',
			'</small>' => '',
			'<h3 id="reply-title" class="comment-reply-title">' => '<span id="reply-title">',
			'</h3>'    => '</span>',
			'<input class="btn navbar-btn btn-info" name="submit" type="submit" id="submit" value="' . $args['label_submit'] . '" />' => '<button class="btn" type="submit">' . $args['label_submit'] . '</button>',
		);
		$new_form     = str_replace( array_keys( $what_changes ), array_values( $what_changes ), ob_get_contents() );
		ob_end_clean();
		echo $new_form;
	} elseif ( comments_open() && ! $recaptchaConfigured ) {
		echo '<p class="comment-notes">' . esc_html__( 'Comments are temporarily unavailable. Please contact the site administrator.', 'webbooks' ) . '</p>';
	}
	?>
</div>
<script>
(function () {
	var form = document.getElementById('commentform');
	if (!form) return;

	form.addEventListener('click', function (event) {
	var button = event.target.closest('.comment-emoji-btn');
	if (!button) return;

	var textarea = document.getElementById('comment');
	if (!textarea) return;

	event.preventDefault();
	textarea.value += button.getAttribute('data-emoji') + ' ';
	textarea.focus();
	});
})();
</script>
