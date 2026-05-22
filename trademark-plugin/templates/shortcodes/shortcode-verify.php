<?php
if (!defined('ABSPATH')) exit;

$verify_status = get_query_var('dpdt_verify_status', '');
$verify_result = get_query_var('dpdt_verify_result', null);
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
?>
<div class="dpdt-verify-wrapper">
    <h2 class="dpdt-verify-title"><?php echo esc_html($atts['title']); ?></h2>

    <!-- Search Form -->
    <div class="dpdt-verify-search">
        <form id="dpdt-verify-form" class="dpdt-verify-form">
            <div class="dpdt-verify-input-group">
                <input type="text" id="dpdt-verify-input" name="certificate_number" placeholder="<?php esc_attr_e('সার্টিফিকেট নম্বর লিখুন (যেমন: DPDT-CERT-2024-001000)', 'dpdt-trademark'); ?>" value="<?php echo esc_attr($token); ?>" />
                <button type="submit" class="dpdt-btn dpdt-btn-primary" id="dpdt-verify-btn">
                    <span class="dpdt-btn-text"><?php esc_html_e('যাচাই করুন', 'dpdt-trademark'); ?></span>
                    <span class="dpdt-btn-loading" style="display:none;">
                        <span class="dpdt-spinner"></span>
                    </span>
                </button>
            </div>
            <p class="dpdt-verify-hint"><?php esc_html_e('সার্টিফিকেট নম্বর বা যাচাই টোকেন দিয়ে খুঁজুন', 'dpdt-trademark'); ?></p>
        </form>
    </div>

    <!-- Result Area -->
    <div id="dpdt-verify-result" class="dpdt-verify-result" <?php echo empty($verify_status) ? 'style="display:none;"' : ''; ?>>
        <?php if ($verify_status === 'valid' && $verify_result) : ?>
            <!-- Pre-loaded result from URL -->
            <?php $this->render_verified_result($verify_result); ?>
        <?php endif; ?>
    </div>

    <!-- Result Template (for AJAX) -->
    <template id="dpdt-verify-template-valid">
        <div class="dpdt-verify-card dpdt-verified">
            <div class="dpdt-verify-badge">
                <span class="dpdt-verified-icon">✓</span>
                <h3><?php esc_html_e('সার্টিফিকেট যাচাই সম্পন্ন', 'dpdt-trademark'); ?></h3>
                <p class="dpdt-verified-text"><?php esc_html_e('এটি একটি বৈধ ট্রেডমার্ক সার্টিফিকেট', 'dpdt-trademark'); ?></p>
            </div>

            <div class="dpdt-verify-details">
                <!-- Brand Logo Section -->
                <div class="dpdt-verify-logo-section" id="verify-logo-section" style="display:none;">
                    <div class="dpdt-verify-brand-logo">
                        <img id="verify-brand-logo" src="" alt="" />
                    </div>
                    <p class="dpdt-verify-brand-name" id="verify-brand-owner"></p>
                </div>

                <!-- Certificate Image -->
                <div class="dpdt-verify-certificate-img" id="verify-cert-img-section" style="display:none;">
                    <img id="verify-cert-img" src="" alt="Certificate" style="max-width:100%;border:1px solid #ddd;padding:5px;border-radius:4px;" />
                </div>

                <!-- Details Table -->
                <table class="dpdt-verify-table">
                    <tr>
                        <th><?php esc_html_e('সার্টিফিকেট নম্বর', 'dpdt-trademark'); ?></th>
                        <td id="verify-cert-number"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('ট্রেডমার্ক নম্বর (Trademark Number)', 'dpdt-trademark'); ?></th>
                        <td id="verify-trademark-number"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('ব্র্যান্ড নাম', 'dpdt-trademark'); ?></th>
                        <td id="verify-brand-name"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('মালিক/আবেদনকারী', 'dpdt-trademark'); ?></th>
                        <td id="verify-holder-name"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('প্রতিষ্ঠান', 'dpdt-trademark'); ?></th>
                        <td id="verify-company"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('ট্রেডমার্ক শ্রেণী', 'dpdt-trademark'); ?></th>
                        <td id="verify-class"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('আবেদনের তারিখ', 'dpdt-trademark'); ?></th>
                        <td id="verify-app-date"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('নিবন্ধনের তারিখ', 'dpdt-trademark'); ?></th>
                        <td id="verify-reg-date"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('অনুমোদনের তারিখ', 'dpdt-trademark'); ?></th>
                        <td id="verify-approved-date"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('মেয়াদ উত্তীর্ণ', 'dpdt-trademark'); ?></th>
                        <td id="verify-expiry-date"></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('স্ট্যাটাস', 'dpdt-trademark'); ?></th>
                        <td id="verify-status"></td>
                    </tr>
                </table>

                <!-- QR Code -->
                <div class="dpdt-verify-qr" id="verify-qr-section" style="display:none;text-align:center;margin-top:20px;">
                    <img id="verify-qr-img" src="" alt="QR Code" style="max-width:200px;" />
                </div>

                <!-- Certificate URL -->
                <div class="dpdt-verify-cert-url" id="verify-cert-url-section" style="display:none;text-align:center;margin-top:10px;">
                    <a id="verify-cert-url" href="" target="_blank" style="color:#1a5276;text-decoration:underline;"></a>
                </div>
            </div>

            <div class="dpdt-verify-footer">
                <p><?php printf(esc_html__('যাচাইকৃত: %s', 'dpdt-trademark'), current_time('d/m/Y h:i A')); ?></p>
                <p class="dpdt-verify-disclaimer"><?php echo esc_html(get_option('dpdt_site_name', '')); ?></p>
            </div>
        </div>
    </template>

    <template id="dpdt-verify-template-invalid">
        <div class="dpdt-verify-card dpdt-invalid">
            <div class="dpdt-verify-badge dpdt-badge-invalid">
                <span class="dpdt-invalid-icon">✗</span>
                <h3><?php esc_html_e('অবৈধ সার্টিফিকেট', 'dpdt-trademark'); ?></h3>
                <p class="dpdt-invalid-text"><?php esc_html_e('এই তথ্য দিয়ে কোনো বৈধ সার্টিফিকেট পাওয়া যায়নি।', 'dpdt-trademark'); ?></p>
            </div>
        </div>
    </template>
</div>
