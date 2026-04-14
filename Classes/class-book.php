<?php
/**
 * Class Class_Book
 */
class Class_Book
{
    public static function get_language($slug): string
    {
        switch ($slug) {
            case 'ru':
                $lang = 'Русский';
                break;
            case 'ua':
                $lang = 'Украинский';
                break;
            case 'en':
                $lang = 'Английский';
                break;
            case 'oth':
                $lang = 'Другой';
                break;
            default:
                $lang = 'Не указано';
                break;
        }

        return $lang;
    }

    public static function get_complexity($slug): string
    {
        switch ($slug) {
            case 'beginner':
                $complexity = 'Для начинающих программистов';
                break;
            case 'professional':
                $complexity = 'Для продвинутых программистов';
                break;
            default:
                $complexity = 'Не указано';
                break;
        }

        return $complexity;
    }

    public static function return_link_to_book_int()
    {
        $parameters = filter_input( INPUT_POST, 'parameters', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ?? [];
        $id         = absint( $parameters['id'] ?? 0 );
        $nonce      = sanitize_text_field( filter_input( INPUT_POST, '_nonce' ) ?? '' );
        $link_to_download = [];
        if ( ! wp_verify_nonce($nonce, DOWNLOAD_BOOK_NONCE)) {
            ob_start(); ?>
            <div class="container-fluid mrg-tb">
                <div class="row">
                    <div class="alert alert-danger" role="alert">
                        <strong>Ошибка!</strong> Не верный токен безопасности.
                    </div>
                </div>
            </div>
            <?php
            wp_send_json_error(
                [
                    'html' => ob_get_clean(),
                ],
                403
            );
        }
        if ( ! $id) {
            ob_start(); ?>
            <div class="container-fluid mrg-tb">
                <div class="row">
                    <div class="alert alert-danger" role="alert">
                        <strong>Ошибка!</strong> Не верный идентификатор книги.
                    </div>
                </div>
            </div>
            <?php
            wp_send_json_error(
                [
                    'html' => ob_get_clean(),
                ],
                400
            );
        }
        if ( ! empty(get_post_meta($id, 'download', true))) {
            $link_to_download['cloud_mail_ru'] = [
                'link' => get_post_meta($id, 'download', true),
                'name' => "Скачать с Облако Mail.ru",
                'description'  => "Скачать файл с облачного хранилища Cloud Mail.ru",
                'img'  => "/wp-content/uploads/2017/06/cloud_mail_ru.png",
            ];
        }
        if ( ! empty(get_post_meta($id, 'download_pcloud', true))) {
            $link_to_download['pcloud'] = [
                'link' => get_post_meta($id, 'download_pcloud', true),
                'name' => "Скачать с Облака pCloud",
                'description'  => "Все Ваши документы всегда с Вами, куда бы Вы не отправились!",
                'img'  => "/wp-content/uploads/2017/06/pcloud-logo.png"
            ];
        }
        if ( ! empty(get_post_meta($id, 'download_hubic', true))) {
            $link_to_download['hubic'] = [
                'link' => get_post_meta($id, 'download_hubic', true),
                'name' => "Скачать с CLOUD Webbooks",
                'description'  => "Свое облако от webbooks.com.ua",
                'img'  => "/wp-content/uploads/2018/08/touchIcon-core.png"
            ];
        }
        if ( ! empty(get_post_meta($id, 'download_mega', true))) {
            $link_to_download['mega'] = [
                'link' => get_post_meta($id, 'download_mega', true),
                'name' => "Скачать с Облака Mega",
                'description'  => "Your documents stay with you everywhere on every one of your devices!",
                'img'  => "/wp-content/uploads/2017/06/logo-facebook-e1498420140798.png",
            ];
        }
//		if ( ! empty( get_post_meta( $id, 's3_name_key', true ) ) ) {
//			$link_to_download['s3_storage'] = [
//				'link' => get_post_meta( $id, 's3_name_key', true ),
//				'name' => "Скачать с Хранилища",
//				'description'  => "Your documents stay with you everywhere on every one of your devices!",
//				'img'  => "/wp-content/uploads/2017/06/logo-facebook-e1498420140798.png",
//			];
//		}
        ob_start();
        ?>
        <div class="container-fluid mrg-tb">
            <div class="row">
                <?php if ( empty($link_to_download)) : ?>
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="alert alert-danger" role="alert">
                            <p><strong>Ошибка!</strong> Ссылки для скачивания не найдены.</p>
                            <p class="text-muted text-white">Напишите на <a href="mailto:homeandriy@gmail.com" data-id="<?=$id?>">homeandriy@gmail.com</a></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="col-sm-6 col-md-4 col-lg-4">
                        <?php foreach ( $link_to_download as $value ) : ?>
                            <?php
                            $book_img         = esc_url( $value['img'] );
                            $book_name        = esc_html( $value['name'] );
                            $book_description = esc_html( $value['description'] );
                            $book_link        = esc_url( $value['link'] );
                            ?>
                            <div class="thumbnail">
                                <img src="<?= $book_img ?>" alt="<?= esc_attr( $book_name ) ?>" loading="lazy">
                                <div class="caption">
                                    <h3><?= $book_name ?></h3>
                                    <p><?= $book_description ?></p>
                                    <p><a href="<?= $book_link ?>" class="btn btn-primary" role="button" target='_blank'><?= esc_html__( 'Скачать', 'webbooks' ) ?></a></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        wp_send_json_success(
            [
                'html' => ob_get_clean(),
            ]
        );
    }
}
