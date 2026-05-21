<?php
if (!defined('ABSPATH')) exit;

get_header();
?>
<main id="primary" class="site-main">
    <div class="container">
        <div class="error-404-wrapper">
            <div class="error-404-content">
                <h1 class="error-code">৪০৪</h1>
                <h2><?php esc_html_e('পেজ খুঁজে পাওয়া যায়নি', 'dpdt-theme'); ?></h2>
                <p><?php esc_html_e('আপনি যে পেজটি খুঁজছেন সেটি বিদ্যমান নেই বা সরানো হয়েছে।', 'dpdt-theme'); ?></p>
                <div class="error-actions">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                        <span class="dashicons dashicons-admin-home"></span> <?php esc_html_e('হোম পেজে যান', 'dpdt-theme'); ?>
                    </a>
                </div>
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</main>
<?php
get_footer();
