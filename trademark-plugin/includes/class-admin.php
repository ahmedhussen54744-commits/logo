<?php
if (!defined('ABSPATH')) exit;

/**
 * Admin Class
 * Handles admin-side application management
 */
class DPDT_Admin {

    private $db;
    private $security;

    public function __construct() {
        $this->db = new DPDT_Database();
        $this->security = new DPDT_Security();
    }

    /**
     * Render applications list page
     */
    public function render_applications() {
        // Handle bulk actions
        if (isset($_POST['dpdt_bulk_action']) && isset($_POST['_dpdt_nonce'])) {
            if ($this->security->verify_nonce($_POST['_dpdt_nonce'])) {
                $this->handle_bulk_actions();
            }
        }

        $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;

        $applications = $this->db->get_applications(array(
            'status' => $status,
            'search' => $search,
            'limit' => $per_page,
            'offset' => ($paged - 1) * $per_page,
        ));

        $total = $this->db->count_applications($status);
        $total_pages = ceil($total / $per_page);

        include DPDT_PLUGIN_DIR . 'admin/views/applications-list.php';
    }

    /**
     * Approve application via AJAX
     */
    public function approve_application() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        if (!isset($_POST['nonce']) || !$this->security->verify_nonce($_POST['nonce'])) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }

        $app_id = isset($_POST['application_id']) ? intval($_POST['application_id']) : 0;
        if (!$app_id) {
            wp_send_json_error(array('message' => 'Invalid application'));
        }

        $application = $this->db->get_application($app_id);
        if (!$application) {
            wp_send_json_error(array('message' => 'Application not found'));
        }

        // Generate certificate number
        $cert_number = $this->security->generate_certificate_number();
        $verify_token = $this->security->generate_token(16);
        $verify_url = get_option('dpdt_verify_base_url', home_url('/verify/')) . '?token=' . $verify_token;

        // Handle PDF and JPG uploads if present
        $pdf_url = '';
        $jpg_url = '';
        $pdf_id = 0;
        $jpg_id = 0;

        if (!empty($_FILES['certificate_pdf']) && $_FILES['certificate_pdf']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handle_certificate_upload($_FILES['certificate_pdf'], 'pdf');
            if (!is_wp_error($upload)) {
                $pdf_url = $upload['url'];
                $pdf_id = $upload['id'];
            }
        }

        if (!empty($_FILES['certificate_jpg']) && $_FILES['certificate_jpg']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->handle_certificate_upload($_FILES['certificate_jpg'], 'jpg');
            if (!is_wp_error($upload)) {
                $jpg_url = $upload['url'];
                $jpg_id = $upload['id'];
            }
        }

        // Get dates from POST or use defaults
        $registration_date = !empty($_POST['registration_date']) ? sanitize_text_field($_POST['registration_date']) : current_time('mysql');
        $approved_date = !empty($_POST['approved_date']) ? sanitize_text_field($_POST['approved_date']) : current_time('mysql');
        $expiry_date = !empty($_POST['expiry_date']) ? sanitize_text_field($_POST['expiry_date']) : date('Y-m-d H:i:s', strtotime('+7 years'));
        $admin_notes = !empty($_POST['admin_notes']) ? sanitize_textarea_field($_POST['admin_notes']) : '';

        // Update application
        $update_data = array(
            'status' => 'approved',
            'certificate_number' => $cert_number,
            'registration_date' => $registration_date,
            'approved_date' => $approved_date,
            'expiry_date' => $expiry_date,
            'certificate_pdf_url' => $pdf_url,
            'certificate_jpg_url' => $jpg_url,
            'certificate_pdf_id' => $pdf_id,
            'certificate_jpg_id' => $jpg_id,
            'verify_url' => $verify_url,
            'admin_notes' => $admin_notes,
        );

        $result = $this->db->update_application($app_id, $update_data);

        if ($result !== false) {
            // Insert certificate record
            $this->db->insert_certificate(array(
                'certificate_number' => $cert_number,
                'application_id' => $application->application_id,
                'holder_name' => $application->applicant_name,
                'brand_name' => $application->brand_name,
                'issue_date' => $approved_date,
                'expiry_date' => $expiry_date,
                'pdf_path' => $pdf_url,
                'jpg_path' => $jpg_url,
                'verify_token' => $verify_token,
                'is_valid' => 1,
            ));

            // Send approval email
            $this->send_approval_email($application, $cert_number, $verify_url);

            wp_send_json_success(array(
                'message' => __('আবেদন অনুমোদিত হয়েছে!', 'dpdt-trademark'),
                'certificate_number' => $cert_number,
                'verify_url' => $verify_url,
            ));
        } else {
            wp_send_json_error(array('message' => __('আপডেট করতে সমস্যা হয়েছে।', 'dpdt-trademark')));
        }
    }

    /**
     * Reject application via AJAX
     */
    public function reject_application() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        if (!isset($_POST['nonce']) || !$this->security->verify_nonce($_POST['nonce'])) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }

        $app_id = isset($_POST['application_id']) ? intval($_POST['application_id']) : 0;
        $reason = isset($_POST['reason']) ? sanitize_textarea_field($_POST['reason']) : '';

        if (!$app_id) {
            wp_send_json_error(array('message' => 'Invalid application'));
        }

        $application = $this->db->get_application($app_id);
        if (!$application) {
            wp_send_json_error(array('message' => 'Application not found'));
        }

        $result = $this->db->update_application($app_id, array(
            'status' => 'rejected',
            'admin_notes' => $reason,
        ));

        if ($result !== false) {
            // Send rejection email
            $this->send_rejection_email($application, $reason);
            wp_send_json_success(array('message' => __('আবেদন প্রত্যাখ্যান করা হয়েছে।', 'dpdt-trademark')));
        } else {
            wp_send_json_error(array('message' => __('আপডেট করতে সমস্যা হয়েছে।', 'dpdt-trademark')));
        }
    }

    /**
     * Handle certificate file upload
     */
    private function handle_certificate_upload($file, $type) {
        $allowed = array(
            'pdf' => array('application/pdf'),
            'jpg' => array('image/jpeg', 'image/png', 'image/jpg'),
        );

        if (!isset($allowed[$type])) {
            return new WP_Error('invalid_type', 'Invalid file type');
        }

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

        return new WP_Error('upload_failed', 'Upload failed');
    }

    /**
     * Handle bulk actions
     */
    private function handle_bulk_actions() {
        $action = sanitize_text_field($_POST['dpdt_bulk_action']);
        $ids = isset($_POST['application_ids']) ? array_map('intval', $_POST['application_ids']) : array();

        if (empty($ids)) return;

        switch ($action) {
            case 'delete':
                foreach ($ids as $id) {
                    $this->db->delete_application($id);
                }
                break;
            case 'mark_pending':
                foreach ($ids as $id) {
                    $this->db->update_application($id, array('status' => 'pending'));
                }
                break;
        }
    }

    /**
     * Send approval email
     */
    private function send_approval_email($application, $cert_number, $verify_url) {
        $site_name = get_option('dpdt_site_name', get_bloginfo('name'));
        $subject = sprintf('[%s] সার্টিফিকেট অনুমোদিত - %s', $site_name, $cert_number);

        $message = sprintf(
            "প্রিয় %s,\n\n" .
            "আপনার ট্রেডমার্ক সার্টিফিকেট আবেদন অনুমোদিত হয়েছে!\n\n" .
            "সার্টিফিকেট নম্বর: %s\n" .
            "ব্র্যান্ড: %s\n" .
            "যাচাই লিংক: %s\n\n" .
            "ধন্যবাদ,\n%s",
            $application->applicant_name,
            $cert_number,
            $application->brand_name,
            $verify_url,
            $site_name
        );

        wp_mail($application->applicant_email, $subject, $message);
    }

    /**
     * Send rejection email
     */
    private function send_rejection_email($application, $reason) {
        $site_name = get_option('dpdt_site_name', get_bloginfo('name'));
        $subject = sprintf('[%s] আবেদন প্রত্যাখ্যান - %s', $site_name, $application->application_id);

        $message = sprintf(
            "প্রিয় %s,\n\n" .
            "দুঃখিত, আপনার ট্রেডমার্ক আবেদন প্রত্যাখ্যান করা হয়েছে।\n\n" .
            "আবেদন নম্বর: %s\n" .
            "কারণ: %s\n\n" .
            "আপনি সংশোধন করে পুনরায় আবেদন করতে পারেন।\n\n" .
            "ধন্যবাদ,\n%s",
            $application->applicant_name,
            $application->application_id,
            !empty($reason) ? $reason : 'নির্দিষ্ট করা হয়নি',
            $site_name
        );

        wp_mail($application->applicant_email, $subject, $message);
    }
}
