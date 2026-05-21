/**
 * DPDT Trademark Plugin - Application Form JavaScript
 * Version: 3.5.0
 */
(function($) {
    'use strict';

    var DPDTApplyForm = {
        init: function() {
            this.bindEvents();
            this.initFilePreview();
        },

        bindEvents: function() {
            $('#dpdt-application-form').on('submit', this.handleSubmit.bind(this));
            // Real-time validation
            $('#dpdt-application-form input[required], #dpdt-application-form select[required]').on('blur', this.validateField.bind(this));
        },

        /**
         * File preview for brand logo
         */
        initFilePreview: function() {
            $('#brand_logo').on('change', function(e) {
                var file = e.target.files[0];
                if (!file) return;

                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('ফাইল সাইজ 2MB এর বেশি হতে পারবে না।');
                    $(this).val('');
                    return;
                }

                // Validate file type
                var allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
                if (allowed.indexOf(file.type) === -1) {
                    alert('শুধুমাত্র JPG, PNG, GIF, SVG ফাইল অনুমোদিত।');
                    $(this).val('');
                    return;
                }

                // Show preview
                var reader = new FileReader();
                reader.onload = function(ev) {
                    $('#brand-logo-preview').html('<img src="' + ev.target.result + '" alt="Logo Preview" />');
                };
                reader.readAsDataURL(file);
            });
        },

        /**
         * Handle form submission
         */
        handleSubmit: function(e) {
            e.preventDefault();

            var $form = $('#dpdt-application-form');
            var $btn = $('#dpdt-submit-btn');

            // Validate all required fields
            if (!this.validateAll()) {
                return false;
            }

            // Check honeypot
            if ($form.find('[name="dpdt_honeypot"]').val() !== '') {
                return false;
            }

            // Show loading state
            $btn.prop('disabled', true);
            $btn.find('.dpdt-btn-text').hide();
            $btn.find('.dpdt-btn-loading').show();

            // Prepare form data
            var formData = new FormData($form[0]);
            formData.append('action', 'dpdt_submit_application');

            // Submit via AJAX
            $.ajax({
                url: dpdtAjax.ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000,
                success: function(response) {
                    if (response.success) {
                        // Show success
                        $form.fadeOut(300, function() {
                            $('#dpdt-success-message').text(response.data.message);
                            $('#dpdt-success-details').text(response.data.details || '');
                            $('#dpdt-form-success').fadeIn(300);
                        });
                    } else {
                        DPDTApplyForm.showFormError(response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    var msg = 'সার্ভারে সমস্যা হয়েছে।';
                    if (status === 'timeout') {
                        msg = 'অনুরোধ সময়সীমা অতিক্রম করেছে। আবার চেষ্টা করুন।';
                    }
                    DPDTApplyForm.showFormError(msg);
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $btn.find('.dpdt-btn-text').show();
                    $btn.find('.dpdt-btn-loading').hide();
                }
            });
        },

        /**
         * Validate single field
         */
        validateField: function(e) {
            var $field = $(e.target);
            var value = $field.val().trim();
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
            var $form = $('#dpdt-application-form');

            $form.find('input[required], select[required]').each(function() {
                var $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });

            if (!isValid) {
                this.showFormError('সকল প্রয়োজনীয় ঘর পূরণ করুন।');
                // Scroll to first error
                var $firstError = $form.find('.error').first();
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

            var $error = $('<div class="dpdt-form-notice dpdt-form-error" style="background:#fdedec;border:1px solid #f5b7b1;color:#c0392b;"><span class="dashicons dashicons-warning"></span><p>' + message + '</p></div>');
            $('#dpdt-application-form').prepend($error);

            // Auto-remove after 5s
            setTimeout(function() {
                $error.fadeOut(300, function() { $(this).remove(); });
            }, 5000);

            // Scroll to top of form
            $('html, body').animate({
                scrollTop: $('#dpdt-application-form').offset().top - 50
            }, 400);
        }
    };

    $(document).ready(function() {
        DPDTApplyForm.init();
    });

})(jQuery);
