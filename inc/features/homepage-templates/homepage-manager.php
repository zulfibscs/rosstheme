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
        // AJAX handlers (UI is now handled by template-switcher-ui.php)
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
    
    // Note: Admin UI removed - now handled by template-switcher-ui.php
    // This class only provides backend AJAX handlers and template data
    
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
