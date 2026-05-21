<?php
if (!defined('ABSPATH')) exit;

class DPDT_Database {
    
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table = $wpdb->prefix . 'dpdt_applications';
        
        $sql = "CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            app_code varchar(20) NOT NULL,
            brand_name varchar(255) NOT NULL,
            owner_name varchar(255) NOT NULL,
            owner_address text NOT NULL,
            owner_email varchar(255) NOT NULL,
            owner_phone varchar(50) NOT NULL,
            trademark_class varchar(100) NOT NULL,
            goods_services text NOT NULL,
            trademark_type varchar(50) NOT NULL,
            description text,
            logo_url varchar(500),
            priority_claim varchar(255),
            attorney_name varchar(255),
            attorney_address text,
            registration_number varchar(100),
            application_date datetime NOT NULL,
            registration_date datetime,
            approved_date datetime,
            expiry_date datetime,
            status varchar(20) DEFAULT 'pending',
            certificate_pdf varchar(500),
            certificate_jpg varchar(500),
            qr_code_url varchar(500),
            verify_url varchar(500),
            admin_notes text,
            ip_address varchar(50),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY app_code (app_code),
            KEY status (status),
            KEY owner_email (owner_email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Add default options
        add_option('dpdt_verify_base_url', home_url('/verify/'));
        add_option('dpdt_site_established', '2009');
        add_option('dpdt_certificate_prefix', 'DPDT');
    }
    
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'dpdt_applications';
    }
}
