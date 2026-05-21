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

        setTimeout(function () {
            toast.classList.add('show');
        }, 50);

        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () {
                if (toast.parentNode) toast.remove();
            }, 400);
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

        // Create dots
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

    function nextSlide() {
        goToSlide(currentSlide + 1);
    }

    function prevSlide() {
        goToSlide(currentSlide - 1);
    }

    function startAutoPlay() {
        slideInterval = setInterval(nextSlide, 4000);
    }

    function stopAutoPlay() {
        clearInterval(slideInterval);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            stopAutoPlay();
            prevSlide();
            startAutoPlay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            stopAutoPlay();
            nextSlide();
            startAutoPlay();
        });
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
            if (navMenu.classList.contains('active')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        });

        // Mobile dropdown toggle
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
            if (fontSize < 22) {
                fontSize += 1;
                document.documentElement.style.fontSize = fontSize + 'px';
                showToast('অক্ষরের আকার বৃদ্ধি করা হয়েছে', 'info');
            }
        });
    }

    if (fontDecrease) {
        fontDecrease.addEventListener('click', function () {
            if (fontSize > 12) {
                fontSize -= 1;
                document.documentElement.style.fontSize = fontSize + 'px';
                showToast('অক্ষরের আকার হ্রাস করা হয়েছে', 'info');
            }
        });
    }

    if (fontReset) {
        fontReset.addEventListener('click', function () {
            fontSize = 16;
            document.documentElement.style.fontSize = '16px';
            showToast('অক্ষরের আকার স্বাভাবিক করা হয়েছে', 'info');
        });
    }


    // ============================================
    // COUNTER ANIMATION WITH INTERSECTION OBSERVER
    // ============================================
    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-target'));
        var duration = 2000;
        var step = target / (duration / 16);
        var current = 0;

        function update() {
            current += step;
            if (current >= target) {
                current = target;
                el.textContent = target.toLocaleString('bn-BD');
                return;
            }
            el.textContent = Math.floor(current).toLocaleString('bn-BD');
            requestAnimationFrame(update);
        }
        update();
    }

    var statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0 && 'IntersectionObserver' in window) {
        var counterObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(function (el) {
            counterObserver.observe(el);
        });
    } else {
        // Fallback: animate all immediately
        statNumbers.forEach(function (el) {
            animateCounter(el);
        });
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

            if (!file.type.startsWith('image/')) {
                showToast('শুধুমাত্র ছবি ফাইল আপলোড করুন', 'error');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                headerLogo.src = ev.target.result;
                localStorage.setItem('dpdt_header_logo', ev.target.result);
                showToast('হেডার লোগো সফলভাবে আপডেট হয়েছে', 'success');
            };
            reader.readAsDataURL(file);
        });
    }

    // Load saved header logo
    var savedHeaderLogo = localStorage.getItem('dpdt_header_logo');
    if (savedHeaderLogo && headerLogo) {
        headerLogo.src = savedHeaderLogo;
    }

    // ============================================
    // LINK CARD LOGO UPLOAD
    // ============================================
    function setupLogoUpload(card) {
        var btn = card.querySelector('.logo-upload-btn');
        var input = card.querySelector('.logo-file-input');
        var img = card.querySelector('.link-logo-img');
        if (!btn || !input || !img) return;

        btn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            input.click();
        });

        input.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                showToast('শুধুমাত্র ছবি ফাইল আপলোড করুন', 'error');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                img.src = ev.target.result;
                var key = img.getAttribute('data-logo-key');
                if (key) {
                    localStorage.setItem('dpdt_logo_' + key, ev.target.result);
                }
                showToast('লোগো সফলভাবে আপলোড হয়েছে', 'success');
            };
            reader.readAsDataURL(file);
        });
    }

    // Initialize logo uploads for existing cards
    var linkCards = document.querySelectorAll('.link-card');
    linkCards.forEach(function (card) {
        setupLogoUpload(card);
    });

    // Load saved logos from localStorage
    function loadSavedLogos() {
        var imgs = document.querySelectorAll('.link-logo-img');
        imgs.forEach(function (img) {
            var key = img.getAttribute('data-logo-key');
            if (key) {
                var saved = localStorage.getItem('dpdt_logo_' + key);
                if (saved) {
                    img.src = saved;
                }
            }
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

    function openModal() {
        if (addLinkModal) {
            addLinkModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal() {
        if (addLinkModal) {
            addLinkModal.classList.remove('active');
            document.body.style.overflow = '';
            // Reset form
            document.getElementById('linkName').value = '';
            document.getElementById('linkUrl').value = '';
            if (linkLogoFile) linkLogoFile.value = '';
            if (logoPreviewImg) {
                logoPreviewImg.classList.remove('visible');
                logoPreviewImg.src = '';
            }
            newLinkLogoData = '';
        }
    }

    if (addLinkBtn) addLinkBtn.addEventListener('click', openModal);
    if (modalClose) modalClose.addEventListener('click', closeModal);
    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);

    // Close modal on overlay click
    if (addLinkModal) {
        addLinkModal.addEventListener('click', function (e) {
            if (e.target === addLinkModal) closeModal();
        });
    }

    // Logo preview in modal
    if (linkLogoFile) {
        linkLogoFile.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                showToast('শুধুমাত্র ছবি ফাইল আপলোড করুন', 'error');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                newLinkLogoData = ev.target.result;
                if (logoPreviewImg) {
                    logoPreviewImg.src = ev.target.result;
                    logoPreviewImg.classList.add('visible');
                }
            };
            reader.readAsDataURL(file);
        });
    }


    // Save new link
    if (modalSaveBtn) {
        modalSaveBtn.addEventListener('click', function () {
            var linkName = document.getElementById('linkName').value.trim();
            var linkUrl = document.getElementById('linkUrl').value.trim();

            if (!linkName) {
                showToast('লিংকের নাম লিখুন', 'error');
                return;
            }
            if (!linkUrl) {
                showToast('ওয়েবসাইট URL লিখুন', 'error');
                return;
            }

            // Generate unique ID
            var linkId = 'custom_' + Date.now();

            // Create card element
            var card = document.createElement('div');
            card.className = 'link-card';
            card.setAttribute('data-link-id', linkId);

            var logoSrc = newLinkLogoData || 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Government_Seal_of_Bangladesh.svg/60px-Government_Seal_of_Bangladesh.svg.png';

            card.innerHTML =
                '<button class="link-delete-btn" data-delete-id="' + linkId + '"><i class="fas fa-trash"></i></button>' +
                '<div class="link-logo-wrapper">' +
                    '<img src="' + logoSrc + '" alt="Logo" class="link-logo-img" data-logo-key="' + linkId + '">' +
                    '<button class="logo-upload-btn" data-target="' + linkId + '"><i class="fas fa-camera"></i></button>' +
                    '<input type="file" class="logo-file-input" id="logoInput-' + linkId + '" accept="image/*" hidden>' +
                '</div>' +
                '<h4 class="link-title">' + linkName + '</h4>' +
                '<a href="' + linkUrl + '" target="_blank" class="link-url">' + linkUrl.replace(/https?:\/\//, '') + '</a>';

            linksGrid.appendChild(card);

            // Setup logo upload for new card
            setupLogoUpload(card);

            // Setup delete for new card
            var deleteBtn = card.querySelector('.link-delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    deleteCustomLink(linkId);
                });
            }

            // Save to localStorage
            if (newLinkLogoData) {
                localStorage.setItem('dpdt_logo_' + linkId, newLinkLogoData);
            }

            // Save link data
            var customLinks = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
            customLinks.push({
                id: linkId,
                name: linkName,
                url: linkUrl,
                logo: newLinkLogoData || ''
            });
            localStorage.setItem('dpdt_custom_links', JSON.stringify(customLinks));

            closeModal();
            showToast('নতুন লিংক সফলভাবে যোগ হয়েছে', 'success');
        });
    }


    // ============================================
    // DELETE CUSTOM LINKS
    // ============================================
    function deleteCustomLink(linkId) {
        var card = document.querySelector('[data-link-id="' + linkId + '"]');
        if (card) {
            card.style.transform = 'scale(0.8)';
            card.style.opacity = '0';
            setTimeout(function () {
                card.remove();
            }, 300);
        }

        // Remove from localStorage
        localStorage.removeItem('dpdt_logo_' + linkId);
        var customLinks = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        customLinks = customLinks.filter(function (link) {
            return link.id !== linkId;
        });
        localStorage.setItem('dpdt_custom_links', JSON.stringify(customLinks));

        showToast('লিংক সফলভাবে মুছে ফেলা হয়েছে', 'success');
    }

    // ============================================
    // LOAD CUSTOM LINKS FROM LOCALSTORAGE
    // ============================================
    function loadCustomLinks() {
        var customLinks = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        customLinks.forEach(function (linkData) {
            var card = document.createElement('div');
            card.className = 'link-card';
            card.setAttribute('data-link-id', linkData.id);

            var logoSrc = linkData.logo || 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Government_Seal_of_Bangladesh.svg/60px-Government_Seal_of_Bangladesh.svg.png';
            var savedLogo = localStorage.getItem('dpdt_logo_' + linkData.id);
            if (savedLogo) logoSrc = savedLogo;

            card.innerHTML =
                '<button class="link-delete-btn" data-delete-id="' + linkData.id + '"><i class="fas fa-trash"></i></button>' +
                '<div class="link-logo-wrapper">' +
                    '<img src="' + logoSrc + '" alt="Logo" class="link-logo-img" data-logo-key="' + linkData.id + '">' +
                    '<button class="logo-upload-btn" data-target="' + linkData.id + '"><i class="fas fa-camera"></i></button>' +
                    '<input type="file" class="logo-file-input" id="logoInput-' + linkData.id + '" accept="image/*" hidden>' +
                '</div>' +
                '<h4 class="link-title">' + linkData.name + '</h4>' +
                '<a href="' + linkData.url + '" target="_blank" class="link-url">' + linkData.url.replace(/https?:\/\//, '') + '</a>';

            if (linksGrid) linksGrid.appendChild(card);

            setupLogoUpload(card);

            var deleteBtn = card.querySelector('.link-delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function () {
                    deleteCustomLink(linkData.id);
                });
            }
        });
    }
    loadCustomLinks();


    // ============================================
    // BACK TO TOP BUTTON (Dynamic)
    // ============================================
    var backToTopBtn = document.createElement('button');
    backToTopBtn.className = 'back-to-top';
    backToTopBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTopBtn.setAttribute('title', 'উপরে যান');
    document.body.appendChild(backToTopBtn);

    window.addEventListener('scroll', function () {
        if (window.scrollY > 400) {
            backToTopBtn.classList.add('visible');
        } else {
            backToTopBtn.classList.remove('visible');
        }
    });

    backToTopBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ============================================
    // SMOOTH SCROLL FOR HASH LINKS
    // ============================================
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ============================================
    // KEYBOARD SHORTCUT: ESC closes modal
    // ============================================
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

})();
