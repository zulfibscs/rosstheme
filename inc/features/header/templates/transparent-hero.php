<?php
/**
 * Header Template: Transparent Hero
 * Overlay header that sits on top of hero images/videos
 * Perfect for landing pages, marketing sites, and visual-heavy websites
 */
return array(
    'id' => 'transparent-hero',
    'title' => 'Transparent Hero',
    'description' => 'Overlay design that sits on top of hero sections with smooth transitions',
    'icon' => '🌅',
    'preview_image' => 'transparent-hero.png',
    
    // Layout Configuration
    'layout' => 'horizontal',
    'logo_position' => 'left',
    'menu_position' => 'right',
    'cta_position' => 'right',
    'position' => 'absolute', // absolute positioning over content
    
    // Design Settings
    'bg' => 'transparent',
    'text' => '#ffffff',
    'accent' => '#E5C902',
    'hover' => '#ffd633',
    'border_bottom' => 'rgba(255, 255, 255, 0.1)',
    'sticky_bg' => '#001946',
    'sticky_shadow' => 'rgba(0, 0, 0, 0.2)',
    
    // Typography
    'font_size' => '15px',
    'font_weight' => '500',
    'letter_spacing' => '0.8px',
    
    // Spacing
    'padding_top' => '30',
    'padding_bottom' => '30',
    'container_width' => 'contained',
    
    // Features
    'sticky_enabled' => true,
    'sticky_behavior' => 'always',
    'sticky_transforms_bg' => true, // Changes from transparent to solid on scroll
    'search_enabled' => true,
    'search_style' => 'icon',
    'mobile_breakpoint' => '768',
    
    // CTA Button
    'cta' => array(
        'enabled' => true,
        'text' => 'Get Started Free',
        'url' => '#signup',
        'style' => 'solid',
        'bg' => '#E5C902',
        'color' => '#001946',
        'hover_bg' => '#ffd633',
        'border_radius' => '6px',
        'padding' => '12px 28px',
        'font_weight' => '600'
    ),
    
    // Mobile Menu
    'mobile' => array(
        'toggle_style' => 'hamburger',
        'animation' => 'slide',
        'position' => 'full',
        'bg' => '#001946',
        'overlay' => true
    ),
    
    // Animation
    'animation' => array(
        'menu_items' => 'fade-in',
        'sticky_transition' => 'smooth',
        'duration' => '350ms',
        'color_transition' => true // Smooth text color change on sticky
    ),
    
    // Transparent Specific
    'transparent' => array(
        'homepage_only' => true, // Only transparent on homepage
        'scroll_threshold' => 100, // Pixels before becoming solid
        'text_shadow' => 'rgba(0, 0, 0, 0.3)' // For readability over images
    )
);
