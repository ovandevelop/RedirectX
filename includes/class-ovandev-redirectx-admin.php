<?php

if (!defined('ABSPATH')) {
    exit;
}

class OvanDev_RedirectX_Admin {

    public function __construct() {
        $this->init_hooks();
    }

    public function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_admin_menu() {
        add_menu_page(
            'RedirectX',
            'RedirectX',
            'manage_options',
            'redirectx',
            [$this, 'render_admin_page'],
            'dashicons-randomize',
            80
        );
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'toplevel_page_redirectx') {
            return;
        }

        wp_enqueue_style(
            'ovandev-redirectx-admin',
            OVANDEV_REDIRECTX_URL . 'assets/css/admin-style.css',
            [],
            OVANDEV_REDIRECTX_VERSION
        );
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('دسترسی کافی ندارید.', 'redirectx'));
        }

        if (isset($_GET['redirectx_status'])) {
            $status = sanitize_text_field($_GET['redirectx_status']);

            if ($status === 'success') {
                echo '<div class="notice notice-success is-dismissible"><p>عملیات با موفقیت انجام شد.</p></div>';
            } elseif ($status === 'error') {
                echo '<div class="notice notice-error"><p>خطایی در انجام عملیات رخ داد.</p></div>';
            }
        }

        include OVANDEV_REDIRECTX_PATH . 'templates/admin-page.php';
    }
}
