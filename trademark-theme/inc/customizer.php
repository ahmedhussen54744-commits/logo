<?php
if (!defined('ABSPATH')) exit;

/**
 * Theme Customizer
 * Adds custom options to Appearance > Customize
 */
function dpdt_customize_register($wp_customize) {

    // ===== Colors Section =====
    $wp_customize->add_section('dpdt_colors', array(
        'title' => __('সাইট রং', 'dpdt-theme'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('dpdt_primary_color', array(
        'default' => '#1a5276',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dpdt_primary_color', array(
        'label' => __('প্রাইমারি রং', 'dpdt-theme'),
        'section' => 'dpdt_colors',
    )));

    $wp_customize->add_setting('dpdt_secondary_color', array(
        'default' => '#2980b9',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dpdt_secondary_color', array(
        'label' => __('সেকেন্ডারি রং', 'dpdt-theme'),
        'section' => 'dpdt_colors',
    )));

    $wp_customize->add_setting('dpdt_accent_color', array(
        'default' => '#27ae60',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dpdt_accent_color', array(
        'label' => __('অ্যাক্সেন্ট রং', 'dpdt-theme'),
        'section' => 'dpdt_colors',
    )));

    // ===== Hero Slider Section =====
    $wp_customize->add_section('dpdt_slider', array(
        'title' => __('হিরো স্লাইডার', 'dpdt-theme'),
        'priority' => 35,
    ));

    for ($i = 1; $i <= 5; $i++) {
        $wp_customize->add_setting("dpdt_slider_image_$i", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "dpdt_slider_image_$i", array(
            'label' => sprintf(__('স্লাইড %d ছবি', 'dpdt-theme'), $i),
            'section' => 'dpdt_slider',
        )));

        $wp_customize->add_setting("dpdt_slider_title_$i", array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("dpdt_slider_title_$i", array(
            'label' => sprintf(__('স্লাইড %d শিরোনাম', 'dpdt-theme'), $i),
            'section' => 'dpdt_slider',
            'type' => 'text',
        ));

        $wp_customize->add_setting("dpdt_slider_desc_$i", array(
            'default' => '',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("dpdt_slider_desc_$i", array(
            'label' => sprintf(__('স্লাইড %d বর্ণনা', 'dpdt-theme'), $i),
            'section' => 'dpdt_slider',
            'type' => 'textarea',
        ));

        $wp_customize->add_setting("dpdt_slider_link_$i", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("dpdt_slider_link_$i", array(
            'label' => sprintf(__('স্লাইড %d লিংক', 'dpdt-theme'), $i),
            'section' => 'dpdt_slider',
            'type' => 'url',
        ));
    }

    // ===== Officer Section =====
    $wp_customize->add_section('dpdt_officer', array(
        'title' => __('মহাপরিচালক তথ্য', 'dpdt-theme'),
        'priority' => 40,
    ));

    $wp_customize->add_setting('dpdt_officer_name', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('dpdt_officer_name', array(
        'label' => __('নাম', 'dpdt-theme'),
        'section' => 'dpdt_officer',
        'type' => 'text',
    ));

    $wp_customize->add_setting('dpdt_officer_designation', array(
        'default' => 'মহাপরিচালক',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('dpdt_officer_designation', array(
        'label' => __('পদবী', 'dpdt-theme'),
        'section' => 'dpdt_officer',
        'type' => 'text',
    ));

    $wp_customize->add_setting('dpdt_officer_photo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'dpdt_officer_photo', array(
        'label' => __('ছবি', 'dpdt-theme'),
        'section' => 'dpdt_officer',
    )));

    // ===== Typography =====
    $wp_customize->add_section('dpdt_typography', array(
        'title' => __('ফন্ট সেটিংস', 'dpdt-theme'),
        'priority' => 45,
    ));

    $wp_customize->add_setting('dpdt_body_font_size', array(
        'default' => '16',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('dpdt_body_font_size', array(
        'label' => __('বডি ফন্ট সাইজ (px)', 'dpdt-theme'),
        'section' => 'dpdt_typography',
        'type' => 'number',
        'input_attrs' => array('min' => 12, 'max' => 24, 'step' => 1),
    ));

    $wp_customize->add_setting('dpdt_heading_font', array(
        'default' => 'Noto Sans Bengali',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('dpdt_heading_font', array(
        'label' => __('হেডিং ফন্ট', 'dpdt-theme'),
        'section' => 'dpdt_typography',
        'type' => 'select',
        'choices' => array(
            'Noto Sans Bengali' => 'Noto Sans Bengali',
            'Hind Siliguri' => 'Hind Siliguri',
            'system' => 'System Default',
        ),
    ));
}
add_action('customize_register', 'dpdt_customize_register');

/**
 * Output custom CSS from customizer
 */
function dpdt_customizer_css() {
    $primary = get_theme_mod('dpdt_primary_color', '#1a5276');
    $secondary = get_theme_mod('dpdt_secondary_color', '#2980b9');
    $accent = get_theme_mod('dpdt_accent_color', '#27ae60');
    $font_size = get_theme_mod('dpdt_body_font_size', '16');
    $heading_font = get_theme_mod('dpdt_heading_font', 'Noto Sans Bengali');

    echo '<style id="dpdt-customizer-css">';
    echo ':root {';
    echo '--dpdt-primary: ' . esc_attr($primary) . ';';
    echo '--dpdt-secondary: ' . esc_attr($secondary) . ';';
    echo '--dpdt-accent: ' . esc_attr($accent) . ';';
    echo '--dpdt-font-size: ' . intval($font_size) . 'px;';
    echo '--dpdt-heading-font: "' . esc_attr($heading_font) . '", sans-serif;';
    echo '}';
    echo '</style>';
}
add_action('wp_head', 'dpdt_customizer_css', 100);
