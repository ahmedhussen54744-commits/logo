/**
 * DPDT Trademark System - Main JavaScript
 * Since: 2009 | Advanced Interactive Features
 */

(function() {
    'use strict';

    // ===== SLIDER =====
    const slider = {
        slides: document.querySelectorAll('.hero-slide'),
        indicators: document.querySelector('.slider-indicators'),
        current: 0,
        interval: null,

        init() {
            if (!this.slides.length) return;
            this.createDots();
            this.start();
            this.bindEvents();
        },

        createDots() {
            if (!this.indicators) return;
            this.slides.forEach((_, i) => {
                const dot = document.createElement('span');
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => this.goTo(i));
                this.indicators.appendChild(dot);
            });
        },

        goTo(index) {
            this.slides[this.current].classList.remove('active');
            const dots = this.indicators?.querySelectorAll('span');
            if (dots) dots[this.current]?.classList.remove('active');
            this.current = index;
            this.slides[this.current].classList.add('active');
            if (dots) dots[this.current]?.classList.add('active');
        },

        next() { this.goTo((this.current + 1) % this.slides.length); },
        prev() { this.goTo((this.current - 1 + this.slides.length) % this.slides.length); },
        start() { this.interval = setInterval(() => this.next(), 5000); },
        stop() { clearInterval(this.interval); },

        bindEvents() {
            const prevBtn = document.querySelector('.slider-nav.prev');
            const nextBtn = document.querySelector('.slider-nav.next');
            if (prevBtn) prevBtn.addEventListener('click', () => { this.stop(); this.prev(); this.start(); });
            if (nextBtn) nextBtn.addEventListener('click', () => { this.stop(); this.next(); this.start(); });
        }
    };

    // ===== MOBILE MENU =====
    const mobileMenu = {
        init() {
            const toggle = document.querySelector('.mobile-toggle');
            const nav = document.querySelector('.nav-list');
            if (!toggle || !nav) return;
            toggle.addEventListener('click', () => nav.classList.toggle('active'));
            document.addEventListener('click', (e) => {
                if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                    nav.classList.remove('active');
                }
            });
        }
    };

    // ===== BACK TO TOP =====
    const backToTop = {
        init() {
            const btn = document.querySelector('.back-to-top');
            if (!btn) return;
            window.addEventListener('scroll', () => {
                btn.classList.toggle('visible', window.scrollY > 300);
            });
            btn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
    };

    // ===== COUNTER ANIMATION =====
    const counters = {
        animated: false,
        init() {
            const section = document.querySelector('.stats-section');
            if (!section) return;
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.animated) {
                        this.animated = true;
                        this.animate();
                    }
                });
            }, { threshold: 0.4 });
            observer.observe(section);
        },
        animate() {
            document.querySelectorAll('.stat-box .number').forEach(el => {
                const target = parseInt(el.dataset.target) || 0;
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
    };

    // ===== INIT ALL =====
    document.addEventListener('DOMContentLoaded', () => {
        slider.init();
        mobileMenu.init();
        backToTop.init();
        counters.init();
    });

})();
