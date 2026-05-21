/**
 * DPDT Admin Dashboard JS
 */
(function($) {
    'use strict';

    // Modal functions
    function openModal(id) { $('#' + id).fadeIn(200); }
    function closeModal() { $('.dpdt-modal').fadeOut(200); }

    $(document).on('click', '.dpdt-modal-close', closeModal);
    $(document).on('click', '.dpdt-modal', function(e) {
        if ($(e.target).hasClass('dpdt-modal')) closeModal();
    });

    // Approve Application
    $(document).on('click', '.dpdt-approve-btn', function() {
        var id = $(this).data('id');
        if (!confirm('Are you sure you want to approve this application?')) return;
        
        $.post(dpdtAdmin.ajaxurl, {
            action: 'dpdt_approve_app',
            nonce: dpdtAdmin.nonce,
            app_id: id
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Reject Application
    $(document).on('click', '.dpdt-reject-btn', function() {
        var id = $(this).data('id');
        var notes = prompt('Reason for rejection (optional):');
        if (notes === null) return;
        
        $.post(dpdtAdmin.ajaxurl, {
            action: 'dpdt_reject_app',
            nonce: dpdtAdmin.nonce,
            app_id: id,
            admin_notes: notes
        }, function(response) {
            if (response.success) {
                alert(response.data.message);
                location.reload();
            }
        });
    });

    // Edit Application
    $(document).on('click', '.dpdt-edit-btn', function() {
        var id = $(this).data('id');
        $('#edit_app_id').val(id);
        openModal('dpdtEditModal');
    });

    // Save Edit
    $('#saveEditBtn').on('click', function() {
        var data = {
            action: 'dpdt_update_app',
            nonce: dpdtAdmin.nonce,
            app_id: $('#edit_app_id').val(),
            registration_number: $('#edit_reg_number').val(),
            application_date: $('#edit_app_date').val(),
            registration_date: $('#edit_reg_date').val(),
            approved_date: $('#edit_approved_date').val(),
            expiry_date: $('#edit_expiry_date').val(),
            status: $('#edit_status').val(),
            verify_url: $('#edit_verify_url').val(),
            qr_code_url: $('#edit_qr_url').val(),
            admin_notes: $('#edit_admin_notes').val()
        };

        $.post(dpdtAdmin.ajaxurl, data, function(response) {
            if (response.success) {
                alert(response.data.message);
                closeModal();
                location.reload();
            } else {
                alert('Error: ' + response.data.message);
            }
        });
    });

    // Upload Certificate
    var currentUploadType = 'jpg';
    
    $(document).on('click', '.dpdt-upload-btn', function() {
        var id = $(this).data('id');
        $('#upload_app_id').val(id);
        openModal('dpdtUploadModal');
    });

    $(document).on('click', '.upload-tab', function() {
        $('.upload-tab').removeClass('active');
        $(this).addClass('active');
        currentUploadType = $(this).data('type');
        
        var accept = currentUploadType === 'pdf' ? '.pdf' : 'image/*';
        $('#cert_file_input').attr('accept', accept);
    });

    $('#uploadCertBtn').on('click', function() {
        var fileInput = document.getElementById('cert_file_input');
        if (!fileInput.files.length) {
            alert('Please select a file first.');
            return;
        }

        var formData = new FormData();
        formData.append('action', 'dpdt_upload_cert');
        formData.append('nonce', dpdtAdmin.nonce);
        formData.append('app_id', $('#upload_app_id').val());
        formData.append('cert_type', currentUploadType);
        formData.append('certificate_file', fileInput.files[0]);

        $.ajax({
            url: dpdtAdmin.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    closeModal();
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Upload failed. Please try again.');
            }
        });
    });

})(jQuery);
