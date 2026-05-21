<?php
if (!defined('ABSPATH')) exit;
settings_errors('dpdt_logos');
?>
<div class="wrap dpdt-logo-settings-wrap">
    <h1><span class="dashicons dashicons-format-image"></span> <?php esc_html_e('লোগো সেটিংস', 'dpdt-trademark'); ?></h1>
    <p class="description"><?php esc_html_e('সাইটের সকল লোগো এখান থেকে পরিচালনা করুন। Media Library থেকে ছবি নির্বাচন করুন বা নতুন আপলোড করুন।', 'dpdt-trademark'); ?></p>

    <form method="post" enctype="multipart/form-data">
        <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>

        <!-- Header Logo -->
        <div class="dpdt-logo-section">
            <h2><?php esc_html_e('হেডার লোগো', 'dpdt-trademark'); ?></h2>
            <p class="description"><?php esc_html_e('সাইটের উপরে যে লোগো দেখাবে। প্রস্তাবিত সাইজ: 300x80px', 'dpdt-trademark'); ?></p>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('লোগো', 'dpdt-trademark'); ?></th>
                    <td>
                        <div class="dpdt-logo-preview">
                            <?php if (!empty($logos['header_logo'])) : ?>
                                <img src="<?php echo esc_url($logos['header_logo']); ?>" style="max-width:300px;max-height:80px;" />
                            <?php else : ?>
                                <p class="dpdt-no-logo"><?php esc_html_e('কোনো লোগো নির্বাচন করা হয়নি', 'dpdt-trademark'); ?></p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="header_logo_id" id="header_logo_id" value="<?php echo intval($logos['header_logo_id']); ?>" />
                        <button type="button" class="button dpdt-upload-btn" data-target="header_logo_id"><?php esc_html_e('লোগো নির্বাচন করুন', 'dpdt-trademark'); ?></button>
                        <button type="button" class="button dpdt-remove-btn" data-target="header_logo_id"><?php esc_html_e('সরান', 'dpdt-trademark'); ?></button>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Alt টেক্সট', 'dpdt-trademark'); ?></th>
                    <td><input type="text" name="header_logo_alt" value="<?php echo esc_attr($logos['header_logo_alt']); ?>" class="regular-text" /></td>
                </tr>
            </table>
        </div>

        <!-- Favicon -->
        <div class="dpdt-logo-section">
            <h2><?php esc_html_e('ফেভিকন', 'dpdt-trademark'); ?></h2>
            <p class="description"><?php esc_html_e('ব্রাউজার ট্যাবে যে আইকন দেখাবে। প্রস্তাবিত সাইজ: 32x32px বা 64x64px', 'dpdt-trademark'); ?></p>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('আইকন', 'dpdt-trademark'); ?></th>
                    <td>
                        <div class="dpdt-logo-preview">
                            <?php if (!empty($logos['favicon'])) : ?>
                                <img src="<?php echo esc_url($logos['favicon']); ?>" style="max-width:64px;max-height:64px;" />
                            <?php else : ?>
                                <p class="dpdt-no-logo"><?php esc_html_e('কোনো ফেভিকন নির্বাচন করা হয়নি', 'dpdt-trademark'); ?></p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="favicon_id" id="favicon_id" value="<?php echo intval($logos['favicon_id']); ?>" />
                        <button type="button" class="button dpdt-upload-btn" data-target="favicon_id"><?php esc_html_e('নির্বাচন করুন', 'dpdt-trademark'); ?></button>
                        <button type="button" class="button dpdt-remove-btn" data-target="favicon_id"><?php esc_html_e('সরান', 'dpdt-trademark'); ?></button>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer Logo -->
        <div class="dpdt-logo-section">
            <h2><?php esc_html_e('ফুটার লোগো', 'dpdt-trademark'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('লোগো', 'dpdt-trademark'); ?></th>
                    <td>
                        <div class="dpdt-logo-preview">
                            <?php if (!empty($logos['footer_logo'])) : ?>
                                <img src="<?php echo esc_url($logos['footer_logo']); ?>" style="max-width:200px;max-height:60px;" />
                            <?php else : ?>
                                <p class="dpdt-no-logo"><?php esc_html_e('কোনো লোগো নির্বাচন করা হয়নি', 'dpdt-trademark'); ?></p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="footer_logo_id" id="footer_logo_id" value="<?php echo intval($logos['footer_logo_id']); ?>" />
                        <button type="button" class="button dpdt-upload-btn" data-target="footer_logo_id"><?php esc_html_e('নির্বাচন করুন', 'dpdt-trademark'); ?></button>
                        <button type="button" class="button dpdt-remove-btn" data-target="footer_logo_id"><?php esc_html_e('সরান', 'dpdt-trademark'); ?></button>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Category Logos -->
        <div class="dpdt-logo-section">
            <h2><?php esc_html_e('ক্যাটাগরি লোগো', 'dpdt-trademark'); ?></h2>
            <p class="description"><?php esc_html_e('প্রতিটি ক্যাটাগরি পেজের জন্য আলাদা লোগো/আইকন সেট করুন।', 'dpdt-trademark'); ?></p>
            <table class="form-table dpdt-category-logos-table">
                <?php foreach ($categories as $cat) : ?>
                    <tr>
                        <th><?php echo esc_html($cat['title']); ?></th>
                        <td>
                            <?php
                            $cat_logo_id = 0;
                            if (isset($logos['category_logos'][$cat['slug']]['id'])) {
                                $cat_logo_id = intval($logos['category_logos'][$cat['slug']]['id']);
                            }
                            ?>
                            <div class="dpdt-logo-preview-small">
                                <?php if ($cat_logo_id && ($img_url = wp_get_attachment_image_url($cat_logo_id, 'thumbnail'))) : ?>
                                    <img src="<?php echo esc_url($img_url); ?>" style="max-width:48px;max-height:48px;" />
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="category_logos[<?php echo esc_attr($cat['slug']); ?>]" id="cat_logo_<?php echo esc_attr($cat['slug']); ?>" value="<?php echo $cat_logo_id; ?>" />
                            <button type="button" class="button button-small dpdt-upload-btn" data-target="cat_logo_<?php echo esc_attr($cat['slug']); ?>"><?php esc_html_e('নির্বাচন', 'dpdt-trademark'); ?></button>
                            <button type="button" class="button button-small dpdt-remove-btn" data-target="cat_logo_<?php echo esc_attr($cat['slug']); ?>"><?php esc_html_e('সরান', 'dpdt-trademark'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <p class="submit">
            <input type="submit" name="dpdt_save_logos" class="button button-primary button-large" value="<?php esc_attr_e('লোগো সেটিংস সংরক্ষণ করুন', 'dpdt-trademark'); ?>" />
        </p>
    </form>
</div>
