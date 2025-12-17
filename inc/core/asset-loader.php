<?php
/**
 * Asset loader - enqueue frontend and admin assets
 */

if (!defined('ABSPATH')) exit;

/**
 * Enqueue front-end styles and scripts
 */
function ross_theme_enqueue_assets() {
	// Add preconnect for external resources to improve performance
	add_action('wp_head', function() {
		echo '<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>' . "\n";
		echo '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">' . "\n";
	}, 1);

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

	// Simple Responsive Header CSS
	$responsive_header_css = get_template_directory() . '/assets/css/frontend/header-simple-responsive.css';
	if (file_exists($responsive_header_css)) {
		wp_enqueue_style('ross-theme-responsive-header', get_template_directory_uri() . '/assets/css/frontend/header-simple-responsive.css', array('ross-theme-frontend-header'), filemtime($responsive_header_css));
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

	// Template-specific CSS (conditional loading based on active template)
	if (is_page_template()) {
		$template_name = get_page_template_slug();
		
		// Map template files to their CSS files
		$template_css_map = array(
			'template-home-business.php' => 'template-business.css',
			'template-home-creative.php' => 'template-creative.css',
			'template-home-ecommerce.php' => 'template-ecommerce.css',
			'template-home-minimal.php' => 'template-minimal.css',
			'template-home-startup.php' => 'template-startup.css',
			'template-home-restaurant.php' => 'template-restaurant.css',
		);
		
		if (isset($template_css_map[$template_name])) {
			$template_css_file = $template_css_map[$template_name];
			$template_css_path = get_template_directory() . '/assets/css/frontend/' . $template_css_file;
			
			if (file_exists($template_css_path)) {
				$handle = 'ross-theme-' . str_replace('.css', '', $template_css_file);
				wp_enqueue_style($handle, get_template_directory_uri() . '/assets/css/frontend/' . $template_css_file, array('ross-theme-templates-global'), filemtime($template_css_path));
			}
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
			'mobile_menu_style' => isset($header_options['mobile_menu_style']) ? $header_options['mobile_menu_style'] : 'slide',
			'hamburger_animation' => isset($header_options['hamburger_animation']) ? $header_options['hamburger_animation'] : 'collapse',
			'mobile_menu_position' => isset($header_options['mobile_menu_position']) ? $header_options['mobile_menu_position'] : 'left',
			'search_type' => isset($header_options['search_type']) ? $header_options['search_type'] : 'modal',
			'search_placeholder' => isset($header_options['search_placeholder']) ? $header_options['search_placeholder'] : 'Search...',
			'menu_hover_color' => isset($header_options['menu_hover_color']) ? $header_options['menu_hover_color'] : '#E5C902',
		));
	}

	// Responsive Pro Header JS
	$responsive_header_js = get_template_directory() . '/assets/js/frontend/header-responsive-pro.js';
	if (file_exists($responsive_header_js)) {
		wp_enqueue_script('ross-theme-responsive-header', get_template_directory_uri() . '/assets/js/frontend/header-responsive-pro.js', array('jquery'), filemtime($responsive_header_js), true);
	}

	// Search overlay JS
	$search_js = get_template_directory() . '/assets/js/frontend/search.js';
	if (file_exists($search_js)) {
		wp_enqueue_script('ross-theme-search', get_template_directory_uri() . '/assets/js/frontend/search.js', array(), filemtime($search_js), true);
	}

	// Simple Responsive Header JS
	$responsive_header_js = get_template_directory() . '/assets/js/frontend/header-simple-responsive.js';
	if (file_exists($responsive_header_js)) {
		wp_enqueue_script('ross-theme-responsive-header-js', get_template_directory_uri() . '/assets/js/frontend/header-simple-responsive.js', array('jquery'), filemtime($responsive_header_js), true);
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
