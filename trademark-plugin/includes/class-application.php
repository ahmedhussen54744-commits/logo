<?php
if (!defined('ABSPATH')) exit;

class DPDT_Application {
    
    public static function handle_submission() {
        // Verify this is a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_die('Invalid request method.', 'Error', array('response' => 405));
        }
        
        // Verify nonce
        DPDT_Security::verify_nonce('dpdt_nonce', 'dpdt_application_submit');
        
        // Check honeypot
        if (!DPDT_Security::check_honeypot()) {
            wp_die('Spam detected.', 'Error', array('response' => 403));
        }
        
        // Rate limit check
        $ip = self::get_client_ip();
        if (!DPDT_Security::check_rate_limit($ip, 3, 3600)) {
            wp_redirect(home_url('/apply/?status=ratelimit'));
            exit;
        }
        
        // Sanitize input
        $data = array(
            'brand_name' => sanitize_text_field($_POST['brand_name'] ?? ''),
            'owner_name' => sanitize_text_field($_POST['owner_name'] ?? ''),
            'owner_address' => sanitize_textarea_field($_POST['owner_address'] ?? ''),
            'owner_email' => sanitize_email($_POST['owner_email'] ?? ''),
            'owner_phone' => sanitize_text_field($_POST['owner_phone'] ?? ''),
            'trademark_class' => sanitize_text_field($_POST['trademark_class'] ?? ''),
            'goods_services' => sanitize_textarea_field($_POST['goods_services'] ?? ''),
            'trademark_type' => sanitize_text_field($_POST['trademark_type'] ?? ''),
            'description' => sanitize_textarea_field($_POST['description'] ?? ''),
            'priority_claim' => sanitize_text_field($_POST['priority_claim'] ?? ''),
            'attorney_name' => sanitize_text_field($_POST['attorney_name'] ?? ''),
            'attorney_address' => sanitize_textarea_field($_POST['attorney_address'] ?? ''),
        );
        
        // Validate required fields
        $required = array('brand_name', 'owner_name', 'owner_address', 'owner_email', 'owner_phone', 'trademark_class', 'goods_services', 'trademark_type');
        foreach ($required as $field) {
            if (empty($data[$field])) {
                wp_redirect(home_url('/apply/?status=missing&field=' . $field));
                exit;
            }
        }
        
        // Validate email
        if (!is_email($data['owner_email'])) {
            wp_redirect(home_url('/apply/?status=invalid_email'));
            exit;
        }
        
        // Handle logo upload
        $logo_url = '';
        if (!empty($_FILES['brand_logo']['tmp_name'])) {
            if (!DPDT_Security::validate_upload($_FILES['brand_logo'])) {
                wp_redirect(home_url('/apply/?status=invalid_file'));
                exit;
            }
            
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            $upload = wp_handle_upload($_FILES['brand_logo'], array('test_form' => false));
            if (!isset($upload['error'])) {
                $logo_url = $upload['url'];
            }
        }
        
        // Generate unique code
        $app_code = DPDT_Security::generate_app_code();
        
        // Insert into database
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        
        $result = $wpdb->insert($table, array(
            'app_code' => $app_code,
            'brand_name' => $data['brand_name'],
            'owner_name' => $data['owner_name'],
            'owner_address' => $data['owner_address'],
            'owner_email' => $data['owner_email'],
            'owner_phone' => $data['owner_phone'],
            'trademark_class' => $data['trademark_class'],
            'goods_services' => $data['goods_services'],
            'trademark_type' => $data['trademark_type'],
            'description' => $data['description'],
            'logo_url' => $logo_url,
            'priority_claim' => $data['priority_claim'],
            'attorney_name' => $data['attorney_name'],
            'attorney_address' => $data['attorney_address'],
            'application_date' => current_time('mysql'),
            'status' => 'pending',
            'ip_address' => $ip,
            'verify_url' => get_option('dpdt_verify_base_url', home_url('/verify/')) . $app_code,
        ));
        
        if ($result) {
            wp_redirect(home_url('/apply/?status=success&code=' . $app_code));
        } else {
            wp_redirect(home_url('/apply/?status=error'));
        }
        exit;
    }
    
    public static function get_application_by_code($code) {
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        $code = sanitize_text_field($code);
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE app_code = %s", $code));
    }
    
    public static function get_all_applications($status = '', $limit = 50, $offset = 0) {
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        
        $where = '';
        if (!empty($status)) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return $wpdb->get_results("SELECT * FROM $table $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
    }
    
    public static function count_applications($status = '') {
        global $wpdb;
        $table = DPDT_Database::get_table_name();
        
        $where = '';
        if (!empty($status)) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table $where");
    }
    
    private static function get_client_ip() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        return sanitize_text_field($ip);
    }
}
