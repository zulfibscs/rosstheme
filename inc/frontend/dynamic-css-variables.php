<?php
/**
 * Dynamic CSS Variables from Theme Options
 * 
 * Place in: inc/frontend/dynamic-css-variables.php
 * Load in functions.php
 * 
 * This generates CSS custom properties from Ross Theme options
 * so all templates can use consistent, dynamic styling
 */

if (!defined('ABSPATH')) exit;

/**
 * Output dynamic CSS variables in <head>
 */
function ross_theme_output_css_variables() {
    // Get all theme options
    $header_options = function_exists('ross_theme_get_header_options') ? ross_theme_get_header_options() : array();
    $footer_options = get_option('ross_theme_footer_options', array());
    $general_options = get_option('ross_theme_general_options', array());
    
    // Extract values with fallbacks
    $primary_color = $general_options['primary_color'] ?? '#001946';
    $secondary_color = $general_options['secondary_color'] ?? '#E5C902';
    $accent_color = $general_options['accent_color'] ?? '#0073aa';
    $text_color = $general_options['text_color'] ?? '#333333';
    $bg_color = $general_options['background_color'] ?? '#ffffff';
    $heading_color = $general_options['heading_color'] ?? '#1d2327';
    
    $container_width = $general_options['container_width'] ?? '1200';
    $content_spacing = $general_options['content_spacing'] ?? '40';
    $border_radius = $general_options['global_border_radius'] ?? '6';
    
    $header_bg = $header_options['header_bg_color'] ?? '#ffffff';
    $header_text = $header_options['header_text_color'] ?? '#333333';
    $header_hover = $header_options['header_link_hover_color'] ?? '#E5C902';
    
    $footer_bg = $footer_options['styling_bg_color'] ?? '#1a1a1a';
    $footer_text = $footer_options['text_color'] ?? '#ffffff';
    $footer_link = $footer_options['link_color'] ?? '#E5C902';
    $footer_heading = $footer_options['heading_color'] ?? '#ffffff';
    
    // Body font
    $body_font = $general_options['body_font_family'] ?? "'Helvetica Neue', Arial, sans-serif";
    $heading_font = $general_options['heading_font_family'] ?? "'Georgia', serif";
    $body_font_size = $general_options['body_font_size'] ?? '16';
    
    ?>
    <style id="ross-theme-css-variables">
    :root {
        /* Colors - General */
        --ross-primary-color: <?php echo esc_attr($primary_color); ?>;
        --ross-secondary-color: <?php echo esc_attr($secondary_color); ?>;
        --ross-accent-color: <?php echo esc_attr($accent_color); ?>;
        --ross-text-color: <?php echo esc_attr($text_color); ?>;
        --ross-background-color: <?php echo esc_attr($bg_color); ?>;
        --ross-heading-color: <?php echo esc_attr($heading_color); ?>;
        
        /* Header Colors */
        --ross-header-bg: <?php echo esc_attr($header_bg); ?>;
        --ross-header-text: <?php echo esc_attr($header_text); ?>;
        --ross-header-hover: <?php echo esc_attr($header_hover); ?>;
        
        /* Footer Colors */
        --ross-footer-bg: <?php echo esc_attr($footer_bg); ?>;
        --ross-footer-text: <?php echo esc_attr($footer_text); ?>;
        --ross-footer-link: <?php echo esc_attr($footer_link); ?>;
        --ross-footer-heading: <?php echo esc_attr($footer_heading); ?>;
        
        /* Layout */
        --ross-container-width: <?php echo esc_attr($container_width); ?>px;
        --ross-content-spacing: <?php echo esc_attr($content_spacing); ?>px;
        --ross-border-radius: <?php echo esc_attr($border_radius); ?>px;
        
        /* Typography */
        --ross-body-font: <?php echo esc_attr($body_font); ?>;
        --ross-heading-font: <?php echo esc_attr($heading_font); ?>;
        --ross-body-font-size: <?php echo esc_attr($body_font_size); ?>px;
        
        /* Transitions */
        --ross-transition-fast: 0.2s ease;
        --ross-transition-normal: 0.3s ease;
        --ross-transition-slow: 0.5s ease;
        
        /* Shadows */
        --ross-shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
        --ross-shadow-md: 0 4px 12px rgba(0,0,0,0.1);
        --ross-shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
        
        /* Spacing Scale */
        --ross-space-xs: 8px;
        --ross-space-sm: 16px;
        --ross-space-md: 24px;
        --ross-space-lg: 40px;
        --ross-space-xl: 60px;
    }
    
    /* Apply theme fonts to body and headings */
    body {
        font-family: var(--ross-body-font);
        font-size: var(--ross-body-font-size);
        color: var(--ross-text-color);
        background-color: var(--ross-background-color);
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--ross-heading-font);
        color: var(--ross-heading-color);
    }
    
    /* Container utility using theme width */
    .ross-container,
    .container {
        max-width: var(--ross-container-width);
        margin-left: auto;
        margin-right: auto;
        padding-left: 20px;
        padding-right: 20px;
    }
    
    /* Button styles using theme colors */
    .ross-btn,
    .wp-block-button__link {
        background-color: var(--ross-secondary-color);
        color: var(--ross-primary-color);
        border-radius: var(--ross-border-radius);
        transition: var(--ross-transition-normal);
    }
    
    .ross-btn:hover,
    .wp-block-button__link:hover {
        transform: translateY(-2px);
        box-shadow: var(--ross-shadow-md);
    }
    
    /* Link colors */
    a {
        color: var(--ross-primary-color);
        transition: var(--ross-transition-fast);
    }
    
    a:hover {
        color: var(--ross-secondary-color);
    }
    
    /* Responsive container padding */
    @media (max-width: 768px) {
        .ross-container,
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'ross_theme_output_css_variables', 5); // Load early

/**
 * Add CSS variables to admin customizer preview
 */
function ross_theme_customizer_css_variables() {
    if (is_customize_preview()) {
        ross_theme_output_css_variables();
    }
}
add_action('customize_preview_init', 'ross_theme_customizer_css_variables');

/**
 * Utility function to get a specific CSS variable value
 */
function ross_get_css_var($var_name) {
    $vars = array(
        'primary-color' => get_option('ross_theme_general_options')['primary_color'] ?? '#001946',
        'secondary-color' => get_option('ross_theme_general_options')['secondary_color'] ?? '#E5C902',
        'container-width' => get_option('ross_theme_general_options')['container_width'] ?? '1200',
        // Add more as needed
    );
    
    return $vars[$var_name] ?? null;
}
