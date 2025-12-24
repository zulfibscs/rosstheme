        // Header JavaScript - IMPROVED: Better sticky header functionality
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

            // Control buttons
            const toggleSticky = document.getElementById('toggleSticky');
            const toggleTopBar = document.getElementById('toggleTopBar');
            const toggleAnnouncement = document.getElementById('toggleAnnouncement');
            const toggleWidth = document.getElementById('toggleWidth');
            const toggleSearch = document.getElementById('toggleSearch');
            const toggleCTA = document.getElementById('toggleCTA');
            const toggleSocial = document.getElementById('toggleSocial');

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

            // Initial check
            handleStickyHeader();

            toggleTopBar.addEventListener('click', function() {
                const topBar = document.getElementById('topBar');
                topBar.classList.toggle('show');
                this.textContent = topBar.classList.contains('show') ? 'Hide Top Bar' : 'Show Top Bar';
                this.classList.toggle('active');
            });

            toggleAnnouncement.addEventListener('click', function() {
                const announcementBar = document.getElementById('announcementBar');
                announcementBar.classList.toggle('show');
                this.textContent = announcementBar.classList.contains('show') ? 'Hide Announcement' : 'Show Announcement';
                this.classList.toggle('active');
            });

            toggleWidth.addEventListener('click', function() {
                if (header.classList.contains('header-contained')) {
                    header.classList.remove('header-contained');
                    header.classList.add('header-fullwidth');
                    this.textContent = 'Contained Width';
                } else {
                    header.classList.remove('header-fullwidth');
                    header.classList.add('header-contained');
                    this.textContent = 'Full Width';
                }
                this.classList.toggle('active');
            });

            toggleSearch.addEventListener('change', function() {
                const search = document.querySelector('.header-search');
                search.style.display = this.checked ? 'block' : 'none';
            });

            toggleCTA.addEventListener('change', function() {
                const ctaButtons = document.querySelectorAll('.cta-button:not(.mobile-cta)');
                ctaButtons.forEach(btn => {
                    btn.style.display = this.checked ? 'flex' : 'none';
                });
            });

            toggleSocial.addEventListener('change', function() {
                const socialLinks = document.querySelector('.social-links');
                socialLinks.style.display = this.checked ? 'flex' : 'none';
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
                    searchOverlay.classList.remove('active');
                    document.body.style.overflow = '';
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
            document.getElementById('topBar').classList.add('show');
            document.getElementById('announcementBar').classList.add('show');
            header.classList.add('sticky-enabled');
            window.addEventListener('scroll', handleStickyHeader);
            handleStickyHeader(); // Initial check
        });

        // Add sample content for scrolling
        document.addEventListener('DOMContentLoaded', function() {
            const contentSection = document.querySelector('.demo-section:last-child');
            const contentDiv = contentSection.querySelector('div[style*="height: 800px"]');
            
            for (let i = 0; i < 20; i++) {
                const paragraph = document.createElement('p');
                paragraph.textContent = `Sample content paragraph ${i + 1}. This helps demonstrate the sticky header effect when scrolling.`;
                paragraph.style.marginBottom = '20px';
                paragraph.style.padding = '15px';
                paragraph.style.background = i % 2 === 0 ? '#f8f9fa' : '#ffffff';
                paragraph.style.borderRadius = '6px';
                contentDiv.appendChild(paragraph);
            }
        });