<?php
if (!defined('ABSPATH')) exit;

class DPDT_Verify {
    
    public static function get_verified_data($code) {
        if (empty($code)) return null;
        
        $code = sanitize_text_field($code);
        $app = DPDT_Application::get_application_by_code($code);
        
        if (!$app || $app->status !== 'approved') {
            return null;
        }
        
        return $app;
    }
    
    public static function render_verified_badge($app) {
        if (!$app) return '';
        
        $output = '<div class="verify-success">';
        $output .= '<div class="verify-badge"><i class="fas fa-check-circle"></i> Verified</div>';
        $output .= '</div>';
        
        return $output;
    }
    
    public static function get_qr_code_url($app) {
        if (!empty($app->qr_code_url)) {
            return $app->qr_code_url;
        }
        
        // Generate QR code URL using Google Charts API
        $verify_url = !empty($app->verify_url) ? $app->verify_url : home_url('/verify/' . $app->app_code);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($verify_url);
    }
}
