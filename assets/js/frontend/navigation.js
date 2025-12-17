/**
 * Ross Theme - Enhanced Navigation Module
 * Handles mobile menus, sticky headers, hamburger animations, and search
 */
(function() {
    'use strict';
    
    // Get header options from localized script
    var headerOptions = window.rossHeaderOptions || {};
    
    // State management
    var state = {
        mobileMenuOpen: false,
        searchOpen: false,
        lastScrollTop: 0,
        headerVisible: true,
        isSticky: false
    };
    
    /**
     * Mobile Menu with Animation Support
     */
    function initMobileMenu() {
        var btn = document.querySelector('.menu-toggle');
        var nav = document.querySelector('.header-navigation');
        var body = document.body;
        
        if (!btn || !nav) return;
        
        // Get mobile menu style from options
        var menuStyle = headerOptions.mobile_menu_style || 'slide';
        var hamburgerAnim = headerOptions.hamburger_animation || 'collapse';
        
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            state.mobileMenuOpen = !state.mobileMenuOpen;
            
            toggleMobileMenu(btn, nav, body, menuStyle, hamburgerAnim);
        });
        
        // Close menu on overlay click (for fullscreen/slide)
        if (menuStyle === 'fullscreen' || menuStyle === 'slide') {
            var overlay = document.querySelector('.mobile-menu-overlay');
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        state.mobileMenuOpen = false;
                        toggleMobileMenu(btn, nav, body, menuStyle, hamburgerAnim);
                    }
                });
            }
        }
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.mobileMenuOpen) {
                state.mobileMenuOpen = false;
                toggleMobileMenu(btn, nav, body, menuStyle, hamburgerAnim);
            }
        });
    }
    
    /**
     * Toggle mobile menu with style-specific animations
     */
    function toggleMobileMenu(btn, nav, body, style, animation) {
        // Toggle hamburger animation
        animateHamburger(btn, animation, state.mobileMenuOpen);
        
        // Apply menu style
        if (state.mobileMenuOpen) {
            nav.classList.add('open', 'menu-' + style);
            body.classList.add('mobile-menu-open');
            btn.classList.add('is-open');
            btn.setAttribute('aria-expanded', 'true');
            
            // Style-specific classes
            if (style === 'fullscreen') {
                nav.classList.add('mobile-menu-overlay');
            } else if (style === 'slide') {
                nav.classList.add('mobile-menu-slide', 'active');
            } else if (style === 'push') {
                document.querySelector('.site-wrapper')?.classList.add('pushed');
            }
        } else {
            nav.classList.remove('open', 'menu-' + style, 'mobile-menu-overlay', 'mobile-menu-slide', 'active');
            body.classList.remove('mobile-menu-open');
            btn.classList.remove('is-open');
            btn.setAttribute('aria-expanded', 'false');
            document.querySelector('.site-wrapper')?.classList.remove('pushed');
        }
    }
    
    /**
     * Hamburger Icon Animations
     */
    function animateHamburger(btn, animation, isOpen) {
        var lines = btn.querySelectorAll('.hamburger-line');
        if (lines.length < 3) return;
        
        switch(animation) {
            case 'collapse':
                // Transform to X
                if (isOpen) {
                    lines[0].style.transform = 'rotate(45deg) translateY(8px)';
                    lines[1].style.opacity = '0';
                    lines[2].style.transform = 'rotate(-45deg) translateY(-8px)';
                } else {
                    lines[0].style.transform = 'none';
                    lines[1].style.opacity = '1';
                    lines[2].style.transform = 'none';
                }
                break;
                
            case 'spin':
                // Spin to X
                if (isOpen) {
                    btn.style.transform = 'rotate(180deg)';
                    lines[0].style.transform = 'rotate(45deg) translateY(8px)';
                    lines[1].style.opacity = '0';
                    lines[2].style.transform = 'rotate(-45deg) translateY(-8px)';
                } else {
                    btn.style.transform = 'rotate(0deg)';
                    lines[0].style.transform = 'none';
                    lines[1].style.opacity = '1';
                    lines[2].style.transform = 'none';
                }
                break;
                
            case 'arrow':
                // Transform to arrow
                if (isOpen) {
                    lines[0].style.transform = 'rotate(45deg) translateX(5px)';
                    lines[1].style.transform = 'scaleX(0)';
                    lines[2].style.transform = 'rotate(-45deg) translateX(5px)';
                } else {
                    lines[0].style.transform = 'none';
                    lines[1].style.transform = 'scaleX(1)';
                    lines[2].style.transform = 'none';
                }
                break;
                
            case 'minimal':
                // Simple fade
                if (isOpen) {
                    lines.forEach(function(line) {
                        line.style.backgroundColor = headerOptions.menu_hover_color || '#E5C902';
                    });
                } else {
                    lines.forEach(function(line) {
                        line.style.backgroundColor = '';
                    });
                }
                break;
        }
    }
    
    /**
     * Advanced Sticky Header with Behavior Modes
     */
    function initStickyHeader() {
        var header = document.querySelector('.site-header');
        if (!header) return;
        
        var stickyEnabled = headerOptions.sticky_header;
        if (!stickyEnabled) return;
        
        var behavior = headerOptions.sticky_behavior || 'always';
        var threshold = parseInt(headerOptions.sticky_scroll_threshold) || 100;
        var shrinkEnabled = headerOptions.sticky_shrink_header;
        
        window.addEventListener('scroll', function() {
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var scrollingDown = scrollTop > state.lastScrollTop;
            
            // Update last scroll position
            state.lastScrollTop = scrollTop;
            
            // Determine if header should be sticky
            var shouldBeSticky = false;
            
            switch(behavior) {
                case 'always':
                    shouldBeSticky = scrollTop > threshold;
                    break;
                    
                case 'scroll_up':
                    // Show on scroll up, hide on scroll down
                    if (scrollTop > threshold) {
                        shouldBeSticky = !scrollingDown;
                    }
                    break;
                    
                case 'after_scroll':
                    shouldBeSticky = scrollTop > threshold;
                    break;
            }
            
            // Apply sticky state
            if (shouldBeSticky && !state.isSticky) {
                header.classList.add('is-sticky');
                document.body.classList.add('has-sticky-header');
                state.isSticky = true;
            } else if (!shouldBeSticky && state.isSticky) {
                header.classList.remove('is-sticky', 'shrink');
                document.body.classList.remove('has-sticky-header', 'is-sticky');
                state.isSticky = false;
            }
            
            // Handle shrink behavior separately
            if (state.isSticky && shrinkEnabled) {
                if (behavior === 'scroll_up') {
                    // For scroll_up, shrink when scrolling down, unshrink when scrolling up
                    if (scrollingDown) {
                        header.classList.add('shrink');
                        document.body.classList.add('is-sticky');
                    } else {
                        header.classList.remove('shrink');
                        document.body.classList.remove('is-sticky');
                    }
                } else {
                    // For other behaviors, always shrink when sticky
                    header.classList.add('shrink');
                    document.body.classList.add('is-sticky');
                }
            }
            
            // Handle scroll_up behavior visibility
            if (behavior === 'scroll_up' && state.isSticky) {
                if (scrollingDown) {
                    header.style.transform = 'translateY(-100%)';
                } else {
                    header.style.transform = 'translateY(0)';
                }
            }
        });
    }
    
    /**
     * Search Functionality with Multiple Types
     */
    function initSearch() {
        var searchBtn = document.querySelector('.header-search-toggle');
        var searchType = headerOptions.search_type || 'modal';
        
        if (!searchBtn) return;
        
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            state.searchOpen = !state.searchOpen;
            
            switch(searchType) {
                case 'modal':
                    toggleSearchModal();
                    break;
                case 'dropdown':
                    toggleSearchDropdown(searchBtn);
                    break;
                case 'inline':
                    toggleSearchInline(searchBtn);
                    break;
            }
        });
        
        // Close search on escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && state.searchOpen) {
                closeAllSearch();
            }
        });
    }
    
    function toggleSearchModal() {
        var modal = document.querySelector('.search-modal-overlay');
        if (!modal) {
            createSearchModal();
            modal = document.querySelector('.search-modal-overlay');
        }
        
        if (state.searchOpen) {
            modal.classList.add('active');
            modal.querySelector('.search-field')?.focus();
        } else {
            modal.classList.remove('active');
        }
    }
    
    function toggleSearchDropdown(btn) {
        var dropdown = btn.nextElementSibling;
        if (!dropdown || !dropdown.classList.contains('header-search-dropdown')) {
            dropdown = createSearchDropdown(btn);
        }
        
        if (state.searchOpen) {
            dropdown.classList.add('active');
            dropdown.querySelector('.search-field')?.focus();
        } else {
            dropdown.classList.remove('active');
        }
    }
    
    function toggleSearchInline(btn) {
        var inline = btn.nextElementSibling;
        if (!inline || !inline.classList.contains('header-search-inline')) {
            inline = createSearchInline(btn);
        }
        
        if (state.searchOpen) {
            inline.classList.add('active');
            inline.querySelector('.search-field')?.focus();
        } else {
            inline.classList.remove('active');
        }
    }
    
    function createSearchModal() {
        var modal = document.createElement('div');
        modal.className = 'search-modal-overlay';
        modal.innerHTML = '<div class="search-modal-content">' +
            '<button class="search-close" aria-label="Close search">&times;</button>' +
            '<form role="search" method="get" class="search-form" action="' + (window.location.origin || '') + '">' +
            '<input type="search" class="search-field" placeholder="' + (headerOptions.search_placeholder || 'Search...') + '" name="s" />' +
            '<button type="submit" class="search-submit">Search</button>' +
            '</form></div>';
        document.body.appendChild(modal);
        
        modal.querySelector('.search-close').addEventListener('click', function() {
            state.searchOpen = false;
            toggleSearchModal();
        });
    }
    
    function createSearchDropdown(btn) {
        var dropdown = document.createElement('div');
        dropdown.className = 'header-search-dropdown';
        dropdown.innerHTML = '<form role="search" method="get" class="search-form" action="' + (window.location.origin || '') + '">' +
            '<input type="search" class="search-field" placeholder="' + (headerOptions.search_placeholder || 'Search...') + '" name="s" />' +
            '<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>' +
            '</form>';
        btn.parentNode.appendChild(dropdown);
        return dropdown;
    }
    
    function createSearchInline(btn) {
        var inline = document.createElement('div');
        inline.className = 'header-search-inline';
        inline.innerHTML = '<form role="search" method="get" class="search-form" action="' + (window.location.origin || '') + '">' +
            '<input type="search" class="search-field" placeholder="' + (headerOptions.search_placeholder || 'Search...') + '" name="s" />' +
            '</form>';
        btn.parentNode.appendChild(inline);
        return inline;
    }
    
    function closeAllSearch() {
        state.searchOpen = false;
        document.querySelector('.search-modal-overlay')?.classList.remove('active');
        document.querySelector('.header-search-dropdown')?.classList.remove('active');
        document.querySelector('.header-search-inline')?.classList.remove('active');
    }
    
    /**
     * Submenu toggles for accessibility
     */
    function initSubmenuToggles() {
        var items = document.querySelectorAll('.primary-menu li.menu-item-has-children');
        items.forEach(function(li) {
            var btn = li.querySelector('.submenu-toggle');
            if (!btn) {
                btn = document.createElement('button');
                btn.className = 'submenu-toggle';
                btn.setAttribute('aria-expanded', 'false');
                btn.innerHTML = '<span class="screen-reader-text">Toggle submenu</span>';
                var submenu = li.querySelector('ul');
                if (submenu) {
                    li.insertBefore(btn, submenu);
                }
            }
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', !expanded);
                li.classList.toggle('submenu-open');
            });
        });
    }
    
    /**
     * Add CSS for hamburger line transitions
     */
    function addHamburgerStyles() {
        var style = document.createElement('style');
        style.textContent = '.hamburger-line { transition: all 0.3s ease !important; transform-origin: center !important; }' +
            '.menu-toggle { transition: transform 0.3s ease !important; }' +
            '.site-header { transition: transform 0.3s ease !important; }';
        document.head.appendChild(style);
    }
    
    /**
     * Initialize all navigation features
     */
    document.addEventListener('DOMContentLoaded', function() {
        addHamburgerStyles();
        initMobileMenu();
        initStickyHeader();
        initSearch();
        initSubmenuToggles();
    });
})();
