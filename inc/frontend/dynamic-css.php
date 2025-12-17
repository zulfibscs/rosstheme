<?php
/**
 * Dynamic CSS Output Module
 *
 * Generates and outputs dynamic CSS based on theme options.
 * Implements caching to improve performance by avoiding
 * repeated CSS generation on each page load.
 *
 * @package RossTheme\Frontend
 * @since 1.0.0
 * @author Ross Theme Team
 */

/**
 * Generate and output dynamic CSS for theme customization
 *
 * Creates CSS rules based on theme options for headers, footers,
 * colors, typography, and other customizable elements. Uses
 * multi-layer caching for optimal performance.
 *
 * @return void
 *
 * @since 1.0.0
 * @uses ross_get_header_options()
 * @uses ross_get_footer_options()
 * @uses ross_get_general_options()
 * @uses wp_cache_get()
 * @uses wp_cache_set()
 */
function ross_theme_dynamic_css() {
    // Get theme options with caching
    $header_options = ross_get_header_options();
    $general_options = ross_get_general_options();

    // Defensive programming: ensure arrays to prevent errors
    if (!is_array($header_options)) { $header_options = array(); }
    if (!is_array($general_options)) { $general_options = array(); }

    // Create unique cache key based on current options
    $cache_key = 'ross_dynamic_css_' . md5(serialize($header_options) . serialize($general_options));

    // Check for cached CSS first (performance optimization)
    $cached_css = wp_cache_get($cache_key, 'ross_theme_css');
    if ($cached_css !== false) {
        echo '<style type="text/css" id="ross-theme-dynamic-css">' . $cached_css . '</style>';

        // Debug: output header options as comment for admins (not cached)
        if (current_user_can('manage_options')) {
            echo '/* ROSS THEME DEBUG - Header Options: ' . esc_html(json_encode($header_options)) . ' */';
        }

        return;
    }

    // Start output buffering to capture CSS
    ob_start();
    // Quick debug log to help identify runtime errors in head injection when WP debugging is enabled
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[ross_theme_dynamic_css] invoked at ' . date('c'));
    }
    
    // Force apply header styles
    if (!empty($header_options['header_bg_color'])) {
        echo '.site-header { background-color: ' . esc_attr($header_options['header_bg_color']) . ' !important; }';
    }
    
    if (!empty($header_options['header_text_color'])) {
        echo '.site-header, .site-header a { color: ' . esc_attr($header_options['header_text_color']) . ' !important; }';
    }
    
    if (!empty($header_options['header_link_hover_color'])) {
        echo '.site-header a:hover { color: ' . esc_attr($header_options['header_link_hover_color']) . ' !important; }';
    }
    
    if (!empty($header_options['active_item_color'])) {
        echo '.primary-menu .current-menu-item a { color: ' . esc_attr($header_options['active_item_color']) . ' !important; }';
    }

    // Menu font size and alignment
    if (!empty($header_options['menu_font_size'])) {
        $size = absint($header_options['menu_font_size']);
        echo '.primary-menu a { font-size: ' . $size . 'px !important; }';
    }

    if (!empty($header_options['menu_alignment'])) {
        $align = esc_attr($header_options['menu_alignment']);
        $justify = 'flex-start';
        if ($align === 'center') $justify = 'center';
        if ($align === 'right') $justify = 'flex-end';
        echo '.primary-menu { justify-content: ' . $justify . ' !important; }';
    }

    // Menu hover, background and border colors
    if (!empty($header_options['menu_hover_color'])) {
        echo '.primary-menu a:hover { color: ' . esc_attr($header_options['menu_hover_color']) . ' !important; }';
        echo '.primary-menu a:hover::after { background: ' . esc_attr($header_options['menu_hover_color']) . ' !important; }';
    }

    if (isset($header_options['menu_bg_color']) && $header_options['menu_bg_color'] !== '') {
        echo '.header-navigation, .header-navigation-centered { background-color: ' . esc_attr($header_options['menu_bg_color']) . ' !important; }';
    }

    if (!empty($header_options['menu_border_color'])) {
        echo '.primary-menu a::after { background: ' . esc_attr($header_options['menu_border_color']) . ' !important; }';
        echo '.primary-menu .current-menu-item a::after { background: ' . esc_attr($header_options['menu_border_color']) . ' !important; }';
    }

    // ===== PHASE 1 ENHANCEMENTS: NEW HEADER CONTROLS =====
    
    // Mobile Logo
    if (!empty($header_options['mobile_logo'])) {
        $mobile_breakpoint = isset($header_options['mobile_breakpoint']) ? absint($header_options['mobile_breakpoint']) : 768;
        echo '@media (max-width: ' . $mobile_breakpoint . 'px) {';
        echo '.site-logo .desktop-logo { display: none !important; }';
        echo '.site-logo .mobile-logo { display: block !important; }';
        echo '}';
        echo '@media (min-width: ' . ($mobile_breakpoint + 1) . 'px) {';
        echo '.site-logo .mobile-logo { display: none !important; }';
        echo '}';
    }
    
    if (!empty($header_options['mobile_logo_width'])) {
        $mobile_breakpoint = isset($header_options['mobile_breakpoint']) ? absint($header_options['mobile_breakpoint']) : 768;
        $width = absint($header_options['mobile_logo_width']);
        echo '@media (max-width: ' . $mobile_breakpoint . 'px) {';
        echo '.site-logo img, .site-logo .mobile-logo { max-width: ' . $width . 'px !important; }';
        echo '}';
    }
    
    // Logo Padding
    if (isset($header_options['logo_padding']) && $header_options['logo_padding'] !== '' && $header_options['logo_padding'] != 0) {
        $padding = absint($header_options['logo_padding']);
        echo '.site-logo, .brand-link { padding-top: ' . $padding . 'px !important; padding-right: ' . $padding . 'px !important; padding-bottom: ' . $padding . 'px !important; padding-left: ' . $padding . 'px !important; }';
    }
    
    // Header Opacity
    if (isset($header_options['header_opacity']) && $header_options['header_opacity'] !== '' && $header_options['header_opacity'] != 1) {
        $opacity = floatval($header_options['header_opacity']);
        echo '.site-header { opacity: ' . $opacity . ' !important; }';
    }
    
    // Transparent Header Overlay
    if (!empty($header_options['transparent_overlay_enable'])) {
        $overlay_color = isset($header_options['transparent_overlay_color']) ? $header_options['transparent_overlay_color'] : '#000000';
        $overlay_opacity = isset($header_options['transparent_overlay_opacity']) ? floatval($header_options['transparent_overlay_opacity']) : 0.3;
        
        // Convert hex to RGB
        $hex = ltrim($overlay_color, '#');
        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex,0,1),2));
            $g = hexdec(str_repeat(substr($hex,1,1),2));
            $b = hexdec(str_repeat(substr($hex,2,1),2));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $overlay_opacity . ')';
        
        echo '.site-header.has-transparent-overlay { position: relative !important; }';
        echo '.site-header.has-transparent-overlay::before { content: "" !important; position: absolute !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; background: ' . $rgba . ' !important; pointer-events: none !important; z-index: 0 !important; }';
        echo '.site-header.has-transparent-overlay > * { position: relative !important; z-index: 1 !important; }';
    }
    
    // Header Shadow
    if (!empty($header_options['header_shadow_enable'])) {
        $shadow_size = isset($header_options['header_shadow_size']) ? $header_options['header_shadow_size'] : 'medium';
        $shadow_values = array(
            'small' => '0 2px 4px rgba(0,0,0,0.1)',
            'medium' => '0 4px 8px rgba(0,0,0,0.15)',
            'large' => '0 6px 16px rgba(0,0,0,0.2)'
        );
        $shadow = isset($shadow_values[$shadow_size]) ? $shadow_values[$shadow_size] : $shadow_values['medium'];
        echo '.site-header { box-shadow: ' . $shadow . ' !important; }';
    }
    
    // Header Bottom Border
    if (!empty($header_options['header_border_enable'])) {
        $border_color = isset($header_options['header_border_color']) ? $header_options['header_border_color'] : '#e0e0e0';
        $border_width = isset($header_options['header_border_width']) ? absint($header_options['header_border_width']) : 1;
        echo '.site-header { border-bottom: ' . $border_width . 'px solid ' . esc_attr($border_color) . ' !important; }';
    }
    
    // Header Typography
    if (!empty($header_options['header_font_family']) && $header_options['header_font_family'] !== 'inherit') {
        $font_family = esc_attr($header_options['header_font_family']);
        echo '.site-header, .primary-menu a { font-family: ' . $font_family . ' !important; }';
    }
    
    if (!empty($header_options['header_font_weight']) && $header_options['header_font_weight'] !== '400') {
        $font_weight = esc_attr($header_options['header_font_weight']);
        echo '.primary-menu a { font-weight: ' . $font_weight . ' !important; }';
    }

    if (!empty($header_options['header_font_size'])) {
        $size = absint($header_options['header_font_size']);
        echo '.primary-menu a { font-size: ' . $size . 'px !important; }';
    }

    if (!empty($header_options['header_letter_spacing'])) {
        echo '.primary-menu a { letter-spacing: ' . esc_attr($header_options['header_letter_spacing']) . ' !important; }';
    }

    if (!empty($header_options['header_text_transform'])) {
        echo '.primary-menu a { text-transform: ' . esc_attr($header_options['header_text_transform']) . ' !important; }';
    }
    
    // Menu Hover Effects
    if (!empty($header_options['menu_hover_effect'])) {
        $effect = $header_options['menu_hover_effect'];
        
        if ($effect === 'background') {
            $hover_color = isset($header_options['menu_hover_color']) ? $header_options['menu_hover_color'] : '#E5C902';
            // Choose a contrasting text color so hover background and text never blend.
            $text_color = '#ffffff';
            $hex = ltrim($hover_color, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
            }
            if (strlen($hex) === 6) {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $brightness = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
                $text_color = ($brightness > 186) ? '#111111' : '#ffffff';
            }

            echo '.primary-menu a:hover { background-color: ' . esc_attr($hover_color) . ' !important; color: ' . esc_attr($text_color) . ' !important; padding: 8px 12px !important; border-radius: 4px !important; }';
            echo '.primary-menu a::after { display: none !important; }';
        } elseif ($effect === 'none') {
            echo '.primary-menu a::after { display: none !important; }';
        }
        // 'underline' is default, already handled by existing CSS
    }
    
    if (!empty($header_options['menu_hover_underline_style']) && $header_options['menu_hover_effect'] === 'underline') {
        $style = $header_options['menu_hover_underline_style'];
        
        if ($style === 'slide') {
            echo '.primary-menu a::after { width: 0 !important; transition: width 0.3s ease !important; }';
            echo '.primary-menu a:hover::after, .primary-menu .current-menu-item a::after { width: 100% !important; }';
        } elseif ($style === 'fade') {
            echo '.primary-menu a::after { opacity: 0 !important; transition: opacity 0.3s ease !important; }';
            echo '.primary-menu a:hover::after, .primary-menu .current-menu-item a::after { opacity: 1 !important; }';
        } elseif ($style === 'instant') {
            echo '.primary-menu a::after { transition: none !important; }';
        }
    }
    
    // Sticky Header Behavior
    if (!empty($header_options['sticky_header'])) {
        echo '.site-header { transition: all 0.3s ease !important; }';
        
        // Basic sticky header (no shrink)
        echo '.site-header.is-sticky { position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; z-index: 1000 !important; box-shadow: 0 2px 20px rgba(0,0,0,0.1) !important; }';
        
        // Add body padding to prevent content jump
        $header_height = isset($header_options['header_height']) ? absint($header_options['header_height']) : 80;
        echo 'body.has-sticky-header { padding-top: ' . $header_height . 'px !important; }';
        
        if (!empty($header_options['sticky_shrink_header'])) {
            $normal_height = isset($header_options['header_height']) ? absint($header_options['header_height']) : 80;
            $sticky_height = isset($header_options['sticky_header_height']) ? absint($header_options['sticky_header_height']) : 60;
            
            // Calculate appropriate padding for sticky state (maintain visual balance)
            $normal_padding_top = isset($header_options['header_padding_top']) ? absint($header_options['header_padding_top']) : 20;
            $normal_padding_bottom = isset($header_options['header_padding_bottom']) ? absint($header_options['header_padding_bottom']) : 20;
            
            // Reduce padding proportionally but keep it reasonable (minimum 8px)
            $sticky_padding_top = max(8, round($normal_padding_top * 0.6));
            $sticky_padding_bottom = max(8, round($normal_padding_bottom * 0.6));
            
            echo '.site-header.is-sticky.shrink { height: ' . $sticky_height . 'px !important; padding-top: ' . $sticky_padding_top . 'px !important; padding-bottom: ' . $sticky_padding_bottom . 'px !important; }';
            
            // Calculate logo max height accounting for logo padding
            $logo_padding = isset($header_options['logo_padding']) ? absint($header_options['logo_padding']) : 0;
            $logo_max_height = $sticky_height - ($sticky_padding_top + $sticky_padding_bottom + ($logo_padding * 2));
            echo '.site-header.is-sticky.shrink .site-logo img { max-height: ' . max(20, $logo_max_height) . 'px !important; transition: max-height 0.3s ease !important; }';
            
            // Update body padding for shrunk header
            echo 'body.has-sticky-header.is-sticky.shrink { padding-top: ' . $sticky_height . 'px !important; }';
        }
    }
    
    // Search Type Styling
    if (!empty($header_options['search_type'])) {
        $search_type = $header_options['search_type'];
        
        if ($search_type === 'dropdown') {
            echo '.header-search-dropdown { position: absolute !important; top: 100% !important; right: 0 !important; background: #fff !important; padding: 15px !important; box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important; border-radius: 4px !important; min-width: 300px !important; z-index: 1000 !important; }';
        } elseif ($search_type === 'inline') {
            echo '.header-search-inline { display: inline-flex !important; align-items: center !important; max-width: 0 !important; overflow: hidden !important; transition: max-width 0.3s ease !important; }';
            echo '.header-search-inline.active { max-width: 300px !important; margin-left: 15px !important; }';
        }
    }
    
    // CTA Button Styling
    if (!empty($header_options['cta_button_style'])) {
        $style = $header_options['cta_button_style'];
        $bg_color = isset($header_options['cta_button_color']) ? $header_options['cta_button_color'] : '#E5C902';
        $text_color = isset($header_options['cta_button_text_color']) ? $header_options['cta_button_text_color'] : '#ffffff';
        $hover_text_color = isset($header_options['cta_button_hover_text_color']) ? $header_options['cta_button_hover_text_color'] : $text_color;

        // Set CSS custom properties for text colors (used by header.css)
        echo '.header-cta-button { --cta-text-color: ' . esc_attr($text_color) . ' !important; --cta-hover-text-color: ' . esc_attr($hover_text_color) . ' !important; --cta-bg-color: ' . esc_attr($bg_color) . ' !important; }';

        if ($style === 'solid') {
            echo '.header-cta-button { background: ' . esc_attr($bg_color) . ' !important; }';
        } elseif ($style === 'outline') {
            echo '.header-cta-button { background: transparent !important; border: 2px solid ' . esc_attr($bg_color) . ' !important; }';
        } elseif ($style === 'ghost') {
            echo '.header-cta-button { background: rgba(255,255,255,0.1) !important; border: 1px solid rgba(255,255,255,0.3) !important; backdrop-filter: blur(10px) !important; }';
        } elseif ($style === 'gradient') {
            // Create gradient from button color
            $hex = ltrim($bg_color, '#');
            if (strlen($hex) === 3) {
                $r = hexdec(str_repeat(substr($hex,0,1),2));
                $g = hexdec(str_repeat(substr($hex,1,1),2));
                $b = hexdec(str_repeat(substr($hex,2,1),2));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            // Darken for gradient end
            $r2 = max(0, $r - 30);
            $g2 = max(0, $g - 30);
            $b2 = max(0, $b - 30);
            $color2 = sprintf('#%02x%02x%02x', $r2, $g2, $b2);
            echo '.header-cta-button { background: linear-gradient(135deg, ' . esc_attr($bg_color) . ' 0%, ' . $color2 . ' 100%) !important; }';
        }
        // 'solid' is default, already handled
    }
    
    // Mobile Menu Styling (basic structure, full implementation in navigation.js)
    $mobile_breakpoint = isset($header_options['mobile_breakpoint']) ? absint($header_options['mobile_breakpoint']) : 768;
    
    if (!empty($header_options['mobile_menu_style'])) {
        $menu_style = $header_options['mobile_menu_style'];
        
        echo '@media (max-width: ' . $mobile_breakpoint . 'px) {';
        
        if ($menu_style === 'fullscreen') {
            echo '.mobile-menu-overlay { position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.95) !important; z-index: 9999 !important; display: flex !important; align-items: center !important; justify-content: center !important; }';
            echo '.mobile-menu-overlay .primary-menu { flex-direction: column !important; gap: 30px !important; }';
            echo '.mobile-menu-overlay .primary-menu a { font-size: 24px !important; }';
        } elseif ($menu_style === 'slide') {
            $position = isset($header_options['mobile_menu_position']) ? $header_options['mobile_menu_position'] : 'left';
            $transform = $position === 'right' ? 'translateX(100%)' : 'translateX(-100%)';
            if ($position === 'top') $transform = 'translateY(-100%)';
            
            echo '.mobile-menu-slide { position: fixed !important; top: 0 !important; ' . ($position === 'right' ? 'right' : 'left') . ': 0 !important; width: 300px !important; height: 100vh !important; background: #fff !important; box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important; transform: ' . $transform . ' !important; transition: transform 0.3s ease !important; z-index: 9999 !important; overflow-y: auto !important; }';
            echo '.mobile-menu-slide.active { transform: translateX(0) !important; }';
        } elseif ($menu_style === 'push') {
            echo 'body.mobile-menu-open { overflow: hidden !important; }';
            echo 'body.mobile-menu-open .site-wrapper { transform: translateX(-300px) !important; transition: transform 0.3s ease !important; }';
        }
        
        echo '}';
    }

    // Footer template colors
    $footer_options = ross_get_footer_options();
    $template = isset($footer_options['footer_template']) ? $footer_options['footer_template'] : 'business-professional';
    $use_template = isset($footer_options['use_template_colors']) ? intval($footer_options['use_template_colors']) : 1;

    // Legacy template ID migration map
    $legacy_map = array(
        'template1' => 'business-professional',
        'template2' => 'ecommerce',
        'template3' => 'creative-agency',
        'template4' => 'minimal-modern'
    );
    
    // Migrate legacy template IDs on the fly
    if (isset($legacy_map[$template])) {
        $template = $legacy_map[$template];
    }

    // Define sane defaults for templates
    $tpl_defaults = array(
        'business-professional' => array('bg' => '#f8f9fb', 'text' => '#0b2140', 'accent' => '#005eb8', 'social' => '#0b2140', 'columns' => 4, 'social_align' => 'center'),
        'ecommerce' => array('bg' => '#1a1a1a', 'text' => '#ffffff', 'accent' => '#b02a2a', 'social' => '#ffffff', 'columns' => 4, 'social_align' => 'center'),
        'creative-agency' => array('bg' => '#0c0c0d', 'text' => '#ffffff', 'accent' => '#E5C902', 'social' => '#ffffff', 'columns' => 4, 'social_align' => 'center'),
        'minimal-modern' => array('bg' => '#ffffff', 'text' => '#1a202c', 'accent' => '#3182ce', 'social' => '#718096', 'columns' => 1, 'social_align' => 'left')
    );

    if (isset($tpl_defaults[$template])) {
        $defaults = $tpl_defaults[$template];
        // allow overrides from settings when use_template_colors disabled
        $bg = ($use_template ? $defaults['bg'] : ($footer_options[$template . '_bg'] ?? $defaults['bg']));
        $text = ($use_template ? $defaults['text'] : ($footer_options[$template . '_text'] ?? $defaults['text']));
        $accent = ($use_template ? $defaults['accent'] : ($footer_options[$template . '_accent'] ?? $defaults['accent']));
        $social = ($use_template ? $defaults['social'] : ($footer_options[$template . '_social'] ?? $defaults['social']));

        // Admin 'Styling' overrides (higher priority)
        if (!empty($footer_options['styling_bg_color'])) {
            $bg = $footer_options['styling_bg_color'];
        }
        if (!empty($footer_options['styling_text_color'])) {
            $text = $footer_options['styling_text_color'];
        }
        if (!empty($footer_options['styling_link_color'])) {
            $accent = $footer_options['styling_link_color'];
        }

        // link hover
        $link_hover = isset($footer_options['styling_link_hover']) && $footer_options['styling_link_hover'] !== '' ? $footer_options['styling_link_hover'] : '';

        // typography
        $font_size = isset($footer_options['styling_font_size']) && intval($footer_options['styling_font_size']) ? intval($footer_options['styling_font_size']) : 0;
        $line_height = isset($footer_options['styling_line_height']) && floatval($footer_options['styling_line_height']) ? floatval($footer_options['styling_line_height']) : 0;

        // spacing & padding
        $padding_lr = isset($footer_options['styling_padding_lr']) ? absint($footer_options['styling_padding_lr']) : 0;

        // border
        $border_top = isset($footer_options['styling_border_top']) && $footer_options['styling_border_top'] ? true : false;
        $border_color = isset($footer_options['styling_border_color']) ? $footer_options['styling_border_color'] : '';
        $border_thickness = isset($footer_options['styling_border_thickness']) ? absint($footer_options['styling_border_thickness']) : 0;

        // widget title style
        $widget_title_color = isset($footer_options['styling_widget_title_color']) ? $footer_options['styling_widget_title_color'] : '';
        $widget_title_size = isset($footer_options['styling_widget_title_size']) ? absint($footer_options['styling_widget_title_size']) : 0;

        // Build background layers: gradient/top-overlay/image
        $bg_layers = array();

        // Compose background based on the chosen background type
        $bg_type = isset($footer_options['styling_bg_type']) ? $footer_options['styling_bg_type'] : 'color';
        if ($bg_type === 'gradient' && !empty($footer_options['styling_bg_gradient_from']) && !empty($footer_options['styling_bg_gradient_to'])) {
            $from = $footer_options['styling_bg_gradient_from'];
            $to = $footer_options['styling_bg_gradient_to'];
            $bg_layers[] = 'linear-gradient(to bottom, ' . esc_attr($from) . ', ' . esc_attr($to) . ')';
        } else {
            // If an overlay color with opacity is requested, create a semi-transparent overlay layer
            if (isset($footer_options['styling_bg_opacity']) && $footer_options['styling_bg_opacity'] !== '' && $footer_options['styling_bg_opacity'] < 1 && !empty($footer_options['styling_bg_color'])) {
                $op = floatval($footer_options['styling_bg_opacity']);
                $hex = ltrim($footer_options['styling_bg_color'], '#');
                if (strlen($hex) === 3) {
                    $r = hexdec(str_repeat(substr($hex,0,1),2));
                    $g = hexdec(str_repeat(substr($hex,1,1),2));
                    $b = hexdec(str_repeat(substr($hex,2,1),2));
                } else {
                    $r = hexdec(substr($hex,0,2));
                    $g = hexdec(substr($hex,2,2));
                    $b = hexdec(substr($hex,4,2));
                }
                $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $op . ')';
                $bg_layers[] = 'linear-gradient(' . $rgba . ', ' . $rgba . ')';
            }
        }

        // If image is provided and the selected background type is 'image', add it as the bottom layer
        // (Avoid adding the image when the background type is 'color' because the image would hide the
        //  solid color and make color changes invisible unless user toggles the type.)
        if ($bg_type === 'image' && !empty($footer_options['styling_bg_image'])) {
            $img = esc_url($footer_options['styling_bg_image']);
            $bg_layers[] = 'url("' . $img . '")';
        }

        // Overlay layer handling (topmost layer)
        $overlay_layers = array();
        if (!empty($footer_options['styling_overlay_enabled']) && intval($footer_options['styling_overlay_enabled'])) {
            $overlay_type = isset($footer_options['styling_overlay_type']) ? $footer_options['styling_overlay_type'] : 'color';
            $overlay_opacity = isset($footer_options['styling_overlay_opacity']) ? floatval($footer_options['styling_overlay_opacity']) : 0.5;
            if ($overlay_type === 'color' && !empty($footer_options['styling_overlay_color'])) {
                $hex = ltrim($footer_options['styling_overlay_color'], '#');
                if (strlen($hex) === 3) {
                    $r = hexdec(str_repeat(substr($hex,0,1),2));
                    $g = hexdec(str_repeat(substr($hex,1,1),2));
                    $b = hexdec(str_repeat(substr($hex,2,1),2));
                } else {
                    $r = hexdec(substr($hex,0,2));
                    $g = hexdec(substr($hex,2,2));
                    $b = hexdec(substr($hex,4,2));
                }
                $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $overlay_opacity . ')';
                $overlay_layers[] = 'linear-gradient(' . $rgba . ',' . $rgba . ')';
            }
            if ($overlay_type === 'gradient' && !empty($footer_options['styling_overlay_gradient_from']) && !empty($footer_options['styling_overlay_gradient_to'])) {
                $from = $footer_options['styling_overlay_gradient_from'];
                $to = $footer_options['styling_overlay_gradient_to'];
                // apply overlay opacity by converting from/to to rgba with opacity
                $from_hex = ltrim($from, '#'); $to_hex = ltrim($to, '#');
                if (strlen($from_hex) === 3) { $fr = hexdec(str_repeat(substr($from_hex,0,1),2)); $fg = hexdec(str_repeat(substr($from_hex,1,1),2)); $fb = hexdec(str_repeat(substr($from_hex,2,1),2)); } else { $fr = hexdec(substr($from_hex,0,2)); $fg = hexdec(substr($from_hex,2,2)); $fb = hexdec(substr($from_hex,4,2)); }
                if (strlen($to_hex) === 3) { $tr = hexdec(str_repeat(substr($to_hex,0,1),2)); $tg = hexdec(str_repeat(substr($to_hex,1,1),2)); $tb = hexdec(str_repeat(substr($to_hex,2,1),2)); } else { $tr = hexdec(substr($to_hex,0,2)); $tg = hexdec(substr($to_hex,2,2)); $tb = hexdec(substr($to_hex,4,2)); }
                $from_rgba = 'rgba(' . $fr . ',' . $fg . ',' . $fb . ',' . $overlay_opacity . ')';
                $to_rgba = 'rgba(' . $tr . ',' . $tg . ',' . $tb . ',' . $overlay_opacity . ')';
                $overlay_layers[] = 'linear-gradient(to bottom, ' . $from_rgba . ', ' . $to_rgba . ')';
            }
            if ($overlay_type === 'image' && !empty($footer_options['styling_overlay_image'])) {
                $oi = esc_url($footer_options['styling_overlay_image']);
                // If opacity < 1, add an overlay tint above the image to simulate shading
                if ($overlay_opacity < 1 && !empty($footer_options['styling_overlay_color'])) {
                    // tint overlay for better control
                    $hex = ltrim($footer_options['styling_overlay_color'], '#');
                    if (strlen($hex) === 3) { $r = hexdec(str_repeat(substr($hex,0,1),2)); $g = hexdec(str_repeat(substr($hex,1,1),2)); $b = hexdec(str_repeat(substr($hex,2,1),2)); } else { $r = hexdec(substr($hex,0,2)); $g = hexdec(substr($hex,2,2)); $b = hexdec(substr($hex,4,2)); }
                    $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . (1-$overlay_opacity) . ')';
                    $overlay_layers[] = 'linear-gradient(' . $rgba . ',' . $rgba . ')';
                }
                $overlay_layers[] = 'url("' . $oi . '")';
            }
        }

        // Prepend overlay layers so they sit on top
        if (!empty($overlay_layers)) {
            $bg_layers = array_merge($overlay_layers, $bg_layers);
        }

        // Always output a fallback solid background color (so color works even when image/gradient present)
        if (!empty($bg)) {
            echo '.site-footer { background-color: ' . esc_attr($bg) . ' !important; color: ' . esc_attr($text) . ' !important; }';
        }

        if (!empty($bg_layers)) {
            $bg_css = implode(', ', $bg_layers);
            echo '.site-footer { background-image: ' . $bg_css . ' !important; background-size: cover !important; background-position: center center !important; }';
            echo '.site-footer a { color: ' . esc_attr($accent) . ' !important; }';
        }

        // Set CSS variable for accent color (used by templates for hover effects)
        if (!empty($accent)) {
            echo '.site-footer, .footer-business-professional { --footer-accent-color: ' . esc_attr($accent) . ' !important; }';
        }

        if (!empty($social)) {
            echo '.site-footer .social-icon { color: ' . esc_attr($social) . ' !important; }';
        }

        // link hover
        if (!empty($link_hover)) {
            echo '.site-footer a:hover { color: ' . esc_attr($link_hover) . ' !important; }';
        }

        // font size and line-height
        if (!empty($font_size)) {
            echo '.site-footer, .site-footer p, .site-footer li, .site-footer a { font-size: ' . intval($font_size) . 'px !important; }';
        }
        if (!empty($line_height)) {
            echo '.site-footer { line-height: ' . floatval($line_height) . ' !important; }';
        }

        // padding: per-side support with fallbacks
        $pad_top = isset($footer_options['styling_padding_top']) && $footer_options['styling_padding_top'] !== '' ? intval($footer_options['styling_padding_top']) : (isset($footer_options['footer_padding']) ? intval($footer_options['footer_padding']) : 60);
        $pad_bottom = isset($footer_options['styling_padding_bottom']) && $footer_options['styling_padding_bottom'] !== '' ? intval($footer_options['styling_padding_bottom']) : (isset($footer_options['footer_padding']) ? intval($footer_options['footer_padding']) : 60);
        $pad_left = isset($footer_options['styling_padding_left']) && $footer_options['styling_padding_left'] !== '' ? intval($footer_options['styling_padding_left']) : (isset($footer_options['styling_padding_lr']) ? intval($footer_options['styling_padding_lr']) : 20);
        $pad_right = isset($footer_options['styling_padding_right']) && $footer_options['styling_padding_right'] !== '' ? intval($footer_options['styling_padding_right']) : (isset($footer_options['styling_padding_lr']) ? intval($footer_options['styling_padding_lr']) : 20);
        echo '.site-footer { padding-top: ' . $pad_top . 'px !important; padding-bottom: ' . $pad_bottom . 'px !important; padding-left: ' . $pad_left . 'px !important; padding-right: ' . $pad_right . 'px !important; }';

        // horizontal padding for containers (if user set padding_lr separately)
        if (!empty($padding_lr)) {
            echo '.footer-widgets .container, .footer-copyright .container { padding-left: ' . intval($padding_lr) . 'px !important; padding-right: ' . intval($padding_lr) . 'px !important; }';
        }

        // Column and Row gaps
        $col_gap = isset($footer_options['styling_col_gap']) ? intval($footer_options['styling_col_gap']) : 24;
        $row_gap = isset($footer_options['styling_row_gap']) ? intval($footer_options['styling_row_gap']) : 18;
        echo '.footer-columns { gap: ' . $row_gap . 'px ' . $col_gap . 'px !important; grid-row-gap: ' . $row_gap . 'px !important; grid-column-gap: ' . $col_gap . 'px !important; }';

        // border top
        if ($border_top && $border_thickness > 0 && !empty($border_color)) {
            echo '.footer-widgets { border-top: ' . intval($border_thickness) . 'px solid ' . esc_attr($border_color) . ' !important; }';
        }

        // widget title styles
        if (!empty($widget_title_color)) {
            echo '.footer-widgets .widget-title, .footer-widgets h4 { color: ' . esc_attr($widget_title_color) . ' !important; }';
        }
        if (!empty($widget_title_size)) {
            echo '.footer-widgets .widget-title, .footer-widgets h4 { font-size: ' . intval($widget_title_size) . 'px !important; }';
        }

        // CTA Styling — generate if CTA will be visible (either enabled or always visible)
        $should_show_cta = function_exists('ross_theme_should_show_footer_cta') ? ross_theme_should_show_footer_cta() : (isset($footer_options['enable_footer_cta']) && $footer_options['enable_footer_cta']);
        if (defined('WP_DEBUG') && WP_DEBUG) { error_log('[ross_theme_dynamic_css] CTA should_show_cta=' . ($should_show_cta ? '1' : '0')); }
        if ($should_show_cta) {
        $cta_bg = isset($footer_options['cta_bg_color']) ? sanitize_hex_color($footer_options['cta_bg_color']) : '';
        $cta_bg_type = isset($footer_options['cta_bg_type']) ? $footer_options['cta_bg_type'] : 'color';
        $cta_bg_img = isset($footer_options['cta_bg_image']) ? esc_url($footer_options['cta_bg_image']) : '';
        // overlay support for CTA
        $cta_overlay_enabled = isset($footer_options['cta_overlay_enabled']) && intval($footer_options['cta_overlay_enabled']);
        $cta_overlay_type = isset($footer_options['cta_overlay_type']) ? $footer_options['cta_overlay_type'] : 'color';
        $cta_overlay_opacity = isset($footer_options['cta_overlay_opacity']) ? floatval($footer_options['cta_overlay_opacity']) : 0.5;
        $cta_grad_from = isset($footer_options['cta_bg_gradient_from']) ? sanitize_hex_color($footer_options['cta_bg_gradient_from']) : '';
        $cta_grad_to = isset($footer_options['cta_bg_gradient_to']) ? sanitize_hex_color($footer_options['cta_bg_gradient_to']) : '';
        $cta_text_color = isset($footer_options['cta_text_color']) ? sanitize_hex_color($footer_options['cta_text_color']) : '';
        $cta_button_bg = isset($footer_options['cta_button_bg_color']) ? sanitize_hex_color($footer_options['cta_button_bg_color']) : '';
        $cta_button_text_color = isset($footer_options['cta_button_text_color']) ? sanitize_hex_color($footer_options['cta_button_text_color']) : '';
        $cta_alignment = isset($footer_options['cta_alignment']) ? sanitize_text_field($footer_options['cta_alignment']) : 'center';
        $cta_gap = isset($footer_options['cta_gap']) ? intval($footer_options['cta_gap']) : 12;
        $cta_padding_top = isset($footer_options['cta_padding_top']) ? intval($footer_options['cta_padding_top']) : 24;
        $cta_padding_right = isset($footer_options['cta_padding_right']) ? intval($footer_options['cta_padding_right']) : 0;
        $cta_padding_bottom = isset($footer_options['cta_padding_bottom']) ? intval($footer_options['cta_padding_bottom']) : 24;
        $cta_padding_left = isset($footer_options['cta_padding_left']) ? intval($footer_options['cta_padding_left']) : 0;

        $cta_bg_layers = array();
        if ($cta_bg_type === 'color' && !empty($cta_bg)) {
            $cta_bg_layers[] = esc_attr($cta_bg);
            echo '.footer-cta { background-color: ' . esc_attr($cta_bg) . ' !important; }';
        }
        if ($cta_bg_type === 'gradient' && !empty($cta_grad_from) && !empty($cta_grad_to)) {
            echo '.footer-cta { background-image: linear-gradient(to right, ' . esc_attr($cta_grad_from) . ', ' . esc_attr($cta_grad_to) . ') !important; }';
        }
        if ($cta_bg_type === 'image' && !empty($cta_bg_img)) {
            $cta_bg_layers[] = 'url("' . esc_url($cta_bg_img) . '")';
        }
        if (!empty($cta_text_color)) {
            echo '.footer-cta, .footer-cta .footer-cta-text, .footer-cta h2, .footer-cta h3 { color: ' . esc_attr($cta_text_color) . ' !important; }';
        }
        if (!empty($cta_button_bg)) {
            echo '.footer-cta .btn { background: ' . esc_attr($cta_button_bg) . ' !important; }';
        }
        if (!empty($cta_button_text_color)) {
            echo '.footer-cta .btn { color: ' . esc_attr($cta_button_text_color) . ' !important; }';
        }
        echo '.footer-cta { padding: ' . intval($cta_padding_top) . 'px ' . intval($cta_padding_right) . 'px ' . intval($cta_padding_bottom) . 'px ' . intval($cta_padding_left) . 'px !important; }';
        if (isset($footer_options['cta_margin_top']) || isset($footer_options['cta_margin_right']) || isset($footer_options['cta_margin_bottom']) || isset($footer_options['cta_margin_left'])) {
            $m_top = isset($footer_options['cta_margin_top']) ? intval($footer_options['cta_margin_top']) : 0;
            $m_right = isset($footer_options['cta_margin_right']) ? intval($footer_options['cta_margin_right']) : 0;
            $m_bottom = isset($footer_options['cta_margin_bottom']) ? intval($footer_options['cta_margin_bottom']) : 0;
            $m_left = isset($footer_options['cta_margin_left']) ? intval($footer_options['cta_margin_left']) : 0;
            echo '.footer-cta { margin: ' . $m_top . 'px ' . $m_right . 'px ' . $m_bottom . 'px ' . $m_left . 'px !important; }';
        }
        // alignment
        if (!empty($cta_alignment)) {
            $justify = 'center';
            if ($cta_alignment === 'left') $justify = 'flex-start';
            if ($cta_alignment === 'right') $justify = 'flex-end';
            echo '.footer-cta .footer-cta-inner { display: flex; align-items: center; justify-content: ' . $justify . ' !important; gap: ' . intval($cta_gap) . 'px !important; }';
        }
        // Icon color
        if (!empty($footer_options['cta_icon_color'])) {
            echo '.footer-cta .cta-icon { color: ' . esc_attr($footer_options['cta_icon_color']) . ' !important; }';
        }

        // Layout specifics (direction, wrap, justify, align)
        $dir = isset($footer_options['cta_layout_direction']) ? $footer_options['cta_layout_direction'] : 'row';
        $wrap = isset($footer_options['cta_layout_wrap']) && $footer_options['cta_layout_wrap'] ? 'wrap' : 'nowrap';
        $justify = isset($footer_options['cta_layout_justify']) ? $footer_options['cta_layout_justify'] : 'center';
        $align = isset($footer_options['cta_layout_align']) ? $footer_options['cta_layout_align'] : 'center';
        echo '.footer-cta .footer-cta-inner { flex-direction: ' . esc_attr($dir) . ' !important; flex-wrap: ' . esc_attr($wrap) . ' !important; justify-content:' . esc_attr($justify) . ' !important; align-items:' . esc_attr($align) . ' !important; }';

        // NEW CTA Enhancements - Border, Shadow, Typography, Button Hover, Container Width
        if (isset($footer_options['cta_border_width']) && intval($footer_options['cta_border_width']) > 0) {
            $border_width = intval($footer_options['cta_border_width']);
            $border_style = isset($footer_options['cta_border_style']) ? esc_attr($footer_options['cta_border_style']) : 'solid';
            $border_color = isset($footer_options['cta_border_color']) ? esc_attr($footer_options['cta_border_color']) : '#cccccc';
            $border_radius = isset($footer_options['cta_border_radius']) ? intval($footer_options['cta_border_radius']) : 0;
            echo '.footer-cta { border: ' . $border_width . 'px ' . $border_style . ' ' . $border_color . ' !important; border-radius: ' . $border_radius . 'px !important; }';
        }
        if (isset($footer_options['cta_box_shadow']) && $footer_options['cta_box_shadow']) {
            $shadow_color = isset($footer_options['cta_shadow_color']) ? esc_attr($footer_options['cta_shadow_color']) : 'rgba(0,0,0,0.1)';
            $shadow_blur = isset($footer_options['cta_shadow_blur']) ? intval($footer_options['cta_shadow_blur']) : 10;
            echo '.footer-cta { box-shadow: 0 4px ' . $shadow_blur . 'px ' . $shadow_color . ' !important; }';
        }
        if (isset($footer_options['cta_title_font_size'])) {
            $title_font_size = intval($footer_options['cta_title_font_size']);
            echo '.footer-cta .footer-cta-title, .footer-cta h2 { font-size: ' . $title_font_size . 'px !important; }';
        }
        if (isset($footer_options['cta_title_font_weight'])) {
            $title_font_weight = esc_attr($footer_options['cta_title_font_weight']);
            echo '.footer-cta .footer-cta-title, .footer-cta h2 { font-weight: ' . $title_font_weight . ' !important; }';
        }
        if (isset($footer_options['cta_text_font_size'])) {
            $text_font_size = intval($footer_options['cta_text_font_size']);
            echo '.footer-cta .footer-cta-text { font-size: ' . $text_font_size . 'px !important; }';
        }
        if (isset($footer_options['cta_button_font_size'])) {
            $button_font_size = intval($footer_options['cta_button_font_size']);
            echo '.footer-cta .btn { font-size: ' . $button_font_size . 'px !important; }';
        }
        if (isset($footer_options['cta_button_font_weight'])) {
            $button_font_weight = esc_attr($footer_options['cta_button_font_weight']);
            echo '.footer-cta .btn { font-weight: ' . $button_font_weight . ' !important; }';
        }
        if (isset($footer_options['cta_letter_spacing'])) {
            $letter_spacing = floatval($footer_options['cta_letter_spacing']);
            echo '.footer-cta .footer-cta-title, .footer-cta h2 { letter-spacing: ' . $letter_spacing . 'px !important; }';
        }
        if (isset($footer_options['cta_button_hover_bg']) || isset($footer_options['cta_button_hover_text'])) {
            $hover_bg = isset($footer_options['cta_button_hover_bg']) ? esc_attr($footer_options['cta_button_hover_bg']) : '';
            $hover_text = isset($footer_options['cta_button_hover_text']) ? esc_attr($footer_options['cta_button_hover_text']) : '';
            if (!empty($hover_bg)) {
                echo '.footer-cta .btn:hover { background: ' . $hover_bg . ' !important; }';
            }
            if (!empty($hover_text)) {
                echo '.footer-cta .btn:hover { color: ' . $hover_text . ' !important; }';
            }
        }
        if (isset($footer_options['cta_button_border_radius'])) {
            $button_radius = intval($footer_options['cta_button_border_radius']);
            echo '.footer-cta .btn { border-radius: ' . $button_radius . 'px !important; }';
        }
        if (isset($footer_options['cta_container_width'])) {
            $container_width = esc_attr($footer_options['cta_container_width']);
            if ($container_width === 'full') {
                echo '.footer-cta .container { max-width: 100% !important; width: 100% !important; }';
            } elseif ($container_width === 'custom' && isset($footer_options['cta_max_width'])) {
                $max_width = intval($footer_options['cta_max_width']);
                echo '.footer-cta .container { max-width: ' . $max_width . 'px !important; }';
            }
        }

            // small animations
            if (isset($footer_options['cta_animation']) && $footer_options['cta_animation'] !== 'none') {
            $anim = $footer_options['cta_animation'];
            $anim_delay = isset($footer_options['cta_anim_delay']) ? intval($footer_options['cta_anim_delay']) : 150;
            $anim_duration = isset($footer_options['cta_anim_duration']) ? intval($footer_options['cta_anim_duration']) : 400;
            $anim_delay_s = max(0.0, floatval($anim_delay / 1000.0));
            if ($anim === 'fade') {
                echo '.footer-cta--anim-fade { opacity: 0; transform: translateY(6px); transition: opacity ' . intval($anim_duration) . 'ms ease, transform ' . intval($anim_duration) . 'ms ease; }';
                echo '.footer-cta--anim-fade.visible { opacity: 1; transform: none; }';
                echo '.footer-cta--anim-fade { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            if ($anim === 'slide') {
                echo '.footer-cta--anim-slide { opacity: 0; transform: translateY(12px); transition: opacity ' . intval($anim_duration) . 'ms ease, transform ' . intval($anim_duration) . 'ms ease; }';
                echo '.footer-cta--anim-slide.visible { opacity: 1; transform: none; }';
                echo '.footer-cta--anim-slide { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            if ($anim === 'pop') {
                echo '.footer-cta--anim-pop { opacity: 0; transform: scale(.96); transition: opacity ' . intval($anim_duration) . 'ms ease, transform ' . intval($anim_duration) . 'ms ease; }';
                echo '.footer-cta--anim-pop.visible { opacity: 1; transform: scale(1); }';
                echo '.footer-cta--anim-pop { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            if ($anim === 'zoom') {
                echo '.footer-cta--anim-zoom { opacity: 0; transform: scale(.95); transition: opacity ' . intval($anim_duration) . 'ms ease, transform ' . intval($anim_duration) . 'ms ease; }';
                echo '.footer-cta--anim-zoom.visible { opacity: 1; transform: scale(1.02); }';
                echo '.footer-cta--anim-zoom { transition-delay:' . $anim_delay_s . 's !important; }';
            }
            }
        }
        // CTA overlay CSS
        if (!empty($cta_overlay_enabled) && $cta_overlay_enabled) {
            // Build overlay layer for CTA background
            $overlay_layer = '';
            if ($cta_overlay_type === 'color' && !empty($footer_options['cta_overlay_color'])) {
                $hex = ltrim($footer_options['cta_overlay_color'], '#');
                if (strlen($hex) === 3) { $r = hexdec(str_repeat(substr($hex,0,1),2)); $g = hexdec(str_repeat(substr($hex,1,1),2)); $b = hexdec(str_repeat(substr($hex,2,1),2)); } else { $r = hexdec(substr($hex,0,2)); $g = hexdec(substr($hex,2,2)); $b = hexdec(substr($hex,4,2)); }
                $rgba = 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $cta_overlay_opacity . ')';
                $overlay_layer = 'linear-gradient(' . $rgba . ',' . $rgba . ')';
            }
            if ($cta_overlay_type === 'gradient' && !empty($footer_options['cta_overlay_gradient_from']) && !empty($footer_options['cta_overlay_gradient_to'])) {
                $f = $footer_options['cta_overlay_gradient_from']; $t = $footer_options['cta_overlay_gradient_to'];
                $overlay_layer = 'linear-gradient(to bottom, ' . esc_attr($f) . ', ' . esc_attr($t) . ')';
            }
            if ($cta_overlay_type === 'image' && !empty($footer_options['cta_overlay_image'])) {
                $overlay_layer = 'url("' . esc_url($footer_options['cta_overlay_image']) . '")';
            }
            if (!empty($overlay_layer)) {
                echo '.footer-cta { background-image: ' . $overlay_layer . (isset($cta_bg_img) && $cta_bg_img ? ', url("' . esc_url($cta_bg_img) . '")' : '') . ' !important; background-size: cover !important; background-position:center center !important; }';
            }
        }
        // Custom CTA CSS (advanced)
        if (!empty($footer_options['enable_custom_cta']) && !empty($footer_options['custom_cta_css'])) {
            echo $footer_options['custom_cta_css'];
        }
        // Output CTA base background layers if no overlay forcing a layer above
        if (!empty($cta_bg_layers) && empty($cta_overlay_enabled)) {
            $bg_css = implode(', ', $cta_bg_layers);
            echo '.footer-cta { background-image: ' . $bg_css . ' !important; background-size: cover !important; background-position:center center !important; }';
        }
    }
    // Copyright styling
    $copyright_font_size = isset($footer_options['copyright_font_size']) ? intval($footer_options['copyright_font_size']) : 14;
    $copyright_font_weight = isset($footer_options['copyright_font_weight']) ? $footer_options['copyright_font_weight'] : 'normal';
    $copyright_letter_spacing = isset($footer_options['copyright_letter_spacing']) ? floatval($footer_options['copyright_letter_spacing']) : 0;
    $copyright_bg = isset($footer_options['copyright_bg_color']) ? sanitize_hex_color($footer_options['copyright_bg_color']) : '';
    $copyright_text_color = isset($footer_options['copyright_text_color']) ? sanitize_hex_color($footer_options['copyright_text_color']) : '';
    $copyright_alignment = isset($footer_options['copyright_alignment']) ? esc_attr($footer_options['copyright_alignment']) : 'center';
    $copyright_padding_top = isset($footer_options['copyright_padding_top']) ? intval($footer_options['copyright_padding_top']) : 20;
    $copyright_padding_bottom = isset($footer_options['copyright_padding_bottom']) ? intval($footer_options['copyright_padding_bottom']) : 20;
    $copyright_border_top = isset($footer_options['copyright_border_top']) ? (bool)$footer_options['copyright_border_top'] : false;
    $copyright_border_color = isset($footer_options['copyright_border_color']) ? sanitize_hex_color($footer_options['copyright_border_color']) : '#333333';
    $copyright_border_width = isset($footer_options['copyright_border_width']) ? intval($footer_options['copyright_border_width']) : 1;
    $copyright_link_color = isset($footer_options['copyright_link_color']) ? sanitize_hex_color($footer_options['copyright_link_color']) : '';
    $copyright_link_hover_color = isset($footer_options['copyright_link_hover_color']) ? sanitize_hex_color($footer_options['copyright_link_hover_color']) : '';
    
    if (!empty($copyright_bg)) {
        echo '.footer-copyright { background-color: ' . esc_attr($copyright_bg) . ' !important; }';
    }
    if (!empty($copyright_text_color)) {
        echo '.footer-copyright, .footer-copyright .copyright-content { color: ' . esc_attr($copyright_text_color) . ' !important; }';
    }
    if (!empty($copyright_font_size)) {
        echo '.footer-copyright .copyright-content { font-size: ' . intval($copyright_font_size) . 'px !important; }';
    }
    if (!empty($copyright_font_weight)) {
        $map = array('light' => '300', 'normal' => '400', 'bold' => '700');
        $fw = isset($map[$copyright_font_weight]) ? $map[$copyright_font_weight] : '400';
        echo '.footer-copyright .copyright-content { font-weight: ' . esc_attr($fw) . ' !important; }';
    }
    if ($copyright_letter_spacing !== '' && $copyright_letter_spacing !== null) {
        echo '.footer-copyright .copyright-content { letter-spacing: ' . floatval($copyright_letter_spacing) . 'px !important; }';
    }
    if ($copyright_padding_top >= 0 || $copyright_padding_bottom >= 0) {
        echo '.footer-copyright { padding-top: ' . intval($copyright_padding_top) . 'px !important; padding-bottom: ' . intval($copyright_padding_bottom) . 'px !important; }';
    }
    if ($copyright_border_top && !empty($copyright_border_color) && $copyright_border_width > 0) {
        echo '.footer-copyright { border-top: ' . intval($copyright_border_width) . 'px solid ' . esc_attr($copyright_border_color) . ' !important; }';
    }
    if (!empty($copyright_link_color)) {
        echo '.footer-copyright a { color: ' . esc_attr($copyright_link_color) . ' !important; }';
    }
    if (!empty($copyright_link_hover_color)) {
        echo '.footer-copyright a:hover { color: ' . esc_attr($copyright_link_hover_color) . ' !important; }';
    }

    // Social Icons Styling
    $social_icon_style = isset($footer_options['social_icon_style']) ? $footer_options['social_icon_style'] : 'circle';
    $social_icon_size = isset($footer_options['social_icon_size']) ? intval($footer_options['social_icon_size']) : 36;
    $social_icon_color = isset($footer_options['social_icon_color']) ? $footer_options['social_icon_color'] : '';
    $social_icon_bg_color = isset($footer_options['social_icon_bg_color']) ? $footer_options['social_icon_bg_color'] : '';
    $social_icon_bg_active_color = isset($footer_options['social_icon_bg_active_color']) ? $footer_options['social_icon_bg_active_color'] : '';
    $social_icon_bg_hover_color = isset($footer_options['social_icon_bg_hover_color']) ? $footer_options['social_icon_bg_hover_color'] : '';
    $social_icon_border_width = isset($footer_options['social_icon_border_width']) ? intval($footer_options['social_icon_border_width']) : 0;
    $social_icon_border_color = isset($footer_options['social_icon_border_color']) ? $footer_options['social_icon_border_color'] : '';
    $social_icon_border_active_color = isset($footer_options['social_icon_border_active_color']) ? $footer_options['social_icon_border_active_color'] : '';
    $social_icon_border_hover_color = isset($footer_options['social_icon_border_hover_color']) ? $footer_options['social_icon_border_hover_color'] : '';
    $social_hover_color = isset($footer_options['social_icon_hover_color']) ? $footer_options['social_icon_hover_color'] : '';
    
    if (!empty($social_icon_size)) {
        echo '.ross-social-icons .social-icon { width: ' . esc_attr($social_icon_size) . 'px !important; height: ' . esc_attr($social_icon_size) . 'px !important; }';
    }
    
    if ($social_icon_style === 'circle') {
        echo '.ross-social-icons .social-icon { border-radius: 50% !important; }';
    } elseif ($social_icon_style === 'square') {
        echo '.ross-social-icons .social-icon { border-radius: 0 !important; }';
    } elseif ($social_icon_style === 'rounded') {
        echo '.ross-social-icons .social-icon { border-radius: 8px !important; }';
    } elseif ($social_icon_style === 'plain') {
        echo '.ross-social-icons .social-icon { background: transparent !important; }';
        echo '.ross-social-icons .social-icon:hover { background: transparent !important; }';
    }
    
    // Apply custom background colors if set (normal, active, hover)
    if (!empty($social_icon_bg_color) && $social_icon_style !== 'plain') {
        // Use multiple selectors with high specificity to override static CSS
        echo '.ross-social-icons .social-icon, .ross-social-icons a.social-icon { background: ' . esc_attr($social_icon_bg_color) . ' !important; background-image: none !important; }';
    } elseif ($social_icon_style !== 'plain') {
        // Fallback to semi-transparent background if no custom bg color
        echo '.ross-social-icons .social-icon, .ross-social-icons a.social-icon { background: rgba(255,255,255,0.1) !important; background-image: none !important; }';
    }
    
    // Active state background color
    if (!empty($social_icon_bg_active_color) && $social_icon_style !== 'plain') {
        echo '.ross-social-icons .social-icon.active, .ross-social-icons .social-icon.current, .ross-social-icons a.social-icon.active, .ross-social-icons a.social-icon.current { background: ' . esc_attr($social_icon_bg_active_color) . ' !important; background-image: none !important; }';
    }
    
    // Hover state background color (override platform-specific hover styles with attribute selector)
    if (!empty($social_icon_bg_hover_color) && $social_icon_style !== 'plain') {
        // Use attribute selector to match static CSS specificity
        echo '.ross-social-icons .social-icon:hover, .ross-social-icons a.social-icon:hover, .ross-social-icons .social-icon[data-platform]:hover { background: ' . esc_attr($social_icon_bg_hover_color) . ' !important; background-image: none !important; }';
    } elseif ($social_icon_style !== 'plain') {
        // Fallback hover effect with attribute selector
        echo '.ross-social-icons .social-icon:hover, .ross-social-icons a.social-icon:hover, .ross-social-icons .social-icon[data-platform]:hover { background: rgba(255,255,255,0.2) !important; background-image: none !important; }';
    }
    
    if (!empty($social_icon_color)) {
        echo '.ross-social-icons .social-icon { color: ' . esc_attr($social_icon_color) . ' !important; }';
    }
    
    if (!empty($social_hover_color)) {
        echo '.ross-social-icons .social-icon:hover { color: ' . esc_attr($social_hover_color) . ' !important; }';
    }
    
    // Border styling (normal, active, hover) - only for circle/square/rounded styles
    if ($social_icon_border_width > 0 && $social_icon_style !== 'plain') {
        // Normal state border
        if (!empty($social_icon_border_color)) {
            echo '.ross-social-icons .social-icon, .ross-social-icons a.social-icon { border: ' . intval($social_icon_border_width) . 'px solid ' . esc_attr($social_icon_border_color) . ' !important; }';
        } else {
            // Default border when width is set but no color specified
            echo '.ross-social-icons .social-icon, .ross-social-icons a.social-icon { border: ' . intval($social_icon_border_width) . 'px solid rgba(255,255,255,0.3) !important; }';
        }
        
        // Active state border
        if (!empty($social_icon_border_active_color)) {
            echo '.ross-social-icons .social-icon.active, .ross-social-icons .social-icon.current, .ross-social-icons a.social-icon.active, .ross-social-icons a.social-icon.current { border-color: ' . esc_attr($social_icon_border_active_color) . ' !important; }';
        }
        
        // Hover state border
        if (!empty($social_icon_border_hover_color)) {
            echo '.ross-social-icons .social-icon:hover, .ross-social-icons a.social-icon:hover, .ross-social-icons .social-icon[data-platform]:hover { border-color: ' . esc_attr($social_icon_border_hover_color) . ' !important; }';
        }
    } else {
        // Ensure no borders when width is 0
        echo '.ross-social-icons .social-icon, .ross-social-icons a.social-icon { border: none !important; }';
    }

    // Custom footer CSS if provided
    if (!empty($footer_options['custom_footer_css'])) {
        echo $footer_options['custom_footer_css'];
    }
    
    // Apply general colors
    if (!empty($general_options['primary_color'])) {
        echo ':root { --primary-color: ' . esc_attr($general_options['primary_color']) . '; }';
        echo '.primary-color { color: ' . esc_attr($general_options['primary_color']) . ' !important; }';
    }
    
    if (!empty($general_options['secondary_color'])) {
        echo ':root { --secondary-color: ' . esc_attr($general_options['secondary_color']) . '; }';
    }

    echo '</style>';

    // Get the captured CSS and cache it
    $css_output = ob_get_clean();
    wp_cache_set($cache_key, $css_output, 'ross_theme_css', 3600); // Cache for 1 hour

    echo $css_output;

    // Debug: output header options as comment for admins (not cached)
    if (current_user_can('manage_options')) {
        echo '/* ROSS THEME DEBUG - Header Options: ' . esc_html(json_encode($header_options)) . ' */';
    }
}

/**
 * Clear the dynamic CSS cache
 * Useful when theme options change or for debugging
 */
function ross_clear_dynamic_css_cache() {
    // Clear all ross_theme_css cache group
    if (function_exists('wp_cache_flush_group')) {
        wp_cache_flush_group('ross_theme_css');
    } else {
        // Fallback: flush all cache (less efficient but works)
        wp_cache_flush();
    }
}

// Clear CSS cache when theme options are updated
add_action('update_option', 'ross_clear_dynamic_css_cache_on_option_update', 10, 3);
function ross_clear_dynamic_css_cache_on_option_update($option_name, $old_value, $new_value) {
    $theme_options = array(
        'ross_theme_header_options',
        'ross_theme_general_options',
        'ross_theme_footer_options'
    );

    if (in_array($option_name, $theme_options)) {
        ross_clear_dynamic_css_cache();
    }
}

add_action('wp_head', 'ross_theme_dynamic_css', 999); // High priority