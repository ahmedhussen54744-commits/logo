/**
 * DPDT Theme - Main JavaScript
 * Version: 3.5.0
 */
(function($) {
    'use strict';

    var DPDTTheme = {
        init: function() {
            this.initScrollEffects();
            this.initAccordions();
            this.initBackToTop();
            this.initLazyLoad();
        },

        /**
         * Scroll effects - sticky header, animations
         */
        initScrollEffects: function() {
            var $header = $('.site-header');
            var lastScroll = 0;

            $(window).on('scroll', function() {
                var currentScroll = $(window).scrollTop();

                if (currentScroll > 100) {
                    $header.addClass('header-scrolled');
                } else {
                    $header.removeClass('header-scrolled');
                }

                lastScroll = currentScroll;
            });

            // Animate elements on scroll
            var observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('dpdt-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.dpdt-service-card, .link-card, .post-card, .section-card').forEach(function(el) {
                el.classList.add('dpdt-animate');
                observer.observe(el);
            });
        },

        /**
         * Accordions
         */
        initAccordions: function() {
            $(document).on('click', '.dpdt-accordion-header', function() {
                var $item = $(this).closest('.dpdt-accordion-item');
                var isActive = $item.hasClass('active');

                // Close all
                $item.siblings().removeClass('active').find('.dpdt-accordion-body').slideUp(300);

                // Toggle current
                if (!isActive) {
                    $item.addClass('active').find('.dpdt-accordion-body').slideDown(300);
                } else {
                    $item.removeClass('active').find('.dpdt-accordion-body').slideUp(300);
                }
            });
        },

        /**
         * Back to top button
         */
        initBackToTop: function() {
            var $btn = $('<button id="dpdt-back-to-top" class="dpdt-back-to-top" aria-label="Back to top"><span class="dashicons dashicons-arrow-up-alt2"></span></button>');
            $('body').append($btn);

            $(window).on('scroll', function() {
                if ($(window).scrollTop() > 400) {
                    $btn.addClass('visible');
                } else {
                    $btn.removeClass('visible');
                }
            });

            $btn.on('click', function() {
                $('html, body').animate({ scrollTop: 0 }, 500);
            });
        },

        /**
         * Lazy load images
         */
        initLazyLoad: function() {
            if ('IntersectionObserver' in window) {
                var lazyImages = document.querySelectorAll('img[data-src]');
                var imageObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var img = entry.target;
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(function(img) {
                    imageObserver.observe(img);
                });
            }
        }
    };

    $(document).ready(function() {
        DPDTTheme.init();
    });

})(jQuery);

/* Add inline style for back-to-top and animations */
(function() {
    var style = document.createElement('style');
    style.textContent = `
        .dpdt-back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 44px;
            height: 44px;
            background: var(--dpdt-primary);
            color: #fff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .dpdt-back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        .dpdt-back-to-top:hover {
            background: var(--dpdt-secondary);
            transform: translateY(-3px);
        }
        .dpdt-animate {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        .dpdt-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .header-scrolled {
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
        }
    `;
    document.head.appendChild(style);
})();
