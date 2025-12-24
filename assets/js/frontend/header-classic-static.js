// Static Complete Header JavaScript
// Enhanced sticky header functionality with performance optimizations

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const header = document.getElementById('mainHeader');
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileNavDrawer = document.getElementById('mobileNavDrawer');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    const mobileClose = document.getElementById('mobileClose');
    const searchToggle = document.querySelector('.search-toggle');
    const searchOverlay = document.querySelector('.search-overlay');
    const searchClose = document.querySelector('.search-close');
    const mobileDropdowns = document.querySelectorAll('.mobile-menu .dropdown-toggle');

    // IMPROVED: Better sticky header implementation
    let lastScrollTop = 0;
    let ticking = false;
    const stickyThreshold = 100;
    const topBar = document.getElementById('topBar');
    const announcementBar = document.getElementById('announcementBar');

    function handleStickyHeader() {
        if (!ticking) {
            requestAnimationFrame(function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > stickyThreshold) {
                    if (!header.classList.contains('sticky')) {
                        header.classList.add('sticky');
                        // Hide top bar and announcement when sticky for cleaner look
                        if (topBar) topBar.style.display = 'none';
                        if (announcementBar) announcementBar.style.display = 'none';
                    }
                } else {
                    if (header.classList.contains('sticky')) {
                        header.classList.remove('sticky');
                        // Show top bar and announcement when not sticky
                        if (topBar) topBar.style.display = 'block';
                        if (announcementBar) announcementBar.style.display = 'block';
                    }
                }

                lastScrollTop = scrollTop;
                ticking = false;
            });
            ticking = true;
        }
    }

    // IMPROVED: Throttled scroll listener for better performance
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (!scrollTimeout) {
            scrollTimeout = setTimeout(function() {
                handleStickyHeader();
                scrollTimeout = null;
            }, 16); // ~60fps
        }
    });

    // Mobile Menu Toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mobileNavDrawer.classList.toggle('active');
            mobileNavOverlay.classList.toggle('active');
            this.setAttribute('aria-expanded', this.classList.contains('active'));
            document.body.style.overflow = this.classList.contains('active') ? 'hidden' : '';
        });
    }

    // Close Mobile Menu
    function closeMobileMenu() {
        if (mobileToggle) {
            mobileToggle.classList.remove('active');
            mobileToggle.setAttribute('aria-expanded', 'false');
        }
        if (mobileNavDrawer) mobileNavDrawer.classList.remove('active');
        if (mobileNavOverlay) mobileNavOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (mobileClose) {
        mobileClose.addEventListener('click', closeMobileMenu);
    }

    if (mobileNavOverlay) {
        mobileNavOverlay.addEventListener('click', closeMobileMenu);
    }

    // Mobile Dropdowns
    mobileDropdowns.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const dropdown = this.parentElement.nextElementSibling;
            if (dropdown) {
                dropdown.classList.toggle('active');
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');
                }
            }
        });
    });

    // Search Toggle
    if (searchToggle) {
        searchToggle.addEventListener('click', function() {
            if (searchOverlay) {
                searchOverlay.classList.add('active');
                const searchInput = document.querySelector('.search-input');
                if (searchInput) searchInput.focus();
                document.body.style.overflow = 'hidden';
            }
        });
    }

    if (searchClose) {
        searchClose.addEventListener('click', function() {
            if (searchOverlay) {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Close search on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (searchOverlay) {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });

    // Close mobile menu on link click
    document.querySelectorAll('.mobile-menu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.querySelector('.dropdown-toggle')) {
                closeMobileMenu();
            }
        });
    });

    // Handle window resize
    function handleResize() {
        // Close mobile menu if switching to desktop
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
        // Close search overlay on mobile
        if (window.innerWidth <= 768) {
            if (searchOverlay) {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    }

    window.addEventListener('resize', handleResize);

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
                closeMobileMenu();
            }
        });
    });

    // Initialize
    if (header) {
        header.classList.add('sticky-enabled');
        window.addEventListener('scroll', handleStickyHeader);
        handleStickyHeader(); // Initial check
    }
});