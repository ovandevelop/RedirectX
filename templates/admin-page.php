<?php
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;
$table = $wpdb->prefix . 'ovandev_redirectx';

if (isset($_GET['delete']) && current_user_can('manage_options')) {
    if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ovandev_redirectx_delete_' . intval($_GET['delete']))) {
        $id = intval($_GET['delete']);
        $wpdb->delete($table, ['id' => $id]);
        echo '<div class="notice notice-success"><p>ردیف حذف شد.</p></div>';
    } else {
        echo '<div class="notice notice-error"><p>خطا در تایید امنیتی حذف.</p></div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && current_user_can('manage_options')) {
    if (isset($_POST['ovandev_redirectx_nonce_field']) && wp_verify_nonce($_POST['ovandev_redirectx_nonce_field'], 'ovandev_redirectx_nonce_action')) {
        $old_url = esc_url_raw($_POST['old_url']);
        $new_url = esc_url_raw($_POST['new_url']);
        $type = intval($_POST['redirect_type']);
        $update_id = isset($_POST['update_id']) ? intval($_POST['update_id']) : 0;

        if ($old_url && in_array($type, [300,301,302,303,304,305,307,308,410])) {
            if ($update_id > 0) {
                $wpdb->update($table, [
                    'old_url' => $old_url,
                    'new_url' => $new_url,
                    'redirect_type' => $type,
                ], ['id' => $update_id]);
                echo '<div class="notice notice-success"><p>ردیف بروزرسانی شد.</p></div>';
            } else {
                $wpdb->insert($table, [
                    'old_url' => $old_url,
                    'new_url' => $new_url,
                    'redirect_type' => $type,
                ]);
                echo '<div class="notice notice-success"><p>ردیف افزوده شد.</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>داده‌های ورودی معتبر نیستند.</p></div>';
        }
    } else {
        echo '<div class="notice notice-error"><p>خطا در تایید امنیتی فرم.</p></div>';
    }
}

$redirects = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");
$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $edit_id));
}
?>
<div class="wrap ovandev-redirectx-admin">
    <h1>RedirectX [ovan.dev] - مدیریت ریدایرکت ها</h1>
    <form method="post">
        <?php wp_nonce_field('ovandev_redirectx_nonce_action', 'ovandev_redirectx_nonce_field'); ?>
        <input type="hidden" name="update_id" value="<?php echo esc_attr($edit->id ?? 0); ?>" />
        <table class="form-table">
            <tr>
                <th><label for="old_url">لینک قبلی :</label></th>
                <td><input type="text" id="old_url" name="old_url" required class="regular-text" value="<?php echo esc_attr($edit->old_url ?? ''); ?>" /></td>
            </tr>
            <tr>
                <th><label for="new_url">لینک جدید :</label></th>
                <td><input type="text" id="new_url" name="new_url" class="regular-text" value="<?php echo esc_attr($edit->new_url ?? ''); ?>" /></td>
            </tr>
            <tr>
                <th><label for="redirect_type">نوع ریدایرکت :</label></th>
                <td>
                    <select id="redirect_type" name="redirect_type">
                        <?php
                        $options = [
                            300 => '300 - Multiple Choices',
                            301 => '301 - Permanent',
                            302 => '302 - Found',
                            303 => '303 - See Other',
                            304 => '304 - Not Modified',
                            305 => '305 - Use Proxy',
                            307 => '307 - Temporary',
                            308 => '308 - Permanent',
                            410 => '410 - Gone',
                        ];
                        foreach ($options as $code => $label) {
                            $selected = (isset($edit->redirect_type) && $edit->redirect_type == $code) ? 'selected' : '';
                            echo "<option value=\"$code\" $selected>$label</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button($edit ? 'ویرایش' : 'افزودن'); ?>
    </form>
    <hr>
    <h2>لیست ریدایرکت‌ها</h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th>لینک قبلی</th>
                <th>لینک جدید</th>
                <th>نوع</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($redirects as $r): ?>
            <tr>
                <td><?php echo esc_html($r->old_url); ?></td>
                <td><?php echo esc_html($r->new_url); ?></td>
                <td><?php echo esc_html($r->redirect_type); ?></td>
                <td><?php echo esc_html($r->created_at); ?></td>
                <td>
                    <?php 
                    $delete_url = wp_nonce_url(admin_url('admin.php?page=redirectx&delete=' . $r->id), 'ovandev_redirectx_delete_' . $r->id);
                    ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=redirectx&edit=' . $r->id)); ?>">ویرایش</a> | 
                    <a href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('حذف شود ؟');">حذف</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
