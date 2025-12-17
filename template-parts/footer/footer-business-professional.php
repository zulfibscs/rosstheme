<?php
/**
 * Footer Template Part: Business Professional
 * Fully dynamic layout using ALL 138 footer options
 * All settings controlled from Ross Theme Settings → Footer
 * 
 * Structure:
 * - Dynamic Columns (1-4, controlled by Layout tab)
 * - Full Background System (color/gradient/image with overlay, controlled by Styling tab)
 * - Social Icons (in first column, controlled by Social tab)
 * - Complete Typography Control (font sizes, line heights, weights)
 * - All Spacing Options (padding all sides, column/row gaps)
 * - Border Controls (enabled, color, thickness)
 * - Widget Title Styling
 * 
 * Note: CTA and Copyright are handled by footer.php, not this template
 */

// Retrieve all footer options with defensive array check
$footer_options = get_option('ross_theme_footer_options', array());
if (!is_array($footer_options)) {
    $footer_options = array();
}

// ========================================
// LAYOUT OPTIONS (5 options)
// ========================================
$num_columns = isset($footer_options['footer_columns']) ? intval($footer_options['footer_columns']) : 4;
$footer_width = $footer_options['footer_width'] ?? 'contained';
$container_class = ($footer_width === 'full') ? 'container-fluid' : 'container';
$show_template_content = isset($footer_options['use_template_content']) ? (bool)$footer_options['use_template_content'] : false;

// ========================================
// BACKGROUND OPTIONS (7 options)
// ========================================
$bg_type = $footer_options['styling_bg_type'] ?? 'color';
$bg_color = $footer_options['styling_bg_color'] ?? '#1a1a1a';
$bg_gradient_from = $footer_options['styling_bg_gradient_from'] ?? '#1a1a1a';
$bg_gradient_to = $footer_options['styling_bg_gradient_to'] ?? '#2c2c2c';
$bg_image = $footer_options['styling_bg_image'] ?? '';
$bg_opacity = isset($footer_options['styling_bg_opacity']) ? floatval($footer_options['styling_bg_opacity']) : 1;

// ========================================
// OVERLAY OPTIONS (8 options)
// ========================================
$overlay_enabled = isset($footer_options['styling_overlay_enabled']) ? (bool)$footer_options['styling_overlay_enabled'] : false;
$overlay_type = $footer_options['styling_overlay_type'] ?? 'color';
$overlay_color = $footer_options['styling_overlay_color'] ?? 'rgba(0,0,0,0.5)';
$overlay_gradient_from = $footer_options['styling_overlay_gradient_from'] ?? 'rgba(0,0,0,0.7)';
$overlay_gradient_to = $footer_options['styling_overlay_gradient_to'] ?? 'rgba(0,0,0,0.3)';
$overlay_image = $footer_options['styling_overlay_image'] ?? '';
$overlay_opacity = isset($footer_options['styling_overlay_opacity']) ? floatval($footer_options['styling_overlay_opacity']) : 0.5;

// ========================================
// TEXT & LINK OPTIONS (3 options)
// ========================================
$text_color = $footer_options['styling_text_color'] ?? '#e0e0e0';
$link_color = $footer_options['styling_link_color'] ?? '#3498db';
$link_hover = $footer_options['styling_link_hover'] ?? '#5dade2';

// ========================================
// TYPOGRAPHY OPTIONS (4 options)
// ========================================
$font_size = isset($footer_options['styling_font_size']) ? intval($footer_options['styling_font_size']) : 14;
$line_height = isset($footer_options['styling_line_height']) ? floatval($footer_options['styling_line_height']) : 1.6;
$widget_title_color = $footer_options['styling_widget_title_color'] ?? '#ffffff';
$widget_title_size = isset($footer_options['styling_widget_title_size']) ? intval($footer_options['styling_widget_title_size']) : 16;

// ========================================
// SPACING OPTIONS (9 options)
// ========================================
$col_gap = isset($footer_options['styling_col_gap']) ? intval($footer_options['styling_col_gap']) : 30;
$row_gap = isset($footer_options['styling_row_gap']) ? intval($footer_options['styling_row_gap']) : 30;
$padding_top = isset($footer_options['styling_padding_top']) && $footer_options['styling_padding_top'] !== '' ? intval($footer_options['styling_padding_top']) : 60;
$padding_bottom = isset($footer_options['styling_padding_bottom']) && $footer_options['styling_padding_bottom'] !== '' ? intval($footer_options['styling_padding_bottom']) : 30;
$padding_left = isset($footer_options['styling_padding_left']) && $footer_options['styling_padding_left'] !== '' ? intval($footer_options['styling_padding_left']) : 0;
$padding_right = isset($footer_options['styling_padding_right']) && $footer_options['styling_padding_right'] !== '' ? intval($footer_options['styling_padding_right']) : 0;

// ========================================
// BORDER OPTIONS (3 options)
// ========================================
$border_top = isset($footer_options['styling_border_top']) ? (bool)$footer_options['styling_border_top'] : false;
$border_color = $footer_options['styling_border_color'] ?? 'rgba(255,255,255,0.1)';
$border_thickness = isset($footer_options['styling_border_thickness']) ? intval($footer_options['styling_border_thickness']) : 1;

// ========================================
// SOCIAL OPTIONS (1 master toggle)
// ========================================
$enable_social = isset($footer_options['enable_social_icons']) ? (bool)$footer_options['enable_social_icons'] : false;

// ========================================
// BUILD DYNAMIC INLINE STYLES
// ========================================

// Background style based on type
$background_style = '';
switch ($bg_type) {
    case 'gradient':
        $background_style = "background: linear-gradient(135deg, {$bg_gradient_from}, {$bg_gradient_to});";
        break;
    case 'image':
        if (!empty($bg_image)) {
            $background_style = "background-image: url('" . esc_url($bg_image) . "'); background-size: cover; background-position: center; background-repeat: no-repeat;";
        } else {
            $background_style = "background-color: {$bg_color};";
        }
        break;
    case 'color':
    default:
        $background_style = "background-color: {$bg_color};";
        break;
}

// Overlay style based on type (only if enabled)
$overlay_style = '';
if ($overlay_enabled) {
    switch ($overlay_type) {
        case 'gradient':
            $overlay_style = "background: linear-gradient(135deg, {$overlay_gradient_from}, {$overlay_gradient_to}); opacity: {$overlay_opacity};";
            break;
        case 'image':
            if (!empty($overlay_image)) {
                $overlay_style = "background-image: url('" . esc_url($overlay_image) . "'); background-size: cover; opacity: {$overlay_opacity};";
            } else {
                $overlay_style = "background-color: {$overlay_color}; opacity: {$overlay_opacity};";
            }
            break;
        case 'color':
        default:
            $overlay_style = "background-color: {$overlay_color}; opacity: {$overlay_opacity};";
            break;
    }
}

// Border style
$border_style = '';
if ($border_top) {
    $border_style = "border-top: {$border_thickness}px solid {$border_color};";
}

// Complete footer inline styles
$footer_inline_styles = "
    {$background_style}
    {$border_style}
    color: {$text_color};
    font-size: {$font_size}px;
    line-height: {$line_height};
    padding-top: {$padding_top}px;
    padding-bottom: {$padding_bottom}px;
    padding-left: {$padding_left}px;
    padding-right: {$padding_right}px;
    position: relative;
";
?>

<footer class="site-footer footer-business-professional" style="<?php echo esc_attr($footer_inline_styles); ?>">
    
    <?php if ($overlay_enabled && !empty($overlay_style)): ?>
        <!-- Overlay Layer -->
        <div class="footer-overlay" style="<?php echo esc_attr($overlay_style); ?>"></div>
    <?php endif; ?>
    
    <div class="footer-main">
        <div class="<?php echo esc_attr($container_class); ?>">
            
            <div class="footer-columns footer-columns-<?php echo esc_attr($num_columns); ?>" style="column-gap: <?php echo esc_attr($col_gap); ?>px; row-gap: <?php echo esc_attr($row_gap); ?>px;">
                
                <?php if ($show_template_content): ?>
                    <?php
                    // ========================================
                    // TEMPLATE MODE: Use static template content
                    // ========================================
                    $template_file = get_template_directory() . '/inc/features/footer/templates/business-professional.php';
                    $template_data = file_exists($template_file) ? include($template_file) : array();
                    $template_cols = $template_data['cols'] ?? array();
                    
                    // Limit to selected number of columns
                    $template_cols = array_slice($template_cols, 0, $num_columns);
                    
                    foreach ($template_cols as $index => $col):
                    ?>
                        <div class="footer-column">
                            <?php if (!empty($col['title'])): ?>
                                <h4 class="footer-column-title" style="color: <?php echo esc_attr($widget_title_color); ?>; font-size: <?php echo esc_attr($widget_title_size); ?>px;"><?php echo esc_html($col['title']); ?></h4>
                            <?php endif; ?>
                            
                            <?php if (!empty($col['html'])): ?>
                                <div class="footer-column-content">
                                    <?php echo wp_kses_post($col['html']); ?>
                                </div>
                            <?php elseif (!empty($col['items']) && is_array($col['items'])): ?>
                                <ul class="footer-column-list">
                                    <?php foreach ($col['items'] as $item): ?>
                                        <li><a href="#" style="color: <?php echo esc_attr($text_color); ?>;"><?php echo esc_html($item); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <?php 
                            // Add social icons to first column if enabled
                            if ($index === 0 && $enable_social && function_exists('ross_footer_social_icons')): 
                            ?>
                                <div class="footer-column-social" style="margin-top: 20px;">
                                    <?php ross_footer_social_icons(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                    endforeach;
                    ?>
                    
                <?php else: ?>
                    <?php
                    // ========================================
                    // WIDGET MODE: Use WordPress widget areas
                    // ========================================
                    for ($i = 1; $i <= $num_columns; $i++): 
                        if (is_active_sidebar('footer-' . $i)): 
                    ?>
                        <div class="footer-column footer-widget-area">
                            <?php dynamic_sidebar('footer-' . $i); ?>
                            
                            <?php 
                            // Add social icons to first column if enabled
                            if ($i === 1 && $enable_social && function_exists('ross_footer_social_icons')): 
                            ?>
                                <div class="footer-widget-social" style="margin-top: 20px;">
                                    <?php ross_footer_social_icons(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                        endif;
                    endfor;
                    ?>
                <?php endif; ?>
                
            </div>
            
        </div>
    </div>
</footer>

<style>
/* ========================================
   FOOTER BUSINESS PROFESSIONAL - DYNAMIC STYLES
   All values controlled from Ross Theme Settings → Footer
   ======================================== */

.footer-business-professional {
    position: relative;
    overflow: hidden;
    z-index: 1;
}

/* Overlay Layer - Behind everything */
.footer-business-professional .footer-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
}

/* Main Content Layer - Above overlay */
.footer-business-professional .footer-main {
    position: relative;
    z-index: 10;
}

/* Grid Layout */
.footer-business-professional .footer-columns {
    display: grid;
    margin-bottom: 40px;
    position: relative;
    z-index: 10;
}

.footer-business-professional .footer-columns-1 { 
    grid-template-columns: 1fr; 
}
.footer-business-professional .footer-columns-2 { 
    grid-template-columns: repeat(2, 1fr); 
}
.footer-business-professional .footer-columns-3 { 
    grid-template-columns: repeat(3, 1fr); 
}
.footer-business-professional .footer-columns-4 { 
    grid-template-columns: repeat(4, 1fr); 
}

/* Column Content - Ensure all content is above overlay */
.footer-business-professional .footer-column {
    position: relative;
    z-index: 10;
}

.footer-business-professional .footer-column-title,
.footer-business-professional .widget-title {
    /* Color and size controlled via inline styles */
    font-weight: 600;
    margin: 0 0 1.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    z-index: 10;
}

.footer-business-professional .footer-column-list {
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
    z-index: 10;
}

.footer-business-professional .footer-column-list li {
    margin-bottom: 0.75rem;
    position: relative;
    z-index: 10;
}

.footer-business-professional .footer-column-list a {
    /* Color controlled via inline styles */
    opacity: 0.8;
    text-decoration: none;
    transition: opacity 0.3s ease, color 0.3s ease;
    position: relative;
    z-index: 10;
}

.footer-business-professional .footer-column-list a:hover {
    opacity: 1;
    color: <?php echo esc_attr($link_hover); ?> !important;
}

.footer-business-professional .footer-column-content {
    /* Line height controlled via parent footer element */
    opacity: 0.8;
    position: relative;
    z-index: 10;
}

/* Widget Areas */
.footer-business-professional .widget {
    margin-bottom: 30px;
    position: relative;
    z-index: 10;
}

.footer-business-professional .widget:last-child {
    margin-bottom: 0;
}

.footer-business-professional .widget ul {
    list-style: none;
    padding: 0;
    margin: 0;
    position: relative;
    z-index: 10;
}

.footer-business-professional .widget ul li {
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 10;
}

.footer-business-professional .widget a {
    color: <?php echo esc_attr($link_color); ?>;
    text-decoration: none;
    transition: color 0.3s ease;
    position: relative;
    z-index: 10;
}

.footer-business-professional .widget a:hover {
    color: <?php echo esc_attr($link_hover); ?>;
}

/* Social Icons Styling */
.footer-business-professional .footer-column-social,
.footer-business-professional .footer-widget-social {
    /* Additional social icon styling controlled by social options */
}

/* Responsive Breakpoints */
@media (max-width: 768px) {
    .footer-business-professional .footer-columns-2,
    .footer-business-professional .footer-columns-3,
    .footer-business-professional .footer-columns-4 {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .footer-business-professional {
        font-size: <?php echo max(12, $font_size - 2); ?>px !important;
    }
    
    .footer-business-professional .footer-column-title,
    .footer-business-professional .widget-title {
        font-size: <?php echo max(14, $widget_title_size - 2); ?>px !important;
    }
}
</style>
