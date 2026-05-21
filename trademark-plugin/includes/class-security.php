<?php
if (!defined('ABSPATH')) exit;

class DPDT_Security {
    
    // Generate secure 20-character application code
    public static function generate_app_code() {
        $prefix = get_option('dpdt_certificate_prefix', 'DPDT');
        $random = strtoupper(bin2hex(random_bytes(6)));
        $year = date('Y');
        $code = $prefix . $year . $random;
        return substr($code, 0, 20);
    }
    
    // Sanitize all input
    public static function sanitize_input($data) {
        if (is_array($data)) {
            return array_map(array(__CLASS__, 'sanitize_input'), $data);
        }
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
    
    // Verify nonce for POST requests
    public static function verify_nonce($nonce_name, $action) {
        if (!isset($_POST[$nonce_name]) || !wp_verify_nonce($_POST[$nonce_name], $action)) {
            wp_die('Security verification failed. Access denied.', 'Security Error', array('response' => 403));
        }
    }
    
    // Rate limiting
    public static function check_rate_limit($ip, $max_attempts = 5, $window = 3600) {
        $transient_key = 'dpdt_rate_' . md5($ip);
        $attempts = get_transient($transient_key);
        
        if ($attempts === false) {
            set_transient($transient_key, 1, $window);
            return true;
        }
        
        if ($attempts >= $max_attempts) {
            return false;
        }
        
        set_transient($transient_key, $attempts + 1, $window);
        return true;
    }
    
    // Validate file upload
    public static function validate_upload($file, $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml')) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return false;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed_types)) {
            return false;
        }
        
        // Max 5MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return false;
        }
        
        return true;
    }
    
    // CSRF Token for forms
    public static function get_csrf_field() {
        return wp_nonce_field('dpdt_application_submit', 'dpdt_nonce', true, false);
    }
    
    // Honeypot field
    public static function get_honeypot() {
        return '<div style="position:absolute;left:-9999px;"><input type="text" name="dpdt_website_url" value="" tabindex="-1" autocomplete="off"></div>';
    }
    
    // Check honeypot
    public static function check_honeypot() {
        return empty($_POST['dpdt_website_url']);
    }
    
    // Encrypt sensitive data
    public static function encrypt($data) {
        $key = wp_salt('auth');
        $iv = substr(hash('sha256', wp_salt('secure_auth')), 0, 16);
        return base64_encode(openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv));
    }
    
    // Decrypt data
    public static function decrypt($data) {
        $key = wp_salt('auth');
        $iv = substr(hash('sha256', wp_salt('secure_auth')), 0, 16);
        return openssl_decrypt(base64_decode($data), 'AES-256-CBC', $key, 0, $iv);
    }
}
