<?php
if (!defined('ABSPATH')) exit;
?>
<section class="home-services-section">
    <div class="container">
        <div class="section-header">
            <h2><?php esc_html_e('আমাদের সেবাসমূহ', 'dpdt-theme'); ?></h2>
            <p><?php esc_html_e('পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর কর্তৃক প্রদত্ত সেবাসমূহ', 'dpdt-theme'); ?></p>
        </div>
        <?php echo do_shortcode('[dpdt_services]'); ?>
    </div>
</section>
