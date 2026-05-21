<?php
if (!defined('ABSPATH')) exit;

$notices = get_posts(array(
    'post_type' => 'dpdt_notice',
    'posts_per_page' => 8,
    'orderby' => 'date',
    'order' => 'DESC',
));
?>
<section class="notice-board-section">
    <div class="section-card">
        <div class="section-card-header">
            <h2><span class="dashicons dashicons-megaphone"></span> <?php esc_html_e('নোটিশ বোর্ড', 'dpdt-theme'); ?></h2>
            <a href="<?php echo get_post_type_archive_link('dpdt_notice'); ?>" class="view-all-link"><?php esc_html_e('সব দেখুন', 'dpdt-theme'); ?> →</a>
        </div>
        <div class="section-card-body">
            <?php if (!empty($notices)) : ?>
                <ul class="notice-list">
                    <?php foreach ($notices as $notice) : ?>
                        <li class="notice-item">
                            <a href="<?php echo get_permalink($notice->ID); ?>">
                                <span class="notice-date"><?php echo get_the_date('d M Y', $notice); ?></span>
                                <span class="notice-title"><?php echo esc_html($notice->post_title); ?></span>
                            </a>
                            <?php if (strtotime($notice->post_date) > strtotime('-7 days')) : ?>
                                <span class="notice-badge-new"><?php esc_html_e('নতুন', 'dpdt-theme'); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="no-notices"><?php esc_html_e('বর্তমানে কোনো নোটিশ নেই।', 'dpdt-theme'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
