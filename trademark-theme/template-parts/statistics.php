<?php
if (!defined('ABSPATH')) exit;
?>
<section class="home-statistics-section">
    <div class="container">
        <div class="section-header section-header-light">
            <h2><?php esc_html_e('পরিসংখ্যান', 'dpdt-theme'); ?></h2>
        </div>
        <?php echo do_shortcode('[dpdt_statistics]'); ?>
    </div>
</section>
