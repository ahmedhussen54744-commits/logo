/**
 * DPDT Trademark Plugin - Main Frontend JavaScript
 * Version: 3.5.0
 */
(function($) {
    'use strict';

    var DPDT = {
        init: function() {
            this.initStatCounters();
            this.initSmoothScroll();
            this.initCopyProtection();
        },

        /**
         * Animated stat counters
         */
        initStatCounters: function() {
            var $counters = $('.dpdt-stat-number[data-count]');
            if (!$counters.length) return;

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var $el = $(entry.target);
                        var target = parseInt($el.data('count'), 10);
                        DPDT.animateCounter($el, target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            $counters.each(function() {
                observer.observe(this);
            });
        },

        animateCounter: function($el, target) {
            var duration = 2000;
            var start = 0;
            var startTime = null;

            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                var progress = Math.min((timestamp - startTime) / duration, 1);
                var value = Math.floor(progress * target);
                $el.text(value.toLocaleString('bn-BD'));
                if (progress < 1) {
                    requestAnimationFrame(step);
                } else {
                    $el.text(target.toLocaleString('bn-BD'));
                }
            }

            requestAnimationFrame(step);
        },

        /**
         * Smooth scroll for anchor links
         */
        initSmoothScroll: function() {
            $('a[href^="#"]').on('click', function(e) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 600);
                }
            });
        },

        /**
         * Basic copy protection
         */
        initCopyProtection: function() {
            // Disable right-click on certificate images
            $(document).on('contextmenu', '.dpdt-verify-certificate-img img, .dpdt-cert-logo img', function(e) {
                e.preventDefault();
                return false;
            });

            // Disable drag on certificate images
            $(document).on('dragstart', '.dpdt-verify-certificate-img img', function(e) {
                e.preventDefault();
                return false;
            });
        }
    };

    // Initialize on DOM ready
    $(document).ready(function() {
        DPDT.init();
    });

})(jQuery);
