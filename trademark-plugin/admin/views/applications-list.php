<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap dpdt-applications-wrap">
    <h1><?php esc_html_e('আবেদন সমূহ', 'dpdt-trademark'); ?>
        <span class="dpdt-count">(<?php echo intval($total); ?>)</span>
    </h1>

    <!-- Status Filter -->
    <ul class="subsubsub">
        <li><a href="<?php echo admin_url('admin.php?page=dpdt-applications'); ?>" <?php echo empty($status) ? 'class="current"' : ''; ?>><?php esc_html_e('সকল', 'dpdt-trademark'); ?> <span class="count">(<?php echo $this->db->count_applications(); ?>)</span></a> |</li>
        <li><a href="<?php echo admin_url('admin.php?page=dpdt-applications&status=pending'); ?>" <?php echo $status === 'pending' ? 'class="current"' : ''; ?>><?php esc_html_e('অপেক্ষমাণ', 'dpdt-trademark'); ?> <span class="count">(<?php echo $this->db->count_applications('pending'); ?>)</span></a> |</li>
        <li><a href="<?php echo admin_url('admin.php?page=dpdt-applications&status=approved'); ?>" <?php echo $status === 'approved' ? 'class="current"' : ''; ?>><?php esc_html_e('অনুমোদিত', 'dpdt-trademark'); ?> <span class="count">(<?php echo $this->db->count_applications('approved'); ?>)</span></a> |</li>
        <li><a href="<?php echo admin_url('admin.php?page=dpdt-applications&status=rejected'); ?>" <?php echo $status === 'rejected' ? 'class="current"' : ''; ?>><?php esc_html_e('প্রত্যাখ্যাত', 'dpdt-trademark'); ?> <span class="count">(<?php echo $this->db->count_applications('rejected'); ?>)</span></a></li>
    </ul>

    <!-- Search -->
    <form method="get" class="dpdt-search-form">
        <input type="hidden" name="page" value="dpdt-applications">
        <?php if (!empty($status)) : ?>
            <input type="hidden" name="status" value="<?php echo esc_attr($status); ?>">
        <?php endif; ?>
        <p class="search-box">
            <input type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('নাম, ব্র্যান্ড বা আবেদন নম্বর দিয়ে খুঁজুন...', 'dpdt-trademark'); ?>">
            <input type="submit" class="button" value="<?php esc_attr_e('অনুসন্ধান', 'dpdt-trademark'); ?>">
        </p>
    </form>

    <!-- Applications Table -->
    <form method="post">
        <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>

        <div class="tablenav top">
            <select name="dpdt_bulk_action">
                <option value=""><?php esc_html_e('গণ কার্যক্রম', 'dpdt-trademark'); ?></option>
                <option value="delete"><?php esc_html_e('মুছে ফেলুন', 'dpdt-trademark'); ?></option>
                <option value="mark_pending"><?php esc_html_e('অপেক্ষমাণ চিহ্নিত করুন', 'dpdt-trademark'); ?></option>
            </select>
            <input type="submit" class="button action" value="<?php esc_attr_e('প্রয়োগ', 'dpdt-trademark'); ?>">
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column"><input type="checkbox" /></td>
                    <th><?php esc_html_e('আবেদন নম্বর', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('আবেদনকারী', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('ব্র্যান্ড', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('শ্রেণী', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('স্ট্যাটাস', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('তারিখ', 'dpdt-trademark'); ?></th>
                    <th><?php esc_html_e('কার্যক্রম', 'dpdt-trademark'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($applications)) : ?>
                    <?php foreach ($applications as $app) : ?>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" name="application_ids[]" value="<?php echo intval($app->id); ?>" />
                            </th>
                            <td>
                                <strong><?php echo esc_html($app->application_id); ?></strong>
                                <?php if ($app->certificate_number) : ?>
                                    <br><small>Cert: <?php echo esc_html($app->certificate_number); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo esc_html($app->applicant_name); ?>
                                <br><small><?php echo esc_html($app->applicant_email); ?></small>
                            </td>
                            <td>
                                <?php echo esc_html($app->brand_name); ?>
                                <?php if ($app->brand_logo_url) : ?>
                                    <br><img src="<?php echo esc_url($app->brand_logo_url); ?>" style="max-width:30px;max-height:30px;" />
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($app->trademark_class); ?></td>
                            <td>
                                <span class="dpdt-status dpdt-status-<?php echo esc_attr($app->status); ?>">
                                    <?php
                                    $status_labels = array(
                                        'pending' => 'অপেক্ষমাণ',
                                        'approved' => 'অনুমোদিত',
                                        'rejected' => 'প্রত্যাখ্যাত',
                                        'revoked' => 'বাতিল',
                                    );
                                    echo esc_html(isset($status_labels[$app->status]) ? $status_labels[$app->status] : $app->status);
                                    ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($app->created_at))); ?></td>
                            <td>
                                <div class="dpdt-actions">
                                    <?php if ($app->status === 'pending') : ?>
                                        <button type="button" class="button button-small button-primary dpdt-approve-btn" data-id="<?php echo intval($app->id); ?>"><?php esc_html_e('অনুমোদন', 'dpdt-trademark'); ?></button>
                                        <button type="button" class="button button-small dpdt-reject-btn" data-id="<?php echo intval($app->id); ?>"><?php esc_html_e('প্রত্যাখ্যান', 'dpdt-trademark'); ?></button>
                                    <?php endif; ?>
                                    <button type="button" class="button button-small dpdt-view-btn" data-id="<?php echo intval($app->id); ?>"><?php esc_html_e('বিস্তারিত', 'dpdt-trademark'); ?></button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8" class="dpdt-no-data"><?php esc_html_e('কোনো আবেদন পাওয়া যায়নি।', 'dpdt-trademark'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>

    <!-- Pagination -->
    <?php if ($total_pages > 1) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => $total_pages,
                    'current' => $paged,
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Approve Modal -->
    <div id="dpdt-approve-modal" class="dpdt-modal" style="display:none;">
        <div class="dpdt-modal-content">
            <h2><?php esc_html_e('আবেদন অনুমোদন', 'dpdt-trademark'); ?></h2>
            <form id="dpdt-approve-form" enctype="multipart/form-data">
                <input type="hidden" name="application_id" id="approve-app-id" />
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('নিবন্ধন তারিখ', 'dpdt-trademark'); ?></th>
                        <td><input type="date" name="registration_date" value="<?php echo date('Y-m-d'); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('অনুমোদন তারিখ', 'dpdt-trademark'); ?></th>
                        <td><input type="date" name="approved_date" value="<?php echo date('Y-m-d'); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('মেয়াদ উত্তীর্ণ তারিখ', 'dpdt-trademark'); ?></th>
                        <td><input type="date" name="expiry_date" value="<?php echo date('Y-m-d', strtotime('+7 years')); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('সার্টিফিকেট PDF', 'dpdt-trademark'); ?></th>
                        <td><input type="file" name="certificate_pdf" accept=".pdf" /></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('সার্টিফিকেট JPG', 'dpdt-trademark'); ?></th>
                        <td><input type="file" name="certificate_jpg" accept=".jpg,.jpeg,.png" /></td>
                    </tr>
                    <tr>
                        <th><?php esc_html_e('মন্তব্য', 'dpdt-trademark'); ?></th>
                        <td><textarea name="admin_notes" rows="3" class="large-text"></textarea></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e('অনুমোদন করুন', 'dpdt-trademark'); ?></button>
                    <button type="button" class="button dpdt-modal-close"><?php esc_html_e('বাতিল', 'dpdt-trademark'); ?></button>
                </p>
            </form>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="dpdt-reject-modal" class="dpdt-modal" style="display:none;">
        <div class="dpdt-modal-content">
            <h2><?php esc_html_e('আবেদন প্রত্যাখ্যান', 'dpdt-trademark'); ?></h2>
            <form id="dpdt-reject-form">
                <input type="hidden" name="application_id" id="reject-app-id" />
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e('প্রত্যাখ্যানের কারণ', 'dpdt-trademark'); ?></th>
                        <td><textarea name="reason" rows="4" class="large-text" required></textarea></td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e('প্রত্যাখ্যান করুন', 'dpdt-trademark'); ?></button>
                    <button type="button" class="button dpdt-modal-close"><?php esc_html_e('বাতিল', 'dpdt-trademark'); ?></button>
                </p>
            </form>
        </div>
    </div>
</div>
