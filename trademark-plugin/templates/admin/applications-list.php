<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap dpdt-admin-wrap">
    <h1><?php echo esc_html($page_title); ?></h1>
    <div class="dpdt-table-wrap">
        <table class="wp-list-table widefat fixed striped dpdt-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>App Code</th>
                    <th>Brand</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                <tr><td colspan="8" style="text-align:center;padding:20px;">No applications found.</td></tr>
                <?php else: ?>
                <?php foreach ($applications as $app): ?>
                <tr>
                    <td><?php echo esc_html($app->id); ?></td>
                    <td><code><?php echo esc_html($app->app_code); ?></code></td>
                    <td><strong><?php echo esc_html($app->brand_name); ?></strong></td>
                    <td><?php echo esc_html($app->owner_name); ?></td>
                    <td><?php echo esc_html($app->owner_email); ?></td>
                    <td><span class="dpdt-status dpdt-status-<?php echo esc_attr($app->status); ?>"><?php echo esc_html(ucfirst($app->status)); ?></span></td>
                    <td><?php echo esc_html(date('d/m/Y', strtotime($app->application_date))); ?></td>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=dpdt-trademark'); ?>" class="button button-small">Manage</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
