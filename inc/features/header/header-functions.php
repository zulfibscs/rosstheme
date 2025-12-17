<?php
/**
 * Header Functions Module
 * Handles header display logic based on options
 */

if (!defined('ABSPATH')) exit;

/**
 * Get header options with fallbacks
 */
function ross_theme_get_header_options() {
    $options = get_option('ross_theme_header_options', array());
    
    // Set defaults if options don't exist
    $defaults = array(
        'header_style' => 'default',
        'header_width' => 'contained',
            'header_center' => 0,
            'header_padding_top' => '20',
            'header_padding_right' => '0',
            'header_padding_bottom' => '20',
            'header_padding_left' => '0',
            'header_margin_top' => '0',
            'header_margin_right' => '0',
            'header_margin_bottom' => '0',
            'header_margin_left' => '0',
        'sticky_header' => 0,
        'header_height' => '80',
        'logo_upload' => '',
        'logo_width' => '200',
        'show_site_title' => 1,
        'enable_topbar' => 0,
        'topbar_left_content' => '',
        'topbar_bg_color' => '#001946',
        'topbar_text_color' => '#ffffff',
        'topbar_icon_color' => '#ffffff',
        // new topbar defaults
        'enable_social' => 0,
        'social_facebook' => '',
        'social_twitter' => '',
        'social_linkedin' => '',
        'social_instagram' => '',
        'social_youtube' => '',
        'social_icon_size' => 'medium',
        'social_icon_shape' => 'circle',
        'social_icon_color' => '#ffffff',
        'social_icon_bg_color' => 'transparent',
        'social_icon_effect' => 'none',
        'social_icon_border_color' => 'transparent',
        'social_icon_border_size' => '0',
        'social_icon_width' => 32,
        'phone_number' => '',
        'topbar_email' => '',
        'enable_announcement' => 0,
            'announcement_text' => '',
            'enable_topbar_left' => 1,
            'announcement_animation' => 'marquee',
            'social_links' => array(),
        'color_palette' => 'professional',
        'menu_alignment' => 'left',
        'menu_font_size' => '16',
        'active_item_color' => '#E5C902',
        'enable_search' => 1,
        'enable_cta_button' => 1,
        'cta_button_text' => 'Get Free Consultation',
        'cta_button_color' => '#E5C902',
        'cta_button_text_color' => '#001946',
        'cta_button_hover_text_color' => '#001946',
        'cta_button_url' => '',
        'cta_button_style' => 'solid',
        'cta_button_size' => 'medium',
        'cta_button_font_size' => '16',
        'cta_button_border_radius' => '8',
        'cta_button_hover_effect' => 'scale',
        'cta_button_text_hover_effect' => 'none',
        'header_bg_color' => '#ffffff',
        'header_text_color' => '#333333',
        'header_link_hover_color' => '#E5C902',
        'transparent_homepage' => 0,
        // Style enhancements
        'topbar_shadow_enable' => 0,
        'topbar_gradient_enable' => 0,
        'topbar_gradient_color1' => '#001946',
        'topbar_gradient_color2' => '#003d7a',
        'topbar_border_color' => '#E5C902',
        'topbar_border_width' => 0,
        'topbar_icon_hover_color' => '#E5C902',
        'sticky_topbar' => 0,
        'topbar_sticky_offset' => 0,
        'topbar_custom_icon_links' => array(),
        // Announcement defaults
        'announcement_bg_color' => '#E5C902',
        'announcement_text_color' => '#001946',
        'announcement_font_size' => '14px',
        'announcement_sticky' => 0,
        'announcement_sticky_offset' => 0,
        'announcement_position' => 'top_of_topbar',
    );
    
    return wp_parse_args($options, $defaults);
}

/**
 * Get header layout from options
 */
function ross_theme_get_header_layout() {
    $options = ross_theme_get_header_options();
    return $options['header_style'];
}

/**
 * Display the appropriate header
 */
function ross_theme_display_header() {
    $options = ross_theme_get_header_options();
    
    // Check if using new template system
    if (isset($options['header_template']) && !empty($options['header_template'])) {
        $template_id = $options['header_template'];
        
        // Map template IDs to template part names
        $template_map = array(
            'creative-agency' => 'creative-agency'
        );
        
        if (isset($template_map[$template_id])) {
            // Check if template file exists
            $template_file = get_template_directory() . '/template-parts/header/header-' . $template_map[$template_id] . '.php';
            
            if (file_exists($template_file)) {
                if (current_user_can('manage_options')) {
                    echo "<!-- Ross Theme Header Template: " . esc_html($template_id) . " -->\n";
                }
                get_template_part('template-parts/header/header', $template_map[$template_id]);
                return;
            }
        }
    }
    
    // Fallback to legacy header_style option
    $layout = isset($options['header_style']) ? $options['header_style'] : 'default';
    
    // Debug output
    if (current_user_can('manage_options')) {
        echo "<!-- Ross Theme Header: " . esc_html($layout) . " -->\n";
        echo "<!-- Logo URL: " . esc_html($options['logo_upload'] ?? '') . " -->\n";
    }
    
    // Load header template
    get_template_part('template-parts/header/header', $layout);
}

/**
 * Get header CSS classes
 */
function ross_theme_header_classes() {
    $options = ross_theme_get_header_options();
    $classes = array('site-header');
    
    // Header style
    $classes[] = 'header-' . $options['header_style'];
    
    // Sticky header
    if ($options['sticky_header']) {
        $classes[] = 'header-sticky';
    }
    
    // Header width
    $classes[] = 'header-' . $options['header_width'];
    // Centered layout flag
    if (!empty($options['header_center'])) {
        // Use the same class name used in CSS (.header-centered)
        $classes[] = 'header-centered';
    }
    
    return implode(' ', $classes);
}

/**
 * Check if header feature is enabled
 */
function ross_theme_header_feature_enabled($feature) {
    $options = ross_theme_get_header_options();
    
    switch($feature) {
        case 'topbar':
            return !empty($options['enable_topbar']);
        case 'search':
            return !empty($options['enable_search']);
        case 'cta_button':
            return !empty($options['enable_cta_button']);
        case 'sticky':
            return !empty($options['sticky_header']);
        default:
            return false;
    }
}

/**
 * Build header inline style from options
 * Returns a safe CSS string for header height, padding and margin
 */
function ross_theme_get_header_inline_style() {
    $options = ross_theme_get_header_options();
    
    // Sanitize each value to ensure it's numeric
    $height = intval($options['header_height'] ?? 80);
    $pt = intval($options['header_padding_top'] ?? 20);
    $pr = intval($options['header_padding_right'] ?? 0);
    $pb = intval($options['header_padding_bottom'] ?? 20);
    $pl = intval($options['header_padding_left'] ?? 0);
    $mt = intval($options['header_margin_top'] ?? 0);
    $mr = intval($options['header_margin_right'] ?? 0);
    $mb = intval($options['header_margin_bottom'] ?? 0);
    $ml = intval($options['header_margin_left'] ?? 0);
    
    $bg_color = isset($options['header_bg_color']) ? sanitize_hex_color($options['header_bg_color']) : '#ffffff';
    $text_color = isset($options['header_text_color']) ? sanitize_hex_color($options['header_text_color']) : '#333333';
    
    return sprintf(
        'background: %s; color: %s; min-height: %dpx; padding: %dpx %dpx %dpx %dpx; margin: %dpx %dpx %dpx %dpx;',
        esc_attr($bg_color),
        esc_attr($text_color),
        $height,
        $pt, $pr, $pb, $pl,
        $mt, $mr, $mb, $ml
    );
}

/**
 * Render top bar if enabled
 */
function ross_theme_render_topbar() {
    $options = ross_theme_get_header_options();
    
    if (empty($options['enable_topbar'])) {
        return;
    }

    // Check if advanced topbar is enabled - prevent duplication
    $advanced_options = get_option('ross_advanced_topbar_options', array());
    if (!empty($advanced_options['enable_topbar'])) {
        return; // Advanced topbar is active, skip main topbar
    }

    $bg = isset($options['topbar_bg_color']) ? esc_attr($options['topbar_bg_color']) : '#001946';
    $color = isset($options['topbar_text_color']) ? esc_attr($options['topbar_text_color']) : '#ffffff';

    // Left content (can contain simple HTML from admin)
    $left = !empty($options['topbar_left_content']) ? $options['topbar_left_content'] : '';
    $enable_left = isset($options['enable_topbar_left']) ? (bool) $options['enable_topbar_left'] : true;

    // Phone, email and social
    $phone = !empty($options['phone_number']) ? esc_html($options['phone_number']) : '';
    $email = !empty($options['topbar_email']) ? sanitize_email($options['topbar_email']) : '';

    $enable_social = !empty($options['enable_social']);
    // Support repeater social_links (preferred) or legacy fb/tw/li fields
    $social_links = array();
    if (!empty($options['social_links']) && is_array($options['social_links'])) {
        foreach ($options['social_links'] as $s) {
            if (!empty($s['url'])) {
                $social_links[] = array('icon' => isset($s['icon']) ? $s['icon'] : '', 'url' => esc_url($s['url']));
            }
        }
    } else {
        $fb = !empty($options['social_facebook']) ? esc_url($options['social_facebook']) : '';
        $fb_enabled = isset($options['social_facebook_enabled']) ? $options['social_facebook_enabled'] : (!empty($fb));
        $fb_icon = isset($options['social_facebook_icon']) ? $options['social_facebook_icon'] : 'fab fa-facebook-f';
        $tw = !empty($options['social_twitter']) ? esc_url($options['social_twitter']) : '';
        $tw_enabled = isset($options['social_twitter_enabled']) ? $options['social_twitter_enabled'] : (!empty($tw));
        $tw_icon = isset($options['social_twitter_icon']) ? $options['social_twitter_icon'] : 'fab fa-twitter';
        $li = !empty($options['social_linkedin']) ? esc_url($options['social_linkedin']) : '';
        $li_enabled = isset($options['social_linkedin_enabled']) ? $options['social_linkedin_enabled'] : (!empty($li));
        $li_icon = isset($options['social_linkedin_icon']) ? $options['social_linkedin_icon'] : 'fab fa-linkedin-in';
        $ig = !empty($options['social_instagram']) ? esc_url($options['social_instagram']) : '';
        $ig_enabled = isset($options['social_instagram_enabled']) ? $options['social_instagram_enabled'] : (!empty($ig));
        $ig_icon = isset($options['social_instagram_icon']) ? $options['social_instagram_icon'] : 'fab fa-instagram';
        $yt = !empty($options['social_youtube']) ? esc_url($options['social_youtube']) : '';
        $yt_enabled = isset($options['social_youtube_enabled']) ? $options['social_youtube_enabled'] : (!empty($yt));
        $yt_icon = isset($options['social_youtube_icon']) ? $options['social_youtube_icon'] : 'fab fa-youtube';
        if ($fb && $fb_enabled) $social_links[] = array('icon' => $fb_icon, 'url' => $fb);
        if ($tw && $tw_enabled) $social_links[] = array('icon' => $tw_icon, 'url' => $tw);
        if ($li && $li_enabled) $social_links[] = array('icon' => $li_icon, 'url' => $li);
        if ($ig && $ig_enabled) $social_links[] = array('icon' => $ig_icon, 'url' => $ig);
        if ($yt && $yt_enabled) $social_links[] = array('icon' => $yt_icon, 'url' => $yt);
    }

    // Render markup
    // Topbar container render with layout variations based on enabled areas
    $classes = array('site-topbar');
    if (!$enable_left) $classes[] = 'topbar--no-left';
    if (empty($social_links) || !$enable_social) $classes[] = 'topbar--no-right';
    if (!empty($options['sticky_topbar'])) $classes[] = 'topbar-sticky';

    echo '<div class="' . implode(' ', $classes) . '">';
    echo '<div class="container topbar-inner">';

    // Left
    if ($enable_left) {
        echo '<div class="topbar-left">' . wp_kses_post($left) . '</div>';
    } else {
        echo '<div class="topbar-left"></div>';
    }

    // Center (reserved for nav/branding). Announcement shown above as a single-line strip.
    echo '<div class="topbar-center"></div>';

    // Right
    echo '<div class="topbar-right">';
    if ($phone) {
        echo '<a class="topbar-phone" href="tel:' . esc_attr($phone) . '" style="color:' . $color . ';">' . esc_html($phone) . '</a>';
    }
    if ($email) {
        echo '<a class="topbar-email" href="mailto:' . esc_attr($email) . '" style="color:' . $color . ';">' . esc_html($email) . '</a>';
    }

    if ($enable_social && !empty($social_links)) {
        $icon_color = isset($options['topbar_icon_color']) ? esc_attr($options['topbar_icon_color']) : $color;
        $icon_size = isset($options['social_icon_size']) ? $options['social_icon_size'] : 'medium';
        $icon_shape = isset($options['social_icon_shape']) ? $options['social_icon_shape'] : 'circle';
        $icon_bg_color = isset($options['social_icon_bg_color']) ? $options['social_icon_bg_color'] : 'transparent';
        $icon_effect = isset($options['social_icon_effect']) ? $options['social_icon_effect'] : 'none';
        $icon_border_color = isset($options['social_icon_border_color']) ? $options['social_icon_border_color'] : 'transparent';
        $icon_border_size = isset($options['social_icon_border_size']) ? $options['social_icon_border_size'] : '0';
        $icon_width = isset($options['social_icon_width']) ? intval($options['social_icon_width']) : 32;
        $link_extra_classes = 'social-link--' . esc_attr($icon_size) . ' social-link--' . esc_attr($icon_shape);
        if ($icon_effect !== 'none') {
            $link_extra_classes .= ' social-link--' . esc_attr($icon_effect);
        }

        echo '<div class="topbar-social">';
        foreach ($social_links as $s) {
            $icon_output = '';
            if (!empty($s['icon'])) {
                $icon_raw = trim($s['icon']);
                // Handle different icon types
                if (strpos($icon_raw, '<') === 0) {
                    // HTML icon (SVG, etc.)
                    $icon_output = wp_kses($icon_raw, array('i' => array('class' => array()), 'svg' => array(), 'path' => array()));
                } elseif (preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $icon_raw)) {
                    // Uploaded image URL
                    $icon_output = '<img src="' . esc_url($icon_raw) . '" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" />';
                } else {
                    // FontAwesome or other CSS class
                    $icon_output = '<i class="' . esc_attr($icon_raw) . '"></i>';
                }
            } else {
                $icon_output = '&#9679;';
            }

            $inline_style = '';
            $inline_style .= 'width: ' . esc_attr($icon_width) . 'px !important; ';
            $inline_style .= 'height: ' . esc_attr($icon_width) . 'px !important; ';
            if ($icon_bg_color !== 'transparent') {
                $inline_style .= 'background-color: ' . esc_attr($icon_bg_color) . ' !important; ';
            }
            if (isset($options['social_icon_color'])) {
                $inline_style .= 'color: ' . esc_attr($options['social_icon_color']) . ' !important; ';
            }
            if ($icon_border_color !== 'transparent' && $icon_border_size !== '0') {
                $inline_style .= 'border: ' . esc_attr($icon_border_size) . 'px solid ' . esc_attr($icon_border_color) . ' !important; ';
            }

            echo '<a class="social-link ' . $link_extra_classes . '" href="' . esc_url($s['url']) . '" target="_blank" rel="noopener noreferrer"' . ($inline_style ? ' style="' . $inline_style . '"' : '') . '>' . $icon_output . '</a>';
        }
        echo '</div>';
    }

    // Custom icon links (configured in admin)
    if (!empty($options['topbar_custom_icon_links']) && is_array($options['topbar_custom_icon_links'])) {
        // Debug output for admins
        if (current_user_can('manage_options')) {
            echo '<!-- DEBUG: Rendering ' . count($options['topbar_custom_icon_links']) . ' custom icons -->';
            foreach ($options['topbar_custom_icon_links'] as $idx => $c) {
                echo '<!-- DEBUG: Custom icon ' . $idx . ': ' . json_encode($c) . ' -->';
            }
        }
        $icon_color = isset($options['social_icon_color']) ? esc_attr($options['social_icon_color']) : $color;
        $icon_bg_color = isset($options['social_icon_bg_color']) ? $options['social_icon_bg_color'] : 'transparent';
        $icon_border_color = isset($options['social_icon_border_color']) ? $options['social_icon_border_color'] : 'transparent';
        $icon_border_size = isset($options['social_icon_border_size']) ? $options['social_icon_border_size'] : '0';
        $icon_width = isset($options['social_icon_width']) ? absint($options['social_icon_width']) : 32;
        $icon_size = isset($options['social_icon_size']) ? $options['social_icon_size'] : 'medium';
        $icon_shape = isset($options['social_icon_shape']) ? $options['social_icon_shape'] : 'circle';
        $icon_effect = isset($options['social_icon_effect']) ? $options['social_icon_effect'] : 'none';
        $custom_link_extra_classes = 'social-link--' . esc_attr($icon_size) . ' social-link--' . esc_attr($icon_shape);
        if ($icon_effect !== 'none') {
            $custom_link_extra_classes .= ' social-link--' . esc_attr($icon_effect);
        }
        
        echo '<div class="topbar-custom-icons">';
        foreach ($options['topbar_custom_icon_links'] as $c) {
            if (empty($c['url']) || empty($c['icon'])) continue;
            // Check if this custom icon is enabled
            if (isset($c['enabled']) && !$c['enabled']) continue;
            $title = isset($c['title']) ? esc_attr($c['title']) : '';
            $icon_raw = trim($c['icon']);
            
            // Handle different icon types
            if (strpos($icon_raw, '<') === 0) {
                // HTML icon (SVG, etc.)
                $icon_html = wp_kses($icon_raw, array('i' => array('class' => array()), 'svg' => array(), 'path' => array()));
            } elseif (preg_match('/\.(jpg|jpeg|png|gif|svg|webp)$/i', $icon_raw)) {
                // Uploaded image URL
                $icon_html = '<img src="' . esc_url($icon_raw) . '" alt="' . $title . '" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" />';
            } else {
                // FontAwesome or other CSS class
                $icon_html = '<i class="' . esc_attr($icon_raw) . '"></i>';
            }

            // Apply same styling as regular social icons
            $inline_style = '';
            $inline_style .= 'width: ' . esc_attr($icon_width) . 'px !important; ';
            $inline_style .= 'height: ' . esc_attr($icon_width) . 'px !important; ';
            if ($icon_bg_color !== 'transparent') {
                $inline_style .= 'background-color: ' . esc_attr($icon_bg_color) . ' !important; ';
            }
            $inline_style .= 'color: ' . esc_attr($icon_color) . ' !important; ';
            if ($icon_border_color !== 'transparent' && $icon_border_size !== '0') {
                $inline_style .= 'border: ' . esc_attr($icon_border_size) . 'px solid ' . esc_attr($icon_border_color) . ' !important; ';
            }

            echo '<a class="topbar-custom-icon social-link ' . $custom_link_extra_classes . '" href="' . esc_url($c['url']) . '" title="' . $title . '" style="' . $inline_style . '">' . $icon_html . '</a>';
        }
        echo '</div>';
    }

    echo '</div>'; // topbar-right

    echo '</div></div>';
}

/**
 * Render announcement strip markup (centralized)
 */
function ross_theme_render_announcement_strip($options = null) {
    if (is_null($options)) {
        $options = ross_theme_get_header_options();
    }

    if (empty($options['enable_announcement']) || empty($options['announcement_text'])) {
        return;
    }

    $announcement = $options['announcement_text'];
    $anim = isset($options['announcement_animation']) ? $options['announcement_animation'] : 'marquee';
    $bg = isset($options['announcement_bg_color']) ? sanitize_hex_color($options['announcement_bg_color']) : '#E5C902';
    $text_color = isset($options['announcement_text_color']) ? sanitize_hex_color($options['announcement_text_color']) : '#001946';
    $font_raw = isset($options['announcement_font_size']) ? trim($options['announcement_font_size']) : '14px';
    // Normalize font size: allow numeric (assume px) or valid CSS unit string
    if (is_numeric($font_raw)) {
        $font = $font_raw . 'px';
    } else {
        $font = $font_raw;
    }

    $anim_class = 'announce-' . esc_attr($anim);
    $position = isset($options['announcement_position']) ? $options['announcement_position'] : 'top_of_topbar';
    $extra_class = '';
    if (!empty($options['announcement_sticky'])) {
        $extra_class .= ' site-announcement--sticky';
    }

    // Position-specific helper class
    if ($position === 'top_of_topbar') {
        $extra_class .= ' site-announcement--top';
    } elseif ($position === 'below_topbar') {
        $extra_class .= ' site-announcement--between-topbar-header';
    } elseif ($position === 'below_header') {
        $extra_class .= ' site-announcement--below-header';
    }

    $inline_style = 'background:' . esc_attr($bg) . '; color:' . esc_attr($text_color) . '; font-size:' . esc_attr($font) . '; padding:6px 0;';
    if (!empty($options['announcement_sticky'])) {
        $offset = isset($options['announcement_sticky_offset']) ? absint($options['announcement_sticky_offset']) : 0;
        $inline_style .= ' --announcement-sticky-offset:' . $offset . 'px; position: sticky; top: ' . $offset . 'px; z-index: 9999;';
    }

    echo '<div class="site-announcement-strip' . $extra_class . '" style="' . $inline_style . '">';
    echo '<div class="announcement-text ' . $anim_class . '">' . wp_kses_post($announcement) . '</div>';
    echo '</div>';
}

/**
 * Helper to render announcement at specific layout locations
 * $location: before_topbar | between_topbar_header | below_header
 */
function ross_theme_render_announcement_at($location) {
    $options = ross_theme_get_header_options();

    if (empty($options['enable_announcement']) || empty($options['announcement_text'])) {
        return;
    }

    $position = isset($options['announcement_position']) ? $options['announcement_position'] : 'top_of_topbar';

    if (
        ($location === 'before_topbar' && $position === 'top_of_topbar') ||
        ($location === 'between_topbar_header' && $position === 'below_topbar') ||
        ($location === 'below_header' && $position === 'below_header')
    ) {
        ross_theme_render_announcement_strip($options);
    }
}

/**
 * Output dynamic CSS for top bar enhancements
 */
function ross_theme_topbar_dynamic_css() {
    $options = ross_theme_get_header_options();

    if (empty($options['enable_topbar'])) {
        return;
    }

    $text_color   = isset($options['topbar_text_color']) ? sanitize_hex_color($options['topbar_text_color']) : '#ffffff';
    $icon_color   = isset($options['topbar_icon_color']) ? sanitize_hex_color($options['topbar_icon_color']) : $text_color;
    $icon_hover   = isset($options['topbar_icon_hover_color']) ? sanitize_hex_color($options['topbar_icon_hover_color']) : $icon_color;
    $bg_color     = isset($options['topbar_bg_color']) ? sanitize_hex_color($options['topbar_bg_color']) : '#001946';
    $use_gradient = !empty($options['topbar_gradient_enable']);
    $grad1        = isset($options['topbar_gradient_color1']) ? sanitize_hex_color($options['topbar_gradient_color1']) : '#001946';
    $grad2        = isset($options['topbar_gradient_color2']) ? sanitize_hex_color($options['topbar_gradient_color2']) : '#003d7a';
    $shadow_enable = !empty($options['topbar_shadow_enable']);
    $border_width  = isset($options['topbar_border_width']) ? absint($options['topbar_border_width']) : 0;
    $border_color  = isset($options['topbar_border_color']) ? sanitize_hex_color($options['topbar_border_color']) : '#E5C902';
    $font_size     = isset($options['topbar_font_size']) ? absint($options['topbar_font_size']) : 14;
    $alignment     = isset($options['topbar_alignment']) ? esc_attr($options['topbar_alignment']) : 'left';
    $sticky        = !empty($options['sticky_topbar']);
    $sticky_offset = isset($options['topbar_sticky_offset']) ? absint($options['topbar_sticky_offset']) : 0;

    echo '<style id="ross-topbar-dynamic-css">';

    echo '.site-topbar {';
    if ($use_gradient) {
        echo 'background: linear-gradient(90deg, ' . $grad1 . ', ' . $grad2 . ');';
    } else {
        echo 'background-color: ' . $bg_color . ';';
    }
    echo 'color: ' . $text_color . ';';
    echo 'font-size: ' . $font_size . 'px;';
    echo 'text-align: ' . $alignment . ';';
    echo 'transition: all 0.3s ease;';
    if ($shadow_enable) {
        echo 'box-shadow: 0 2px 8px rgba(0,0,0,0.15);';
    }
    if ($border_width > 0) {
        echo 'border-bottom: ' . $border_width . 'px solid ' . $border_color . ';';
    }
    echo '}';

    if ($sticky) {
        echo '.site-topbar.topbar-sticky { position: sticky; top: ' . $sticky_offset . 'px; z-index: 9998; }';
    }

    echo '.site-topbar a, .topbar-left, .topbar-center, .topbar-right {';
    echo 'color: ' . $text_color . ';';
    echo '}';

    echo '.site-topbar .social-link, .site-topbar .topbar-phone, .site-topbar .topbar-custom-icon {';
    echo 'color: ' . $icon_color . ';';
    echo 'transition: all 0.2s ease;';
    echo '}';

    echo '.site-topbar .social-link:hover, .site-topbar .topbar-phone:hover, .site-topbar .topbar-custom-icon:hover {';
    echo 'color: ' . $icon_hover . ';';
    echo 'opacity: 0.8; transform: scale(1.1);';
    echo '}';

    echo '</style>';
}

/**
 * Adjust color brightness
 */
function adjust_brightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color = hexdec($color); // Convert to decimal
        $color = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

add_action('wp_head', 'ross_theme_topbar_dynamic_css', 999);