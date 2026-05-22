<?php
/**
 * Plugin Name: DPDT Trademark Certificate System
 * Plugin URI: https://dpdt.gov.bd
 * Description: Complete Trademark Certificate Management System for Bangladesh Department of Patents, Designs and Trademarks (DPDT). Features: application management, certificate generation, QR verification, logo management, category pages, and full admin control.
 * Version: 4.1.0
 * Author: DPDT Development Team
 * Author URI: https://dpdt.gov.bd
 * Text Domain: dpdt-trademark
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

// Plugin Constants
define('DPDT_VERSION', '4.1.0');
define('DPDT_PLUGIN_FILE', __FILE__);
define('DPDT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DPDT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DPDT_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('DPDT_DB_VERSION', '4.1.0');
define('DPDT_MIN_PHP', '7.4');
define('DPDT_MIN_WP', '5.8');
define('DPDT_TEXT_DOMAIN', 'dpdt-trademark');
define('DPDT_PREFIX', 'dpdt_');
define('DPDT_NONCE_ACTION', 'dpdt_security_nonce');

/**
 * Main Plugin Class
 */
final class DPDT_Trademark_Plugin {

    private static $instance = null;
    private $admin;
    private $application;
    private $database;
    private $security;
    private $verify;
    private $qrcode;
    private $logo_manager;
    private $category_manager;
    private $menu_manager;
    private $certificate_generator;
    private $dashboard;
    private $settings;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->check_requirements();
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function check_requirements() {
        if (version_compare(PHP_VERSION, DPDT_MIN_PHP, '<')) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>';
                printf(
                    esc_html__('DPDT Trademark requires PHP %s or higher. You are running PHP %s.', 'dpdt-trademark'),
                    DPDT_MIN_PHP,
                    PHP_VERSION
                );
                echo '</p></div>';
            });
            return;
        }
    }

    private function load_dependencies() {
        // Core includes
        require_once DPDT_PLUGIN_DIR . 'includes/class-database.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-security.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-application.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-admin.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-verify.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-qrcode.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-logo-manager.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-category-manager.php';
        require_once DPDT_PLUGIN_DIR . 'includes/class-menu-manager.php';

        // Certificate system
        require_once DPDT_PLUGIN_DIR . 'certificates/class-certificate-generator.php';
        require_once DPDT_PLUGIN_DIR . 'certificates/class-certificate-template.php';

        // Admin
        require_once DPDT_PLUGIN_DIR . 'admin/class-dashboard.php';
        require_once DPDT_PLUGIN_DIR . 'admin/class-settings.php';
        require_once DPDT_PLUGIN_DIR . 'admin/class-logo-settings.php';

        // Initialize classes
        $this->database = new DPDT_Database();
        $this->security = new DPDT_Security();
        $this->application = new DPDT_Application();
        $this->admin = new DPDT_Admin();
        $this->verify = new DPDT_Verify();
        $this->qrcode = new DPDT_QRCode();
        $this->logo_manager = new DPDT_Logo_Manager();
        $this->category_manager = new DPDT_Category_Manager();
        $this->menu_manager = new DPDT_Menu_Manager();
        $this->certificate_generator = new DPDT_Certificate_Generator();
        $this->dashboard = new DPDT_Dashboard();
        $this->settings = new DPDT_Settings();
    }

    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Init
        add_action('init', array($this, 'init'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_shortcodes'));

        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // AJAX handlers
        add_action('wp_ajax_dpdt_submit_application', array($this->application, 'handle_submission'));
        add_action('wp_ajax_nopriv_dpdt_submit_application', array($this->application, 'handle_submission'));
        add_action('wp_ajax_dpdt_verify_certificate', array($this->verify, 'ajax_verify'));
        add_action('wp_ajax_nopriv_dpdt_verify_certificate', array($this->verify, 'ajax_verify'));
        add_action('wp_ajax_dpdt_approve_application', array($this->admin, 'approve_application'));
        add_action('wp_ajax_dpdt_reject_application', array($this->admin, 'reject_application'));
        add_action('wp_ajax_dpdt_upload_logo', array($this->logo_manager, 'handle_upload'));
        add_action('wp_ajax_dpdt_delete_logo', array($this->logo_manager, 'handle_delete'));

        // Template redirect
        add_action('template_redirect', array($this, 'template_redirect'));

        // Load translations
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Admin menu
        add_action('admin_menu', array($this, 'register_admin_menu'));

        // Custom columns
        add_filter('manage_dpdt_notice_posts_columns', array($this, 'notice_columns'));
        add_action('manage_dpdt_notice_posts_custom_column', array($this, 'notice_column_content'), 10, 2);
    }

    public function activate() {
        // Create database tables
        $this->database->create_tables();

        // Create category pages
        $this->category_manager->create_default_pages();

        // Create and assign menu
        $this->menu_manager->create_default_menu();

        // Set default options
        $this->set_default_options();

        // Create upload directories
        $this->create_upload_dirs();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set activation flag
        update_option('dpdt_plugin_activated', true);
        update_option('dpdt_plugin_version', DPDT_VERSION);
    }

    public function deactivate() {
        flush_rewrite_rules();
        update_option('dpdt_plugin_activated', false);
    }

    private function set_default_options() {
        $defaults = array(
            'dpdt_site_name' => 'পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর',
            'dpdt_site_name_en' => 'Department of Patents, Designs and Trademarks',
            'dpdt_site_description' => 'শিল্প মন্ত্রণালয়, গণপ্রজাতন্ত্রী বাংলাদেশ সরকার',
            'dpdt_site_phone' => '+880-2-9573398',
            'dpdt_site_email' => 'info@dpdt.gov.bd',
            'dpdt_site_address' => '৯১, মতিঝিল বা/এ, ঢাকা-১০০০',
            'dpdt_site_established' => '২০০৯',
            'dpdt_copyright_text' => '© ২০০৯-২০২৪ পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর। সর্বস্বত্ব সংরক্ষিত।',
            'dpdt_social_facebook' => '',
            'dpdt_social_twitter' => '',
            'dpdt_social_youtube' => '',
            'dpdt_verify_base_url' => home_url('/verify/'),
            'dpdt_certificate_prefix' => 'DPDT',
            'dpdt_rate_limit_attempts' => 5,
            'dpdt_rate_limit_window' => 300,
            'dpdt_encryption_key' => wp_generate_password(32, true, true),
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option($key)) {
                update_option($key, $value);
            }
        }
    }

    private function create_upload_dirs() {
        $upload_dir = wp_upload_dir();
        $dirs = array(
            $upload_dir['basedir'] . '/dpdt-certificates',
            $upload_dir['basedir'] . '/dpdt-certificates/pdf',
            $upload_dir['basedir'] . '/dpdt-certificates/jpg',
            $upload_dir['basedir'] . '/dpdt-certificates/qr',
            $upload_dir['basedir'] . '/dpdt-logos',
            $upload_dir['basedir'] . '/dpdt-logos/brands',
            $upload_dir['basedir'] . '/dpdt-logos/categories',
        );

        foreach ($dirs as $dir) {
            if (!file_exists($dir)) {
                wp_mkdir_p($dir);
                // Add .htaccess for security
                file_put_contents($dir . '/.htaccess', "Options -Indexes\n");
            }
        }
    }

    public function init() {
        // Session handling for rate limiting
        if (!session_id() && !headers_sent()) {
            session_start();
        }
    }

    public function register_post_types() {
        // Notice Post Type
        register_post_type('dpdt_notice', array(
            'labels' => array(
                'name' => __('নোটিশ', 'dpdt-trademark'),
                'singular_name' => __('নোটিশ', 'dpdt-trademark'),
                'add_new' => __('নতুন নোটিশ', 'dpdt-trademark'),
                'add_new_item' => __('নতুন নোটিশ যোগ করুন', 'dpdt-trademark'),
                'edit_item' => __('নোটিশ সম্পাদনা', 'dpdt-trademark'),
                'view_item' => __('নোটিশ দেখুন', 'dpdt-trademark'),
                'all_items' => __('সকল নোটিশ', 'dpdt-trademark'),
                'search_items' => __('নোটিশ অনুসন্ধান', 'dpdt-trademark'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-megaphone',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'rewrite' => array('slug' => 'notices'),
            'show_in_rest' => true,
            'menu_position' => 25,
        ));

        // Activity Post Type
        register_post_type('dpdt_activity', array(
            'labels' => array(
                'name' => __('কার্যক্রম', 'dpdt-trademark'),
                'singular_name' => __('কার্যক্রম', 'dpdt-trademark'),
                'add_new' => __('নতুন কার্যক্রম', 'dpdt-trademark'),
                'add_new_item' => __('নতুন কার্যক্রম যোগ করুন', 'dpdt-trademark'),
                'edit_item' => __('কার্যক্রম সম্পাদনা', 'dpdt-trademark'),
                'view_item' => __('কার্যক্রম দেখুন', 'dpdt-trademark'),
                'all_items' => __('সকল কার্যক্রম', 'dpdt-trademark'),
            ),
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'rewrite' => array('slug' => 'activities'),
            'show_in_rest' => true,
            'menu_position' => 26,
        ));

        // Notice Category Taxonomy
        register_taxonomy('dpdt_notice_cat', 'dpdt_notice', array(
            'labels' => array(
                'name' => __('নোটিশ বিভাগ', 'dpdt-trademark'),
                'singular_name' => __('বিভাগ', 'dpdt-trademark'),
            ),
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'notice-category'),
        ));
    }

    public function register_shortcodes() {
        add_shortcode('dpdt_apply_form', array($this->application, 'render_form'));
        add_shortcode('dpdt_verify', array($this->verify, 'render_verify_page'));
        add_shortcode('dpdt_services', array($this, 'render_services'));
        add_shortcode('dpdt_statistics', array($this, 'render_statistics'));
    }

    public function render_services($atts) {
        ob_start();
        include DPDT_PLUGIN_DIR . 'templates/shortcodes/shortcode-services.php';
        return ob_get_clean();
    }

    public function render_statistics($atts) {
        ob_start();
        include DPDT_PLUGIN_DIR . 'templates/shortcodes/shortcode-statistics.php';
        return ob_get_clean();
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('dpdt-plugin', DPDT_PLUGIN_URL . 'assets/css/plugin.css', array(), DPDT_VERSION);
        wp_enqueue_style('dpdt-verify', DPDT_PLUGIN_URL . 'assets/css/verify.css', array(), DPDT_VERSION);
        wp_enqueue_style('dpdt-apply-form', DPDT_PLUGIN_URL . 'assets/css/apply-form.css', array(), DPDT_VERSION);

        wp_enqueue_script('dpdt-plugin', DPDT_PLUGIN_URL . 'assets/js/plugin.js', array('jquery'), DPDT_VERSION, true);
        wp_enqueue_script('dpdt-verify', DPDT_PLUGIN_URL . 'assets/js/verify.js', array('jquery'), DPDT_VERSION, true);
        wp_enqueue_script('dpdt-apply-form', DPDT_PLUGIN_URL . 'assets/js/apply-form.js', array('jquery'), DPDT_VERSION, true);

        $localize_data = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(DPDT_NONCE_ACTION),
            'verifyUrl' => get_option('dpdt_verify_base_url', home_url('/verify/')),
            'strings' => array(
                'submitting' => __('জমা হচ্ছে...', 'dpdt-trademark'),
                'success' => __('সফলভাবে জমা হয়েছে!', 'dpdt-trademark'),
                'error' => __('একটি ত্রুটি হয়েছে।', 'dpdt-trademark'),
                'verifying' => __('যাচাই হচ্ছে...', 'dpdt-trademark'),
                'verified' => __('সার্টিফিকেট যাচাই সম্পন্ন', 'dpdt-trademark'),
                'invalid' => __('অবৈধ সার্টিফিকেট', 'dpdt-trademark'),
            ),
        );

        wp_localize_script('dpdt-plugin', 'dpdtAjax', $localize_data);
        wp_localize_script('dpdt-apply-form', 'dpdtAjax', $localize_data);
        wp_localize_script('dpdt-verify', 'dpdtAjax', $localize_data);
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'dpdt') === false && strpos($hook, 'trademark') === false) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style('dpdt-admin', DPDT_PLUGIN_URL . 'assets/css/admin.css', array(), DPDT_VERSION);
        wp_enqueue_script('dpdt-admin', DPDT_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'wp-color-picker'), DPDT_VERSION, true);
        wp_enqueue_style('wp-color-picker');

        wp_localize_script('dpdt-admin', 'dpdtAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(DPDT_NONCE_ACTION),
            'uploadTitle' => __('ফাইল নির্বাচন করুন', 'dpdt-trademark'),
            'uploadButton' => __('নির্বাচন করুন', 'dpdt-trademark'),
        ));
    }

    public function register_admin_menu() {
        // Main menu
        add_menu_page(
            __('ট্রেডমার্ক সার্টিফিকেট', 'dpdt-trademark'),
            __('Trademark Cert', 'dpdt-trademark'),
            'manage_options',
            'dpdt-dashboard',
            array($this->dashboard, 'render'),
            'dashicons-awards',
            3
        );

        // Sub menus
        add_submenu_page('dpdt-dashboard', __('ড্যাশবোর্ড', 'dpdt-trademark'), __('ড্যাশবোর্ড', 'dpdt-trademark'), 'manage_options', 'dpdt-dashboard', array($this->dashboard, 'render'));
        add_submenu_page('dpdt-dashboard', __('আবেদন সমূহ', 'dpdt-trademark'), __('আবেদন সমূহ', 'dpdt-trademark'), 'manage_options', 'dpdt-applications', array($this->admin, 'render_applications'));
        add_submenu_page('dpdt-dashboard', __('সেটিংস', 'dpdt-trademark'), __('সেটিংস', 'dpdt-trademark'), 'manage_options', 'dpdt-settings', array($this->settings, 'render'));
        add_submenu_page('dpdt-dashboard', __('লোগো সেটিংস', 'dpdt-trademark'), __('লোগো সেটিংস', 'dpdt-trademark'), 'manage_options', 'dpdt-logo-settings', array(new DPDT_Logo_Settings(), 'render'));
        add_submenu_page('dpdt-dashboard', __('ক্যাটাগরি সেটিংস', 'dpdt-trademark'), __('ক্যাটাগরি/পেজ', 'dpdt-trademark'), 'manage_options', 'dpdt-categories', array($this->category_manager, 'render_admin_page'));
    }

    public function template_redirect() {
        // Handle verify page requests
        if (isset($_GET['dpdt_verify']) && !empty($_GET['dpdt_verify'])) {
            $this->verify->handle_verify_request(sanitize_text_field($_GET['dpdt_verify']));
        }
    }

    public function notice_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['title'] = $columns['title'];
        $new_columns['dpdt_notice_cat'] = __('বিভাগ', 'dpdt-trademark');
        $new_columns['date'] = $columns['date'];
        return $new_columns;
    }

    public function notice_column_content($column, $post_id) {
        if ($column === 'dpdt_notice_cat') {
            $terms = get_the_terms($post_id, 'dpdt_notice_cat');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html(implode(', ', wp_list_pluck($terms, 'name')));
            }
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain('dpdt-trademark', false, dirname(DPDT_PLUGIN_BASENAME) . '/languages');
    }
}

// Initialize plugin
function dpdt_trademark() {
    return DPDT_Trademark_Plugin::get_instance();
}

// Boot the plugin
add_action('plugins_loaded', 'dpdt_trademark');
