/**
 * DPDT Trademark Plugin - Verify Page JavaScript
 * Version: 4.0.0
 */
(function($) {
    'use strict';

    var DPDTVerify = {
        init: function() {
            this.form = $('#dpdt-verify-form');
            if (!this.form.length) return;

            this.input = $('#dpdt-verify-input');
            this.btn = $('#dpdt-verify-btn');
            this.result = $('#dpdt-verify-result');

            this.bindEvents();
            this.checkUrlToken();
        },

        bindEvents: function() {
            this.form.on('submit', this.handleVerify.bind(this));
            // Allow enter key in input
            this.input.on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    DPDTVerify.form.trigger('submit');
                }
            });
        },

        /**
         * Check if token is in URL
         */
        checkUrlToken: function() {
            try {
                var urlParams = new URLSearchParams(window.location.search);
                var token = urlParams.get('token') || urlParams.get('verify') || urlParams.get('cert');
                if (token) {
                    this.input.val(token);
                    this.doVerify(token, '');
                }
            } catch (e) {
                // URLSearchParams not supported in older browsers
            }
        },

        /**
         * Handle form submit
         */
        handleVerify: function(e) {
            e.preventDefault();
            var input = this.input.val() ? this.input.val().trim() : '';

            if (!input) {
                this.showError('সার্টিফিকেট নম্বর, আবেদন নম্বর বা টোকেন লিখুন।');
                this.input.focus();
                return;
            }

            // Determine if it's a certificate number or token
            // Certificate numbers typically contain "CERT" prefix
            // Application IDs contain "DPDT-YEAR-" pattern
            if (input.indexOf('DPDT-CERT') !== -1 || input.indexOf('CERT-') !== -1) {
                // It's a certificate number
                this.doVerify('', input);
            } else if (input.indexOf('DPDT-') !== -1) {
                // Could be application ID or certificate number, send as certificate_number
                this.doVerify('', input);
            } else {
                // Treat as a verification token
                this.doVerify(input, '');
            }
        },

        /**
         * Perform verification AJAX call
         */
        doVerify: function(token, certNumber) {
            var self = this;

            // Prevent double-click
            if (this.btn.prop('disabled')) return;

            // Show loading
            this.btn.find('.dpdt-btn-text').hide();
            this.btn.find('.dpdt-btn-loading').show();
            this.btn.prop('disabled', true);
            this.result.html('').hide();

            var ajaxUrl = (typeof dpdtAjax !== 'undefined') ? dpdtAjax.ajaxurl : '/wp-admin/admin-ajax.php';

            var data = {
                action: 'dpdt_verify_certificate',
                token: token || '',
                certificate_number: certNumber || ''
            };

            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: data,
                timeout: 15000,
                success: function(response) {
                    if (response.success && response.data && response.data.certificate) {
                        self.showResult(response.data.certificate);
                    } else {
                        var msg = (response.data && response.data.message) ? response.data.message : 'এই তথ্য দিয়ে কোনো সার্টিফিকেট পাওয়া যায়নি।';
                        self.showInvalid(msg);
                    }
                },
                error: function(xhr, status, error) {
                    var msg = 'সার্ভারে সমস্যা হয়েছে। আবার চেষ্টা করুন।';
                    if (status === 'timeout') {
                        msg = 'অনুরোধ সময়সীমা অতিক্রম করেছে। আবার চেষ্টা করুন।';
                    } else if (xhr.status === 0) {
                        msg = 'ইন্টারনেট সংযোগ পরীক্ষা করুন।';
                    }
                    self.showError(msg);
                },
                complete: function() {
                    self.btn.find('.dpdt-btn-text').show();
                    self.btn.find('.dpdt-btn-loading').hide();
                    self.btn.prop('disabled', false);
                }
            });
        },

        /**
         * Show verified result
         */
        showResult: function(cert) {
            var $template = $('#dpdt-verify-template-valid');
            if ($template.length) {
                this.result.html($template.html()).fadeIn(300);
            } else {
                // Fallback template if script template not in DOM
                this.result.html(this.buildResultHTML(cert)).fadeIn(300);
            }

            // Fill in data
            $('#verify-cert-number').text(cert.certificate_number || cert.application_id || '-');
            $('#verify-brand-name').text(cert.brand_name + (cert.brand_name_bn ? ' (' + cert.brand_name_bn + ')' : ''));
            $('#verify-holder-name').text(cert.applicant_name + (cert.owner_name ? ' / ' + cert.owner_name : ''));
            $('#verify-company').text(cert.company_name || '-');
            $('#verify-class').text(cert.trademark_class || '-');
            $('#verify-app-date').text(this.formatDate(cert.application_date));
            $('#verify-reg-date').text(this.formatDate(cert.registration_date));
            $('#verify-approved-date').text(this.formatDate(cert.approved_date));
            $('#verify-expiry-date').text(this.formatDate(cert.expiry_date));

            // Status badge
            var statusHtml = '';
            if (cert.is_expired) {
                statusHtml = '<span class="dpdt-status-badge dpdt-status-expired" style="color:#e74c3c;font-weight:bold;padding:4px 10px;background:#fdedec;border-radius:4px;">মেয়াদ উত্তীর্ণ</span>';
            } else if (cert.is_valid !== false) {
                statusHtml = '<span class="dpdt-status-badge dpdt-status-valid" style="color:#27ae60;font-weight:bold;padding:4px 10px;background:#eafaf1;border-radius:4px;">✓ বৈধ</span>';
            } else {
                statusHtml = '<span class="dpdt-status-badge dpdt-status-revoked" style="color:#e74c3c;font-weight:bold;padding:4px 10px;background:#fdedec;border-radius:4px;">বাতিল</span>';
            }
            $('#verify-status').html(statusHtml);

            // Brand logo
            if (cert.brand_logo_url) {
                $('#verify-logo-section').show();
                $('#verify-brand-logo').attr('src', cert.brand_logo_url).attr('alt', cert.brand_name || 'Brand Logo');
                var ownerText = cert.owner_name || cert.applicant_name || '';
                if (cert.brand_name) {
                    ownerText = cert.brand_name + (ownerText ? ' - ' + ownerText : '');
                }
                $('#verify-brand-owner').text(ownerText);
            } else {
                $('#verify-logo-section').hide();
            }

            // Certificate image (show inline, NOT as download link)
            if (cert.certificate_jpg_url) {
                $('#verify-cert-img-section').show();
                $('#verify-cert-img').attr('src', cert.certificate_jpg_url).attr('alt', 'Certificate');
            } else {
                $('#verify-cert-img-section').hide();
            }

            // QR Code image
            if (cert.qr_code_url) {
                $('#verify-qr-section').show();
                $('#verify-qr-img').attr('src', cert.qr_code_url).attr('alt', 'QR Code');
            } else {
                $('#verify-qr-section').hide();
            }

            // Certificate verify URL
            if (cert.verify_url) {
                $('#verify-cert-url-section').show();
                $('#verify-cert-url').attr('href', cert.verify_url).text(cert.verify_url);
            } else {
                $('#verify-cert-url-section').hide();
            }

            // Scroll to result
            $('html, body').animate({
                scrollTop: this.result.offset().top - 100
            }, 500);
        },

        /**
         * Build result HTML as fallback
         */
        buildResultHTML: function(cert) {
            return '<div class="dpdt-verify-card dpdt-valid">' +
                '<div class="dpdt-verify-badge dpdt-badge-valid">' +
                '<span class="dpdt-valid-icon">✓</span>' +
                '<h3>সার্টিফিকেট বৈধ</h3>' +
                '</div>' +
                '<div class="dpdt-verify-details">' +
                '<div id="verify-logo-section" style="display:none;text-align:center;margin-bottom:20px;"><img id="verify-brand-logo" src="" style="max-width:150px;max-height:150px;" /><p id="verify-brand-owner"></p></div>' +
                '<div id="verify-cert-img-section" style="display:none;text-align:center;margin-bottom:20px;"><img id="verify-cert-img" src="" style="max-width:100%;border:1px solid #ddd;padding:5px;" /></div>' +
                '<table class="dpdt-verify-table">' +
                '<tr><th>সার্টিফিকেট নম্বর:</th><td id="verify-cert-number"></td></tr>' +
                '<tr><th>ব্র্যান্ড:</th><td id="verify-brand-name"></td></tr>' +
                '<tr><th>আবেদনকারী:</th><td id="verify-holder-name"></td></tr>' +
                '<tr><th>প্রতিষ্ঠান:</th><td id="verify-company"></td></tr>' +
                '<tr><th>শ্রেণী:</th><td id="verify-class"></td></tr>' +
                '<tr><th>আবেদনের তারিখ:</th><td id="verify-app-date"></td></tr>' +
                '<tr><th>নিবন্ধনের তারিখ:</th><td id="verify-reg-date"></td></tr>' +
                '<tr><th>অনুমোদনের তারিখ:</th><td id="verify-approved-date"></td></tr>' +
                '<tr><th>মেয়াদ:</th><td id="verify-expiry-date"></td></tr>' +
                '<tr><th>স্ট্যাটাস:</th><td id="verify-status"></td></tr>' +
                '</table>' +
                '</div>' +
                '<div id="verify-qr-section" style="display:none;text-align:center;margin-top:20px;"><img id="verify-qr-img" src="" style="max-width:200px;" /></div>' +
                '<div id="verify-cert-url-section" style="display:none;text-align:center;margin-top:10px;"><a id="verify-cert-url" href="" target="_blank"></a></div>' +
                '</div>';
        },

        /**
         * Show invalid result
         */
        showInvalid: function(message) {
            var $template = $('#dpdt-verify-template-invalid');
            if ($template.length) {
                this.result.html($template.html()).fadeIn(300);
                this.result.find('.dpdt-invalid-text').text(message || 'এই তথ্য দিয়ে কোনো বৈধ সার্টিফিকেট পাওয়া যায়নি।');
            } else {
                this.result.html(
                    '<div class="dpdt-verify-card dpdt-invalid">' +
                    '<div class="dpdt-verify-badge dpdt-badge-invalid">' +
                    '<span class="dpdt-invalid-icon" style="font-size:48px;color:#e74c3c;">✗</span>' +
                    '<h3 style="color:#e74c3c;">অবৈধ সার্টিফিকেট</h3>' +
                    '<p class="dpdt-invalid-text">' + (message || 'এই তথ্য দিয়ে কোনো বৈধ সার্টিফিকেট পাওয়া যায়নি।') + '</p>' +
                    '</div></div>'
                ).fadeIn(300);
            }

            // Scroll to result
            $('html, body').animate({
                scrollTop: this.result.offset().top - 100
            }, 500);
        },

        /**
         * Show error message
         */
        showError: function(message) {
            this.result.html(
                '<div class="dpdt-verify-card dpdt-invalid" style="border-color:#f39c12;">' +
                '<div class="dpdt-verify-badge dpdt-badge-invalid">' +
                '<span class="dpdt-invalid-icon" style="font-size:36px;color:#f39c12;">!</span>' +
                '<h3 style="color:#f39c12;">ত্রুটি</h3>' +
                '<p class="dpdt-invalid-text">' + message + '</p>' +
                '</div></div>'
            ).fadeIn(300);
        },

        /**
         * Format date to Bengali locale
         */
        formatDate: function(dateStr) {
            if (!dateStr || dateStr === '0000-00-00 00:00:00' || dateStr === '' || dateStr === null) return '-';
            try {
                var date = new Date(dateStr);
                if (isNaN(date.getTime())) return dateStr;
                return date.toLocaleDateString('bn-BD', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (e) {
                return dateStr;
            }
        }
    };

    $(document).ready(function() {
        DPDTVerify.init();
    });

})(jQuery);
