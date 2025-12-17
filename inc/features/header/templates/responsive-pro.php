<?php
/**
 * Responsive Pro Header Template Configuration
 * Mobile-first header with separate desktop/mobile controls
 */

return array(
    'id' => 'responsive-pro',
    'title' => 'Responsive Pro',
    'description' => 'Mobile-first header with separate desktop and mobile layouts. Perfect for responsive design with granular control over each breakpoint.',
    'icon' => '📱',
    'category' => 'modern',
    'version' => '1.0.0',
    'author' => 'Ross Theme',
    'bg' => '#ffffff',
    'text' => '#333333',
    'accent' => '#007cba',
    'features' => array(
        'mobile-first',
        'responsive-breakpoints',
        'separate-controls',
        'accessibility-ready',
        'touch-friendly'
    ),
    'sticky_enabled' => true,
    'search_enabled' => true,
    'cta' => array(
        'enabled' => true,
        'text' => 'Get Started',
        'bg' => '#007cba',
        'color' => '#ffffff',
        'border_radius' => '30px'
    ),
    'settings' => array(
        // Desktop Header Settings
        'desktop_header_enabled' => 1,
        'desktop_header_height' => 80,
        'desktop_logo_max_width' => 200,
        'desktop_menu_alignment' => 'center',
        'desktop_padding_top' => 20,
        'desktop_padding_right' => 0,
        'desktop_padding_bottom' => 20,
        'desktop_padding_left' => 0,
        'desktop_cta_visibility' => 1,
        'desktop_search_visibility' => 1,
        'desktop_sticky_behavior' => 'inherit',
        'desktop_font_size' => 0,

        // Mobile Header Settings
        'mobile_breakpoint' => 768,
        'mobile_header_enabled' => 1,
        'mobile_header_height' => 60,
        'mobile_logo_max_width' => 120,
        'mobile_menu_style' => 'slide',
        'mobile_menu_position' => 'right',
        'mobile_padding_top' => 15,
        'mobile_padding_right' => 15,
        'mobile_padding_bottom' => 15,
        'mobile_padding_left' => 15,
        'mobile_cta_visibility' => 1,
        'mobile_search_visibility' => 1,
        'mobile_sticky_header' => 'inherit',
        'mobile_font_size' => 0,

        // Inherit standard settings
        'enable_search' => 1,
        'enable_cta_button' => 1,
        'cta_button_text' => 'Get Started',
        'cta_button_url' => '#',
        'logo_upload' => '',
        'show_site_title' => 1,
        'sticky_header' => 0,
        'header_bg_color' => '#ffffff',
        'header_text_color' => '#333333',
        'menu_hover_color' => '#007cba'
    ),
    'preview' => array(
        'desktop' => array(
            'logo' => 'Logo',
            'menu' => array('Home', 'About', 'Services', 'Contact'),
            'search' => true,
            'cta' => 'Get Started'
        ),
        'mobile' => array(
            'logo' => 'Logo',
            'hamburger' => true,
            'search' => true,
            'menu_items' => 4
        )
    ),
    'compatibility' => array(
        'wp_version' => '5.0+',
        'php_version' => '7.4+',
        'mobile_breakpoint' => 768,
        'touch_devices' => true,
        'accessibility' => 'WCAG 2.1 AA'
    )
);