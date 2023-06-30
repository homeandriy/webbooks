<?php
/**
 * Шаблон комментариев (comments.php)
 * Выводит список комментариев и форму добавления
 * @package WordPress
 * @subpackage your-clean-template
 */
?>
<div id="comments">
	<span>Всего комментариев: <?= get_comments_number();?></span>
	<?php if (have_comments()) : ?>
	<ul class="comment-list">
		<?php
			$args = array(
				'walker' => new Clean_comments_constructor(),
			);
			wp_list_comments($args);
		?>
	</ul>
	<?php if (get_comment_pages_count() > 1 && get_option( 'page_comments')) : ?>
	<?php $args = array(
			'prev_text' => '«',
			'next_text' => '»'
		); 
		paginate_comments_links($args);
	?>
	<?php endif;?>
	<?php endif; ?>
	<?php if (comments_open()) {
		/* ФОРМА КОММЕНТИРОВАНИЯ */
		$fields =  array(
			'author' => '<label for="author">Имя <input id="author" name="author" type="text" value="'.esc_attr($commenter['comment_author']).'" size="30" required></label>', // поле Имя
			'email' => '<label for="email">Email<input id="email" name="email" type="text" value="'.esc_attr($commenter['comment_author_email']).'" size="30" required></label>', // поле email
			'url' => '<label for="url">Сайт<input id="url" name="url" type="text" value="'.esc_attr($commenter['comment_author_url']).'" size="30"></label>', // поле сайт
			);
		$args = array(
			'fields' => apply_filters('comment_form_default_fields', $fields),
			'comment_field' => '<label for="comment">Комментарий: <textarea id="comment" class="form-control" name="comment" cols="45" rows="8"></textarea></label>', // разметка поля для комментирования
			'must_log_in' => '<p class="must-log-in">Вы должны быть зарегистрированы! '.wp_login_url(apply_filters('the_permalink',get_permalink())).'</p>', // текст "Вы должны быть зарегистрированы!"
			'logged_in_as' => '<p class="logged-in-as">'.sprintf(__( 'Вы вошли как <a href="%1$s">%2$s</a>. <a href="%3$s">Выйти?</a>'), admin_url('profile.php'), $user_identity, wp_logout_url(apply_filters('the_permalink',get_permalink()))).'</p>', // разметка "Вы вошли как"
			'comment_notes_before' => '<p class="comment-notes">Ваш email не будет опубликован.</p>',
			'id_form' => 'commentform',
			'id_submit' => 'submit',
			'title_reply' => 'Оставить комментарий',
			'title_reply_to' => 'Ответить %s',
			'cancel_reply_link' => 'Отменить ответ',
			'label_submit' => 'Написать'
		);
		/* Следующий кусок кода будет менять разметку формы, которую мы не можем изменить стандартным функционалом wp */
		/* Например, это может понадобиться, если надо сделать форму на бутстрапе */
		ob_start();
	    comment_form($args);
	    $what_changes = array(
	    		'<small>' => '',
	    		'</small>' => '',
	    		'<h3 id="reply-title" class="comment-reply-title">' => '<span id="reply-title">',
	    		'</h3>' => '</span>',
	    		'<input class="btn navbar-btn btn-info" name="submit" type="submit" id="submit" value="'.$args['label_submit'].'" />' => '<button class="btn" type="submit">'.$args['label_submit'].'</button>'
	    	);
	    $new_form = str_replace(array_keys($what_changes), array_values($what_changes), ob_get_contents());
	    ob_end_clean();
	    echo $new_form;
	} ?>
</div>
