<?php

namespace Webbooks\Comment;

class CleanCommentsWalker extends \Walker_Comment
{
    public function start_lvl(&$output, $depth = 0, $args = []): void
    {
        $output .= "<ul class=\"children\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = []): void
    {
        $output .= "</ul><!-- .children -->\n";
    }

    protected function comment($comment, $depth, $args): void
    {
        $classes = implode(' ', get_comment_class()) . ($comment->comment_author_email == get_the_author_meta('email') ? ' author-comment' : '');
        echo '<li id="li-comment-' . get_comment_ID() . '" class="' . $classes . '">' . "\n";
        echo '<div id="comment-' . get_comment_ID() . '">' . "\n";
        echo get_avatar($comment, 64) . "\n";
        echo '<p class="meta">Автор: ' . get_comment_author() . "\n";
        echo ' ' . get_comment_author_email();
        echo ' ' . get_comment_author_url();
        echo ' Добавлено ' . get_the_time('l, F jS, Y') . ' в ' . get_the_time() . '</p>' . "\n";
        if ('0' == $comment->comment_approved) {
            echo '<em class="comment-awaiting-moderation">Ваш комментарий будет опубликован после проверки модератором.</em>' . "\n";
        }
        comment_text();
        echo "\n";
        echo get_comment_reply_link(array_merge($args, ['depth' => $depth, 'reply_text' => 'Ответить', 'login_text' => 'Вы должны быть залогинены']));
        echo '</div>' . "\n";
    }

    public function end_el(&$output, $comment, $depth = 0, $args = []): void
    {
        $output .= "</li><!-- #comment-## -->\n";
    }
}
