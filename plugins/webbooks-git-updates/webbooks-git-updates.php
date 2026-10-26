<?php
/**
 * Plugin Name: Webbooks Git Updates
 * Description: Оновлення теми Webbooks із GitHub, налаштування та журнал перевірок.
 * Version: 1.0.0
 * Requires at least: 6.5
 * Requires PHP: 8.2
 * License: GPL-2.0-or-later
 * Text Domain: webbooks-git-updates
 */
declare(strict_types=1);

namespace Webbooks\GitUpdates;

defined('ABSPATH') || exit;
require_once __DIR__ . '/src/Updater.php';
if (!is_multisite()) {
    Updater::register();
    register_activation_hook(__FILE__, [Updater::class, 'activate']);
    register_deactivation_hook(__FILE__, [Updater::class, 'deactivate']);
}
