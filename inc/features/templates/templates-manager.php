<?php
/**
 * Template Manager
 * Centralized system for managing header, footer, and full site templates
 */

class RossTemplateManager {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_templates_menu'), 15);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Add Templates menu to admin
     */
    public function add_templates_menu() {
        add_submenu_page(
            'ross-theme',                     // Parent slug (Ross Theme Settings)
            'Templates',                       // Page title
            '🎨 Templates',                    // Menu title
            'manage_options',                  // Capability
            'ross-theme-templates',            // Menu slug
            array($this, 'render_templates_page')
        );
    }
    
    /**
     * Enqueue CSS and JS for template manager
     */
    public function enqueue_assets($hook) {
        if ('ross-theme_page_ross-theme-templates' !== $hook) {
            return;
        }
        
        // Enqueue CSS
        $css_file = get_template_directory() . '/assets/css/admin/templates-manager.css';
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'ross-templates-manager',
                get_template_directory_uri() . '/assets/css/admin/templates-manager.css',
                array(),
                filemtime($css_file)
            );
        }
        
        // Enqueue JS
        $js_file = get_template_directory() . '/assets/js/admin/templates-manager.js';
        if (file_exists($js_file)) {
            wp_enqueue_script(
                'ross-templates-manager',
                get_template_directory_uri() . '/assets/js/admin/templates-manager.js',
                array('jquery'),
                filemtime($js_file),
                true
            );
            
            wp_localize_script('ross-templates-manager', 'rossTemplates', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ross_templates_nonce')
            ));
        }
    }
    
    /**
     * Get header templates
     */
    public function get_header_templates() {
        // For now, return placeholder data
        // You can expand this to load from actual template files
        return array(
            'header-default' => array(
                'id' => 'header-default',
                'title' => 'Default Header',
                'description' => 'Standard header with logo and navigation'
            ),
            'header-centered' => array(
                'id' => 'header-centered',
                'title' => 'Centered Header',
                'description' => 'Centered logo with navigation below'
            ),
            'header-minimal' => array(
                'id' => 'header-minimal',
                'title' => 'Minimal Header',
                'description' => 'Clean minimal design'
            ),
            'header-transparent' => array(
                'id' => 'header-transparent',
                'title' => 'Transparent Header',
                'description' => 'Overlay header with transparent background'
            )
        );
    }
    
    /**
     * Get footer templates
     */
    public function get_footer_templates() {
        $templates = array();
        $dir = get_template_directory() . '/inc/features/footer/templates/';
        
        if (is_dir($dir)) {
            foreach (glob($dir . '*.php') as $file) {
                $id = basename($file, '.php');
                $data = include $file;
                if (is_array($data)) {
                    $templates[$id] = $data;
                }
            }
        }
        
        return $templates;
    }
    
    /**
     * Get full site templates
     */
    public function get_full_site_templates() {
        return array(
            'corporate-business' => array(
                'id' => 'corporate-business',
                'title' => 'Corporate Business',
                'description' => 'Professional business website',
                'category' => 'Business',
                'preview' => get_template_directory_uri() . '/assets/images/templates/corporate-business.jpg'
            ),
            'creative-portfolio' => array(
                'id' => 'creative-portfolio',
                'title' => 'Creative Portfolio',
                'description' => 'Showcase your creative work',
                'category' => 'Creative',
                'preview' => get_template_directory_uri() . '/assets/images/templates/creative-portfolio.jpg'
            )
        );
    }
    
    /**
     * Render templates admin page
     */
    public function render_templates_page() {
        $header_templates = $this->get_header_templates();
        $footer_templates = $this->get_footer_templates();
        $full_site_templates = $this->get_full_site_templates();
        ?>
        <div class="wrap ross-templates-manager">
            <h1>🎨 Template Manager</h1>
            <p class="description">Choose from professional templates to customize your site instantly.</p>
            
            <!-- Filter Tabs -->
            <div class="ross-filter-tabs" style="margin: 20px 0;">
                <button class="button ross-filter-tab active" data-category="all">All Templates</button>
                <button class="button ross-filter-tab" data-category="header">Header Templates</button>
                <button class="button ross-filter-tab" data-category="footer">Footer Templates</button>
                <button class="button ross-filter-tab" data-category="full_site">Full Site Templates</button>
            </div>
            
            <!-- Header Templates -->
            <div class="ross-template-section" data-section="header">
                <h2>📐 Header Templates (<?php echo count($header_templates); ?>)</h2>
                <div class="ross-template-grid">
                    <?php foreach ($header_templates as $id => $template): ?>
                        <div class="ross-template-card">
                            <div class="ross-template-preview">
                                <div class="ross-template-placeholder">
                                    <span class="dashicons dashicons-admin-customizer"></span>
                                </div>
                            </div>
                            <div class="ross-template-info">
                                <h3><?php echo esc_html($template['title']); ?></h3>
                                <p class="description"><?php echo esc_html($template['description']); ?></p>
                                <a href="<?php echo admin_url('admin.php?page=ross-theme-header'); ?>" class="button button-primary">
                                    Configure Header
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Footer Templates -->
            <div class="ross-template-section" data-section="footer">
                <h2>📐 Footer Templates (<?php echo count($footer_templates); ?>)</h2>
                <div class="ross-template-grid">
                    <?php foreach ($footer_templates as $id => $template): ?>
                        <div class="ross-template-card">
                            <div class="ross-template-preview">
                                <div class="ross-template-placeholder">
                                    <span class="dashicons dashicons-admin-customizer"></span>
                                </div>
                            </div>
                            <div class="ross-template-info">
                                <h3><?php echo esc_html($template['title'] ?? $id); ?></h3>
                                <p class="description"><?php echo esc_html($template['description'] ?? ''); ?></p>
                                <a href="<?php echo admin_url('admin.php?page=ross-theme-footer'); ?>" class="button button-primary">
                                    Configure Footer
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Full Site Templates -->
            <div class="ross-template-section" data-section="full_site">
                <h2>🌐 Full Site Templates (<?php echo count($full_site_templates); ?>)</h2>
                <div class="ross-template-grid">
                    <?php foreach ($full_site_templates as $id => $template): ?>
                        <div class="ross-template-card">
                            <div class="ross-template-preview">
                                <?php if (isset($template['preview']) && file_exists(str_replace(get_template_directory_uri(), get_template_directory(), $template['preview']))): ?>
                                    <img src="<?php echo esc_url($template['preview']); ?>" alt="<?php echo esc_attr($template['title']); ?>">
                                <?php else: ?>
                                    <div class="ross-template-placeholder">
                                        <span class="dashicons dashicons-layout"></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="ross-template-info">
                                <h3><?php echo esc_html($template['title']); ?></h3>
                                <p class="description"><?php echo esc_html($template['description']); ?></p>
                                <span class="ross-template-category"><?php echo esc_html($template['category']); ?></span>
                                <button class="button button-secondary" disabled>Coming Soon</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <style>
            .ross-templates-manager {
                max-width: 1400px;
                margin: 20px auto;
            }
            .ross-filter-tabs {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }
            .ross-filter-tab.active {
                background: #0969da;
                color: #fff;
                border-color: #0969da;
            }
            .ross-template-section {
                margin: 40px 0;
            }
            .ross-template-section h2 {
                font-size: 24px;
                margin-bottom: 20px;
            }
            .ross-template-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 25px;
            }
            .ross-template-card {
                background: #fff;
                border: 2px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                transition: all 0.3s;
            }
            .ross-template-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(0,0,0,0.1);
                border-color: #0969da;
            }
            .ross-template-preview {
                height: 200px;
                background: #f5f5f5;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .ross-template-preview img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .ross-template-placeholder {
                font-size: 48px;
                color: #ccc;
            }
            .ross-template-info {
                padding: 20px;
            }
            .ross-template-info h3 {
                margin: 0 0 10px 0;
                font-size: 18px;
            }
            .ross-template-info .description {
                margin-bottom: 15px;
                color: #666;
            }
            .ross-template-category {
                display: inline-block;
                background: #f0f6fc;
                color: #0969da;
                padding: 4px 10px;
                border-radius: 4px;
                font-size: 11px;
                margin-bottom: 10px;
            }
            .ross-template-info .button {
                width: 100%;
                margin-top: 10px;
            }
        </style>
        <?php
    }
}

// Initialize
RossTemplateManager::get_instance();
