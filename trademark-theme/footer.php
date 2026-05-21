<?php
if (!defined('ABSPATH')) exit;

$site_name = get_option('dpdt_site_name', get_bloginfo('name'));
$copyright = get_option('dpdt_copyright_text', '');
$address = get_option('dpdt_site_address', '');
$phone = get_option('dpdt_site_phone', '');
$email = get_option('dpdt_site_email', '');
$facebook = get_option('dpdt_social_facebook', '');
$twitter = get_option('dpdt_social_twitter', '');
$youtube = get_option('dpdt_social_youtube', '');
$established = get_option('dpdt_site_established', '২০০৯');
?>
</div><!-- #content -->

<footer id="colophon" class="site-footer">
    <!-- Footer Widgets -->
    <div class="footer-widgets">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-1')) : ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php else : ?>
                        <h4 class="widget-title"><?php echo esc_html($site_name); ?></h4>
                        <p><?php echo esc_html(get_option('dpdt_site_description', '')); ?></p>
                        <?php if ($established) : ?>
                            <p class="footer-established"><?php printf(esc_html__('প্রতিষ্ঠিত: %s', 'dpdt-theme'), esc_html($established)); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-2')) : ?>
                        <?php dynamic_sidebar('footer-2'); ?>
                    <?php else : ?>
                        <h4 class="widget-title"><?php esc_html_e('দ্রুত লিংক', 'dpdt-theme'); ?></h4>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class' => 'footer-menu',
                            'depth' => 1,
                            'fallback_cb' => false,
                        ));
                        ?>
                    <?php endif; ?>
                </div>

                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-3')) : ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php else : ?>
                        <h4 class="widget-title"><?php esc_html_e('যোগাযোগ', 'dpdt-theme'); ?></h4>
                        <?php if ($address) : ?>
                            <p><span class="dashicons dashicons-location"></span> <?php echo esc_html($address); ?></p>
                        <?php endif; ?>
                        <?php if ($phone) : ?>
                            <p><span class="dashicons dashicons-phone"></span> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></p>
                        <?php endif; ?>
                        <?php if ($email) : ?>
                            <p><span class="dashicons dashicons-email"></span> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="footer-col">
                    <?php if (is_active_sidebar('footer-4')) : ?>
                        <?php dynamic_sidebar('footer-4'); ?>
                    <?php else : ?>
                        <h4 class="widget-title"><?php esc_html_e('সামাজিক মাধ্যম', 'dpdt-theme'); ?></h4>
                        <div class="footer-social">
                            <?php if ($facebook) : ?>
                                <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" class="social-link social-facebook" aria-label="Facebook">
                                    <span class="dashicons dashicons-facebook-alt"></span>
                                </a>
                            <?php endif; ?>
                            <?php if ($twitter) : ?>
                                <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" class="social-link social-twitter" aria-label="Twitter">
                                    <span class="dashicons dashicons-twitter"></span>
                                </a>
                            <?php endif; ?>
                            <?php if ($youtube) : ?>
                                <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener" class="social-link social-youtube" aria-label="YouTube">
                                    <span class="dashicons dashicons-video-alt3"></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom-content">
                <p class="copyright">
                    <?php
                    if ($copyright) {
                        echo esc_html($copyright);
                    } else {
                        printf(
                            esc_html__('© %s %s। সর্বস্বত্ব সংরক্ষিত।', 'dpdt-theme'),
                            esc_html($established . '-' . date('Y')),
                            esc_html($site_name)
                        );
                    }
                    ?>
                </p>
                <p class="footer-credits">
                    <?php esc_html_e('শিল্প মন্ত্রণালয়, গণপ্রজাতন্ত্রী বাংলাদেশ সরকার', 'dpdt-theme'); ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
