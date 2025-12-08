<?php
/**
 * Ross Theme - Modular Loader
 */

if (!defined('ABSPATH')) exit;

// Core modules
require_once get_template_directory() . '/inc/core/theme-setup.php';
require_once get_template_directory() . '/inc/core/asset-loader.php';

// Admin modules  
require_once get_template_directory() . '/inc/admin/admin-pages.php';
require_once get_template_directory() . '/inc/admin/settings-api.php';
require_once get_template_directory() . '/inc/admin/customizer-topbar.php';
require_once get_template_directory() . '/inc/admin/customizer-enqueuer.php';

// Feature modules
require_once get_template_directory() . '/inc/features/header/header-template-manager.php';
require_once get_template_directory() . '/inc/features/header/header-options.php';
require_once get_template_directory() . '/inc/features/header/header-functions.php';
require_once get_template_directory() . '/inc/features/footer/footer-options.php';
require_once get_template_directory() . '/inc/features/footer/footer-functions.php';
require_once get_template_directory() . '/inc/features/footer/social-customizer.php';
require_once get_template_directory() . '/inc/features/footer/social-functions.php';
require_once get_template_directory() . '/inc/features/general/general-options.php';

// Homepage Templates
require_once get_template_directory() . '/inc/features/homepage-templates/homepage-setup.php';
require_once get_template_directory() . '/inc/features/homepage-templates/homepage-manager.php';
require_once get_template_directory() . '/inc/features/homepage-templates/template-switcher-ui.php';

// Initialize Homepage Templates Manager
if (class_exists('RossHomepageManager')) {
	RossHomepageManager::get_instance();
}

// Frontend modules
require_once get_template_directory() . '/inc/frontend/dynamic-css.php';

// Utility modules
require_once get_template_directory() . '/inc/utilities/helper-functions.php';
require_once get_template_directory() . '/inc/utilities/theme-reset-utility.php';