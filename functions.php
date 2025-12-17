<?php
/**
 * Ross Theme - Main Functions File
 *
 * This file serves as the central hub for loading all theme functionality.
 * It uses a modular architecture to organize code into logical groups:
 * - Core: Essential WordPress setup and asset loading
 * - Admin: Administrative interfaces and settings
 * - Features: Theme-specific functionality (header, footer, general)
 * - Frontend: Client-side output and dynamic CSS
 * - Utilities: Helper functions and performance optimizations
 *
 * @package RossTheme
 * @since 1.0.0
 * @author Ross Theme Team
 * @license GPL-2.0+
 *
 * @link https://wordpress.org/themes/ross-theme/
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

// =============================================================================
// CORE MODULES
// Essential WordPress setup, theme support, and asset management
// =============================================================================

/**
 * Theme Setup - Registers menus, theme supports, and text domain
 * @see inc/core/theme-setup.php
 */
require_once get_template_directory() . '/inc/core/theme-setup.php';

/**
 * Asset Loader - Manages CSS/JS enqueuing and optimization
 * @see inc/core/asset-loader.php
 */
require_once get_template_directory() . '/inc/core/asset-loader.php';

// =============================================================================
// ADMIN MODULES
// Administrative interfaces, settings pages, and customizer integration
// =============================================================================

/**
 * Admin Pages - Main admin menu and page routing
 * @see inc/admin/admin-pages.php
 */
require_once get_template_directory() . '/inc/admin/admin-pages.php';

/**
 * Settings API - WordPress settings API integration
 * @see inc/admin/settings-api.php
 */
require_once get_template_directory() . '/inc/admin/settings-api.php';

/**
 * Customizer Topbar - WordPress Customizer integration for topbar
 * @see inc/admin/customizer-topbar.php
 */
require_once get_template_directory() . '/inc/admin/customizer-topbar.php';

/**
 * Customizer Enqueuer - Asset management for customizer
 * @see inc/admin/customizer-enqueuer.php
 */
require_once get_template_directory() . '/inc/admin/customizer-enqueuer.php';

/**
 * Footer Social Customizer - Social media integration
 * @see inc/template-tags-footer-social.php
 */
require_once get_template_directory() . '/inc/template-tags-footer-social.php';

// =============================================================================
// FEATURE MODULES
// Theme-specific functionality organized by component
// =============================================================================

/**
 * Header Template Manager - Header template system
 * @see inc/features/header/header-template-manager.php
 */
require_once get_template_directory() . '/inc/features/header/header-template-manager.php';

/**
 * Header Options - Header settings and configuration
 * @see inc/features/header/header-options.php
 */
require_once get_template_directory() . '/inc/features/header/header-options.php';

/**
 * Header Functions - Header rendering and logic
 * @see inc/features/header/header-functions.php
 */
require_once get_template_directory() . '/inc/features/header/header-functions.php';

/**
 * Footer Options - Footer settings and configuration
 * @see inc/features/footer/footer-options.php
 */
require_once get_template_directory() . '/inc/features/footer/footer-options.php';

/**
 * Footer Functions - Footer rendering and logic
 * @see inc/features/footer/footer-functions.php
 */
require_once get_template_directory() . '/inc/features/footer/footer-functions.php';

/**
 * General Options - Global theme settings
 * @see inc/features/general/general-options.php
 */
require_once get_template_directory() . '/inc/features/general/general-options.php';

// =============================================================================
// HOMEPAGE TEMPLATES
// Pre-designed homepage layouts and management
// =============================================================================

/**
 * Homepage Templates Manager - Template system for homepages
 * @see inc/features/homepage-templates/homepage-manager.php
 */
require_once get_template_directory() . '/inc/features/homepage-templates/homepage-manager.php';

// Initialize Homepage Templates Manager
if (class_exists('RossHomepageManager')) {
	RossHomepageManager::get_instance();
}

// =============================================================================
// FRONTEND MODULES
// Client-side output, dynamic CSS, and user-facing functionality
// =============================================================================

/**
 * Dynamic CSS - Generates and outputs dynamic stylesheets
 * @see inc/frontend/dynamic-css.php
 */
require_once get_template_directory() . '/inc/frontend/dynamic-css.php';

// =============================================================================
// UTILITY MODULES
// Helper functions, performance optimizations, and reusable utilities
// =============================================================================

/**
 * Helper Functions - Common utility functions
 * @see inc/utilities/helper-functions.php
 */
require_once get_template_directory() . '/inc/utilities/helper-functions.php';

/**
 * Theme Reset Utility - Settings reset functionality
 * @see inc/utilities/theme-reset-utility.php
 */
require_once get_template_directory() . '/inc/utilities/theme-reset-utility.php';

/**
 * Caching Utilities - Theme options caching system
 * @see inc/utilities/caching.php
 */
require_once get_template_directory() . '/inc/utilities/caching.php';

/**
 * Performance Optimizations - Speed and performance enhancements
 * @see inc/utilities/performance.php
 */
require_once get_template_directory() . '/inc/utilities/performance.php';