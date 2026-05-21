<?php
if (!defined('ABSPATH')) exit;

/**
 * Dashboard Class
 * Admin dashboard with statistics and quick actions
 */
class DPDT_Dashboard {

    private $db;

    public function __construct() {
        $this->db = new DPDT_Database();
    }

    /**
     * Render dashboard page
     */
    public function render() {
        $stats = $this->db->get_statistics();
        $recent_applications = $this->db->get_applications(array('limit' => 5));
        $recent_logs = $this->db->get_activity_logs(10);

        include DPDT_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Get dashboard widget data
     */
    public function get_widget_data() {
        return array(
            'stats' => $this->db->get_statistics(),
            'version' => DPDT_VERSION,
            'php_version' => PHP_VERSION,
            'wp_version' => get_bloginfo('version'),
            'db_version' => get_option('dpdt_db_version', '0'),
        );
    }
}
