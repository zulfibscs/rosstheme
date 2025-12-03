<?php
/**
 * Footer Functions Module
 * Handles footer display logic based on options
 */

function ross_theme_get_footer_layout() {
    // Footer style option removed; always use the default layout.
    return 'default';
}

function ross_theme_display_footer() {
    $footer_options = get_option('ross_theme_footer_options', array());
    
    // Get selected template
    $template_id = $footer_options['footer_template'] ?? 'creative-agency';
    
    // Check if template part exists
    $template_part = 'template-parts/footer/footer-' . $template_id;
    $template_file = locate_template($template_part . '.php');
    
    if ($template_file) {
        // Use specific template
        get_template_part('template-parts/footer/footer', $template_id);
    } else {
        // Fallback to default
        get_template_part('template-parts/footer/footer-default');
    }
}


/**
 * Render custom footer HTML when enabled
 */
function ross_theme_render_custom_footer() {
    $footer_options = get_option('ross_theme_footer_options');
    if (empty($footer_options) || empty($footer_options['enable_custom_footer'])) {
        return;
    }

    if (!empty($footer_options['custom_footer_html'])) {
        echo '<div class="site-footer-custom">' . $footer_options['custom_footer_html'] . '</div>';
    }
}

function ross_theme_should_show_footer_widgets() {
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_widgets']) ? $footer_options['enable_widgets'] : true;
}

function ross_theme_should_show_footer_cta() {
    $footer_options = get_option('ross_theme_footer_options');
    // Developer override: allow defining ROSS_DISABLE_CTA to globally disable CTA
    if (defined('ROSS_DISABLE_CTA') && ROSS_DISABLE_CTA) {
        return false;
    }
    // Always-visible override
    if (!empty($footer_options['cta_always_visible'])) {
        return true;
    }

    // Respect the enable toggle; fall back to false
    $enabled = isset($footer_options['enable_footer_cta']) ? (bool) $footer_options['enable_footer_cta'] : false;
    if (!$enabled) return false;

    // If enabled, optionally check 'cta_display_on' for template-specific visibility
    if (!empty($footer_options['cta_display_on']) && is_array($footer_options['cta_display_on'])) {
        $visible_on = $footer_options['cta_display_on'];
        // If 'all' selected, show everywhere
        if (in_array('all', $visible_on, true)) return true;
        // Front page
        if (in_array('front', $visible_on, true) && is_front_page()) return true;
        if (in_array('home', $visible_on, true) && is_home()) return true;
        if (in_array('single', $visible_on, true) && is_single()) return true;
        if (in_array('page', $visible_on, true) && is_page()) return true;
        if (in_array('archive', $visible_on, true) && is_archive()) return true;

        // No matching visibility: do not show.
        return false;
    }

    return $enabled;
}

function ross_theme_should_show_social_icons() {
    // Prefer Customizer theme_mod if present
    $theme_mod = get_theme_mod( 'footer_social_enable', null );
    if ( null !== $theme_mod ) {
        return (bool) $theme_mod;
    }
    $footer_options = get_option('ross_theme_footer_options');
    return isset($footer_options['enable_social_icons']) ? (bool) $footer_options['enable_social_icons'] : true;
}

function ross_theme_should_show_copyright() {
    $footer_options = get_option('ross_theme_footer_options');
    // If a custom footer is enabled, avoid showing the default copyright block
    if (!empty($footer_options['enable_custom_footer'])) {
        return false;
    }
    return isset($footer_options['enable_copyright']) ? (bool) $footer_options['enable_copyright'] : true;
}

function ross_theme_get_copyright_text() {
    $footer_options = get_option('ross_theme_footer_options');
    $text = isset($footer_options['copyright_text']) ? $footer_options['copyright_text'] : '© {year} {site_name}. All rights reserved.';
    // Replace placeholders
    $placeholders = array('{year}', '{site_name}');
    $replacements = array(date('Y'), get_bloginfo('name'));
    $text = str_replace($placeholders, $replacements, $text);
    // Allow HTML via kses_post but with placeholders replaced
    return wp_kses_post($text);
}

/**
 * Render footer template content from template file
 * Converts template array structure into HTML columns
 * 
 * @param string $template_id Template identifier (business-professional, ecommerce, etc.)
 * @param int $max_columns Maximum number of columns to display (respects user's column setting)
 * @return void
 */
function ross_theme_render_template_content($template_id = '', $max_columns = 0) {
    $footer_options = get_option('ross_theme_footer_options', array());
    
    // Use stored template ID if not provided
    if (empty($template_id)) {
        $template_id = isset($footer_options['footer_template']) ? $footer_options['footer_template'] : 'business-professional';
    }
    
    // Legacy template ID migration
    $legacy_map = array(
        'template1' => 'business-professional',
        'template2' => 'ecommerce',
        'template3' => 'creative-agency',
        'template4' => 'minimal-modern'
    );
    
    if (isset($legacy_map[$template_id])) {
        $template_id = $legacy_map[$template_id];
    }
    
    // Load template file
    $template_file = get_template_directory() . '/inc/features/footer/templates/' . $template_id . '.php';
    
    if (!file_exists($template_file)) {
        return;
    }
    
    $template_data = include $template_file;
    
    if (!is_array($template_data) || empty($template_data['cols'])) {
        return;
    }
    
    $columns = $template_data['cols'];
    
    // Limit columns based on user's selection
    if ($max_columns > 0 && $max_columns < count($columns)) {
        $columns = array_slice($columns, 0, $max_columns);
    }
    
    $num_columns = count($columns);
    
    // Determine grid class based on column count
    $grid_class = 'footer-grid-' . $num_columns;
    
    echo '<div class="footer-template-content ' . esc_attr($grid_class) . '">';
    
    foreach ($columns as $column) {
        echo '<div class="footer-column">';
        
        // Column title
        if (!empty($column['title'])) {
            echo '<h4 class="footer-column-title">' . esc_html($column['title']) . '</h4>';
        }
        
        // Column items (links)
        if (!empty($column['items']) && is_array($column['items'])) {
            echo '<ul class="footer-column-links">';
            foreach ($column['items'] as $item) {
                if (is_array($item)) {
                    $label = isset($item['label']) ? $item['label'] : '';
                    $url = isset($item['url']) ? $item['url'] : '#';
                    echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
                } else {
                    // Simple string item
                    echo '<li>' . esc_html($item) . '</li>';
                }
            }
            echo '</ul>';
        }
        
        // Custom HTML content
        if (!empty($column['html'])) {
            echo '<div class="footer-column-html">' . wp_kses_post($column['html']) . '</div>';
        }
        
        // Social icons
        if (!empty($column['social']) && is_array($column['social'])) {
            echo '<div class="footer-column-social">';
            foreach ($column['social'] as $platform => $url) {
                if (!empty($url)) {
                    $icon_class = 'fab fa-' . esc_attr($platform);
                    echo '<a href="' . esc_url($url) . '" class="social-icon" target="_blank" rel="noopener noreferrer">';
                    echo '<i class="' . esc_attr($icon_class) . '"></i>';
                    echo '</a>';
                }
            }
            echo '</div>';
        }
        
        echo '</div>'; // .footer-column
    }
    
    echo '</div>'; // .footer-template-content
}

/**
 * Register footer widget areas (footer-1 .. footer-4)
 */
function ross_theme_register_footer_sidebars() {
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name' => sprintf(__('Footer Column %d', 'rosstheme'), $i),
            'id' => 'footer-' . $i,
            'description' => sprintf(__('Widgets for footer column %d', 'rosstheme'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
        ));
    }
}
add_action('widgets_init', 'ross_theme_register_footer_sidebars');

/**
 * Display the optional footer CTA — placed OUTSIDE the <footer> element.
 * This allows CTA to be shown above the footer rather than inside it.
 */
function ross_theme_display_footer_cta() {
    if (!function_exists('ross_theme_should_show_footer_cta') || !ross_theme_should_show_footer_cta()) {
        return;
    }
    // Use get_template_part so developers can override markup in child themes
    get_template_part('template-parts/footer/footer-cta');
}

/**
 * Display the footer copyright bar
 */
function ross_theme_display_footer_copyright() {
    get_template_part('template-parts/footer/footer-copyright');
}