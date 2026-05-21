<?php
if (!defined('ABSPATH')) exit;

/**
 * Certificate Layout Template
 * Renders the visual certificate for preview/print
 */
$site_name = isset($data['site_name']) ? $data['site_name'] : get_option('dpdt_site_name', 'DPDT');
?>
<div class="dpdt-certificate-wrapper">
    <div class="dpdt-cert-border">
        <div class="dpdt-cert-number">
            <?php echo esc_html($data['certificate_number']); ?>
        </div>

        <div class="dpdt-cert-header">
            <h1><?php echo esc_html($site_name); ?></h1>
            <h2><?php esc_html_e('ট্রেডমার্ক নিবন্ধন সনদপত্র', 'dpdt-trademark'); ?></h2>
            <p><?php esc_html_e('Trademark Registration Certificate', 'dpdt-trademark'); ?></p>
        </div>

        <div class="dpdt-cert-body">
            <?php if (!empty($data['brand_logo_url'])) : ?>
                <div class="dpdt-cert-logo">
                    <img src="<?php echo esc_url($data['brand_logo_url']); ?>" alt="<?php echo esc_attr($data['brand_name']); ?>" />
                    <p><strong><?php echo esc_html($data['brand_name']); ?></strong></p>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <td class="label"><?php esc_html_e('সার্টিফিকেট নম্বর', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html($data['certificate_number']); ?></td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e('ব্র্যান্ড নাম', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html($data['brand_name']); ?> <?php if (!empty($data['brand_name_bn'])) echo '(' . esc_html($data['brand_name_bn']) . ')'; ?></td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e('মালিক/আবেদনকারী', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html($data['applicant_name']); ?> <?php if (!empty($data['owner_name'])) echo ' / ' . esc_html($data['owner_name']); ?></td>
                </tr>
                <?php if (!empty($data['company_name'])) : ?>
                <tr>
                    <td class="label"><?php esc_html_e('প্রতিষ্ঠান', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html($data['company_name']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="label"><?php esc_html_e('ট্রেডমার্ক শ্রেণী', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html($data['trademark_class']); ?></td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e('আবেদনের তারিখ', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($data['application_date']))); ?></td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e('নিবন্ধনের তারিখ', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($data['registration_date']))); ?></td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e('অনুমোদনের তারিখ', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($data['approved_date']))); ?></td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e('মেয়াদ উত্তীর্ণ', 'dpdt-trademark'); ?></td>
                    <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($data['expiry_date']))); ?></td>
                </tr>
            </table>
        </div>

        <div class="dpdt-cert-footer">
            <?php if (!empty($data['qr_code_url'])) : ?>
            <div class="dpdt-cert-qr">
                <img src="<?php echo esc_url($data['qr_code_url']); ?>" alt="QR Code" />
                <p style="font-size: 8pt; margin: 1mm 0 0 0;"><?php esc_html_e('স্ক্যান করে যাচাই করুন', 'dpdt-trademark'); ?></p>
            </div>
            <?php endif; ?>

            <div class="dpdt-cert-seal">
                <p style="border-top: 1px solid #333; padding-top: 2mm; margin-top: 10mm;">
                    <?php esc_html_e('মহাপরিচালক', 'dpdt-trademark'); ?><br>
                    <?php echo esc_html($site_name); ?>
                </p>
            </div>
        </div>
    </div>
</div>
