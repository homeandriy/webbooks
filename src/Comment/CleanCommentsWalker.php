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
        $classes = implode(' ', get_comment_class('', $comment)) . ($comment->comment_author_email === get_the_author_meta('email') ? ' author-comment' : '');
        echo '<li id="li-comment-' . (int) $comment->comment_ID . '" class="' . esc_attr(trim($classes)) . '">' . "\n";
        echo '<div id="comment-' . (int) $comment->comment_ID . '">' . "\n";
        echo get_avatar($comment, 64) . "\n";
        echo '<p class="meta">';
        echo esc_html__('Author:', 'webbooks') . ' ' . esc_html(get_comment_author($comment));
        echo ' · ' . esc_html(get_comment_date('d.m.Y H:i', $comment));
        echo '</p>' . "\n";

        if ('0' === (string) $comment->comment_approved) {
            echo '<em class="comment-awaiting-moderation">' . esc_html__('Your comment is awaiting moderation.', 'webbooks') . '</em>' . "\n";
        }

        comment_text($comment);
        echo "\n";

        echo get_comment_reply_link(array_merge($args, [
            'depth' => $depth,
            'max_depth' => $args['max_depth'] ?? 4,
            'reply_text' => esc_html__('Reply', 'webbooks'),
            'login_text' => esc_html__('You must be logged in to reply.', 'webbooks'),
        ]));
        echo '</div>' . "\n";
    }

    public function end_el(&$output, $comment, $depth = 0, $args = []): void
    {
        $output .= "</li><!-- #comment-## -->\n";
    }
}
