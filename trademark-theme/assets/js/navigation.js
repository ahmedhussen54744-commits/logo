/**
 * DPDT Theme - Navigation JavaScript
 * Version: 3.5.0
 * Handles mobile menu toggle, dropdown interactions
 */
(function() {
    'use strict';

    var DPDTNav = {
        init: function() {
            this.menuToggle = document.querySelector('.menu-toggle');
            this.menuContainer = document.querySelector('.menu-container');
            this.dropdownToggles = document.querySelectorAll('.dpdt-dropdown-toggle');

            if (!this.menuToggle || !this.menuContainer) return;

            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            // Mobile menu toggle
            this.menuToggle.addEventListener('click', function() {
                self.toggleMenu();
            });

            // Dropdown toggles (mobile)
            this.dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.toggleDropdown(this);
                });
            });

            // Close menu on outside click
            document.addEventListener('click', function(e) {
                if (!self.menuContainer.contains(e.target) && !self.menuToggle.contains(e.target)) {
                    self.closeMenu();
                }
            });

            // Close menu on ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    self.closeMenu();
                }
            });

            // Close menu on resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    self.closeMenu();
                    self.closeAllDropdowns();
                }
            });

            // Desktop hover dropdowns with delay
            var menuItems = document.querySelectorAll('.primary-menu-list > li.dpdt-has-dropdown');
            menuItems.forEach(function(item) {
                var timeout;

                item.addEventListener('mouseenter', function() {
                    if (window.innerWidth <= 768) return;
                    clearTimeout(timeout);
                    var subMenu = this.querySelector('.sub-menu');
                    if (subMenu) {
                        subMenu.style.display = 'block';
                    }
                });

                item.addEventListener('mouseleave', function() {
                    if (window.innerWidth <= 768) return;
                    var subMenu = this.querySelector('.sub-menu');
                    timeout = setTimeout(function() {
                        if (subMenu) {
                            subMenu.style.display = '';
                        }
                    }, 200);
                });
            });

            // Accessibility: keyboard navigation
            var menuLinks = document.querySelectorAll('.dpdt-menu-link');
            menuLinks.forEach(function(link) {
                link.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        var parent = this.closest('.dpdt-has-dropdown');
                        if (parent && window.innerWidth <= 768) {
                            e.preventDefault();
                            var toggle = parent.querySelector('.dpdt-dropdown-toggle');
                            if (toggle) {
                                self.toggleDropdown(toggle);
                            }
                        }
                    }
                });
            });
        },

        toggleMenu: function() {
            var isOpen = this.menuContainer.classList.contains('active');

            if (isOpen) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        },

        openMenu: function() {
            this.menuContainer.classList.add('active');
            this.menuToggle.classList.add('active');
            this.menuToggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('menu-open');
        },

        closeMenu: function() {
            this.menuContainer.classList.remove('active');
            this.menuToggle.classList.remove('active');
            this.menuToggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('menu-open');
        },

        toggleDropdown: function(toggle) {
            var parent = toggle.closest('.dpdt-has-dropdown');
            var subMenu = parent.querySelector('.sub-menu');
            var isActive = subMenu.classList.contains('active');

            // Close siblings
            var siblings = parent.parentNode.querySelectorAll('.dpdt-has-dropdown');
            siblings.forEach(function(sibling) {
                if (sibling !== parent) {
                    var sibSubMenu = sibling.querySelector('.sub-menu');
                    var sibToggle = sibling.querySelector('.dpdt-dropdown-toggle');
                    if (sibSubMenu) sibSubMenu.classList.remove('active');
                    if (sibToggle) sibToggle.classList.remove('active');
                }
            });

            // Toggle current
            if (isActive) {
                subMenu.classList.remove('active');
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                subMenu.classList.add('active');
                toggle.classList.add('active');
                toggle.setAttribute('aria-expanded', 'true');
            }
        },

        closeAllDropdowns: function() {
            document.querySelectorAll('.sub-menu.active').forEach(function(menu) {
                menu.classList.remove('active');
            });
            document.querySelectorAll('.dpdt-dropdown-toggle.active').forEach(function(toggle) {
                toggle.classList.remove('active');
                toggle.setAttribute('aria-expanded', 'false');
            });
        }
    };

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            DPDTNav.init();
        });
    } else {
        DPDTNav.init();
    }

})();
