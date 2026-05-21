<?php
if (!defined('ABSPATH')) exit;

$security = new DPDT_Security();
$classes = DPDT_Application::get_trademark_classes();
?>
<div class="dpdt-apply-form-wrapper">
    <?php if ($atts['show_title'] === 'yes') : ?>
        <h2 class="dpdt-form-title"><?php echo esc_html($atts['title']); ?></h2>
    <?php endif; ?>

    <div class="dpdt-form-notice dpdt-notice-info">
        <span class="dashicons dashicons-info"></span>
        <p><?php esc_html_e('* চিহ্নিত ঘরগুলো অবশ্যই পূরণ করতে হবে।', 'dpdt-trademark'); ?></p>
    </div>

    <form id="dpdt-application-form" class="dpdt-form" method="post" enctype="multipart/form-data" novalidate>
        <?php echo $security->get_nonce_field(); ?>
        <?php echo $security->get_honeypot_field(); ?>

        <!-- Applicant Information -->
        <fieldset class="dpdt-fieldset">
            <legend><?php esc_html_e('আবেদনকারীর তথ্য', 'dpdt-trademark'); ?></legend>

            <div class="dpdt-form-row dpdt-form-row-2">
                <div class="dpdt-form-group">
                    <label for="applicant_name"><?php esc_html_e('আবেদনকারীর নাম (English)', 'dpdt-trademark'); ?> <span class="required">*</span></label>
                    <input type="text" id="applicant_name" name="applicant_name" required placeholder="Full Name in English" />
                </div>
                <div class="dpdt-form-group">
                    <label for="applicant_name_bn"><?php esc_html_e('আবেদনকারীর নাম (বাংলা)', 'dpdt-trademark'); ?></label>
                    <input type="text" id="applicant_name_bn" name="applicant_name_bn" placeholder="পূর্ণ নাম বাংলায়" />
                </div>
            </div>

            <div class="dpdt-form-row dpdt-form-row-2">
                <div class="dpdt-form-group">
                    <label for="applicant_email"><?php esc_html_e('ইমেইল ঠিকানা', 'dpdt-trademark'); ?> <span class="required">*</span></label>
                    <input type="email" id="applicant_email" name="applicant_email" required placeholder="email@example.com" />
                </div>
                <div class="dpdt-form-group">
                    <label for="applicant_phone"><?php esc_html_e('মোবাইল নম্বর', 'dpdt-trademark'); ?> <span class="required">*</span></label>
                    <input type="tel" id="applicant_phone" name="applicant_phone" required placeholder="+880 1XXX-XXXXXX" />
                </div>
            </div>

            <div class="dpdt-form-group">
                <label for="applicant_address"><?php esc_html_e('ঠিকানা', 'dpdt-trademark'); ?></label>
                <textarea id="applicant_address" name="applicant_address" rows="3" placeholder="সম্পূর্ণ ঠিকানা লিখুন"></textarea>
            </div>
        </fieldset>

        <!-- Brand/Trademark Information -->
        <fieldset class="dpdt-fieldset">
            <legend><?php esc_html_e('ব্র্যান্ড/ট্রেডমার্ক তথ্য', 'dpdt-trademark'); ?></legend>

            <div class="dpdt-form-row dpdt-form-row-2">
                <div class="dpdt-form-group">
                    <label for="brand_name"><?php esc_html_e('ব্র্যান্ডের নাম (English)', 'dpdt-trademark'); ?> <span class="required">*</span></label>
                    <input type="text" id="brand_name" name="brand_name" required placeholder="Brand Name" />
                </div>
                <div class="dpdt-form-group">
                    <label for="brand_name_bn"><?php esc_html_e('ব্র্যান্ডের নাম (বাংলা)', 'dpdt-trademark'); ?></label>
                    <input type="text" id="brand_name_bn" name="brand_name_bn" placeholder="ব্র্যান্ডের নাম বাংলায়" />
                </div>
            </div>

            <div class="dpdt-form-row dpdt-form-row-2">
                <div class="dpdt-form-group">
                    <label for="trademark_class"><?php esc_html_e('ট্রেডমার্ক শ্রেণী', 'dpdt-trademark'); ?> <span class="required">*</span></label>
                    <select id="trademark_class" name="trademark_class" required>
                        <option value=""><?php esc_html_e('— শ্রেণী নির্বাচন করুন —', 'dpdt-trademark'); ?></option>
                        <?php foreach ($classes as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dpdt-form-group">
                    <label for="trademark_type"><?php esc_html_e('ট্রেডমার্কের ধরন', 'dpdt-trademark'); ?></label>
                    <select id="trademark_type" name="trademark_type">
                        <option value="word"><?php esc_html_e('শব্দ (Word Mark)', 'dpdt-trademark'); ?></option>
                        <option value="device"><?php esc_html_e('ডিভাইস (Device Mark)', 'dpdt-trademark'); ?></option>
                        <option value="combined"><?php esc_html_e('সম্মিলিত (Combined)', 'dpdt-trademark'); ?></option>
                        <option value="shape"><?php esc_html_e('আকৃতি (Shape)', 'dpdt-trademark'); ?></option>
                        <option value="color"><?php esc_html_e('রঙ (Color)', 'dpdt-trademark'); ?></option>
                        <option value="sound"><?php esc_html_e('শব্দ (Sound)', 'dpdt-trademark'); ?></option>
                    </select>
                </div>
            </div>

            <div class="dpdt-form-group">
                <label for="brand_logo"><?php esc_html_e('ব্র্যান্ড লোগো', 'dpdt-trademark'); ?></label>
                <div class="dpdt-file-upload">
                    <input type="file" id="brand_logo" name="brand_logo" accept="image/jpeg,image/png,image/gif,image/svg+xml" />
                    <p class="description"><?php esc_html_e('অনুমোদিত: JPG, PNG, GIF, SVG। সর্বোচ্চ সাইজ: 2MB', 'dpdt-trademark'); ?></p>
                </div>
                <div id="brand-logo-preview" class="dpdt-logo-preview-area"></div>
            </div>

            <div class="dpdt-form-group">
                <label for="description"><?php esc_html_e('পণ্য/সেবার বর্ণনা', 'dpdt-trademark'); ?></label>
                <textarea id="description" name="description" rows="4" placeholder="আপনার পণ্য বা সেবার বিস্তারিত বর্ণনা দিন"></textarea>
            </div>
        </fieldset>

        <!-- Owner Information -->
        <fieldset class="dpdt-fieldset">
            <legend><?php esc_html_e('মালিকের তথ্য', 'dpdt-trademark'); ?></legend>

            <div class="dpdt-form-row dpdt-form-row-2">
                <div class="dpdt-form-group">
                    <label for="owner_name"><?php esc_html_e('মালিকের নাম (English)', 'dpdt-trademark'); ?></label>
                    <input type="text" id="owner_name" name="owner_name" placeholder="Owner Name" />
                </div>
                <div class="dpdt-form-group">
                    <label for="owner_name_bn"><?php esc_html_e('মালিকের নাম (বাংলা)', 'dpdt-trademark'); ?></label>
                    <input type="text" id="owner_name_bn" name="owner_name_bn" placeholder="মালিকের নাম বাংলায়" />
                </div>
            </div>

            <div class="dpdt-form-group">
                <label for="company_name"><?php esc_html_e('প্রতিষ্ঠানের নাম', 'dpdt-trademark'); ?></label>
                <input type="text" id="company_name" name="company_name" placeholder="Company/Organization Name" />
            </div>
        </fieldset>

        <!-- Submit -->
        <div class="dpdt-form-submit">
            <button type="submit" id="dpdt-submit-btn" class="dpdt-btn dpdt-btn-primary dpdt-btn-large">
                <span class="dpdt-btn-text"><?php esc_html_e('আবেদন জমা দিন', 'dpdt-trademark'); ?></span>
                <span class="dpdt-btn-loading" style="display:none;">
                    <span class="dpdt-spinner"></span> <?php esc_html_e('জমা হচ্ছে...', 'dpdt-trademark'); ?>
                </span>
            </button>
            <p class="dpdt-form-disclaimer"><?php esc_html_e('আবেদন জমা দেওয়ার মাধ্যমে আপনি আমাদের শর্তাবলী মেনে নিচ্ছেন।', 'dpdt-trademark'); ?></p>
        </div>
    </form>

    <!-- Success Message (hidden by default) -->
    <div id="dpdt-form-success" class="dpdt-form-result dpdt-success" style="display:none;">
        <div class="dpdt-success-icon">✓</div>
        <h3><?php esc_html_e('আবেদন সফল!', 'dpdt-trademark'); ?></h3>
        <p id="dpdt-success-message"></p>
        <p id="dpdt-success-details"></p>
    </div>
</div>
