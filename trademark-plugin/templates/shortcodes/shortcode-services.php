<?php
if (!defined('ABSPATH')) exit;

$services = array(
    array(
        'icon' => 'dashicons-awards',
        'title' => __('ট্রেডমার্ক নিবন্ধন', 'dpdt-trademark'),
        'description' => __('আপনার ব্র্যান্ড ও ট্রেডমার্ক নিবন্ধন করুন এবং আইনি সুরক্ষা পান।', 'dpdt-trademark'),
        'link' => home_url('/services/trademark-registration/'),
    ),
    array(
        'icon' => 'dashicons-lightbulb',
        'title' => __('পেটেন্ট নিবন্ধন', 'dpdt-trademark'),
        'description' => __('আপনার উদ্ভাবন ও আবিষ্কারের পেটেন্ট নিবন্ধন করুন।', 'dpdt-trademark'),
        'link' => home_url('/services/patent-registration/'),
    ),
    array(
        'icon' => 'dashicons-art',
        'title' => __('ডিজাইন নিবন্ধন', 'dpdt-trademark'),
        'description' => __('আপনার শিল্পনকশা ও ডিজাইন নিবন্ধন করে সুরক্ষিত করুন।', 'dpdt-trademark'),
        'link' => home_url('/services/design-registration/'),
    ),
    array(
        'icon' => 'dashicons-shield',
        'title' => __('সার্টিফিকেট যাচাই', 'dpdt-trademark'),
        'description' => __('QR কোড বা সার্টিফিকেট নম্বর দিয়ে সার্টিফিকেটের বৈধতা যাচাই করুন।', 'dpdt-trademark'),
        'link' => home_url('/verify/'),
    ),
    array(
        'icon' => 'dashicons-search',
        'title' => __('ট্রেডমার্ক অনুসন্ধান', 'dpdt-trademark'),
        'description' => __('বিদ্যমান ট্রেডমার্ক ডাটাবেসে অনুসন্ধান করুন।', 'dpdt-trademark'),
        'link' => home_url('/database/trademark-search/'),
    ),
    array(
        'icon' => 'dashicons-media-text',
        'title' => __('অনলাইন আবেদন', 'dpdt-trademark'),
        'description' => __('অনলাইনে ট্রেডমার্ক সার্টিফিকেটের জন্য আবেদন করুন।', 'dpdt-trademark'),
        'link' => home_url('/apply/'),
    ),
);
?>
<div class="dpdt-services-grid">
    <?php foreach ($services as $service) : ?>
        <div class="dpdt-service-card">
            <div class="dpdt-service-icon">
                <span class="dashicons <?php echo esc_attr($service['icon']); ?>"></span>
            </div>
            <h3 class="dpdt-service-title"><?php echo esc_html($service['title']); ?></h3>
            <p class="dpdt-service-desc"><?php echo esc_html($service['description']); ?></p>
            <a href="<?php echo esc_url($service['link']); ?>" class="dpdt-service-link">
                <?php esc_html_e('বিস্তারিত দেখুন', 'dpdt-trademark'); ?> →
            </a>
        </div>
    <?php endforeach; ?>
</div>
