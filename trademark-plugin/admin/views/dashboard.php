<?php
if (!defined('ABSPATH')) exit;
?>
<div class="wrap dpdt-dashboard-wrap">
    <h1><span class="dashicons dashicons-awards"></span> <?php esc_html_e('ট্রেডমার্ক সার্টিফিকেট ড্যাশবোর্ড', 'dpdt-trademark'); ?></h1>

    <div class="dpdt-dashboard-header">
        <p class="dpdt-version"><?php printf(__('সংস্করণ: %s', 'dpdt-trademark'), DPDT_VERSION); ?> | <?php printf(__('প্রতিষ্ঠিত: %s', 'dpdt-trademark'), get_option('dpdt_site_established', '২০০৯')); ?></p>
    </div>

    <!-- Statistics Cards -->
    <div class="dpdt-stats-grid">
        <div class="dpdt-stat-card dpdt-stat-total">
            <div class="dpdt-stat-icon"><span class="dashicons dashicons-clipboard"></span></div>
            <div class="dpdt-stat-content">
                <h3><?php echo intval($stats['total']); ?></h3>
                <p><?php esc_html_e('মোট আবেদন', 'dpdt-trademark'); ?></p>
            </div>
        </div>

        <div class="dpdt-stat-card dpdt-stat-pending">
            <div class="dpdt-stat-icon"><span class="dashicons dashicons-clock"></span></div>
            <div class="dpdt-stat-content">
                <h3><?php echo intval($stats['pending']); ?></h3>
                <p><?php esc_html_e('অপেক্ষমাণ', 'dpdt-trademark'); ?></p>
            </div>
        </div>

        <div class="dpdt-stat-card dpdt-stat-approved">
            <div class="dpdt-stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
            <div class="dpdt-stat-content">
                <h3><?php echo intval($stats['approved']); ?></h3>
                <p><?php esc_html_e('অনুমোদিত', 'dpdt-trademark'); ?></p>
            </div>
        </div>

        <div class="dpdt-stat-card dpdt-stat-rejected">
            <div class="dpdt-stat-icon"><span class="dashicons dashicons-dismiss"></span></div>
            <div class="dpdt-stat-content">
                <h3><?php echo intval($stats['rejected']); ?></h3>
                <p><?php esc_html_e('প্রত্যাখ্যাত', 'dpdt-trademark'); ?></p>
            </div>
        </div>

        <div class="dpdt-stat-card dpdt-stat-month">
            <div class="dpdt-stat-icon"><span class="dashicons dashicons-calendar"></span></div>
            <div class="dpdt-stat-content">
                <h3><?php echo intval($stats['this_month']); ?></h3>
                <p><?php esc_html_e('এই মাসে', 'dpdt-trademark'); ?></p>
            </div>
        </div>

        <div class="dpdt-stat-card dpdt-stat-year">
            <div class="dpdt-stat-icon"><span class="dashicons dashicons-chart-bar"></span></div>
            <div class="dpdt-stat-content">
                <h3><?php echo intval($stats['this_year']); ?></h3>
                <p><?php esc_html_e('এই বছরে', 'dpdt-trademark'); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="dpdt-dashboard-section">
        <h2><?php esc_html_e('দ্রুত কার্যক্রম', 'dpdt-trademark'); ?></h2>
        <div class="dpdt-quick-actions">
            <a href="<?php echo admin_url('admin.php?page=dpdt-applications'); ?>" class="button button-primary button-large">
                <span class="dashicons dashicons-list-view"></span> <?php esc_html_e('আবেদন দেখুন', 'dpdt-trademark'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=dpdt-applications&status=pending'); ?>" class="button button-secondary button-large">
                <span class="dashicons dashicons-clock"></span> <?php esc_html_e('অপেক্ষমাণ আবেদন', 'dpdt-trademark'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=dpdt-settings'); ?>" class="button button-secondary button-large">
                <span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e('সেটিংস', 'dpdt-trademark'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=dpdt-logo-settings'); ?>" class="button button-secondary button-large">
                <span class="dashicons dashicons-format-image"></span> <?php esc_html_e('লোগো সেটিংস', 'dpdt-trademark'); ?>
            </a>
            <a href="<?php echo admin_url('nav-menus.php'); ?>" class="button button-secondary button-large">
                <span class="dashicons dashicons-menu"></span> <?php esc_html_e('মেনু ম্যানেজ', 'dpdt-trademark'); ?>
            </a>
        </div>
    </div>

    <!-- Recent Applications -->
    <div class="dpdt-dashboard-section">
        <h2><?php esc_html_e('সাম্প্রতিক আবেদন', 'dpdt-trademark'); ?></h2>
        <?php if (!empty($recent_applications)) : ?>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('আবেদন নম্বর', 'dpdt-trademark'); ?></th>
                        <th><?php esc_html_e('আবেদনকারী', 'dpdt-trademark'); ?></th>
                        <th><?php esc_html_e('ব্র্যান্ড', 'dpdt-trademark'); ?></th>
                        <th><?php esc_html_e('স্ট্যাটাস', 'dpdt-trademark'); ?></th>
                        <th><?php esc_html_e('তারিখ', 'dpdt-trademark'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_applications as $app) : ?>
                        <tr>
                            <td><strong><?php echo esc_html($app->application_id); ?></strong></td>
                            <td><?php echo esc_html($app->applicant_name); ?></td>
                            <td><?php echo esc_html($app->brand_name); ?></td>
                            <td>
                                <span class="dpdt-status dpdt-status-<?php echo esc_attr($app->status); ?>">
                                    <?php echo esc_html(ucfirst($app->status)); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html(date_i18n('d/m/Y', strtotime($app->created_at))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p class="dpdt-no-data"><?php esc_html_e('কোনো আবেদন পাওয়া যায়নি।', 'dpdt-trademark'); ?></p>
        <?php endif; ?>
    </div>

    <!-- System Info -->
    <div class="dpdt-dashboard-section">
        <h2><?php esc_html_e('সিস্টেম তথ্য', 'dpdt-trademark'); ?></h2>
        <table class="widefat">
            <tr><td><strong>Plugin Version</strong></td><td><?php echo DPDT_VERSION; ?></td></tr>
            <tr><td><strong>WordPress Version</strong></td><td><?php echo get_bloginfo('version'); ?></td></tr>
            <tr><td><strong>PHP Version</strong></td><td><?php echo PHP_VERSION; ?></td></tr>
            <tr><td><strong>Database Version</strong></td><td><?php echo get_option('dpdt_db_version', 'N/A'); ?></td></tr>
            <tr><td><strong>Active Theme</strong></td><td><?php echo wp_get_theme()->get('Name'); ?></td></tr>
        </table>
    </div>
</div>
