<?php
if (!defined('ABSPATH')) exit;

$app_id = isset($_GET['app_id']) ? intval($_GET['app_id']) : 0;
if (!$app_id) {
    echo '<div class="wrap"><p>' . esc_html__('অবৈধ আবেদন।', 'dpdt-trademark') . '</p></div>';
    return;
}

$db = new DPDT_Database();
$app = $db->get_application($app_id);

if (!$app) {
    echo '<div class="wrap"><p>' . esc_html__('আবেদন পাওয়া যায়নি।', 'dpdt-trademark') . '</p></div>';
    return;
}

// Ensure all properties exist with defaults
$defaults = array(
    'id' => 0, 'application_id' => '', 'applicant_name' => '', 'applicant_name_bn' => '',
    'applicant_email' => '', 'applicant_phone' => '', 'applicant_address' => '',
    'brand_name' => '', 'brand_name_bn' => '', 'trademark_class' => '', 'trademark_type' => 'word',
    'brand_logo_url' => '', 'description' => '', 'owner_name' => '', 'owner_name_bn' => '',
    'company_name' => '', 'application_date' => '', 'registration_date' => '',
    'approved_date' => '', 'expiry_date' => '', 'status' => 'pending',
    'certificate_number' => '', 'certificate_pdf_url' => '', 'certificate_jpg_url' => '',
    'qr_code_url' => '', 'verify_url' => '', 'admin_notes' => '',
    'ip_address' => '', 'created_at' => '', 'updated_at' => '',
);
foreach ($defaults as $key => $default) {
    if (!isset($app->$key)) {
        $app->$key = $default;
    }
}

$qrcode = new DPDT_QRCode();
$qr_url = isset($app->application_id) ? $qrcode->get_qr_url($app->application_id) : '';
?>
<div class="wrap dpdt-edit-application-wrap">
    <h1>
        <?php esc_html_e('আবেদন সম্পাদনা', 'dpdt-trademark'); ?>
        <a href="<?php echo admin_url('admin.php?page=dpdt-applications'); ?>" class="page-title-action"><?php esc_html_e('← তালিকায় ফিরুন', 'dpdt-trademark'); ?></a>
    </h1>

    <div id="dpdt-edit-notice" style="display:none;"></div>

    <form id="dpdt-edit-application-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo intval($app->id); ?>" />
        <?php wp_nonce_field(DPDT_NONCE_ACTION, '_dpdt_nonce'); ?>

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- Main Content -->
                <div id="post-body-content">
                    <!-- Application Info -->
                    <div class="postbox">
                        <h2 class="hndle"><?php esc_html_e('আবেদনকারীর তথ্য', 'dpdt-trademark'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('আবেদন নম্বর', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="application_id" value="<?php echo esc_attr($app->application_id); ?>" class="regular-text" readonly style="background:#f0f0f0;" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('আবেদনকারীর নাম (EN)', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="applicant_name" value="<?php echo esc_attr($app->applicant_name); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('আবেদনকারীর নাম (BN)', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="applicant_name_bn" value="<?php echo esc_attr($app->applicant_name_bn); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ইমেইল', 'dpdt-trademark'); ?></th>
                                    <td><input type="email" name="applicant_email" value="<?php echo esc_attr($app->applicant_email); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ফোন', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="applicant_phone" value="<?php echo esc_attr($app->applicant_phone); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ঠিকানা', 'dpdt-trademark'); ?></th>
                                    <td><textarea name="applicant_address" rows="3" class="large-text"><?php echo esc_textarea($app->applicant_address); ?></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Brand Info -->
                    <div class="postbox">
                        <h2 class="hndle"><?php esc_html_e('ব্র্যান্ড তথ্য', 'dpdt-trademark'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('ব্র্যান্ড নাম (EN)', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="brand_name" value="<?php echo esc_attr($app->brand_name); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ব্র্যান্ড নাম (BN)', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="brand_name_bn" value="<?php echo esc_attr($app->brand_name_bn); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ট্রেডমার্ক শ্রেণী', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="trademark_class" value="<?php echo esc_attr($app->trademark_class); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ট্রেডমার্কের ধরন', 'dpdt-trademark'); ?></th>
                                    <td>
                                        <select name="trademark_type">
                                            <option value="word" <?php selected($app->trademark_type, 'word'); ?>>Word Mark</option>
                                            <option value="device" <?php selected($app->trademark_type, 'device'); ?>>Device Mark</option>
                                            <option value="combined" <?php selected($app->trademark_type, 'combined'); ?>>Combined</option>
                                            <option value="shape" <?php selected($app->trademark_type, 'shape'); ?>>Shape</option>
                                            <option value="color" <?php selected($app->trademark_type, 'color'); ?>>Color</option>
                                            <option value="sound" <?php selected($app->trademark_type, 'sound'); ?>>Sound</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('মালিকের নাম (EN)', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="owner_name" value="<?php echo esc_attr($app->owner_name); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('মালিকের নাম (BN)', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="owner_name_bn" value="<?php echo esc_attr($app->owner_name_bn); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('প্রতিষ্ঠান', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="company_name" value="<?php echo esc_attr($app->company_name); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('বিবরণ', 'dpdt-trademark'); ?></th>
                                    <td><textarea name="description" rows="4" class="large-text"><?php echo esc_textarea($app->description); ?></textarea></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('ব্র্যান্ড লোগো', 'dpdt-trademark'); ?></th>
                                    <td>
                                        <?php if (!empty($app->brand_logo_url)) : ?>
                                            <img src="<?php echo esc_url($app->brand_logo_url); ?>" style="max-width:120px;max-height:120px;display:block;margin-bottom:10px;border:1px solid #ddd;padding:5px;" />
                                        <?php endif; ?>
                                        <input type="text" name="brand_logo_url" value="<?php echo esc_attr($app->brand_logo_url); ?>" class="large-text" placeholder="Logo URL" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Dates & Status -->
                    <div class="postbox">
                        <h2 class="hndle"><?php esc_html_e('তারিখ ও স্ট্যাটাস', 'dpdt-trademark'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('স্ট্যাটাস', 'dpdt-trademark'); ?></th>
                                    <td>
                                        <select name="status">
                                            <option value="pending" <?php selected($app->status, 'pending'); ?>><?php esc_html_e('অপেক্ষমাণ (Pending)', 'dpdt-trademark'); ?></option>
                                            <option value="approved" <?php selected($app->status, 'approved'); ?>><?php esc_html_e('অনুমোদিত (Approved)', 'dpdt-trademark'); ?></option>
                                            <option value="rejected" <?php selected($app->status, 'rejected'); ?>><?php esc_html_e('প্রত্যাখ্যাত (Rejected)', 'dpdt-trademark'); ?></option>
                                            <option value="revoked" <?php selected($app->status, 'revoked'); ?>><?php esc_html_e('বাতিল (Revoked)', 'dpdt-trademark'); ?></option>
                                            <option value="completed" <?php selected($app->status, 'completed'); ?>><?php esc_html_e('সম্পন্ন (Completed)', 'dpdt-trademark'); ?></option>
                                            <option value="active" <?php selected($app->status, 'active'); ?>><?php esc_html_e('সক্রিয় (Active)', 'dpdt-trademark'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('আবেদনের তারিখ', 'dpdt-trademark'); ?></th>
                                    <td><input type="datetime-local" name="application_date" value="<?php echo esc_attr(!empty($app->application_date) ? date('Y-m-d\TH:i', strtotime($app->application_date)) : ''); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('নিবন্ধনের তারিখ (Seal Date)', 'dpdt-trademark'); ?></th>
                                    <td><input type="datetime-local" name="registration_date" value="<?php echo esc_attr(!empty($app->registration_date) ? date('Y-m-d\TH:i', strtotime($app->registration_date)) : ''); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('অনুমোদনের তারিখ', 'dpdt-trademark'); ?></th>
                                    <td><input type="datetime-local" name="approved_date" value="<?php echo esc_attr(!empty($app->approved_date) ? date('Y-m-d\TH:i', strtotime($app->approved_date)) : ''); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('মেয়াদ উত্তীর্ণ তারিখ', 'dpdt-trademark'); ?></th>
                                    <td><input type="datetime-local" name="expiry_date" value="<?php echo esc_attr(!empty($app->expiry_date) ? date('Y-m-d\TH:i', strtotime($app->expiry_date)) : ''); ?>" class="regular-text" /></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Certificate Info -->
                    <div class="postbox">
                        <h2 class="hndle"><?php esc_html_e('সার্টিফিকেট তথ্য', 'dpdt-trademark'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><?php esc_html_e('সার্টিফিকেট নম্বর', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="certificate_number" value="<?php echo esc_attr($app->certificate_number); ?>" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('সার্টিফিকেট JPG আপলোড', 'dpdt-trademark'); ?></th>
                                    <td>
                                        <?php if (!empty($app->certificate_jpg_url)) : ?>
                                            <img src="<?php echo esc_url($app->certificate_jpg_url); ?>" style="max-width:300px;display:block;margin-bottom:10px;border:1px solid #ddd;padding:5px;" />
                                            <p><small>বর্তমান: <?php echo esc_html($app->certificate_jpg_url); ?></small></p>
                                        <?php endif; ?>
                                        <input type="file" name="certificate_jpg" accept=".jpg,.jpeg,.png" />
                                        <p class="description"><?php esc_html_e('নতুন JPG আপলোড করলে আগেরটি প্রতিস্থাপিত হবে।', 'dpdt-trademark'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('সার্টিফিকেট PDF আপলোড', 'dpdt-trademark'); ?></th>
                                    <td>
                                        <?php if (!empty($app->certificate_pdf_url)) : ?>
                                            <p><a href="<?php echo esc_url($app->certificate_pdf_url); ?>" target="_blank"><?php esc_html_e('বর্তমান PDF দেখুন', 'dpdt-trademark'); ?></a></p>
                                        <?php endif; ?>
                                        <input type="file" name="certificate_pdf" accept=".pdf" />
                                        <p class="description"><?php esc_html_e('নতুন PDF আপলোড করলে আগেরটি প্রতিস্থাপিত হবে।', 'dpdt-trademark'); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('যাচাই URL', 'dpdt-trademark'); ?></th>
                                    <td><input type="text" name="verify_url" value="<?php echo esc_attr($app->verify_url); ?>" class="large-text" /></td>
                                </tr>
                                <tr>
                                    <th><?php esc_html_e('প্রশাসক মন্তব্য', 'dpdt-trademark'); ?></th>
                                    <td><textarea name="admin_notes" rows="3" class="large-text"><?php echo esc_textarea($app->admin_notes); ?></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="postbox">
                        <h2 class="hndle"><?php esc_html_e('QR কোড', 'dpdt-trademark'); ?></h2>
                        <div class="inside">
                            <?php if (!empty($qr_url)) : ?>
                                <p><img src="<?php echo esc_url($qr_url); ?>" style="max-width:200px;" /></p>
                                <p><small><?php echo esc_html($qr_url); ?></small></p>
                            <?php else : ?>
                                <p><?php esc_html_e('QR কোড এখনো তৈরি হয়নি।', 'dpdt-trademark'); ?></p>
                            <?php endif; ?>
                            <button type="button" class="button" id="dpdt-generate-qr" data-app-id="<?php echo esc_attr($app->application_id); ?>"><?php esc_html_e('QR কোড তৈরি/আপডেট করুন', 'dpdt-trademark'); ?></button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div id="postbox-container-1" class="postbox-container" style="width:35%;">
                    <div class="postbox">
                        <h2 class="hndle"><?php esc_html_e('প্রকাশ', 'dpdt-trademark'); ?></h2>
                        <div class="inside">
                            <div class="submitbox">
                                <p><strong><?php esc_html_e('তৈরি:', 'dpdt-trademark'); ?></strong> <?php echo esc_html(date_i18n('d/m/Y h:i A', strtotime($app->created_at))); ?></p>
                                <p><strong><?php esc_html_e('আপডেট:', 'dpdt-trademark'); ?></strong> <?php echo esc_html(date_i18n('d/m/Y h:i A', strtotime($app->updated_at))); ?></p>
                                <p><strong>IP:</strong> <?php echo esc_html($app->ip_address); ?></p>
                                <hr>
                                <p>
                                    <button type="submit" class="button button-primary button-large" id="dpdt-save-application"><?php esc_html_e('আপডেট সংরক্ষণ করুন', 'dpdt-trademark'); ?></button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Save application
    $('#dpdt-edit-application-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'dpdt_admin_update_application');

        $('#dpdt-save-application').prop('disabled', true).text('সংরক্ষণ হচ্ছে...');

        $.ajax({
            url: dpdtAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#dpdt-edit-notice').html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>').show();
                    window.scrollTo(0, 0);
                } else {
                    $('#dpdt-edit-notice').html('<div class="notice notice-error"><p>' + (response.data.message || 'ত্রুটি হয়েছে') + '</p></div>').show();
                }
            },
            error: function() {
                $('#dpdt-edit-notice').html('<div class="notice notice-error"><p>সার্ভার ত্রুটি</p></div>').show();
            },
            complete: function() {
                $('#dpdt-save-application').prop('disabled', false).text('আপডেট সংরক্ষণ করুন');
            }
        });
    });

    // Generate QR
    $('#dpdt-generate-qr').on('click', function() {
        var appId = $(this).data('app-id');
        var $btn = $(this);
        $btn.prop('disabled', true).text('তৈরি হচ্ছে...');

        $.post(dpdtAdmin.ajaxurl, {
            action: 'dpdt_admin_generate_qr',
            application_id: appId,
            nonce: dpdtAdmin.nonce
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data.message || 'QR তৈরি ব্যর্থ');
            }
            $btn.prop('disabled', false).text('QR কোড তৈরি/আপডেট করুন');
        });
    });
});
</script>
