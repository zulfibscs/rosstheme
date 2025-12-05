<?php
/**
 * Header Template: E-commerce Shop
 * Optimized layout with icons for cart, wishlist, and account
 * Perfect for online stores, marketplaces, and retail websites
 */
return array(
    'id' => 'ecommerce-shop',
    'title' => 'E-commerce Shop',
    'description' => 'Feature-rich layout optimized for online stores with cart and user icons',
    'icon' => '🛒',
    'preview_image' => 'ecommerce-shop.png',
    
    // Layout Configuration
    'layout' => 'horizontal',
    'logo_position' => 'left',
    'menu_position' => 'center',
    'cta_position' => 'right',
    
    // Design Settings
    'bg' => '#ffffff',
    'text' => '#1f2937',
    'accent' => '#dc2626',
    'hover' => '#b91c1c',
    'border_bottom' => '#f3f4f6',
    'sticky_bg' => '#ffffff',
    'sticky_shadow' => 'rgba(0, 0, 0, 0.08)',
    
    // Typography
    'font_size' => '14px',
    'font_weight' => '500',
    'letter_spacing' => '0.3px',
    
    // Spacing
    'padding_top' => '15',
    'padding_bottom' => '15',
    'container_width' => 'contained',
    
    // Features
    'sticky_enabled' => true,
    'sticky_behavior' => 'always',
    'search_enabled' => true,
    'search_style' => 'inline', // Shows search bar inline
    'mobile_breakpoint' => '768',
    
    // E-commerce Specific Icons
    'icons' => array(
        'search' => true,
        'wishlist' => true,
        'cart' => true,
        'account' => true
    ),
    
    // CTA Button
    'cta' => array(
        'enabled' => true,
        'text' => 'Sale',
        'url' => '/shop/sale',
        'style' => 'solid',
        'bg' => '#dc2626',
        'color' => '#ffffff',
        'hover_bg' => '#b91c1c',
        'border_radius' => '4px',
        'padding' => '8px 16px',
        'badge' => 'Up to 50% OFF' // Optional badge
    ),
    
    // Mobile Menu
    'mobile' => array(
        'toggle_style' => 'hamburger',
        'animation' => 'slide',
        'position' => 'sidebar',
        'bg' => '#ffffff',
        'overlay' => true,
        'show_icons' => true // Show wishlist, cart in mobile
    ),
    
    // Animation
    'animation' => array(
        'menu_items' => 'fade-in',
        'sticky_transition' => 'smooth',
        'duration' => '250ms'
    ),
    
    // Topbar Support
    'topbar' => array(
        'enabled' => true,
        'content_left' => 'Free shipping on orders over $50',
        'content_right' => 'Track Order | Help',
        'bg' => '#1f2937',
        'text' => '#ffffff'
    )
);
