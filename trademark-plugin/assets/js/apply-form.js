/**
 * DPDT Trademark Plugin - Application Form JavaScript
 * Version: 4.0.0
 */
(function($) {
    'use strict';

    var DPDTApplyForm = {
        init: function() {
            this.form = $('#dpdt-application-form');
            if (!this.form.length) return;

            this.submitBtn = $('#dpdt-submit-btn');
            this.bindEvents();
            this.initFilePreview();
        },

        bindEvents: function() {
            this.form.on('submit', this.handleSubmit.bind(this));
            // Real-time validation
            this.form.find('input[required], select[required]').on('blur', this.validateField.bind(this));
            // Remove error state on focus
            this.form.find('input, select, textarea').on('focus', function() {
                $(this).removeClass('error');
            });
        },

        /**
         * File preview for brand logo
         */
        initFilePreview: function() {
            $('#brand_logo').on('change', function(e) {
                var file = e.target.files[0];
                var $preview = $('#brand-logo-preview');

                if (!file) {
                    $preview.html('');
                    return;
                }

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    DPDTApplyForm.showFormError('ফাইল সাইজ 2MB এর বেশি হতে পারবে না।');
                    $(this).val('');
                    $preview.html('');
                    return;
                }

                // Validate file type
                var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                if (allowed.indexOf(file.type) === -1) {
                    DPDTApplyForm.showFormError('শুধুমাত্র JPG, PNG, GIF, SVG ফাইল অনুমোদিত।');
                    $(this).val('');
                    $preview.html('');
                    return;
                }

                // Show preview
                var reader = new FileReader();
                reader.onload = function(ev) {
                    $preview.html('<img src="' + ev.target.result + '" alt="Logo Preview" style="max-width:150px;max-height:150px;border:1px solid #ddd;padding:5px;border-radius:4px;" />');
                };
                reader.readAsDataURL(file);
            });
        },

        /**
         * Handle form submission
         */
        handleSubmit: function(e) {
            e.preventDefault();

            var self = this;

            // Validate all required fields
            if (!this.validateAll()) {
                return false;
            }

            // Check honeypot
            if (this.form.find('[name="dpdt_honeypot"]').val() !== '') {
                return false;
            }

            // Prevent double submit
            if (this.submitBtn.prop('disabled')) {
                return false;
            }

            // Show loading state
            this.submitBtn.prop('disabled', true);
            this.submitBtn.find('.dpdt-btn-text').hide();
            this.submitBtn.find('.dpdt-btn-loading').show();

            // Remove previous messages
            $('.dpdt-form-error').remove();

            // Prepare form data
            var formData = new FormData(this.form[0]);
            formData.append('action', 'dpdt_submit_application');

            // Submit via AJAX
            $.ajax({
                url: (typeof dpdtAjax !== 'undefined') ? dpdtAjax.ajaxurl : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000,
                success: function(response) {
                    if (response.success) {
                        // Show success
                        self.form.fadeOut(300, function() {
                            var $success = $('#dpdt-form-success');
                            $('#dpdt-success-message').text(response.data.message || 'আবেদন সফলভাবে জমা হয়েছে!');
                            $('#dpdt-success-details').text(response.data.details || '');
                            if (response.data.application_id) {
                                $('#dpdt-success-details').append('<br><strong>আবেদন নম্বর: ' + response.data.application_id + '</strong>');
                            }
                            $success.fadeIn(300);

                            // Scroll to success message
                            $('html, body').animate({
                                scrollTop: $success.offset().top - 100
                            }, 400);
                        });
                    } else {
                        self.showFormError(response.data.message || 'আবেদন জমা দিতে সমস্যা হয়েছে।');
                    }
                },
                error: function(xhr, status, error) {
                    var msg = 'সার্ভারে সমস্যা হয়েছে। পুনরায় চেষ্টা করুন।';
                    if (status === 'timeout') {
                        msg = 'অনুরোধ সময়সীমা অতিক্রম করেছে। আবার চেষ্টা করুন।';
                    } else if (xhr.status === 0) {
                        msg = 'ইন্টারনেট সংযোগ নেই। সংযোগ পরীক্ষা করে আবার চেষ্টা করুন।';
                    } else if (xhr.status === 403) {
                        msg = 'অনুমতি অস্বীকৃত। পেজ রিফ্রেশ করে আবার চেষ্টা করুন।';
                    }
                    self.showFormError(msg);
                },
                complete: function() {
                    self.submitBtn.prop('disabled', false);
                    self.submitBtn.find('.dpdt-btn-text').show();
                    self.submitBtn.find('.dpdt-btn-loading').hide();
                }
            });

            return false;
        },

        /**
         * Validate single field
         */
        validateField: function(e) {
            var $field = $(e.target);
            var value = $field.val() ? $field.val().trim() : '';
            var isValid = true;

            if ($field.prop('required') && !value) {
                isValid = false;
            }

            // Email validation
            if ($field.attr('type') === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                isValid = emailRegex.test(value);
            }

            // Phone validation
            if ($field.attr('type') === 'tel' && value) {
                var phone = value.replace(/[^0-9+]/g, '');
                isValid = phone.length >= 10 && phone.length <= 15;
            }

            if (isValid) {
                $field.removeClass('error');
            } else {
                $field.addClass('error');
            }

            return isValid;
        },

        /**
         * Validate all required fields
         */
        validateAll: function() {
            var isValid = true;
            var self = this;

            this.form.find('input[required], select[required]').each(function() {
                var $field = $(this);
                var value = $field.val() ? $field.val().trim() : '';
                if (!value) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });

            // Specific email validation
            var $email = this.form.find('input[type="email"]');
            if ($email.length && $email.val()) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test($email.val().trim())) {
                    $email.addClass('error');
                    isValid = false;
                }
            }

            // Specific phone validation
            var $phone = this.form.find('input[type="tel"]');
            if ($phone.length && $phone.val() && $phone.prop('required')) {
                var phone = $phone.val().replace(/[^0-9+]/g, '');
                if (phone.length < 10 || phone.length > 15) {
                    $phone.addClass('error');
                    isValid = false;
                }
            }

            if (!isValid) {
                this.showFormError('সকল প্রয়োজনীয় ঘর সঠিকভাবে পূরণ করুন।');
                // Scroll to first error
                var $firstError = this.form.find('.error').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 400);
                }
            }

            return isValid;
        },

        /**
         * Show form error message
         */
        showFormError: function(message) {
            // Remove existing error
            $('.dpdt-form-error').remove();

            var $error = $('<div class="dpdt-form-notice dpdt-form-error" style="background:#fdedec;border:1px solid #f5b7b1;color:#c0392b;padding:12px 15px;border-radius:4px;margin-bottom:15px;display:flex;align-items:center;gap:8px;"><span class="dashicons dashicons-warning" style="font-size:18px;"></span><p style="margin:0;">' + message + '</p></div>');
            this.form.prepend($error);

            // Auto-remove after 8s
            setTimeout(function() {
                $error.fadeOut(300, function() { $(this).remove(); });
            }, 8000);

            // Scroll to top of form
            $('html, body').animate({
                scrollTop: this.form.offset().top - 50
            }, 400);
        }
    };

    $(document).ready(function() {
        DPDTApplyForm.init();
    });

})(jQuery);
