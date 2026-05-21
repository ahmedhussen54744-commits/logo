/**
 * DPDT Clone - Complete JavaScript
 * Since: 2009 | All Categories Functional
 */
(function() {
    'use strict';

    // ==================== SLIDER ====================
    const slides = document.querySelectorAll('.slide');
    const sliderDotsContainer = document.getElementById('sliderDots');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');
    let currentSlide = 0;
    let slideInterval;

    if (slides.length) {
        // Create dots
        slides.forEach((_, i) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(i));
            sliderDotsContainer.appendChild(dot);
        });

        function goToSlide(index) {
            slides[currentSlide].classList.remove('active');
            const dots = sliderDotsContainer.querySelectorAll('.dot');
            dots[currentSlide].classList.remove('active');
            currentSlide = index;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() { goToSlide((currentSlide + 1) % slides.length); }
        function prevSlideF() { goToSlide((currentSlide - 1 + slides.length) % slides.length); }

        function startSlider() { slideInterval = setInterval(nextSlide, 5000); }
        function stopSlider() { clearInterval(slideInterval); }

        prevBtn.addEventListener('click', () => { stopSlider(); prevSlideF(); startSlider(); });
        nextBtn.addEventListener('click', () => { stopSlider(); nextSlide(); startSlider(); });
        startSlider();
    }


    // ==================== MOBILE MENU ====================
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');

    if (mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', () => navMenu.classList.toggle('active'));
        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                navMenu.classList.remove('active');
            }
        });
    }

    // ==================== FONT SIZE ====================
    const fontIncrease = document.getElementById('fontIncrease');
    const fontDecrease = document.getElementById('fontDecrease');
    const fontReset = document.getElementById('fontReset');
    let currentFontSize = 100;

    if (fontIncrease) {
        fontIncrease.addEventListener('click', () => {
            if (currentFontSize < 130) { currentFontSize += 10; document.body.style.fontSize = currentFontSize + '%'; }
        });
        fontDecrease.addEventListener('click', () => {
            if (currentFontSize > 80) { currentFontSize -= 10; document.body.style.fontSize = currentFontSize + '%'; }
        });
        fontReset.addEventListener('click', () => { currentFontSize = 100; document.body.style.fontSize = '100%'; });
    }

    // ==================== COUNTER ANIMATION ====================
    function animateCounters() {
        document.querySelectorAll('.stat-number').forEach(el => {
            const target = parseInt(el.getAttribute('data-target')) || 0;
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;
            const update = () => {
                current += step;
                if (current < target) {
                    el.textContent = Math.floor(current).toLocaleString('bn-BD');
                    requestAnimationFrame(update);
                } else {
                    el.textContent = target.toLocaleString('bn-BD');
                }
            };
            update();
        });
    }

    const statsSection = document.querySelector('.statistics');
    let countersAnimated = false;
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !countersAnimated) {
                    countersAnimated = true;
                    animateCounters();
                }
            });
        }, { threshold: 0.4 });
        observer.observe(statsSection);
    }


    // ==================== LOGO UPLOAD FOR CATEGORIES ====================
    // Handle logo upload buttons on each link card
    document.querySelectorAll('.logo-upload-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const targetId = this.getAttribute('data-target');
            const fileInput = document.getElementById('file-' + targetId);
            if (fileInput) fileInput.click();
        });
    });

    // Handle file input change - preview logo immediately
    document.querySelectorAll('.logo-file-input').forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('অনুগ্রহ করে একটি ছবি ফাইল নির্বাচন করুন (JPG, PNG, GIF, SVG, WEBP)');
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('ফাইলের আকার ৫MB এর বেশি হতে পারবে না।');
                return;
            }

            // Get corresponding image element
            const targetId = this.id.replace('file-', '');
            const imgElement = document.getElementById('logo-' + targetId);
            
            if (imgElement) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    imgElement.src = ev.target.result;
                    // Save to localStorage for persistence
                    saveLogo(targetId, ev.target.result);
                    showToast('লোগো সফলভাবে আপডেট হয়েছে!');
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // ==================== SAVE/LOAD LOGOS (LocalStorage) ====================
    function saveLogo(id, dataUrl) {
        try {
            const logos = JSON.parse(localStorage.getItem('dpdt_logos') || '{}');
            logos[id] = dataUrl;
            localStorage.setItem('dpdt_logos', JSON.stringify(logos));
        } catch(e) {
            console.warn('Could not save logo to localStorage:', e);
        }
    }

    function loadSavedLogos() {
        try {
            const logos = JSON.parse(localStorage.getItem('dpdt_logos') || '{}');
            Object.keys(logos).forEach(id => {
                const img = document.getElementById('logo-' + id);
                if (img && logos[id]) {
                    img.src = logos[id];
                }
            });
        } catch(e) {
            console.warn('Could not load logos from localStorage:', e);
        }
    }

    // Load saved logos on page load
    loadSavedLogos();


    // ==================== ADD NEW LINK MODAL ====================
    const addNewLinkBtn = document.getElementById('addNewLink');
    const addLinkModal = document.getElementById('addLinkModal');
    const closeAddModal = document.getElementById('closeAddModal');
    const cancelAddModal = document.getElementById('cancelAddModal');
    const saveNewLink = document.getElementById('saveNewLink');
    const newLinkLogo = document.getElementById('newLinkLogo');
    const newLogoPreview = document.getElementById('newLogoPreview');
    const newLogoPreviewImg = document.getElementById('newLogoPreviewImg');

    let linkCounter = 7; // Starting after existing 6 links

    // Load saved custom links on page load
    loadCustomLinks();

    if (addNewLinkBtn) {
        addNewLinkBtn.addEventListener('click', () => {
            addLinkModal.style.display = 'flex';
        });
    }

    if (closeAddModal) closeAddModal.addEventListener('click', closeModal);
    if (cancelAddModal) cancelAddModal.addEventListener('click', closeModal);

    // Close modal on overlay click
    if (addLinkModal) {
        addLinkModal.addEventListener('click', (e) => {
            if (e.target === addLinkModal) closeModal();
        });
    }

    function closeModal() {
        addLinkModal.style.display = 'none';
        document.getElementById('newLinkName').value = '';
        document.getElementById('newLinkUrl').value = '';
        newLinkLogo.value = '';
        newLogoPreview.style.display = 'none';
    }

    // Preview new logo
    if (newLinkLogo) {
        newLinkLogo.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    newLogoPreviewImg.src = ev.target.result;
                    newLogoPreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Save new link
    if (saveNewLink) {
        saveNewLink.addEventListener('click', function() {
            const name = document.getElementById('newLinkName').value.trim();
            const url = document.getElementById('newLinkUrl').value.trim();
            const logoSrc = newLogoPreviewImg.src || 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Coat_of_arms_of_Bangladesh.svg/60px-Coat_of_arms_of_Bangladesh.svg.png';

            if (!name) { alert('অনুগ্রহ করে লিংকের নাম লিখুন।'); return; }
            if (!url) { alert('অনুগ্রহ করে URL লিখুন।'); return; }

            const linkId = 'link-' + linkCounter++;
            addLinkCard(linkId, name, url, logoSrc);

            // Save to localStorage
            saveCustomLink(linkId, name, url, logoSrc);
            showToast('নতুন লিংক সফলভাবে যোগ হয়েছে!');
            closeModal();
        });
    }

    function addLinkCard(id, name, url, logoSrc) {
        const linksGrid = document.getElementById('linksGrid');
        const card = document.createElement('div');
        card.className = 'link-card';
        card.setAttribute('data-id', id);
        card.innerHTML = `
            <div class="link-logo-wrapper">
                <img src="${logoSrc}" alt="${name}" class="link-logo-img" id="logo-${id}">
                <button class="logo-upload-btn" data-target="${id}" title="লোগো পরিবর্তন করুন"><i class="fas fa-camera"></i></button>
                <input type="file" class="logo-file-input" id="file-${id}" accept="image/*" hidden>
            </div>
            <a href="${url}" target="_blank" class="link-name">${name}</a>
            <button class="link-delete-btn" data-id="${id}" title="মুছে ফেলুন"><i class="fas fa-trash-alt"></i></button>
        `;
        linksGrid.appendChild(card);

        // Bind upload for new card
        const uploadBtn = card.querySelector('.logo-upload-btn');
        const fileInput = card.querySelector('.logo-file-input');

        uploadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                card.querySelector('.link-logo-img').src = ev.target.result;
                saveLogo(id, ev.target.result);
                showToast('লোগো আপডেট হয়েছে!');
            };
            reader.readAsDataURL(file);
        });

        // Delete button
        const deleteBtn = card.querySelector('.link-delete-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                if (confirm('এই লিংকটি মুছে ফেলতে চান?')) {
                    card.remove();
                    removeCustomLink(id);
                    showToast('লিংক মুছে ফেলা হয়েছে।');
                }
            });
        }
    }

    // ==================== LOCALSTORAGE FOR CUSTOM LINKS ====================
    function saveCustomLink(id, name, url, logoSrc) {
        const links = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        links.push({ id, name, url, logoSrc });
        localStorage.setItem('dpdt_custom_links', JSON.stringify(links));
    }

    function removeCustomLink(id) {
        let links = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        links = links.filter(l => l.id !== id);
        localStorage.setItem('dpdt_custom_links', JSON.stringify(links));
    }

    function loadCustomLinks() {
        const links = JSON.parse(localStorage.getItem('dpdt_custom_links') || '[]');
        links.forEach(link => {
            linkCounter = Math.max(linkCounter, parseInt(link.id.replace('link-', '')) + 1);
            addLinkCard(link.id, link.name, link.url, link.logoSrc);
        });
    }


    // ==================== TOAST NOTIFICATION ====================
    function showToast(message) {
        // Remove existing toast
        const existing = document.querySelector('.toast-notification');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #006a4e;
            color: #fff;
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Noto Sans Bengali', sans-serif;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            z-index: 999999;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: toastIn 0.3s ease;
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add toast animation CSS
    const toastStyle = document.createElement('style');
    toastStyle.textContent = `
        @keyframes toastIn { from { opacity: 0; transform: translateX(-50%) translateY(20px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
        .link-delete-btn { position: absolute; top: 6px; right: 6px; width: 22px; height: 22px; background: #f42a41; color: #fff; border: none; border-radius: 50%; font-size: 9px; cursor: pointer; opacity: 0; transition: all 0.3s; display: flex; align-items: center; justify-content: center; }
        .link-card:hover .link-delete-btn { opacity: 1; }
        .link-delete-btn:hover { background: #c0392b; transform: scale(1.15); }
    `;
    document.head.appendChild(toastStyle);

    // ==================== BACK TO TOP ====================
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="fas fa-chevron-up"></i>';
    backToTop.style.cssText = `
        position: fixed; bottom: 30px; right: 30px; width: 48px; height: 48px;
        background: #006a4e; color: #fff; border: none; border-radius: 50%;
        cursor: pointer; font-size: 18px; display: none; align-items: center;
        justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        transition: all 0.3s; z-index: 9999;
    `;
    document.body.appendChild(backToTop);

    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 300 ? 'flex' : 'none';
    });
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    backToTop.addEventListener('mouseenter', () => { backToTop.style.background = '#004d3a'; backToTop.style.transform = 'translateY(-3px)'; });
    backToTop.addEventListener('mouseleave', () => { backToTop.style.background = '#006a4e'; backToTop.style.transform = 'translateY(0)'; });

    // ==================== SMOOTH SCROLL FOR HASH LINKS ====================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                // Close mobile menu if open
                if (navMenu) navMenu.classList.remove('active');
            }
        });
    });

})();
