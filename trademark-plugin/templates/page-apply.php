<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: Apply Page
 * This template can be assigned to a page via Page Attributes
 */
get_header();
?>
<div class="dpdt-page-wrapper dpdt-apply-page">
    <div class="dpdt-container">
        <div class="dpdt-page-header">
            <h1><?php esc_html_e('ট্রেডমার্ক সার্টিফিকেট আবেদন', 'dpdt-trademark'); ?></h1>
            <p><?php esc_html_e('নিচের ফর্মটি পূরণ করে আপনার ট্রেডমার্ক সার্টিফিকেটের জন্য আবেদন করুন।', 'dpdt-trademark'); ?></p>
        </div>
        <div class="dpdt-page-content">
            <?php echo do_shortcode('[dpdt_apply_form]'); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
