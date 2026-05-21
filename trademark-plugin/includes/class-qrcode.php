<?php
if (!defined('ABSPATH')) exit;

class DPDT_QRCode {
    
    /**
     * Generate QR code URL for verification
     * Uses external API or custom QR if admin uploaded one
     */
    public static function generate($data, $size = 200) {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data) . '&format=png&margin=10';
    }
    
    /**
     * Get QR for application - uses custom if set by admin
     */
    public static function get_for_application($app) {
        // If admin has set a custom QR code image
        if (!empty($app->qr_code_url)) {
            return $app->qr_code_url;
        }
        
        // Generate from verify URL
        $verify_url = !empty($app->verify_url) 
            ? $app->verify_url 
            : get_option('dpdt_verify_base_url', home_url('/verify/')) . $app->app_code;
        
        return self::generate($verify_url);
    }
}
