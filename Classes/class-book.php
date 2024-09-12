<?php

/**
 * Class Class_Book
 *
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
        $request = $_REQUEST['parameters'] ?? null;
        $id      = $request['id'] ?? null;
        $nonce   = $request['nonce'] ?? null;
        if ( ! wp_verify_nonce($nonce, DOWNLOAD_BOOK_NONCE)) {
            ob_start(); ?>
            <div class="alert alert-danger" role="alert">
                <strong>Ошибка!</strong> Не верный токен безопасности.
            </div>
            <?php
            echo ob_get_clean();
            wp_die();
        }
        if ( ! $id) {
            ob_start(); ?>
            <div class="alert alert-danger" role="alert">
                <strong>Ошибка!</strong> Не верный идентификатор книги.
            </div>
            <?php
            echo ob_get_clean();
            wp_die();
        }
        if ( ! empty(get_post_meta($id, 'download', true))) {
            $link_to_download['cloud_mail_ru'] = [
                'link' => get_post_meta($id, 'download', true),
                'name' => "Скачать с Облако Mail.ru",
                'des'  => "Скачать файл с облачного хранилища Cloud Mail.ru",
                'img'  => "https://webbooks.com.ua/wp-content/uploads/2017/06/cloud_mail_ru.png",
            ];
        }
        if ( ! empty(get_post_meta($id, 'download_pcloud', true))) {
            $link_to_download['pcloud'] = [
                'link' => get_post_meta($id, 'download_pcloud', true),
                'name' => "Скачать с Облака pCloud",
                'des'  => "Все Ваши документы всегда с Вами, куда бы Вы не отправились!",
                'img'  => "https://webbooks.com.ua/wp-content/uploads/2017/06/pcloud-logo.png"
            ];
        }
        if ( ! empty(get_post_meta($id, 'download_hubic', true))) {
            $link_to_download['hubic'] = [
                'link' => get_post_meta($id, 'download_hubic', true),
                'name' => "Скачать с CLOUD Webbooks",
                'des'  => "Свое облако от webbooks.com.ua",
                'img'  => "/wp-content/uploads/2018/08/touchIcon-core.png"
            ];
        }
        if ( ! empty(get_post_meta($id, 'download_mega', true))) {
            $link_to_download['mega'] = [
                'link' => get_post_meta($id, 'download_mega', true),
                'name' => "Скачать с Облака Mega",
                'des'  => "Your documents stay with you everywhere on every one of your devices!",
                'img'  => "/wp-content/uploads/2017/06/logo-facebook-e1498420140798.png",
            ];
        }
//		if ( ! empty( get_post_meta( $id, 's3_name_key', true ) ) ) {
//			$link_to_download['s3_storage'] = [
//				'link' => get_post_meta( $id, 's3_name_key', true ),
//				'name' => "Скачать с Хранилища",
//				'des'  => "Your documents stay with you everywhere on every one of your devices!",
//				'img'  => "/wp-content/uploads/2017/06/logo-facebook-e1498420140798.png",
//			];
//		}
        ob_start();
        ?>
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <?php
                if ( ! empty($link_to_download)) : ?>
                    <h2>Ссылки для скачивания не найдены</h2>
                <?php
                else: ?>
                    <?php
                    foreach ($link_to_download as $value): ?>
                        <div class="thumbnail">
                            <img src="<?= $value['img'] ?>" alt="<?= $value['name'] ?>">
                            <div class="caption">
                                <h3><?= $value['name'] ?></h3>
                                <p><?= $value['des'] ?></p>
                                <p><a href="<?= $value['link'] ?>" class="btn btn-primary" role="button"
                                      target='_blank'>Скачать</a>
                            </div>
                        </div>
                    <?php
                    endforeach; ?>
                <?php
                endif; ?>
            </div>
        </div>
        <?php
        echo ob_get_clean();
        wp_die();
    }
}
