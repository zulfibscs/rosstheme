<?php
/**
 * Theme Options Caching Utility
 * Provides cached access to theme options for improved performance
 */

if (!defined('ABSPATH')) exit;

/**
 * Get cached theme option with automatic cache invalidation
 *
 * @param string $option_name The option name
 * @param mixed $default Default value if option doesn't exist
 * @param int $cache_time Cache expiration time in seconds (default: 1 hour)
 * @return mixed
 */
function ross_get_cached_option($option_name, $default = array(), $cache_time = 3600) {
    // Create a unique cache key
    $cache_key = 'ross_theme_' . md5($option_name);

    // Try to get from object cache first (fastest)
    $cached_value = wp_cache_get($cache_key, 'ross_theme_options');

    if ($cached_value !== false) {
        return $cached_value;
    }

    // Try to get from transient cache
    $transient_key = 'ross_' . $cache_key;
    $cached_value = get_transient($transient_key);

    if ($cached_value !== false) {
        // Store in object cache for faster future access
        wp_cache_set($cache_key, $cached_value, 'ross_theme_options', $cache_time);
        return $cached_value;
    }

    // Get from database
    $value = get_option($option_name, $default);

    // Ensure it's an array if default is array
    if (is_array($default) && !is_array($value)) {
        $value = $default;
    }

    // Cache the value
    set_transient($transient_key, $value, $cache_time);
    wp_cache_set($cache_key, $value, 'ross_theme_options', $cache_time);

    return $value;
}

/**
 * Clear all theme options cache
 *
 * Removes cached theme options from both object cache and transients.
 * Should be called whenever theme options are updated to ensure
 * fresh data is served.
 *
 * @return void
 *
 * @since 1.0.0
 * @uses wp_cache_flush_group() To clear object cache
 * @uses $wpdb To clear transient entries from database
 */
function ross_clear_theme_options_cache() {
    // Clear object cache group
    wp_cache_flush_group('ross_theme_options');
    wp_cache_flush_group('ross_theme_css');

    // Clear transients with ross_ prefix from database
    global $wpdb;
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_ross_%'
        )
    );

    // Also clear timeout entries to prevent orphaned records
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            '_transient_timeout_ross_%'
        )
    );
}

/**
 * Get cached header options
 *
 * Convenience function to retrieve header-specific theme options
 * with caching applied.
 *
 * @return array Header theme options array
 *
 * @since 1.0.0
 * @uses ross_get_cached_option()
 */
function ross_get_header_options() {
    return ross_get_cached_option('ross_theme_header_options', array());
}

/**
 * Get cached general options
 *
 * Convenience function to retrieve general theme options
 * with caching applied.
 *
 * @return array General theme options array
 *
 * @since 1.0.0
 * @uses ross_get_cached_option()
 */
function ross_get_general_options() {
    return ross_get_cached_option('ross_theme_general_options', array());
}

/**
 * Get cached footer options
 *
 * Convenience function to retrieve footer-specific theme options
 * with caching applied.
 *
 * @return array Footer theme options array
 *
 * @since 1.0.0
 * @uses ross_get_cached_option()
 */
function ross_get_footer_options() {
    return ross_get_cached_option('ross_theme_footer_options', array());
}

/**
 * Get cached advanced topbar options
 *
 * Convenience function to retrieve advanced topbar theme options
 * with caching applied.
 *
 * @return array Advanced topbar theme options array
 *
 * @since 1.0.0
 * @uses ross_get_cached_option()
 */
function ross_get_advanced_topbar_options() {
    return ross_get_cached_option('ross_advanced_topbar_options', array());
}

/**
 * Hook into option updates to clear cache
 *
 * Automatically clears theme options cache when any theme option
 * is updated via WordPress settings API.
 *
 * @param string $option_name The option name being updated
 * @param mixed $old_value The old option value
 * @param mixed $new_value The new option value
 * @return void
 *
 * @since 1.0.0
 * @uses ross_clear_theme_options_cache()
 */
function ross_theme_options_updated($option_name, $old_value, $new_value) {
    $theme_options = array(
        'ross_theme_header_options',
        'ross_theme_general_options',
        'ross_theme_footer_options',
        'ross_advanced_topbar_options'
    );

    if (in_array($option_name, $theme_options)) {
        ross_clear_theme_options_cache();
    }
}
add_action('updated_option', 'ross_theme_options_updated', 10, 3);