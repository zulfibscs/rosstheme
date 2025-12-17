<?php
/**
 * Header Template: Creative Agency
 * Bold, modern design with centered logo and full-width navigation
 * Perfect for design studios, creative agencies, and portfolio sites
 */
return array(
    'id' => 'creative-agency',
    'title' => 'Creative Agency',
    'description' => 'Bold centered design perfect for creative studios and agencies',
    'icon' => '🎨',
    'preview_image' => 'creative-agency.png',
    
    // Layout Configuration
    'layout' => 'stacked', // horizontal, stacked, centered, split
    'logo_position' => 'center',
    'menu_position' => 'center',
    'cta_position' => 'right',
    
    // Design Settings
    'bg' => '#0c0c0d',
    'text' => '#f3f4f6',
    'accent' => '#E5C902',
    'hover' => '#ffd633',
    'border_bottom' => 'transparent',
    'sticky_bg' => '#0c0c0d',
    'sticky_shadow' => 'rgba(229, 201, 2, 0.15)',
    
    // Typography
    'font_size' => '15px',
    'font_weight' => '600',
    'letter_spacing' => '1.2px',
    'text_transform' => 'uppercase',
    
    // Spacing
    'padding_top' => '30',
    'padding_bottom' => '30',
    'container_width' => 'contained',
    'logo_margin_bottom' => '20',
    
    // Features
    'sticky_enabled' => true,
    'sticky_behavior' => 'scroll-up',
    'search_enabled' => true,
    'search_style' => 'icon',
    'mobile_breakpoint' => '768',
    
    // CTA Button
    'cta' => array(
        'enabled' => true,
        'text' => 'Start Project',
        'url' => '#contact',
        'style' => 'outline',
        'bg' => 'transparent',
        'color' => '#E5C902',
        'hover_bg' => '#E5C902',
        'hover_color' => '#0c0c0d',
        'border_color' => '#E5C902',
        'border_radius' => '30px',
        'padding' => '10px 30px'
    ),
    
    // Mobile Menu
    'mobile' => array(
        'toggle_style' => 'hamburger',
        'animation' => 'fade',
        'position' => 'full',
        'bg' => '#0c0c0d',
        'overlay' => true
    ),
    
    // Animation
    'animation' => array(
        'menu_items' => 'slide-up',
        'sticky_transition' => 'smooth',
        'duration' => '400ms',
        'logo_scale' => '0.9' // Scale down logo on sticky
    )
);
