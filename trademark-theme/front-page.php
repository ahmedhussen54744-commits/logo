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

    <!-- Services Grid -->
    <?php get_template_part('template-parts/services-grid'); ?>

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
