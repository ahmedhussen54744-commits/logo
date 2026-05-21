<?php
if (!defined('ABSPATH')) exit;

/**
 * Certificate Generator Class
 * Handles certificate creation, PDF/JPG management
 */
class DPDT_Certificate_Generator {

    private $db;
    private $qrcode;
    private $security;

    public function __construct() {
        $this->db = new DPDT_Database();
        $this->qrcode = new DPDT_QRCode();
        $this->security = new DPDT_Security();
    }

    /**
     * Generate certificate for approved application
     */
    public function generate($application_id) {
        $application = $this->db->get_application($application_id);
        if (!$application || $application->status !== 'approved') {
            return new WP_Error('invalid', 'Application not found or not approved');
        }

        // Generate verify token
        $verify_token = $this->security->generate_token(16);
        $verify_url = get_option('dpdt_verify_base_url', home_url('/verify/')) . '?token=' . $verify_token;

        // Generate QR code
        $qr_result = $this->qrcode->generate_for_certificate($application->application_id, $verify_token);

        // Update application with QR and verify data
        $this->db->update_application($application_id, array(
            'qr_code_url' => isset($qr_result['url']) ? $qr_result['url'] : '',
            'qr_code_data' => isset($qr_result['data']) ? $qr_result['data'] : '',
            'verify_url' => $verify_url,
        ));

        // Insert certificate record
        $cert_data = array(
            'certificate_number' => $application->certificate_number,
            'application_id' => $application->application_id,
            'holder_name' => $application->applicant_name,
            'brand_name' => $application->brand_name,
            'issue_date' => $application->approved_date ? $application->approved_date : current_time('mysql'),
            'expiry_date' => $application->expiry_date ? $application->expiry_date : date('Y-m-d H:i:s', strtotime('+7 years')),
            'pdf_path' => $application->certificate_pdf_url,
            'jpg_path' => $application->certificate_jpg_url,
            'qr_data' => $verify_url,
            'verify_token' => $verify_token,
            'is_valid' => 1,
        );

        $cert_id = $this->db->insert_certificate($cert_data);

        return array(
            'certificate_id' => $cert_id,
            'certificate_number' => $application->certificate_number,
            'verify_url' => $verify_url,
            'verify_token' => $verify_token,
            'qr_url' => isset($qr_result['url']) ? $qr_result['url'] : '',
        );
    }

    /**
     * Regenerate QR code for existing certificate
     */
    public function regenerate_qr($application_id) {
        $application = $this->db->get_application($application_id);
        if (!$application) {
            return new WP_Error('not_found', 'Application not found');
        }

        $verify_url = $application->verify_url;
        if (empty($verify_url)) {
            $verify_token = $this->security->generate_token(16);
            $verify_url = get_option('dpdt_verify_base_url', home_url('/verify/')) . '?token=' . $verify_token;
        }

        $qr_result = $this->qrcode->update_qr($application->application_id, $verify_url);

        $this->db->update_application($application_id, array(
            'qr_code_url' => isset($qr_result['url']) ? $qr_result['url'] : '',
            'qr_code_data' => $verify_url,
            'verify_url' => $verify_url,
        ));

        return $qr_result;
    }

    /**
     * Update verify URL for a certificate
     */
    public function update_verify_url($application_id, $new_url) {
        $application = $this->db->get_application($application_id);
        if (!$application) {
            return new WP_Error('not_found', 'Application not found');
        }

        // Update QR code with new URL
        $qr_result = $this->qrcode->update_qr($application->application_id, $new_url);

        // Update application
        $this->db->update_application($application_id, array(
            'verify_url' => $new_url,
            'qr_code_url' => isset($qr_result['url']) ? $qr_result['url'] : '',
            'qr_code_data' => $new_url,
        ));

        return true;
    }

    /**
     * Revoke a certificate
     */
    public function revoke($certificate_number, $reason = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'dpdt_certificates';

        $result = $wpdb->update(
            $table,
            array(
                'is_valid' => 0,
                'revoked_at' => current_time('mysql'),
                'revoke_reason' => sanitize_textarea_field($reason),
            ),
            array('certificate_number' => $certificate_number)
        );

        if ($result !== false) {
            // Also update application status
            $application = $this->db->get_application_by_certificate($certificate_number);
            if ($application) {
                $this->db->update_application($application->id, array(
                    'status' => 'revoked',
                    'admin_notes' => 'Certificate revoked: ' . $reason,
                ));
            }

            $this->db->log_activity(
                get_current_user_id(),
                'certificate_revoked',
                'certificate',
                0,
                wp_json_encode(array('certificate' => $certificate_number, 'reason' => $reason))
            );
        }

        return $result;
    }

    /**
     * Get certificate template data
     */
    public function get_template_data($application_id) {
        $application = $this->db->get_application($application_id);
        if (!$application) return null;

        return array(
            'certificate_number' => $application->certificate_number,
            'applicant_name' => $application->applicant_name,
            'applicant_name_bn' => $application->applicant_name_bn,
            'brand_name' => $application->brand_name,
            'brand_name_bn' => $application->brand_name_bn,
            'owner_name' => $application->owner_name,
            'company_name' => $application->company_name,
            'trademark_class' => $application->trademark_class,
            'brand_logo_url' => $application->brand_logo_url,
            'application_date' => $application->application_date,
            'registration_date' => $application->registration_date,
            'approved_date' => $application->approved_date,
            'expiry_date' => $application->expiry_date,
            'qr_code_url' => $application->qr_code_url,
            'verify_url' => $application->verify_url,
            'site_name' => get_option('dpdt_site_name', ''),
        );
    }

    /**
     * Upload certificate files (PDF + JPG)
     */
    public function upload_files($application_id, $pdf_file = null, $jpg_file = null) {
        $update_data = array();

        if ($pdf_file && $pdf_file['error'] === UPLOAD_ERR_OK) {
            $result = $this->process_upload($pdf_file, 'pdf');
            if (!is_wp_error($result)) {
                $update_data['certificate_pdf_url'] = $result['url'];
                $update_data['certificate_pdf_id'] = $result['id'];
            }
        }

        if ($jpg_file && $jpg_file['error'] === UPLOAD_ERR_OK) {
            $result = $this->process_upload($jpg_file, 'jpg');
            if (!is_wp_error($result)) {
                $update_data['certificate_jpg_url'] = $result['url'];
                $update_data['certificate_jpg_id'] = $result['id'];
            }
        }

        if (!empty($update_data)) {
            return $this->db->update_application($application_id, $update_data);
        }

        return false;
    }

    /**
     * Process file upload
     */
    private function process_upload($file, $type) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/dpdt-certificates/' . $type . '/';

        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        $filename = sanitize_file_name($file['name']);
        $filename = wp_unique_filename($target_dir, $filename);
        $target_path = $target_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $attachment = array(
                'post_mime_type' => $file['type'],
                'post_title' => pathinfo($filename, PATHINFO_FILENAME),
                'post_content' => '',
                'post_status' => 'inherit',
            );

            $attach_id = wp_insert_attachment($attachment, $target_path);
            if (!is_wp_error($attach_id)) {
                $attach_data = wp_generate_attachment_metadata($attach_id, $target_path);
                wp_update_attachment_metadata($attach_id, $attach_data);
                return array(
                    'id' => $attach_id,
                    'url' => $upload_dir['baseurl'] . '/dpdt-certificates/' . $type . '/' . $filename,
                );
            }
        }

        return new WP_Error('upload_failed', 'File upload failed');
    }
}
