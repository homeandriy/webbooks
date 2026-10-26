<?php
/**
 * @var array{config: array, option: string} $data
 */
defined('ABSPATH') || exit;
?>
<div class="wrap">
    <h1><?= esc_html__('Webbooks Git — налаштування', 'webbooks-git-updates') ?></h1>
    <?php settings_errors($data['option']); ?>
    <p><?= esc_html__('Джерело: homeandriy/webbooks. Перевірка двічі на добу через WP-Cron. Оновлення встановлює адміністратор; локальні зміни файлів теми буде замінено.', 'webbooks-git-updates') ?></p>
    <form method="post" action="options.php">
        <?php settings_fields('webbooks_git_updates'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="wb-enabled"><?= esc_html__('Перевірка оновлень', 'webbooks-git-updates') ?></label>
                </th>
                <td>
                    <input id="wb-enabled" type="checkbox" name="<?= esc_attr($data['option']) ?>[enabled]" value="1" <?php checked($data['config']['enabled'], 1); ?>>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wb-branch"><?= esc_html__('Гілка Git', 'webbooks-git-updates') ?></label>
                </th>
                <td>
                    <input id="wb-branch" class="regular-text" name="<?= esc_attr($data['option']) ?>[branch]" value="<?= esc_attr($data['config']['branch']) ?>" required>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <p><?= esc_html__('Для приватного репозиторію перевірка підтримує WEBBOOKS_GITHUB_TOKEN у wp-config.php. ZIP для встановлення має бути публічно доступним; токен не передається серверу завантажень.', 'webbooks-git-updates') ?></p>
</div>
