<?php
if (!defined('ABSPATH')) exit;

/**
 * Logo Settings Admin Page
 * Manages all logo uploads from WP Admin
 */
class DPDT_Logo_Settings {

    private $logo_manager;

    public function __construct() {
        $this->logo_manager = new DPDT_Logo_Manager();
    }

    /**
     * Render logo settings page
     */
    public function render() {
        // Handle form submission
        if (isset($_POST['dpdt_save_logos']) && isset($_POST['_dpdt_nonce'])) {
            if (wp_verify_nonce($_POST['_dpdt_nonce'], DPDT_NONCE_ACTION)) {
                $this->save_logos();
            }
        }

        $logos = $this->logo_manager->get_logos();
        $categories = (new DPDT_Category_Manager())->get_categories_list();

        include DPDT_PLUGIN_DIR . 'admin/views/logo-settings.php';
    }

    /**
     * Save logo settings
     */
    private function save_logos() {
        $data = array();

        // Header logo
        if (isset($_POST['header_logo_id'])) {
            $data['header_logo_id'] = intval($_POST['header_logo_id']);
            if ($data['header_logo_id']) {
                $data['header_logo'] = wp_get_attachment_url($data['header_logo_id']);
            } else {
                $data['header_logo'] = '';
            }
        }
        if (isset($_POST['header_logo_alt'])) {
            $data['header_logo_alt'] = sanitize_text_field($_POST['header_logo_alt']);
        }

        // Favicon
        if (isset($_POST['favicon_id'])) {
            $data['favicon_id'] = intval($_POST['favicon_id']);
            if ($data['favicon_id']) {
                $data['favicon'] = wp_get_attachment_url($data['favicon_id']);
            } else {
                $data['favicon'] = '';
            }
        }

        // Footer logo
        if (isset($_POST['footer_logo_id'])) {
            $data['footer_logo_id'] = intval($_POST['footer_logo_id']);
            if ($data['footer_logo_id']) {
                $data['footer_logo'] = wp_get_attachment_url($data['footer_logo_id']);
            } else {
                $data['footer_logo'] = '';
            }
        }

        // Category logos
        if (isset($_POST['category_logos']) && is_array($_POST['category_logos'])) {
            $cat_logos = array();
            foreach ($_POST['category_logos'] as $slug => $logo_id) {
                $logo_id = intval($logo_id);
                if ($logo_id) {
                    $cat_logos[sanitize_title($slug)] = array(
                        'id' => $logo_id,
                        'url' => wp_get_attachment_url($logo_id),
                    );
                }
            }
            $data['category_logos'] = $cat_logos;
        }

        $this->logo_manager->save_logos($data);
        add_settings_error('dpdt_logos', 'logos_saved', __('লোগো সেটিংস সংরক্ষিত হয়েছে।', 'dpdt-trademark'), 'success');
    }
}
