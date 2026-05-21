<?php
if (!defined('ABSPATH')) exit;
get_header();

$status = sanitize_text_field($_GET['status'] ?? '');
$code = sanitize_text_field($_GET['code'] ?? '');
?>

<section class="dpdt-apply-section">
    <div class="container">
        <div class="apply-header">
            <h2><i class="fas fa-file-signature"></i> Trademark Registration Application</h2>
            <p>ট্রেডমার্ক সার্টিফিকেট আবেদন ফরম | Application Form for Trademark Certificate</p>
        </div>

        <?php if ($status === 'success'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Application Submitted Successfully!</strong>
                <p>Your application code: <code><?php echo esc_html($code); ?></code></p>
                <p>Please save this code for future reference. You will be notified once approved.</p>
            </div>
        </div>
        <?php elseif ($status === 'ratelimit'): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <div><strong>Too many attempts.</strong> Please try again after some time.</div>
        </div>
        <?php elseif ($status === 'error'): ?>
        <div class="alert alert-error">
            <i class="fas fa-times-circle"></i>
            <div><strong>Error!</strong> Something went wrong. Please try again.</div>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" class="dpdt-form" id="trademarkForm">
            <input type="hidden" name="action" value="dpdt_submit_application">
            <?php echo DPDT_Security::get_csrf_field(); ?>
            <?php echo DPDT_Security::get_honeypot(); ?>

            <!-- Section 1: Applicant Information -->
            <div class="form-section">
                <h3><i class="fas fa-user"></i> Applicant / Owner Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="owner_name">Owner / Applicant Name <span class="req">*</span></label>
                        <input type="text" id="owner_name" name="owner_name" required placeholder="Full legal name of the owner">
                    </div>
                    <div class="form-group">
                        <label for="owner_email">Email Address <span class="req">*</span></label>
                        <input type="email" id="owner_email" name="owner_email" required placeholder="email@example.com">
                    </div>
                    <div class="form-group">
                        <label for="owner_phone">Phone Number <span class="req">*</span></label>
                        <input type="tel" id="owner_phone" name="owner_phone" required placeholder="+880-XXXX-XXXXXX">
                    </div>
                    <div class="form-group full-width">
                        <label for="owner_address">Address <span class="req">*</span></label>
                        <textarea id="owner_address" name="owner_address" rows="3" required placeholder="Full address including city, state, postal code, country"></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Trademark Details -->
            <div class="form-section">
                <h3><i class="fas fa-trademark"></i> Trademark Details</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="brand_name">Brand / Trademark Name <span class="req">*</span></label>
                        <input type="text" id="brand_name" name="brand_name" required placeholder="Your brand or trademark name">
                    </div>
                    <div class="form-group">
                        <label for="trademark_type">Type of Trademark <span class="req">*</span></label>
                        <select id="trademark_type" name="trademark_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="Word Mark">Word Mark</option>
                            <option value="Device Mark (Logo)">Device Mark (Logo)</option>
                            <option value="Combined Mark">Combined Mark (Word + Logo)</option>
                            <option value="Sound Mark">Sound Mark</option>
                            <option value="Color Mark">Color Mark</option>
                            <option value="3D Mark">3D Mark</option>
                            <option value="Collective Mark">Collective Mark</option>
                            <option value="Certification Mark">Certification Mark</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="trademark_class">Class of Goods/Services <span class="req">*</span></label>
                        <select id="trademark_class" name="trademark_class" required>
                            <option value="">-- Select Class --</option>
                            <option value="Class 1">Class 1 - Chemicals</option>
                            <option value="Class 2">Class 2 - Paints</option>
                            <option value="Class 3">Class 3 - Cosmetics & Cleaning</option>
                            <option value="Class 4">Class 4 - Lubricants & Fuels</option>
                            <option value="Class 5">Class 5 - Pharmaceuticals</option>
                            <option value="Class 6">Class 6 - Metal Goods</option>
                            <option value="Class 7">Class 7 - Machinery</option>
                            <option value="Class 8">Class 8 - Hand Tools</option>
                            <option value="Class 9">Class 9 - Electronics & Software</option>
                            <option value="Class 10">Class 10 - Medical Apparatus</option>
                            <option value="Class 11">Class 11 - Lighting & Heating</option>
                            <option value="Class 12">Class 12 - Vehicles</option>
                            <option value="Class 13">Class 13 - Firearms</option>
                            <option value="Class 14">Class 14 - Jewelry</option>
                            <option value="Class 15">Class 15 - Musical Instruments</option>
                            <option value="Class 16">Class 16 - Paper & Printing</option>
                            <option value="Class 17">Class 17 - Rubber</option>
                            <option value="Class 18">Class 18 - Leather Goods</option>
                            <option value="Class 19">Class 19 - Building Materials</option>
                            <option value="Class 20">Class 20 - Furniture</option>
                            <option value="Class 21">Class 21 - Housewares</option>
                            <option value="Class 22">Class 22 - Ropes & Textiles</option>
                            <option value="Class 23">Class 23 - Yarns & Threads</option>
                            <option value="Class 24">Class 24 - Fabrics</option>
                            <option value="Class 25">Class 25 - Clothing</option>
                            <option value="Class 26">Class 26 - Fancy Goods</option>
                            <option value="Class 27">Class 27 - Floor Coverings</option>
                            <option value="Class 28">Class 28 - Games & Toys</option>
                            <option value="Class 29">Class 29 - Foods (Processed)</option>
                            <option value="Class 30">Class 30 - Foods (Staple)</option>
                            <option value="Class 31">Class 31 - Agriculture</option>
                            <option value="Class 32">Class 32 - Beverages (Non-Alcoholic)</option>
                            <option value="Class 33">Class 33 - Beverages (Alcoholic)</option>
                            <option value="Class 34">Class 34 - Tobacco</option>
                            <option value="Class 35">Class 35 - Advertising & Business</option>
                            <option value="Class 36">Class 36 - Insurance & Finance</option>
                            <option value="Class 37">Class 37 - Construction & Repair</option>
                            <option value="Class 38">Class 38 - Telecommunications</option>
                            <option value="Class 39">Class 39 - Transport & Storage</option>
                            <option value="Class 40">Class 40 - Material Treatment</option>
                            <option value="Class 41">Class 41 - Education & Entertainment</option>
                            <option value="Class 42">Class 42 - Technology & Science</option>
                            <option value="Class 43">Class 43 - Food & Accommodation</option>
                            <option value="Class 44">Class 44 - Medical & Beauty</option>
                            <option value="Class 45">Class 45 - Legal & Security</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label for="goods_services">Description of Goods/Services <span class="req">*</span></label>
                        <textarea id="goods_services" name="goods_services" rows="3" required placeholder="Describe the goods or services for which trademark protection is sought"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="description">Trademark Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Describe the trademark (colors, design elements, meaning, etc.)"></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Brand Logo Upload -->
            <div class="form-section">
                <h3><i class="fas fa-image"></i> Brand Logo / Mark</h3>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="brand_logo">Upload Brand Logo</label>
                        <div class="file-upload-area" id="logoUploadArea">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload or drag and drop</p>
                            <span>PNG, JPG, SVG, WEBP (Max 5MB)</span>
                            <input type="file" id="brand_logo" name="brand_logo" accept="image/*">
                        </div>
                        <div class="logo-preview" id="logoPreview" style="display:none;">
                            <img id="logoPreviewImg" src="" alt="Logo Preview">
                            <button type="button" class="remove-logo" id="removeLogo"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Priority & Attorney -->
            <div class="form-section">
                <h3><i class="fas fa-gavel"></i> Priority Claim & Attorney</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="priority_claim">Priority Claim (if any)</label>
                        <input type="text" id="priority_claim" name="priority_claim" placeholder="Country, Application No., Date">
                    </div>
                    <div class="form-group">
                        <label for="attorney_name">Attorney / Agent Name</label>
                        <input type="text" id="attorney_name" name="attorney_name" placeholder="Name of authorized agent">
                    </div>
                    <div class="form-group full-width">
                        <label for="attorney_address">Attorney / Agent Address</label>
                        <textarea id="attorney_address" name="attorney_address" rows="2" placeholder="Address of attorney or agent"></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Submit Application
                </button>
                <button type="reset" class="btn-reset">
                    <i class="fas fa-redo"></i> Reset Form
                </button>
            </div>
        </form>
    </div>
</section>

<?php get_footer(); ?>
