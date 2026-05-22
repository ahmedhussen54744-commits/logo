<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: Front Page (Homepage)
 * Displays hero slider, services, notices, statistics, important links
 */
get_header();
?>
<main id="primary" class="site-main front-page-main">

    <!-- Hero Slider -->
    <?php get_template_part('template-parts/hero-slider'); ?>

    <!-- Services Grid - dpdt.gov.bd style -->
    <section class="home-services-section">
        <div class="container">
            <div class="section-header">
                <h2><?php esc_html_e('আমাদের সেবাসমূহ', 'dpdt-theme'); ?></h2>
                <p><?php esc_html_e('পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর কর্তৃক প্রদত্ত সেবাসমূহ', 'dpdt-theme'); ?></p>
            </div>
            <div class="services-grid">

                <!-- ট্রেডমার্ক অনুসন্ধান -->
                <div class="service-card">
                    <a href="<?php echo esc_url(home_url('/verify/')); ?>">
                        <div class="service-icon">
                            <span class="dashicons dashicons-search"></span>
                        </div>
                        <h3 class="service-title"><?php esc_html_e('ট্রেডমার্ক অনুসন্ধান', 'dpdt-theme'); ?></h3>
                        <p class="service-desc"><?php esc_html_e('আপনার ট্রেডমার্ক নিবন্ধন যাচাই ও অনুসন্ধান করুন', 'dpdt-theme'); ?></p>
                        <span class="service-link"><?php esc_html_e('বিস্তারিত দেখুন →', 'dpdt-theme'); ?></span>
                    </a>
                </div>

                <!-- অনলাইন আবেদন -->
                <div class="service-card">
                    <a href="<?php echo esc_url(home_url('/apply/')); ?>">
                        <div class="service-icon">
                            <span class="dashicons dashicons-edit-page"></span>
                        </div>
                        <h3 class="service-title"><?php esc_html_e('অনলাইন আবেদন', 'dpdt-theme'); ?></h3>
                        <p class="service-desc"><?php esc_html_e('ট্রেডমার্ক নিবন্ধনের জন্য অনলাইনে আবেদন করুন', 'dpdt-theme'); ?></p>
                        <span class="service-link"><?php esc_html_e('বিস্তারিত দেখুন →', 'dpdt-theme'); ?></span>
                    </a>
                </div>

                <!-- সার্টিফিকেট যাচাই -->
                <div class="service-card">
                    <a href="<?php echo esc_url(home_url('/verify/')); ?>">
                        <div class="service-icon">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </div>
                        <h3 class="service-title"><?php esc_html_e('সার্টিফিকেট যাচাই', 'dpdt-theme'); ?></h3>
                        <p class="service-desc"><?php esc_html_e('ট্রেডমার্ক সার্টিফিকেট QR কোড দিয়ে যাচাই করুন', 'dpdt-theme'); ?></p>
                        <span class="service-link"><?php esc_html_e('বিস্তারিত দেখুন →', 'dpdt-theme'); ?></span>
                    </a>
                </div>

                <!-- ট্রেডমার্ক ক্যাটাগরি -->
                <div class="service-card">
                    <a href="<?php echo esc_url(home_url('/trademark-categories/')); ?>">
                        <div class="service-icon">
                            <span class="dashicons dashicons-category"></span>
                        </div>
                        <h3 class="service-title"><?php esc_html_e('ট্রেডমার্ক ক্যাটাগরি', 'dpdt-theme'); ?></h3>
                        <p class="service-desc"><?php esc_html_e('ট্রেডমার্ক শ্রেণিবিভাগ ও ক্যাটাগরি তালিকা দেখুন', 'dpdt-theme'); ?></p>
                        <span class="service-link"><?php esc_html_e('বিস্তারিত দেখুন →', 'dpdt-theme'); ?></span>
                    </a>
                </div>

                <!-- নোটিশ বোর্ড -->
                <div class="service-card">
                    <a href="<?php echo esc_url(get_post_type_archive_link('dpdt_notice') ?: home_url('/notices/')); ?>">
                        <div class="service-icon">
                            <span class="dashicons dashicons-megaphone"></span>
                        </div>
                        <h3 class="service-title"><?php esc_html_e('নোটিশ বোর্ড', 'dpdt-theme'); ?></h3>
                        <p class="service-desc"><?php esc_html_e('সকল নোটিশ, বিজ্ঞপ্তি ও আপডেট দেখুন', 'dpdt-theme'); ?></p>
                        <span class="service-link"><?php esc_html_e('বিস্তারিত দেখুন →', 'dpdt-theme'); ?></span>
                    </a>
                </div>

                <!-- ডাউনলোড ফর্ম -->
                <div class="service-card">
                    <a href="<?php echo esc_url(home_url('/downloads/')); ?>">
                        <div class="service-icon">
                            <span class="dashicons dashicons-download"></span>
                        </div>
                        <h3 class="service-title"><?php esc_html_e('ডাউনলোড ফর্ম', 'dpdt-theme'); ?></h3>
                        <p class="service-desc"><?php esc_html_e('প্রয়োজনীয় ফর্ম ও ডকুমেন্ট ডাউনলোড করুন', 'dpdt-theme'); ?></p>
                        <span class="service-link"><?php esc_html_e('বিস্তারিত দেখুন →', 'dpdt-theme'); ?></span>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Notice Board + Officer Section -->
    <div class="container">
        <div class="home-two-columns">
            <div class="home-column-main">
                <?php get_template_part('template-parts/notice-board'); ?>
            </div>
            <div class="home-column-side">
                <?php get_template_part('template-parts/officer-section'); ?>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <?php get_template_part('template-parts/statistics'); ?>

    <!-- Important Links -->
    <?php get_template_part('template-parts/important-links'); ?>

</main>
<?php
get_footer();
