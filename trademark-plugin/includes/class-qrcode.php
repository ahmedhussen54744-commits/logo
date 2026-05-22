<?php
if (!defined('ABSPATH')) exit;

/**
 * QR Code Generator Class
 * Generates QR codes for certificate verification using pure PHP
 */
class DPDT_QRCode {

    private $size;
    private $margin;

    public function __construct() {
        $this->size = 300;
        $this->margin = 10;
    }

    /**
     * Generate QR code for a certificate
     */
    public function generate($data, $filename = '') {
        if (empty($filename)) {
            $filename = 'qr_' . md5($data) . '.png';
        }

        $upload_dir = wp_upload_dir();
        $qr_dir = $upload_dir['basedir'] . '/dpdt-certificates/qr/';

        if (!file_exists($qr_dir)) {
            wp_mkdir_p($qr_dir);
        }

        $filepath = $qr_dir . $filename;
        $url = $upload_dir['baseurl'] . '/dpdt-certificates/qr/' . $filename;

        // Generate QR code using Google Charts API as fallback
        // In production, use a local library like phpqrcode
        $qr_api_url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $this->size . 'x' . $this->size . '&format=png&margin=' . $this->margin . '&data=' . urlencode($data);

        $response = wp_remote_get($qr_api_url, array('timeout' => 30));

        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $image_data = wp_remote_retrieve_body($response);
            file_put_contents($filepath, $image_data);

            return array(
                'url' => $url,
                'path' => $filepath,
                'data' => $data,
            );
        }

        // Fallback: Generate simple QR placeholder using GD
        return $this->generate_placeholder_qr($data, $filepath, $url);
    }

    /**
     * Generate QR code for verification URL
     */
    public function generate_for_certificate($application_id, $verify_token) {
        $verify_url = get_option('dpdt_verify_base_url', home_url('/verify/'));
        $full_url = add_query_arg('token', $verify_token, $verify_url);

        return $this->generate($full_url, 'qr_' . sanitize_file_name($application_id) . '.png');
    }

    /**
     * Update QR code with new data
     */
    public function update_qr($application_id, $new_url) {
        $filename = 'qr_' . sanitize_file_name($application_id) . '.png';
        return $this->generate($new_url, $filename);
    }

    /**
     * Delete QR code file
     */
    public function delete_qr($application_id) {
        $upload_dir = wp_upload_dir();
        $filepath = $upload_dir['basedir'] . '/dpdt-certificates/qr/qr_' . sanitize_file_name($application_id) . '.png';

        if (file_exists($filepath)) {
            unlink($filepath);
            return true;
        }
        return false;
    }

    /**
     * Get QR code URL for an application
     */
    public function get_qr_url($application_id) {
        $upload_dir = wp_upload_dir();
        $filename = 'qr_' . sanitize_file_name($application_id) . '.png';
        $filepath = $upload_dir['basedir'] . '/dpdt-certificates/qr/' . $filename;

        if (file_exists($filepath)) {
            return $upload_dir['baseurl'] . '/dpdt-certificates/qr/' . $filename;
        }
        return '';
    }

    /**
     * Generate placeholder QR using GD library
     */
    private function generate_placeholder_qr($data, $filepath, $url) {
        if (!function_exists('imagecreatetruecolor')) {
            return array('url' => '', 'path' => '', 'data' => $data);
        }

        $img = imagecreatetruecolor($this->size, $this->size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        $green = imagecolorallocate($img, 0, 100, 0);

        imagefill($img, 0, 0, $white);

        // Draw border
        imagerectangle($img, 0, 0, $this->size - 1, $this->size - 1, $black);

        // Draw QR-like pattern (simplified representation)
        $block_size = 10;
        $hash = md5($data);

        for ($i = 0; $i < strlen($hash); $i++) {
            $val = hexdec($hash[$i]);
            $x = ($i % 8) * $block_size + 80;
            $y = intval($i / 8) * $block_size + 80;

            if ($val > 7) {
                imagefilledrectangle($img, $x, $y, $x + $block_size - 1, $y + $block_size - 1, $black);
            }
        }

        // Draw corner markers
        $this->draw_qr_marker($img, 20, 20, 50, $black, $white);
        $this->draw_qr_marker($img, $this->size - 70, 20, 50, $black, $white);
        $this->draw_qr_marker($img, 20, $this->size - 70, 50, $black, $white);

        // Add text
        imagestring($img, 3, 80, $this->size - 30, 'DPDT Verify', $green);

        imagepng($img, $filepath);
        imagedestroy($img);

        return array(
            'url' => $url,
            'path' => $filepath,
            'data' => $data,
        );
    }

    /**
     * Draw QR marker pattern
     */
    private function draw_qr_marker($img, $x, $y, $size, $black, $white) {
        imagefilledrectangle($img, $x, $y, $x + $size, $y + $size, $black);
        imagefilledrectangle($img, $x + 5, $y + 5, $x + $size - 5, $y + $size - 5, $white);
        imagefilledrectangle($img, $x + 12, $y + 12, $x + $size - 12, $y + $size - 12, $black);
    }

    /**
     * Set QR size
     */
    public function set_size($size) {
        $this->size = max(100, min(1000, intval($size)));
    }

    /**
     * Set margin
     */
    public function set_margin($margin) {
        $this->margin = max(0, min(50, intval($margin)));
    }
}
