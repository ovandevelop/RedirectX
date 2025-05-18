<?php
if (!defined('ABSPATH')) {
    exit;
}

defined('ABSPATH') || exit;

class OvanDev_RedirectX_Loader {

    public function __construct() {
        $this->load_dependencies();
    }

    private function load_dependencies() {
        require_once OVANDEV_REDIRECTX_PATH . 'includes/class-ovandev-redirectx-manager.php';
        require_once OVANDEV_REDIRECTX_PATH . 'includes/class-ovandev-redirectx-admin.php';
    }

    public function run() {
        if (is_admin()) {
            new OvanDev_RedirectX_Admin();
        } else {
            new OvanDev_RedirectX_Manager();
        }
    }

    public static function activate() {
        global $wpdb;

        $table = $wpdb->prefix . 'ovandev_redirectx';
        $charset = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            old_url VARCHAR(255) NOT NULL,
            new_url VARCHAR(255),
            redirect_type INT(3) NOT NULL DEFAULT 301,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            INDEX (old_url)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function uninstall() {
        global $wpdb;

        $table = $wpdb->prefix . 'ovandev_redirectx';

        if (current_user_can('activate_plugins')) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
}