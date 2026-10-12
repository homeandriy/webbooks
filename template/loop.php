<?php
/**
 * Select the appropriate card for a book or a regular article.
 *
 * @package Webbooks
 */

declare(strict_types=1);

$template = \Webbooks\Book\BookMeta::isDownloadableBook( get_the_ID() )
	? 'template-parts/cards/book-card'
	: 'template-parts/cards/article-card';

get_template_part( $template, null, $args ?? array() );
