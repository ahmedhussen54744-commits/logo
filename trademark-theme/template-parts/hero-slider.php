<?php
if (!defined('ABSPATH')) exit;

$slides = array();
for ($i = 1; $i <= 5; $i++) {
    $image = get_theme_mod("dpdt_slider_image_$i", '');
    $title = get_theme_mod("dpdt_slider_title_$i", '');
    $desc = get_theme_mod("dpdt_slider_desc_$i", '');
    $link = get_theme_mod("dpdt_slider_link_$i", '');

    if ($image || $title) {
        $slides[] = array(
            'image' => $image,
            'title' => $title,
            'description' => $desc,
            'link' => $link,
        );
    }
}

// Default slides if none configured
if (empty($slides)) {
    $slides = array(
        array(
            'image' => '',
            'title' => get_option('dpdt_site_name', 'পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর'),
            'description' => 'বাংলাদেশের বৌদ্ধিক সম্পদ সুরক্ষায় আমরা প্রতিশ্রুতিবদ্ধ',
            'link' => home_url('/about-us/'),
        ),
        array(
            'image' => '',
            'title' => 'অনলাইনে ট্রেডমার্ক নিবন্ধন করুন',
            'description' => 'সহজে এবং দ্রুত আপনার ব্র্যান্ড নিবন্ধন করুন',
            'link' => home_url('/apply/'),
        ),
        array(
            'image' => '',
            'title' => 'সার্টিফিকেট যাচাই করুন',
            'description' => 'QR কোড বা সার্টিফিকেট নম্বর দিয়ে যাচাই করুন',
            'link' => home_url('/verify/'),
        ),
    );
}
?>
<section class="hero-slider-section">
    <div class="hero-slider" id="hero-slider">
        <?php foreach ($slides as $index => $slide) : ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"
                 <?php if ($slide['image']) : ?>style="background-image: url(<?php echo esc_url($slide['image']); ?>);"<?php endif; ?>>
                <div class="slide-overlay"></div>
                <div class="slide-content container">
                    <h2 class="slide-title"><?php echo esc_html($slide['title']); ?></h2>
                    <?php if ($slide['description']) : ?>
                        <p class="slide-description"><?php echo esc_html($slide['description']); ?></p>
                    <?php endif; ?>
                    <?php if ($slide['link']) : ?>
                        <a href="<?php echo esc_url($slide['link']); ?>" class="slide-btn"><?php esc_html_e('বিস্তারিত', 'dpdt-theme'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Slider Controls -->
        <button class="slider-prev" aria-label="Previous"><span>&lsaquo;</span></button>
        <button class="slider-next" aria-label="Next"><span>&rsaquo;</span></button>

        <!-- Dots -->
        <div class="slider-dots">
            <?php foreach ($slides as $index => $slide) : ?>
                <button class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
</section>
