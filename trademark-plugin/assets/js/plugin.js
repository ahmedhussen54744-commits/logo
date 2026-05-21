/**
 * DPDT Plugin Frontend JS
 */
(function() {
    'use strict';

    // Logo Upload Preview
    const logoInput = document.getElementById('brand_logo');
    const logoPreview = document.getElementById('logoPreview');
    const logoPreviewImg = document.getElementById('logoPreviewImg');
    const removeLogo = document.getElementById('removeLogo');
    const uploadArea = document.getElementById('logoUploadArea');

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    logoPreviewImg.src = ev.target.result;
                    logoPreview.style.display = 'inline-block';
                    uploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (removeLogo) {
        removeLogo.addEventListener('click', function() {
            logoInput.value = '';
            logoPreview.style.display = 'none';
            uploadArea.style.display = 'block';
        });
    }

    // Form Validation
    const form = document.getElementById('trademarkForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const required = form.querySelectorAll('[required]');
            let valid = true;
            
            required.forEach(function(field) {
                if (!field.value.trim()) {
                    field.style.borderColor = '#f42a41';
                    valid = false;
                } else {
                    field.style.borderColor = '#e0e0e0';
                }
            });

            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }
})();
