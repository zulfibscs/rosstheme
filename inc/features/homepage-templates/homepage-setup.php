<?php
/**
 * Homepage Setup & Activation
 * Handles homepage creation and theme activation tasks
 * 
 * @package RossTheme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Create homepage on theme activation
 */
function ross_theme_setup_homepage() {
    $page_on_front = get_option('page_on_front');
    
    // Only create if no homepage is set
    if (!$page_on_front) {
        // Create homepage
        $page_id = wp_insert_post(array(
            'post_title' => 'Home',
            'post_name' => 'home',
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => '<!-- Ross Theme Homepage - Select a template from Ross Theme → Homepage Templates -->'
        ));
        
        if (!is_wp_error($page_id)) {
            // Set as front page
            update_option('show_on_front', 'page');
            update_option('page_on_front', $page_id);
            
            // Set activation flag for admin notice
            set_transient('ross_theme_activated', true, 60);
        }
    }
}
add_action('after_switch_theme', 'ross_theme_setup_homepage');

/**
 * Show activation notice with template selection instructions
 */
function ross_theme_activation_notice() {
    if (get_transient('ross_theme_activated')) {
        delete_transient('ross_theme_activated');
        ?>
        <div class="notice notice-success is-dismissible ross-activation-notice">
            <h2 style="margin-top: 10px;">🎉 Welcome to Ross Theme!</h2>
            <p style="font-size: 14px; margin-bottom: 15px;">
                <strong>Your theme is activated and ready to go!</strong>
            </p>
            <p style="font-size: 14px; margin-bottom: 15px;">
                We've created a homepage for you. Now choose a professional template:
            </p>
            <p style="margin-bottom: 10px;">
                <a href="<?php echo admin_url('admin.php?page=ross-homepage-templates'); ?>" class="button button-primary button-hero">
                    <span class="dashicons dashicons-layout" style="margin-top: 4px;"></span>
                    Choose Your Homepage Template
                </a>
                <a href="<?php echo admin_url('admin.php?page=ross-theme'); ?>" class="button button-secondary button-hero">
                    <span class="dashicons dashicons-admin-customizer" style="margin-top: 4px;"></span>
                    Customize Theme Settings
                </a>
                <a href="<?php echo home_url('/'); ?>" class="button button-secondary button-hero" target="_blank">
                    <span class="dashicons dashicons-visibility" style="margin-top: 4px;"></span>
                    View Your Website
                </a>
            </p>
            
            <div style="background: #f0f6fc; padding: 15px; border-left: 4px solid #0073aa; margin-top: 15px;">
                <h4 style="margin-top: 0;">📦 What's Included:</h4>
                <ul style="margin-left: 20px; font-size: 13px;">
                    <li><strong>6 Professional Homepage Templates</strong> - Business, Creative, E-commerce, Minimal, Restaurant, Startup</li>
                    <li><strong>5 Header Layouts</strong> - Default, Centered, Minimal, Modern, Transparent</li>
                    <li><strong>4 Footer Templates</strong> - Classic, Modern, Minimal, Corporate</li>
                    <li><strong>Full Customization</strong> - Colors, Typography, Spacing, Logo, Social Icons</li>
                </ul>
            </div>
        </div>
        <style>
            .ross-activation-notice .button {
                margin-right: 10px;
                margin-bottom: 5px;
            }
            .ross-activation-notice .button .dashicons {
                line-height: inherit;
            }
        </style>
        <?php
    }
}
add_action('admin_notices', 'ross_theme_activation_notice');

/**
 * Add quick actions to theme page
 */
function ross_theme_add_customizer_link($links) {
    $homepage_link = '<a href="' . admin_url('admin.php?page=ross-homepage-templates') . '">' . __('Homepage Templates', 'ross-theme') . '</a>';
    $settings_link = '<a href="' . admin_url('admin.php?page=ross-theme') . '">' . __('Theme Settings', 'ross-theme') . '</a>';
    
    array_unshift($links, $homepage_link);
    array_unshift($links, $settings_link);
    
    return $links;
}
add_filter('theme_row_meta', 'ross_theme_add_customizer_link', 10, 2);
