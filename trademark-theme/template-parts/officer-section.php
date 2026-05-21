<?php
if (!defined('ABSPATH')) exit;
?>
<section class="officer-section">
    <div class="section-card">
        <div class="section-card-header">
            <h2><span class="dashicons dashicons-businessman"></span> <?php esc_html_e('মহাপরিচালক', 'dpdt-theme'); ?></h2>
        </div>
        <div class="section-card-body officer-profile">
            <div class="officer-photo">
                <?php
                $officer_photo = get_theme_mod('dpdt_officer_photo', '');
                if ($officer_photo) :
                ?>
                    <img src="<?php echo esc_url($officer_photo); ?>" alt="<?php esc_attr_e('মহাপরিচালক', 'dpdt-theme'); ?>" />
                <?php else : ?>
                    <div class="officer-photo-placeholder">
                        <span class="dashicons dashicons-businessman"></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="officer-info">
                <h3 class="officer-name"><?php echo esc_html(get_theme_mod('dpdt_officer_name', 'মো. মাসুদ আহমেদ')); ?></h3>
                <p class="officer-designation"><?php echo esc_html(get_theme_mod('dpdt_officer_designation', 'মহাপরিচালক')); ?></p>
                <p class="officer-dept"><?php echo esc_html(get_option('dpdt_site_name', 'DPDT')); ?></p>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <?php
    $activities = get_posts(array(
        'post_type' => 'dpdt_activity',
        'posts_per_page' => 5,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
    if (!empty($activities)) :
    ?>
    <div class="section-card" style="margin-top: 20px;">
        <div class="section-card-header">
            <h2><span class="dashicons dashicons-calendar-alt"></span> <?php esc_html_e('সাম্প্রতিক কার্যক্রম', 'dpdt-theme'); ?></h2>
        </div>
        <div class="section-card-body">
            <ul class="activity-list">
                <?php foreach ($activities as $activity) : ?>
                    <li>
                        <a href="<?php echo get_permalink($activity->ID); ?>">
                            <?php echo esc_html($activity->post_title); ?>
                        </a>
                        <small><?php echo get_the_date('d M Y', $activity); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</section>
