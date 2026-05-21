<?php
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('বিষয়বস্তুতে যান', 'dpdt-theme'); ?></a>

<header id="masthead" class="site-header">
    <!-- Top Bar -->
    <div class="header-top-bar">
        <div class="container">
            <div class="top-bar-left">
                <span class="established-text">
                    <?php echo esc_html(get_option('dpdt_site_established', '২০০৯') ? 'প্রতিষ্ঠিত: ' . get_option('dpdt_site_established', '২০০৯') : ''); ?>
                </span>
                <span class="top-bar-divider">|</span>
                <span class="gov-text"><?php esc_html_e('গণপ্রজাতন্ত্রী বাংলাদেশ সরকার', 'dpdt-theme'); ?></span>
            </div>
            <div class="top-bar-right">
                <?php
                $phone = get_option('dpdt_site_phone', '');
                $email = get_option('dpdt_site_email', '');
                if ($phone) : ?>
                    <a href="tel:<?php echo esc_attr($phone); ?>" class="top-bar-link">
                        <span class="dashicons dashicons-phone"></span> <?php echo esc_html($phone); ?>
                    </a>
                <?php endif;
                if ($email) : ?>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="top-bar-link">
                        <span class="dashicons dashicons-email"></span> <?php echo esc_html($email); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <div class="header-branding">
                <div class="site-logo">
                    <?php
                    if (function_exists('dpdt_trademark') && class_exists('DPDT_Logo_Manager')) {
                        $logo_manager = new DPDT_Logo_Manager();
                        echo $logo_manager->get_header_logo_html();
                    } elseif (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        echo '<span class="site-title-text">' . esc_html(get_bloginfo('name')) . '</span>';
                    }
                    ?>
                </div>
                <div class="site-identity">
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <?php echo esc_html(get_option('dpdt_site_name', get_bloginfo('name'))); ?>
                        </a>
                    </h1>
                    <p class="site-description"><?php echo esc_html(get_option('dpdt_site_description', get_bloginfo('description'))); ?></p>
                    <p class="site-name-en"><?php echo esc_html(get_option('dpdt_site_name_en', '')); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('প্রধান মেনু', 'dpdt-theme'); ?>">
        <div class="container">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e('মেনু', 'dpdt-theme'); ?>">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>

            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id' => 'primary-menu',
                'menu_class' => 'primary-menu-list',
                'container' => 'div',
                'container_class' => 'menu-container',
                'depth' => 3,
                'fallback_cb' => 'dpdt_fallback_menu',
                'walker' => new DPDT_Walker_Nav_Menu(),
            ));
            ?>
        </div>
    </nav>
</header>

<div id="content" class="site-content">
