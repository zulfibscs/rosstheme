/**
 * Responsive Pro Header JavaScript
 * Handles mobile menu toggles, search functionality, and responsive behavior
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // Only run if responsive header is present
        if (!$('.header-responsive-pro').length) {
            return;
        }

        // Mobile Menu Toggle
        $('.header-responsive-pro .mobile-menu-toggle').on('click', function(e) {
            e.preventDefault();

            const $toggle = $(this);
            const $menu = $toggle.closest('.header-mobile').find('.mobile-navigation');
            const $searchBar = $toggle.closest('.header-mobile').find('.mobile-search-bar');
            const isExpanded = $toggle.attr('aria-expanded') === 'true';

            // Close search bar if open
            if ($searchBar.hasClass('active')) {
                $searchBar.removeClass('active');
                $toggle.closest('.header-mobile').find('.mobile-search-toggle').attr('aria-expanded', 'false');
            }

            // Toggle menu
            $toggle.attr('aria-expanded', !isExpanded);
            $menu.toggleClass('active');

            // Update ARIA attributes
            $toggle.attr('aria-controls', 'mobile-menu');
        });

        // Mobile Search Toggle
        $('.header-responsive-pro .mobile-search-toggle').on('click', function(e) {
            e.preventDefault();

            const $toggle = $(this);
            const $searchBar = $toggle.closest('.header-mobile').find('.mobile-search-bar');
            const $menu = $toggle.closest('.header-mobile').find('.mobile-navigation');
            const isExpanded = $toggle.attr('aria-expanded') === 'true';

            // Close menu if open
            if ($menu.hasClass('active')) {
                $menu.removeClass('active');
                $toggle.closest('.header-mobile').find('.mobile-menu-toggle').attr('aria-expanded', 'false');
            }

            // Toggle search
            $toggle.attr('aria-expanded', !isExpanded);
            $searchBar.toggleClass('active');
        });

        // Desktop Search Toggle
        $('.header-responsive-pro .search-toggle').on('click', function(e) {
            e.preventDefault();

            const $toggle = $(this);
            const $searchContainer = $toggle.closest('.header-search-inline');
            const isActive = $searchContainer.hasClass('active');

            // Close other search forms
            $('.header-responsive-pro .header-search-inline').not($searchContainer).removeClass('active');

            // Toggle this search form
            $searchContainer.toggleClass('active');

            // Focus input when opening
            if (!isActive) {
                $searchContainer.find('input[type="search"]').focus();
            }
        });

        // Close search forms when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.header-search-inline').length) {
                $('.header-responsive-pro .header-search-inline').removeClass('active');
            }
        });

        // Close mobile menu/search on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                // Close mobile menu
                $('.header-responsive-pro .mobile-navigation').removeClass('active');
                $('.header-responsive-pro .mobile-menu-toggle').attr('aria-expanded', 'false');

                // Close mobile search
                $('.header-responsive-pro .mobile-search-bar').removeClass('active');
                $('.header-responsive-pro .mobile-search-toggle').attr('aria-expanded', 'false');

                // Close desktop search
                $('.header-responsive-pro .header-search-inline').removeClass('active');
            }
        });

        // Handle window resize for responsive behavior
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Close mobile menus if switching to desktop
                if ($(window).width() >= 768) {
                    $('.header-responsive-pro .mobile-navigation').removeClass('active');
                    $('.header-responsive-pro .mobile-menu-toggle').attr('aria-expanded', 'false');
                    $('.header-responsive-pro .mobile-search-bar').removeClass('active');
                    $('.header-responsive-pro .mobile-search-toggle').attr('aria-expanded', 'false');
                }
            }, 250);
        });

        // Smooth scroll for anchor links in mobile menu
        $('.header-responsive-pro .mobile-menu a[href^="#"]').on('click', function(e) {
            const $menu = $(this).closest('.mobile-navigation');
            const $toggle = $(this).closest('.header-mobile').find('.mobile-menu-toggle');

            // Close menu after clicking anchor link
            $menu.removeClass('active');
            $toggle.attr('aria-expanded', 'false');
        });

        // Prevent search form submission if empty
        $('.header-responsive-pro .search-form').on('submit', function(e) {
            const $input = $(this).find('input[type="search"]');
            if (!$input.val().trim()) {
                e.preventDefault();
                $input.focus();
                return false;
            }
        });

        // Add loading states for better UX
        $('.header-responsive-pro .header-cta-button').on('click', function() {
            const $button = $(this);
            const originalText = $button.text();

            // Add loading state for external links
            if ($button.attr('target') === '_blank' || $button.attr('href').indexOf('http') === 0) {
                $button.prop('disabled', true).text('Loading...');

                // Re-enable after a short delay
                setTimeout(function() {
                    $button.prop('disabled', false).text(originalText);
                }, 1000);
            }
        });

        // Initialize ARIA attributes
        $('.header-responsive-pro .mobile-menu-toggle').attr({
            'aria-expanded': 'false',
            'aria-controls': 'mobile-menu'
        });

        $('.header-responsive-pro .mobile-search-toggle').attr({
            'aria-expanded': 'false',
            'aria-controls': 'mobile-search-bar'
        });

        // Add focus management for accessibility
        $('.header-responsive-pro .mobile-menu-toggle, .header-responsive-pro .mobile-search-toggle').on('keydown', function(e) {
            if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
                e.preventDefault();
                $(this).click();
            }
        });

        // Console log for debugging (remove in production)
        if (window.location.hash === '#debug-responsive-header') {
            console.log('Responsive Pro Header initialized', {
                mobileBreakpoint: getComputedStyle(document.documentElement).getPropertyValue('--mobile-breakpoint'),
                desktopHeight: getComputedStyle(document.documentElement).getPropertyValue('--desktop-header-height'),
                mobileHeight: getComputedStyle(document.documentElement).getPropertyValue('--mobile-header-height')
            });
        }

    });

})(jQuery);