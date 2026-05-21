<?php
if (!defined('ABSPATH')) exit;
settings_errors('dpdt_settings');
?>
<div class="wrap dpdt-settings-wrap">
    <h1><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e('সাইট সেটিংস', 'dpdt-trademark'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>

        <!-- General Settings -->
        <div class="dpdt-settings-section">
            <h2><?php esc_html_e('সাধারণ সেটিংস', 'dpdt-trademark'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="dpdt_site_name"><?php esc_html_e('সাইটের নাম (বাংলা)', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_site_name" name="dpdt_site_name" value="<?php echo esc_attr(get_option('dpdt_site_name')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_site_name_en"><?php esc_html_e('সাইটের নাম (English)', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_site_name_en" name="dpdt_site_name_en" value="<?php echo esc_attr(get_option('dpdt_site_name_en')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_site_description"><?php esc_html_e('সাইটের বর্ণনা', 'dpdt-trademark'); ?></label></th>
                    <td><textarea id="dpdt_site_description" name="dpdt_site_description" rows="3" class="large-text"><?php echo esc_textarea(get_option('dpdt_site_description')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_site_phone"><?php esc_html_e('ফোন নম্বর', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_site_phone" name="dpdt_site_phone" value="<?php echo esc_attr(get_option('dpdt_site_phone')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_site_email"><?php esc_html_e('ইমেইল', 'dpdt-trademark'); ?></label></th>
                    <td><input type="email" id="dpdt_site_email" name="dpdt_site_email" value="<?php echo esc_attr(get_option('dpdt_site_email')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_site_address"><?php esc_html_e('ঠিকানা', 'dpdt-trademark'); ?></label></th>
                    <td><textarea id="dpdt_site_address" name="dpdt_site_address" rows="3" class="large-text"><?php echo esc_textarea(get_option('dpdt_site_address')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_site_established"><?php esc_html_e('প্রতিষ্ঠাকাল', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_site_established" name="dpdt_site_established" value="<?php echo esc_attr(get_option('dpdt_site_established', '২০০৯')); ?>" class="small-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_copyright_text"><?php esc_html_e('কপিরাইট টেক্সট', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_copyright_text" name="dpdt_copyright_text" value="<?php echo esc_attr(get_option('dpdt_copyright_text')); ?>" class="large-text" /></td>
                </tr>
            </table>
        </div>

        <!-- Social Links -->
        <div class="dpdt-settings-section">
            <h2><?php esc_html_e('সামাজিক মাধ্যম লিংক', 'dpdt-trademark'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="dpdt_social_facebook"><?php esc_html_e('Facebook', 'dpdt-trademark'); ?></label></th>
                    <td><input type="url" id="dpdt_social_facebook" name="dpdt_social_facebook" value="<?php echo esc_attr(get_option('dpdt_social_facebook')); ?>" class="regular-text" placeholder="https://facebook.com/..." /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_social_twitter"><?php esc_html_e('Twitter/X', 'dpdt-trademark'); ?></label></th>
                    <td><input type="url" id="dpdt_social_twitter" name="dpdt_social_twitter" value="<?php echo esc_attr(get_option('dpdt_social_twitter')); ?>" class="regular-text" placeholder="https://twitter.com/..." /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_social_youtube"><?php esc_html_e('YouTube', 'dpdt-trademark'); ?></label></th>
                    <td><input type="url" id="dpdt_social_youtube" name="dpdt_social_youtube" value="<?php echo esc_attr(get_option('dpdt_social_youtube')); ?>" class="regular-text" placeholder="https://youtube.com/..." /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_social_linkedin"><?php esc_html_e('LinkedIn', 'dpdt-trademark'); ?></label></th>
                    <td><input type="url" id="dpdt_social_linkedin" name="dpdt_social_linkedin" value="<?php echo esc_attr(get_option('dpdt_social_linkedin')); ?>" class="regular-text" placeholder="https://linkedin.com/..." /></td>
                </tr>
            </table>
        </div>

        <!-- Certificate Settings -->
        <div class="dpdt-settings-section">
            <h2><?php esc_html_e('সার্টিফিকেট সেটিংস', 'dpdt-trademark'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="dpdt_verify_base_url"><?php esc_html_e('যাচাই পেজ URL', 'dpdt-trademark'); ?></label></th>
                    <td>
                        <input type="url" id="dpdt_verify_base_url" name="dpdt_verify_base_url" value="<?php echo esc_attr(get_option('dpdt_verify_base_url')); ?>" class="large-text" />
                        <p class="description"><?php esc_html_e('সার্টিফিকেট যাচাইয়ের জন্য বেস URL। এটি পরিবর্তন করলে QR কোড আপডেট হবে।', 'dpdt-trademark'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_certificate_prefix"><?php esc_html_e('সার্টিফিকেট প্রিফিক্স', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_certificate_prefix" name="dpdt_certificate_prefix" value="<?php echo esc_attr(get_option('dpdt_certificate_prefix', 'DPDT')); ?>" class="small-text" /></td>
                </tr>
            </table>
        </div>

        <!-- Security Settings -->
        <div class="dpdt-settings-section">
            <h2><?php esc_html_e('নিরাপত্তা সেটিংস', 'dpdt-trademark'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="dpdt_rate_limit_attempts"><?php esc_html_e('Rate Limit (প্রচেষ্টা)', 'dpdt-trademark'); ?></label></th>
                    <td><input type="number" id="dpdt_rate_limit_attempts" name="dpdt_rate_limit_attempts" value="<?php echo intval(get_option('dpdt_rate_limit_attempts', 5)); ?>" class="small-text" min="1" max="100" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_rate_limit_window"><?php esc_html_e('Rate Limit Window (সেকেন্ড)', 'dpdt-trademark'); ?></label></th>
                    <td><input type="number" id="dpdt_rate_limit_window" name="dpdt_rate_limit_window" value="<?php echo intval(get_option('dpdt_rate_limit_window', 300)); ?>" class="small-text" min="60" max="3600" /></td>
                </tr>
            </table>
        </div>

        <!-- Email Settings -->
        <div class="dpdt-settings-section">
            <h2><?php esc_html_e('ইমেইল সেটিংস', 'dpdt-trademark'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="dpdt_admin_notification_email"><?php esc_html_e('নোটিফিকেশন ইমেইল', 'dpdt-trademark'); ?></label></th>
                    <td><input type="email" id="dpdt_admin_notification_email" name="dpdt_admin_notification_email" value="<?php echo esc_attr(get_option('dpdt_admin_notification_email', get_option('admin_email'))); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_email_from_name"><?php esc_html_e('প্রেরকের নাম', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="dpdt_email_from_name" name="dpdt_email_from_name" value="<?php echo esc_attr(get_option('dpdt_email_from_name', get_option('dpdt_site_name'))); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th scope="row"><label for="dpdt_email_from_address"><?php esc_html_e('প্রেরকের ইমেইল', 'dpdt-trademark'); ?></label></th>
                    <td><input type="email" id="dpdt_email_from_address" name="dpdt_email_from_address" value="<?php echo esc_attr(get_option('dpdt_email_from_address', '')); ?>" class="regular-text" /></td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <input type="submit" name="dpdt_save_settings" class="button button-primary button-large" value="<?php esc_attr_e('সেটিংস সংরক্ষণ করুন', 'dpdt-trademark'); ?>" />
        </p>
    </form>
</div>
