(function () {
    'use strict';

    // ============================================
    // UTILITY: Show Toast Notification
    // ============================================
    function showToast(message, type) {
        type = type || 'info';
        var existing = document.querySelector('.toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.className = 'toast ' + type;
        var icon = '';
        if (type === 'success') icon = '<i class="fas fa-check-circle"></i>';
        else if (type === 'error') icon = '<i class="fas fa-exclamation-circle"></i>';
        else icon = '<i class="fas fa-info-circle"></i>';
        toast.innerHTML = icon + message;
        document.body.appendChild(toast);

        setTimeout(function () { toast.classList.add('show'); }, 50);
        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () { if (toast.parentNode) toast.remove(); }, 400);
        }, 3000);
    }

    // ============================================
    // DATE DISPLAY
    // ============================================
    function setCurrentDate() {
        var dateEl = document.getElementById('currentDate');
        if (!dateEl) return;
        var now = new Date();
        var options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
        dateEl.textContent = now.toLocaleDateString('bn-BD', options);
    }
    setCurrentDate();


    // ============================================
    // SPA PAGE NAVIGATION
    // ============================================
    function navigateTo(pageName) {
        if (!pageName) return;
        var pages = document.querySelectorAll('.page-content');
        pages.forEach(function (p) { p.classList.remove('active'); });

        var target = document.getElementById('page-' + pageName);
        if (target) {
            target.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Close mobile menu if open
            var navMenu = document.getElementById('navMenu');
            if (navMenu) navMenu.classList.remove('active');
        }
    }

    // Handle all data-page clicks
    document.addEventListener('click', function (e) {
        var el = e.target.closest('[data-page]');
        if (el) {
            e.preventDefault();
            var page = el.getAttribute('data-page');
            navigateTo(page);
        }
    });

    // Login/Register nav buttons
    var loginNavBtn = document.getElementById('loginNavBtn');
    var registerNavBtn = document.getElementById('registerNavBtn');
    if (loginNavBtn) loginNavBtn.addEventListener('click', function () { navigateTo('login'); });
    if (registerNavBtn) registerNavBtn.addEventListener('click', function () { navigateTo('register'); });


    // ============================================
    // HERO SLIDER
    // ============================================
    var slides = document.querySelectorAll('.slide');
    var dotsContainer = document.getElementById('sliderDots');
    var prevBtn = document.querySelector('.prev-btn');
    var nextBtn = document.querySelector('.next-btn');
    var currentSlide = 0;
    var slideInterval;

    function initSlider() {
        if (!slides.length || !dotsContainer) return;
        for (var i = 0; i < slides.length; i++) {
            var dot = document.createElement('span');
            dot.className = 'dot' + (i === 0 ? ' active' : '');
            dot.setAttribute('data-index', i);
            dot.addEventListener('click', function () {
                goToSlide(parseInt(this.getAttribute('data-index')));
            });
            dotsContainer.appendChild(dot);
        }
        startAutoPlay();
    }

    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        var dots = dotsContainer.querySelectorAll('.dot');
        dots[currentSlide].classList.remove('active');
        currentSlide = index;
        if (currentSlide >= slides.length) currentSlide = 0;
        if (currentSlide < 0) currentSlide = slides.length - 1;
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlideFn() { goToSlide(currentSlide + 1); }
    function prevSlideFn() { goToSlide(currentSlide - 1); }
    function startAutoPlay() { slideInterval = setInterval(nextSlideFn, 4000); }
    function stopAutoPlay() { clearInterval(slideInterval); }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () { stopAutoPlay(); prevSlideFn(); startAutoPlay(); });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () { stopAutoPlay(); nextSlideFn(); startAutoPlay(); });
    }
    initSlider();


    // ============================================
    // MOBILE MENU TOGGLE
    // ============================================
    var mobileMenuBtn = document.getElementById('mobileMenuBtn');
    var navMenu = document.getElementById('navMenu');

    if (mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            var icon = this.querySelector('i');
            icon.className = navMenu.classList.contains('active') ? 'fas fa-times' : 'fas fa-bars';
        });

        var dropdownItems = navMenu.querySelectorAll('.has-dropdown');
        dropdownItems.forEach(function (item) {
            item.querySelector('a').addEventListener('click', function (e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    item.classList.toggle('dropdown-open');
                }
            });
        });
    }

    // ============================================
    // FONT SIZE CONTROLS
    // ============================================
    var fontSize = 16;
    var fontIncrease = document.getElementById('fontIncrease');
    var fontDecrease = document.getElementById('fontDecrease');
    var fontReset = document.getElementById('fontReset');

    if (fontIncrease) {
        fontIncrease.addEventListener('click', function () {
            if (fontSize < 22) { fontSize += 1; document.documentElement.style.fontSize = fontSize + 'px'; showToast('অক্ষরের আকার বৃদ্ধি করা হয়েছে', 'info'); }
        });
    }
    if (fontDecrease) {
        fontDecrease.addEventListener('click', function () {
            if (fontSize > 12) { fontSize -= 1; document.documentElement.style.fontSize = fontSize + 'px'; showToast('অক্ষরের আকার হ্রাস করা হয়েছে', 'info'); }
        });
    }
    if (fontReset) {
        fontReset.addEventListener('click', function () {
            fontSize = 16; document.documentElement.style.fontSize = '16px'; showToast('অক্ষরের আকার স্বাভাবিক করা হয়েছে', 'info');
        });
    }


    // ============================================
    // COUNTER ANIMATION
    // ============================================
    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-target'));
        var duration = 2000;
        var step = target / (duration / 16);
        var current = 0;
        function update() {
            current += step;
            if (current >= target) { el.textContent = target.toLocaleString('bn-BD'); return; }
            el.textContent = Math.floor(current).toLocaleString('bn-BD');
            requestAnimationFrame(update);
        }
        update();
    }

    var statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
        var counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { animateCounter(entry.target); counterObserver.unobserve(entry.target); }
            });
        }, { threshold: 0.5 });
        statNumbers.forEach(function (el) { counterObserver.observe(el); });
    } else {
        statNumbers.forEach(function (el) { animateCounter(el); });
    }

    // ============================================
    // HEADER LOGO UPLOAD
    // ============================================
    var headerLogoInput = document.getElementById('headerLogoInput');
    var headerLogo = document.getElementById('headerLogo');

    if (headerLogoInput && headerLogo) {
        headerLogoInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) { showToast('শুধুমাত্র ছবি ফাইল আপলোড করুন', 'error'); return; }
            var reader = new FileReader();
            reader.onload = function (ev) {
                headerLogo.src = ev.target.result;
                localStorage.setItem('dpdt_header_logo', ev.target.result);
                showToast('হেডার লোগো সফলভাবে আপডেট হয়েছে', 'success');
            };
            reader.readAsDataURL(file);
        });
    }

    var savedHeaderLogo = localStorage.getItem('dpdt_header_logo');
    if (savedHeaderLogo && headerLogo) headerLogo.src = savedHeaderLogo;


    // ============================================
    // LINK CARD LOGO UPLOAD
    // ============================================
    function setupLogoUpload(card) {
        var btn = card.querySelector('.logo-upload-btn');
        var input = card.querySelector('.logo-file-input');
        var img = card.querySelector('.link-logo-img');
        if (!btn || !input || !img) return;

        btn.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); input.click(); });
        input.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) { showToast('শুধুমাত্র ছবি ফাইল আপলোড করুন', 'error'); return; }
            var reader = new FileReader();
            reader.onload = function (ev) {
                img.src = ev.target.result;
                var key = img.getAttribute('data-logo-key');
                if (key) localStorage.setItem('dpdt_logo_' + key, ev.target.result);
                showToast('লোগো সফলভাবে আপলোড হয়েছে', 'success');
            };
            reader.readAsDataURL(file);
        });
    }

    var linkCards = document.querySelectorAll('.link-card');
    linkCards.forEach(function (card) { setupLogoUpload(card); });

    function loadSavedLogos() {
        var imgs = document.querySelectorAll('.link-logo-img');
        imgs.forEach(function (img) {
            var key = img.getAttribute('data-logo-key');
            if (key) { var saved = localStorage.getItem('dpdt_logo_' + key); if (saved) img.src = saved; }
        });
    }
    loadSavedLogos();

    // ============================================
    // ADD NEW LINK MODAL
    // ============================================
    var addLinkBtn = document.getElementById('addLinkBtn');
    var addLinkModal = document.getElementById('addLinkModal');
    var modalClose = document.getElementById('modalClose');
    var modalCancelBtn = document.getElementById('modalCancelBtn');
    var modalSaveBtn = document.getElementById('modalSaveBtn');
    var linkLogoFile = document.getElementById('linkLogoFile');
    var logoPreviewImg = document.getElementById('logoPreviewImg');
    var linksGrid = document.getElementById('linksGrid');
    var newLinkLogoData = '';

    function openModal() { if (addLinkModal) { addLinkModal.classList.add('active'); document.body.style.overflow = 'hidden'; } }
    function closeModal() {
        if (addLinkModal) {
            addLinkModal.classList.remove('active'); document.body.style.overflow = '';
            document.getElementById('linkName').value = '';
            document.getElementById('linkUrl').value = '';
            if (linkLogoFile) linkLogoFile.value = '';
            if (logoPreviewImg) { logoPreviewImg.classList.remove('visible'); logoPreviewImg.src = ''; }
            newLinkLogoData = '';
        }
    }

    if (addLinkBtn) addLinkBtn.addEventListener('click', openModal);
    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);
    if (addLinkModal) addLinkModal.addEventListener('click', function (e) { if (e.target === addLinkModal) closeModal(); });

    if (linkLogoFile) {
        linkLogoFile.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (ev) { newLinkLogoData = ev.target.result; if (logoPreviewImg) { logoPreviewImg.src = ev.target.result; logoPreviewImg.classList.add('visible'); } };
            reader.readAsDataURL(file);
        });
    }


    if (modalSaveBtn) {
        modalSaveBtn.addEventListener('click', function () {
            var linkName = document.getElementById('linkName').value.trim();
            var linkUrl = document.getElementById('linkUrl').value.trim();
            if (!linkName) { showToast('লিংকের নাম লিখুন', 'error'); return; }
            if (!linkUrl) { showToast('ওয়েবসাইট URL লিখুন', 'error'); return; }

            var linkId = 'custom_' + Date.now();
            var card = document.createElement('div');
            card.className = 'link-card';
            card.setAttribute('data-link-id', linkId);
            var logoSrc = newLinkLogoData || 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Government_Seal_of_Bangladesh.svg/60px-Government_Seal_of_Bangladesh.svg.png';
            card.innerHTML =
                '<button class="link-delete-btn" data-delete-id="' + linkId + '"><i class="fas fa-trash"></i></button>' +
                '<div class="link-logo-wrapper"><img src="' + logoSrc + '" alt="Logo" class="link-logo-img" data-logo-key="' + linkId + '"><button class="logo-upload-btn" data-target="' + linkId + '"><i class="fas fa-camera"></i></button><input type="file" class="logo-file-input" id="logoInput-' + linkId + '" accept="image/*" hidden></div>' +
                '<h4 class="link-title">' + linkName + '</h4>' +
                '<a href="' + linkUrl + '" target="_blank" class="link-url">' + linkUrl.replace(/https?:\/\//, '') + '</a>';

            linksGrid.appendChild(card);
            setupLogoUpload(card);
            var deleteBtn = card.querySelector('.link-delete-btn');
            if (deleteBtn) deleteBtn.addEventListener('click', function () { deleteCustomLink(linkId); });

            if (newLinkLogoData) localStorage.setItem('dpdt_logo_' + linkId, newLinkLogoData);
            var customLinks = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
            customLinks.push({ id: linkId, name: linkName, url: linkUrl, logo: newLinkLogoData || '' });
            localStorage.setItem('dpdt_custom_links', JSON.stringify(customLinks));

            closeModal();
            showToast('নতুন লিংক সফলভাবে যোগ হয়েছে', 'success');
        });
    }

    function deleteCustomLink(linkId) {
        var card = document.querySelector('[data-link-id="' + linkId + '"]');
        if (card) { card.style.transform = 'scale(0.8)'; card.style.opacity = '0'; setTimeout(function () { card.remove(); }, 300); }
        localStorage.removeItem('dpdt_logo_' + linkId);
        var customLinks = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        customLinks = customLinks.filter(function (link) { return link.id !== linkId; });
        localStorage.setItem('dpdt_custom_links', JSON.stringify(customLinks));
        showToast('লিংক সফলভাবে মুছে ফেলা হয়েছে', 'success');
    }

    function loadCustomLinks() {
        var customLinks = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        customLinks.forEach(function (linkData) {
            var card = document.createElement('div');
            card.className = 'link-card';
            card.setAttribute('data-link-id', linkData.id);
            var logoSrc = localStorage.getItem('dpdt_logo_' + linkData.id) || linkData.logo || 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Government_Seal_of_Bangladesh.svg/60px-Government_Seal_of_Bangladesh.svg.png';
            card.innerHTML =
                '<button class="link-delete-btn" data-delete-id="' + linkData.id + '"><i class="fas fa-trash"></i></button>' +
                '<div class="link-logo-wrapper"><img src="' + logoSrc + '" alt="Logo" class="link-logo-img" data-logo-key="' + linkData.id + '"><button class="logo-upload-btn" data-target="' + linkData.id + '"><i class="fas fa-camera"></i></button><input type="file" class="logo-file-input" id="logoInput-' + linkData.id + '" accept="image/*" hidden></div>' +
                '<h4 class="link-title">' + linkData.name + '</h4>' +
                '<a href="' + linkData.url + '" target="_blank" class="link-url">' + linkData.url.replace(/https?:\/\//, '') + '</a>';
            if (linksGrid) linksGrid.appendChild(card);
            setupLogoUpload(card);
            var deleteBtn = card.querySelector('.link-delete-btn');
            if (deleteBtn) deleteBtn.addEventListener('click', function () { deleteCustomLink(linkData.id); });
        });
    }
    loadCustomLinks();


    // ============================================
    // TRADEMARK REGISTRATION FORM
    // ============================================
    var trademarkForm = document.getElementById('trademark-application-form');
    var trademarkSuccess = document.getElementById('trademark-form-success');
    var newAppBtn = document.getElementById('newApplicationBtn');
    var brandLogoInput = document.getElementById('brand_logo');
    var brandLogoPreview = document.getElementById('brand-logo-preview');

    // Logo preview
    if (brandLogoInput && brandLogoPreview) {
        brandLogoInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function (ev) {
                brandLogoPreview.innerHTML = '<img src="' + ev.target.result + '" alt="Logo Preview">';
            };
            reader.readAsDataURL(file);
        });
    }

    if (trademarkForm) {
        trademarkForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validation
            var name = document.getElementById('applicant_name').value.trim();
            var email = document.getElementById('applicant_email').value.trim();
            var phone = document.getElementById('applicant_phone').value.trim();
            var brand = document.getElementById('brand_name').value.trim();
            var tmClass = document.getElementById('trademark_class').value;

            if (!name || !email || !phone || !brand || !tmClass) {
                showToast('অনুগ্রহ করে সকল বাধ্যতামূলক (*) ঘর পূরণ করুন', 'error');
                // Highlight errors
                if (!name) document.getElementById('applicant_name').classList.add('error');
                if (!email) document.getElementById('applicant_email').classList.add('error');
                if (!phone) document.getElementById('applicant_phone').classList.add('error');
                if (!brand) document.getElementById('brand_name').classList.add('error');
                if (!tmClass) document.getElementById('trademark_class').classList.add('error');
                return;
            }

            // Show loading
            var submitBtn = document.getElementById('trademark-submit-btn');
            submitBtn.querySelector('.btn-text').style.display = 'none';
            submitBtn.querySelector('.btn-loading').style.display = 'inline-flex';
            submitBtn.disabled = true;

            // Simulate submission
            setTimeout(function () {
                // Generate certificate number
                var certNum = 'DPDT-TM-' + new Date().getFullYear() + '-' + String(Math.floor(Math.random() * 999999)).padStart(6, '0');

                // Save to localStorage
                var applications = JSON.parse(localStorage.getItem('dpdt_applications') || '[]');
                var appData = {
                    id: certNum,
                    applicant_name: name,
                    applicant_email: email,
                    applicant_phone: phone,
                    brand_name: brand,
                    trademark_class: tmClass,
                    trademark_type: document.getElementById('trademark_type').value,
                    owner_name: document.getElementById('owner_name').value.trim(),
                    company_name: document.getElementById('company_name').value.trim(),
                    status: 'pending',
                    date: new Date().toISOString(),
                    approved_date: ''
                };
                applications.push(appData);
                localStorage.setItem('dpdt_applications', JSON.stringify(applications));

                // Show success
                trademarkForm.style.display = 'none';
                trademarkSuccess.style.display = 'block';
                document.getElementById('trademark-success-message').textContent = 'আপনার ট্রেডমার্ক আবেদন সফলভাবে জমা হয়েছে।';
                document.getElementById('trademark-success-details').textContent = 'সার্টিফিকেট নম্বর: ' + certNum;

                // Reset button
                submitBtn.querySelector('.btn-text').style.display = 'inline-flex';
                submitBtn.querySelector('.btn-loading').style.display = 'none';
                submitBtn.disabled = false;

                showToast('আবেদন সফলভাবে জমা হয়েছে!', 'success');
            }, 2000);
        });
    }

    // Clear error on input
    document.querySelectorAll('.dpdt-form-group input, .dpdt-form-group select').forEach(function (input) {
        input.addEventListener('focus', function () { this.classList.remove('error'); });
    });

    // New application button
    if (newAppBtn) {
        newAppBtn.addEventListener('click', function () {
            trademarkForm.style.display = 'block';
            trademarkSuccess.style.display = 'none';
            trademarkForm.reset();
            if (brandLogoPreview) brandLogoPreview.innerHTML = '';
        });
    }


    // ============================================
    // VERIFY CERTIFICATE
    // ============================================
    var verifyForm = document.getElementById('verify-form');
    var verifyResult = document.getElementById('verify-result');

    if (verifyForm) {
        verifyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var input = document.getElementById('verify-input').value.trim();
            if (!input) { showToast('সার্টিফিকেট নম্বর লিখুন', 'error'); return; }

            // Show loading
            var verifyBtn = document.getElementById('verify-btn');
            verifyBtn.querySelector('.btn-text').style.display = 'none';
            verifyBtn.querySelector('.btn-loading').style.display = 'inline-flex';
            verifyBtn.disabled = true;

            setTimeout(function () {
                // Search in localStorage
                var applications = JSON.parse(localStorage.getItem('dpdt_applications') || '[]');
                var found = applications.find(function (app) { return app.id === input; });

                verifyResult.style.display = 'block';

                if (found) {
                    var statusText = found.status === 'approved' ? 'অনুমোদিত' : (found.status === 'pending' ? 'প্রক্রিয়াধীন' : 'প্রত্যাখ্যাত');
                    var appDate = new Date(found.date).toLocaleDateString('bn-BD');
                    verifyResult.innerHTML =
                        '<div class="verify-card">' +
                            '<div class="verify-badge valid">' +
                                '<div class="verify-badge-icon"><i class="fas fa-check"></i></div>' +
                                '<h3>সার্টিফিকেট যাচাই সম্পন্ন</h3>' +
                                '<p>এটি একটি বৈধ ট্রেডমার্ক আবেদন</p>' +
                            '</div>' +
                            '<div class="verify-details">' +
                                '<table class="verify-table">' +
                                    '<tr><th>সার্টিফিকেট নম্বর</th><td>' + found.id + '</td></tr>' +
                                    '<tr><th>ব্র্যান্ড নাম</th><td>' + found.brand_name + '</td></tr>' +
                                    '<tr><th>আবেদনকারী</th><td>' + found.applicant_name + '</td></tr>' +
                                    '<tr><th>প্রতিষ্ঠান</th><td>' + (found.company_name || 'N/A') + '</td></tr>' +
                                    '<tr><th>ট্রেডমার্ক শ্রেণী</th><td>Class ' + found.trademark_class + '</td></tr>' +
                                    '<tr><th>আবেদনের তারিখ</th><td>' + appDate + '</td></tr>' +
                                    '<tr><th>স্ট্যাটাস</th><td><span style="color:#27ae60;font-weight:700;">' + statusText + '</span></td></tr>' +
                                '</table>' +
                            '</div>' +
                            '<div class="verify-footer">' +
                                '<p>যাচাইকৃত: ' + new Date().toLocaleDateString('bn-BD') + '</p>' +
                                '<p style="font-weight:600;color:#1a5276;">পেটেন্ট, ডিজাইন ও ট্রেডমার্কস অধিদপ্তর</p>' +
                            '</div>' +
                        '</div>';
                } else {
                    verifyResult.innerHTML =
                        '<div class="verify-card">' +
                            '<div class="verify-badge invalid">' +
                                '<div class="verify-badge-icon"><i class="fas fa-times"></i></div>' +
                                '<h3>অবৈধ সার্টিফিকেট</h3>' +
                                '<p>এই তথ্য দিয়ে কোনো বৈধ সার্টিফিকেট পাওয়া যায়নি।</p>' +
                            '</div>' +
                        '</div>';
                }

                // Reset button
                verifyBtn.querySelector('.btn-text').style.display = 'inline-flex';
                verifyBtn.querySelector('.btn-loading').style.display = 'none';
                verifyBtn.disabled = false;
            }, 1500);
        });
    }


    // ============================================
    // LOGIN FUNCTIONALITY
    // ============================================
    var loginForm = document.getElementById('login-form');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var email = document.getElementById('login_email').value.trim();
            var password = document.getElementById('login_password').value.trim();

            if (!email || !password) { showToast('ইমেইল ও পাসওয়ার্ড দিন', 'error'); return; }

            // Show loading
            var btn = loginForm.querySelector('button[type="submit"]');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loading').style.display = 'inline-flex';
            btn.disabled = true;

            setTimeout(function () {
                // Check localStorage for registered users
                var users = JSON.parse(localStorage.getItem('dpdt_users') || '[]');
                var user = users.find(function (u) { return u.email === email && u.password === password; });

                if (user) {
                    localStorage.setItem('dpdt_current_user', JSON.stringify(user));
                    showToast('সফলভাবে লগইন হয়েছে! স্বাগতম, ' + user.name, 'success');
                    updateAuthUI();
                    navigateTo('home');
                } else {
                    showToast('ইমেইল বা পাসওয়ার্ড ভুল হয়েছে', 'error');
                }

                btn.querySelector('.btn-text').style.display = 'inline-flex';
                btn.querySelector('.btn-loading').style.display = 'none';
                btn.disabled = false;
            }, 1500);
        });
    }

    // ============================================
    // REGISTER FUNCTIONALITY
    // ============================================
    var registerForm = document.getElementById('register-form');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var name = document.getElementById('reg_name').value.trim();
            var phone = document.getElementById('reg_phone').value.trim();
            var email = document.getElementById('reg_email').value.trim();
            var password = document.getElementById('reg_password').value;
            var confirmPass = document.getElementById('reg_confirm_password').value;
            var org = document.getElementById('reg_org').value.trim();

            if (!name || !phone || !email || !password || !confirmPass) {
                showToast('সকল বাধ্যতামূলক ঘর পূরণ করুন', 'error'); return;
            }
            if (password.length < 6) {
                showToast('পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে', 'error'); return;
            }
            if (password !== confirmPass) {
                showToast('পাসওয়ার্ড মিলছে না', 'error'); return;
            }

            // Show loading
            var btn = registerForm.querySelector('button[type="submit"]');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loading').style.display = 'inline-flex';
            btn.disabled = true;

            setTimeout(function () {
                var users = JSON.parse(localStorage.getItem('dpdt_users') || '[]');
                // Check if email already exists
                var existing = users.find(function (u) { return u.email === email; });
                if (existing) {
                    showToast('এই ইমেইল দিয়ে ইতিমধ্যে রেজিস্টার করা হয়েছে', 'error');
                } else {
                    var newUser = { name: name, phone: phone, email: email, password: password, organization: org, date: new Date().toISOString() };
                    users.push(newUser);
                    localStorage.setItem('dpdt_users', JSON.stringify(users));
                    localStorage.setItem('dpdt_current_user', JSON.stringify(newUser));
                    showToast('সফলভাবে রেজিস্ট্রেশন সম্পন্ন হয়েছে!', 'success');
                    updateAuthUI();
                    navigateTo('home');
                }

                btn.querySelector('.btn-text').style.display = 'inline-flex';
                btn.querySelector('.btn-loading').style.display = 'none';
                btn.disabled = false;
            }, 1500);
        });
    }


    // ============================================
    // AUTH UI UPDATE (Show/Hide login/register buttons)
    // ============================================
    function updateAuthUI() {
        var authBtns = document.querySelector('.user-auth-btns');
        var currentUser = JSON.parse(localStorage.getItem('dpdt_current_user') || 'null');

        if (currentUser && authBtns) {
            authBtns.innerHTML =
                '<span class="user-logged-in"><i class="fas fa-user-circle"></i> <span class="user-name">' + currentUser.name + '</span></span>' +
                '<button class="acc-btn logout-btn" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> লগআউট</button>';

            document.getElementById('logoutBtn').addEventListener('click', function () {
                localStorage.removeItem('dpdt_current_user');
                showToast('সফলভাবে লগআউট হয়েছে', 'success');
                updateAuthUI();
                navigateTo('home');
            });
        } else if (authBtns) {
            authBtns.innerHTML =
                '<button class="acc-btn auth-btn" id="loginNavBtn"><i class="fas fa-sign-in-alt"></i> লগইন</button>' +
                '<button class="acc-btn auth-btn" id="registerNavBtn"><i class="fas fa-user-plus"></i> রেজিস্টার</button>';

            document.getElementById('loginNavBtn').addEventListener('click', function () { navigateTo('login'); });
            document.getElementById('registerNavBtn').addEventListener('click', function () { navigateTo('register'); });
        }
    }

    // Initial auth state check
    updateAuthUI();

    // ============================================
    // PASSWORD TOGGLE
    // ============================================
    document.querySelectorAll('.toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }
        });
    });

    // ============================================
    // BACK TO TOP BUTTON
    // ============================================
    var backToTopBtn = document.createElement('button');
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTopBtn.setAttribute('title', 'উপরে যান');
    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', function () {
        if (window.scrollY > 400) backToTopBtn.classList.add('visible');
        else backToTopBtn.classList.remove('visible');
    });

    backToTopBtn.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

    // ============================================
    // KEYBOARD SHORTCUT: ESC closes modal
    // ============================================
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

})();
