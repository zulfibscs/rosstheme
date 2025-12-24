/**
 * Ross Theme - Advanced Sticky Header
 * Smooth, performant sticky header with multiple behaviors and mobile support
 */
(function() {
    'use strict';

    // Configuration from WordPress
    var config = window.rossStickyOptions || {};
    var isEnabled = config.enabled || false;

    if (!isEnabled) return;

    // State management
    var state = {
        isSticky: false,
        isShrunk: false,
        lastScrollTop: 0,
        scrollDirection: 'up',
        animationFrame: null,
        resizeTimeout: null
    };

    // DOM elements
    var elements = {};

    /**
     * Initialize sticky header
     */
    function init() {
        try {
            elements.header = document.querySelector('.site-header');
            elements.body = document.body;

            if (!elements.header) {
                console.warn('Ross Sticky Header: No .site-header element found');
                return;
            }

            // Store original styles for restoration
            storeOriginalStyles();

            // Set initial state
            updateHeaderState();

            // Bind events
            bindEvents();

            // Add accessibility attributes
            elements.header.setAttribute('aria-expanded', 'false');

            // Initial check
            handleScroll();

            console.log('Ross Sticky Header: Initialized successfully');

        } catch (error) {
            console.error('Ross Sticky Header: Initialization failed', error);
        }
    }

    /**
     * Bind scroll and resize events
     */
    function bindEvents() {
        // Throttled scroll handler for performance
        var scrollHandler = throttle(handleScroll, 16); // ~60fps
        window.addEventListener('scroll', scrollHandler, { passive: true });

        // Resize handler for responsive adjustments
        window.addEventListener('resize', handleResize);
    }

    /**
     * Handle scroll events with performance optimization
     */
    function handleScroll() {
        if (state.animationFrame) {
            cancelAnimationFrame(state.animationFrame);
        }

        state.animationFrame = requestAnimationFrame(function() {
            try {
                var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                // Skip if scroll hasn't changed significantly (performance optimization)
                if (Math.abs(scrollTop - state.lastScrollTop) < 1) {
                    return;
                }

                var scrollDelta = scrollTop - state.lastScrollTop;
                var isScrollingDown = scrollDelta > 0;

                // Update scroll direction
                state.scrollDirection = isScrollingDown ? 'down' : 'up';

                // Determine sticky state based on behavior
                var shouldBeSticky = shouldHeaderBeSticky(scrollTop);

                // Update header state
                updateStickyState(shouldBeSticky, scrollTop);

                // Handle shrink behavior
                if (shouldBeSticky && config.shrink_enabled) {
                    updateShrinkState(scrollTop, isScrollingDown);
                }

                // Handle scroll-up behavior
                if (config.behavior === 'scroll_up' && shouldBeSticky) {
                    updateScrollUpVisibility(isScrollingDown);
                }

                state.lastScrollTop = scrollTop;

            } catch (error) {
                console.error('Ross Sticky Header: Scroll handling error', error);
            }
        });
    }

    /**
     * Determine if header should be sticky
     */
    function shouldHeaderBeSticky(scrollTop) {
        // Check mobile hide option
        if (config.hide_on_mobile && window.innerWidth <= config.mobile_breakpoint) {
            return false;
        }

        switch (config.behavior) {
            case 'always':
                return scrollTop > config.threshold;

            case 'scroll_up':
                return scrollTop > config.threshold;

            case 'after_scroll':
                return scrollTop > config.threshold;

            default:
                return false;
        }
    }

    /**
     * Update sticky state
     */
    function updateStickyState(shouldBeSticky, scrollTop) {
        if (shouldBeSticky && !state.isSticky) {
            // Becoming sticky
            elements.header.classList.add('is-sticky');
            elements.body.classList.add('has-sticky-header');
            state.isSticky = true;

            // Store original inline styles
            if (!elements.header.dataset.originalStyle) {
                elements.header.dataset.originalStyle = elements.header.getAttribute('style') || '';
            }

            // Override inline styles for sticky state
            updateHeaderInlineStyles(true, false);

            // Update accessibility
            updateAccessibility(true);

            // Trigger custom event
            triggerEvent('sticky:activated', { scrollTop: scrollTop });

        } else if (!shouldBeSticky && state.isSticky) {
            // No longer sticky
            elements.header.classList.remove('is-sticky', 'shrink');
            elements.body.classList.remove('has-sticky-header', 'is-sticky');
            state.isSticky = false;
            state.isShrunk = false;

            // Reset transforms
            elements.header.style.transform = '';

            // Restore original inline styles
            if (elements.header.dataset.originalStyle) {
                elements.header.setAttribute('style', elements.header.dataset.originalStyle);
            }

            // Update accessibility
            updateAccessibility(false);

            // Trigger custom event
            triggerEvent('sticky:deactivated', { scrollTop: scrollTop });
        }
    }

    /**
     * Store original header styles for restoration
     */
    function storeOriginalStyles() {
        if (!elements.header.dataset.originalStyle) {
            elements.header.dataset.originalStyle = elements.header.getAttribute('style') || '';
        }
    }

    /**
     * Update accessibility attributes
     */
    function updateAccessibility(isSticky) {
        if (elements.header) {
            elements.header.setAttribute('aria-expanded', isSticky ? 'true' : 'false');
        }
    }
    function updateHeaderInlineStyles(isSticky, isShrunk) {
        if (!elements.header.dataset.originalStyle) {
            return;
        }

        var originalStyle = elements.header.dataset.originalStyle;
        var newStyle = originalStyle;

        if (isSticky) {
            // Remove min-height, padding, and margin from inline styles
            newStyle = newStyle.replace(/min-height:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/padding:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/padding-top:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/padding-bottom:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/padding-left:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/padding-right:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/margin:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/margin-top:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/margin-bottom:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/margin-left:\s*[^;]+;?/gi, '');
            newStyle = newStyle.replace(/margin-right:\s*[^;]+;?/gi, '');

            // Apply sticky-specific styles
            if (isShrunk) {
                newStyle += ' height: ' + config.shrink_height + 'px !important;';
            } else {
                newStyle += ' height: ' + config.normal_height + 'px !important;';
            }
        }

        // Clean up extra semicolons
        newStyle = newStyle.replace(/;+/g, ';').replace(/;$/, '');

        elements.header.setAttribute('style', newStyle);
    }
    function updateShrinkState(scrollTop, isScrollingDown) {
        var shouldShrink = false;

        if (config.behavior === 'scroll_up') {
            // Shrink when scrolling down, unshrink when scrolling up
            shouldShrink = isScrollingDown && scrollTop > config.threshold;
        } else {
            // Always shrink when sticky
            shouldShrink = true;
        }

        if (shouldShrink && !state.isShrunk) {
            elements.header.classList.add('shrink');
            elements.body.classList.add('is-sticky');
            state.isShrunk = true;

            // Update inline styles for shrunk state
            updateHeaderInlineStyles(true, true);

            triggerEvent('sticky:shrunk', { scrollTop: scrollTop });

        } else if (!shouldShrink && state.isShrunk) {
            elements.header.classList.remove('shrink');
            elements.body.classList.remove('is-sticky');
            state.isShrunk = false;

            // Update inline styles for expanded state
            updateHeaderInlineStyles(true, false);

            triggerEvent('sticky:expanded', { scrollTop: scrollTop });
        }
    }

    /**
     * Update scroll-up behavior visibility
     */
    function updateScrollUpVisibility(isScrollingDown) {
        if (isScrollingDown) {
            elements.header.classList.add('sticky-scroll-up');
            elements.header.classList.remove('visible');
        } else {
            elements.header.classList.add('visible');
            // Remove the hiding class after animation
            setTimeout(function() {
                elements.header.classList.remove('sticky-scroll-up');
            }, config.animation_duration);
        }
    }

    /**
     * Handle window resize
     */
    function handleResize() {
        clearTimeout(state.resizeTimeout);
        state.resizeTimeout = setTimeout(function() {
            try {
                // Re-evaluate sticky state on resize
                updateHeaderState();

                // Update mobile breakpoint checks
                if (config.hide_on_mobile) {
                    var isMobile = window.innerWidth <= config.mobile_breakpoint;
                    if (isMobile && state.isSticky) {
                        // Hide sticky header on mobile if option is enabled
                        elements.header.classList.remove('is-sticky');
                        elements.body.classList.remove('has-sticky-header');
                        state.isSticky = false;
                        updateAccessibility(false);
                    }
                }

                // Force scroll check after resize
                handleScroll();
            } catch (error) {
                console.error('Ross Sticky Header: Resize handling error', error);
            }
        }, 250); // Increased debounce time for better performance
    }

    /**
     * Update header state based on current conditions
     */
    function updateHeaderState() {
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Reset classes
        elements.header.classList.remove('is-sticky', 'shrink', 'sticky-scroll-up', 'visible');
        elements.body.classList.remove('has-sticky-header', 'is-sticky');

        // Reapply current state
        if (shouldHeaderBeSticky(scrollTop)) {
            elements.header.classList.add('is-sticky');
            elements.body.classList.add('has-sticky-header');

            if (config.shrink_enabled) {
                elements.header.classList.add('shrink');
                elements.body.classList.add('is-sticky');
            }
        }
    }

    /**
     * Trigger custom events
     */
    function triggerEvent(eventName, data) {
        var event = new CustomEvent('ross:' + eventName, {
            detail: data,
            bubbles: true
        });
        elements.header.dispatchEvent(event);
    }

    /**
     * Throttle function for performance
     */
    function throttle(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        };
    }

    /**
     * Public API
     */
    window.rossStickyHeader = {
        refresh: updateHeaderState,
        isSticky: function() { return state.isSticky; },
        isShrunk: function() { return state.isShrunk; }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();