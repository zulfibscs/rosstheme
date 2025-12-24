<?php
/**
 * Sticky Header Component
 * Advanced sticky header with smooth animations and responsive behavior
 *
 * @package RossTheme
 * @subpackage Header
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Ross Sticky Header Component Class
 */
class Ross_Sticky_Header {

    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_head', array($this, 'output_sticky_css'), 998);
    }

    /**
     * Enqueue sticky header scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            'ross-sticky-header',
            get_template_directory_uri() . '/assets/js/frontend/sticky-header.js',
            array('jquery'),
            filemtime(get_template_directory() . '/assets/js/frontend/sticky-header.js'),
            true
        );

        // Localize script with options
        $options = get_option('ross_theme_header_options', array());
        wp_localize_script('ross-sticky-header', 'rossStickyOptions', array(
            'enabled' => !empty($options['sticky_header']),
            'behavior' => $options['sticky_behavior'] ?? 'always',
            'threshold' => absint($options['sticky_scroll_threshold'] ?? 100),
            'shrink_enabled' => !empty($options['sticky_shrink_header']),
            'shrink_height' => absint($options['sticky_header_height'] ?? 70),
            'normal_height' => absint($options['header_height'] ?? 80),
            'mobile_breakpoint' => absint($options['header_mobile_breakpoint'] ?? 768),
            'animation_duration' => absint($options['sticky_animation_duration'] ?? 300),
            'easing' => $options['sticky_easing'] ?? 'ease-out',
            'hide_on_mobile' => !empty($options['sticky_hide_mobile']),
        ));
    }

    /**
     * Output sticky header CSS
     */
    public function output_sticky_css() {
        $options = get_option('ross_theme_header_options', array());

        if (empty($options['sticky_header'])) {
            return;
        }

        $css = $this->generate_sticky_css($options);
        if (!empty($css)) {
            echo '<style type="text/css" id="ross-sticky-header-css">' . $css . '</style>';
        }
    }

    /**
     * Generate comprehensive sticky header CSS
     */
    private function generate_sticky_css($options) {
        $css = array();

        // Get header dimensions and spacing from options
        $header_height = absint($options['header_height'] ?? 80);
        $header_width = $options['header_width'] ?? 'contained';
        $header_padding_top = absint($options['header_padding_top'] ?? 20);
        $header_padding_bottom = absint($options['header_padding_bottom'] ?? 20);
        $header_margin_top = absint($options['header_margin_top'] ?? 0);
        $header_margin_bottom = absint($options['header_margin_bottom'] ?? 0);

        // Get background options
        $bg_color = $options['header_bg_color'] ?? '#ffffff';

        // Base header styles (normal state)
        $css[] = '.site-header {';
        $css[] = '  height: ' . $header_height . 'px !important;';
        $css[] = '  padding-top: ' . $header_padding_top . 'px !important;';
        $css[] = '  padding-bottom: ' . $header_padding_bottom . 'px !important;';
        $css[] = '  margin-top: ' . $header_margin_top . 'px !important;';
        $css[] = '  margin-bottom: ' . $header_margin_bottom . 'px !important;';
        $css[] = '  transition: all ' . (absint($options['sticky_animation_duration'] ?? 300) / 1000) . 's ' . esc_attr($options['sticky_easing'] ?? 'ease-out') . ' !important;';
        $css[] = '}';

        // Handle header width
        if ($header_width === 'full') {
            $css[] = '.site-header.header-full, .site-header {';
            $css[] = '  width: 100% !important;';
            $css[] = '  max-width: none !important;';
            $css[] = '}';
            $css[] = '.site-header.header-full .container, .site-header .container {';
            $css[] = '  max-width: none !important;';
            $css[] = '  margin: 0 !important;';
            $css[] = '  padding-left: 0 !important;';
            $css[] = '  padding-right: 0 !important;';
            $css[] = '}';
        }

        // Sticky state styles - use more specific selectors
        $css[] = '.site-header.is-sticky, header.site-header.is-sticky {';
        $css[] = '  position: fixed !important;';
        $css[] = '  top: 0 !important;';
        $css[] = '  left: 0 !important;';
        $css[] = '  right: 0 !important;';
        $css[] = '  width: 100% !important;';
        $css[] = '  z-index: 1000 !important;';
        $css[] = '  box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;';

        // Add browser compatibility fallbacks
        $css[] = '/* Sticky Header Browser Compatibility */';
        $css[] = '@supports not (backdrop-filter: blur(10px)) {';
        $css[] = '  .site-header.is-sticky {';
        $css[] = '    background-color: ' . esc_attr($bg_color) . ' !important;';
        $css[] = '  }';
        $css[] = '}';

        // Background - ensure solid background for sticky state
        $css[] = '.site-header.is-sticky, header.site-header.is-sticky {';
        $css[] = '  background: ' . esc_attr($bg_color) . ' !important;';
        $css[] = '  background-color: ' . esc_attr($bg_color) . ' !important;';
        $css[] = '}';

        // Ensure header-full variant has solid background
        $css[] = '.site-header.header-full.is-sticky, header.site-header.header-full.is-sticky {';
        $css[] = '  background: ' . esc_attr($bg_color) . ' !important;';
        $css[] = '  background-color: ' . esc_attr($bg_color) . ' !important;';
        $css[] = '}';

        // Container adjustments based on header width
        if ($header_width === 'full') {
            $css[] = '.site-header.is-sticky .container, .site-header.is-sticky .header-inner {';
            $css[] = '  max-width: none !important;';
            $css[] = '  margin: 0 !important;';
            $css[] = '  padding-left: max(20px, 2vw) !important;';
            $css[] = '  padding-right: max(20px, 2vw) !important;';
            $css[] = '}';
        } else {
            // For contained headers, maintain container width
            $css[] = '.site-header.is-sticky .container {';
            $css[] = '  max-width: 1200px !important;';
            $css[] = '  margin: 0 auto !important;';
            $css[] = '  padding-left: 20px !important;';
            $css[] = '  padding-right: 20px !important;';
            $css[] = '}';
            $css[] = '.site-header.is-sticky .header-inner {';
            $css[] = '  max-width: none !important;';
            $css[] = '  margin: 0 !important;';
            $css[] = '}';
        }
        $css[] = '}';

        $css[] = '.site-header.is-sticky .header-inner {';
        $css[] = '  max-width: none !important;';
        $css[] = '  margin: 0 !important;';
        $css[] = '}';

        // Shrink behavior
        if (!empty($options['sticky_shrink_header'])) {
            $normal_height = absint($options['header_height'] ?? 80);
            $shrink_height = absint($options['sticky_header_height'] ?? 70);

            // Calculate proportional padding for shrunk state
            $shrink_padding_top = max(10, round($header_padding_top * 0.7));
            $shrink_padding_bottom = max(10, round($header_padding_bottom * 0.7));

            $css[] = '.site-header.is-sticky.shrink, header.site-header.is-sticky.shrink {';
            $css[] = '  height: ' . $shrink_height . 'px !important;';
            $css[] = '  padding-top: ' . $shrink_padding_top . 'px !important;';
            $css[] = '  padding-bottom: ' . $shrink_padding_bottom . 'px !important;';
            $css[] = '}';

            // Logo scaling
            $logo_scale = $shrink_height / $normal_height;
            $css[] = '.site-header.is-sticky.shrink .site-logo img {';
            $css[] = '  transform: scale(' . max(0.7, $logo_scale) . ') !important;';
            $css[] = '  transition: transform ' . (absint($options['sticky_animation_duration'] ?? 300) / 1000) . 's ' . esc_attr($options['sticky_easing'] ?? 'ease-out') . ' !important;';
            $css[] = '}';

            // Menu font size reduction
            $css[] = '.site-header.is-sticky.shrink .primary-menu a {';
            $css[] = '  font-size: max(14px, 0.9em) !important;';
            $css[] = '  transition: font-size ' . (absint($options['sticky_animation_duration'] ?? 300) / 1000) . 's ' . esc_attr($options['sticky_easing'] ?? 'ease-out') . ' !important;';
            $css[] = '}';

            // CTA button scaling
            $css[] = '.site-header.is-sticky.shrink .header-cta-button {';
            $css[] = '  padding: 8px 16px !important;';
            $css[] = '  font-size: max(12px, 0.85em) !important;';
            $css[] = '  transition: all ' . (absint($options['sticky_animation_duration'] ?? 300) / 1000) . 's ' . esc_attr($options['sticky_easing'] ?? 'ease-out') . ' !important;';
            $css[] = '}';
        }

        // Behavior-specific styles
        $behavior = $options['sticky_behavior'] ?? 'always';

        if ($behavior === 'scroll_up') {
            $css[] = '.site-header.sticky-scroll-up {';
            $css[] = '  transform: translateY(-100%) !important;';
            $css[] = '  transition: transform ' . (absint($options['sticky_animation_duration'] ?? 300) / 1000) . 's ' . esc_attr($options['sticky_easing'] ?? 'ease-out') . ' !important;';
            $css[] = '}';

            $css[] = '.site-header.sticky-scroll-up.visible {';
            $css[] = '  transform: translateY(0) !important;';
            $css[] = '}';
        }

        // Mobile responsiveness
        $mobile_breakpoint = absint($options['header_mobile_breakpoint'] ?? 768);

        $css[] = '@media (max-width: ' . $mobile_breakpoint . 'px) {';

        if (!empty($options['sticky_hide_mobile'])) {
            $css[] = '  .site-header.is-sticky { display: none !important; }';
        } else {
            $css[] = '  .site-header.is-sticky {';
            $css[] = '    padding-top: 10px !important;';
            $css[] = '    padding-bottom: 10px !important;';
            $css[] = '  }';

            $css[] = '  .site-header.is-sticky .site-logo img {';
            $css[] = '    max-height: 40px !important;';
            $css[] = '  }';

            $css[] = '  .site-header.is-sticky .primary-menu {';
            $css[] = '    display: none !important;';
            $css[] = '  }';
        }

        $css[] = '}';

        // Body padding to prevent content jump
        $header_height = absint($options['header_height'] ?? 80);
        $css[] = 'body.has-sticky-header {';
        $css[] = '  padding-top: ' . $header_height . 'px !important;';
        $css[] = '  transition: padding-top ' . (absint($options['sticky_animation_duration'] ?? 300) / 1000) . 's ' . esc_attr($options['sticky_easing'] ?? 'ease-out') . ' !important;';
        $css[] = '}';

        if (!empty($options['sticky_shrink_header'])) {
            $shrink_height = absint($options['sticky_header_height'] ?? 70);
            $css[] = 'body.has-sticky-header.is-sticky.shrink {';
            $css[] = '  padding-top: ' . $shrink_height . 'px !important;';
            $css[] = '}';
        }

        return implode("\n", $css);
    }

    /**
     * Convert hex color to RGB
     */
    private function hex_to_rgb($hex) {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return $r . ',' . $g . ',' . $b;
    }

    /**
     * Initialize the component
     */
    public function init() {
        // Component is initialized in constructor
    }
}

// Initialize the component
Ross_Sticky_Header::get_instance();