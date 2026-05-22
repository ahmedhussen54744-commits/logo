<?php
if (!defined('ABSPATH')) exit;

/**
 * Security Class
 * Handles CSRF protection, rate limiting, honeypot, encryption, XSS protection
 */
class DPDT_Security {

    private $rate_limit_key = 'dpdt_rate_limit_';
    private $encryption_method = 'AES-256-CBC';

    public function __construct() {
        // Only add these hooks if not in CLI and not too late
        if (php_sapi_name() !== 'cli') {
            if (!did_action('init')) {
                add_action('init', array($this, 'init_security_headers'));
            }
            add_action('wp_head', array($this, 'add_security_meta'));
        }
    }

    /**
     * Add security headers
     */
    public function init_security_headers() {
        if (!is_admin()) {
            add_action('send_headers', function() {
                header('X-Content-Type-Options: nosniff');
                header('X-Frame-Options: SAMEORIGIN');
                header('X-XSS-Protection: 1; mode=block');
                header('Referrer-Policy: strict-origin-when-cross-origin');
            });
        }
    }

    /**
     * Add security meta tags
     */
    public function add_security_meta() {
        echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n";
        echo '<meta name="referrer" content="strict-origin-when-cross-origin">' . "\n";
    }

    /**
     * Verify CSRF nonce
     */
    public function verify_nonce($nonce, $action = DPDT_NONCE_ACTION) {
        if (!wp_verify_nonce($nonce, $action)) {
            $this->log_security_event('nonce_failure', 'Invalid nonce detected');
            return false;
        }
        return true;
    }

    /**
     * Generate CSRF token field
     */
    public function get_nonce_field($action = DPDT_NONCE_ACTION) {
        return wp_nonce_field($action, '_dpdt_nonce', true, false);
    }

    /**
     * Rate limiting check
     */
    public function check_rate_limit($identifier, $max_attempts = null, $window = null) {
        if (is_null($max_attempts)) {
            $max_attempts = intval(get_option('dpdt_rate_limit_attempts', 5));
        }
        if (is_null($window)) {
            $window = intval(get_option('dpdt_rate_limit_window', 300));
        }

        $key = $this->rate_limit_key . md5($identifier);
        $attempts = get_transient($key);

        if (false === $attempts) {
            set_transient($key, 1, $window);
            return true;
        }

        if ($attempts >= $max_attempts) {
            $this->log_security_event('rate_limit_exceeded', "Identifier: {$identifier}, Attempts: {$attempts}");
            return false;
        }

        set_transient($key, $attempts + 1, $window);
        return true;
    }

    /**
     * Reset rate limit for identifier
     */
    public function reset_rate_limit($identifier) {
        $key = $this->rate_limit_key . md5($identifier);
        delete_transient($key);
    }

    /**
     * Honeypot field generation
     */
    public function get_honeypot_field() {
        $field_name = 'dpdt_website_url_' . wp_rand(1000, 9999);
        $html = '<div style="position:absolute;left:-9999px;top:-9999px;height:0;width:0;overflow:hidden;" aria-hidden="true">';
        $html .= '<label for="' . esc_attr($field_name) . '">Leave empty</label>';
        $html .= '<input type="text" name="dpdt_honeypot" id="' . esc_attr($field_name) . '" value="" tabindex="-1" autocomplete="off">';
        $html .= '</div>';
        return $html;
    }

    /**
     * Check honeypot field
     */
    public function check_honeypot($value) {
        if (!empty($value)) {
            $this->log_security_event('honeypot_triggered', 'Bot detected via honeypot');
            return false;
        }
        return true;
    }

    /**
     * Encrypt data
     */
    public function encrypt($data) {
        $key = get_option('dpdt_encryption_key', '');
        if (empty($key)) {
            $key = wp_generate_password(32, true, true);
            update_option('dpdt_encryption_key', $key);
        }

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->encryption_method));
        $encrypted = openssl_encrypt($data, $this->encryption_method, $key, 0, $iv);

        if ($encrypted === false) {
            return false;
        }

        return base64_encode($iv . '::' . $encrypted);
    }

    /**
     * Decrypt data
     */
    public function decrypt($data) {
        $key = get_option('dpdt_encryption_key', '');
        if (empty($key)) {
            return false;
        }

        $decoded = base64_decode($data);
        if ($decoded === false) {
            return false;
        }

        $parts = explode('::', $decoded, 2);
        if (count($parts) !== 2) {
            return false;
        }

        $decrypted = openssl_decrypt($parts[1], $this->encryption_method, $key, 0, $parts[0]);
        return $decrypted;
    }

    /**
     * Sanitize input (XSS protection)
     */
    public function sanitize_input($input, $type = 'text') {
        switch ($type) {
            case 'email':
                return sanitize_email($input);
            case 'url':
                return esc_url_raw($input);
            case 'int':
                return intval($input);
            case 'float':
                return floatval($input);
            case 'html':
                return wp_kses_post($input);
            case 'textarea':
                return sanitize_textarea_field($input);
            case 'filename':
                return sanitize_file_name($input);
            case 'text':
            default:
                return sanitize_text_field($input);
        }
    }

    /**
     * Sanitize array of inputs
     */
    public function sanitize_array($data, $schema) {
        $sanitized = array();
        foreach ($schema as $key => $type) {
            if (isset($data[$key])) {
                $sanitized[$key] = $this->sanitize_input($data[$key], $type);
            }
        }
        return $sanitized;
    }

    /**
     * Generate secure random token
     */
    public function generate_token($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate application ID
     */
    public function generate_application_id() {
        $prefix = get_option('dpdt_certificate_prefix', 'DPDT');
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid(wp_rand(), true)), 0, 8));
        return $prefix . '-' . $year . '-' . $random;
    }

    /**
     * Generate certificate number
     */
    public function generate_certificate_number() {
        $prefix = get_option('dpdt_certificate_prefix', 'DPDT');
        $year = date('Y');
        $sequence = intval(get_option('dpdt_cert_sequence', 1000));
        $sequence++;
        update_option('dpdt_cert_sequence', $sequence);
        return $prefix . '-CERT-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Validate file upload
     */
    public function validate_upload($file, $allowed_types = array(), $max_size = 0) {
        $errors = array();

        if (empty($allowed_types)) {
            $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'application/pdf');
        }

        if ($max_size === 0) {
            $max_size = 5 * 1024 * 1024; // 5MB default
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = __('ফাইল আপলোড ত্রুটি।', 'dpdt-trademark');
            return $errors;
        }

        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = __('অনুমোদিত ফাইল প্রকার নয়।', 'dpdt-trademark');
        }

        if ($file['size'] > $max_size) {
            $errors[] = sprintf(__('ফাইল সাইজ %s MB এর বেশি হতে পারবে না।', 'dpdt-trademark'), round($max_size / (1024 * 1024), 1));
        }

        // Check for PHP in file
        $content = file_get_contents($file['tmp_name']);
        if (preg_match('/<\?php/i', $content) || preg_match('/<script/i', $content)) {
            $errors[] = __('নিরাপত্তা লঙ্ঘন সনাক্ত হয়েছে।', 'dpdt-trademark');
            $this->log_security_event('malicious_upload', 'PHP/Script code detected in upload');
        }

        return $errors;
    }

    /**
     * Log security events
     */
    public function log_security_event($type, $details) {
        $log_data = array(
            'type' => $type,
            'details' => $details,
            'ip' => $this->get_client_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
            'time' => current_time('mysql'),
            'user_id' => get_current_user_id(),
        );

        $logs = get_option('dpdt_security_logs', array());
        array_unshift($logs, $log_data);

        // Keep only last 500 entries
        $logs = array_slice($logs, 0, 500);
        update_option('dpdt_security_logs', $logs);
    }

    /**
     * Get client IP
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = explode(',', sanitize_text_field($_SERVER[$key]));
                return trim($ip[0]);
            }
        }
        return '0.0.0.0';
    }

    /**
     * Check if request is from admin
     */
    public function is_admin_request() {
        return current_user_can('manage_options');
    }

    /**
     * Prevent direct access response
     */
    public function forbidden_response() {
        wp_die(
            __('অননুমোদিত অ্যাক্সেস।', 'dpdt-trademark'),
            __('নিষিদ্ধ', 'dpdt-trademark'),
            array('response' => 403)
        );
    }
}
