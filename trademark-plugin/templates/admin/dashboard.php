<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap dpdt-admin-wrap">
    <h1><span class="dashicons dashicons-awards"></span> Trademark Certificate Dashboard</h1>
    
    <!-- Stats Cards -->
    <div class="dpdt-stats-row">
        <div class="dpdt-stat-card total">
            <div class="stat-icon"><span class="dashicons dashicons-portfolio"></span></div>
            <div class="stat-info">
                <span class="stat-num"><?php echo esc_html($total); ?></span>
                <span class="stat-label">Total Applications</span>
            </div>
        </div>
        <div class="dpdt-stat-card pending">
            <div class="stat-icon"><span class="dashicons dashicons-clock"></span></div>
            <div class="stat-info">
                <span class="stat-num"><?php echo esc_html($pending); ?></span>
                <span class="stat-label">Pending</span>
            </div>
        </div>
        <div class="dpdt-stat-card approved">
            <div class="stat-icon"><span class="dashicons dashicons-yes-alt"></span></div>
            <div class="stat-info">
                <span class="stat-num"><?php echo esc_html($approved); ?></span>
                <span class="stat-label">Approved</span>
            </div>
        </div>
        <div class="dpdt-stat-card rejected">
            <div class="stat-icon"><span class="dashicons dashicons-dismiss"></span></div>
            <div class="stat-info">
                <span class="stat-num"><?php echo esc_html($rejected); ?></span>
                <span class="stat-label">Rejected</span>
            </div>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="dpdt-table-wrap">
        <h2>All Applications</h2>
        <table class="wp-list-table widefat fixed striped dpdt-table">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="12%">App Code</th>
                    <th width="12%">Brand Name</th>
                    <th width="12%">Owner</th>
                    <th width="8%">Type</th>
                    <th width="8%">Class</th>
                    <th width="8%">Status</th>
                    <th width="10%">Applied</th>
                    <th width="25%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($applications)): ?>
                <tr><td colspan="9" style="text-align:center;padding:30px;">No applications found.</td></tr>
                <?php else: ?>
                <?php foreach ($applications as $app): ?>
                <tr data-id="<?php echo esc_attr($app->id); ?>">
                    <td><?php echo esc_html($app->id); ?></td>
                    <td><code><?php echo esc_html($app->app_code); ?></code></td>
                    <td><strong><?php echo esc_html($app->brand_name); ?></strong></td>
                    <td><?php echo esc_html($app->owner_name); ?></td>
                    <td><?php echo esc_html($app->trademark_type); ?></td>
                    <td><?php echo esc_html($app->trademark_class); ?></td>
                    <td>
                        <span class="dpdt-status dpdt-status-<?php echo esc_attr($app->status); ?>">
                            <?php echo esc_html(ucfirst($app->status)); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html(date('d/m/Y', strtotime($app->application_date))); ?></td>
                    <td>
                        <button class="button button-small dpdt-view-btn" data-id="<?php echo esc_attr($app->id); ?>">
                            <span class="dashicons dashicons-visibility"></span> View
                        </button>
                        <?php if ($app->status === 'pending'): ?>
                        <button class="button button-small button-primary dpdt-approve-btn" data-id="<?php echo esc_attr($app->id); ?>">
                            <span class="dashicons dashicons-yes"></span> Approve
                        </button>
                        <button class="button button-small dpdt-reject-btn" data-id="<?php echo esc_attr($app->id); ?>">
                            <span class="dashicons dashicons-no"></span> Reject
                        </button>
                        <?php endif; ?>
                        <button class="button button-small dpdt-edit-btn" data-id="<?php echo esc_attr($app->id); ?>">
                            <span class="dashicons dashicons-edit"></span> Edit
                        </button>
                        <button class="button button-small dpdt-upload-btn" data-id="<?php echo esc_attr($app->id); ?>">
                            <span class="dashicons dashicons-upload"></span> Upload Cert
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div class="dpdt-modal" id="dpdtEditModal" style="display:none;">
    <div class="dpdt-modal-content">
        <div class="dpdt-modal-header">
            <h3><span class="dashicons dashicons-edit"></span> Edit Application</h3>
            <button class="dpdt-modal-close">&times;</button>
        </div>
        <div class="dpdt-modal-body">
            <input type="hidden" id="edit_app_id">
            <div class="dpdt-form-grid">
                <div class="dpdt-form-group">
                    <label>Registration Number</label>
                    <input type="text" id="edit_reg_number" placeholder="e.g., TM-2026-XXXXX">
                </div>
                <div class="dpdt-form-group">
                    <label>Application Date</label>
                    <input type="datetime-local" id="edit_app_date">
                </div>
                <div class="dpdt-form-group">
                    <label>Registration Date</label>
                    <input type="datetime-local" id="edit_reg_date">
                </div>
                <div class="dpdt-form-group">
                    <label>Approved Date</label>
                    <input type="datetime-local" id="edit_approved_date">
                </div>
                <div class="dpdt-form-group">
                    <label>Expiry Date</label>
                    <input type="datetime-local" id="edit_expiry_date">
                </div>
                <div class="dpdt-form-group">
                    <label>Status</label>
                    <select id="edit_status">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="dpdt-form-group full">
                    <label>Verify URL (changeable)</label>
                    <input type="url" id="edit_verify_url" placeholder="Full verification URL">
                </div>
                <div class="dpdt-form-group full">
                    <label>Custom QR Code URL (changeable)</label>
                    <input type="url" id="edit_qr_url" placeholder="Custom QR code image URL or leave blank for auto">
                </div>
                <div class="dpdt-form-group full">
                    <label>Admin Notes</label>
                    <textarea id="edit_admin_notes" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="dpdt-modal-footer">
            <button class="button button-primary" id="saveEditBtn">Save Changes</button>
            <button class="button dpdt-modal-close">Cancel</button>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="dpdt-modal" id="dpdtUploadModal" style="display:none;">
    <div class="dpdt-modal-content">
        <div class="dpdt-modal-header">
            <h3><span class="dashicons dashicons-upload"></span> Upload Certificate</h3>
            <button class="dpdt-modal-close">&times;</button>
        </div>
        <div class="dpdt-modal-body">
            <input type="hidden" id="upload_app_id">
            <div class="dpdt-upload-tabs">
                <button class="upload-tab active" data-type="jpg">Upload JPG/PNG</button>
                <button class="upload-tab" data-type="pdf">Upload PDF</button>
            </div>
            <div class="dpdt-upload-area">
                <input type="file" id="cert_file_input" accept="image/*,.pdf">
                <p>Select the certificate file to upload</p>
            </div>
        </div>
        <div class="dpdt-modal-footer">
            <button class="button button-primary" id="uploadCertBtn">Upload</button>
            <button class="button dpdt-modal-close">Cancel</button>
        </div>
    </div>
</div>
