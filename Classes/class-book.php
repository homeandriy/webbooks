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
        $request = $_REQUEST;
        $id      = $request['parameters']['id'] ?? null;
        $nonce   = $request['_nonce'] ?? null;
        $link_to_download = [];
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
        $id = (int) $id;
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
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <?php if ( empty($link_to_download)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <p>
                            <strong>Ошибка!</strong> Ссылки для скачивания не найдены.
                        </p>
                        <p class="text-muted">Напишите на <a href="mailto:homeandriy@gmail.com" data-id="<?=$id?>">homeandriy@gmail.com</a></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($link_to_download as $value): ?>
                        <div class="thumbnail">
                            <img src="<?= $value['img'] ?>" alt="<?= $value['name'] ?>">
                            <div class="caption">
                                <h3><?= $value['name'] ?></h3>
                                <p><?= $value['description'] ?></p>
                                <p><a href="<?= $value['link'] ?>" class="btn btn-primary" role="button" target='_blank'><?= __('Скачать', 'webbooks')?></a></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        echo ob_get_clean();
        wp_die();
    }
}
