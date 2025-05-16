<?php
/**
 * Plugin Name: RedirectX
 * Plugin URI: https://ovan.dev/RedirectX/
 * Description: ساخت و مدیریت انواع ریدایرکت ها !
 * Version: 1.0.0
 * Author: Ovan Develop (RezaEi.Ali)
 * Author URI: https://ovan.dev/
 * License: GPL2
 * Text Domain: redirectx
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!defined('OVANDEV_REDIRECTX_PATH')) {
    define('OVANDEV_REDIRECTX_PATH', plugin_dir_path(__FILE__));
    define('OVANDEV_REDIRECTX_URL', plugin_dir_url(__FILE__));
    define('OVANDEV_REDIRECTX_VERSION', '1.0.0');
}
require_once OVANDEV_REDIRECTX_PATH . 'includes/class-ovandev-redirectx-loader.php';
function ovandev_redirectx_run() {
    $plugin = new OvanDev_RedirectX_Loader();
    $plugin->run();
}
register_activation_hook(__FILE__, ['OvanDev_RedirectX_Loader', 'activate']);
register_deactivation_hook(__FILE__, ['OvanDev_RedirectX_Loader', 'deactivate']);
register_uninstall_hook(__FILE__, ['OvanDev_RedirectX_Loader', 'uninstall']);
ovandev_redirectx_run();