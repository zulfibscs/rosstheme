<?php
/**
 * Header Template: E-commerce Shop
 * Feature-rich header with cart, account, wishlist, and promotional space
 */

$header_options = get_option('ross_theme_header_options', array());
$logo = isset($header_options['logo_upload']) ? $header_options['logo_upload'] : '';
$mobile_logo = isset($header_options['mobile_logo']) ? $header_options['mobile_logo'] : $logo;
$logo_width = isset($header_options['logo_width']) ? absint($header_options['logo_width']) : 180;
$mobile_logo_width = isset($header_options['mobile_logo_width']) ? absint($header_options['mobile_logo_width']) : 120;
$enable_search = isset($header_options['enable_search']) ? $header_options['enable_search'] : 1;
$enable_cta = isset($header_options['enable_cta_button']) ? $header_options['enable_cta_button'] : 1;
$cta_text = isset($header_options['cta_button_text']) ? $header_options['cta_button_text'] : 'Shop Now';
$cta_url = isset($header_options['cta_button_url']) ? $header_options['cta_button_url'] : '/shop';
?>

<header class="site-header header-ecommerce-shop" role="banner">
    
    <!-- Top Utilities Bar -->
    <div class="header-utilities-bar">
        <div class="utilities-container">
            <div class="utilities-left">
                <span class="promo-text">🎉 Free Shipping on Orders Over $50!</span>
            </div>
            <div class="utilities-right">
                <a href="/account" class="utility-link">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2 2H6a6 6 0 00-6 6h14a6 6 0 00-6-6z"/>
                    </svg>
                    <span>Account</span>
                </a>
                <a href="/wishlist" class="utility-link">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 14.25l-1.1-1A7.5 7.5 0 011 6.5 4.5 4.5 0 019.5 2.5a4.5 4.5 0 018.5 4c0 2.7-1.9 5-4.9 6.75l-1.1 1z"/>
                    </svg>
                    <span>Wishlist</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <div class="header-main">
        <div class="header-container">
            <div class="header-inner">
                
                <!-- Logo -->
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
                
                <!-- Search Bar (Prominent) -->
                <?php if ($enable_search): ?>
                <div class="header-search-bar">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="search" class="search-field" placeholder="Search products..." name="s" />
                        <button type="submit" class="search-submit">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM19 19l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
                <?php endif; ?>
                
                <!-- Shop Actions -->
                <div class="header-shop-actions">
                    <button class="shop-action-btn cart-btn" aria-label="Shopping Cart">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/>
                        </svg>
                        <span class="cart-count">0</span>
                    </button>
                    
                    <?php if ($enable_cta): ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="header-cta-button shop-cta">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                    <?php endif; ?>
                    
                    <button class="menu-toggle shop-menu-toggle" aria-label="Toggle Menu" aria-expanded="false">
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                        <span class="hamburger-line"></span>
                    </button>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Navigation Bar -->
    <div class="header-navigation-bar">
        <div class="header-container">
            <nav class="header-navigation" role="navigation" aria-label="Primary Navigation">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'primary-menu shop-menu',
                        'container' => false,
                        'fallback_cb' => false,
                    ));
                }
                ?>
            </nav>
        </div>
    </div>
    
</header>

<style>
/* E-commerce Shop Header Styles */
.header-ecommerce-shop {
    background: #fff;
}

/* Utilities Bar */
.header-utilities-bar {
    background: #1a1a1a;
    color: #fff;
    padding: 8px 0;
    font-size: 13px;
}

.header-utilities-bar .utilities-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-utilities-bar .utilities-right {
    display: flex;
    gap: 20px;
}

.header-utilities-bar .utility-link {
    color: #fff;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
    transition: opacity 0.2s;
}

.header-utilities-bar .utility-link:hover {
    opacity: 0.7;
}

/* Main Header */
.header-main {
    padding: 20px 0;
    border-bottom: 1px solid #e5e7eb;
}

.header-main .header-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.header-main .header-inner {
    display: flex;
    align-items: center;
    gap: 30px;
}

/* Logo */
.header-ecommerce-shop .site-branding {
    flex-shrink: 0;
}

.header-ecommerce-shop .site-logo {
    display: inline-block;
}

/* Search Bar */
.header-search-bar {
    flex: 1;
    max-width: 600px;
}

.header-search-bar .search-form {
    display: flex;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.2s;
}

.header-search-bar .search-form:focus-within {
    border-color: #3b82f6;
}

.header-search-bar .search-field {
    flex: 1;
    border: none;
    padding: 12px 15px;
    font-size: 15px;
    outline: none;
}

.header-search-bar .search-submit {
    background: #3b82f6;
    border: none;
    color: #fff;
    padding: 12px 20px;
    cursor: pointer;
    transition: background 0.2s;
}

.header-search-bar .search-submit:hover {
    background: #2563eb;
}

/* Shop Actions */
.header-shop-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.shop-action-btn {
    background: transparent;
    border: none;
    color: #1f2937;
    cursor: pointer;
    padding: 8px;
    position: relative;
    transition: color 0.2s;
}

.shop-action-btn:hover {
    color: #3b82f6;
}

.cart-count {
    position: absolute;
    top: 0;
    right: 0;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
}

.shop-cta {
    background: #3b82f6;
    color: #fff;
    padding: 10px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}

.shop-cta:hover {
    background: #2563eb;
}

.shop-menu-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: transparent;
    border: none;
    padding: 8px;
    cursor: pointer;
}

.shop-menu-toggle .hamburger-line {
    width: 24px;
    height: 2px;
    background: #1f2937;
    transition: all 0.3s;
}

/* Navigation Bar */
.header-navigation-bar {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.header-navigation-bar .header-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
}

.header-ecommerce-shop .shop-menu {
    display: flex;
    gap: 35px;
    list-style: none;
    margin: 0;
    padding: 15px 0;
}

.header-ecommerce-shop .shop-menu a {
    text-decoration: none;
    color: #1f2937;
    font-weight: 500;
    font-size: 15px;
    transition: color 0.2s;
    position: relative;
}

.header-ecommerce-shop .shop-menu a:hover,
.header-ecommerce-shop .shop-menu .current-menu-item a {
    color: #3b82f6;
}

.header-ecommerce-shop .shop-menu a::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 0;
    width: 0;
    height: 3px;
    background: #3b82f6;
    transition: width 0.3s;
}

.header-ecommerce-shop .shop-menu a:hover::after,
.header-ecommerce-shop .shop-menu .current-menu-item a::after {
    width: 100%;
}

/* Mobile Responsive */
@media (max-width: 1024px) {
    .header-search-bar {
        max-width: 400px;
    }
}

@media (max-width: 768px) {
    .header-utilities-bar .promo-text {
        font-size: 12px;
    }
    
    .header-utilities-bar .utilities-right {
        gap: 15px;
    }
    
    .header-utilities-bar .utility-link span {
        display: none;
    }
    
    .header-main .header-inner {
        gap: 15px;
    }
    
    .header-search-bar {
        display: none;
    }
    
    .shop-menu-toggle {
        display: flex;
    }
    
    .header-navigation-bar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 80%;
        max-width: 300px;
        height: 100vh;
        background: #fff;
        z-index: 999;
        transition: left 0.3s;
        overflow-y: auto;
    }
    
    .header-navigation-bar.open {
        left: 0;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .header-ecommerce-shop .shop-menu {
        flex-direction: column;
        gap: 0;
        padding: 80px 20px 20px;
    }
    
    .header-ecommerce-shop .shop-menu li {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .header-ecommerce-shop .shop-menu a {
        display: block;
        padding: 15px 0;
    }
    
    .header-ecommerce-shop .shop-menu a::after {
        display: none;
    }
}

/* Mobile Logo */
.header-ecommerce-shop .mobile-logo {
    display: none;
}

@media (max-width: 768px) {
    .header-ecommerce-shop .desktop-logo {
        display: none;
    }
    
    .header-ecommerce-shop .mobile-logo {
        display: block;
    }
}
</style>
