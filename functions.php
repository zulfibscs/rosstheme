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
// Include footer social customizer & helper implemented under inc/
if ( file_exists( get_template_directory() . '/inc/customizer-footer-social.php' ) ) {
	require_once get_template_directory() . '/inc/customizer-footer-social.php';
}
if ( file_exists( get_template_directory() . '/inc/template-tags-footer-social.php' ) ) {
	require_once get_template_directory() . '/inc/template-tags-footer-social.php';
}

// Feature modules
require_once get_template_directory() . '/inc/features/header/header-options.php';
require_once get_template_directory() . '/inc/features/header/header-functions.php';
require_once get_template_directory() . '/inc/features/footer/footer-options.php';
require_once get_template_directory() . '/inc/features/footer/footer-functions.php';
require_once get_template_directory() . '/inc/features/general/general-options.php';
require_once get_template_directory() . '/inc/features/homepage-templates/homepage-manager.php';

// Frontend modules
require_once get_template_directory() . '/inc/frontend/dynamic-css.php';
require_once get_template_directory() . '/inc/frontend/dynamic-css-variables.php';

// Utility modules
require_once get_template_directory() . '/inc/utilities/helper-functions.php';
require_once get_template_directory() . '/inc/utilities/theme-reset-utility.php';