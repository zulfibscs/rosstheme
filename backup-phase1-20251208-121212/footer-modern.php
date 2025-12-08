<?php
/**
 * Footer Template Part - Modern Professional
 * Fully dynamic based on Ross Theme options
 * 
 * Usage: Place in template-parts/footer/footer-modern.php
 * 
 * This template uses:
 * - Ross Theme → Footer options
 * - Ross Theme → General options
 * - All settings are dynamic and customizable
 */

if (!defined('ABSPATH')) exit;

// Get theme options
$footer_options = get_option('ross_theme_footer_options', array());
$general_options = get_option('ross_theme_general_options', array());

// Footer styling from options
$footer_bg = $footer_options['styling_bg_color'] ?? '#1a1a1a';
$footer_text = $footer_options['text_color'] ?? '#ffffff';
$footer_link = $footer_options['link_color'] ?? '#E5C902';
$footer_heading = $footer_options['heading_color'] ?? '#ffffff';

// Layout options
$footer_columns = $footer_options['footer_columns'] ?? '4';
$footer_width = $footer_options['footer_width'] ?? 'contained';

// Widget areas
$enable_widgets = $footer_options['enable_widgets'] ?? 1;

// Social icons
$enable_social = ross_theme_should_show_social_icons();

// Copyright options
$show_copyright = ross_theme_should_show_copyright();
$copyright_text = $footer_options['copyright_text'] ?? '&copy; {year} {site_name}. All rights reserved.';
$copyright_bg = $footer_options['copyright_bg_color'] ?? '#111111';

// Replace placeholders
$copyright_text = str_replace('{year}', date('Y'), $copyright_text);
$copyright_text = str_replace('{site_name}', get_bloginfo('name'), $copyright_text);

// Background gradient
$use_gradient = $footer_options['styling_bg_gradient'] ?? 0;
$gradient_from = $footer_options['styling_bg_gradient_from'] ?? '#1a1a1a';
$gradient_to = $footer_options['styling_bg_gradient_to'] ?? '#2a2a2a';

// Calculate background
$footer_background = $use_gradient 
    ? "linear-gradient(135deg, {$gradient_from} 0%, {$gradient_to} 100%)"
    : $footer_bg;

// Background image
$bg_image = $footer_options['styling_bg_image'] ?? '';
$bg_style = $bg_image ? "background-image: url('" . esc_url($bg_image) . "'); background-size: cover; background-position: center;" : '';
?>

<footer class="site-footer ross-footer-modern" 
        style="background: <?php echo esc_attr($footer_background); ?>; color: <?php echo esc_attr($footer_text); ?>; <?php echo $bg_style; ?>">
    
    <?php if ($bg_image): ?>
    <div class="ross-footer-overlay"></div>
    <?php endif; ?>
    
    <div class="ross-footer-container <?php echo $footer_width === 'contained' ? 'container' : 'container-fluid'; ?>">
        
        <?php if ($enable_widgets && is_active_sidebar('footer-1')): ?>
        <!-- Footer Widgets -->
        <div class="ross-footer-widgets">
            <div class="ross-footer-columns ross-footer-columns-<?php echo esc_attr($footer_columns); ?>">
                
                <?php
                // Dynamic widget areas based on column count
                for ($i = 1; $i <= intval($footer_columns); $i++):
                    if (is_active_sidebar('footer-' . $i)):
                ?>
                <div class="ross-footer-column">
                    <?php dynamic_sidebar('footer-' . $i); ?>
                </div>
                <?php
                    endif;
                endfor;
                ?>
                
            </div>
        </div>
        <?php endif; ?>
        
        <?php if ($enable_social): ?>
        <!-- Social Icons -->
        <div class="ross-footer-social">
            <?php
            // Unified renderer: respects enable/disable toggles, custom icon, size, shape, colors
            if (function_exists('ross_footer_social_icons')) {
                ross_footer_social_icons();
            }
            ?>
        </div>
        <?php endif; ?>
        
    </div>
    
</footer>

<?php if ($show_copyright): ?>
<!-- Copyright Bar -->
<div class="ross-copyright-bar" style="background-color: <?php echo esc_attr($copyright_bg); ?>; color: <?php echo esc_attr($footer_text); ?>;">
    <div class="ross-copyright-container container">
        <div class="ross-copyright-content">
            <p class="ross-copyright-text"><?php echo wp_kses_post($copyright_text); ?></p>
            
            <?php if (has_nav_menu('footer')): ?>
            <nav class="ross-footer-menu" aria-label="Footer Navigation">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu-links',
                    'container'      => false,
                    'depth'          => 1,
                ));
                ?>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
/* Dynamic Footer Styles - Responsive */
.ross-footer-modern {
    position: relative;
    padding: 60px 0 40px;
    overflow: hidden;
}

.ross-footer-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 0;
}

.ross-footer-container {
    position: relative;
    z-index: 1;
    max-width: <?php echo esc_attr($general_options['container_width'] ?? '1200'); ?>px;
    margin: 0 auto;
    padding: 0 20px;
}

.container-fluid {
    max-width: 100%;
}

.ross-footer-widgets {
    margin-bottom: 40px;
}

.ross-footer-columns {
    display: grid;
    gap: 40px;
}

.ross-footer-columns-1 {
    grid-template-columns: 1fr;
}

.ross-footer-columns-2 {
    grid-template-columns: repeat(2, 1fr);
}

.ross-footer-columns-3 {
    grid-template-columns: repeat(3, 1fr);
}

.ross-footer-columns-4 {
    grid-template-columns: repeat(4, 1fr);
}

.ross-footer-column h2,
.ross-footer-column h3,
.ross-footer-column .widget-title {
    color: <?php echo esc_attr($footer_heading); ?>;
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ross-footer-column ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.ross-footer-column ul li {
    margin-bottom: 12px;
}

.ross-footer-column a {
    color: <?php echo esc_attr($footer_text); ?>;
    text-decoration: none;
    transition: color 0.3s ease;
    opacity: 0.9;
}

.ross-footer-column a:hover {
    color: <?php echo esc_attr($footer_link); ?>;
    opacity: 1;
}

.ross-footer-social {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    padding: 30px 0 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.ross-social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 20px;
}

.ross-social-link:hover {
    background: <?php echo esc_attr($footer_link); ?>;
    color: #000 !important;
    transform: translateY(-3px);
}

.ross-copyright-bar {
    padding: 20px 0;
    font-size: 14px;
}

.ross-copyright-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.ross-copyright-text {
    margin: 0;
    opacity: 0.9;
}

.footer-menu-links {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 20px;
}

.footer-menu-links a {
    color: <?php echo esc_attr($footer_text); ?>;
    text-decoration: none;
    opacity: 0.9;
    transition: all 0.3s ease;
}

.footer-menu-links a:hover {
    color: <?php echo esc_attr($footer_link); ?>;
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .ross-footer-columns-4 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .ross-footer-columns-3 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .ross-footer-modern {
        padding: 40px 0 30px;
    }
    
    .ross-footer-columns-2,
    .ross-footer-columns-3,
    .ross-footer-columns-4 {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .ross-footer-social {
        gap: 15px;
        padding: 20px 0;
    }
    
    .ross-social-link {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .ross-copyright-content {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .footer-menu-links {
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
}

@media (max-width: 480px) {
    .ross-footer-modern {
        padding: 30px 0 20px;
    }
    
    .ross-footer-container {
        padding: 0 15px;
    }
    
    .ross-footer-column h2,
    .ross-footer-column h3,
    .ross-footer-column .widget-title {
        font-size: 16px;
        margin-bottom: 15px;
    }
    
    .ross-copyright-bar {
        padding: 15px 0;
        font-size: 13px;
    }
}

/* Widget Styles */
.ross-footer-column .widget {
    margin-bottom: 0;
}

.ross-footer-column .widget:not(:last-child) {
    margin-bottom: 30px;
}

.ross-footer-column p {
    line-height: 1.7;
    margin: 0 0 15px;
    opacity: 0.9;
}

.ross-footer-column form {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.ross-footer-column input[type="email"],
.ross-footer-column input[type="text"] {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.1);
    color: <?php echo esc_attr($footer_text); ?>;
    border-radius: 4px;
}

.ross-footer-column button,
.ross-footer-column input[type="submit"] {
    padding: 10px 20px;
    background: <?php echo esc_attr($footer_link); ?>;
    color: #000;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.ross-footer-column button:hover,
.ross-footer-column input[type="submit"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}
</style>
