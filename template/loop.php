<?php
/**
 * Запись в цикле (loop.php)
 * @package WordPress
 * @subpackage webbooks
 */
$thumb_url = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 content_block">
    <div class="list-group" itemscope itemtype="http://schema.org/Book">
        <div class="list-group-item ">
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <img width="390"
                         height="440"
                         class="media-object lazy"
                         data-original="<?= esc_url( $thumb_url ) ?>"
                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mMMDk76DwAEEgIJ2SbKSQAAAABJRU5ErkJggg=="
                         alt="<?= esc_attr( get_the_title() ) ?>"
                         itemprop="image"
                    >
                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                    <h4 class="list-group-item-heading">
                        <a href="<?= esc_url( get_the_permalink() ) ?>" itemprop="name"><?= esc_html( get_the_title() ) ?></a>
                    </h4>
                    <p class="list-group-item-text" itemprop="description">
						<?= wp_kses_post( get_short_description( get_the_content(), 50 ) ) ?>
                    </p>
                    <table class="table">
                        <tbody>
                        <tr>
                            <td><?php esc_html_e( 'Автор книги:', 'webbooks' ); ?></td>
                            <td itemprop="author">
								<?= esc_html( get_post_meta( $post->ID, 'autor', true ) );?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Год выхода:', 'webbooks' ); ?></td>
                            <td itemprop="copyrightYear">
								<?= esc_html( get_post_meta( $post->ID, 'year', true ) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Издательство:', 'webbooks' ); ?></td>
                            <td>
								<?= esc_html( get_post_meta( $post->ID, 'create', true ) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Язык:', 'webbooks' ); ?></td>
                            <td itemprop="inLanguage">
								<?= esc_html( Class_Book::get_language( get_field( 'language' ) ) ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Статус:', 'webbooks' ); ?></td>
                            <td>
								<?php
								$complexity = trim( get_field( "complexity" ) );
								echo esc_html( Class_Book::get_complexity( $complexity ) );
								?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Формат:', 'webbooks' ); ?></td>
                            <td>
								<?= esc_html( get_post_meta( $post->ID, 'format', true ) ); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="next-reed">
                    <p>
                        <a
                            href="<?= esc_url( get_the_permalink() ) ?>"
                            class="btn navbar-btn btn-info navbar-right"><?php esc_html_e( 'Дальше', 'webbooks' ); ?>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
