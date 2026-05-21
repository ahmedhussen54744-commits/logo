/**
 * DPDT Trademark Plugin - Admin JavaScript
 * Version: 3.5.0
 */
(function($) {
    'use strict';

    var DPDTAdmin = {
        init: function() {
            this.initMediaUploader();
            this.initModals();
            this.initApproveForm();
            this.initRejectForm();
            this.initBulkActions();
        },

        /**
         * WordPress Media Uploader for logos
         */
        initMediaUploader: function() {
            $(document).on('click', '.dpdt-upload-btn', function(e) {
                e.preventDefault();
                var $button = $(this);
                var targetId = $button.data('target');

                var frame = wp.media({
                    title: dpdtAdmin.uploadTitle || 'Select Image',
                    button: { text: dpdtAdmin.uploadButton || 'Select' },
                    multiple: false,
                    library: { type: 'image' }
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#' + targetId).val(attachment.id);

                    // Update preview
                    var $preview = $button.closest('td').find('.dpdt-logo-preview, .dpdt-logo-preview-small');
                    if ($preview.length) {
                        var size = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                        $preview.html('<img src="' + size + '" style="max-width:200px;max-height:100px;" />');
                    }
                });

                frame.open();
            });

            // Remove button
            $(document).on('click', '.dpdt-remove-btn', function(e) {
                e.preventDefault();
                var targetId = $(this).data('target');
                $('#' + targetId).val('0');

                var $preview = $(this).closest('td').find('.dpdt-logo-preview, .dpdt-logo-preview-small');
                if ($preview.length) {
                    $preview.html('<p class="dpdt-no-logo">No logo selected</p>');
                }
            });
        },

        /**
         * Modal handlers
         */
        initModals: function() {
            // Open approve modal
            $(document).on('click', '.dpdt-approve-btn', function() {
                var appId = $(this).data('id');
                $('#approve-app-id').val(appId);
                $('#dpdt-approve-modal').fadeIn(200);
            });

            // Open reject modal
            $(document).on('click', '.dpdt-reject-btn', function() {
                var appId = $(this).data('id');
                $('#reject-app-id').val(appId);
                $('#dpdt-reject-modal').fadeIn(200);
            });

            // Close modal
            $(document).on('click', '.dpdt-modal-close', function() {
                $(this).closest('.dpdt-modal').fadeOut(200);
            });

            // Close on backdrop click
            $(document).on('click', '.dpdt-modal', function(e) {
                if (e.target === this) {
                    $(this).fadeOut(200);
                }
            });

            // Close on ESC
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.dpdt-modal').fadeOut(200);
                }
            });
        },

        /**
         * Approve form submission
         */
        initApproveForm: function() {
            $('#dpdt-approve-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var formData = new FormData(this);
                formData.append('action', 'dpdt_approve_application');
                formData.append('nonce', dpdtAdmin.nonce);

                var $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true).text('Processing...');

                $.ajax({
                    url: dpdtAdmin.ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload();
                        } else {
                            alert(response.data.message || 'Error occurred');
                        }
                    },
                    error: function() {
                        alert('Server error. Please try again.');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('অনুমোদন করুন');
                    }
                });
            });
        },

        /**
         * Reject form submission
         */
        initRejectForm: function() {
            $('#dpdt-reject-form').on('submit', function(e) {
                e.preventDefault();
                var $form = $(this);
                var data = {
                    action: 'dpdt_reject_application',
                    nonce: dpdtAdmin.nonce,
                    application_id: $('#reject-app-id').val(),
                    reason: $form.find('[name="reason"]').val()
                };

                var $btn = $form.find('button[type="submit"]');
                $btn.prop('disabled', true).text('Processing...');

                $.post(dpdtAdmin.ajaxurl, data, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message || 'Error occurred');
                    }
                }).fail(function() {
                    alert('Server error');
                }).always(function() {
                    $btn.prop('disabled', false).text('প্রত্যাখ্যান করুন');
                });
            });
        },

        /**
         * Bulk action confirmation
         */
        initBulkActions: function() {
            $('form').on('submit', function() {
                var action = $(this).find('[name="dpdt_bulk_action"]').val();
                if (action === 'delete') {
                    var checked = $(this).find('input[name="application_ids[]"]:checked');
                    if (checked.length === 0) {
                        alert('Please select at least one item.');
                        return false;
                    }
                    return confirm('Are you sure you want to delete ' + checked.length + ' item(s)?');
                }
            });
        }
    };

    $(document).ready(function() {
        DPDTAdmin.init();
    });

})(jQuery);
