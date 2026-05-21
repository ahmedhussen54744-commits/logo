<?php
if (!defined('ABSPATH')) exit;

class DPDT_Admin {
    
    // Main Dashboard
    public static function render_dashboard() {
        $applications = DPDT_Application::get_all_applications();
        $total = DPDT_Application::count_applications();
        $pending = DPDT_Application::count_applications('pending');
        $approved = DPDT_Application::count_applications('approved');
        $rejected = DPDT_Application::count_applications('rejected');
        
        include DPDT_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }
    
    // Pending Applications
    public static function render_pending() {
        $applications = DPDT_Application::get_all_applications('pending');
        $page_title = 'Pending Applications';
        include DPDT_PLUGIN_DIR . 'templates/admin/applications-list.php';
    }
    
    // Approved Applications
    public static function render_approved() {
        $applications = DPDT_Application::get_all_applications('approved');
        $page_title = 'Approved Applications';
        include DPDT_PLUGIN_DIR . 'templates/admin/applications-list.php';
    }
    
    // Settings Page
    public static function render_settings() {
        if (isset($_POST['dpdt_save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'dpdt_settings_save')) {
            update_option('dpdt_verify_base_url', sanitize_url($_POST['verify_base_url'] ?? ''));
            update_option('dpdt_certificate_prefix', sanitize_text_field($_POST['certificate_prefix'] ?? 'DPDT'));
            update_option('dpdt_site_established', sanitize_text_field($_POST['site_established'] ?? '2009'));
            echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
        }
        include DPDT_PLUGIN_DIR . 'templates/admin/settings.php';
    }
    
    // AJAX: Approve application
    public static function approve_application() {
        check_ajax_referer('dpdt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        $id = intval($_POST['app_id']);
        
        $wpdb->update($table, array(
            'status' => 'approved',
            'approved_date' => current_time('mysql'),
        ), array('id' => $id));
        
        wp_send_json_success(array('message' => 'Application approved successfully!'));
    }
    
    // AJAX: Reject application
    public static function reject_application() {
        check_ajax_referer('dpdt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        $id = intval($_POST['app_id']);
        $notes = sanitize_textarea_field($_POST['admin_notes'] ?? '');
        
        $wpdb->update($table, array(
            'status' => 'rejected',
            'admin_notes' => $notes,
        ), array('id' => $id));
        
        wp_send_json_success(array('message' => 'Application rejected.'));
    }
    
    // AJAX: Update application details
    public static function update_application() {
        check_ajax_referer('dpdt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        $id = intval($_POST['app_id']);
        
        $update_data = array();
        
        if (!empty($_POST['registration_number'])) {
            $update_data['registration_number'] = sanitize_text_field($_POST['registration_number']);
        }
        if (!empty($_POST['registration_date'])) {
            $update_data['registration_date'] = sanitize_text_field($_POST['registration_date']);
        }
        if (!empty($_POST['approved_date'])) {
            $update_data['approved_date'] = sanitize_text_field($_POST['approved_date']);
        }
        if (!empty($_POST['expiry_date'])) {
            $update_data['expiry_date'] = sanitize_text_field($_POST['expiry_date']);
        }
        if (!empty($_POST['application_date'])) {
            $update_data['application_date'] = sanitize_text_field($_POST['application_date']);
        }
        if (!empty($_POST['verify_url'])) {
            $update_data['verify_url'] = esc_url_raw($_POST['verify_url']);
        }
        if (!empty($_POST['qr_code_url'])) {
            $update_data['qr_code_url'] = esc_url_raw($_POST['qr_code_url']);
        }
        if (!empty($_POST['admin_notes'])) {
            $update_data['admin_notes'] = sanitize_textarea_field($_POST['admin_notes']);
        }
        if (!empty($_POST['status'])) {
            $update_data['status'] = sanitize_text_field($_POST['status']);
        }
        
        if (!empty($update_data)) {
            $wpdb->update($table, $update_data, array('id' => $id));
            wp_send_json_success(array('message' => 'Application updated successfully!'));
        } else {
            wp_send_json_error(array('message' => 'No data to update.'));
        }
    }
    
    // AJAX: Upload certificate PDF/JPG
    public static function upload_certificate() {
        check_ajax_referer('dpdt_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_die('Unauthorized');
        
        $id = intval($_POST['app_id']);
        $type = sanitize_text_field($_POST['cert_type']); // pdf or jpg
        
        if (!in_array($type, array('pdf', 'jpg'))) {
            wp_send_json_error(array('message' => 'Invalid certificate type.'));
        }
        
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $file_key = 'certificate_file';
        if (empty($_FILES[$file_key]['tmp_name'])) {
            wp_send_json_error(array('message' => 'No file uploaded.'));
        }
        
        $allowed = ($type === 'pdf') 
            ? array('application/pdf') 
            : array('image/jpeg', 'image/png', 'image/webp');
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$file_key]['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed)) {
            wp_send_json_error(array('message' => 'Invalid file type. Expected: ' . $type));
        }
        
        $upload = wp_handle_upload($_FILES[$file_key], array('test_form' => false));
        
        if (isset($upload['error'])) {
            wp_send_json_error(array('message' => $upload['error']));
        }
        
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        $field = ($type === 'pdf') ? 'certificate_pdf' : 'certificate_jpg';
        
        $wpdb->update($table, array($field => $upload['url']), array('id' => $id));
        
        wp_send_json_success(array(
            'message' => ucfirst($type) . ' certificate uploaded successfully!',
            'url' => $upload['url'],
        ));
    }
}
