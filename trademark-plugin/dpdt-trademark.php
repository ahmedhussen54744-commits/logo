<?php
/**
 * Plugin Name: DPDT Trademark Certificate System
 * Plugin URI: https://dpdt.gov.bd
 * Description: Advanced Trademark Certificate Management - Apply, Approve, Verify with QR Code. Secure POST-based system.
 * Version: 3.0.0
 * Author: DPDT Development Team
 * Author URI: https://dpdt.gov.bd
 * License: GPL v2 or later
 * Text Domain: dpdt-trademark
 * Since: 2009
 */

if (!defined('ABSPATH')) exit;

define('DPDT_VERSION', '3.0.0');
define('DPDT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DPDT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include core files
require_once DPDT_PLUGIN_DIR . 'includes/class-database.php';
require_once DPDT_PLUGIN_DIR . 'includes/class-security.php';
require_once DPDT_PLUGIN_DIR . 'includes/class-application.php';
require_once DPDT_PLUGIN_DIR . 'includes/class-admin.php';
require_once DPDT_PLUGIN_DIR . 'includes/class-verify.php';
require_once DPDT_PLUGIN_DIR . 'includes/class-qrcode.php';

// Activation Hook
register_activation_hook(__FILE__, array('DPDT_Database', 'create_tables'));

// Init Plugin
add_action('init', 'dpdt_init_plugin');
function dpdt_init_plugin() {
    // Register POST-based routes (20 char slugs)
    add_rewrite_rule('^apply/?$', 'index.php?dpdt_page=apply', 'top');
    add_rewrite_rule('^verify/([a-zA-Z0-9]+)/?$', 'index.php?dpdt_page=verify&dpdt_code=$matches[1]', 'top');
    add_rewrite_rule('^verify/?$', 'index.php?dpdt_page=verify', 'top');
    add_rewrite_tag('%dpdt_page%', '([^&]+)');
    add_rewrite_tag('%dpdt_code%', '([^&]+)');
}

// Flush rewrite on activation
register_activation_hook(__FILE__, function() {
    dpdt_init_plugin();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, 'flush_rewrite_rules');

// Template redirect
add_action('template_redirect', 'dpdt_template_handler');
function dpdt_template_handler() {
    $page = get_query_var('dpdt_page');
    if (!$page) return;
    
    switch ($page) {
        case 'apply':
            include DPDT_PLUGIN_DIR . 'templates/page-apply.php';
            exit;
        case 'verify':
            include DPDT_PLUGIN_DIR . 'templates/page-verify.php';
            exit;
    }
}

// Handle form submission via POST
add_action('admin_post_nopriv_dpdt_submit_application', 'dpdt_handle_application');
add_action('admin_post_dpdt_submit_application', 'dpdt_handle_application');
function dpdt_handle_application() {
    DPDT_Application::handle_submission();
}

// Admin AJAX handlers
add_action('wp_ajax_dpdt_approve_app', array('DPDT_Admin', 'approve_application'));
add_action('wp_ajax_dpdt_reject_app', array('DPDT_Admin', 'reject_application'));
add_action('wp_ajax_dpdt_update_app', array('DPDT_Admin', 'update_application'));
add_action('wp_ajax_dpdt_upload_cert', array('DPDT_Admin', 'upload_certificate'));

// Enqueue plugin assets
add_action('wp_enqueue_scripts', 'dpdt_plugin_assets');
function dpdt_plugin_assets() {
    wp_enqueue_style('dpdt-plugin', DPDT_PLUGIN_URL . 'assets/css/plugin.css', array(), DPDT_VERSION);
    wp_enqueue_script('dpdt-plugin', DPDT_PLUGIN_URL . 'assets/js/plugin.js', array(), DPDT_VERSION, true);
}

// Admin menu
add_action('admin_menu', 'dpdt_admin_menu');
function dpdt_admin_menu() {
    add_menu_page(
        'Trademark Applications',
        'Trademark Cert',
        'manage_options',
        'dpdt-trademark',
        array('DPDT_Admin', 'render_dashboard'),
        'dashicons-awards',
        30
    );
    add_submenu_page('dpdt-trademark', 'All Applications', 'All Applications', 'manage_options', 'dpdt-trademark', array('DPDT_Admin', 'render_dashboard'));
    add_submenu_page('dpdt-trademark', 'Pending', 'Pending', 'manage_options', 'dpdt-pending', array('DPDT_Admin', 'render_pending'));
    add_submenu_page('dpdt-trademark', 'Approved', 'Approved', 'manage_options', 'dpdt-approved', array('DPDT_Admin', 'render_approved'));
    add_submenu_page('dpdt-trademark', 'Settings', 'Settings', 'manage_options', 'dpdt-settings', array('DPDT_Admin', 'render_settings'));
}

// Admin assets
add_action('admin_enqueue_scripts', 'dpdt_admin_assets');
function dpdt_admin_assets($hook) {
    if (strpos($hook, 'dpdt') === false) return;
    wp_enqueue_style('dpdt-admin', DPDT_PLUGIN_URL . 'assets/css/admin.css', array(), DPDT_VERSION);
    wp_enqueue_script('dpdt-admin', DPDT_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), DPDT_VERSION, true);
    wp_enqueue_media();
    wp_localize_script('dpdt-admin', 'dpdtAdmin', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dpdt_admin_nonce'),
    ));
}
