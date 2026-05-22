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

    /**
     * Check if a specific table exists
     */
    public function table_exists($table_name = '') {
        global $wpdb;
        if (empty($table_name)) {
            $table_name = $this->table_applications;
        }
        $result = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name));
        return ($result === $table_name);
    }

    /**
     * Create all plugin tables using dbDelta
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // dbDelta requires:
        // - Each field on its own line
        // - Two spaces between PRIMARY KEY and the column definition
        // - KEY (not INDEX) for indexes
        // - No IF NOT EXISTS (dbDelta handles this)

        $sql_applications = "CREATE TABLE {$this->table_applications} (
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
            PRIMARY KEY  (id),
            UNIQUE KEY application_id (application_id),
            KEY status (status),
            KEY applicant_email (applicant_email),
            KEY certificate_number (certificate_number),
            KEY brand_name (brand_name)
        ) $charset_collate;";

        $sql_certificates = "CREATE TABLE {$this->table_certificates} (
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
            PRIMARY KEY  (id),
            UNIQUE KEY certificate_number (certificate_number),
            KEY verify_token (verify_token),
            KEY application_id (application_id)
        ) $charset_collate;";

        $sql_logs = "CREATE TABLE {$this->table_logs} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT 0,
            action varchar(100) NOT NULL,
            object_type varchar(50) DEFAULT '',
            object_id bigint(20) DEFAULT 0,
            details text DEFAULT '',
            ip_address varchar(45) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_applications);
        dbDelta($sql_certificates);
        dbDelta($sql_logs);

        // Run upgrade to add any missing columns
        $this->upgrade_tables();

        update_option('dpdt_db_version', DPDT_DB_VERSION);
    }

    /**
     * Upgrade tables - add any missing columns using raw ALTER TABLE
     * This handles cases where dbDelta doesn't add columns properly
     */
    public function upgrade_tables() {
        global $wpdb;

        // Define all expected columns for applications table
        $expected_columns = array(
            'application_id'     => "VARCHAR(50) NOT NULL DEFAULT ''",
            'applicant_name'     => "VARCHAR(255) NOT NULL DEFAULT ''",
            'applicant_name_bn'  => "VARCHAR(255) DEFAULT ''",
            'applicant_email'    => "VARCHAR(255) NOT NULL DEFAULT ''",
            'applicant_phone'    => "VARCHAR(20) NOT NULL DEFAULT ''",
            'applicant_address'  => "TEXT DEFAULT NULL",
            'brand_name'         => "VARCHAR(255) NOT NULL DEFAULT ''",
            'brand_name_bn'      => "VARCHAR(255) DEFAULT ''",
            'trademark_class'    => "VARCHAR(100) DEFAULT ''",
            'trademark_type'     => "VARCHAR(50) DEFAULT 'word'",
            'brand_logo_url'     => "TEXT DEFAULT NULL",
            'brand_logo_id'      => "BIGINT(20) DEFAULT 0",
            'description'        => "TEXT DEFAULT NULL",
            'owner_name'         => "VARCHAR(255) DEFAULT ''",
            'owner_name_bn'      => "VARCHAR(255) DEFAULT ''",
            'company_name'       => "VARCHAR(255) DEFAULT ''",
            'application_date'   => "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
            'registration_date'  => "DATETIME DEFAULT NULL",
            'approved_date'      => "DATETIME DEFAULT NULL",
            'expiry_date'        => "DATETIME DEFAULT NULL",
            'status'             => "VARCHAR(20) DEFAULT 'pending'",
            'certificate_number' => "VARCHAR(100) DEFAULT ''",
            'certificate_pdf_url'=> "TEXT DEFAULT NULL",
            'certificate_jpg_url'=> "TEXT DEFAULT NULL",
            'certificate_pdf_id' => "BIGINT(20) DEFAULT 0",
            'certificate_jpg_id' => "BIGINT(20) DEFAULT 0",
            'qr_code_url'        => "TEXT DEFAULT NULL",
            'qr_code_data'       => "TEXT DEFAULT NULL",
            'verify_url'         => "TEXT DEFAULT NULL",
            'admin_notes'        => "TEXT DEFAULT NULL",
            'ip_address'         => "VARCHAR(45) DEFAULT ''",
            'user_agent'         => "TEXT DEFAULT NULL",
            'honeypot_field'     => "VARCHAR(255) DEFAULT ''",
            'created_at'         => "DATETIME DEFAULT CURRENT_TIMESTAMP",
            'updated_at'         => "DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
        );

        // Only proceed if the table exists
        if (!$this->table_exists($this->table_applications)) {
            return;
        }

        // Get existing columns
        $existing_columns = array();
        $columns = $wpdb->get_results("SHOW COLUMNS FROM {$this->table_applications}");
        if ($columns) {
            foreach ($columns as $col) {
                $existing_columns[] = $col->Field;
            }
        }

        // Add missing columns
        foreach ($expected_columns as $column_name => $column_def) {
            if (!in_array($column_name, $existing_columns)) {
                $wpdb->query("ALTER TABLE {$this->table_applications} ADD COLUMN `{$column_name}` {$column_def}");
            }
        }
    }

    public function insert_application($data) {
        global $wpdb;

        // Ensure table exists before insert
        if (!$this->table_exists($this->table_applications)) {
            $this->create_tables();
        }

        $result = $wpdb->insert($this->table_applications, $data);
        if ($result) {
            $this->log_activity(0, 'application_submitted', 'application', $wpdb->insert_id, wp_json_encode($data));
            return $wpdb->insert_id;
        }

        // Log the error for debugging
        if (!empty($wpdb->last_error)) {
            error_log('DPDT insert_application error: ' . $wpdb->last_error);
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

        // Silently fail if log table doesn't exist yet
        if (!$this->table_exists($this->table_logs)) {
            return;
        }

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

        // Return zeros if table doesn't exist
        if (!$this->table_exists($this->table_applications)) {
            return array(
                'total' => 0,
                'pending' => 0,
                'approved' => 0,
                'rejected' => 0,
                'this_month' => 0,
                'this_year' => 0,
            );
        }

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
