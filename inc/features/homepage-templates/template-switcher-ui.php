<?php
/**
 * Homepage Template Switcher UI
 * Admin interface for selecting and previewing homepage templates
 */

if (!defined('ABSPATH')) exit;

class RossHomepageTemplateSwitcher {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'), 25);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_ross_preview_homepage_template', array($this, 'ajax_preview_template'));
        add_action('wp_ajax_ross_apply_homepage_template', array($this, 'ajax_apply_template'));
        add_action('wp_ajax_ross_get_current_template', array($this, 'ajax_get_current_template'));
    }
    
    /**
     * Add submenu page under Ross Theme
     */
    public function add_admin_menu() {
        add_submenu_page(
            'ross-theme',
            'Homepage Templates',
            'Homepage Templates',
            'manage_options',
            'ross-homepage-templates',
            array($this, 'render_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook) {
        if ($hook !== 'ross-theme_page_ross-homepage-templates') {
            return;
        }
        
        // Styles
        wp_enqueue_style(
            'ross-template-switcher',
            get_template_directory_uri() . '/assets/css/admin/template-switcher.css',
            array(),
            filemtime(get_template_directory() . '/assets/css/admin/template-switcher.css')
        );
        
        // Scripts
        wp_enqueue_script(
            'ross-template-switcher',
            get_template_directory_uri() . '/assets/js/admin/template-switcher.js',
            array('jquery'),
            filemtime(get_template_directory() . '/assets/js/admin/template-switcher.js'),
            true
        );
        
        // Localize script
        wp_localize_script('ross-template-switcher', 'rossTemplateSwitcher', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ross_template_switcher'),
            'siteUrl' => home_url('/'),
            'homePageId' => get_option('page_on_front')
        ));
    }
    
    /**
     * Get available templates
     */
    private function get_available_templates() {
        return array(
            'template-home-business.php' => array(
                'id' => 'business',
                'name' => 'Business Professional',
                'description' => 'Perfect for corporate websites, professional services, and B2B companies',
                'icon' => '💼',
                'features' => array('Hero Section', 'Services Grid', 'Testimonials', 'CTA Banner'),
                'preview_url' => home_url('/?preview_template=business')
            ),
            'template-home-creative.php' => array(
                'id' => 'creative',
                'name' => 'Creative Agency',
                'description' => 'Ideal for design studios, agencies, and creative professionals',
                'icon' => '🎨',
                'features' => array('Portfolio Grid', 'Team Showcase', 'Process Steps', 'Client Logos'),
                'preview_url' => home_url('/?preview_template=creative')
            ),
            'template-home-ecommerce.php' => array(
                'id' => 'ecommerce',
                'name' => 'E-commerce Shop',
                'description' => 'Built for online stores and product-focused businesses',
                'icon' => '🛒',
                'features' => array('Featured Products', 'Categories', 'Promotions', 'Newsletter'),
                'preview_url' => home_url('/?preview_template=ecommerce')
            ),
            'template-home-minimal.php' => array(
                'id' => 'minimal',
                'name' => 'Minimal Portfolio',
                'description' => 'Clean and elegant for photographers, artists, and minimalists',
                'icon' => '✨',
                'features' => array('Large Images', 'Simple Navigation', 'About Section', 'Contact Form'),
                'preview_url' => home_url('/?preview_template=minimal')
            ),
            'template-home-restaurant.php' => array(
                'id' => 'restaurant',
                'name' => 'Restaurant & Food',
                'description' => 'Appetizing design for restaurants, cafes, and food businesses',
                'icon' => '🍽️',
                'features' => array('Menu Showcase', 'Reservations', 'Gallery', 'Location Map'),
                'preview_url' => home_url('/?preview_template=restaurant')
            ),
            'template-home-startup.php' => array(
                'id' => 'startup',
                'name' => 'Startup & Tech',
                'description' => 'Modern design for tech startups, SaaS, and innovation-focused companies',
                'icon' => '🚀',
                'features' => array('Feature Highlights', 'Pricing Tables', 'FAQ Section', 'App Showcase'),
                'preview_url' => home_url('/?preview_template=startup')
            )
        );
    }
    
    /**
     * Get currently active template
     */
    private function get_current_template() {
        $home_page_id = get_option('page_on_front');
        if ($home_page_id) {
            $template = get_page_template_slug($home_page_id);
            return $template ? $template : 'default';
        }
        return 'default';
    }
    
    /**
     * Render admin page
     */
    public function render_page() {
        $templates = $this->get_available_templates();
        $current_template = $this->get_current_template();
        $home_page_id = get_option('page_on_front');
        
        ?>
        <div class="wrap ross-template-switcher">
            <h1>
                <span class="dashicons dashicons-layout" style="font-size: 32px; width: 32px; height: 32px;"></span>
                Homepage Templates
            </h1>
            
            <?php if (!$home_page_id): ?>
                <div class="notice notice-warning">
                    <p>
                        <strong>No homepage set!</strong> 
                        Please <a href="<?php echo admin_url('options-reading.php'); ?>">set a static front page</a> first.
                    </p>
                </div>
            <?php endif; ?>
            
            <div class="ross-template-header">
                <p class="description">
                    Choose from 6 professionally designed homepage templates. 
                    Preview templates before applying, and customize them using Ross Theme options.
                </p>
                
                <?php if ($current_template && $current_template !== 'default'): ?>
                    <div class="current-template-badge">
                        <span class="dashicons dashicons-yes-alt"></span>
                        Currently using: <strong><?php echo esc_html($this->get_template_name($current_template)); ?></strong>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="ross-templates-grid">
                <?php foreach ($templates as $template_file => $template): ?>
                    <?php 
                        $is_active = ($current_template === $template_file);
                        $card_class = $is_active ? 'template-card active' : 'template-card';
                    ?>
                    <div class="<?php echo esc_attr($card_class); ?>" data-template="<?php echo esc_attr($template_file); ?>">
                        <div class="template-icon"><?php echo esc_html($template['icon']); ?></div>
                        
                        <h3 class="template-name"><?php echo esc_html($template['name']); ?></h3>
                        
                        <p class="template-description"><?php echo esc_html($template['description']); ?></p>
                        
                        <div class="template-features">
                            <?php foreach ($template['features'] as $feature): ?>
                                <span class="feature-tag">
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php echo esc_html($feature); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="template-actions">
                            <?php if ($is_active): ?>
                                <button class="button button-primary active-button" disabled>
                                    <span class="dashicons dashicons-yes"></span> Active Template
                                </button>
                            <?php else: ?>
                                <button class="button button-secondary ross-preview-template" 
                                        data-template="<?php echo esc_attr($template_file); ?>"
                                        data-preview-url="<?php echo esc_url($template['preview_url']); ?>">
                                    <span class="dashicons dashicons-visibility"></span> Preview
                                </button>
                                <button class="button button-primary ross-apply-template" 
                                        data-template="<?php echo esc_attr($template_file); ?>"
                                        data-name="<?php echo esc_attr($template['name']); ?>">
                                    <span class="dashicons dashicons-download"></span> Apply
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($is_active): ?>
                            <div class="active-indicator">
                                <span class="dashicons dashicons-star-filled"></span>
                                Currently Active
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="ross-help-section">
                <h3><span class="dashicons dashicons-info"></span> How to Use</h3>
                <ol>
                    <li><strong>Preview:</strong> Click "Preview" to see the template in action on your site</li>
                    <li><strong>Apply:</strong> Click "Apply" to set the template as your homepage</li>
                    <li><strong>Customize:</strong> After applying, customize colors, fonts, and content via Ross Theme → General/Header/Footer settings</li>
                </ol>
                
                <div class="help-tip">
                    <span class="dashicons dashicons-lightbulb"></span>
                    <strong>Tip:</strong> Templates automatically use your theme colors (Primary, Secondary, Accent) from General Settings.
                    Change colors once, update all templates instantly!
                </div>
            </div>
        </div>
        
        <!-- Preview Modal -->
        <div id="ross-template-preview-modal" class="ross-modal" style="display: none;">
            <div class="ross-modal-overlay"></div>
            <div class="ross-modal-content">
                <div class="ross-modal-header">
                    <h2>Template Preview</h2>
                    <button class="ross-modal-close">
                        <span class="dashicons dashicons-no-alt"></span>
                    </button>
                </div>
                <div class="ross-modal-body">
                    <iframe id="ross-preview-iframe" src="" frameborder="0"></iframe>
                </div>
                <div class="ross-modal-footer">
                    <button class="button button-secondary ross-modal-close">Close Preview</button>
                    <button class="button button-primary ross-apply-from-preview">
                        <span class="dashicons dashicons-yes"></span> Apply This Template
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get template display name from file
     */
    private function get_template_name($template_file) {
        $templates = $this->get_available_templates();
        return isset($templates[$template_file]) ? $templates[$template_file]['name'] : $template_file;
    }
    
    /**
     * AJAX: Preview template
     */
    public function ajax_preview_template() {
        check_ajax_referer('ross_template_switcher', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $template = sanitize_text_field($_POST['template']);
        $templates = $this->get_available_templates();
        
        if (!isset($templates[$template])) {
            wp_send_json_error('Invalid template');
        }
        
        wp_send_json_success(array(
            'preview_url' => $templates[$template]['preview_url'],
            'name' => $templates[$template]['name']
        ));
    }
    
    /**
     * AJAX: Apply template
     */
    public function ajax_apply_template() {
        check_ajax_referer('ross_template_switcher', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $template = sanitize_text_field($_POST['template']);
        $home_page_id = get_option('page_on_front');
        
        // Auto-create homepage if it doesn't exist
        if (!$home_page_id) {
            $home_page_id = wp_insert_post(array(
                'post_title' => 'Home',
                'post_name' => 'home',
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_content' => '<!-- Homepage content managed by template -->'
            ));
            
            if (!is_wp_error($home_page_id)) {
                // Set as front page
                update_option('show_on_front', 'page');
                update_option('page_on_front', $home_page_id);
            } else {
                wp_send_json_error('Failed to create homepage');
                return;
            }
        }
        
        $templates = $this->get_available_templates();
        if (!isset($templates[$template])) {
            wp_send_json_error('Invalid template');
        }
        
        // Update page template
        update_post_meta($home_page_id, '_wp_page_template', $template);
        
        // Store template metadata for tracking
        update_post_meta($home_page_id, '_ross_homepage_template', $template);
        update_post_meta($home_page_id, '_ross_template_applied_date', current_time('mysql'));
        
        // Clear any caches
        delete_transient('ross_homepage_template_cache');
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        wp_send_json_success(array(
            'message' => 'Template applied successfully!',
            'template' => $template,
            'name' => $templates[$template]['name'],
            'home_url' => home_url('/'),
            'created_page' => !$home_page_id ? true : false
        ));
    }
    
    /**
     * AJAX: Get current template
     */
    public function ajax_get_current_template() {
        check_ajax_referer('ross_template_switcher', 'nonce');
        
        $current = $this->get_current_template();
        wp_send_json_success(array('template' => $current));
    }
}

// Initialize
new RossHomepageTemplateSwitcher();
