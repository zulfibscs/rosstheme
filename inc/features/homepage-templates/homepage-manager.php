<?php
/**
 * Homepage Templates Manager
 * Manages predesigned homepage templates with responsive design
 * 
 * @package RossTheme
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

class RossHomepageManager {
    
    private static $instance = null;
    private $templates = array();
    
    /**
     * Singleton instance
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
        // Admin hooks
        add_action('admin_menu', array($this, 'add_templates_menu'), 15);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // AJAX handlers
        add_action('wp_ajax_ross_get_template_preview', array($this, 'ajax_get_template_preview'));
        add_action('wp_ajax_ross_apply_homepage_template', array($this, 'ajax_apply_homepage_template'));
        add_action('wp_ajax_ross_reset_homepage_template', array($this, 'ajax_reset_homepage_template'));
        
        // Initialize templates
        $this->init_templates();
    }
    
    /**
     * Initialize template definitions
     */
    private function init_templates() {
        $this->templates = array(
            'home-business' => array(
                'title' => 'Business Professional',
                'description' => 'Professional business homepage with hero section, services, and call-to-action',
                'category' => 'business',
                'preview_image' => 'business-pro.jpg',
                'features' => array('Hero Section', 'Services Grid', 'Testimonials', 'CTA Banner')
            ),
            'home-creative' => array(
                'title' => 'Creative Agency',
                'description' => 'Modern creative design with portfolio showcase and team section',
                'category' => 'creative',
                'preview_image' => 'creative-agency.jpg',
                'features' => array('Full-width Hero', 'Portfolio Grid', 'Team Members', 'Contact Form')
            ),
            'home-ecommerce' => array(
                'title' => 'E-Commerce Store',
                'description' => 'Product-focused layout with featured items and promotional banners',
                'category' => 'ecommerce',
                'preview_image' => 'ecommerce.jpg',
                'features' => array('Product Carousel', 'Category Grid', 'Promotions', 'Newsletter')
            ),
            'home-minimal' => array(
                'title' => 'Minimal Modern',
                'description' => 'Clean, minimal design focused on content and typography',
                'category' => 'minimal',
                'preview_image' => 'minimal.jpg',
                'features' => array('Clean Typography', 'Feature Blocks', 'Simple CTA', 'Blog Feed')
            ),
            'home-startup' => array(
                'title' => 'Startup Launch',
                'description' => 'Dynamic startup page with app features and pricing tables',
                'category' => 'startup',
                'preview_image' => 'startup.jpg',
                'features' => array('App Showcase', 'Feature List', 'Pricing Tables', 'Download CTA')
            ),
            'home-restaurant' => array(
                'title' => 'Restaurant & Cafe',
                'description' => 'Food-focused design with menu highlights and reservation form',
                'category' => 'restaurant',
                'preview_image' => 'restaurant.jpg',
                'features' => array('Hero Banner', 'Menu Showcase', 'Gallery', 'Reservations')
            )
        );
    }
    
    /**
     * Add Templates submenu to admin
     */
    public function add_templates_menu() {
        add_submenu_page(
            'ross-theme',
            'Homepage Templates',
            '🏠 Homepage Templates',
            'manage_options',
            'ross-homepage-templates',
            array($this, 'render_templates_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('ross-theme_page_ross-homepage-templates' !== $hook) {
            return;
        }
        
        // Admin CSS
        wp_enqueue_style(
            'ross-homepage-templates-admin',
            get_template_directory_uri() . '/assets/css/admin/homepage-templates.css',
            array(),
            '1.0.2'
        );
        
        // Admin JS
        wp_enqueue_script(
            'ross-homepage-templates-admin',
            get_template_directory_uri() . '/assets/js/admin/homepage-templates.js',
            array('jquery'),
            '1.0.2',
            true
        );
        
        // Localize script
        wp_localize_script('ross-homepage-templates-admin', 'rossHomepageTemplates', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ross_homepage_templates_nonce'),
            'strings' => array(
                'applying' => __('Applying template...', 'ross-theme'),
                'success' => __('Template applied successfully!', 'ross-theme'),
                'error' => __('Error applying template. Please try again.', 'ross-theme'),
                'confirmReset' => __('Are you sure you want to reset this template? This will restore the default layout.', 'ross-theme')
            )
        ));
    }
    
    /**
     * Render templates admin page
     */
    public function render_templates_page() {
        // Get current homepage
        $current_homepage_id = get_option('page_on_front', 0);
        $current_template = '';
        
        if ($current_homepage_id) {
            $current_template = get_post_meta($current_homepage_id, '_ross_homepage_template', true);
        }
        
        ?>
        <div class="wrap ross-homepage-templates-wrap">
            <h1><?php _e('🏠 Homepage Templates', 'ross-theme'); ?></h1>
            <p class="description">
                <?php _e('Choose a pre-designed homepage template. All templates are fully responsive and use your theme settings for header, footer, and styling.', 'ross-theme'); ?>
            </p>
            
            <?php if ($current_homepage_id && $current_template): ?>
            <div class="notice notice-info ross-current-template-notice">
                <p>
                    <strong><?php _e('Current Homepage:', 'ross-theme'); ?></strong>
                    <?php 
                    $template_info = isset($this->templates[$current_template]) ? $this->templates[$current_template] : null;
                    echo $template_info ? esc_html($template_info['title']) : esc_html($current_template);
                    ?>
                    <a href="<?php echo get_permalink($current_homepage_id); ?>" target="_blank" class="button button-small" style="margin-left: 10px;">
                        <?php _e('View Page', 'ross-theme'); ?>
                    </a>
                </p>
            </div>
            <?php endif; ?>
            
            <!-- Filter Tabs -->
            <div class="ross-template-filters">
                <button class="ross-filter-btn active" data-category="all">
                    <?php _e('All Templates', 'ross-theme'); ?>
                </button>
                <button class="ross-filter-btn" data-category="business">
                    <?php _e('Business', 'ross-theme'); ?>
                </button>
                <button class="ross-filter-btn" data-category="creative">
                    <?php _e('Creative', 'ross-theme'); ?>
                </button>
                <button class="ross-filter-btn" data-category="ecommerce">
                    <?php _e('E-Commerce', 'ross-theme'); ?>
                </button>
                <button class="ross-filter-btn" data-category="minimal">
                    <?php _e('Minimal', 'ross-theme'); ?>
                </button>
            </div>
            
            <!-- Templates Grid -->
            <div class="ross-templates-grid">
                <?php foreach ($this->templates as $template_id => $template): ?>
                    <div class="ross-template-card" data-category="<?php echo esc_attr($template['category']); ?>" data-template="<?php echo esc_attr($template_id); ?>">
                        <div class="ross-template-preview">
                            <div class="ross-template-image">
                                <?php 
                                $preview_img = get_template_directory_uri() . '/assets/images/homepage-templates/' . $template['preview_image'];
                                ?>
                                <img src="<?php echo esc_url($preview_img); ?>" alt="<?php echo esc_attr($template['title']); ?>" 
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2218%22%3E<?php echo esc_js($template['title']); ?>%3C/text%3E%3C/svg%3E'">
                            </div>
                            
                            <?php if ($current_template === $template_id): ?>
                            <div class="ross-template-badge active-badge">
                                <span class="dashicons dashicons-yes-alt"></span> <?php _e('Active', 'ross-theme'); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="ross-template-info">
                            <h3><?php echo esc_html($template['title']); ?></h3>
                            <p class="description"><?php echo esc_html($template['description']); ?></p>
                            
                            <div class="ross-template-features">
                                <?php foreach ($template['features'] as $feature): ?>
                                    <span class="ross-feature-tag"><?php echo esc_html($feature); ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="ross-template-actions">
                                <button class="button button-primary ross-apply-template" 
                                        data-template="<?php echo esc_attr($template_id); ?>"
                                        <?php echo ($current_template === $template_id) ? 'disabled' : ''; ?>>
                                    <?php echo ($current_template === $template_id) ? __('Active', 'ross-theme') : __('Apply Template', 'ross-theme'); ?>
                                </button>
                                
                                <?php if ($current_template === $template_id): ?>
                                <button class="button ross-reset-template" data-template="<?php echo esc_attr($template_id); ?>">
                                    <?php _e('Reset to Default', 'ross-theme'); ?>
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Information Box -->
            <div class="ross-info-box">
                <h3><?php _e('📋 How It Works', 'ross-theme'); ?></h3>
                <ul>
                    <li><?php _e('Select a homepage template by clicking "Apply Template"', 'ross-theme'); ?></li>
                    <li><?php _e('The template will be automatically set as your site homepage (Settings → Reading)', 'ross-theme'); ?></li>
                    <li><?php _e('All templates use your Header, Footer, and General theme settings', 'ross-theme'); ?></li>
                    <li><?php _e('Templates are fully responsive and mobile-friendly', 'ross-theme'); ?></li>
                    <li><?php _e('Use "Reset to Default" to restore original template content', 'ross-theme'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Get template preview
     */
    public function ajax_get_template_preview() {
        check_ajax_referer('ross_homepage_templates_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'ross-theme')));
            return;
        }
        
        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
        
        if (!isset($this->templates[$template_id])) {
            wp_send_json_error(array('message' => __('Invalid template', 'ross-theme')));
            return;
        }
        
        $template_data = $this->templates[$template_id];
        
        wp_send_json_success(array(
            'template' => $template_data,
            'preview_url' => get_template_directory_uri() . '/template-parts/homepage/' . $template_id . '.php'
        ));
    }
    
    /**
     * AJAX: Apply homepage template
     */
    public function ajax_apply_homepage_template() {
        // Clean ALL output buffers that might break JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        
        check_ajax_referer('ross_homepage_templates_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            while (ob_get_level()) { ob_end_clean(); }
            wp_send_json_error(array('message' => __('Insufficient permissions', 'ross-theme')));
            return;
        }
        
        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
        
        if (!isset($this->templates[$template_id])) {
            while (ob_get_level()) { ob_end_clean(); }
            wp_send_json_error(array('message' => __('Invalid template ID: ' . $template_id, 'ross-theme')));
            return;
        }
        
        // Create or update homepage
        $page_id = $this->create_homepage_from_template($template_id);
        
        if (is_wp_error($page_id)) {
            while (ob_get_level()) { ob_end_clean(); }
            wp_send_json_error(array('message' => $page_id->get_error_message()));
            return;
        }
        
        if (!$page_id) {
            while (ob_get_level()) { ob_end_clean(); }
            wp_send_json_error(array('message' => __('Failed to create page', 'ross-theme')));
            return;
        }
        
        // Set as front page
        update_option('show_on_front', 'page');
        update_option('page_on_front', $page_id);
        
        // Store template meta
        update_post_meta($page_id, '_ross_homepage_template', $template_id);
        update_post_meta($page_id, '_ross_template_version', '1.0.0');
        
        // Clean ALL output buffers before sending JSON
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        wp_send_json_success(array(
            'message' => __('Homepage template applied successfully!', 'ross-theme'),
            'page_id' => $page_id,
            'preview_url' => get_permalink($page_id)
        ));
    }
    
    /**
     * Create homepage from template
     */
    private function create_homepage_from_template($template_id) {
        $template_data = $this->templates[$template_id];
        
        // Check if page already exists
        $existing_pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_ross_homepage_template',
            'meta_value' => $template_id,
            'posts_per_page' => 1,
            'post_status' => 'any'
        ));
        
        if (!empty($existing_pages)) {
            $page_id = $existing_pages[0]->ID;
            // Update existing page
            wp_update_post(array(
                'ID' => $page_id,
                'post_status' => 'publish'
            ));
        } else {
            // Create new page
            $page_id = wp_insert_post(array(
                'post_title' => $template_data['title'],
                'post_name' => str_replace('home-', '', $template_id),
                'post_type' => 'page',
                'post_status' => 'publish',
                'post_content' => $this->get_template_default_content($template_id),
                'page_template' => 'template-' . $template_id . '.php'
            ));
        }
        
        if (is_wp_error($page_id)) {
            return $page_id;
        }
        
        // Store default content for reset functionality
        update_post_meta($page_id, '_ross_template_default_content', $this->get_template_default_content($template_id));
        
        return $page_id;
    }
    
    /**
     * Get default content for template
     */
    private function get_template_default_content($template_id) {
        // This will be populated with actual content blocks
        // For now, return placeholder that template file will handle
        return '<!-- Ross Homepage Template: ' . $template_id . ' -->';
    }
    
    /**
     * AJAX: Reset homepage template
     */
    public function ajax_reset_homepage_template() {
        check_ajax_referer('ross_homepage_templates_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'ross-theme')));
            return;
        }
        
        $template_id = isset($_POST['template_id']) ? sanitize_text_field($_POST['template_id']) : '';
        
        if (!isset($this->templates[$template_id])) {
            wp_send_json_error(array('message' => __('Invalid template', 'ross-theme')));
            return;
        }
        
        // Find the page
        $pages = get_posts(array(
            'post_type' => 'page',
            'meta_key' => '_ross_homepage_template',
            'meta_value' => $template_id,
            'posts_per_page' => 1
        ));
        
        if (empty($pages)) {
            wp_send_json_error(array('message' => __('Template page not found', 'ross-theme')));
            return;
        }
        
        $page_id = $pages[0]->ID;
        $default_content = get_post_meta($page_id, '_ross_template_default_content', true);
        
        // Reset to default content
        wp_update_post(array(
            'ID' => $page_id,
            'post_content' => $default_content ? $default_content : $this->get_template_default_content($template_id)
        ));
        
        wp_send_json_success(array(
            'message' => __('Template reset to default successfully!', 'ross-theme')
        ));
    }
    
    /**
     * Get all templates
     */
    public function get_templates() {
        return $this->templates;
    }
}

// Note: Initialized in functions.php
