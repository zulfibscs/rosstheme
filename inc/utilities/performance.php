<?php
/**
 * Performance Optimization Utilities
 *
 * Implements various performance enhancements for the theme including:
 * - Lazy loading for images
 * - Script optimization and deferring
 * - Emoji removal for reduced HTTP requests
 * - Query string removal from assets
 * - Common query caching
 *
 * @package RossTheme\Utilities
 * @since 1.0.0
 * @author Ross Theme Team
 */

if (!defined('ABSPATH')) exit;

/**
 * Add lazy loading to images
 *
 * Automatically adds loading="lazy" attribute to img tags in post content
 * and widget text to improve page load performance by deferring off-screen
 * images until they come into view.
 *
 * @param string $content The content to filter
 * @return string Modified content with lazy loading attributes
 *
 * @since 1.0.0
 * @uses preg_replace() For regex-based attribute addition
 */
function ross_add_lazy_loading($content) {
    // Only apply to frontend content, not admin or feeds
    if (!is_admin() && !is_feed()) {
        // Add loading="lazy" to self-closing img tags
        $content = preg_replace('/<img([^>]+?)\/>/i', '<img$1 loading="lazy" />', $content);

        // Also handle img tags without self-closing (though rare in HTML5)
        $content = preg_replace('/<img([^>]+)>/i', '<img$1 loading="lazy">', $content);
    }
    return $content;
}
add_filter('the_content', 'ross_add_lazy_loading');
add_filter('widget_text', 'ross_add_lazy_loading');

/**
 * Optimize scripts loading - defer non-critical JS
 *
 * Adds defer attribute to non-critical JavaScript files to improve
 * page load performance by allowing HTML parsing to continue while
 * scripts download in parallel.
 *
 * @param string $tag The script tag HTML
 * @param string $handle The script handle
 * @param string $src The script source URL
 * @return string Modified script tag with defer attribute if applicable
 *
 * @since 1.0.0
 */
function ross_optimize_script_loading($tag, $handle, $src) {
    // List of script handles that should be deferred (non-critical JS)
    $defer_handles = array(
        'ross-theme-navigation',
        'ross-theme-responsive-header',
        'ross-theme-search',
        'ross-theme-responsive-header-js',
        'ross-theme-templates'
    );

    // Add defer attribute to specified handles
    if (in_array($handle, $defer_handles)) {
        return str_replace('<script ', '<script defer ', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'ross_optimize_script_loading', 10, 3);

/**
 * Add performance hints to wp_head
 *
 * Adds HTML comments indicating performance optimizations are active.
 * Useful for debugging and confirming optimizations are loaded.
 *
 * @return void
 *
 * @since 1.0.0
 */
function ross_performance_hints() {
    // Only add hints if not in admin area
    if (is_admin()) return;

    echo '<!-- Performance optimizations active -->' . "\n";
}
add_action('wp_head', 'ross_performance_hints', 1);

/**
 * Disable emojis for better performance
 *
 * Removes WordPress emoji scripts and styles to reduce HTTP requests
 * and improve page load performance. Emojis will still display normally
 * as they fall back to system fonts.
 *
 * @return void
 *
 * @since 1.0.0
 * @uses remove_action() To remove emoji-related hooks
 * @uses add_filter() To filter TinyMCE plugins
 */
function ross_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', 'ross_disable_emojis_tinymce');
}
add_action('init', 'ross_disable_emojis');

/**
 * Remove wpemoji plugin from TinyMCE editor
 *
 * Helper function to remove the wpemoji plugin from TinyMCE
 * when emojis are disabled for performance.
 *
 * @param array $plugins Array of TinyMCE plugins
 * @return array Modified array without wpemoji plugin
 *
 * @since 1.0.0
 */
function ross_disable_emojis_tinymce($plugins) {
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}

/**
 * Remove query strings from static resources
 *
 * Strips version query parameters from CSS and JS files in production
 * to enable better caching by CDNs and browsers. Only active when
 * WP_DEBUG is disabled.
 *
 * @param string $src The resource URL
 * @return string URL without version query string
 *
 * @since 1.0.0
 * @uses remove_query_arg() To strip version parameters
 */
function ross_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
// Only remove query strings in production (when debug is off)
if (!defined('WP_DEBUG') || !WP_DEBUG) {
    add_filter('script_loader_src', 'ross_remove_query_strings', 15, 1);
    add_filter('style_loader_src', 'ross_remove_query_strings', 15, 1);
}

/**
 * Optimize database queries by caching common queries
 *
 * Caches frequently accessed data like recent posts to reduce
 * database load. Runs on WordPress initialization.
 *
 * @return void
 *
 * @since 1.0.0
 * @uses wp_cache_get() To check for existing cache
 * @uses get_posts() To fetch recent posts
 * @uses wp_cache_set() To store cached data
 */
function ross_cache_common_queries() {
    // Cache recent posts query to reduce database load
    if (!is_admin() && !wp_cache_get('ross_recent_posts')) {
        $recent_posts = get_posts(array(
            'numberposts' => 5,
            'post_status' => 'publish'
        ));
        wp_cache_set('ross_recent_posts', $recent_posts, '', 300); // Cache for 5 minutes
    }
}
add_action('wp', 'ross_cache_common_queries');