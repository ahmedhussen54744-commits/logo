<?php
if (!defined('ABSPATH')) exit;
settings_errors('dpdt_categories');

$category_manager = new DPDT_Category_Manager();
$categories = $category_manager->get_category_pages();
?>
<div class="wrap dpdt-category-settings-wrap">
    <h1><span class="dashicons dashicons-category"></span> <?php esc_html_e('ক্যাটাগরি / পেজ ম্যানেজমেন্ট', 'dpdt-trademark'); ?></h1>
    <p class="description"><?php esc_html_e('এখান থেকে সাইটের ক্যাটাগরি পেজগুলো এবং নেভিগেশন মেনু পরিচালনা করুন। পেজগুলো WordPress Pages হিসেবে তৈরি হয় এবং মেনু Appearance > Menus থেকেও পরিচালনা করা যায়।', 'dpdt-trademark'); ?></p>

    <!-- Quick Actions -->
    <div class="dpdt-category-actions">
        <form method="post" style="display:inline;">
            <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>
            <input type="hidden" name="dpdt_category_action" value="recreate_pages" />
            <button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('সকল ক্যাটাগরি পেজ পুনরায় তৈরি করতে চান?', 'dpdt-trademark'); ?>');">
                <span class="dashicons dashicons-update"></span> <?php esc_html_e('পেজ পুনরায় তৈরি করুন', 'dpdt-trademark'); ?>
            </button>
        </form>

        <form method="post" style="display:inline;">
            <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>
            <input type="hidden" name="dpdt_category_action" value="recreate_menu" />
            <button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('মেনু পুনরায় তৈরি করতে চান?', 'dpdt-trademark'); ?>');">
                <span class="dashicons dashicons-menu"></span> <?php esc_html_e('মেনু পুনরায় তৈরি করুন', 'dpdt-trademark'); ?>
            </button>
        </form>

        <a href="<?php echo admin_url('nav-menus.php'); ?>" class="button button-secondary">
            <span class="dashicons dashicons-admin-appearance"></span> <?php esc_html_e('Appearance > Menus এ যান', 'dpdt-trademark'); ?>
        </a>
    </div>

    <!-- Existing Pages -->
    <div class="dpdt-category-list">
        <h2><?php esc_html_e('বর্তমান ক্যাটাগরি পেজ', 'dpdt-trademark'); ?></h2>
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('পেজ', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('স্লাগ', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('স্ট্যাটাস', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('কার্যক্রম', 'dpdt-trademark'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat) : ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html($cat['title']); ?></strong>
                            <?php if (!empty($cat['children'])) : ?>
                                <ul class="dpdt-children-list">
                                    <?php foreach ($cat['children'] as $child) : ?>
                                        <li>— <?php echo esc_html($child['title']); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo esc_html($cat['slug']); ?></code></td>
                        <td>
                            <?php if (isset($cat['page_id'])) : ?>
                                <span class="dpdt-status dpdt-status-approved"><?php esc_html_e('সক্রিয়', 'dpdt-trademark'); ?></span>
                            <?php else : ?>
                                <span class="dpdt-status dpdt-status-rejected"><?php esc_html_e('পেজ নেই', 'dpdt-trademark'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($cat['page_id'])) : ?>
                                <a href="<?php echo get_edit_post_link($cat['page_id']); ?>" class="button button-small"><?php esc_html_e('সম্পাদনা', 'dpdt-trademark'); ?></a>
                                <a href="<?php echo esc_url($cat['url']); ?>" class="button button-small" target="_blank"><?php esc_html_e('দেখুন', 'dpdt-trademark'); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add New Page -->
    <div class="dpdt-add-page-section">
        <h2><?php esc_html_e('নতুন পেজ যোগ করুন', 'dpdt-trademark'); ?></h2>
        <form method="post">
            <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>
            <input type="hidden" name="dpdt_category_action" value="add_page" />
            <table class="form-table">
                <tr>
                    <th><label for="page_title"><?php esc_html_e('পেজ শিরোনাম', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="page_title" name="page_title" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="page_slug"><?php esc_html_e('স্লাগ', 'dpdt-trademark'); ?></label></th>
                    <td><input type="text" id="page_slug" name="page_slug" class="regular-text" required /></td>
                </tr>
                <tr>
                    <th><label for="page_parent"><?php esc_html_e('প্যারেন্ট পেজ', 'dpdt-trademark'); ?></label></th>
                    <td>
                        <select id="page_parent" name="page_parent">
                            <option value="0"><?php esc_html_e('— কোনো প্যারেন্ট নেই —', 'dpdt-trademark'); ?></option>
                            <?php foreach ($categories as $cat) : ?>
                                <?php if (isset($cat['page_id'])) : ?>
                                    <option value="<?php echo intval($cat['page_id']); ?>"><?php echo esc_html($cat['title']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <button type="submit" class="button button-primary"><?php esc_html_e('পেজ তৈরি করুন', 'dpdt-trademark'); ?></button>
            </p>
        </form>
    </div>
</div>
