<?php
/**
 * DPDT Trademark Theme Functions
 * Since: 2009
 * Security: Advanced Protection Enabled
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme Setup
function dpdt_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'dpdt-trademark'),
        'footer' => __('Footer Menu', 'dpdt-trademark'),
    ));
}
add_action('after_setup_theme', 'dpdt_theme_setup');

// Enqueue Styles & Scripts
function dpdt_enqueue_assets() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap', array(), null);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', array(), '6.5.0');
    wp_enqueue_style('dpdt-main', get_template_directory_uri() . '/assets/css/main.css', array(), '2.0.0');
    wp_enqueue_style('dpdt-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), '2.0.0');
    
    wp_enqueue_script('dpdt-main', get_template_directory_uri() . '/assets/js/main.js', array(), '2.0.0', true);
    wp_localize_script('dpdt-main', 'dpdtAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('dpdt_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'dpdt_enqueue_assets');

// Security Headers
function dpdt_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
        header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.gstatic.com; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; img-src 'self' data: https:;");
    }
}
add_action('send_headers', 'dpdt_security_headers');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Remove WordPress version
remove_action('wp_head', 'wp_generator');

// Copyright Protection - Disable right-click for non-admins
function dpdt_copyright_protection() {
    if (!is_admin() && !current_user_can('manage_options')) {
        echo '<script>
        document.addEventListener("contextmenu", function(e) { e.preventDefault(); });
        document.addEventListener("keydown", function(e) {
            if (e.ctrlKey && (e.key === "u" || e.key === "s" || e.key === "c")) { e.preventDefault(); }
            if (e.key === "F12") { e.preventDefault(); }
        });
        </script>';
    }
}
add_action('wp_footer', 'dpdt_copyright_protection');
