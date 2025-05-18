<?php
if (!defined('ABSPATH')) {
    exit;
}

defined('ABSPATH') || exit;

class OvanDev_RedirectX_Manager {
    public function __construct() {
        add_action('parse_request', [$this, 'maybe_redirect'], 1);
    }

    public function maybe_redirect($wp) {
        if (is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
            return;
        }

        global $wpdb;

        $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $request_path = untrailingslashit(parse_url($request_uri, PHP_URL_PATH));

        if (!$request_path) {
            return;
        }

        $table = $wpdb->prefix . 'ovandev_redirectx';

        $redirect = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE TRIM(TRAILING '/' FROM old_url) = %s LIMIT 1",
            $request_path
        ));

        if ($redirect) {
            $new_url_path = untrailingslashit(parse_url($redirect->new_url, PHP_URL_PATH));
            if ($new_url_path === $request_path) {
                return;
            }

            if ((int)$redirect->redirect_type === 410) {
                status_header(410);
                nocache_headers();
                echo '<h1>410 - صفحه حذف شده است</h1>';
                exit;
            }

            if (!empty($redirect->new_url)) {
                if (strpos($redirect->new_url, home_url()) === 0) {
                    wp_safe_redirect($redirect->new_url, (int)$redirect->redirect_type);
                } else {
                    wp_redirect(esc_url_raw($redirect->new_url), (int)$redirect->redirect_type);
                }
                exit;
            }
        }
    }
}
