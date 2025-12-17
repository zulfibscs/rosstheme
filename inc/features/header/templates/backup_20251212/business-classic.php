<?php
/**
 * Backup - business-classic template
 */
return array(
	'id' => 'business-classic',
	'title' => 'Business Classic',
	'description' => 'Professional layout with logo left, navigation center, and CTA button right',
	'icon' => '💼',
	'preview_image' => 'business-classic.png',
	'layout' => 'horizontal',
	'logo_position' => 'left',
	'menu_position' => 'center',
	'cta_position' => 'right',
	'bg' => '#ffffff',
	'text' => '#0b2140',
	'accent' => '#0b66a6',
	'hover' => '#084578',
	'border_bottom' => '#e5e7eb',
	'sticky_bg' => '#ffffff',
	'sticky_shadow' => 'rgba(0, 0, 0, 0.1)',
	'font_size' => '16px',
	'font_weight' => '500',
	'letter_spacing' => '0.5px',
	'padding_top' => '20',
	'padding_bottom' => '20',
	'container_width' => 'contained',
	'sticky_enabled' => true,
	'sticky_behavior' => 'always',
	'search_enabled' => true,
	'search_style' => 'icon',
	'mobile_breakpoint' => '768',
	'cta' => array(
		'enabled' => true,
		'text' => 'Get Started',
		'url' => '#contact',
		'style' => 'solid',
		'bg' => '#0b66a6',
		'color' => '#ffffff',
		'hover_bg' => '#084578',
		'border_radius' => '6px',
		'padding' => '12px 24px'
	),
	'mobile' => array(
		'toggle_style' => 'hamburger',
		'animation' => 'slide',
		'position' => 'full',
		'bg' => '#ffffff',
		'overlay' => true
	),
	'animation' => array(
		'menu_items' => 'fade-in',
		'sticky_transition' => 'smooth',
		'duration' => '300ms'
	)
);