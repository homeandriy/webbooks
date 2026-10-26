<?php
/**
 * @var array{rows: array, events: array} $data
 */
defined('ABSPATH') || exit;
?>
<div class="wrap">
    <h1><?= esc_html__('Webbooks Git — статистика', 'webbooks-git-updates') ?></h1>
    <table class="widefat striped">
        <tbody>
            <?php foreach ($data['rows'] as $label => $value): ?>
                <tr>
                    <th scope="row"><?= esc_html($label) ?></th>
                    <td><?= esc_html((string) $value) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <form method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
        <input type="hidden" name="action" value="webbooks_git_check">
        <?php wp_nonce_field('webbooks_git_check'); ?>
        <?php submit_button(__('Перевірити зараз', 'webbooks-git-updates'), 'secondary'); ?>
    </form>
    <h2><?= esc_html__('Журнал (останні 50 подій)', 'webbooks-git-updates') ?></h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th scope="col"><?= esc_html__('Час', 'webbooks-git-updates') ?></th>
                <th scope="col"><?= esc_html__('Результат', 'webbooks-git-updates') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['events'] as $event): ?>
                <tr>
                    <td><?= esc_html($event['date']) ?></td>
                    <td><?= esc_html($event['message']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['events'])): ?>
                <tr>
                    <td colspan="2"><?= esc_html__('Перевірок ще не було.', 'webbooks-git-updates') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
