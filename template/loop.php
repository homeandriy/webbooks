<?php
/**
 * Запись в цикле (loop.php)
 * @package WordPress
 * @subpackage your-clean-template
 */
$thumb_url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 content_block">
    <div class="list-group" itemscope itemtype="http://schema.org/Book">
        <div class="list-group-item ">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <img width="390" height="440" class="media-object lazy" data-original="<?= $thumb_url; ?>"
                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mMMDk76DwAEEgIJ2SbKSQAAAABJRU5ErkJggg=="
                         alt="<?= sanitize_text_field( get_the_title() ) ?>" itemprop="image">
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <h4 class="list-group-item-heading"><a href="<?= get_the_permalink(); ?>"
                                                           itemprop="name"><?= get_the_title() ?></a></h4>
                    <p class="list-group-item-text" itemprop="description">
						<?= get_short_description( get_the_content(), 500 ); ?>
                    <table class="table">
                        <tbody>
                        <tr>
                            <td>Автор книги:</td>
                            <td itemprop="author">
								<?php
								    echo get_post_meta( $post->ID, 'autor', true );
								?>
                            </td>
                        </tr>
                        <tr>
                            <td>Год выхода:</td>
                            <td itemprop="copyrightYear">
								<?php
                                    echo get_post_meta( $post->ID, 'year', true );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Жанр:</td>
                            <td itemprop="genre">
								<?php the_category() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Издательство:</td>
                            <td>
								<?= get_post_meta( $post->ID, 'create', true ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Язык:</td>
                            <td itemprop="inLanguage">
								<?= Class_Book::get_language( get_field( 'language' ) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Статус:</td>
                            <td>
								<?php
								$complexity = trim( get_field( "complexity" ) );
								echo Class_Book::get_complexity( $complexity );
								?>
                            </td>
                        </tr>
                        <tr>
                            <td>Формат:</td>
                            <td>
								<?= get_post_meta( $post->ID, 'format', true ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Cтраниц:</td>
                            <td itemprop="numberOfPages">
								<?= get_post_meta( $post->ID, 'number_page', true ); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </p>
                </div>
                <div class="next-reed">
                    <p><a href="<?= get_the_permalink(); ?>"
                          class="btn navbar-btn btn-info navbar-right">Дальше</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
