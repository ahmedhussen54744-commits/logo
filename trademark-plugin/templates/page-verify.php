<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: Verify Page
 */
get_header();
?>
<div class="dpdt-page-wrapper dpdt-verify-page">
    <div class="dpdt-container">
        <div class="dpdt-page-header">
            <h1><?php esc_html_e('সার্টিফিকেট যাচাই', 'dpdt-trademark'); ?></h1>
            <p><?php esc_html_e('সার্টিফিকেট নম্বর বা QR কোড দিয়ে যাচাই করুন আপনার সার্টিফিকেট বৈধ কিনা।', 'dpdt-trademark'); ?></p>
        </div>
        <div class="dpdt-page-content">
            <?php echo do_shortcode('[dpdt_verify]'); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
