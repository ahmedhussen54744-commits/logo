<?php
if (!defined('ABSPATH')) exit;

$links = array(
    array('title' => 'প্রধানমন্ত্রীর কার্যালয়', 'url' => 'https://pmo.gov.bd', 'icon' => 'dashicons-admin-multisite'),
    array('title' => 'শিল্প মন্ত্রণালয়', 'url' => 'https://moind.gov.bd', 'icon' => 'dashicons-building'),
    array('title' => 'বাংলাদেশ জাতীয় তথ্য বাতায়ন', 'url' => 'https://bangladesh.gov.bd', 'icon' => 'dashicons-admin-site-alt3'),
    array('title' => 'জাতীয় ই-তথ্যকোষ', 'url' => 'https://infokosh.gov.bd', 'icon' => 'dashicons-database'),
    array('title' => 'ফরম ও ডাউনলোড', 'url' => home_url('/forms-download/'), 'icon' => 'dashicons-download'),
    array('title' => 'WIPO', 'url' => 'https://www.wipo.int', 'icon' => 'dashicons-networking'),
);
?>
<section class="important-links-section">
    <div class="container">
        <div class="section-header">
            <h2><?php esc_html_e('গুরুত্বপূর্ণ লিংক', 'dpdt-theme'); ?></h2>
        </div>
        <div class="links-grid">
            <?php foreach ($links as $link) : ?>
                <a href="<?php echo esc_url($link['url']); ?>" class="link-card" target="_blank" rel="noopener">
                    <span class="dashicons <?php echo esc_attr($link['icon']); ?>"></span>
                    <span class="link-title"><?php echo esc_html($link['title']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
