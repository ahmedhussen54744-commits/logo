<?php
if (!defined('ABSPATH')) exit;

/**
 * Verification Class
 * Handles certificate verification via URL/QR code
 */
class DPDT_Verify {

    private $db;
    private $security;

    public function __construct() {
        $this->db = new DPDT_Database();
        $this->security = new DPDT_Security();
    }

    /**
     * Render verify page shortcode
     */
    public function render_verify_page($atts = array()) {
        $atts = shortcode_atts(array(
            'title' => __('সার্টিফিকেট যাচাই', 'dpdt-trademark'),
        ), $atts);

        ob_start();
        include DPDT_PLUGIN_DIR . 'templates/shortcodes/shortcode-verify.php';
        return ob_get_clean();
    }

    /**
     * Handle verify request from URL
     */
    public function handle_verify_request($token) {
        $token = sanitize_text_field($token);
        $result = $this->verify_certificate($token);

        if ($result) {
            set_query_var('dpdt_verify_result', $result);
            set_query_var('dpdt_verify_status', 'valid');
        } else {
            set_query_var('dpdt_verify_status', 'invalid');
        }
    }

    /**
     * AJAX verification handler
     */
    public function ajax_verify() {
        // Rate limiting
        $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '0.0.0.0';
        if (!$this->security->check_rate_limit('verify_' . $ip, 10, 60)) {
            wp_send_json_error(array('message' => __('অনেক বেশি অনুরোধ। ১ মিনিট পর চেষ্টা করুন।', 'dpdt-trademark')));
        }

        $token = isset($_POST['token']) ? sanitize_text_field(trim($_POST['token'])) : '';
        $cert_number = isset($_POST['certificate_number']) ? sanitize_text_field(trim($_POST['certificate_number'])) : '';

        if (empty($token) && empty($cert_number)) {
            wp_send_json_error(array('message' => __('সার্টিফিকেট নম্বর বা টোকেন প্রদান করুন।', 'dpdt-trademark')));
        }

        $result = null;

        if (!empty($cert_number)) {
            // Try by certificate number first
            $result = $this->verify_by_number($cert_number);

            // If not found, try by application ID
            if (!$result) {
                $result = $this->verify_by_application_id($cert_number);
            }
        }

        if (!$result && !empty($token)) {
            // Try by token
            $result = $this->verify_by_token($token);

            // If token looks like a cert number or application ID, try those too
            if (!$result) {
                $result = $this->verify_by_number($token);
            }
            if (!$result) {
                $result = $this->verify_by_application_id($token);
            }
        }

        if ($result) {
            wp_send_json_success(array(
                'message' => __('সার্টিফিকেট যাচাই সম্পন্ন! এটি একটি বৈধ সার্টিফিকেট।', 'dpdt-trademark'),
                'certificate' => $result,
                'status' => 'verified',
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('অবৈধ সার্টিফিকেট! এই তথ্য দিয়ে কোনো সার্টিফিকেট পাওয়া যায়নি।', 'dpdt-trademark'),
                'status' => 'invalid',
            ));
        }
    }

    /**
     * Verify by token
     */
    private function verify_by_token($token) {
        if (empty($token)) return null;

        $certificate = $this->db->get_certificate_by_token($token);
        if (!$certificate) {
            return null;
        }

        $application = $this->db->get_application_by_app_id($certificate->application_id);
        return $this->format_verify_result($application, $certificate);
    }

    /**
     * Verify by certificate number
     */
    private function verify_by_number($number) {
        if (empty($number)) return null;

        $application = $this->db->get_application_by_certificate($number);
        if (!$application) {
            return null;
        }

        // Allow verified even if status is approved or completed
        if (!in_array($application->status, array('approved', 'completed', 'active'))) {
            return null;
        }

        $certificate = $this->db->get_certificate_by_number($number);
        return $this->format_verify_result($application, $certificate);
    }

    /**
     * Verify by application ID
     */
    private function verify_by_application_id($app_id) {
        if (empty($app_id)) return null;

        $application = $this->db->get_application_by_app_id($app_id);
        if (!$application) {
            return null;
        }

        // Only return if application has been approved
        if (!in_array($application->status, array('approved', 'completed', 'active'))) {
            return null;
        }

        $certificate = null;
        if (!empty($application->certificate_number)) {
            $certificate = $this->db->get_certificate_by_number($application->certificate_number);
        }

        return $this->format_verify_result($application, $certificate);
    }

    /**
     * Verify certificate (general - used for URL-based verification)
     */
    private function verify_certificate($token) {
        // Try token first
        $result = $this->verify_by_token($token);
        if ($result) return $result;

        // Try as certificate number
        $result = $this->verify_by_number($token);
        if ($result) return $result;

        // Try as application ID
        $result = $this->verify_by_application_id($token);
        return $result;
    }

    /**
     * Format verification result
     */
    private function format_verify_result($application, $certificate = null) {
        if (!$application) return null;

        $expiry_date = isset($application->expiry_date) ? $application->expiry_date : '';
        $is_expired = false;
        if (!empty($expiry_date) && $expiry_date !== '0000-00-00 00:00:00') {
            $is_expired = strtotime($expiry_date) < time();
        }

        $result = array(
            'application_id' => isset($application->application_id) ? $application->application_id : '',
            'certificate_number' => isset($application->certificate_number) ? $application->certificate_number : '',
            'applicant_name' => isset($application->applicant_name) ? $application->applicant_name : '',
            'applicant_name_bn' => isset($application->applicant_name_bn) ? $application->applicant_name_bn : '',
            'brand_name' => isset($application->brand_name) ? $application->brand_name : '',
            'brand_name_bn' => isset($application->brand_name_bn) ? $application->brand_name_bn : '',
            'owner_name' => isset($application->owner_name) ? $application->owner_name : '',
            'owner_name_bn' => isset($application->owner_name_bn) ? $application->owner_name_bn : '',
            'company_name' => isset($application->company_name) ? $application->company_name : '',
            'trademark_class' => isset($application->trademark_class) ? $application->trademark_class : '',
            'trademark_type' => isset($application->trademark_type) ? $application->trademark_type : '',
            'brand_logo_url' => isset($application->brand_logo_url) ? $application->brand_logo_url : '',
            'application_date' => isset($application->application_date) ? $application->application_date : '',
            'registration_date' => isset($application->registration_date) ? $application->registration_date : '',
            'approved_date' => isset($application->approved_date) ? $application->approved_date : '',
            'expiry_date' => $expiry_date,
            'certificate_jpg_url' => isset($application->certificate_jpg_url) ? $application->certificate_jpg_url : '',
            'certificate_pdf_url' => isset($application->certificate_pdf_url) ? $application->certificate_pdf_url : '',
            'qr_code_url' => isset($application->qr_code_url) ? $application->qr_code_url : '',
            'verify_url' => isset($application->verify_url) ? $application->verify_url : '',
            'status' => isset($application->status) ? $application->status : '',
            'is_valid' => true,
            'is_expired' => $is_expired,
        );

        if ($certificate) {
            $result['is_valid'] = (bool) $certificate->is_valid;
            $result['revoked_at'] = isset($certificate->revoked_at) ? $certificate->revoked_at : '';
            $result['revoke_reason'] = isset($certificate->revoke_reason) ? $certificate->revoke_reason : '';
        }

        // Log verification
        $this->db->log_activity(0, 'certificate_verified', 'certificate', 0, wp_json_encode(array(
            'certificate_number' => $result['certificate_number'],
            'application_id' => $result['application_id'],
            'ip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
        )));

        return $result;
    }
}
