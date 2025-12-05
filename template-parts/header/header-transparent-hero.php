<?php
/**
 * Header Template: Transparent Hero
 * Overlay header designed for hero sections with background images/videos
 */

$header_options = get_option('ross_theme_header_options', array());
$logo = isset($header_options['logo_upload']) ? $header_options['logo_upload'] : '';
$logo_dark = isset($header_options['logo_dark']) ? $header_options['logo_dark'] : $logo;
$mobile_logo = isset($header_options['mobile_logo']) ? $header_options['mobile_logo'] : $logo;
$logo_width = isset($header_options['logo_width']) ? absint($header_options['logo_width']) : 180;
$mobile_logo_width = isset($header_options['mobile_logo_width']) ? absint($header_options['mobile_logo_width']) : 120;
$enable_search = isset($header_options['enable_search']) ? $header_options['enable_search'] : 1;
$enable_cta = isset($header_options['enable_cta_button']) ? $header_options['enable_cta_button'] : 1;
$cta_text = isset($header_options['cta_button_text']) ? $header_options['cta_button_text'] : 'Get Started';
$cta_url = isset($header_options['cta_button_url']) ? $header_options['cta_button_url'] : '/contact';
$cta_style = isset($header_options['cta_button_style']) ? $header_options['cta_button_style'] : 'outline';
?>

<header class="site-header header-transparent-hero has-transparent-overlay" role="banner">
    <div class="header-container">
        <div class="header-inner">
            
            <!-- Logo -->
            <div class="site-branding">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                    <?php if ($logo): ?>
                        <!-- Use dark logo for transparent header, switches on scroll -->
                        <img src="<?php echo esc_url($logo); ?>" 
                             alt="<?php bloginfo('name'); ?>" 
                             class="logo-light desktop-logo"
                             style="max-width: <?php echo esc_attr($logo_width); ?>px;">
                        <?php if ($logo_dark): ?>
                            <img src="<?php echo esc_url($logo_dark); ?>" 
                                 alt="<?php bloginfo('name'); ?>" 
                                 class="logo-dark desktop-logo"
                                 style="max-width: <?php echo esc_attr($logo_width); ?>px; display: none;">
                        <?php endif; ?>
                        <?php if ($mobile_logo && $mobile_logo !== $logo): ?>
                            <img src="<?php echo esc_url($mobile_logo); ?>" 
                                 alt="<?php bloginfo('name'); ?>" 
                                 class="mobile-logo"
                                 style="max-width: <?php echo esc_attr($mobile_logo_width); ?>px;">
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="site-title"><?php bloginfo('name'); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="header-navigation" role="navigation" aria-label="Primary Navigation">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'primary-menu transparent-menu',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                }
                ?>
            </nav>
            
            <!-- Right Actions -->
            <div class="header-actions">
                <?php if ($enable_search): ?>
                    <button class="header-search-toggle transparent-icon-btn" aria-label="Search">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="9" r="8"/>
                            <path d="M21 21l-6-6"/>
                        </svg>
                    </button>
                <?php endif; ?>
                
                <?php if ($enable_cta): ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="header-cta-button transparent-cta cta-<?php echo esc_attr($cta_style); ?>">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button class="menu-toggle transparent-menu-toggle" aria-label="Toggle Menu" aria-expanded="false">
                    <span class="hamburger-icon">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </span>
                </button>
            </div>
            
        </div>
    </div>
</header>

<style>
/* Transparent Hero Header Styles */
.header-transparent-hero {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    background: transparent;
    padding: 25px 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Scrolled State */
.header-transparent-hero.is-sticky {
    position: fixed;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 15px 0;
}

/* Logo Switching on Scroll */
.header-transparent-hero .logo-light {
    display: block;
}

.header-transparent-hero .logo-dark {
    display: none;
}

.header-transparent-hero.is-sticky .logo-light {
    display: none;
}

.header-transparent-hero.is-sticky .logo-dark {
    display: block;
}

/* Container */
.header-transparent-hero .header-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 40px;
}

.header-transparent-hero .header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 50px;
}

/* Logo */
.header-transparent-hero .site-branding {
    flex-shrink: 0;
    z-index: 10;
}

.header-transparent-hero .site-logo {
    display: inline-block;
    transition: transform 0.3s ease;
}

.header-transparent-hero .site-logo:hover {
    transform: scale(1.05);
}

.header-transparent-hero .site-title {
    font-size: 28px;
    font-weight: 600;
    color: #fff;
    letter-spacing: -0.5px;
}

.header-transparent-hero.is-sticky .site-title {
    color: #111827;
}

/* Navigation */
.header-transparent-hero .header-navigation {
    flex: 1;
    display: flex;
    justify-content: center;
    z-index: 10;
}

.header-transparent-hero .transparent-menu {
    display: flex;
    gap: 45px;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}

.header-transparent-hero .transparent-menu a {
    text-decoration: none;
    color: rgba(255, 255, 255, 0.95);
    font-size: 16px;
    font-weight: 500;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
    position: relative;
    padding: 8px 0;
}

.header-transparent-hero.is-sticky .transparent-menu a {
    color: #374151;
}

.header-transparent-hero .transparent-menu a:hover,
.header-transparent-hero .transparent-menu .current-menu-item a {
    color: #fff;
    text-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.header-transparent-hero.is-sticky .transparent-menu a:hover,
.header-transparent-hero.is-sticky .transparent-menu .current-menu-item a {
    color: #111827;
    text-shadow: none;
}

.header-transparent-hero .transparent-menu a::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: #fff;
    transition: width 0.3s ease;
}

.header-transparent-hero.is-sticky .transparent-menu a::after {
    background: #111827;
}

.header-transparent-hero .transparent-menu a:hover::after,
.header-transparent-hero .transparent-menu .current-menu-item a::after {
    width: 100%;
}

/* Actions */
.header-transparent-hero .header-actions {
    display: flex;
    gap: 20px;
    align-items: center;
    z-index: 10;
}

.header-transparent-hero .transparent-icon-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.95);
    cursor: pointer;
    padding: 10px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-transparent-hero.is-sticky .transparent-icon-btn {
    color: #374151;
}

.header-transparent-hero .transparent-icon-btn:hover {
    transform: scale(1.1);
    color: #fff;
}

.header-transparent-hero.is-sticky .transparent-icon-btn:hover {
    color: #111827;
}

/* CTA Button Styles */
.transparent-cta {
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.3px;
}

/* Outline Style (Default for Transparent) */
.transparent-cta.cta-outline {
    background: transparent;
    border: 2px solid rgba(255, 255, 255, 0.8);
    color: #fff;
}

.transparent-cta.cta-outline:hover {
    background: rgba(255, 255, 255, 1);
    color: #111827;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
}

.header-transparent-hero.is-sticky .transparent-cta.cta-outline {
    border-color: #111827;
    color: #111827;
}

.header-transparent-hero.is-sticky .transparent-cta.cta-outline:hover {
    background: #111827;
    color: #fff;
}

/* Solid Style */
.transparent-cta.cta-solid {
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid transparent;
    color: #111827;
}

.transparent-cta.cta-solid:hover {
    background: #fff;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(255, 255, 255, 0.3);
}

/* Ghost Style */
.transparent-cta.cta-ghost {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    backdrop-filter: blur(10px);
}

.transparent-cta.cta-ghost:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

/* Hamburger */
.transparent-menu-toggle {
    display: none;
    background: transparent;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.transparent-menu-toggle .hamburger-icon {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.transparent-menu-toggle .hamburger-line {
    width: 26px;
    height: 2px;
    background: rgba(255, 255, 255, 0.95);
    transition: all 0.3s ease;
    border-radius: 2px;
}

.header-transparent-hero.is-sticky .transparent-menu-toggle .hamburger-line {
    background: #111827;
}

/* Mobile Responsive */
@media (max-width: 1024px) {
    .header-transparent-hero .transparent-menu {
        gap: 30px;
    }
}

@media (max-width: 768px) {
    .header-transparent-hero {
        padding: 20px 0;
    }
    
    .header-transparent-hero .header-container {
        padding: 0 20px;
    }
    
    .header-transparent-hero .header-inner {
        gap: 20px;
    }
    
    .header-transparent-hero .header-navigation {
        position: fixed;
        top: 0;
        left: -100%;
        width: 85%;
        max-width: 350px;
        height: 100vh;
        background: rgba(17, 24, 39, 0.98);
        backdrop-filter: blur(20px);
        padding: 100px 30px 30px;
        transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 999;
        overflow-y: auto;
    }
    
    .header-transparent-hero .header-navigation.open {
        left: 0;
    }
    
    .header-transparent-hero .transparent-menu {
        flex-direction: column;
        gap: 30px;
        align-items: flex-start;
    }
    
    .header-transparent-hero .transparent-menu a {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.95);
    }
    
    .transparent-menu-toggle {
        display: flex;
    }
    
    .transparent-cta {
        padding: 10px 20px;
        font-size: 14px;
    }
}

/* Mobile Logo */
.header-transparent-hero .mobile-logo {
    display: none;
}

@media (max-width: 768px) {
    .header-transparent-hero .desktop-logo {
        display: none !important;
    }
    
    .header-transparent-hero .mobile-logo {
        display: block !important;
    }
}

/* Overlay Effect (if enabled in settings) */
.header-transparent-hero.has-transparent-overlay::before {
    opacity: 0;
    transition: opacity 0.4s ease;
}

.header-transparent-hero.is-sticky::before {
    opacity: 0 !important;
}
</style>

<script>
// Handle logo switching on scroll
(function() {
    var header = document.querySelector('.header-transparent-hero');
    if (!header) return;
    
    var lightLogo = header.querySelector('.logo-light');
    var darkLogo = header.querySelector('.logo-dark');
    
    if (!lightLogo || !darkLogo) return;
    
    window.addEventListener('scroll', function() {
        var scrolled = window.pageYOffset > 50;
        
        if (scrolled) {
            lightLogo.style.display = 'none';
            darkLogo.style.display = 'block';
        } else {
            lightLogo.style.display = 'block';
            darkLogo.style.display = 'none';
        }
    });
})();
</script>
