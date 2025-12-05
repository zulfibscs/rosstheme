<?php
/**
 * Header Template: Minimal Modern
 * Ultra-clean design with minimal elements and maximum whitespace
 */

$header_options = get_option('ross_theme_header_options', array());
$logo = isset($header_options['logo_upload']) ? $header_options['logo_upload'] : '';
$mobile_logo = isset($header_options['mobile_logo']) ? $header_options['mobile_logo'] : $logo;
$logo_width = isset($header_options['logo_width']) ? absint($header_options['logo_width']) : 160;
$mobile_logo_width = isset($header_options['mobile_logo_width']) ? absint($header_options['mobile_logo_width']) : 120;
$enable_search = isset($header_options['enable_search']) ? $header_options['enable_search'] : 1;
?>

<header class="site-header header-minimal-modern" role="banner">
    <div class="header-container">
        <div class="header-inner">
            
            <!-- Logo Section -->
            <div class="site-branding">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
                    <?php if ($logo): ?>
                        <img src="<?php echo esc_url($logo); ?>" 
                             alt="<?php bloginfo('name'); ?>" 
                             class="desktop-logo"
                             style="max-width: <?php echo esc_attr($logo_width); ?>px;">
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
                        'menu_class' => 'primary-menu minimal-menu',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                }
                ?>
            </nav>
            
            <!-- Right Actions -->
            <div class="header-actions">
                <?php if ($enable_search): ?>
                    <button class="header-search-toggle minimal-icon-btn" aria-label="Search">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button class="menu-toggle minimal-icon-btn" aria-label="Toggle Menu" aria-expanded="false">
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
/* Minimal Modern Header Styles */
.header-minimal-modern {
    background: transparent;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    padding: 20px 0;
    transition: all 0.3s ease;
}

.header-minimal-modern.is-sticky {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-minimal-modern .header-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.header-minimal-modern .header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
}

/* Logo */
.header-minimal-modern .site-branding {
    flex-shrink: 0;
}

.header-minimal-modern .site-logo {
    display: inline-block;
    text-decoration: none;
    transition: opacity 0.2s ease;
}

.header-minimal-modern .site-logo:hover {
    opacity: 0.7;
}

.header-minimal-modern .site-title {
    font-size: 24px;
    font-weight: 300;
    letter-spacing: -0.5px;
    color: #111827;
}

/* Navigation */
.header-minimal-modern .header-navigation {
    flex: 1;
    display: flex;
    justify-content: center;
}

.header-minimal-modern .minimal-menu {
    display: flex;
    gap: 40px;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}

.header-minimal-modern .minimal-menu a {
    text-decoration: none;
    color: #111827;
    font-size: 15px;
    font-weight: 400;
    letter-spacing: 0.3px;
    transition: color 0.2s ease;
    position: relative;
}

.header-minimal-modern .minimal-menu a:hover,
.header-minimal-modern .minimal-menu .current-menu-item a {
    color: #6b7280;
}

.header-minimal-modern .minimal-menu a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 1px;
    background: currentColor;
    transition: width 0.3s ease;
}

.header-minimal-modern .minimal-menu a:hover::after,
.header-minimal-modern .minimal-menu .current-menu-item a::after {
    width: 100%;
}

/* Actions */
.header-minimal-modern .header-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.header-minimal-modern .minimal-icon-btn {
    background: transparent;
    border: none;
    color: #111827;
    cursor: pointer;
    padding: 8px;
    transition: opacity 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.header-minimal-modern .minimal-icon-btn:hover {
    opacity: 0.6;
}

.header-minimal-modern .hamburger-icon {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.header-minimal-modern .hamburger-line {
    width: 20px;
    height: 1px;
    background: currentColor;
    transition: all 0.3s ease;
}

.header-minimal-modern .menu-toggle {
    display: none;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .header-minimal-modern .header-inner {
        gap: 20px;
    }
    
    .header-minimal-modern .header-navigation {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        max-width: 300px;
        height: 100vh;
        background: #fff;
        padding: 80px 30px 30px;
        transition: left 0.3s ease;
        z-index: 999;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
    }
    
    .header-minimal-modern .header-navigation.open {
        left: 0;
    }
    
    .header-minimal-modern .minimal-menu {
        flex-direction: column;
        gap: 25px;
        align-items: flex-start;
    }
    
    .header-minimal-modern .minimal-menu a {
        font-size: 16px;
    }
    
    .header-minimal-modern .menu-toggle {
        display: flex;
    }
}

/* Mobile Logo Switching */
.header-minimal-modern .mobile-logo {
    display: none;
}

@media (max-width: 768px) {
    .header-minimal-modern .desktop-logo {
        display: none;
    }
    
    .header-minimal-modern .mobile-logo {
        display: block;
    }
}
</style>
