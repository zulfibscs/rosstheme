/**
 * Simple Responsive Header JavaScript
 * Handles mobile menu toggle and search overlay
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // Mobile Menu Toggle
        $('.mobile-menu-toggle').on('click', function(e) {
            e.preventDefault();
            const $toggle = $(this);
            const $overlay = $('.mobile-menu-overlay');
            const isOpen = $overlay.hasClass('active');

            if (isOpen) {
                // Close menu
                $overlay.removeClass('active');
                $toggle.attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            } else {
                // Open menu
                $overlay.addClass('active');
                $toggle.attr('aria-expanded', 'true');
                $('body').addClass('mobile-menu-open');

                // Close search overlay if open
                $('.search-overlay').removeClass('active');
            }
        });

        // Search Toggle
        $('.search-toggle').on('click', function(e) {
            e.preventDefault();
            const $overlay = $('.search-overlay');
            const isOpen = $overlay.hasClass('active');

            if (isOpen) {
                // Close search
                $overlay.removeClass('active');
                $('body').removeClass('search-open');
            } else {
                // Open search
                $overlay.addClass('active');
                $('body').addClass('search-open');
                $('.search-overlay input[type="search"]').focus();

                // Close mobile menu if open
                $('.mobile-menu-overlay').removeClass('active');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            }
        });

        // Search Close
        $('.search-close').on('click', function(e) {
            e.preventDefault();
            $('.search-overlay').removeClass('active');
            $('body').removeClass('search-open');
        });

        // Close overlays when clicking outside
        $(document).on('click', function(e) {
            // Close mobile menu
            if (!$(e.target).closest('.mobile-menu-overlay, .mobile-menu-toggle').length) {
                $('.mobile-menu-overlay').removeClass('active');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');
            }

            // Close search overlay
            if (!$(e.target).closest('.search-overlay, .search-toggle').length) {
                $('.search-overlay').removeClass('active');
                $('body').removeClass('search-open');
            }
        });

        // Close on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                $('.mobile-menu-overlay').removeClass('active');
                $('.mobile-menu-toggle').attr('aria-expanded', 'false');
                $('body').removeClass('mobile-menu-open');

                $('.search-overlay').removeClass('active');
                $('body').removeClass('search-open');
            }
        });

        // Handle window resize
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Close mobile menu on desktop
                if ($(window).width() > 768) {
                    $('.mobile-menu-overlay').removeClass('active');
                    $('.mobile-menu-toggle').attr('aria-expanded', 'false');
                    $('body').removeClass('mobile-menu-open');

                    $('.search-overlay').removeClass('active');
                    $('body').removeClass('search-open');
                }
            }, 250);
        });

        // Prevent body scroll when mobile menu is open
        const originalOverflow = $('body').css('overflow');
        $(document).on('mobile-menu-open', function() {
            $('body').css('overflow', 'hidden');
        }).on('mobile-menu-close', function() {
            $('body').css('overflow', originalOverflow);
        });

    });

})(jQuery);