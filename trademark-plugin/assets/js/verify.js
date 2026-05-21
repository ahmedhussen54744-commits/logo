/**
 * DPDT Trademark Plugin - Verify Page JavaScript
 * Version: 3.5.0
 */
(function($) {
    'use strict';

    var DPDTVerify = {
        init: function() {
            this.bindEvents();
            this.checkUrlToken();
        },

        bindEvents: function() {
            $('#dpdt-verify-form').on('submit', this.handleVerify.bind(this));
        },

        /**
         * Check if token is in URL
         */
        checkUrlToken: function() {
            var urlParams = new URLSearchParams(window.location.search);
            var token = urlParams.get('token');
            if (token) {
                $('#dpdt-verify-input').val(token);
                this.doVerify(token, '');
            }
        },

        /**
         * Handle form submit
         */
        handleVerify: function(e) {
            e.preventDefault();
            var input = $('#dpdt-verify-input').val().trim();
            if (!input) {
                this.showError('সার্টিফিকেট নম্বর বা টোকেন লিখুন।');
                return;
            }

            // Determine if it's a token or certificate number
            if (input.indexOf('DPDT-CERT') !== -1 || input.indexOf('DPDT-') !== -1) {
                this.doVerify('', input);
            } else {
                this.doVerify(input, '');
            }
        },

        /**
         * Perform verification AJAX call
         */
        doVerify: function(token, certNumber) {
            var self = this;
            var $btn = $('#dpdt-verify-btn');
            var $result = $('#dpdt-verify-result');

            // Show loading
            $btn.find('.dpdt-btn-text').hide();
            $btn.find('.dpdt-btn-loading').show();
            $btn.prop('disabled', true);
            $result.html('').hide();

            var data = {
                action: 'dpdt_verify_certificate',
                token: token,
                certificate_number: certNumber
            };

            $.post(dpdtAjax.ajaxurl, data, function(response) {
                if (response.success) {
                    self.showResult(response.data.certificate);
                } else {
                    self.showInvalid(response.data.message);
                }
            }).fail(function() {
                self.showError('সার্ভারে সমস্যা হয়েছে। আবার চেষ্টা করুন।');
            }).always(function() {
                $btn.find('.dpdt-btn-text').show();
                $btn.find('.dpdt-btn-loading').hide();
                $btn.prop('disabled', false);
            });
        },

        /**
         * Show verified result
         */
        showResult: function(cert) {
            var $result = $('#dpdt-verify-result');
            var template = $('#dpdt-verify-template-valid').html();
            $result.html(template).fadeIn(300);

            // Fill in data
            $('#verify-cert-number').text(cert.certificate_number || '');
            $('#verify-brand-name').text(cert.brand_name + (cert.brand_name_bn ? ' (' + cert.brand_name_bn + ')' : ''));
            $('#verify-holder-name').text(cert.applicant_name + (cert.owner_name ? ' / ' + cert.owner_name : ''));
            $('#verify-company').text(cert.company_name || '-');
            $('#verify-class').text(cert.trademark_class || '-');
            $('#verify-app-date').text(this.formatDate(cert.application_date));
            $('#verify-reg-date').text(this.formatDate(cert.registration_date));
            $('#verify-approved-date').text(this.formatDate(cert.approved_date));
            $('#verify-expiry-date').text(this.formatDate(cert.expiry_date));

            // Status
            var statusHtml = '';
            if (cert.is_expired) {
                statusHtml = '<span style="color:#e74c3c;font-weight:bold;">মেয়াদ উত্তীর্ণ</span>';
            } else if (cert.is_valid) {
                statusHtml = '<span style="color:#27ae60;font-weight:bold;">✓ বৈধ</span>';
            } else {
                statusHtml = '<span style="color:#e74c3c;font-weight:bold;">বাতিল</span>';
            }
            $('#verify-status').html(statusHtml);

            // Brand logo
            if (cert.brand_logo_url) {
                $('#verify-logo-section').show();
                $('#verify-brand-logo').attr('src', cert.brand_logo_url).attr('alt', cert.brand_name);
                var ownerText = cert.owner_name || cert.applicant_name;
                if (cert.brand_name) {
                    ownerText = cert.brand_name + ' - ' + ownerText;
                }
                $('#verify-brand-owner').text(ownerText);
            }

            // Certificate image
            if (cert.certificate_jpg_url) {
                $('#verify-cert-img-section').show();
                $('#verify-cert-img').attr('src', cert.certificate_jpg_url).attr('alt', 'Certificate');
            }

            // Scroll to result
            $('html, body').animate({
                scrollTop: $result.offset().top - 100
            }, 500);
        },

        /**
         * Show invalid result
         */
        showInvalid: function(message) {
            var $result = $('#dpdt-verify-result');
            var template = $('#dpdt-verify-template-invalid').html();
            $result.html(template).fadeIn(300);

            if (message) {
                $result.find('.dpdt-invalid-text').text(message);
            }
        },

        /**
         * Show error message
         */
        showError: function(message) {
            var $result = $('#dpdt-verify-result');
            $result.html('<div class="dpdt-verify-card dpdt-invalid"><div class="dpdt-verify-badge dpdt-badge-invalid"><span class="dpdt-invalid-icon">!</span><h3>ত্রুটি</h3><p class="dpdt-invalid-text">' + message + '</p></div></div>').fadeIn(300);
        },

        /**
         * Format date
         */
        formatDate: function(dateStr) {
            if (!dateStr || dateStr === '0000-00-00 00:00:00') return '-';
            try {
                var date = new Date(dateStr);
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
