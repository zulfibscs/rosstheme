/**
 * Ross Theme - Template Interactions
 * Frontend JavaScript for homepage templates
 * 
 * @package RossTheme
 * @version 1.0.0
 */

(function() {
    'use strict';

    /**
     * Initialize all template features when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        initPortfolioFilter();
        initFAQAccordion();
        initSmoothScroll();
        initScrollAnimations();
    });

    /**
     * Portfolio Filter (Creative Template)
     * Filters portfolio items by category
     */
    function initPortfolioFilter() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const portfolioItems = document.querySelectorAll('.portfolio-item');

        if (filterButtons.length === 0 || portfolioItems.length === 0) return;

        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');

                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Filter items
                portfolioItems.forEach(function(item) {
                    if (filter === 'all') {
                        item.style.display = 'block';
                        setTimeout(function() {
                            item.style.opacity = '1';
                            item.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        if (item.classList.contains(filter)) {
                            item.style.display = 'block';
                            setTimeout(function() {
                                item.style.opacity = '1';
                                item.style.transform = 'scale(1)';
                            }, 10);
                        } else {
                            item.style.opacity = '0';
                            item.style.transform = 'scale(0.8)';
                            setTimeout(function() {
                                item.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            });
        });

        // Set initial state for portfolio items
        portfolioItems.forEach(function(item) {
            item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        });
    }

    /**
     * FAQ Accordion (Startup Template)
     * Expandable FAQ items
     */
    function initFAQAccordion() {
        const faqQuestions = document.querySelectorAll('.faq-question');

        if (faqQuestions.length === 0) return;

        faqQuestions.forEach(function(question) {
            question.addEventListener('click', function() {
                const faqItem = this.parentElement;
                const answer = faqItem.querySelector('.faq-answer');
                const isActive = this.classList.contains('active');

                // Close all other FAQs
                faqQuestions.forEach(function(q) {
                    q.classList.remove('active');
                    q.parentElement.querySelector('.faq-answer').classList.remove('active');
                });

                // Toggle current FAQ
                if (!isActive) {
                    this.classList.add('active');
                    answer.classList.add('active');
                }
            });
        });
    }

    /**
     * Smooth Scroll
     * Smooth scrolling for anchor links
     */
    function initSmoothScroll() {
        const scrollLinks = document.querySelectorAll('a[href^="#"]');

        scrollLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Skip if it's just "#" or empty
                if (href === '#' || href === '') return;

                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    const offsetTop = target.offsetTop - 80; // Account for fixed header
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    /**
     * Scroll Animations
     * Fade in elements on scroll
     */
    function initScrollAnimations() {
        const animatedElements = document.querySelectorAll('.ross-service-card, .ross-feature-card, .ross-team-card, .ross-product-card, .ross-testimonial-card, .process-step');

        if (animatedElements.length === 0) return;

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(30px)';
                    
                    setTimeout(function() {
                        entry.target.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);
                    
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        animatedElements.forEach(function(element) {
            observer.observe(element);
        });
    }

    /**
     * Mobile Menu Enhancement
     * Add touch support for mobile menus
     */
    function initMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('.main-navigation');

        if (!menuToggle || !mainNav) return;

        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
                mainNav.classList.remove('active');
                menuToggle.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }

    /**
     * Image Gallery Lightbox (Optional Enhancement)
     * Basic lightbox for gallery images
     */
    function initGalleryLightbox() {
        const galleryItems = document.querySelectorAll('.gallery-item');

        if (galleryItems.length === 0) return;

        // Create lightbox container
        const lightbox = document.createElement('div');
        lightbox.className = 'ross-lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <span class="lightbox-close">&times;</span>
                <img src="" alt="">
            </div>
        `;
        document.body.appendChild(lightbox);

        const lightboxImg = lightbox.querySelector('img');
        const closeBtn = lightbox.querySelector('.lightbox-close');

        // Open lightbox on gallery item click
        galleryItems.forEach(function(item) {
            item.addEventListener('click', function() {
                const img = this.querySelector('img');
                if (img) {
                    lightboxImg.src = img.src;
                    lightbox.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // Close lightbox
        closeBtn.addEventListener('click', function() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        });

        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    /**
     * Newsletter Form Validation (Optional)
     */
    function initNewsletterValidation() {
        const newsletterForms = document.querySelectorAll('.newsletter-form');

        newsletterForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const emailInput = form.querySelector('input[type="email"]');
                
                if (!emailInput) return;

                const email = emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    emailInput.focus();
                    
                    // Add error styling
                    emailInput.style.borderColor = '#e74c3c';
                    
                    setTimeout(function() {
                        emailInput.style.borderColor = '';
                    }, 3000);
                }
            });
        });
    }

    /**
     * Scroll Progress Indicator (Optional Enhancement)
     */
    function initScrollProgress() {
        const progressBar = document.createElement('div');
        progressBar.className = 'scroll-progress-bar';
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--ross-primary-color, #001946);
            z-index: 9999;
            transition: width 0.2s ease;
        `;
        document.body.appendChild(progressBar);

        window.addEventListener('scroll', function() {
            const windowHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (window.scrollY / windowHeight) * 100;
            progressBar.style.width = scrolled + '%';
        });
    }

    /**
     * Add to Cart Animation (E-commerce Template)
     */
    function initAddToCartAnimation() {
        const addToCartButtons = document.querySelectorAll('.add-to-cart');

        addToCartButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Add loading state
                const originalText = this.textContent;
                this.textContent = 'Adding...';
                this.disabled = true;

                // Simulate add to cart
                setTimeout(() => {
                    this.textContent = 'Added ✓';
                    this.style.background = '#27ae60';

                    setTimeout(() => {
                        this.textContent = originalText;
                        this.disabled = false;
                        this.style.background = '';
                    }, 2000);
                }, 800);
            });
        });
    }

    // Initialize optional features
    initMobileMenu();
    initGalleryLightbox();
    initNewsletterValidation();
    initAddToCartAnimation();

    // Optional: Uncomment to enable scroll progress bar
    // initScrollProgress();

})();
