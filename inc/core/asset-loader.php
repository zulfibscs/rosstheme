<?php
/**
 * Asset loader - enqueue frontend and admin assets
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue front-end styles and scripts
 */
function ross_theme_enqueue_assets() {
	// Main theme stylesheet (style.css)
	wp_enqueue_style('ross-theme-style', get_stylesheet_uri(), array(), filemtime(get_template_directory() . '/style.css'));

	// Font Awesome for social icons
	wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

	// Additional frontend CSS (optional, keep if exists)
	$frontend_css = get_template_directory() . '/assets/css/frontend/base.css';
	if (file_exists($frontend_css)) {
		wp_enqueue_style('ross-theme-frontend-base', get_template_directory_uri() . '/assets/css/frontend/base.css', array('ross-theme-style'), filemtime($frontend_css));
	}

	// Header CSS
	$header_css = get_template_directory() . '/assets/css/frontend/header.css';
	if (file_exists($header_css)) {
		wp_enqueue_style('ross-theme-frontend-header', get_template_directory_uri() . '/assets/css/frontend/header.css', array('ross-theme-frontend-base'), filemtime($header_css));
	}

	// Front page styles
	$front_css = get_template_directory() . '/assets/css/frontend/front-page.css';
	if (file_exists($front_css)) {
		wp_enqueue_style('ross-theme-front-page', get_template_directory_uri() . '/assets/css/frontend/front-page.css', array('ross-theme-frontend-header'), filemtime($front_css));
	}

	// Footer CSS
	$footer_css = get_template_directory() . '/assets/css/frontend/footer.css';
	if (file_exists($footer_css)) {
		wp_enqueue_style('ross-theme-frontend-footer', get_template_directory_uri() . '/assets/css/frontend/footer.css', array('ross-theme-frontend-header'), filemtime($footer_css));
	}

	// Footer template content CSS
	$footer_template_css = get_template_directory() . '/assets/css/frontend/footer-template.css';
	if (file_exists($footer_template_css)) {
		wp_enqueue_style('ross-theme-footer-template', get_template_directory_uri() . '/assets/css/frontend/footer-template.css', array('ross-theme-frontend-footer'), filemtime($footer_template_css));
	}

	// Business Professional footer template CSS
	$footer_business_css = get_template_directory() . '/assets/css/frontend/footer-business-professional.css';
	if (file_exists($footer_business_css)) {
		wp_enqueue_style('ross-theme-footer-business-professional', get_template_directory_uri() . '/assets/css/frontend/footer-business-professional.css', array('ross-theme-frontend-footer'), filemtime($footer_business_css));
	}

	// Social icons CSS
	$social_icons_css = get_template_directory() . '/assets/css/frontend/social-icons.css';
	if (file_exists($social_icons_css)) {
		wp_enqueue_style('ross-theme-social-icons', get_template_directory_uri() . '/assets/css/frontend/social-icons.css', array('ross-theme-frontend-footer'), filemtime($social_icons_css));
	}

	// Homepage templates CSS
	$homepage_templates_css = get_template_directory() . '/assets/css/frontend/homepage-templates.css';
	if (file_exists($homepage_templates_css)) {
		wp_enqueue_style('ross-theme-homepage-templates', get_template_directory_uri() . '/assets/css/frontend/homepage-templates.css', array('ross-theme-style'), filemtime($homepage_templates_css));
	}

	// Global template styles (always load for homepage templates)
	$templates_global_css = get_template_directory() . '/assets/css/frontend/templates-global.css';
	if (file_exists($templates_global_css)) {
		wp_enqueue_style('ross-theme-templates-global', get_template_directory_uri() . '/assets/css/frontend/templates-global.css', array('ross-theme-style'), filemtime($templates_global_css));
	}

	// Header template-specific CSS (conditional loading based on active header template)
	$header_options = get_option('ross_theme_header_options', array());
	$header_template = $header_options['header_template'] ?? '';

	// Map header template IDs to their CSS files
	$header_template_css_map = array(
		'modern-complete' => 'header-modern-complete.css',
		'classic-static' => 'header-classic-static.css',
	);

	if (isset($header_template_css_map[$header_template])) {
		$header_css_file = $header_template_css_map[$header_template];
		$header_css_path = get_template_directory() . '/assets/css/frontend/' . $header_css_file;

		if (file_exists($header_css_path)) {
			$handle = 'ross-theme-header-' . str_replace('.css', '', str_replace('header-', '', $header_css_file));
			wp_enqueue_style($handle, get_template_directory_uri() . '/assets/css/frontend/' . $header_css_file, array('ross-theme-frontend-header'), filemtime($header_css_path));
		}
	}

	// Navigation JS
	$nav_js = get_template_directory() . '/assets/js/frontend/navigation.js';
	if (file_exists($nav_js)) {
		wp_enqueue_script('ross-theme-navigation', get_template_directory_uri() . '/assets/js/frontend/navigation.js', array('jquery'), filemtime($nav_js), true);
		
		// Localize header options for navigation.js
		$header_options = get_option('ross_theme_header_options', array());
		wp_localize_script('ross-theme-navigation', 'rossHeaderOptions', array(
			'sticky_header' => isset($header_options['sticky_header']) ? (bool)$header_options['sticky_header'] : false,
			'sticky_behavior' => isset($header_options['sticky_behavior']) ? $header_options['sticky_behavior'] : 'always',
			'sticky_scroll_threshold' => isset($header_options['sticky_scroll_threshold']) ? absint($header_options['sticky_scroll_threshold']) : 100,
			'sticky_shrink_header' => isset($header_options['sticky_shrink_header']) ? (bool)$header_options['sticky_shrink_header'] : false,
			'search_type' => isset($header_options['search_type']) ? $header_options['search_type'] : 'modal',
			'search_placeholder' => isset($header_options['search_placeholder']) ? $header_options['search_placeholder'] : 'Search...',
			'menu_hover_color' => isset($header_options['menu_hover_color']) ? $header_options['menu_hover_color'] : '#E5C902',
		));
	}

	// Search overlay JS
	$search_js = get_template_directory() . '/assets/js/frontend/search.js';
	if (file_exists($search_js)) {
		wp_enqueue_script('ross-theme-search', get_template_directory_uri() . '/assets/js/frontend/search.js', array(), filemtime($search_js), true);
	}

	// Header template-specific JS (conditional loading based on active header template)
	if (!empty($header_template) && isset($header_template_css_map[$header_template])) {
		$header_js_file = str_replace('.css', '.js', $header_template_css_map[$header_template]);
		$header_js_path = get_template_directory() . '/assets/js/frontend/' . $header_js_file;

		if (file_exists($header_js_path)) {
			$handle = 'ross-theme-header-' . str_replace('.js', '', str_replace('header-', '', $header_js_file));
			wp_enqueue_script($handle, get_template_directory_uri() . '/assets/js/frontend/' . $header_js_file, array('jquery'), filemtime($header_js_path), true);
		}
	}

	// Templates interactions JS (for homepage templates)
	$templates_js = get_template_directory() . '/assets/js/frontend/templates.js';
	if (file_exists($templates_js) && is_page_template()) {
		wp_enqueue_script('ross-theme-templates', get_template_directory_uri() . '/assets/js/frontend/templates.js', array(), filemtime($templates_js), true);
	}
}
add_action('wp_enqueue_scripts', 'ross_theme_enqueue_assets');

/**
 * Enqueue admin assets if needed (placeholder)
 */
function ross_theme_enqueue_admin_assets($hook) {
	// Admin enqueues are handled in individual modules, but ensure stylesheet for WP-admin preview if needed.
}
add_action('admin_enqueue_scripts', 'ross_theme_enqueue_admin_assets');
