<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap dpdt-admin-wrap">
    <h1><span class="dashicons dashicons-admin-generic"></span> DPDT Trademark Settings</h1>
    
    <form method="POST" action="">
        <?php wp_nonce_field('dpdt_settings_save'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="verify_base_url">Verify Base URL</label></th>
                <td>
                    <input type="url" name="verify_base_url" id="verify_base_url" class="regular-text" 
                           value="<?php echo esc_attr(get_option('dpdt_verify_base_url', home_url('/verify/'))); ?>">
                    <p class="description">The base URL for certificate verification. You can change this anytime.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="certificate_prefix">Certificate Prefix</label></th>
                <td>
                    <input type="text" name="certificate_prefix" id="certificate_prefix" class="regular-text" 
                           value="<?php echo esc_attr(get_option('dpdt_certificate_prefix', 'DPDT')); ?>">
                    <p class="description">Prefix for generated application codes (e.g., DPDT2026XXXX)</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="site_established">Site Established Year</label></th>
                <td>
                    <input type="text" name="site_established" id="site_established" class="regular-text" 
                           value="<?php echo esc_attr(get_option('dpdt_site_established', '2009')); ?>">
                    <p class="description">Year shown in footer as establishment year.</p>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="dpdt_save_settings" class="button-primary" value="Save Settings">
        </p>
    </form>
    
    <hr>
    <h2>System Information</h2>
    <table class="form-table">
        <tr><th>Plugin Version</th><td><?php echo DPDT_VERSION; ?></td></tr>
        <tr><th>WordPress Version</th><td><?php echo get_bloginfo('version'); ?></td></tr>
        <tr><th>PHP Version</th><td><?php echo phpversion(); ?></td></tr>
        <tr><th>Database Table</th><td><code><?php echo DPDT_Database::get_table_name(); ?></code></td></tr>
        <tr><th>Total Applications</th><td><?php echo DPDT_Application::count_applications(); ?></td></tr>
        <tr><th>Since</th><td>2009</td></tr>
    </table>
</div>
