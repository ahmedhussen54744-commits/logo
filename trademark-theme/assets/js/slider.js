/**
 * DPDT Theme - Hero Slider JavaScript
 * Version: 3.5.0
 */
(function() {
    'use strict';

    var DPDTSlider = {
        currentSlide: 0,
        totalSlides: 0,
        autoPlayInterval: null,
        autoPlayDelay: 5000,
        isPlaying: true,

        init: function() {
            var slider = document.getElementById('hero-slider');
            if (!slider) return;

            this.slider = slider;
            this.slides = slider.querySelectorAll('.slide');
            this.dots = slider.querySelectorAll('.dot');
            this.prevBtn = slider.querySelector('.slider-prev');
            this.nextBtn = slider.querySelector('.slider-next');
            this.totalSlides = this.slides.length;

            if (this.totalSlides <= 1) return;

            this.bindEvents();
            this.startAutoPlay();
        },

        bindEvents: function() {
            var self = this;

            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', function() {
                    self.prevSlide();
                    self.resetAutoPlay();
                });
            }

            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', function() {
                    self.nextSlide();
                    self.resetAutoPlay();
                });
            }

            // Dots
            this.dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() {
                    self.goToSlide(index);
                    self.resetAutoPlay();
                });
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    self.prevSlide();
                    self.resetAutoPlay();
                } else if (e.key === 'ArrowRight') {
                    self.nextSlide();
                    self.resetAutoPlay();
                }
            });

            // Pause on hover
            this.slider.addEventListener('mouseenter', function() {
                self.stopAutoPlay();
            });

            this.slider.addEventListener('mouseleave', function() {
                self.startAutoPlay();
            });

            // Touch support
            var touchStartX = 0;
            var touchEndX = 0;

            this.slider.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            this.slider.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                var diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        self.nextSlide();
                    } else {
                        self.prevSlide();
                    }
                    self.resetAutoPlay();
                }
            }, { passive: true });

            // Visibility API - pause when tab not visible
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    self.stopAutoPlay();
                } else {
                    self.startAutoPlay();
                }
            });
        },

        goToSlide: function(index) {
            if (index < 0) index = this.totalSlides - 1;
            if (index >= this.totalSlides) index = 0;

            this.slides[this.currentSlide].classList.remove('active');
            this.slides[index].classList.add('active');

            if (this.dots.length) {
                this.dots[this.currentSlide].classList.remove('active');
                this.dots[index].classList.add('active');
            }

            this.currentSlide = index;
        },

        nextSlide: function() {
            this.goToSlide(this.currentSlide + 1);
        },

        prevSlide: function() {
            this.goToSlide(this.currentSlide - 1);
        },

        startAutoPlay: function() {
            var self = this;
            this.stopAutoPlay();
            this.autoPlayInterval = setInterval(function() {
                self.nextSlide();
            }, this.autoPlayDelay);
            this.isPlaying = true;
        },

        stopAutoPlay: function() {
            if (this.autoPlayInterval) {
                clearInterval(this.autoPlayInterval);
                this.autoPlayInterval = null;
            }
            this.isPlaying = false;
        },

        resetAutoPlay: function() {
            this.stopAutoPlay();
            this.startAutoPlay();
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            DPDTSlider.init();
        });
    } else {
        DPDTSlider.init();
    }

})();
