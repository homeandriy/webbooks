<?php
/**
 * @var array{version: string, install_url: string} $data
 */
defined('ABSPATH') || exit;
?>
<div class="notice notice-info">
    <p>
        <?= esc_html(sprintf(__('З’явилася нова версія Webbooks %s.', 'webbooks-git-updates'), $data['version'])) ?>
        <?php if ($data['install_url'] !== ''): ?>
            <a href="<?= esc_url($data['install_url']) ?>"><?= esc_html__('Встановити оновлення', 'webbooks-git-updates') ?></a>
        <?php else: ?>
            <?= esc_html__('Встановлення стане доступним після публікації релізного ZIP.', 'webbooks-git-updates') ?>
        <?php endif; ?>
    </p>
</div>
