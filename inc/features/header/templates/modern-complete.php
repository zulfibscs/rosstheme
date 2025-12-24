<?php
/**
 * Header Template: Modern Complete
 * Complete responsive header with all features from the provided code
 * Fully responsive with mobile-first design and advanced functionality
 */
return array(
    'id' => 'modern-complete',
    'title' => 'Modern Complete',
    'description' => 'Complete responsive header with top bar, announcement, search, CTA, and mobile drawer - matches exact HTML structure',
    'icon' => '🎯',
    'preview_image' => 'responsive-modern-complete.png',

    // Layout Configuration
    'layout' => 'responsive',
    'logo_position' => 'left',
    'menu_position' => 'left',
    'cta_position' => 'right',

    // Design Settings
    'bg' => '#ffffff',
    'text' => '#333333',
    'accent' => '#E5C902',
    'primary' => '#001946',
    'hover' => '#d4af37',
    'border_bottom' => '#f0f0f0',
    'sticky_bg' => 'rgba(255, 255, 255, 0.98)',
    'sticky_shadow' => 'rgba(0, 0, 0, 0.1)',

    // Typography
    'font_size' => '16px',
    'font_weight' => '600',
    'letter_spacing' => '0.5px',
    'text_transform' => 'none',

    // Spacing
    'padding_top' => '15',
    'padding_bottom' => '15',
    'container_width' => 'contained',
    'logo_margin_bottom' => '0',

    // Features - ALL ENABLED by default to match HTML
    'sticky_enabled' => true,
    'sticky_behavior' => 'on-scroll',
    'search_enabled' => true,
    'search_style' => 'overlay',
    'mobile_breakpoint' => '768',

    // CTA Button - EXACTLY as in HTML
    'cta' => array(
        'enabled' => true,
        'text' => 'Get Started',
        'url' => '#',
        'style' => 'gradient',
        'color' => 'linear-gradient(135deg, #E5C902 0%, #ffd700 100%)',
        'text_color' => '#001946',
        'hover_text_color' => '#001946',
        'font_size' => '16',
        'border_radius' => '8',
        'size' => 'medium',
        'hover_effect' => 'scale',
        'icon' => 'fas fa-arrow-right',
        'icon_position' => 'right',
        'target' => '_self',
        'mobile_hide' => true
    ),

    // Mobile Settings - EXACTLY as in HTML
    'mobile' => array(
        'layout' => 'drawer',
        'logo_width' => '150',
        'menu_toggle' => 'hamburger',
        'search_position' => 'overlay',
        'header_height' => '70',
        'drawer_width' => '90%',
        'drawer_bg' => '#ffffff',
        'drawer_shadow' => '-5px 0 30px rgba(0, 0, 0, 0.15)'
    ),

    // Tablet Settings
    'tablet' => array(
        'layout' => 'horizontal',
        'logo_width' => '180',
        'breakpoint_min' => '769px',
        'breakpoint_max' => '1024px',
        'header_height' => '80'
    ),

    // Top Bar Settings - ENABLED by default to match HTML
    'topbar' => array(
        'enabled' => true,
        'bg' => 'linear-gradient(135deg, #001946 0%, #003d7a 100%)',
        'text' => '#ffffff',
        'phone' => '+1 (123) 456-7890',
        'email' => 'info@example.com',
        'social_enabled' => true,
        'social_links' => array(
            'facebook' => '#',
            'twitter' => '#',
            'instagram' => '#',
            'linkedin' => '#'
        ),
        'social_size' => '32px',
        'social_shape' => 'circle',
        'social_bg' => 'rgba(255, 255, 255, 0.1)',
        'social_hover_bg' => '#E5C902',
        'social_color' => '#ffffff'
    ),

    // Announcement Bar Settings - ENABLED by default to match HTML
    'announcement' => array(
        'enabled' => true,
        'text' => '🎉 Special Offer: Get 20% off on all services this month! Limited time offer.',
        'animation' => 'marquee',
        'position' => 'above_header',
        'bg' => 'linear-gradient(90deg, #E5C902 0%, #ffd700 100%)',
        'text_color' => '#001946',
        'font_size' => '14',
        'font_weight' => '600',
        'speed' => '25s'
    ),

    // Search Settings - EXACTLY as in HTML
    'search' => array(
        'enabled' => true,
        'type' => 'overlay',
        'placeholder' => 'Search for products, articles, guides...',
        'overlay_bg' => 'rgba(0, 0, 0, 0.95)',
        'input_bg' => '#ffffff',
        'input_border_radius' => '50px',
        'mobile_hide' => false
    ),

    // Menu Settings - LEFT ALIGNED as in HTML
    'menu' => array(
        'alignment' => 'left',
        'font_size' => '16',
        'font_weight' => '600',
        'hover_effect' => 'underline',
        'dropdown_animation' => 'fade',
        'dropdown_bg' => '#ffffff',
        'dropdown_shadow' => '0 10px 30px rgba(0, 0, 0, 0.1)',
        'dropdown_border_radius' => '8px',
        'depth' => '2'
    ),

    // Advanced Features
    'advanced' => array(
        'smooth_scroll' => true,
        'touch_friendly' => true,
        'accessibility' => true,
        'performance' => true,
        'seo_friendly' => true,
        'dark_mode_support' => true,
        'print_styles' => true
    )
);