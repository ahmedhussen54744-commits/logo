<?php
if (!defined('ABSPATH')) exit;
get_header();

$code = get_query_var('dpdt_code');
$app = null;
$verified = false;

if (!empty($code)) {
    $app = DPDT_Verify::get_verified_data($code);
    if ($app) $verified = true;
}
?>

<section class="dpdt-verify-section">
    <div class="container">
        
        <?php if (empty($code)): ?>
        <!-- Verification Search Form -->
        <div class="verify-search-card">
            <div class="verify-icon-large">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>Certificate Verification</h2>
            <p>সার্টিফিকেট যাচাই করুন | Verify Your Trademark Certificate</p>
            <form method="POST" action="" class="verify-form">
                <?php wp_nonce_field('dpdt_verify_search', 'verify_nonce'); ?>
                <div class="verify-input-group">
                    <input type="text" name="verify_code" placeholder="Enter Application/Registration Code" required>
                    <button type="submit"><i class="fas fa-search"></i> Verify</button>
                </div>
            </form>
            <p class="verify-hint"><i class="fas fa-qrcode"></i> You can also scan the QR code on your certificate</p>
        </div>

        <?php
        // Handle POST search
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_nonce'])) {
            if (wp_verify_nonce($_POST['verify_nonce'], 'dpdt_verify_search')) {
                $search_code = sanitize_text_field($_POST['verify_code'] ?? '');
                if (!empty($search_code)) {
                    $app = DPDT_Verify::get_verified_data($search_code);
                    if ($app) {
                        $verified = true;
                    } else {
                        echo '<div class="verify-not-found">';
                        echo '<i class="fas fa-times-circle"></i>';
                        echo '<h3>Certificate Not Found</h3>';
                        echo '<p>No verified certificate found with this code. Please check and try again.</p>';
                        echo '</div>';
                    }
                }
            }
        }
        ?>

        <?php endif; ?>

        <?php if ($verified && $app): ?>
        <!-- Verified Certificate Display -->
        <div class="verify-result-card">
            <!-- Verification Badge -->
            <div class="verify-badge-header">
                <div class="badge-circle verified">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="verified-text">Certificate Verified</h2>
                <p class="verified-sub">This trademark certificate is authentic and valid</p>
            </div>

            <!-- Brand Logo Highlight -->
            <?php if (!empty($app->logo_url)): ?>
            <div class="verify-logo-section">
                <div class="verify-logo-wrapper">
                    <img src="<?php echo esc_url($app->logo_url); ?>" alt="<?php echo esc_attr($app->brand_name); ?>" class="verify-brand-logo">
                </div>
                <div class="verify-brand-info">
                    <h3 class="brand-name-display"><?php echo esc_html($app->brand_name); ?></h3>
                    <p class="owner-name-display"><?php echo esc_html($app->owner_name); ?></p>
                </div>
            </div>
            <?php else: ?>
            <div class="verify-logo-section no-logo">
                <div class="verify-brand-info">
                    <h3 class="brand-name-display"><?php echo esc_html($app->brand_name); ?></h3>
                    <p class="owner-name-display"><?php echo esc_html($app->owner_name); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Certificate Details -->
            <div class="verify-details-grid">
                <div class="detail-item">
                    <span class="detail-label">Application Code</span>
                    <span class="detail-value"><?php echo esc_html($app->app_code); ?></span>
                </div>
                <?php if (!empty($app->registration_number)): ?>
                <div class="detail-item">
                    <span class="detail-label">Registration Number</span>
                    <span class="detail-value"><?php echo esc_html($app->registration_number); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-item">
                    <span class="detail-label">Brand / Mark Name</span>
                    <span class="detail-value"><?php echo esc_html($app->brand_name); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Owner</span>
                    <span class="detail-value"><?php echo esc_html($app->owner_name); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Trademark Type</span>
                    <span class="detail-value"><?php echo esc_html($app->trademark_type); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Class</span>
                    <span class="detail-value"><?php echo esc_html($app->trademark_class); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Application Date</span>
                    <span class="detail-value"><?php echo esc_html(date('d M Y', strtotime($app->application_date))); ?></span>
                </div>
                <?php if (!empty($app->registration_date)): ?>
                <div class="detail-item">
                    <span class="detail-label">Registration Date</span>
                    <span class="detail-value"><?php echo esc_html(date('d M Y', strtotime($app->registration_date))); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($app->approved_date)): ?>
                <div class="detail-item">
                    <span class="detail-label">Approved Date</span>
                    <span class="detail-value"><?php echo esc_html(date('d M Y', strtotime($app->approved_date))); ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($app->expiry_date)): ?>
                <div class="detail-item">
                    <span class="detail-label">Expiry Date</span>
                    <span class="detail-value"><?php echo esc_html(date('d M Y', strtotime($app->expiry_date))); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <span class="detail-value status-approved"><i class="fas fa-check-circle"></i> Approved & Active</span>
                </div>
            </div>

            <!-- Certificate Image (JPG uploaded by admin) -->
            <?php if (!empty($app->certificate_jpg)): ?>
            <div class="verify-certificate-image">
                <h4><i class="fas fa-certificate"></i> Certificate Document</h4>
                <img src="<?php echo esc_url($app->certificate_jpg); ?>" alt="Certificate" class="cert-image">
            </div>
            <?php endif; ?>

            <!-- QR Code -->
            <div class="verify-qr-section">
                <h4>Verification QR Code</h4>
                <img src="<?php echo esc_url(DPDT_QRCode::get_for_application($app)); ?>" alt="QR Code" class="verify-qr-img">
                <p class="qr-note">Scan this QR code to verify this certificate anytime</p>
            </div>

            <!-- Footer Note -->
            <div class="verify-footer-note">
                <p><i class="fas fa-info-circle"></i> This certificate was issued by the Department of Patents, Designs and Trademarks (DPDT), Bangladesh.</p>
                <p class="since">System operational since 2009</p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
