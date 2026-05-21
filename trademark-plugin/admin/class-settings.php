<?php
if (!defined('ABSPATH')) exit;

/**
 * Settings Class
 * Manages all plugin settings from WP Admin
 */
class DPDT_Settings {

    private $option_group = 'dpdt_settings_group';

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Register all settings
     */
    public function register_settings() {
        // General Settings
        register_setting($this->option_group, 'dpdt_site_name', 'sanitize_text_field');
        register_setting($this->option_group, 'dpdt_site_name_en', 'sanitize_text_field');
        register_setting($this->option_group, 'dpdt_site_description', 'sanitize_textarea_field');
        register_setting($this->option_group, 'dpdt_site_phone', 'sanitize_text_field');
        register_setting($this->option_group, 'dpdt_site_email', 'sanitize_email');
        register_setting($this->option_group, 'dpdt_site_address', 'sanitize_textarea_field');
        register_setting($this->option_group, 'dpdt_site_established', 'sanitize_text_field');
        register_setting($this->option_group, 'dpdt_copyright_text', 'sanitize_text_field');

        // Social Links
        register_setting($this->option_group, 'dpdt_social_facebook', 'esc_url_raw');
        register_setting($this->option_group, 'dpdt_social_twitter', 'esc_url_raw');
        register_setting($this->option_group, 'dpdt_social_youtube', 'esc_url_raw');
        register_setting($this->option_group, 'dpdt_social_linkedin', 'esc_url_raw');

        // Certificate Settings
        register_setting($this->option_group, 'dpdt_verify_base_url', 'esc_url_raw');
        register_setting($this->option_group, 'dpdt_certificate_prefix', 'sanitize_text_field');

        // Security Settings
        register_setting($this->option_group, 'dpdt_rate_limit_attempts', 'intval');
        register_setting($this->option_group, 'dpdt_rate_limit_window', 'intval');

        // Email Settings
        register_setting($this->option_group, 'dpdt_admin_notification_email', 'sanitize_email');
        register_setting($this->option_group, 'dpdt_email_from_name', 'sanitize_text_field');
        register_setting($this->option_group, 'dpdt_email_from_address', 'sanitize_email');
    }

    /**
     * Render settings page
     */
    public function render() {
        // Handle form save
        if (isset($_POST['dpdt_save_settings']) && isset($_POST['_dpdt_nonce'])) {
            if (wp_verify_nonce($_POST['_dpdt_nonce'], DPDT_NONCE_ACTION)) {
                $this->save_settings();
            }
        }

        include DPDT_PLUGIN_DIR . 'admin/views/settings.php';
    }

    /**
     * Save settings from form
     */
    private function save_settings() {
        $fields = array(
            'dpdt_site_name' => 'text',
            'dpdt_site_name_en' => 'text',
            'dpdt_site_description' => 'textarea',
            'dpdt_site_phone' => 'text',
            'dpdt_site_email' => 'email',
            'dpdt_site_address' => 'textarea',
            'dpdt_site_established' => 'text',
            'dpdt_copyright_text' => 'text',
            'dpdt_social_facebook' => 'url',
            'dpdt_social_twitter' => 'url',
            'dpdt_social_youtube' => 'url',
            'dpdt_social_linkedin' => 'url',
            'dpdt_verify_base_url' => 'url',
            'dpdt_certificate_prefix' => 'text',
            'dpdt_rate_limit_attempts' => 'int',
            'dpdt_rate_limit_window' => 'int',
            'dpdt_admin_notification_email' => 'email',
            'dpdt_email_from_name' => 'text',
            'dpdt_email_from_address' => 'email',
        );

        foreach ($fields as $key => $type) {
            if (isset($_POST[$key])) {
                switch ($type) {
                    case 'email':
                        update_option($key, sanitize_email($_POST[$key]));
                        break;
                    case 'url':
                        update_option($key, esc_url_raw($_POST[$key]));
                        break;
                    case 'textarea':
                        update_option($key, sanitize_textarea_field($_POST[$key]));
                        break;
                    case 'int':
                        update_option($key, intval($_POST[$key]));
                        break;
                    default:
                        update_option($key, sanitize_text_field($_POST[$key]));
                }
            }
        }

        add_settings_error('dpdt_settings', 'settings_saved', __('সেটিংস সফলভাবে সংরক্ষিত হয়েছে।', 'dpdt-trademark'), 'success');
    }

    /**
     * Get all settings as array
     */
    public static function get_all() {
        return array(
            'site_name' => get_option('dpdt_site_name', ''),
            'site_name_en' => get_option('dpdt_site_name_en', ''),
            'site_description' => get_option('dpdt_site_description', ''),
            'site_phone' => get_option('dpdt_site_phone', ''),
            'site_email' => get_option('dpdt_site_email', ''),
            'site_address' => get_option('dpdt_site_address', ''),
            'site_established' => get_option('dpdt_site_established', '২০০৯'),
            'copyright_text' => get_option('dpdt_copyright_text', ''),
            'social_facebook' => get_option('dpdt_social_facebook', ''),
            'social_twitter' => get_option('dpdt_social_twitter', ''),
            'social_youtube' => get_option('dpdt_social_youtube', ''),
            'social_linkedin' => get_option('dpdt_social_linkedin', ''),
            'verify_base_url' => get_option('dpdt_verify_base_url', ''),
            'certificate_prefix' => get_option('dpdt_certificate_prefix', 'DPDT'),
        );
    }
}
