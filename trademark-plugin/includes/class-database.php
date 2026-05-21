<?php
if (!defined('ABSPATH')) exit;

/**
 * Database Handler Class
 * Manages all database operations for the DPDT Trademark system
 */
class DPDT_Database {

    private $table_applications;
    private $table_certificates;
    private $table_logs;

    public function __construct() {
        global $wpdb;
        $this->table_applications = $wpdb->prefix . 'dpdt_applications';
        $this->table_certificates = $wpdb->prefix . 'dpdt_certificates';
        $this->table_logs = $wpdb->prefix . 'dpdt_activity_logs';
    }

    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql_applications = "CREATE TABLE IF NOT EXISTS {$this->table_applications} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            application_id varchar(50) NOT NULL,
            applicant_name varchar(255) NOT NULL,
            applicant_name_bn varchar(255) DEFAULT '',
            applicant_email varchar(255) NOT NULL,
            applicant_phone varchar(20) NOT NULL,
            applicant_address text DEFAULT '',
            brand_name varchar(255) NOT NULL,
            brand_name_bn varchar(255) DEFAULT '',
            trademark_class varchar(100) DEFAULT '',
            trademark_type varchar(50) DEFAULT 'word',
            brand_logo_url text DEFAULT '',
            brand_logo_id bigint(20) DEFAULT 0,
            description text DEFAULT '',
            owner_name varchar(255) DEFAULT '',
            owner_name_bn varchar(255) DEFAULT '',
            company_name varchar(255) DEFAULT '',
            application_date datetime NOT NULL,
            registration_date datetime DEFAULT NULL,
            approved_date datetime DEFAULT NULL,
            expiry_date datetime DEFAULT NULL,
            status varchar(20) DEFAULT 'pending',
            certificate_number varchar(100) DEFAULT '',
            certificate_pdf_url text DEFAULT '',
            certificate_jpg_url text DEFAULT '',
            certificate_pdf_id bigint(20) DEFAULT 0,
            certificate_jpg_id bigint(20) DEFAULT 0,
            qr_code_url text DEFAULT '',
            qr_code_data text DEFAULT '',
            verify_url text DEFAULT '',
            admin_notes text DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            user_agent text DEFAULT '',
            honeypot_field varchar(255) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY application_id (application_id),
            KEY status (status),
            KEY applicant_email (applicant_email),
            KEY certificate_number (certificate_number),
            KEY brand_name (brand_name)
        ) $charset_collate;";

        $sql_certificates = "CREATE TABLE IF NOT EXISTS {$this->table_certificates} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            certificate_number varchar(100) NOT NULL,
            application_id varchar(50) NOT NULL,
            holder_name varchar(255) NOT NULL,
            brand_name varchar(255) NOT NULL,
            issue_date datetime NOT NULL,
            expiry_date datetime NOT NULL,
            pdf_path text DEFAULT '',
            jpg_path text DEFAULT '',
            qr_data text DEFAULT '',
            verify_token varchar(255) NOT NULL,
            is_valid tinyint(1) DEFAULT 1,
            revoked_at datetime DEFAULT NULL,
            revoke_reason text DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY certificate_number (certificate_number),
            KEY verify_token (verify_token),
            KEY application_id (application_id)
        ) $charset_collate;";

        $sql_logs = "CREATE TABLE IF NOT EXISTS {$this->table_logs} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT 0,
            action varchar(100) NOT NULL,
            object_type varchar(50) DEFAULT '',
            object_id bigint(20) DEFAULT 0,
            details text DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_applications);
        dbDelta($sql_certificates);
        dbDelta($sql_logs);

        update_option('dpdt_db_version', DPDT_DB_VERSION);
    }

    public function insert_application($data) {
        global $wpdb;
        $result = $wpdb->insert($this->table_applications, $data);
        if ($result) {
            $this->log_activity(0, 'application_submitted', 'application', $wpdb->insert_id, wp_json_encode($data));
            return $wpdb->insert_id;
        }
        return false;
    }

    public function get_application($id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_applications} WHERE id = %d",
            $id
        ));
    }

    public function get_application_by_app_id($application_id) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_applications} WHERE application_id = %s",
            $application_id
        ));
    }

    public function get_application_by_certificate($certificate_number) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_applications} WHERE certificate_number = %s",
            $certificate_number
        ));
    }

    public function get_applications($args = array()) {
        global $wpdb;

        $defaults = array(
            'status' => '',
            'search' => '',
            'orderby' => 'created_at',
            'order' => 'DESC',
            'limit' => 20,
            'offset' => 0,
        );
        $args = wp_parse_args($args, $defaults);

        $where = "WHERE 1=1";
        $values = array();

        if (!empty($args['status'])) {
            $where .= " AND status = %s";
            $values[] = $args['status'];
        }

        if (!empty($args['search'])) {
            $where .= " AND (applicant_name LIKE %s OR brand_name LIKE %s OR application_id LIKE %s OR certificate_number LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($args['search']) . '%';
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
            $values[] = $search_term;
        }

        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);
        if (!$orderby) {
            $orderby = 'created_at DESC';
        }

        $sql = "SELECT * FROM {$this->table_applications} {$where} ORDER BY {$orderby} LIMIT %d OFFSET %d";
        $values[] = intval($args['limit']);
        $values[] = intval($args['offset']);

        if (!empty($values)) {
            $sql = $wpdb->prepare($sql, $values);
        }

        return $wpdb->get_results($sql);
    }

    public function count_applications($status = '') {
        global $wpdb;
        if (!empty($status)) {
            return $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_applications} WHERE status = %s",
                $status
            ));
        }
        return $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_applications}");
    }

    public function update_application($id, $data) {
        global $wpdb;
        $data['updated_at'] = current_time('mysql');
        $result = $wpdb->update($this->table_applications, $data, array('id' => $id));
        if ($result !== false) {
            $this->log_activity(get_current_user_id(), 'application_updated', 'application', $id, wp_json_encode($data));
        }
        return $result;
    }

    public function delete_application($id) {
        global $wpdb;
        $this->log_activity(get_current_user_id(), 'application_deleted', 'application', $id, '');
        return $wpdb->delete($this->table_applications, array('id' => $id));
    }

    public function insert_certificate($data) {
        global $wpdb;
        $result = $wpdb->insert($this->table_certificates, $data);
        if ($result) {
            return $wpdb->insert_id;
        }
        return false;
    }

    public function get_certificate_by_token($token) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_certificates} WHERE verify_token = %s AND is_valid = 1",
            $token
        ));
    }

    public function get_certificate_by_number($number) {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_certificates} WHERE certificate_number = %s",
            $number
        ));
    }

    public function log_activity($user_id, $action, $object_type = '', $object_id = 0, $details = '') {
        global $wpdb;
        $wpdb->insert($this->table_logs, array(
            'user_id' => $user_id,
            'action' => $action,
            'object_type' => $object_type,
            'object_id' => $object_id,
            'details' => $details,
            'ip_address' => $this->get_client_ip(),
            'created_at' => current_time('mysql'),
        ));
    }

    public function get_activity_logs($limit = 50) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_logs} ORDER BY created_at DESC LIMIT %d",
            $limit
        ));
    }

    public function get_statistics() {
        global $wpdb;
        return array(
            'total' => $this->count_applications(),
            'pending' => $this->count_applications('pending'),
            'approved' => $this->count_applications('approved'),
            'rejected' => $this->count_applications('rejected'),
            'this_month' => $wpdb->get_var(
                "SELECT COUNT(*) FROM {$this->table_applications} WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
            ),
            'this_year' => $wpdb->get_var(
                "SELECT COUNT(*) FROM {$this->table_applications} WHERE YEAR(created_at) = YEAR(NOW())"
            ),
        );
    }

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
}
