<?php
if (!defined('ABSPATH')) exit;

/**
 * DPDT Trademark Theme Functions
 * Version: 4.1.0
 */

define('DPDT_THEME_VERSION', '4.1.0');
define('DPDT_THEME_DIR', get_template_directory());
define('DPDT_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function dpdt_theme_setup() {
    // Translation support
    load_theme_textdomain('dpdt-theme', DPDT_THEME_DIR . '/languages');

    // Theme supports
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height' => 80,
        'width' => 300,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script',
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('custom-background', array(
        'default-color' => 'f5f5f5',
    ));

    // Image sizes
    add_image_size('dpdt-hero', 1920, 600, true);
    add_image_size('dpdt-card', 400, 300, true);
    add_image_size('dpdt-thumbnail', 150, 150, true);

    // Register Navigation Menus
    register_nav_menus(array(
        'primary' => __('প্রাইমারি মেনু (হেডার)', 'dpdt-theme'),
        'footer' => __('ফুটার মেনু', 'dpdt-theme'),
        'mobile' => __('মোবাইল মেনু', 'dpdt-theme'),
    ));
}
add_action('after_setup_theme', 'dpdt_theme_setup');

/**
 * Enqueue Styles and Scripts
 */
function dpdt_theme_scripts() {
    // Google Fonts - Noto Sans Bengali + Hind Siliguri
    wp_enqueue_style('dpdt-google-fonts', 'https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Noto+Sans+Bengali:wght@300;400;500;600;700;800&display=swap', array(), null);

    // Theme styles
    wp_enqueue_style('dpdt-main', DPDT_THEME_URI . '/assets/css/main.css', array(), DPDT_THEME_VERSION);
    wp_enqueue_style('dpdt-components', DPDT_THEME_URI . '/assets/css/components.css', array('dpdt-main'), DPDT_THEME_VERSION);
    wp_enqueue_style('dpdt-slider', DPDT_THEME_URI . '/assets/css/slider.css', array('dpdt-main'), DPDT_THEME_VERSION);
    wp_enqueue_style('dpdt-responsive', DPDT_THEME_URI . '/assets/css/responsive.css', array('dpdt-main'), DPDT_THEME_VERSION);
    wp_enqueue_style('dpdt-theme-style', get_stylesheet_uri(), array('dpdt-main'), DPDT_THEME_VERSION);

    // Dashicons on frontend
    wp_enqueue_style('dashicons');

    // Theme scripts
    wp_enqueue_script('dpdt-navigation', DPDT_THEME_URI . '/assets/js/navigation.js', array(), DPDT_THEME_VERSION, true);
    wp_enqueue_script('dpdt-slider', DPDT_THEME_URI . '/assets/js/slider.js', array(), DPDT_THEME_VERSION, true);
    wp_enqueue_script('dpdt-main', DPDT_THEME_URI . '/assets/js/main.js', array('jquery', 'dpdt-navigation', 'dpdt-slider'), DPDT_THEME_VERSION, true);

    // Localize
    wp_localize_script('dpdt-main', 'dpdtTheme', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'themeUrl' => DPDT_THEME_URI,
        'homeUrl' => home_url('/'),
        'isRTL' => is_rtl(),
    ));

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'dpdt_theme_scripts');

/**
 * Register Widget Areas
 */
function dpdt_theme_widgets_init() {
    register_sidebar(array(
        'name' => __('সাইডবার', 'dpdt-theme'),
        'id' => 'sidebar-1',
        'description' => __('প্রধান সাইডবার', 'dpdt-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('ফুটার ১', 'dpdt-theme'),
        'id' => 'footer-1',
        'description' => __('ফুটার প্রথম কলাম', 'dpdt-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('ফুটার ২', 'dpdt-theme'),
        'id' => 'footer-2',
        'description' => __('ফুটার দ্বিতীয় কলাম', 'dpdt-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('ফুটার ৩', 'dpdt-theme'),
        'id' => 'footer-3',
        'description' => __('ফুটার তৃতীয় কলাম', 'dpdt-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => __('ফুটার ৪', 'dpdt-theme'),
        'id' => 'footer-4',
        'description' => __('ফুটার চতুর্থ কলাম', 'dpdt-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="widget-title">',
        'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'dpdt_theme_widgets_init');

/**
 * Include additional files
 */
require_once DPDT_THEME_DIR . '/inc/customizer.php';
require_once DPDT_THEME_DIR . '/inc/template-tags.php';
require_once DPDT_THEME_DIR . '/inc/walker-nav-menu.php';

/**
 * Custom excerpt length
 */
function dpdt_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'dpdt_excerpt_length');

/**
 * Custom excerpt more text
 */
function dpdt_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'dpdt_excerpt_more');

/**
 * Add body classes
 */
function dpdt_body_classes($classes) {
    if (is_front_page()) {
        $classes[] = 'dpdt-front-page';
    }
    if (is_page_template('page-templates/full-width.php')) {
        $classes[] = 'dpdt-full-width';
    }
    if (is_page_template('page-templates/with-sidebar.php')) {
        $classes[] = 'dpdt-with-sidebar';
    }
    $classes[] = 'dpdt-theme';
    return $classes;
}
add_filter('body_class', 'dpdt_body_classes');

/**
 * Disable emojis for performance
 */
function dpdt_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
}
add_action('init', 'dpdt_disable_emojis');

/**
 * Add preconnect for Google Fonts
 */
function dpdt_resource_hints($urls, $relation_type) {
    if ($relation_type === 'preconnect') {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin' => true,
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin' => true,
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'dpdt_resource_hints', 10, 2);

/**
 * Custom page templates
 */
function dpdt_page_templates($templates) {
    $templates['page-templates/full-width.php'] = __('Full Width (No Sidebar)', 'dpdt-theme');
    $templates['page-templates/with-sidebar.php'] = __('With Sidebar', 'dpdt-theme');
    return $templates;
}
add_filter('theme_page_templates', 'dpdt_page_templates');
