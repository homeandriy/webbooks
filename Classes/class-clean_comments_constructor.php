<?php

class Clean_comments_constructor extends Walker_Comment {
	// класс, который собирает всю структуру комментов
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		// что выводим перед дочерними комментариями
		$output .= '<ul class="children">' . "\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		// что выводим после дочерних комментариев
		$output .= "</ul><!-- .children -->\n";
	}

	protected function comment( $comment, $depth, $args ) {
		// разметка каждого комментария, без закрывающего </li>!
		$classes = implode( ' ',
				get_comment_class() ) . ( $comment->comment_author_email == get_the_author_meta( 'email' ) ? ' author-comment' : '' ); // берем стандартные классы комментария и если коммент пренадлежит автору поста добавляем класс author-comment
		echo '<li id="li-comment-' . get_comment_ID() . '" class="' . $classes . '">' . "\n"; // родительский тэг комментария с классами выше и уникальным id
		echo '<div id="comment-' . get_comment_ID() . '">' . "\n"; // элемент с таким id нужен для якорных ссылок на коммент
		echo get_avatar( $comment, 64 ) . "\n"; // покажем аватар с размером 64х64
		echo '<p class="meta">Автор: ' . get_comment_author() . "\n"; // имя автора коммента
		echo ' ' . get_comment_author_email(); // email автора коммента
		echo ' ' . get_comment_author_url(); // url автора коммента
		echo ' Добавлено ' . get_the_time( 'l, F jS, Y' ) . ' в ' . get_the_time() . '</p>' . "\n"; // дата и время комментирования
		if ( '0' == $comment->comment_approved ) {
			echo '<em class="comment-awaiting-moderation">Ваш комментарий будет опубликован после проверки модератором.</em>' . "\n";
		}
		// если комментарий должен пройти проверку
		comment_text() . "\n"; // текст коммента
		$reply_link_args = array( // опции ссылки "ответить"
			'depth'      => $depth, // текущая вложенность
			'reply_text' => 'Ответить', // текст
			'login_text' => 'Вы должны быть залогинены', // текст если юзер должен залогинеться
		);
		echo get_comment_reply_link( array_merge( $args, $reply_link_args ) ); // выводим ссылку ответить
		echo '</div>' . "\n"; // закрываем див
	}

	public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
		// конец каждого коммента
		$output .= "</li><!-- #comment-## -->\n";
	}
}
