<?php
/**
 * Header Template: Business Classic
 * Professional layout: Logo Left, Navigation Center, CTA Right
 * Perfect for corporate websites, professional services, and B2B companies
 */
return array(
    'id' => 'business-classic',
    'title' => 'Business Classic',
    'description' => 'Professional layout with logo left, navigation center, and CTA button right',
    'icon' => '💼',
    'preview_image' => 'business-classic.png',
    
    // Layout Configuration
    'layout' => 'horizontal', // horizontal, stacked, centered, split
    'logo_position' => 'left', // left, center, right
    'menu_position' => 'center', // left, center, right
    'cta_position' => 'right', // left, right, none
    
    // Design Settings
    'bg' => '#ffffff',
    'text' => '#0b2140',
    'accent' => '#0b66a6',
    'hover' => '#084578',
    'border_bottom' => '#e5e7eb',
    'sticky_bg' => '#ffffff',
    'sticky_shadow' => 'rgba(0, 0, 0, 0.1)',
    
    // Typography
    'font_size' => '16px',
    'font_weight' => '500',
    'letter_spacing' => '0.5px',
    
    // Spacing
    'padding_top' => '20',
    'padding_bottom' => '20',
    'container_width' => 'contained', // contained, full
    
    // Features
    'sticky_enabled' => true,
    'sticky_behavior' => 'always', // always, scroll-up, scroll-down
    'search_enabled' => true,
    'search_style' => 'icon', // icon, button, inline
    'mobile_breakpoint' => '768',
    
    // CTA Button
    'cta' => array(
        'enabled' => true,
        'text' => 'Get Started',
        'url' => '#contact',
        'style' => 'solid', // solid, outline, ghost
        'bg' => '#0b66a6',
        'color' => '#ffffff',
        'hover_bg' => '#084578',
        'border_radius' => '6px',
        'padding' => '12px 24px'
    ),
    
    // Mobile Menu
    'mobile' => array(
        'toggle_style' => 'hamburger', // hamburger, dots, text
        'animation' => 'slide', // slide, fade, push
        'position' => 'full', // full, dropdown, sidebar
        'bg' => '#ffffff',
        'overlay' => true
    ),
    
    // Animation
    'animation' => array(
        'menu_items' => 'fade-in', // fade-in, slide-up, none
        'sticky_transition' => 'smooth', // smooth, instant
        'duration' => '300ms'
    )
);
