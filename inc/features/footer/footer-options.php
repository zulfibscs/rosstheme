<?php
/**
 * Footer Options Module
 * Controls everything visible in the site footer
 */

class RossFooterOptions {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('ross_theme_footer_options');
        // Run a quick migration for legacy template keys before registering settings
        add_action('admin_init', array($this, 'migrate_legacy_template_keys'), 5);
        add_action('admin_init', array($this, 'migrate_social_icons_enabled_flags'), 5); // NEW: Social icons V2 migration
        add_action('admin_init', array($this, 'ensure_default_template_options'), 6);
        add_action('admin_init', array($this, 'register_footer_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_footer_scripts'));
        add_action('wp_ajax_ross_apply_footer_template', array($this, 'ajax_apply_footer_template'));
        add_action('wp_ajax_ross_restore_footer_backup', array($this, 'ajax_restore_footer_backup'));
        add_action('wp_ajax_ross_delete_footer_backup', array($this, 'ajax_delete_footer_backup'));
        add_action('wp_ajax_ross_list_footer_backups', array($this, 'ajax_list_footer_backups'));
        add_action('wp_ajax_ross_get_footer_template_preview', array($this, 'ajax_get_footer_template_preview'));
        // Admin notice to show when a template is applied to protect against overwrites
        add_action('admin_notices', array($this, 'show_template_applied_notice'));
        add_action('wp_ajax_ross_sync_footer_templates', array($this, 'ajax_sync_footer_templates'));
        add_action('wp_ajax_ross_apply_template_sync', array($this, 'ajax_apply_template_sync'));
    }

    /**
     * Migrate legacy per-template option keys saved under names like
     * 'template_template1_bg' into the new keys 'template1_bg', etc.
     * Also migrate old template IDs (template1, template2, etc.) to new naming
     * This runs once on admin_init and updates the stored option if needed.
     */
    public function migrate_legacy_template_keys() {
        if (!is_admin()) return;

        $opts = get_option('ross_theme_footer_options', array());
        if (empty($opts) || !is_array($opts)) return;

        $changed = false;
        
        // Migrate legacy prefix (template_template1_bg -> template1_bg)
        for ($i = 1; $i <= 4; $i++) {
            $legacy_prefix = 'template_template' . $i . '_';
            $new_prefix = 'template' . $i . '_';
            $keys = array('bg', 'text', 'accent', 'social');
            foreach ($keys as $k) {
                $legacy = $legacy_prefix . $k;
                $new = $new_prefix . $k;
                if (isset($opts[$legacy])) {
                    // If new key empty, copy value; otherwise drop legacy key to avoid confusion
                    if (empty($opts[$new]) && $opts[$new] !== '0') {
                        $opts[$new] = $opts[$legacy];
                    }
                    unset($opts[$legacy]);
                    $changed = true;
                }
            }
        }
        
        // NEW: Migrate old template IDs to new semantic names
        $template_id_map = array(
            'template1' => 'business-professional',
            'template2' => 'ecommerce',
            'template3' => 'creative-agency',
            'template4' => 'minimal-modern'
        );
        
        if (isset($opts['footer_template']) && isset($template_id_map[$opts['footer_template']])) {
            $opts['footer_template'] = $template_id_map[$opts['footer_template']];
            $changed = true;
        }

        if ($changed) {
            update_option('ross_theme_footer_options', $opts);
            // Refresh local copy for current request
            $this->options = $opts;
        }
    }

    /**
     * Migrate social icons to V2 format with enabled flags
     * If a platform has a URL but no _enabled field, set it to enabled
     * This runs once to handle upgrades from old social icons system
     */
    public function migrate_social_icons_enabled_flags() {
        if (!is_admin()) return;
        
        // Check if migration already ran
        $migration_flag = get_option('ross_social_icons_v2_migrated', false);
        if ($migration_flag) return;
        
        $opts = get_option('ross_theme_footer_options', array());
        if (empty($opts) || !is_array($opts)) {
            $opts = array();
        }
        
        $changed = false;
        
        // Ensure master toggle defaults to enabled
        if (!isset($opts['enable_social_icons'])) {
            $opts['enable_social_icons'] = 1;
            $changed = true;
        }
        
        // Core 4 platforms: If URL exists but no _enabled field, enable it
        $core_platforms = array('facebook', 'instagram', 'twitter', 'linkedin');
        foreach ($core_platforms as $platform) {
            $url_key = $platform . '_url';
            $enabled_key = $platform . '_enabled';
            
            // If platform has a URL but no enabled flag, enable it
            if (!empty($opts[$url_key]) && !isset($opts[$enabled_key])) {
                $opts[$enabled_key] = 1;
                $changed = true;
            }
            // If no URL and no enabled flag, set to enabled by default (user can disable)
            elseif (!isset($opts[$enabled_key])) {
                $opts[$enabled_key] = 1;
                $changed = true;
            }
        }
        
        // Custom platform defaults to disabled
        if (!isset($opts['custom_social_enabled'])) {
            $opts['custom_social_enabled'] = 0;
            $changed = true;
        }
        
        // Set display order if not exists
        if (!isset($opts['social_display_order']) || !is_array($opts['social_display_order'])) {
            $opts['social_display_order'] = array('facebook', 'instagram', 'twitter', 'linkedin', 'custom');
            $changed = true;
        }
        
        if ($changed) {
            update_option('ross_theme_footer_options', $opts);
            // Refresh local copy
            $this->options = $opts;
        }
        
        // Set migration flag so this only runs once
        update_option('ross_social_icons_v2_migrated', true);
    }

    /** Ensure that default footer templates exist in stored options (for customization or persistence) */
    public function ensure_default_template_options() {
        if (!is_admin()) return;
        $templates = get_option('ross_theme_footer_templates', array());
        // If folder-based templates exist, force them to override stored templates
        $folder_templates = $this->load_templates_from_dir();
        $force_override = apply_filters('ross_theme_force_template_files_override', true);
        if ($force_override && is_array($folder_templates) && !empty($folder_templates)) {
            // Only update stored option if it differs to avoid unnecessary writes
            if ($templates !== $folder_templates) {
                update_option('ross_theme_footer_templates', $folder_templates);
                $templates = $folder_templates;
                // Set a transient so we can show an admin notice informing the user
                set_transient('ross_templates_overridden', 1, 30);
                add_action('admin_notices', array($this, 'show_templates_overridden_notice'));
            }
        }

        if (!is_array($templates) || empty($templates)) {
            // Prefer to load templates from the dedicated `templates/` folder if available
            if (is_array($folder_templates) && !empty($folder_templates)) {
                $default = $folder_templates;
            } else {
                // Fallback to hardcoded defaults with new template IDs
                $default = array(
                'business-professional' => array(
                    'title' => 'Business Professional',
                    'description' => 'Clean 4-column layout ideal for professional services and B2B companies',
                    'icon' => '💼',
                    'cols' => array(
                        'About Us|Empowering businesses with expert financial, consulting, and digital solutions for growth.',
                        'Our Services|Auditing, Tax, Advisory, Digital Transformation, Strategy',
                        'Insights & Resources|Blog, Case Studies, Whitepapers, FAQs',
                        'Contact|123 Business Ave, City | (555) 123-4567 | info@businesspro.com | LinkedIn, Facebook'
                    ),
                    'bg' => '#f8f9fb',
                    'text' => '#0b2140',
                    'accent' => '#0b66a6',
                    'columns' => 4
                ),
                'ecommerce' => array(
                    'title' => 'E-commerce',
                    'description' => 'Modern layout optimized for online stores with product categories',
                    'icon' => '🛒',
                    'cols' => array('Shop|All Products, New Arrivals, Sale', 'Customer Service|Shipping, Returns, FAQ', 'Company|About, Careers, Press', 'Subscribe|Join our newsletter'),
                    'bg' => '#fff',
                    'text' => '#0b2140',
                    'accent' => '#b02a2a',
                    'columns' => 4
                ),
                'creative-agency' => array(
                    'title' => 'Creative Agency',
                    'description' => 'Bold dark design perfect for creative studios and agencies',
                    'icon' => '🎨',
                    'cols' => array('Who We Are|Design-led agency crafting beautiful experiences.', 'Work|Case Studies, Clients', 'Services|Branding, UX/UI', 'Contact|hello@agency.example'),
                    'bg' => '#0c0c0d',
                    'text' => '#f3f4f6',
                    'accent' => '#E5C902',
                    'columns' => 4
                ),
                'minimal-modern' => array(
                    'title' => 'Minimal Modern',
                    'description' => 'Ultra-clean minimalist design ideal for SaaS products',
                    'icon' => '✨',
                    'cols' => array('Company|About, Contact', 'Explore|Features, Pricing', 'Resources|Docs, API', 'Follow|Social links'),
                    'bg' => '#fafafa',
                    'text' => '#0b2140',
                    'accent' => '#0b66a6',
                    'columns' => 1
                ),
                );
            }
            update_option('ross_theme_footer_templates', $default);
            $templates = $default;
        }
        return $templates;
    }

    /** Render admin notice informing that folder templates were used to override stored templates */
    public function show_templates_overridden_notice() {
        if (!get_transient('ross_templates_overridden')) return;
        delete_transient('ross_templates_overridden');
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><strong>Footer templates updated:</strong> Template files in <code>inc/features/footer/templates/</code> were detected and have overridden stored theme footer templates.</p>
        </div>
        <?php
    }

    /** Admin notice to inform when a template was applied and protected by transient */
    public function show_template_applied_notice() {
        if (!get_transient('ross_template_applied_notice')) return;
        delete_transient('ross_template_applied_notice');
        ?>
        <div  class="notice notice-success is-dismissible">
            <p ><strong>Footer template applied:</strong> The selected template was applied and your footer layout has been updated. Saving this page will not overwrite the applied template values.</p>
        </div>
        <?php
    }

    /** Return templates array - stored override falls back to default hardcoded list */
    private function get_templates() {
        $stored = get_option('ross_theme_footer_templates', array());

        // Try loading templates from the 'templates' folder first
        $from_dir = $this->load_templates_from_dir();

        // Allow plugin/theme to opt-out of enforcement via a filter (default TRUE)
        $force_override = apply_filters('ross_theme_force_template_files_override', true);
        // Force folder-based templates to override any stored templates of the same key
        $templates = is_array($stored) ? $stored : array();
        if ($force_override && is_array($from_dir) && !empty($from_dir)) {
            foreach ($from_dir as $k => $v) {
                if (isset($templates[$k]) && is_array($templates[$k]) && is_array($v)) {
                    // folder keys should win over stored; merge where needed
                    $templates[$k] = array_replace_recursive($templates[$k], $v);
                } else {
                    $templates[$k] = $v;
                }
            }
        }

        if (!empty($templates)) return $templates;

        // default templates (same as earlier sample list)
        return array(
            'template1' => array(
                'title' => 'Business Professional',
                'cols' => array(
                    'About Us|Empowering businesses with expert financial, consulting, and digital solutions for growth.',
                    'Our Services|Auditing, Tax, Advisory, Digital Transformation, Strategy',
                    'Insights & Resources|Blog, Case Studies, Whitepapers, FAQs',
                    'Contact|123 Business Ave, City | (555) 123-4567 | info@businesspro.com | LinkedIn, Facebook'
                ),
                'bg' => '#f8f9fb'
            ),
            'template2' => array('title' => 'E-commerce', 'cols' => array('Shop|All Products, New Arrivals, Sale', 'Customer Service|Shipping, Returns, FAQ', 'Company|About, Careers, Press', 'Subscribe|Join our newsletter'), 'bg' => '#fff'),
            'template3' => array('title' => 'Creative Agency', 'cols' => array('Who We Are|Design-led agency crafting beautiful experiences.', 'Work|Case Studies, Clients', 'Services|Branding, UX/UI', 'Contact|hello@agency.example'), 'bg' => '#111'),
            'template4' => array('title' => 'Minimal Modern', 'cols' => array('Company|About, Contact', 'Explore|Features, Pricing', 'Resources|Docs, API', 'Follow|Social links'), 'bg' => '#fafafa'),
        );
    }

    /** Scan `templates` subfolder and load individual template files (return associative array keyed by file basename) */
    private function load_templates_from_dir() {
        $dir = __DIR__ . '/templates';
        $templates = array();
        if (!is_dir($dir)) return $templates;
        foreach (glob($dir . '/*.php') as $file) {
            // Filename becomes the template id: e.g. template1.php -> template1
            $id = basename($file, '.php');
            try {
                $data = include $file;
                if (is_array($data)) {
                    $templates[$id] = $data;
                }
            } catch (Exception $e) {
                // skip files that throw errors
            }
        }
        return $templates;
    }
    
    public function enqueue_footer_scripts($hook) {
        // Enqueue footer admin scripts when on the Footer Options page.
        // The $hook value differs for top-level vs submenu pages, so check by presence
        // of the page slug as well as the GET param for robustness.
        $is_footer_page = false;
        // If available, use current screen to reliably determine the page
        if (function_exists('get_current_screen')) {
            $screen = get_current_screen();
            if ($screen && (strpos($screen->id, 'ross-theme-footer') !== false || isset($_GET['page']) && $_GET['page'] === 'ross-theme-footer')) {
                $is_footer_page = true;
            }
        }
        if (!$is_footer_page && is_string($hook) && strpos($hook, 'ross-theme-footer') !== false) {
            $is_footer_page = true;
        }
        if (!$is_footer_page && isset($_GET['page']) && $_GET['page'] === 'ross-theme-footer') {
            $is_footer_page = true;
        }

        if ($is_footer_page) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ross_footer_enqueue_scripts: enqueueing footer scripts for hook=' . print_r($hook, true));
            }
            
            // Font Awesome for social icons display in admin
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
            
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            // Media uploader for background image field
            wp_enqueue_media();
            wp_enqueue_script('ross-footer-admin', get_template_directory_uri() . '/assets/js/admin/footer-options.js', array('jquery', 'wp-color-picker'), filemtime(get_template_directory() . '/assets/js/admin/footer-options.js'), true);
            
            // NEW: Enqueue modern template selector JS
            $template_selector_js = get_template_directory() . '/assets/js/admin/footer-template-selector.js';
            if (file_exists($template_selector_js)) {
                wp_enqueue_script('ross-footer-template-selector', get_template_directory_uri() . '/assets/js/admin/footer-template-selector.js', array('jquery'), filemtime($template_selector_js), true);
            }
            
            // Admin UI styling for footer options
            wp_enqueue_style('ross-footer-admin-css', get_template_directory_uri() . '/assets/css/admin/footer-styling-admin.css', array(), '1.0.0');
            
            // NEW: Tooltips and help system CSS
            $tooltips_css = get_template_directory() . '/assets/css/admin/footer-admin-tooltips.css';
            if (file_exists($tooltips_css)) {
                wp_enqueue_style('ross-footer-tooltips', get_template_directory_uri() . '/assets/css/admin/footer-admin-tooltips.css', array(), filemtime($tooltips_css));
            }
            
            // NEW: Enqueue modern template selector CSS
            $template_ui_css = get_template_directory() . '/assets/css/admin/footer-template-ui.css';
            if (file_exists($template_ui_css)) {
                wp_enqueue_style('ross-footer-template-ui', get_template_directory_uri() . '/assets/css/admin/footer-template-ui.css', array(), filemtime($template_ui_css));
            }
            
            // NEW: Enqueue social icons UI CSS
            $social_ui_css = get_template_directory() . '/assets/css/admin/social-icons-ui.css';
            if (file_exists($social_ui_css)) {
                wp_enqueue_style('ross-social-icons-ui', get_template_directory_uri() . '/assets/css/admin/social-icons-ui.css', array('wp-color-picker'), filemtime($social_ui_css));
            }
            
            // NEW: Enqueue social icons manager JS
            $social_ui_js = get_template_directory() . '/assets/js/admin/social-icons-manager.js';
            if (file_exists($social_ui_js)) {
                wp_enqueue_script('ross-social-icons-manager', get_template_directory_uri() . '/assets/js/admin/social-icons-manager.js', array('jquery', 'wp-color-picker'), filemtime($social_ui_js), true);
            }
            
            wp_localize_script('ross-footer-admin', 'rossFooterAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ross_apply_footer_template'),
                'sync_nonce' => wp_create_nonce('ross_sync_footer_templates'),
                'site_url' => home_url('/'),
                'site_name' => get_bloginfo('name'),
                'widgets_url' => admin_url('widgets.php')
            ));
            // enqueue preview CSS
            wp_enqueue_style('ross-footer-preview-css', get_template_directory_uri() . '/assets/css/admin/footer-preview.css', array(), '1.0.0');
            
            // Add inline script for conditional field display
            wp_add_inline_script('ross-footer-admin', "
                jQuery(document).ready(function($) {
                    // Conditional display for copyright fields
                    function toggleCopyrightFields() {
                        var isEnabled = $('input[name=\"ross_theme_footer_options[enable_copyright]\"]').is(':checked');
                        var copyrightFields = [
                            'copyright_text', 'copyright_alignment', 'copyright_bg_color', 
                            'copyright_text_color', 'copyright_font_size', 'copyright_font_weight',
                            'copyright_letter_spacing', 'copyright_padding_top', 'copyright_padding_bottom',
                            'copyright_border_top', 'copyright_border_color', 'copyright_border_width',
                            'copyright_link_color', 'copyright_link_hover_color'
                        ];
                        
                        // Toggle individual copyright fields
                        copyrightFields.forEach(function(field) {
                            var row = $('input[name=\"ross_theme_footer_options[' + field + ']\"], textarea[name=\"ross_theme_footer_options[' + field + ']\"], select[name=\"ross_theme_footer_options[' + field + ']\"]').closest('tr');
                            if (isEnabled) {
                                row.show();
                            } else {
                                row.hide();
                            }
                        });
                        
                        // Toggle entire Advanced section (collapsible box + all fields)
                        var advancedSection = $('.ross-collapsible-section');
                        var advancedFields = $('input[name=\"ross_theme_footer_options[enable_custom_footer]\"], textarea[name=\"ross_theme_footer_options[custom_footer_html]\"], textarea[name=\"ross_theme_footer_options[custom_footer_css]\"], textarea[name=\"ross_theme_footer_options[custom_footer_js]\"]').closest('tr');
                        
                        if (isEnabled) {
                            advancedSection.show();
                            advancedFields.show();
                        } else {
                            advancedSection.hide();
                            advancedFields.hide();
                        }
                    }
                    
                    // Conditional display for custom footer fields (within Advanced section)
                    function toggleCustomFooterFields() {
                        var isEnabled = $('input[name=\"ross_theme_footer_options[enable_custom_footer]\"]').is(':checked');
                        var customFields = ['custom_footer_html', 'custom_footer_css', 'custom_footer_js'];
                        
                        customFields.forEach(function(field) {
                            var row = $('textarea[name=\"ross_theme_footer_options[' + field + ']\"]').closest('tr');
                            if (isEnabled) {
                                row.show();
                            } else {
                                row.hide();
                            }
                        });
                    }
                    
                    // Initialize on page load
                    toggleCopyrightFields();
                    toggleCustomFooterFields();
                    
                    // Toggle on change
                    $('input[name=\"ross_theme_footer_options[enable_copyright]\"]').on('change', toggleCopyrightFields);
                    $('input[name=\"ross_theme_footer_options[enable_custom_footer]\"]').on('change', toggleCustomFooterFields);
                });
            ");
        }
    }
    
    public function register_footer_settings() {
        register_setting(
            'ross_theme_footer_group',
            'ross_theme_footer_options',
            array($this, 'sanitize_footer_options')
        );
        
        $this->add_layout_section();
        $this->add_widgets_section();
        $this->add_styling_section();
        $this->add_cta_section();
        $this->add_social_section();
        $this->add_copyright_section();
    }

    private function add_styling_section() {
        add_settings_section(
            'ross_footer_styling_section',
            '🎨 Footer Styling',
            array($this, 'styling_section_callback'),
            'ross-theme-footer-styling'
        );

        // SECTION 1: Background
        add_settings_field(
            'styling_bg_color',
            'Background Color',
            array($this, 'styling_bg_color_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_gradient',
            'Background Gradient (enable)',
            array($this, 'styling_bg_gradient_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_image',
            'Background Image (URL)',
            array($this, 'styling_bg_image_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // Background type selector (color | image | gradient)
        add_settings_field(
            'styling_bg_type',
            'Background Type',
            array($this, 'styling_bg_type_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // Overlay controls
        add_settings_field(
            'styling_overlay_enabled',
            'Enable Background Overlay',
            array($this, 'styling_overlay_enabled_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_overlay_type',
            'Overlay Type',
            array($this, 'styling_overlay_type_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // Overlay color, image, gradient
        add_settings_field(
            'styling_overlay_color',
            'Overlay Color',
            array($this, 'styling_overlay_color_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_image',
            'Overlay Image (URL)',
            array($this, 'styling_overlay_image_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_gradient_from',
            'Overlay Gradient - From',
            array($this, 'styling_overlay_gradient_from_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_gradient_to',
            'Overlay Gradient - To',
            array($this, 'styling_overlay_gradient_to_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );
        add_settings_field(
            'styling_overlay_opacity',
            'Overlay Opacity',
            array($this, 'styling_overlay_opacity_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_gradient_from',
            'Background Gradient - From',
            array($this, 'styling_bg_gradient_from_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_gradient_to',
            'Background Gradient - To',
            array($this, 'styling_bg_gradient_to_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        add_settings_field(
            'styling_bg_opacity',
            'Background Opacity (0-1)',
            array($this, 'styling_bg_opacity_callback'),
            'ross-theme-footer-styling',
            'ross_footer_styling_section'
        );

        // SECTION 2: Text & Links
        add_settings_field('styling_text_color','Text Color',array($this,'styling_text_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_link_color','Link Color',array($this,'styling_link_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_link_hover','Link Hover Color',array($this,'styling_link_hover_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 3: Typography
        add_settings_field('styling_font_size','Font Size (px)',array($this,'styling_font_size_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_line_height','Line Height',array($this,'styling_line_height_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 4: Spacing
        add_settings_field('styling_col_gap','Column Gap (px)',array($this,'styling_col_gap_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_row_gap','Row Gap (px)',array($this,'styling_row_gap_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_lr','Padding Left / Right (px)',array($this,'styling_padding_lr_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_top','Padding Top (px)',array($this,'styling_padding_top_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_bottom','Padding Bottom (px)',array($this,'styling_padding_bottom_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_left','Padding Left (px)',array($this,'styling_padding_left_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_padding_right','Padding Right (px)',array($this,'styling_padding_right_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 5: Border
        add_settings_field('styling_border_top','Border Top (enable)',array($this,'styling_border_top_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_border_color','Border Color',array($this,'styling_border_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_border_thickness','Border Thickness (px)',array($this,'styling_border_thickness_callback'),'ross-theme-footer-styling','ross_footer_styling_section');

        // SECTION 6: Widget Styling
        add_settings_field('styling_widget_title_color','Widget Title Color',array($this,'styling_widget_title_color_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
        add_settings_field('styling_widget_title_size','Widget Title Font Size (px)',array($this,'styling_widget_title_size_callback'),'ross-theme-footer-styling','ross_footer_styling_section');
    }

    public function styling_section_callback() {
        echo '<p>Fine-grained visual controls for the footer. These settings affect frontend appearance.</p>';
    }

    // Styling field callbacks
    public function styling_bg_color_callback() {
        $v = isset($this->options['styling_bg_color']) ? $this->options['styling_bg_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_bg_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_bg_gradient_from_callback() {
        $v = isset($this->options['styling_bg_gradient_from']) ? $this->options['styling_bg_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_bg_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_bg_gradient_to_callback() {
        $v = isset($this->options['styling_bg_gradient_to']) ? $this->options['styling_bg_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_bg_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_bg_gradient_callback() {
        $v = isset($this->options['styling_bg_gradient']) ? $this->options['styling_bg_gradient'] : 0;
        // Modern toggle switch markup
        $checked = checked(1, $v, false);
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[styling_bg_gradient]" value="1" ' . $checked . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable gradient (uses two template colors)';
        echo '</label>';
    }

    public function styling_bg_image_callback() {
        $v = isset($this->options['styling_bg_image']) ? $this->options['styling_bg_image'] : '';
        echo '<input type="text" id="ross-styling-bg-image" name="ross_theme_footer_options[styling_bg_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-styling-bg-image" data-input-name="ross_theme_footer_options[styling_bg_image]">Upload</button>';
        echo '<input type="hidden" id="ross-styling-bg-image-id" name="ross_theme_footer_options[styling_bg_image_id]" value="' . esc_attr(isset($this->options['styling_bg_image_id']) ? $this->options['styling_bg_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-styling-bg-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
    }

    public function styling_bg_type_callback() {
        $v = isset($this->options['styling_bg_type']) ? $this->options['styling_bg_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[styling_bg_type]" id="styling_bg_type">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function styling_overlay_enabled_callback() {
        $v = isset($this->options['styling_overlay_enabled']) ? $this->options['styling_overlay_enabled'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[styling_overlay_enabled]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable Overlay';
        echo '</label>';
    }

    public function styling_overlay_type_callback() {
        $v = isset($this->options['styling_overlay_type']) ? $this->options['styling_overlay_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[styling_overlay_type]" id="styling_overlay_type">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function styling_overlay_color_callback() {
        $v = isset($this->options['styling_overlay_color']) ? $this->options['styling_overlay_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_overlay_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_overlay_image_callback() {
        $v = isset($this->options['styling_overlay_image']) ? $this->options['styling_overlay_image'] : '';
        echo '<input type="text" id="ross-styling-overlay-image" name="ross_theme_footer_options[styling_overlay_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-styling-overlay-image" data-input-name="ross_theme_footer_options[styling_overlay_image]">Upload</button>';
        echo '<input type="hidden" id="ross-styling-overlay-image-id" name="ross_theme_footer_options[styling_overlay_image_id]" value="' . esc_attr(isset($this->options['styling_overlay_image_id']) ? $this->options['styling_overlay_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-styling-overlay-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
    }

    public function styling_overlay_gradient_from_callback() {
        $v = isset($this->options['styling_overlay_gradient_from']) ? $this->options['styling_overlay_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_overlay_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_overlay_gradient_to_callback() {
        $v = isset($this->options['styling_overlay_gradient_to']) ? $this->options['styling_overlay_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_overlay_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_overlay_opacity_callback() {
        $v = isset($this->options['styling_overlay_opacity']) ? $this->options['styling_overlay_opacity'] : '0.5';
        echo '<input type="number" step="0.1" min="0" max="1" name="ross_theme_footer_options[styling_overlay_opacity]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    public function styling_bg_opacity_callback() {
        $v = isset($this->options['styling_bg_opacity']) ? $this->options['styling_bg_opacity'] : '1';
        echo '<input type="number" step="0.1" min="0" max="1" name="ross_theme_footer_options[styling_bg_opacity]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    // Padding callbacks
    public function styling_padding_top_callback() {
        $v = isset($this->options['styling_padding_top']) ? $this->options['styling_padding_top'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_top]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_bottom_callback() {
        $v = isset($this->options['styling_padding_bottom']) ? $this->options['styling_padding_bottom'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_bottom]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_left_callback() {
        $v = isset($this->options['styling_padding_left']) ? $this->options['styling_padding_left'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_left]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_right_callback() {
        $v = isset($this->options['styling_padding_right']) ? $this->options['styling_padding_right'] : '';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_right]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_text_color_callback() {
        $v = isset($this->options['styling_text_color']) ? $this->options['styling_text_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_text_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_link_color_callback() {
        $v = isset($this->options['styling_link_color']) ? $this->options['styling_link_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_link_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_link_hover_callback() {
        $v = isset($this->options['styling_link_hover']) ? $this->options['styling_link_hover'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_link_hover]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_font_size_callback() {
        $v = isset($this->options['styling_font_size']) ? $this->options['styling_font_size'] : '14';
        echo '<input type="number" name="ross_theme_footer_options[styling_font_size]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_line_height_callback() {
        $v = isset($this->options['styling_line_height']) ? $this->options['styling_line_height'] : '1.6';
        echo '<input type="number" step="0.1" name="ross_theme_footer_options[styling_line_height]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    public function styling_col_gap_callback() {
        $v = isset($this->options['styling_col_gap']) ? $this->options['styling_col_gap'] : '24';
        echo '<input type="number" name="ross_theme_footer_options[styling_col_gap]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_row_gap_callback() {
        $v = isset($this->options['styling_row_gap']) ? $this->options['styling_row_gap'] : '18';
        echo '<input type="number" name="ross_theme_footer_options[styling_row_gap]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_padding_lr_callback() {
        $v = isset($this->options['styling_padding_lr']) ? $this->options['styling_padding_lr'] : '20';
        echo '<input type="number" name="ross_theme_footer_options[styling_padding_lr]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_border_top_callback() {
        $v = isset($this->options['styling_border_top']) ? $this->options['styling_border_top'] : 0;
        $checked = checked(1, $v, false);
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[styling_border_top]" value="1" ' . $checked . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable top border';
        echo '</label>';
    }

    public function styling_border_color_callback() {
        $v = isset($this->options['styling_border_color']) ? $this->options['styling_border_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_border_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_border_thickness_callback() {
        $v = isset($this->options['styling_border_thickness']) ? $this->options['styling_border_thickness'] : '1';
        echo '<input type="number" name="ross_theme_footer_options[styling_border_thickness]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function styling_widget_title_color_callback() {
        $v = isset($this->options['styling_widget_title_color']) ? $this->options['styling_widget_title_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[styling_widget_title_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function styling_widget_title_size_callback() {
        $v = isset($this->options['styling_widget_title_size']) ? $this->options['styling_widget_title_size'] : '16';
        echo '<input type="number" name="ross_theme_footer_options[styling_widget_title_size]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    
    private function add_layout_section() {
        add_settings_section(
            'ross_footer_layout_section',
            '🧱 Footer Layout',
            array($this, 'layout_section_callback'),
            'ross-theme-footer-layout'
        );
        
        // Footer Style option removed: layout selection handled by theme templates.

        
        add_settings_field(
            'footer_template',
            'Footer Template',
            array($this, 'footer_template_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'use_template_content',
            'Use Template Content',
            array($this, 'use_template_content_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );
        
        add_settings_field(
            'footer_columns',
            'Footer Columns',
            array($this, 'footer_columns_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );        add_settings_field(
            'footer_width',
            'Footer Width',
            array($this, 'footer_width_callback'),
            'ross-theme-footer-layout',
            'ross_footer_layout_section'
        );
        
        
    }
    
    private function add_widgets_section() {
        // Widget styling section removed - simplified UI
        // Colors are controlled via Styling tab
    }
    
    private function add_cta_section() {
        // Create multiple CTA sub-sections (visibility, content, layout, styling, spacing, animation, advanced)
        add_settings_section(
            'ross_footer_cta_visibility',
            'Visibility',
            array($this, 'cta_visibility_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_content',
            'Content',
            array($this, 'cta_content_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_layout',
            'Layout',
            array($this, 'cta_layout_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_styling',
            'Styling',
            array($this, 'cta_styling_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_typography',
            'Typography',
            array($this, 'cta_typography_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_spacing',
            'Spacing & Dimensions',
            array($this, 'cta_spacing_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_animation',
            'Animation',
            array($this, 'cta_animation_section_callback'),
            'ross-theme-footer-cta'
        );
        add_settings_section(
            'ross_footer_cta_advanced',
            'Advanced',
            array($this, 'cta_advanced_section_callback'),
            'ross-theme-footer-cta'
        );
        
        // Visibility
        add_settings_field(
            'enable_footer_cta',
            'Enable CTA Section',
            array($this, 'enable_footer_cta_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_visibility'
        );
        add_settings_field(
            'cta_always_visible',
            'Always Show CTA',
            array($this, 'cta_always_visible_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_visibility'
        );
        add_settings_field(
            'cta_display_on',
            'Display On',
            array($this, 'cta_display_on_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_visibility'
        );

        // Content
        add_settings_field(
            'cta_title',
            'CTA Title',
            array($this, 'cta_title_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_text',
            'CTA Subtitle / Text',
            array($this, 'cta_text_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_button_text',
            'CTA Button Text',
            array($this, 'cta_button_text_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_button_url',
            'Button URL',
            array($this, 'cta_button_url_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );
        add_settings_field(
            'cta_icon',
            'CTA Icon',
            array($this, 'cta_icon_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_content'
        );

        // Layout
        add_settings_field(
            'cta_alignment',
            'Alignment',
            array($this, 'cta_alignment_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_direction',
            'Layout Direction',
            array($this, 'cta_layout_direction_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_wrap',
            'Allow Wrap',
            array($this, 'cta_layout_wrap_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_justify',
            'Justify',
            array($this, 'cta_layout_justify_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_layout_align',
            'Align Items',
            array($this, 'cta_layout_align_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );
        add_settings_field(
            'cta_gap',
            'Gap Between Items (px)',
            array($this, 'cta_gap_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_layout'
        );

        // Styling
        add_settings_field(
            'cta_bg_type',
            'Background Type',
            array($this, 'cta_bg_type_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        // CTA Overlay controls
        add_settings_field('cta_overlay_enabled','Enable CTA Overlay',array($this,'cta_overlay_enabled_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_type','Overlay Type',array($this,'cta_overlay_type_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_color','Overlay Color',array($this,'cta_overlay_color_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_image','Overlay Image (URL)',array($this,'cta_overlay_image_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_gradient_from','Overlay Gradient - From',array($this,'cta_overlay_gradient_from_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_gradient_to','Overlay Gradient - To',array($this,'cta_overlay_gradient_to_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field('cta_overlay_opacity','Overlay Opacity (0-1)',array($this,'cta_overlay_opacity_callback'),'ross-theme-footer-cta','ross_footer_cta_styling');
        add_settings_field(
            'cta_bg_color',
            'CTA Background Color',
            array($this, 'cta_bg_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_bg_gradient_from',
            'CTA Gradient - From',
            array($this, 'cta_bg_gradient_from_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_bg_gradient_to',
            'CTA Gradient - To',
            array($this, 'cta_bg_gradient_to_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_bg_image',
            'CTA Background Image',
            array($this, 'cta_bg_image_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_text_color',
            'CTA Text Color',
            array($this, 'cta_text_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_button_bg_color',
            'CTA Button Background',
            array($this, 'cta_button_bg_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_button_text_color',
            'CTA Button Text Color',
            array($this, 'cta_button_text_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        // Typography
        add_settings_field(
            'cta_title_font_size',
            'Title Font Size (px)',
            array($this, 'cta_title_font_size_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_typography'
        );
        add_settings_field(
            'cta_title_font_weight',
            'Title Font Weight',
            array($this, 'cta_title_font_weight_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_typography'
        );
        add_settings_field(
            'cta_text_font_size',
            'Text Font Size (px)',
            array($this, 'cta_text_font_size_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_typography'
        );
        add_settings_field(
            'cta_button_font_size',
            'Button Font Size (px)',
            array($this, 'cta_button_font_size_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_typography'
        );
        add_settings_field(
            'cta_button_font_weight',
            'Button Font Weight',
            array($this, 'cta_button_font_weight_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_typography'
        );
        add_settings_field(
            'cta_letter_spacing',
            'Letter Spacing (px)',
            array($this, 'cta_letter_spacing_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_typography'
        );
        
        // Spacing (padding)
        add_settings_field(
            'cta_padding_top',
            'Padding Top (px)',
            array($this, 'cta_padding_top_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_padding_right',
            'Padding Right (px)',
            array($this, 'cta_padding_right_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_padding_bottom',
            'Padding Bottom (px)',
            array($this, 'cta_padding_bottom_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_padding_left',
            'Padding Left (px)',
            array($this, 'cta_padding_left_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );

        add_settings_field(
            'cta_icon_color',
            'CTA Icon Color',
            array($this, 'cta_icon_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        
        // Border controls
        add_settings_field(
            'cta_border_width',
            'Border Width (px)',
            array($this, 'cta_border_width_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_border_style',
            'Border Style',
            array($this, 'cta_border_style_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_border_color',
            'Border Color',
            array($this, 'cta_border_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_border_radius',
            'Border Radius (px)',
            array($this, 'cta_border_radius_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        
        // Shadow controls
        add_settings_field(
            'cta_box_shadow',
            'Enable Box Shadow',
            array($this, 'cta_box_shadow_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_shadow_color',
            'Shadow Color',
            array($this, 'cta_shadow_color_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_shadow_blur',
            'Shadow Blur (px)',
            array($this, 'cta_shadow_blur_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        
        // Button hover effects
        add_settings_field(
            'cta_button_hover_bg',
            'Button Hover Background',
            array($this, 'cta_button_hover_bg_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_button_hover_text',
            'Button Hover Text Color',
            array($this, 'cta_button_hover_text_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );
        add_settings_field(
            'cta_button_border_radius',
            'Button Border Radius (px)',
            array($this, 'cta_button_border_radius_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_styling'
        );

        // Animation
        add_settings_field(
            'cta_animation',
            'CTA Animation',
            array($this, 'cta_animation_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_animation'
        );
        add_settings_field(
            'cta_anim_delay',
            'Animation Delay (ms)',
            array($this, 'cta_anim_delay_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_animation'
        );
        add_settings_field(
            'cta_anim_duration',
            'Animation Duration (ms)',
            array($this, 'cta_anim_duration_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_animation'
        );

        // Advanced
        add_settings_field(
            'enable_custom_cta',
            'Enable Custom CTA Content',
            array($this, 'enable_custom_cta_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );
        add_settings_field(
            'custom_cta_html',
            'Custom CTA HTML',
            array($this, 'custom_cta_html_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );
        add_settings_field(
            'custom_cta_css',
            'Custom CTA CSS',
            array($this, 'custom_cta_css_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );
        add_settings_field(
            'custom_cta_js',
            'Custom CTA JS',
            array($this, 'custom_cta_js_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_advanced'
        );

        // Container width
        add_settings_field(
            'cta_container_width',
            'Container Width',
            array($this, 'cta_container_width_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        add_settings_field(
            'cta_max_width',
            'Max Width (px)',
            array($this, 'cta_max_width_callback'),
            'ross-theme-footer-cta',
            'ross_footer_cta_spacing'
        );
        
        // Margin controls
        add_settings_field('cta_margin_top','Margin Top (px)',array($this,'cta_margin_top_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
        add_settings_field('cta_margin_right','Margin Right (px)',array($this,'cta_margin_right_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
        add_settings_field('cta_margin_bottom','Margin Bottom (px)',array($this,'cta_margin_bottom_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
        add_settings_field('cta_margin_left','Margin Left (px)',array($this,'cta_margin_left_callback'),'ross-theme-footer-cta','ross_footer_cta_spacing');
    }
    
    private function add_social_section() {
        add_settings_section(
            'ross_footer_social_section',
            '🌍 Social Icons',
            array($this, 'social_section_callback'),
            'ross-theme-footer-social'
        );
        
        add_settings_field(
            'enable_social_icons',
            'Enable Social Icons',
            array($this, 'enable_social_icons_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icons_list',
            'Social Media Links',
            array($this, 'social_icons_list_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icon_style',
            'Icon Style',
            array($this, 'social_icon_style_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icon_size',
            'Icon Size',
            array($this, 'social_icon_size_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icon_color',
            'Icon Color',
            array($this, 'social_icon_color_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
        
        add_settings_field(
            'social_icon_hover_color',
            'Icon Hover Color',
            array($this, 'social_icon_hover_color_callback'),
            'ross-theme-footer-social',
            'ross_footer_social_section'
        );
    }
    
    private function add_copyright_section() {
        add_settings_section(
            'ross_footer_copyright_section',
            '🧾 Copyright Bar',
            array($this, 'copyright_section_callback'),
            'ross-theme-footer-copyright'
        );
        // Create sub-sections to organize fields
        add_settings_section(
            'ross_footer_copyright_visibility',
            'Visibility',
            array($this, 'copyright_visibility_section_callback'),
            'ross-theme-footer-copyright'
        );
        add_settings_section(
            'ross_footer_copyright_content',
            'Content',
            array($this, 'copyright_content_section_callback'),
            'ross-theme-footer-copyright'
        );
        add_settings_section(
            'ross_footer_copyright_styling',
            'Styling',
            array($this, 'copyright_styling_section_callback'),
            'ross-theme-footer-copyright'
        );
        add_settings_section(
            'ross_footer_copyright_advanced',
            'Advanced / Custom',
            array($this, 'copyright_advanced_section_callback'),
            'ross-theme-footer-copyright'
        );

        // Visibility
        add_settings_field(
            'enable_copyright',
            'Enable Copyright',
            array($this, 'enable_copyright_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_visibility'
        );
        add_settings_field(
            'copyright_alignment',
            'Alignment',
            array($this, 'copyright_alignment_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_visibility'
        );

        // Content
        add_settings_field(
            'copyright_text',
            'Copyright Text',
            array($this, 'copyright_text_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_content'
        );

        // Styling
        add_settings_field(
            'copyright_bg_color',
            'Background Color',
            array($this, 'copyright_bg_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_text_color',
            'Text Color',
            array($this, 'copyright_text_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_font_size',
            'Font Size (px)',
            array($this, 'copyright_font_size_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_font_weight',
            'Font Weight',
            array($this, 'copyright_font_weight_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_letter_spacing',
            'Letter Spacing (px)',
            array($this, 'copyright_letter_spacing_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_padding_top',
            'Padding Top (px)',
            array($this, 'copyright_padding_top_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_padding_bottom',
            'Padding Bottom (px)',
            array($this, 'copyright_padding_bottom_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_border_top',
            'Top Border',
            array($this, 'copyright_border_top_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_border_color',
            'Border Color',
            array($this, 'copyright_border_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_border_width',
            'Border Width (px)',
            array($this, 'copyright_border_width_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_link_color',
            'Link Color',
            array($this, 'copyright_link_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );
        add_settings_field(
            'copyright_link_hover_color',
            'Link Hover Color',
            array($this, 'copyright_link_hover_color_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_styling'
        );

        // Custom footer area inside copyright/tab so it's easy to find
        // Advanced / Custom
        add_settings_field(
            'enable_custom_footer',
            'Enable Custom Footer',
            array($this, 'enable_custom_footer_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
        add_settings_field(
            'custom_footer_html',
            'Custom Footer HTML',
            array($this, 'custom_footer_html_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
        add_settings_field(
            'custom_footer_css',
            'Custom Footer CSS',
            array($this, 'custom_footer_css_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
        add_settings_field(
            'custom_footer_js',
            'Custom Footer JS',
            array($this, 'custom_footer_js_callback'),
            'ross-theme-footer-copyright',
            'ross_footer_copyright_advanced'
        );
    }
    
    // Section Callbacks
    public function layout_section_callback() {
        echo '<p>Control the overall structure and layout of your footer.</p>';
    }
    
    public function widgets_section_callback() {
        echo '<p>Customize widget area colors. These apply when "Use Template Content" is unchecked.</p>';
    }
    
    public function cta_section_callback() {
        echo '<p>Add a call-to-action section above the main footer.</p>';
    }

    public function cta_visibility_section_callback() {
        echo '<p>Control where and when the CTA appears on your site.</p>';
    }
    public function cta_content_section_callback() {
        echo '<p>Content for the CTA: title, subtitle, button and icon.</p>';
    }
    public function cta_layout_section_callback() {
        echo '<p>Layout and structure of CTA: alignment, direction, and spacing.</p>';
    }
    public function cta_styling_section_callback() {
        echo '<p>Visual styling for the CTA, including background and colors.</p>';
    }
    public function cta_spacing_section_callback() {
        echo '<p>💎 Control spacing, margins, padding, and container dimensions for the CTA block.</p>';
    }
    public function cta_typography_section_callback() {
        echo '<p>✍️ Font sizes, weights, and letter spacing for CTA elements.</p>';
    }
    public function cta_animation_section_callback() {
        echo '<p>✨ Entrance animations for the CTA appearance.</p>';
    }
    public function cta_advanced_section_callback() {
        echo '<p>Advanced options: custom HTML/CSS/JS for the CTA area.</p>';
    }
    
    public function social_section_callback() {
        echo '<p>Configure social media icons and links.</p>';
    }
    
    public function copyright_section_callback() {
        // No description needed
    }

    public function template_diagnostics_callback() {
        // Show a compact diagnostics summary of relevant options and footer sidebars
        echo '<div style="max-width:900px; font-family:monospace; background:#f8f8f8; padding:10px; border-radius:6px;">';
        $opts = get_option('ross_theme_footer_options', array());
        $tpls = get_option('ross_theme_footer_templates', array());
        $backups = get_option('ross_footer_template_backups', array());
        $sidebars = get_option('sidebars_widgets', array());

        echo '<strong>Footer Options</strong>';
        echo '<pre style="white-space:pre-wrap; margin-top:6px;">' . esc_html(print_r(array_intersect_key($opts, array('enable_widgets' => '', 'footer_template' => '', 'footer_columns' => '', 'enable_custom_footer' => '')), true)) . '</pre>';

        echo '<strong>Templates (keys)</strong>';
        $tpl_keys = is_array($tpls) ? array_keys($tpls) : array();
        echo '<div style="margin-top:6px;">' . esc_html(implode(', ', $tpl_keys ?: array('<none>'))) . '</div>';

        echo '<strong>Footer sidebars</strong>';
        for ($i = 1; $i <= 4; $i++) {
            $key = 'footer-' . $i;
            $widgets = isset($sidebars[$key]) && is_array($sidebars[$key]) ? $sidebars[$key] : array();
            echo '<div style="margin-top:6px;"><span style="display:inline-block;width:80px;font-weight:600;">' . esc_html($key) . '</span> ' . esc_html(implode(', ', $widgets ?: array('<empty>'))) . '</div>';
        }

        echo '<strong>Widget options counts</strong>';
        $custom_html = get_option('widget_custom_html', array());
        $text = get_option('widget_text', array());
        echo '<div style="margin-top:6px;">custom_html: ' . esc_html(is_array($custom_html) ? count(array_filter(array_keys($custom_html), 'is_numeric')) : 0) . ' | text: ' . esc_html(is_array($text) ? count(array_filter(array_keys($text), 'is_numeric')) : 0) . '</div>';

        echo '<div style="margin-top:10px;"><em>Tip: If widgets are present but not visible, check the "Enable Widgets Area" toggle and the "Footer Columns" setting in the Layout tab, or clear any caching plugins.</em></div>';
        echo '</div>';
    }
    
    public function footer_template_callback() {
        // Get templates and determine selected value
        $templates = $this->get_templates();
        $template_keys = array_keys($templates);
        $first_key = isset($template_keys[0]) ? $template_keys[0] : 'business-professional';
        $value = isset($this->options['footer_template']) && isset($templates[$this->options['footer_template']]) ? $this->options['footer_template'] : $first_key;
        ?>
        <div class="ross-footer-templates">
            <?php
            foreach ($templates as $id => $tpl) {
                $title = isset($tpl['title']) ? $tpl['title'] : $id;
                $description = isset($tpl['description']) ? $tpl['description'] : '';
                $icon = isset($tpl['icon']) ? $tpl['icon'] : '📄';
                $columns = isset($tpl['columns']) ? $tpl['columns'] : 4;
                ?>
                <div class="ross-template-card <?php echo ($value === $id) ? 'selected' : ''; ?>">
                    <input type="radio" name="ross_theme_footer_options[footer_template]" value="<?php echo esc_attr($id); ?>" <?php checked($value, $id); ?> id="template-<?php echo esc_attr($id); ?>" />
                    <div class="ross-template-card-header">
                        <span class="ross-template-icon"><?php echo $icon; ?></span>
                        <h3 class="ross-template-title"><?php echo esc_html($title); ?></h3>
                    </div>
                    <div class="ross-template-card-body">
                        <p class="ross-template-description"><?php echo esc_html($description); ?></p>
                        <div class="ross-template-meta">
                            <span class="ross-template-meta-item">
                                <span>📐</span> <?php echo absint($columns); ?> Column<?php echo ($columns > 1) ? 's' : ''; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="ross-template-actions">
            <button type="button" class="button" id="ross-preview-template">👁️ Preview Selected Template</button>
            <button type="button" class="button button-primary" id="ross-apply-template">✅ Apply Template</button>
            <button type="button" class="button" id="ross-sync-templates">🔄 Sync Templates</button>
            <span class="description">Preview first, then click Apply to update your footer with the selected template.</span>
        </div>

        <div id="ross-template-preview" style="display:none;"></div>

        <?php // Hidden previews for quick client-side display ?>
        <div id="ross-hidden-previews" style="display:none;">
            <?php
            foreach (array_keys($templates) as $tpl_id) {
                echo $this->get_template_preview_html($tpl_id);
            }
            ?>
        </div>
        
        <!-- Sync modal container -->
        <div id="ross-sync-modal" style="display:none;"></div>

        <div id="ross-footer-backups" style="margin-top:1rem;">
            <h4>Recent Footer Backups</h4>
            <?php echo $this->render_backups_list_html(); ?>
        </div>

        <!-- Confirmation modal -->
        <div id="ross-confirm-modal" style="display:none;">
            <div class="ross-confirm-overlay" style="position:fixed;left:0;top:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9998;display:flex;align-items:center;justify-content:center;">
                <div class="ross-confirm-box" style="background:#fff;padding:20px;border-radius:6px;max-width:520px;width:100%;box-shadow:0 6px 18px rgba(0,0,0,0.2);">
                    <div class="ross-confirm-message" style="margin-bottom:16px;font-size:15px;color:#222;"></div>
                    <div style="text-align:right;">
                        <button type="button" class="button" id="ross-confirm-cancel">Cancel</button>
                        <button type="button" class="button button-primary" id="ross-confirm-ok" style="margin-left:8px;">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Callback for use_template_content toggle
     * When enabled, displays template content instead of WordPress widget areas
     */
    public function use_template_content_callback() {
        $checked = isset($this->options['use_template_content']) ? intval($this->options['use_template_content']) : 0;
        ?>
        <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 10px;">
            <label style="display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <input type="checkbox" name="ross_theme_footer_options[use_template_content]" value="1" <?php checked($checked, 1); ?> />
                <span>Use Template Content</span>
            </label>
            <p class="description" style="margin: 10px 0 0 0; padding-left: 30px;">
                <strong>✓ Checked:</strong> Footer displays the selected template design above<br>
                <strong>✗ Unchecked:</strong> Footer displays WordPress widget areas (Appearance → Widgets)
            </p>
        </div>
        <?php
    }

    /**
     * Return array of backups (most recent first)
     */
    private function get_backups() {
        $backups = get_option('ross_footer_template_backups', array());
        if (!is_array($backups)) $backups = array();
        return $backups;
    }

    private function save_backups($backups) {
        update_option('ross_footer_template_backups', $backups);
    }

    private function render_backups_list_html() {
        $backups = $this->get_backups();
        if (empty($backups)) {
            return '<p><em>No backups yet. Applying a template will create a backup.</em></p>';
        }

        $html = '<table class="widefat" style="max-width:800px;"><thead><tr><th>When</th><th>User</th><th>Template</th><th>Actions</th></tr></thead><tbody>';
        foreach ($backups as $b) {
            $id = esc_attr($b['id']);
            $when = esc_html($b['timestamp']);
            $user = isset($b['user_display']) ? esc_html($b['user_display']) : esc_html($b['user_id']);
            $template = isset($b['template']) ? esc_html($b['template']) : '';
            $html .= '<tr data-backup-id="' . $id . '"><td>' . $when . '</td><td>' . $user . '</td><td>' . $template . '</td><td><button class="button ross-restore-backup" data-backup-id="' . $id . '">Restore</button> <button class="button ross-delete-backup" data-backup-id="' . $id . '">Delete</button></td></tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }

    /**
     * Return HTML for a preview of a template (safe for admin display)
     */
    private function get_template_preview_html($tpl) {
        $samples = $this->get_templates();

        if (!isset($samples[$tpl])) return '';
        $s = $samples[$tpl];

        // Compute columns count and theme color variables
        $cols_count = isset($s['columns']) ? intval($s['columns']) : (is_array($s['cols']) ? count($s['cols']) : 4);
        $accent = isset($s['accent']) ? $s['accent'] : '';
        $text_color = isset($s['text']) ? $s['text'] : '';
        $bg = isset($s['bg']) ? $s['bg'] : '';
        $style_vars = '';
        if ($accent) $style_vars .= '--ross-accent:' . esc_attr($accent) . ';';
        if ($text_color) $style_vars .= '--ross-text:' . esc_attr($text_color) . ';';
        if ($bg) $style_vars .= '--ross-bg:' . esc_attr($bg) . ';';

        // Wrap preview with id and data-template so admin JS can find it
        // Use semantic markup and classes so we can style it in admin CSS
        $html = '<footer id="ross-preview-' . esc_attr($tpl) . '" class="ross-footer-preview ross-footer-preview--' . esc_attr($tpl) . ' ross-footer-preview-columns--' . intval($cols_count) . '" data-template="' . esc_attr($tpl) . '" style="' . esc_attr($style_vars) . '">';
        $html .= '<div class="ross-footer-preview-inner">';
        // CTA (if present) - render above the columns
        if (!empty($s['cta']) && is_array($s['cta'])) {
            $cta = $s['cta'];
            $html .= '<div class="footer-cta" style="margin-bottom:12px;background:' . esc_attr(isset($s['accent']) ? $s['accent'] : '#eee') . ';padding:12px;border-radius:6px;color:' . esc_attr(isset($s['text']) ? $s['text'] : '#fff') . '">';
            $html .= '<div class="footer-cta-inner"><div class="footer-cta-text"><strong>' . esc_html($cta['title'] ?? '') . '</strong><div>' . esc_html($cta['subtitle'] ?? '') . '</div></div>';
            if (!empty($cta['button_text'])) {
                $html .= '<div class="footer-cta-btn" style="margin-left:12px;"><a class="btn" href="' . esc_url($cta['button_url'] ?? '#') . '" style="background:#001946;color:#fff;padding:6px 10px;border-radius:6px;text-decoration:none;">' . esc_html($cta['button_text']) . '</a></div>';
            }
            $html .= '</div></div>';
        }

        $html .= '<div class="ross-footer-preview-columns">';
        foreach ($s['cols'] as $col) {
            $html .= '<div class="ross-footer-preview-col">';
            if (is_array($col)) {
                $title = isset($col['title']) ? $col['title'] : '';
                $html .= '<h4>' . esc_html($title) . '</h4>';
                if (!empty($col['html'])) {
                    $html .= '<div class="col-html">' . $col['html'] . '</div>';
                } elseif (isset($col['items']) && is_array($col['items'])) {
                    $html .= '<ul class="col-list">';
                    foreach ($col['items'] as $item) {
                        $html .= '<li>' . esc_html($item) . '</li>';
                    }
                    $html .= '</ul>';
                } elseif (isset($col['social']) && is_array($col['social'])) {
                    $html .= '<div class="col-social">';
                    foreach ($col['social'] as $sicon) {
                        $html .= '<a href="#" class="social">' . esc_html($sicon) . '</a> ';
                    }
                    $html .= '</div>';
                } else {
                    $html .= '<p>' . esc_html(isset($col['content']) ? $col['content'] : '') . '</p>';
                }
            } else {
                $parts = explode('|', $col, 2);
                $title = isset($parts[0]) ? $parts[0] : '';
                $content = isset($parts[1]) ? $parts[1] : '';
                $html .= '<h4>' . esc_html($title) . '</h4>';
                if (strpos($content, ',') !== false) {
                    $items = array_map('trim', explode(',', $content));
                    $html .= '<ul>';
                    foreach ($items as $item) {
                        $html .= '<li>' . esc_html($item) . '</li>';
                    }
                    $html .= '</ul>';
                } else {
                    $html .= '<p>' . esc_html($content) . '</p>';
                }
            }
            $html .= '</div>';
        }
        $html .= '</div>'; // .ross-footer-preview-columns
        $html .= '<div class="ross-footer-preview-bottom">';
        $html .= '<div class="ross-footer-preview-copyright">&copy; ' . date('Y') . ' ' . esc_html(get_bloginfo('name')) . '</div>';
        $html .= '<div class="ross-footer-preview-social">';
        $html .= '<a href="#" class="social"><span style="margin-right:4px;">🔗</span>LinkedIn</a> <a href="#" class="social"><span style="margin-right:4px;">📘</span>Facebook</a>';
        $html .= '</div>'; // .ross-footer-preview-social
        $html .= '</div>'; // .ross-footer-preview-bottom
        $html .= '</div>'; // .ross-footer-preview-inner
        $html .= '</footer>';

        return $html;
    }

    /**
     * AJAX handler: create sample widgets and assign to footer sidebars
     */
    public function ajax_apply_footer_template() {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_apply_footer_template called by user id=' . get_current_user_id() . ' nonce=' . (isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 'none') . ' template=' . (isset($_POST['template']) ? sanitize_text_field($_POST['template']) : 'none'));
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $tpl = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : 'template1';

        // Sample column contents (same structure as previews)
        $samples = $this->get_templates();

        if (!isset($samples[$tpl])) {
            wp_send_json_error('Invalid template');
        }

        $cols = $samples[$tpl]['cols'];

        // Backup current footer widgets and relevant widget options so we can undo
        $backup = array(
            'id' => uniqid('rossbk_'),
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'user_display' => wp_get_current_user() ? wp_get_current_user()->display_name : '',
            'template' => $tpl,
            'sidebars_widgets' => get_option('sidebars_widgets'),
            'widget_options' => array(),
        );

        // capture all widget_* options for safer restore (only keys starting with widget_)
        global $wpdb;
        $all_options = wp_load_alloptions();
        foreach ($all_options as $opt_key => $opt_val) {
            if (strpos($opt_key, 'widget_') === 0) {
                $backup['widget_options'][$opt_key] = get_option($opt_key);
            }
        }

        // Store into backups array (most recent first) and rotate to keep last 10
        $backups = get_option('ross_footer_template_backups', array());
        if (!is_array($backups)) $backups = array();
        array_unshift($backups, $backup);
        // keep last 10
        $backups = array_slice($backups, 0, 10);
        update_option('ross_footer_template_backups', $backups);

        // Pick a widget base - prefer custom_html, fallback to text
        $widget_base = null;
        // Prefer custom_html if it exists; otherwise fallback to text widget
        if (get_option('widget_custom_html') !== false) {
            $widget_base = 'custom_html';
            $widget_option_name = 'widget_custom_html';
        } else {
            $widget_base = 'text';
            $widget_option_name = 'widget_text';
        }

        $widgets = get_option($widget_option_name, array());
        if (!is_array($widgets)) $widgets = array();

        // Ensure _multiwidget flag exists
        if (!isset($widgets['_multiwidget'])) $widgets['_multiwidget'] = 1;

        // Find next numeric index
        $next_index = 1;
        foreach ($widgets as $k => $v) {
            if (is_numeric($k) && intval($k) >= $next_index) $next_index = intval($k) + 1;
        }

        $created_ids = array();
        foreach ($cols as $i => $col) {
            $title = '';
            $content_html = '';
            $plain_text = '';
            // Structured column
            if (is_array($col)) {
                $title = isset($col['title']) ? $col['title'] : '';
                if (!empty($col['html'])) {
                    $content_html = $col['html'];
                    $plain_text = strip_tags($col['html']);
                } elseif (isset($col['items']) && is_array($col['items'])) {
                    $content_html .= '<ul>';
                    $plain_text_items = array();
                    foreach ($col['items'] as $it) {
                        $content_html .= '<li>' . esc_html($it) . '</li>';
                        $plain_text_items[] = $it;
                    }
                    $content_html .= '</ul>';
                    $plain_text = implode(', ', $plain_text_items);
                } elseif (isset($col['content'])) {
                    $content_html = '<p>' . esc_html($col['content']) . '</p>';
                    $plain_text = $col['content'];
                }
                // If CTA defined and this is first column, append CTA HTML to first column
                if ($i === 0 && !empty($samples[$tpl]['cta']) && is_array($samples[$tpl]['cta'])) {
                    $cta = $samples[$tpl]['cta'];
                    $cta_html = '<div class="footer-cta">';
                    $cta_html .= '<strong>' . esc_html($cta['title'] ?? '') . '</strong>';
                    $cta_html .= '<div class="cta-subtitle">' . esc_html($cta['subtitle'] ?? '') . '</div>';
                    if (!empty($cta['button_text'])) {
                        $cta_html .= '<div class="cta-button"><a class="btn" href="' . esc_url($cta['button_url'] ?? '#') . '">' . esc_html($cta['button_text']) . '</a></div>';
                    }
                    $cta_html .= '</div>';
                    $content_html .= $cta_html;
                    $plain_text .= ' ' . ($cta['title'] ?? '') . ' ' . ($cta['subtitle'] ?? '');
                }
            } else {
                // Legacy string column support
                $parts = explode('|', $col, 2);
                $title = isset($parts[0]) ? $parts[0] : '';
                $content = isset($parts[1]) ? $parts[1] : '';
                if (strpos($content, ',') !== false) {
                    $items = array_map('trim', explode(',', $content));
                    $content_html .= '<ul>';
                    foreach ($items as $it) { $content_html .= '<li>' . esc_html($it) . '</li>'; }
                    $content_html .= '</ul>';
                    $plain_text = implode(', ', $items);
                } else {
                    $content_html = '<p>' . esc_html($content) . '</p>';
                    $plain_text = $content;
                }
            }

            $widget_data = array();
            if ($widget_base === 'custom_html') {
                $clean_html = $content_html ? wp_kses_post($content_html) : '<p>' . esc_html($plain_text) . '</p>';
                $widget_data = array('title' => $title, 'content' => $clean_html);
            } else {
                $widget_data = array('title' => $title, 'text' => $plain_text, 'filter' => 0);
            }

            $widgets[$next_index] = $widget_data;

            // Record created widget unique id like custom_html-5
            $created_ids[] = $widget_base . '-' . $next_index;

            $next_index++;
        }

        // Update widget option
        update_option($widget_option_name, $widgets);

        // Assign widgets to footer sidebars
        $sidebars = get_option('sidebars_widgets', array());
        if (!is_array($sidebars)) $sidebars = array();

        // Clear existing footer-* sidebars and assign new created widgets per column
        for ($i = 1; $i <= 4; $i++) {
            $key = 'footer-' . $i;
            if (isset($created_ids[$i - 1])) {
                $sidebars[$key] = array($created_ids[$i - 1]);
            } else {
                // empty
                $sidebars[$key] = array();
            }
        }

        update_option('sidebars_widgets', $sidebars);

        // Update footer options so the front-end shows the correct template/columns
        $footer_opts = get_option('ross_theme_footer_options', array());
        if (!is_array($footer_opts)) $footer_opts = array();
        // Ensure widgets are enabled so they display
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_apply_footer_template: before footer_options=' . print_r($footer_opts, true));
        $footer_opts['enable_widgets'] = 1;
        // Save which template applied
        $footer_opts['footer_template'] = $tpl;
        
        // Enable template content display by default when applying a template
        $footer_opts['use_template_content'] = 1;

        // Derive recommended footer columns based on known template behavior
        $template_map = array(
            'business-professional' => array('columns' => 4, 'social_align' => 'center'),
            'ecommerce' => array('columns' => 4, 'social_align' => 'center'),
            'creative-agency' => array('columns' => 4, 'social_align' => 'center'),
            'minimal-modern' => array('columns' => 1, 'social_align' => 'left'),
            // Legacy fallbacks
            'template1' => array('columns' => 4, 'social_align' => 'center'),
            'template2' => array('columns' => 4, 'social_align' => 'center'),
            'template3' => array('columns' => 4, 'social_align' => 'center'),
            'template4' => array('columns' => 1, 'social_align' => 'left'),
        );
        $mapped = isset($template_map[$tpl]) ? $template_map[$tpl] : array('columns' => 4);
        // Prefer explicit columns metadata in the template definition if present
        $footer_opts['footer_columns'] = (isset($samples[$tpl]['columns']) && intval($samples[$tpl]['columns']) > 0) ? intval($samples[$tpl]['columns']) : (isset($mapped['columns']) ? $mapped['columns'] : 4);
        update_option('ross_theme_footer_options', $footer_opts);
        // Set a transient to protect against subsequent form-save overwriting these values
        set_transient('ross_footer_template_applied', $footer_opts, 30);
        set_transient('ross_template_applied_notice', 1, 30);
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_apply_footer_template: after footer_options=' . print_r(get_option('ross_theme_footer_options', array()), true));

        $new_backups_html = $this->render_backups_list_html();
        $saved_footer_options = get_option('ross_theme_footer_options', array());
        wp_send_json_success(array('message' => 'Template applied', 'created' => $created_ids, 'backup_created' => true, 'backup_id' => $backup['id'], 'backups_html' => $new_backups_html, 'footer_options' => $saved_footer_options));
    }

    /**
     * AJAX handler: restore backup created before applying template
     */
    public function ajax_restore_footer_backup() {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_restore_footer_backup called by user id=' . get_current_user_id() . ' nonce=' . (isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 'none') . ' backup_id=' . (isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : 'none'));
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');
        $id = isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : '';

        $backups = get_option('ross_footer_template_backups', array());
        if (!is_array($backups) || empty($backups)) {
            wp_send_json_error('No backups found');
        }

        // find backup by id (or use first if no id)
        $found = null;
        $found_index = null;
        if ($id) {
            foreach ($backups as $k => $b) {
                if (isset($b['id']) && $b['id'] === $id) {
                    $found = $b;
                    $found_index = $k;
                    break;
                }
            }
        } else {
            $found = $backups[0];
            $found_index = 0;
        }

        if (empty($found) || !is_array($found)) {
            wp_send_json_error('Backup not found');
        }

        // Restore sidebars_widgets
        if (isset($found['sidebars_widgets'])) {
            update_option('sidebars_widgets', $found['sidebars_widgets']);
        }

        // Restore captured widget options
        if (!empty($found['widget_options']) && is_array($found['widget_options'])) {
            foreach ($found['widget_options'] as $opt_name => $opt_value) {
                update_option($opt_name, $opt_value);
            }
        }

        // Remove the restored backup from history
        array_splice($backups, $found_index, 1);
        update_option('ross_footer_template_backups', $backups);

        $new_backups_html = $this->render_backups_list_html();
        wp_send_json_success(array('message' => 'Footer restored from backup', 'restored_id' => isset($found['id']) ? $found['id'] : '', 'backups_html' => $new_backups_html));
    }

    /**
     * Delete a backup by id
     */
    public function ajax_delete_footer_backup() {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_delete_footer_backup called by user id=' . get_current_user_id() . ' nonce=' . (isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 'none') . ' backup_id=' . (isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : 'none'));
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $id = isset($_POST['backup_id']) ? sanitize_text_field($_POST['backup_id']) : '';
        if (!$id) wp_send_json_error('Missing backup id');

        $backups = $this->get_backups();
        $found_index = null;
        foreach ($backups as $k => $b) {
            if (isset($b['id']) && $b['id'] === $id) {
                $found_index = $k;
                break;
            }
        }

        if ($found_index === null) wp_send_json_error('Backup not found');

        array_splice($backups, $found_index, 1);
        $this->save_backups($backups);

        $new_backups_html = $this->render_backups_list_html();
        wp_send_json_success(array('message' => 'Backup deleted', 'deleted_id' => $id, 'backups_html' => $new_backups_html));
    }

    /**
     * Return backups array as JSON (for AJAX listing if needed)
     */
    public function ajax_list_footer_backups() {
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_list_footer_backups called by user id=' . get_current_user_id());
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_apply_footer_template', 'nonce');

        $backups = $this->get_backups();
        $html = $this->render_backups_list_html();
        wp_send_json_success(array('backups' => $backups, 'html' => $html));
    }

    /**
     * AJAX: return preview HTML for a given template key.
     */
    public function ajax_get_footer_template_preview() {
        // Preview is read-only; don't require nonce. Still require manage_options to view previews in admin.
        if (!current_user_can('manage_options')) {
            if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_get_footer_template_preview: unauthorized user');
            wp_send_json_error('Unauthorized', 403);
        }

        $tpl = isset($_POST['template']) ? sanitize_text_field($_POST['template']) : '';
        if (defined('WP_DEBUG') && WP_DEBUG) error_log('ajax_get_footer_template_preview: template=' . $tpl . ' user=' . get_current_user_id());
        if (empty($tpl)) wp_send_json_error('Missing template');

        $html = $this->get_template_preview_html($tpl);
        if (empty($html)) wp_send_json_error('No preview available');

        wp_send_json_success(array('template' => $tpl, 'html' => $html));
    }

    /** AJAX: return a dialog with diffs between folder-based templates and stored templates */
    public function ajax_sync_footer_templates() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        check_ajax_referer('ross_sync_footer_templates', 'nonce');
        $folder = $this->load_templates_from_dir();
        $stored = get_option('ross_theme_footer_templates', array());
        if (!is_array($stored)) $stored = array();

        ob_start();
        echo '<div class="ross-sync-dialog">';
        echo '<h2>Sync templates</h2>';
        echo '<p>Select the templates you want to overwrite in stored options with the versions in the <code>templates/</code> folder.</p>';
        if (empty($folder)) {
            echo '<p><em>No templates were found in the templates folder.</em></p>';
            echo '<div style="text-align:right"><button class="button" id="ross-sync-close">Close</button></div>';
            echo '</div>';
            $html = ob_get_clean();
            wp_send_json_success(array('html' => $html));
        }

        echo '<form id="ross-sync-form">';
        echo '<table class="widefat" style="width:100%;"><thead><tr><th></th><th>Template ID</th><th>Stored? / Diff</th><th>Folder (File) Preview</th><th>Stored Preview</th></tr></thead><tbody>';
        foreach ($folder as $id => $tpl) {
            $stored_tpl = isset($stored[$id]) ? $stored[$id] : null;
            // Compare simple fields
            $changes = array();
            if (empty($stored_tpl)) {
                $changes[] = 'New template (not stored)';
            } else {
                if (isset($tpl['title']) && (!isset($stored_tpl['title']) || $tpl['title'] !== $stored_tpl['title'])) $changes[] = 'Title changed';
                if (isset($tpl['bg']) && (!isset($stored_tpl['bg']) || $tpl['bg'] !== $stored_tpl['bg'])) $changes[] = 'Background color changed';
                // Compare cols array
                $file_cols = isset($tpl['cols']) && is_array($tpl['cols']) ? $tpl['cols'] : array();
                $stored_cols = isset($stored_tpl['cols']) && is_array($stored_tpl['cols']) ? $stored_tpl['cols'] : array();
                if ($file_cols !== $stored_cols) $changes[] = 'Columns content differs';
            }
            $diff_label = empty($changes) ? '<em>No differences</em>' : esc_html(implode(', ', $changes));
            echo '<tr>';
            echo '<td style="width:36px;">';
            echo '<input type="checkbox" name="sync_template_ids[]" value="' . esc_attr($id) . '" ' . (empty($changes) ? '' : 'checked') . '>'; 
            echo '</td>';
            echo '<td style="width:140px; font-weight:600;">' . esc_html($id) . '<br/><small>' . esc_html(isset($tpl['title']) ? $tpl['title'] : '') . '</small></td>';
            echo '<td>' . $diff_label . '</td>';
            echo '<td style="width:280px; vertical-align:top;">' . $this->get_template_preview_html($id) . '</td>';
            echo '<td style="width:280px; vertical-align:top;">' . ($stored_tpl ? $this->get_template_preview_html($id) : '<em>Not stored</em>') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
        echo '<div style="margin-top:8px;text-align:right;"><button class="button" id="ross-apply-sync">Apply selected</button> <button class="button" id="ross-sync-close">Close</button></div>';
        echo '</form>';
        echo '</div>';
        $html = ob_get_clean();
        wp_send_json_success(array('html' => $html));
    }

    /** AJAX: apply selected templates from folder to the stored templates option */
    public function ajax_apply_template_sync() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }
        check_ajax_referer('ross_sync_footer_templates', 'nonce');
        $selected = isset($_POST['templates']) ? (array) $_POST['templates'] : array();
        if (empty($selected)) wp_send_json_error('No templates selected');
        $folder = $this->load_templates_from_dir();
        $stored = get_option('ross_theme_footer_templates', array());
        if (!is_array($stored)) $stored = array();
        $changed = false;
        foreach ($selected as $id) {
            if (!isset($folder[$id])) continue; // skip unknown selections
            $stored[$id] = $folder[$id];
            $changed = true;
        }
        if ($changed) {
            update_option('ross_theme_footer_templates', $stored);
            // set transient to show admin notice about override
            set_transient('ross_templates_overridden', 1, 10);
            $html = '<p>Selected templates synced to stored templates.</p>';
            wp_send_json_success(array('message' => 'Synced', 'html' => $html));
        }
        wp_send_json_error('No changes applied');
    }
    
    public function footer_columns_callback() {
        $value = isset($this->options['footer_columns']) ? $this->options['footer_columns'] : '4';
        ?>
        <select name="ross_theme_footer_options[footer_columns]" id="footer_columns">
            <option value="1" <?php selected($value, '1'); ?>>1 Column</option>
            <option value="2" <?php selected($value, '2'); ?>>2 Columns</option>
            <option value="3" <?php selected($value, '3'); ?>>3 Columns</option>
            <option value="4" <?php selected($value, '4'); ?>>4 Columns</option>
        </select>
        <p class="description">
            Number of columns to display in footer.<br>
            <strong>With Template Content:</strong> Shows first 1-4 columns from selected template.<br>
            <strong>With Widgets:</strong> Controls number of widget areas (footer-1 to footer-4).
        </p>
        <?php
    }
    
    public function footer_width_callback() {
        $value = isset($this->options['footer_width']) ? $this->options['footer_width'] : 'contained';
        ?>
        <select name="ross_theme_footer_options[footer_width]" id="footer_width">
            <option value="full" <?php selected($value, 'full'); ?>>Full Width</option>
            <option value="contained" <?php selected($value, 'contained'); ?>>Contained</option>
        </select>
        <?php
    }
    
    

    // New: custom footer controls
    public function enable_custom_footer_callback() {
        $value = isset($this->options['enable_custom_footer']) ? $this->options['enable_custom_footer'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_custom_footer]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_custom_footer">Enable custom site footer HTML</label>
        <?php
    }

    public function custom_footer_html_callback() {
        $value = isset($this->options['custom_footer_html']) ? $this->options['custom_footer_html'] : '';
        ?>
        <textarea name="ross_theme_footer_options[custom_footer_html]" rows="6" class="large-text" placeholder="Paste allowed HTML for the footer (links allowed)"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">Use basic HTML. Allowed tags: a, br, strong, em, p, span, div, ul, li</p>
        <?php
    }
    
    // Field Callbacks - Widgets Section
    public function enable_widgets_callback() {
        $value = isset($this->options['enable_widgets']) ? $this->options['enable_widgets'] : 1;
        ?>
        <label>
            <input type="checkbox" name="ross_theme_footer_options[enable_widgets]" value="1" <?php checked(1, $value); ?> />
            Show footer widget areas
        </label>
        <p class="description">
            When checked, footer widget areas (footer-1 to footer-4) will be displayed.<br>
            <strong>Note:</strong> If "Use Template Content" is enabled above, template content will show instead of widgets.
        </p>
        <?php
    }
    
    public function widgets_bg_color_callback() {
        $value = isset($this->options['widgets_bg_color']) ? $this->options['widgets_bg_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_footer_options[widgets_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function widgets_text_color_callback() {
        $value = isset($this->options['widgets_text_color']) ? $this->options['widgets_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_footer_options[widgets_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    public function widget_title_color_callback() {
        $value = isset($this->options['widget_title_color']) ? $this->options['widget_title_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_footer_options[widget_title_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function use_template_colors_callback() {
        $value = isset($this->options['use_template_colors']) ? $this->options['use_template_colors'] : 1;
        ?>
        <label class="ross-switch-label">
            <input type="checkbox" name="ross_theme_footer_options[use_template_colors]" value="1" <?php checked(1, $value); ?> />
            <span class="ross-label-text">Use selected template default colors (uncheck to customize)</span>
        </label>
        <?php
    }

    public function template_custom_colors_callback() {
        $tpl = isset($this->options['footer_template']) ? $this->options['footer_template'] : 'template1';
        // Load stored template color overrides (keys like 'template1_bg')
        $bg = isset($this->options[$tpl . '_bg']) ? $this->options[$tpl . '_bg'] : '';
        $text = isset($this->options[$tpl . '_text']) ? $this->options[$tpl . '_text'] : '';
        $accent = isset($this->options[$tpl . '_accent']) ? $this->options[$tpl . '_accent'] : '';
        $social = isset($this->options[$tpl . '_social']) ? $this->options[$tpl . '_social'] : '';
        ?>
        <div>
            <label>Background Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_bg]" value="<?php echo esc_attr($bg); ?>" class="color-picker" /></label><br/>
            <label>Text Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_text]" value="<?php echo esc_attr($text); ?>" class="color-picker" /></label><br/>
            <label>Accent Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_accent]" value="<?php echo esc_attr($accent); ?>" class="color-picker" /></label><br/>
            <label>Social Icon Color: <input type="text" name="ross_theme_footer_options[<?php echo esc_attr($tpl); ?>_social]" value="<?php echo esc_attr($social); ?>" class="color-picker" /></label>
        </div>
        <p class="description">Customize colors for the selected template. Leave empty to use the template defaults.</p>
        <?php
    }
    
    // Field Callbacks - CTA Section
    public function enable_footer_cta_callback() {
        $value = isset($this->options['enable_footer_cta']) ? $this->options['enable_footer_cta'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_footer_options[enable_footer_cta]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_footer_cta">Enable footer CTA section</label>
        <?php
    }
    
    public function cta_title_callback() {
        $value = isset($this->options['cta_title']) ? $this->options['cta_title'] : 'Ready to Get Started?';
        ?>
        <input type="text" name="ross_theme_footer_options[cta_title]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    public function cta_button_text_callback() {
        $value = isset($this->options['cta_button_text']) ? $this->options['cta_button_text'] : 'Contact Us Today';
        ?>
        <input type="text" name="ross_theme_footer_options[cta_button_text]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    public function cta_bg_color_callback() {
        $value = isset($this->options['cta_bg_color']) ? $this->options['cta_bg_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_footer_options[cta_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function cta_always_visible_callback() {
        $v = isset($this->options['cta_always_visible']) ? $this->options['cta_always_visible'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_always_visible]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Always show CTA (ignore conditional visibility)';
        echo '</label>';
    }

    public function cta_display_on_callback() {
        $v = isset($this->options['cta_display_on']) ? $this->options['cta_display_on'] : array();
        if (!is_array($v)) $v = array($v);
        $choices = array('all' => 'Everywhere', 'front' => 'Front Page', 'home' => 'Blog Index', 'single' => 'Single Posts', 'page' => 'Pages', 'archive' => 'Archives');
        foreach ($choices as $key => $label) {
            $checked = in_array($key, $v, true) ? 'checked="checked"' : '';
            echo '<label style="display:block;margin-right:12px;">';
            echo '<input type="checkbox" name="ross_theme_footer_options[cta_display_on][]" value="' . esc_attr($key) . '" ' . $checked . ' /> ' . esc_html($label);
            echo '</label>';
        }
        echo '<p class="description">Select where the CTA appears when enabled. Use "Everywhere" to show on all pages.</p>';
    }

    public function cta_text_callback() {
        $v = isset($this->options['cta_text']) ? $this->options['cta_text'] : '';
        echo '<textarea name="ross_theme_footer_options[cta_text]" rows="3" class="large-text">' . esc_textarea($v) . '</textarea>';
    }

    public function cta_button_url_callback() {
        $v = isset($this->options['cta_button_url']) ? $this->options['cta_button_url'] : '';
        echo '<input type="url" name="ross_theme_footer_options[cta_button_url]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
    }

    public function cta_icon_callback() {
        $v = isset($this->options['cta_icon']) ? $this->options['cta_icon'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_icon]" value="' . esc_attr($v) . '" class="regular-text" placeholder="fa fa-arrow-right (class name)" />';
        echo '<p class="description">Set an icon class name to show an icon next to the CTA button (font-awesome / theme icons).</p>';
    }

    public function cta_alignment_callback() {
        $v = isset($this->options['cta_alignment']) ? $this->options['cta_alignment'] : 'center';
        ?>
        <select name="ross_theme_footer_options[cta_alignment]">
            <option value="left" <?php selected($v, 'left'); ?>>Left</option>
            <option value="center" <?php selected($v, 'center'); ?>>Center</option>
            <option value="right" <?php selected($v, 'right'); ?>>Right</option>
        </select>
        <?php
    }

    public function cta_layout_direction_callback() {
        $v = isset($this->options['cta_layout_direction']) ? $this->options['cta_layout_direction'] : 'row';
        ?>
        <select name="ross_theme_footer_options[cta_layout_direction]">
            <option value="row" <?php selected($v, 'row'); ?>>Horizontal</option>
            <option value="column" <?php selected($v, 'column'); ?>>Vertical</option>
        </select>
        <?php
    }

    public function cta_layout_wrap_callback() {
        $v = isset($this->options['cta_layout_wrap']) ? $this->options['cta_layout_wrap'] : 0;
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_layout_wrap]" value="1" ' . checked(1, $v, false) . ' /> Allow wrap to next line on small viewports';
    }

    public function cta_layout_justify_callback() {
        $v = isset($this->options['cta_layout_justify']) ? $this->options['cta_layout_justify'] : 'center';
        ?>
        <select name="ross_theme_footer_options[cta_layout_justify]">
            <option value="flex-start" <?php selected($v, 'flex-start'); ?>>Left (flex-start)</option>
            <option value="center" <?php selected($v, 'center'); ?>>Center</option>
            <option value="flex-end" <?php selected($v, 'flex-end'); ?>>Right (flex-end)</option>
            <option value="space-between" <?php selected($v, 'space-between'); ?>>Space Between</option>
            <option value="space-around" <?php selected($v, 'space-around'); ?>>Space Around</option>
        </select>
        <?php
    }

    public function cta_layout_align_callback() {
        $v = isset($this->options['cta_layout_align']) ? $this->options['cta_layout_align'] : 'center';
        ?>
        <select name="ross_theme_footer_options[cta_layout_align]">
            <option value="flex-start" <?php selected($v, 'flex-start'); ?>>Top</option>
            <option value="center" <?php selected($v, 'center'); ?>>Center</option>
            <option value="flex-end" <?php selected($v, 'flex-end'); ?>>Bottom</option>
        </select>
        <?php
    }

    public function cta_gap_callback() {
        $v = isset($this->options['cta_gap']) ? intval($this->options['cta_gap']) : 12;
        echo '<input type="number" name="ross_theme_footer_options[cta_gap]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function cta_bg_type_callback() {
        $v = isset($this->options['cta_bg_type']) ? $this->options['cta_bg_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[cta_bg_type]">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function cta_bg_gradient_from_callback() {
        $v = isset($this->options['cta_bg_gradient_from']) ? $this->options['cta_bg_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_bg_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_bg_gradient_to_callback() {
        $v = isset($this->options['cta_bg_gradient_to']) ? $this->options['cta_bg_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_bg_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_bg_image_callback() {
        $v = isset($this->options['cta_bg_image']) ? $this->options['cta_bg_image'] : '';
        echo '<input type="text" id="ross-cta-bg-image" name="ross_theme_footer_options[cta_bg_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-cta-bg-image" data-input-name="ross_theme_footer_options[cta_bg_image]">Upload</button>';
        echo '<input type="hidden" id="ross-cta-bg-image-id" name="ross_theme_footer_options[cta_bg_image_id]" value="' . esc_attr(isset($this->options['cta_bg_image_id']) ? $this->options['cta_bg_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-cta-bg-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
    }

    public function cta_text_color_callback() {
        $v = isset($this->options['cta_text_color']) ? $this->options['cta_text_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_text_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_button_bg_color_callback() {
        $v = isset($this->options['cta_button_bg_color']) ? $this->options['cta_button_bg_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_button_bg_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_button_text_color_callback() {
        $v = isset($this->options['cta_button_text_color']) ? $this->options['cta_button_text_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_button_text_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_padding_top_callback() {
        $v = isset($this->options['cta_padding_top']) ? intval($this->options['cta_padding_top']) : 24;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_top]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_padding_right_callback() {
        $v = isset($this->options['cta_padding_right']) ? intval($this->options['cta_padding_right']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_right]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_padding_bottom_callback() {
        $v = isset($this->options['cta_padding_bottom']) ? intval($this->options['cta_padding_bottom']) : 24;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_bottom]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_padding_left_callback() {
        $v = isset($this->options['cta_padding_left']) ? intval($this->options['cta_padding_left']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_padding_left]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    public function cta_overlay_enabled_callback() {
        $v = isset($this->options['cta_overlay_enabled']) ? $this->options['cta_overlay_enabled'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_overlay_enabled]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable CTA Overlay';
        echo '</label>';
    }

    public function cta_overlay_type_callback() {
        $v = isset($this->options['cta_overlay_type']) ? $this->options['cta_overlay_type'] : 'color';
        ?>
        <select name="ross_theme_footer_options[cta_overlay_type]">
            <option value="color" <?php selected($v,'color'); ?>>Color</option>
            <option value="image" <?php selected($v,'image'); ?>>Image</option>
            <option value="gradient" <?php selected($v,'gradient'); ?>>Gradient</option>
        </select>
        <?php
    }

    public function cta_overlay_color_callback() {
        $v = isset($this->options['cta_overlay_color']) ? $this->options['cta_overlay_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_overlay_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_overlay_image_callback() {
        $v = isset($this->options['cta_overlay_image']) ? $this->options['cta_overlay_image'] : '';
        echo '<input type="text" id="ross-cta-overlay-image" name="ross_theme_footer_options[cta_overlay_image]" value="' . esc_attr($v) . '" class="regular-text" placeholder="https://..." />';
        echo ' <button type="button" class="button ross-upload-button" data-target="ross-cta-overlay-image" data-input-name="ross_theme_footer_options[cta_overlay_image]">Upload</button>';
        echo '<input type="hidden" id="ross-cta-overlay-image-id" name="ross_theme_footer_options[cta_overlay_image_id]" value="' . esc_attr(isset($this->options['cta_overlay_image_id']) ? $this->options['cta_overlay_image_id'] : '') . '" />';
        echo '&nbsp;<span id="ross-cta-overlay-image-preview">';
        if (!empty($v)) {
            echo '<img src="' . esc_url($v) . '" style="max-height:40px;vertical-align:middle;border:1px solid #ddd;padding:2px;" />';
        }
        echo '</span>';
    }

    public function cta_overlay_gradient_from_callback() {
        $v = isset($this->options['cta_overlay_gradient_from']) ? $this->options['cta_overlay_gradient_from'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_overlay_gradient_from]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_overlay_gradient_to_callback() {
        $v = isset($this->options['cta_overlay_gradient_to']) ? $this->options['cta_overlay_gradient_to'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_overlay_gradient_to]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_overlay_opacity_callback() {
        $v = isset($this->options['cta_overlay_opacity']) ? $this->options['cta_overlay_opacity'] : '0.5';
        echo '<input type="number" step="0.1" min="0" max="1" name="ross_theme_footer_options[cta_overlay_opacity]" value="' . esc_attr($v) . '" class="small-text" />';
    }

    // New Margin callbacks
    public function cta_margin_top_callback() {
        $v = isset($this->options['cta_margin_top']) ? intval($this->options['cta_margin_top']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_top]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_margin_right_callback() {
        $v = isset($this->options['cta_margin_right']) ? intval($this->options['cta_margin_right']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_right]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_margin_bottom_callback() {
        $v = isset($this->options['cta_margin_bottom']) ? intval($this->options['cta_margin_bottom']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_bottom]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }
    public function cta_margin_left_callback() {
        $v = isset($this->options['cta_margin_left']) ? intval($this->options['cta_margin_left']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_margin_left]" value="' . esc_attr($v) . '" class="small-text" /> px';
    }

    // Animation duration callback
    public function cta_anim_duration_callback() {
        $v = isset($this->options['cta_anim_duration']) ? intval($this->options['cta_anim_duration']) : 400;
        echo '<input type="number" name="ross_theme_footer_options[cta_anim_duration]" value="' . esc_attr($v) . '" class="small-text" /> ms';
    }

    public function cta_icon_color_callback() {
        $v = isset($this->options['cta_icon_color']) ? $this->options['cta_icon_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_icon_color]" value="' . esc_attr($v) . '" class="color-picker" />';
    }

    public function cta_animation_callback() {
        $v = isset($this->options['cta_animation']) ? $this->options['cta_animation'] : 'none';
        ?>
        <select name="ross_theme_footer_options[cta_animation]">
            <option value="none" <?php selected($v,'none'); ?>>None</option>
            <option value="fade" <?php selected($v,'fade'); ?>>Fade</option>
            <option value="slide" <?php selected($v,'slide'); ?>>Slide Up</option>
            <option value="pop" <?php selected($v,'pop'); ?>>Pop</option>
            <option value="zoom" <?php selected($v,'zoom'); ?>>Zoom</option>
        </select>
        <?php
    }

    public function cta_anim_delay_callback() {
        $v = isset($this->options['cta_anim_delay']) ? intval($this->options['cta_anim_delay']) : 150;
        echo '<input type="number" name="ross_theme_footer_options[cta_anim_delay]" value="' . esc_attr($v) . '" class="small-text" /> ms';
    }

    public function enable_custom_cta_callback() {
        $v = isset($this->options['enable_custom_cta']) ? $this->options['enable_custom_cta'] : 0;
        echo '<input type="checkbox" name="ross_theme_footer_options[enable_custom_cta]" value="1" ' . checked(1, $v, false) . ' />';
        echo ' <label for="enable_custom_cta">Use custom CTA HTML/CSS instead of the built-in CTA</label>';
    }

    public function custom_cta_html_callback() {
        $v = isset($this->options['custom_cta_html']) ? $this->options['custom_cta_html'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_cta_html]" rows="6" class="large-text">' . esc_textarea($v) . '</textarea>';
    }

    public function custom_cta_css_callback() {
        $v = isset($this->options['custom_cta_css']) ? $this->options['custom_cta_css'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_cta_css]" rows="6" class="large-text" placeholder="Any additional CSS to apply to the CTA">' . esc_textarea($v) . '</textarea>';
    }

    public function custom_cta_js_callback() {
        $v = isset($this->options['custom_cta_js']) ? $this->options['custom_cta_js'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_cta_js]" rows="6" class="large-text" placeholder="Custom JS for the CTA (will be escaped)">' . esc_textarea($v) . '</textarea>';
    }
    
    // Border controls
    public function cta_border_width_callback() {
        $v = isset($this->options['cta_border_width']) ? intval($this->options['cta_border_width']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_border_width]" value="' . esc_attr($v) . '" class="small-text" min="0" max="20" /> px';
        echo '<p class="description">Set to 0 for no border</p>';
    }
    
    public function cta_border_style_callback() {
        $v = isset($this->options['cta_border_style']) ? $this->options['cta_border_style'] : 'solid';
        ?>
        <select name="ross_theme_footer_options[cta_border_style]">
            <option value="none" <?php selected($v,'none'); ?>>None</option>
            <option value="solid" <?php selected($v,'solid'); ?>>Solid</option>
            <option value="dashed" <?php selected($v,'dashed'); ?>>Dashed</option>
            <option value="dotted" <?php selected($v,'dotted'); ?>>Dotted</option>
            <option value="double" <?php selected($v,'double'); ?>>Double</option>
        </select>
        <?php
    }
    
    public function cta_border_color_callback() {
        $v = isset($this->options['cta_border_color']) ? $this->options['cta_border_color'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_border_color]" value="' . esc_attr($v) . '" class="color-picker" data-default-color="#dddddd" />';
    }
    
    public function cta_border_radius_callback() {
        $v = isset($this->options['cta_border_radius']) ? intval($this->options['cta_border_radius']) : 0;
        echo '<input type="number" name="ross_theme_footer_options[cta_border_radius]" value="' . esc_attr($v) . '" class="small-text" min="0" max="100" /> px';
        echo '<p class="description">Rounded corners (0 = sharp corners)</p>';
    }
    
    // Shadow controls
    public function cta_box_shadow_callback() {
        $v = isset($this->options['cta_box_shadow']) ? $this->options['cta_box_shadow'] : 0;
        echo '<label class="ross-toggle">';
        echo '<input type="checkbox" name="ross_theme_footer_options[cta_box_shadow]" value="1" ' . checked(1, $v, false) . ' />';
        echo '<span class="ross-toggle-slider"></span> Enable box shadow for depth';
        echo '</label>';
    }
    
    public function cta_shadow_color_callback() {
        $v = isset($this->options['cta_shadow_color']) ? $this->options['cta_shadow_color'] : 'rgba(0,0,0,0.1)';
        echo '<input type="text" name="ross_theme_footer_options[cta_shadow_color]" value="' . esc_attr($v) . '" class="regular-text" />';
        echo '<p class="description">Use rgba() for transparency: rgba(0,0,0,0.1)</p>';
    }
    
    public function cta_shadow_blur_callback() {
        $v = isset($this->options['cta_shadow_blur']) ? intval($this->options['cta_shadow_blur']) : 10;
        echo '<input type="number" name="ross_theme_footer_options[cta_shadow_blur]" value="' . esc_attr($v) . '" class="small-text" min="0" max="100" /> px';
        echo '<p class="description">Higher value = more blur/spread</p>';
    }
    
    // Button hover effects
    public function cta_button_hover_bg_callback() {
        $v = isset($this->options['cta_button_hover_bg']) ? $this->options['cta_button_hover_bg'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_button_hover_bg]" value="' . esc_attr($v) . '" class="color-picker" />';
        echo '<p class="description">Leave empty to auto-darken</p>';
    }
    
    public function cta_button_hover_text_callback() {
        $v = isset($this->options['cta_button_hover_text']) ? $this->options['cta_button_hover_text'] : '';
        echo '<input type="text" name="ross_theme_footer_options[cta_button_hover_text]" value="' . esc_attr($v) . '" class="color-picker" />';
    }
    
    public function cta_button_border_radius_callback() {
        $v = isset($this->options['cta_button_border_radius']) ? intval($this->options['cta_button_border_radius']) : 4;
        echo '<input type="number" name="ross_theme_footer_options[cta_button_border_radius]" value="' . esc_attr($v) . '" class="small-text" min="0" max="50" /> px';
        echo '<p class="description">0 = square, 50 = pill shape</p>';
    }
    
    // Typography controls
    public function cta_title_font_size_callback() {
        $v = isset($this->options['cta_title_font_size']) ? intval($this->options['cta_title_font_size']) : 28;
        echo '<input type="number" name="ross_theme_footer_options[cta_title_font_size]" value="' . esc_attr($v) . '" class="small-text" min="12" max="72" /> px';
    }
    
    public function cta_title_font_weight_callback() {
        $v = isset($this->options['cta_title_font_weight']) ? $this->options['cta_title_font_weight'] : '700';
        ?>
        <select name="ross_theme_footer_options[cta_title_font_weight]">
            <option value="300" <?php selected($v,'300'); ?>>Light (300)</option>
            <option value="400" <?php selected($v,'400'); ?>>Normal (400)</option>
            <option value="500" <?php selected($v,'500'); ?>>Medium (500)</option>
            <option value="600" <?php selected($v,'600'); ?>>Semi-Bold (600)</option>
            <option value="700" <?php selected($v,'700'); ?>>Bold (700)</option>
            <option value="800" <?php selected($v,'800'); ?>>Extra-Bold (800)</option>
        </select>
        <?php
    }
    
    public function cta_text_font_size_callback() {
        $v = isset($this->options['cta_text_font_size']) ? intval($this->options['cta_text_font_size']) : 16;
        echo '<input type="number" name="ross_theme_footer_options[cta_text_font_size]" value="' . esc_attr($v) . '" class="small-text" min="10" max="32" /> px';
    }
    
    public function cta_button_font_size_callback() {
        $v = isset($this->options['cta_button_font_size']) ? intval($this->options['cta_button_font_size']) : 16;
        echo '<input type="number" name="ross_theme_footer_options[cta_button_font_size]" value="' . esc_attr($v) . '" class="small-text" min="10" max="24" /> px';
    }
    
    public function cta_button_font_weight_callback() {
        $v = isset($this->options['cta_button_font_weight']) ? $this->options['cta_button_font_weight'] : '600';
        ?>
        <select name="ross_theme_footer_options[cta_button_font_weight]">
            <option value="400" <?php selected($v,'400'); ?>>Normal (400)</option>
            <option value="500" <?php selected($v,'500'); ?>>Medium (500)</option>
            <option value="600" <?php selected($v,'600'); ?>>Semi-Bold (600)</option>
            <option value="700" <?php selected($v,'700'); ?>>Bold (700)</option>
        </select>
        <?php
    }
    
    public function cta_letter_spacing_callback() {
        $v = isset($this->options['cta_letter_spacing']) ? floatval($this->options['cta_letter_spacing']) : 0;
        echo '<input type="number" step="0.1" name="ross_theme_footer_options[cta_letter_spacing]" value="' . esc_attr($v) . '" class="small-text" min="-2" max="10" /> px';
        echo '<p class="description">Adjust spacing between letters (0 = normal)</p>';
    }
    
    // Container width controls
    public function cta_container_width_callback() {
        $v = isset($this->options['cta_container_width']) ? $this->options['cta_container_width'] : 'container';
        ?>
        <select name="ross_theme_footer_options[cta_container_width]">
            <option value="container" <?php selected($v,'container'); ?>>Standard Container</option>
            <option value="full" <?php selected($v,'full'); ?>>Full Width</option>
            <option value="custom" <?php selected($v,'custom'); ?>>Custom Max Width</option>
        </select>
        <p class="description">Choose container size (use custom for specific pixel width)</p>
        <?php
    }
    
    public function cta_max_width_callback() {
        $v = isset($this->options['cta_max_width']) ? intval($this->options['cta_max_width']) : 1200;
        echo '<input type="number" name="ross_theme_footer_options[cta_max_width]" value="' . esc_attr($v) . '" class="small-text" min="300" max="2000" step="50" /> px';
        echo '<p class="description">Only applies when container width is set to "Custom"</p>';
    }
    
    // Field Callbacks - Social Section
    public function enable_social_icons_callback() {
        $value = isset($this->options['enable_social_icons']) ? $this->options['enable_social_icons'] : 1;
        ?>
        <label>
            <input type="checkbox" name="ross_theme_footer_options[enable_social_icons]" value="1" <?php checked(1, $value); ?> />
            Show social media icons in footer
        </label>
        <?php
    }
    
    public function social_icons_list_callback() {
        // Core 4 platforms
        $core_platforms = array(
            'facebook' => array('label' => 'Facebook', 'icon' => 'fab fa-facebook-f', 'color' => '#1877F2'),
            'instagram' => array('label' => 'Instagram', 'icon' => 'fab fa-instagram', 'color' => '#E4405F'),
            'twitter' => array('label' => 'Twitter / X', 'icon' => 'fab fa-x-twitter', 'color' => '#000000'),
            'linkedin' => array('label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in', 'color' => '#0A66C2'),
        );
        
        // Custom platform settings
        $custom_enabled = isset($this->options['custom_social_enabled']) ? $this->options['custom_social_enabled'] : 0;
        $custom_label = isset($this->options['custom_social_label']) ? $this->options['custom_social_label'] : '';
        $custom_icon = isset($this->options['custom_social_icon']) ? $this->options['custom_social_icon'] : 'fas fa-link';
        $custom_url = isset($this->options['custom_social_url']) ? $this->options['custom_social_url'] : '';
        $custom_color = isset($this->options['custom_social_color']) ? $this->options['custom_social_color'] : '#666666';
        
        // Display order
        $display_order = isset($this->options['social_display_order']) ? $this->options['social_display_order'] : array('facebook', 'instagram', 'twitter', 'linkedin', 'custom');
        
        ?>
        <div class="ross-social-icons-manager-v2">
            <div class="social-platforms-grid">
                <?php foreach ($core_platforms as $platform_key => $platform_data): 
                    $enabled = isset($this->options[$platform_key . '_enabled']) ? $this->options[$platform_key . '_enabled'] : 1;
                    $url_value = isset($this->options[$platform_key . '_url']) ? $this->options[$platform_key . '_url'] : '';
                ?>
                    <div class="social-platform-card <?php echo $enabled ? 'is-enabled' : 'is-disabled'; ?>" data-platform="<?php echo esc_attr($platform_key); ?>">
                        <div class="platform-card-header">
                            <div class="platform-icon" style="background-color: <?php echo esc_attr($platform_data['color']); ?>;">
                                <i class="<?php echo esc_attr($platform_data['icon']); ?>"></i>
                            </div>
                            <div class="platform-info">
                                <h4><?php echo esc_html($platform_data['label']); ?></h4>
                                <label class="toggle-switch">
                                    <input type="checkbox" 
                                           name="ross_theme_footer_options[<?php echo esc_attr($platform_key); ?>_enabled]" 
                                           value="1" 
                                           <?php checked(1, $enabled); ?>
                                           class="platform-toggle" />
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="platform-card-body">
                            <input type="url" 
                                   name="ross_theme_footer_options[<?php echo esc_attr($platform_key); ?>_url]" 
                                   value="<?php echo esc_url($url_value); ?>" 
                                   class="widefat platform-url-input" 
                                   placeholder="https://<?php echo esc_attr($platform_key); ?>.com/yourprofile"
                                   <?php disabled(0, $enabled); ?> />
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Custom Platform Card -->
                <div class="social-platform-card social-platform-card--custom <?php echo $custom_enabled ? 'is-enabled' : 'is-disabled'; ?>" data-platform="custom">
                    <div class="platform-card-header">
                        <div class="platform-icon platform-icon--custom" style="background-color: <?php echo esc_attr($custom_color); ?>;">
                            <i class="<?php echo esc_attr($custom_icon); ?>" id="custom-platform-icon-preview"></i>
                        </div>
                        <div class="platform-info">
                            <h4>Custom Platform</h4>
                            <label class="toggle-switch">
                                <input type="checkbox" 
                                       name="ross_theme_footer_options[custom_social_enabled]" 
                                       value="1" 
                                       <?php checked(1, $custom_enabled); ?>
                                       class="platform-toggle" 
                                       id="custom-platform-toggle" />
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                    <div class="platform-card-body">
                        <div class="custom-platform-fields">
                            <div class="field-row">
                                <label>Platform Name</label>
                                <input type="text" 
                                       name="ross_theme_footer_options[custom_social_label]" 
                                       value="<?php echo esc_attr($custom_label); ?>" 
                                       class="widefat" 
                                       placeholder="e.g., Discord, Behance, Medium"
                                       <?php disabled(0, $custom_enabled); ?> />
                            </div>
                            <div class="field-row">
                                <label>Icon</label>
                                <div class="icon-picker-trigger" <?php disabled(0, $custom_enabled); ?>>
                                    <input type="hidden" 
                                           name="ross_theme_footer_options[custom_social_icon]" 
                                           value="<?php echo esc_attr($custom_icon); ?>" 
                                           id="custom-social-icon-input" />
                                    <button type="button" class="button" id="open-icon-picker" <?php disabled(0, $custom_enabled); ?>>
                                        <i class="<?php echo esc_attr($custom_icon); ?>"></i> Choose Icon
                                    </button>
                                </div>
                            </div>
                            <div class="field-row">
                                <label>URL</label>
                                <input type="url" 
                                       name="ross_theme_footer_options[custom_social_url]" 
                                       value="<?php echo esc_url($custom_url); ?>" 
                                       class="widefat" 
                                       placeholder="https://example.com/yourprofile"
                                       <?php disabled(0, $custom_enabled); ?> />
                            </div>
                            <div class="field-row">
                                <label>Icon Color</label>
                                <input type="text" 
                                       name="ross_theme_footer_options[custom_social_color]" 
                                       value="<?php echo esc_attr($custom_color); ?>" 
                                       class="color-picker" 
                                       data-default-color="#666666"
                                       <?php disabled(0, $custom_enabled); ?> />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="social-display-order" style="margin-top: 20px;">
                <label><strong>Display Order:</strong></label>
                <p class="description">Drag platforms to reorder (coming soon) or use dropdown:</p>
                <select name="ross_theme_footer_options[social_display_order][]" multiple size="5" class="widefat" style="max-width: 300px;">
                    <?php 
                    $all_platforms = array_merge(array_keys($core_platforms), array('custom'));
                    foreach ($all_platforms as $platform): 
                        $label = $platform === 'custom' ? 'Custom Platform' : ucfirst($platform);
                    ?>
                        <option value="<?php echo esc_attr($platform); ?>" selected><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <p class="description" style="margin-top: 15px;">
                <strong>Tip:</strong> Toggle platforms on/off using the switches. Only enabled platforms with URLs will display in the footer.
            </p>
        </div>
        
        <!-- Icon Picker Modal -->
        <div id="ross-icon-picker-modal" class="ross-modal" style="display: none;">
            <div class="ross-modal-content">
                <div class="ross-modal-header">
                    <h3>Choose Icon</h3>
                    <button type="button" class="ross-modal-close">&times;</button>
                </div>
                <div class="ross-modal-body">
                    <input type="text" id="icon-search" class="widefat" placeholder="Search icons..." style="margin-bottom: 15px;" />
                    <div class="icon-picker-grid" id="icon-picker-grid">
                        <!-- Icons will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    public function social_icon_style_callback() {
        $value = isset($this->options['social_icon_style']) ? $this->options['social_icon_style'] : 'circle';
        ?>
        <select name="ross_theme_footer_options[social_icon_style]">
            <option value="circle" <?php selected($value, 'circle'); ?>>Circle Background</option>
            <option value="square" <?php selected($value, 'square'); ?>>Square Background</option>
            <option value="rounded" <?php selected($value, 'rounded'); ?>>Rounded Square</option>
            <option value="plain" <?php selected($value, 'plain'); ?>>Plain Icons (No Background)</option>
        </select>
        <p class="description">Choose the background style for social icons</p>
        <?php
    }
    
    public function social_icon_size_callback() {
        $value = isset($this->options['social_icon_size']) ? $this->options['social_icon_size'] : '36';
        ?>
        <input type="number" name="ross_theme_footer_options[social_icon_size]" value="<?php echo esc_attr($value); ?>" min="20" max="80" step="2" class="small-text" /> px
        <p class="description">Icon container size (default: 36px)</p>
        <?php
    }
    
    public function social_icon_color_callback() {
        $value = isset($this->options['social_icon_color']) ? $this->options['social_icon_color'] : '';
        ?>
        <input type="text" name="ross_theme_footer_options[social_icon_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="" />
        <p class="description">Leave empty to use template default color</p>
        <?php
    }
    
    public function social_icon_hover_color_callback() {
        $value = isset($this->options['social_icon_hover_color']) ? $this->options['social_icon_hover_color'] : '';
        ?>
        <input type="text" name="ross_theme_footer_options[social_icon_hover_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="" />
        <p class="description">Leave empty to auto-generate hover effect</p>
        <?php
    }
    
    // Legacy callbacks for backward compatibility
    public function facebook_url_callback() {
        $value = isset($this->options['facebook_url']) ? $this->options['facebook_url'] : '';
        ?>
        <input type="url" name="ross_theme_footer_options[facebook_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://facebook.com/yourpage" />
        <?php
    }
    
    public function linkedin_url_callback() {
        $value = isset($this->options['linkedin_url']) ? $this->options['linkedin_url'] : '';
        ?>
        <input type="url" name="ross_theme_footer_options[linkedin_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://linkedin.com/company/yourcompany" />
        <?php
    }
    
    public function instagram_url_callback() {
        $value = isset($this->options['instagram_url']) ? $this->options['instagram_url'] : '';
        ?>
        <input type="url" name="ross_theme_footer_options[instagram_url]" value="<?php echo esc_url($value); ?>" class="regular-text" placeholder="https://instagram.com/yourprofile" />
        <?php
    }
    
    // Field Callbacks - Copyright Section
    public function enable_copyright_callback() {
        $value = isset($this->options['enable_copyright']) ? $this->options['enable_copyright'] : 1;
        ?>
        <div style="background: #f9f9f9; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 10px;">
            <label style="display: flex; align-items: center; gap: 10px; font-weight: 500;">
                <input type="checkbox" name="ross_theme_footer_options[enable_copyright]" value="1" <?php checked(1, $value); ?> />
                <span>Show Copyright Bar</span>
            </label>
            <p class="description" style="margin: 10px 0 0 30px;">
                Display the copyright information at the bottom of your footer.
            </p>
        </div>
        <?php
    }
    
    public function copyright_text_callback() {
        $value = isset($this->options['copyright_text']) ? $this->options['copyright_text'] : '© ' . date('Y') . ' ' . get_bloginfo('name') . '. All Rights Reserved.';
        ?>
        <textarea name="ross_theme_footer_options[copyright_text]" rows="3" class="large-text" style="font-family: monospace;"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">
            Use <code>{year}</code> and <code>{site_name}</code> as placeholders. 
            For links: <code>&lt;a href="/privacy"&gt;Privacy Policy&lt;/a&gt;</code>
        </p>
        <?php
    }
    
    public function copyright_bg_color_callback() {
        $value = isset($this->options['copyright_bg_color']) ? $this->options['copyright_bg_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function copyright_text_color_callback() {
        $value = isset($this->options['copyright_text_color']) ? $this->options['copyright_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    public function copyright_alignment_callback() {
        $value = isset($this->options['copyright_alignment']) ? $this->options['copyright_alignment'] : 'center';
        ?>
        <select name="ross_theme_footer_options[copyright_alignment]" id="copyright_alignment">
            <option value="left" <?php selected($value, 'left'); ?>>Left</option>
            <option value="center" <?php selected($value, 'center'); ?>>Center</option>
            <option value="right" <?php selected($value, 'right'); ?>>Right</option>
        </select>
        <?php
    }

    // New: Font size callback
    public function copyright_font_size_callback() {
        $v = isset($this->options['copyright_font_size']) ? intval($this->options['copyright_font_size']) : 14;
        ?>
        <input type="range" name="ross_theme_footer_options[copyright_font_size]" min="10" max="24" value="<?php echo esc_attr($v); ?>" class="ross-range-slider" data-target="copyright-font-size-value" style="width: 200px;" />
        <span id="copyright-font-size-value" class="ross-range-value"><?php echo esc_html($v); ?>px</span>
        <p class="description">Adjust the font size of copyright text.</p>
        <?php
    }

    // New: Font weight callback
    public function copyright_font_weight_callback() {
        $v = isset($this->options['copyright_font_weight']) ? $this->options['copyright_font_weight'] : 'normal';
        ?>
        <select name="ross_theme_footer_options[copyright_font_weight]">
            <option value="light" <?php selected($v, 'light'); ?>>Light</option>
            <option value="normal" <?php selected($v, 'normal'); ?>>Normal</option>
            <option value="bold" <?php selected($v, 'bold'); ?>>Bold</option>
        </select>
        <?php
    }

    // New: Letter spacing
    public function copyright_letter_spacing_callback() {
        $v = isset($this->options['copyright_letter_spacing']) ? floatval($this->options['copyright_letter_spacing']) : 0;
        echo '<input type="number" step="0.1" name="ross_theme_footer_options[copyright_letter_spacing]" value="' . esc_attr($v) . '" class="small-text" /> px';
        echo '<p class="description">Spacing between letters. Positive values spread out text, negative brings it closer.</p>';
    }

    // New: Padding Top
    public function copyright_padding_top_callback() {
        $v = isset($this->options['copyright_padding_top']) ? intval($this->options['copyright_padding_top']) : 20;
        ?>
        <div style="display: flex; align-items: center; gap: 15px;">
            <input type="range" name="ross_theme_footer_options[copyright_padding_top]" min="0" max="100" value="<?php echo esc_attr($v); ?>" class="ross-range-slider" data-target="copyright-padding-top-value" style="width: 250px;" oninput="document.getElementById('copyright-padding-top-value').textContent = this.value + 'px'" />
            <span id="copyright-padding-top-value" class="ross-range-value" style="min-width: 50px; font-weight: 600; color: #2271b1;"><?php echo esc_html($v); ?>px</span>
        </div>
        <p class="description">Space above the copyright text (controls top height).</p>
        <?php
    }

    // New: Padding Bottom
    public function copyright_padding_bottom_callback() {
        $v = isset($this->options['copyright_padding_bottom']) ? intval($this->options['copyright_padding_bottom']) : 20;
        ?>
        <div style="display: flex; align-items: center; gap: 15px;">
            <input type="range" name="ross_theme_footer_options[copyright_padding_bottom]" min="0" max="100" value="<?php echo esc_attr($v); ?>" class="ross-range-slider" data-target="copyright-padding-bottom-value" style="width: 250px;" oninput="document.getElementById('copyright-padding-bottom-value').textContent = this.value + 'px'" />
            <span id="copyright-padding-bottom-value" class="ross-range-value" style="min-width: 50px; font-weight: 600; color: #2271b1;"><?php echo esc_html($v); ?>px</span>
        </div>
        <p class="description">Space below the copyright text (controls bottom height).</p>
        <?php
    }

    // New: Border Top Enable
    public function copyright_border_top_callback() {
        $v = isset($this->options['copyright_border_top']) ? $this->options['copyright_border_top'] : 0;
        ?>
        <label>
            <input type="checkbox" name="ross_theme_footer_options[copyright_border_top]" value="1" <?php checked(1, $v); ?> />
            Enable top border line
        </label>
        <p class="description">Adds a decorative border line above the copyright bar.</p>
        <?php
    }

    // New: Border Color
    public function copyright_border_color_callback() {
        $v = isset($this->options['copyright_border_color']) ? $this->options['copyright_border_color'] : '#333333';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_border_color]" value="<?php echo esc_attr($v); ?>" class="color-picker" data-default-color="#333333" />
        <p class="description">Color of the top border (only visible when border is enabled).</p>
        <?php
    }

    // New: Border Width
    public function copyright_border_width_callback() {
        $v = isset($this->options['copyright_border_width']) ? intval($this->options['copyright_border_width']) : 1;
        ?>
        <input type="number" name="ross_theme_footer_options[copyright_border_width]" value="<?php echo esc_attr($v); ?>" class="small-text" min="1" max="10" /> px
        <p class="description">Thickness of the top border line.</p>
        <?php
    }

    // New: Link Color
    public function copyright_link_color_callback() {
        $v = isset($this->options['copyright_link_color']) ? $this->options['copyright_link_color'] : '';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_link_color]" value="<?php echo esc_attr($v); ?>" class="color-picker" data-default-color="" />
        <p class="description">Color for links in the copyright text. Leave empty to use text color.</p>
        <?php
    }

    // New: Link Hover Color
    public function copyright_link_hover_color_callback() {
        $v = isset($this->options['copyright_link_hover_color']) ? $this->options['copyright_link_hover_color'] : '';
        ?>
        <input type="text" name="ross_theme_footer_options[copyright_link_hover_color]" value="<?php echo esc_attr($v); ?>" class="color-picker" data-default-color="" />
        <p class="description">Color for links on hover. Leave empty to use default hover effect.</p>
        <?php
    }

    // New: Custom Footer CSS
    public function custom_footer_css_callback() {
        $v = isset($this->options['custom_footer_css']) ? $this->options['custom_footer_css'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_footer_css]" rows="4" class="large-text" placeholder="Add custom CSS specific to the footer (no <style> tag needed)">' . esc_textarea($v) . '</textarea>';
    }

    // New: Custom Footer JS
    public function custom_footer_js_callback() {
        $v = isset($this->options['custom_footer_js']) ? $this->options['custom_footer_js'] : '';
        echo '<textarea name="ross_theme_footer_options[custom_footer_js]" rows="4" class="large-text" placeholder="Add JS to run for the footer (will be sanitized)">' . esc_textarea($v) . '</textarea>';
    }

    // Section callbacks for the new sections
    public function copyright_visibility_section_callback() {
        echo '<div style="background: #f0f8ff; padding: 12px; border-left: 4px solid #2271b1; margin-bottom: 15px;">';
        echo '<strong>⚙️ Visibility Controls</strong>';
        echo '<p style="margin: 8px 0 0 0;">Show or hide the copyright bar and control text alignment.</p>';
        echo '</div>';
    }
    public function copyright_content_section_callback() {
        echo '<div style="background: #fff9e6; padding: 12px; border-left: 4px solid #f59e0b; margin-bottom: 15px;">';
        echo '<strong>📝 Content Settings</strong>';
        echo '<p style="margin: 8px 0 0 0;">Customize the copyright text. Use <code>{year}</code> for auto-updating year and <code>{site_name}</code> for site name. HTML links are supported.</p>';
        echo '</div>';
    }
    public function copyright_styling_section_callback() {
        echo '<div style="background: #f3e8ff; padding: 12px; border-left: 4px solid #9333ea; margin-bottom: 15px;">';
        echo '<strong>🎨 Styling Options</strong>';
        echo '<p style="margin: 8px 0 0 0;">Complete visual control: colors, typography, spacing, borders and link colors.</p>';
        echo '</div>';
    }
    public function copyright_advanced_section_callback() {
        ?>
        <div class="ross-collapsible-section" style="background: #fee2e2; border-left: 4px solid #dc2626; margin-bottom: 15px;">
            <div class="ross-collapsible-header" onclick="rossToggleCollapsible(this)" style="padding: 12px; cursor: pointer; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong>⚡ Advanced Options</strong>
                    <p style="margin: 8px 0 0 0; font-weight: normal; color: #666;">For developers: custom HTML, CSS, and JavaScript</p>
                </div>
                <span class="dashicons dashicons-arrow-down-alt2" style="transition: transform 0.3s;"></span>
            </div>
            <div class="ross-collapsible-content" style="display: none; padding: 12px; background: #fff; border-top: 1px solid #dc2626;">
                <p style="margin: 0 0 10px 0; color: #666; font-size: 13px;">
                    <strong>⚠️ Warning:</strong> These are advanced settings for developers. Improper code may break your site. Use with caution.
                </p>
            </div>
        </div>
        <script>
        function rossToggleCollapsible(header) {
            var content = header.nextElementSibling;
            var arrow = header.querySelector('.dashicons');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                content.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }
        </script>
        <?php
    }
    
    // Sanitization
    public function sanitize_footer_options($input) {
        // If a template was recently applied via AJAX, restore the applied values here
        // to prevent saving stale values from the options form (opened before Apply)
        if (is_admin()) {
            $applied = get_transient('ross_footer_template_applied');
            if (is_array($applied) && !empty($applied)) {
                if (isset($applied['footer_template'])) {
                    $input['footer_template'] = $applied['footer_template'];
                }
                if (isset($applied['footer_columns'])) {
                    $input['footer_columns'] = $applied['footer_columns'];
                }
                if (isset($applied['enable_widgets'])) {
                    $input['enable_widgets'] = $applied['enable_widgets'];
                }
                // remove transient so this only protects a single save
                delete_transient('ross_footer_template_applied');
                // keep a short-lived notice transient for admins to see the result
                set_transient('ross_template_applied_notice', 1, 10);
            }
        }
        $sanitized = array();
        
        // Layout
        // footer_style option removed; default layout is used by the theme.
        $sanitized['footer_template'] = isset($input['footer_template']) ? sanitize_text_field($input['footer_template']) : 'business-professional';
        $sanitized['use_template_content'] = isset($input['use_template_content']) ? 1 : 0;
        $sanitized['footer_columns'] = isset($input['footer_columns']) ? sanitize_text_field($input['footer_columns']) : '4';
        $sanitized['footer_width'] = isset($input['footer_width']) ? sanitize_text_field($input['footer_width']) : 'contained';
        $sanitized['footer_padding'] = isset($input['footer_padding']) ? absint($input['footer_padding']) : 60; // kept for backward compatibility, now controlled by Styling padding

        // Custom footer
        $sanitized['enable_custom_footer'] = isset($input['enable_custom_footer']) ? 1 : 0;
        if (!empty($input['custom_footer_html'])) {
            $allowed = array(
                'a' => array('href' => array(), 'title' => array(), 'target' => array()),
                'br' => array(),
                'strong' => array(),
                'em' => array(),
                'p' => array(),
                'span' => array('class' => array()),
                'div' => array('class' => array()),
                'ul' => array(),
                'li' => array(),
            );

            $sanitized['custom_footer_html'] = wp_kses($input['custom_footer_html'], $allowed);
        } else {
            $sanitized['custom_footer_html'] = '';
        }
        
        // Widgets (enable_widgets removed - controlled by use_template_content toggle)
        $sanitized['enable_widgets'] = 1; // Always enabled for backward compatibility
        $sanitized['widgets_bg_color'] = sanitize_hex_color($input['widgets_bg_color']);
        $sanitized['widgets_text_color'] = sanitize_hex_color($input['widgets_text_color']);
        $sanitized['widget_title_color'] = sanitize_hex_color($input['widget_title_color']);
        // Styling options
        $sanitized['styling_bg_color'] = isset($input['styling_bg_color']) ? sanitize_hex_color($input['styling_bg_color']) : '';
        $sanitized['styling_bg_gradient'] = isset($input['styling_bg_gradient']) ? 1 : 0;
        $sanitized['styling_bg_image'] = isset($input['styling_bg_image']) ? esc_url_raw($input['styling_bg_image']) : '';
        $sanitized['styling_bg_image_id'] = isset($input['styling_bg_image_id']) ? absint($input['styling_bg_image_id']) : '';
        $sanitized['styling_bg_type'] = isset($input['styling_bg_type']) && in_array($input['styling_bg_type'], array('color','image','gradient')) ? sanitize_text_field($input['styling_bg_type']) : 'color';
        $sanitized['styling_bg_opacity'] = isset($input['styling_bg_opacity']) ? floatval($input['styling_bg_opacity']) : '1';
        $sanitized['styling_bg_gradient_from'] = isset($input['styling_bg_gradient_from']) ? sanitize_hex_color($input['styling_bg_gradient_from']) : '';
        $sanitized['styling_bg_gradient_to'] = isset($input['styling_bg_gradient_to']) ? sanitize_hex_color($input['styling_bg_gradient_to']) : '';

        $sanitized['styling_text_color'] = isset($input['styling_text_color']) ? sanitize_hex_color($input['styling_text_color']) : '';
        $sanitized['styling_link_color'] = isset($input['styling_link_color']) ? sanitize_hex_color($input['styling_link_color']) : '';
        $sanitized['styling_link_hover'] = isset($input['styling_link_hover']) ? sanitize_hex_color($input['styling_link_hover']) : '';

        $sanitized['styling_font_size'] = isset($input['styling_font_size']) ? absint($input['styling_font_size']) : 14;
        $sanitized['styling_line_height'] = isset($input['styling_line_height']) ? floatval($input['styling_line_height']) : 1.6;

        $sanitized['styling_col_gap'] = isset($input['styling_col_gap']) ? absint($input['styling_col_gap']) : 24;
        $sanitized['styling_row_gap'] = isset($input['styling_row_gap']) ? absint($input['styling_row_gap']) : 18;
        $sanitized['styling_padding_lr'] = isset($input['styling_padding_lr']) ? absint($input['styling_padding_lr']) : 20;

        $sanitized['styling_padding_top'] = isset($input['styling_padding_top']) ? absint($input['styling_padding_top']) : '';
        $sanitized['styling_padding_bottom'] = isset($input['styling_padding_bottom']) ? absint($input['styling_padding_bottom']) : '';
        $sanitized['styling_padding_left'] = isset($input['styling_padding_left']) ? absint($input['styling_padding_left']) : '';
        $sanitized['styling_padding_right'] = isset($input['styling_padding_right']) ? absint($input['styling_padding_right']) : '';

        $sanitized['styling_border_top'] = isset($input['styling_border_top']) ? 1 : 0;
        $sanitized['styling_border_color'] = isset($input['styling_border_color']) ? sanitize_hex_color($input['styling_border_color']) : '';
        $sanitized['styling_border_thickness'] = isset($input['styling_border_thickness']) ? absint($input['styling_border_thickness']) : 1;

        $sanitized['styling_widget_title_color'] = isset($input['styling_widget_title_color']) ? sanitize_hex_color($input['styling_widget_title_color']) : '';
        $sanitized['styling_widget_title_size'] = isset($input['styling_widget_title_size']) ? absint($input['styling_widget_title_size']) : 16;
        // Overlay sanitization
        $sanitized['styling_overlay_enabled'] = isset($input['styling_overlay_enabled']) ? 1 : 0;
        $sanitized['styling_overlay_type'] = isset($input['styling_overlay_type']) && in_array($input['styling_overlay_type'], array('color','image','gradient')) ? sanitize_text_field($input['styling_overlay_type']) : 'color';
        $sanitized['styling_overlay_color'] = isset($input['styling_overlay_color']) ? sanitize_hex_color($input['styling_overlay_color']) : '';
        $sanitized['styling_overlay_image'] = isset($input['styling_overlay_image']) ? esc_url_raw($input['styling_overlay_image']) : '';
        $sanitized['styling_overlay_image_id'] = isset($input['styling_overlay_image_id']) ? absint($input['styling_overlay_image_id']) : '';
        $sanitized['styling_overlay_gradient_from'] = isset($input['styling_overlay_gradient_from']) ? sanitize_hex_color($input['styling_overlay_gradient_from']) : '';
        $sanitized['styling_overlay_gradient_to'] = isset($input['styling_overlay_gradient_to']) ? sanitize_hex_color($input['styling_overlay_gradient_to']) : '';
        $sanitized['styling_overlay_opacity'] = isset($input['styling_overlay_opacity']) ? floatval($input['styling_overlay_opacity']) : 0.5;
        // Template selection & custom colors
        $sanitized['footer_template'] = isset($input['footer_template']) ? sanitize_text_field($input['footer_template']) : 'template1';
        $sanitized['use_template_colors'] = isset($input['use_template_colors']) ? 1 : 0;
        // Allow per-template color overrides (template1_bg, template1_text, template1_accent, template1_social)
        for ($i = 1; $i <= 4; $i++) {
            $tpl = 'template' . $i;
            $key = $tpl . '_bg';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
            $key = $tpl . '_text';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
            $key = $tpl . '_accent';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
            $key = $tpl . '_social';
            $sanitized[$key] = isset($input[$key]) && !empty($input[$key]) ? sanitize_hex_color($input[$key]) : '';
        }
        
        // CTA
        $sanitized['enable_footer_cta'] = isset($input['enable_footer_cta']) ? 1 : 0;
        $sanitized['cta_title'] = isset($input['cta_title']) ? sanitize_text_field($input['cta_title']) : '';
        $sanitized['cta_text'] = isset($input['cta_text']) ? wp_kses_post($input['cta_text']) : '';
        $sanitized['cta_button_text'] = isset($input['cta_button_text']) ? sanitize_text_field($input['cta_button_text']) : '';
        $sanitized['cta_button_url'] = isset($input['cta_button_url']) ? esc_url_raw($input['cta_button_url']) : '';
        $sanitized['cta_icon'] = isset($input['cta_icon']) ? sanitize_text_field($input['cta_icon']) : '';
        $sanitized['cta_bg_color'] = isset($input['cta_bg_color']) ? sanitize_hex_color($input['cta_bg_color']) : '';
        $sanitized['cta_bg_type'] = isset($input['cta_bg_type']) && in_array($input['cta_bg_type'], array('color','image','gradient')) ? sanitize_text_field($input['cta_bg_type']) : 'color';
        $sanitized['cta_bg_image'] = isset($input['cta_bg_image']) ? esc_url_raw($input['cta_bg_image']) : '';
        $sanitized['cta_bg_image_id'] = isset($input['cta_bg_image_id']) ? absint($input['cta_bg_image_id']) : '';
        $sanitized['cta_bg_gradient_from'] = isset($input['cta_bg_gradient_from']) ? sanitize_hex_color($input['cta_bg_gradient_from']) : '';
        $sanitized['cta_bg_gradient_to'] = isset($input['cta_bg_gradient_to']) ? sanitize_hex_color($input['cta_bg_gradient_to']) : '';
        $sanitized['cta_text_color'] = isset($input['cta_text_color']) ? sanitize_hex_color($input['cta_text_color']) : '';
        $sanitized['cta_button_bg_color'] = isset($input['cta_button_bg_color']) ? sanitize_hex_color($input['cta_button_bg_color']) : '';
        $sanitized['cta_button_text_color'] = isset($input['cta_button_text_color']) ? sanitize_hex_color($input['cta_button_text_color']) : '';
        $sanitized['cta_padding_top'] = isset($input['cta_padding_top']) ? absint($input['cta_padding_top']) : 24;
        $sanitized['cta_padding_right'] = isset($input['cta_padding_right']) ? absint($input['cta_padding_right']) : 0;
        $sanitized['cta_padding_bottom'] = isset($input['cta_padding_bottom']) ? absint($input['cta_padding_bottom']) : 24;
        $sanitized['cta_padding_left'] = isset($input['cta_padding_left']) ? absint($input['cta_padding_left']) : 0;
        // CTA margins (new)
        $sanitized['cta_margin_top'] = isset($input['cta_margin_top']) ? absint($input['cta_margin_top']) : 0;
        $sanitized['cta_margin_right'] = isset($input['cta_margin_right']) ? absint($input['cta_margin_right']) : 0;
        $sanitized['cta_margin_bottom'] = isset($input['cta_margin_bottom']) ? absint($input['cta_margin_bottom']) : 0;
        $sanitized['cta_margin_left'] = isset($input['cta_margin_left']) ? absint($input['cta_margin_left']) : 0;
        $sanitized['cta_icon_color'] = isset($input['cta_icon_color']) ? sanitize_hex_color($input['cta_icon_color']) : '';
        // CTA overlay sanitization
        $sanitized['cta_overlay_enabled'] = isset($input['cta_overlay_enabled']) ? 1 : 0;
        $sanitized['cta_overlay_type'] = isset($input['cta_overlay_type']) && in_array($input['cta_overlay_type'], array('color','image','gradient')) ? sanitize_text_field($input['cta_overlay_type']) : 'color';
        $sanitized['cta_overlay_color'] = isset($input['cta_overlay_color']) ? sanitize_hex_color($input['cta_overlay_color']) : '';
        $sanitized['cta_overlay_image'] = isset($input['cta_overlay_image']) ? esc_url_raw($input['cta_overlay_image']) : '';
        $sanitized['cta_overlay_image_id'] = isset($input['cta_overlay_image_id']) ? absint($input['cta_overlay_image_id']) : '';
        $sanitized['cta_overlay_gradient_from'] = isset($input['cta_overlay_gradient_from']) ? sanitize_hex_color($input['cta_overlay_gradient_from']) : '';
        $sanitized['cta_overlay_gradient_to'] = isset($input['cta_overlay_gradient_to']) ? sanitize_hex_color($input['cta_overlay_gradient_to']) : '';
        $sanitized['cta_overlay_opacity'] = isset($input['cta_overlay_opacity']) ? floatval($input['cta_overlay_opacity']) : 0.5;
        // Layout & alignment
        $sanitized['cta_alignment'] = isset($input['cta_alignment']) && in_array($input['cta_alignment'], array('left','center','right')) ? sanitize_text_field($input['cta_alignment']) : 'center';
        $sanitized['cta_layout_direction'] = isset($input['cta_layout_direction']) && in_array($input['cta_layout_direction'], array('row','column')) ? sanitize_text_field($input['cta_layout_direction']) : 'row';
        $sanitized['cta_layout_wrap'] = isset($input['cta_layout_wrap']) ? 1 : 0;
        $allowed_justify = array('flex-start','center','flex-end','space-between','space-around');
        $sanitized['cta_layout_justify'] = isset($input['cta_layout_justify']) && in_array($input['cta_layout_justify'], $allowed_justify) ? sanitize_text_field($input['cta_layout_justify']) : 'center';
        $allowed_align = array('flex-start','center','flex-end');
        $sanitized['cta_layout_align'] = isset($input['cta_layout_align']) && in_array($input['cta_layout_align'], $allowed_align) ? sanitize_text_field($input['cta_layout_align']) : 'center';
        $sanitized['cta_gap'] = isset($input['cta_gap']) ? absint($input['cta_gap']) : 12;
        $sanitized['cta_animation'] = isset($input['cta_animation']) && in_array($input['cta_animation'], array('none','fade','slide','pop','zoom')) ? sanitize_text_field($input['cta_animation']) : 'none';
        $sanitized['cta_anim_delay'] = isset($input['cta_anim_delay']) ? absint($input['cta_anim_delay']) : 150;
        $sanitized['cta_anim_duration'] = isset($input['cta_anim_duration']) ? absint($input['cta_anim_duration']) : 400;
        // Visibility
        $sanitized['cta_always_visible'] = isset($input['cta_always_visible']) ? 1 : 0;
        if (!empty($input['cta_display_on']) && is_array($input['cta_display_on'])) {
            $allowed = array('all','front','home','single','page','archive');
            $san = array();
            foreach ($input['cta_display_on'] as $v) {
                if (in_array($v, $allowed, true)) $san[] = sanitize_text_field($v);
            }
            $sanitized['cta_display_on'] = $san;
        } else {
            $sanitized['cta_display_on'] = array();
        }
        // Advanced custom CTA
        $sanitized['enable_custom_cta'] = isset($input['enable_custom_cta']) ? 1 : 0;
        $sanitized['custom_cta_html'] = isset($input['custom_cta_html']) ? wp_kses($input['custom_cta_html'], array('a'=>array('href'=>array(),'title'=>array(),'target'=>array()),'br'=>array(),'strong'=>array(),'em'=>array(),'p'=>array(),'span'=>array(),'div'=>array(),'ul'=>array(),'li'=>array())) : '';
        $sanitized['custom_cta_css'] = isset($input['custom_cta_css']) ? wp_strip_all_tags($input['custom_cta_css']) : '';
        $sanitized['custom_cta_js'] = isset($input['custom_cta_js']) ? wp_strip_all_tags($input['custom_cta_js']) : '';
        
        // NEW CTA enhancements - Border, Shadow, Typography, Button Hover, Container
        $sanitized['cta_border_width'] = isset($input['cta_border_width']) ? absint($input['cta_border_width']) : 0;
        $allowed_border_styles = array('none','solid','dashed','dotted','double');
        $sanitized['cta_border_style'] = isset($input['cta_border_style']) && in_array($input['cta_border_style'], $allowed_border_styles) ? sanitize_text_field($input['cta_border_style']) : 'solid';
        $sanitized['cta_border_color'] = isset($input['cta_border_color']) ? sanitize_hex_color($input['cta_border_color']) : '';
        $sanitized['cta_border_radius'] = isset($input['cta_border_radius']) ? absint($input['cta_border_radius']) : 0;
        $sanitized['cta_box_shadow'] = isset($input['cta_box_shadow']) ? 1 : 0;
        $sanitized['cta_shadow_color'] = isset($input['cta_shadow_color']) ? sanitize_text_field($input['cta_shadow_color']) : 'rgba(0,0,0,0.1)';
        $sanitized['cta_shadow_blur'] = isset($input['cta_shadow_blur']) ? absint($input['cta_shadow_blur']) : 10;
        $sanitized['cta_button_hover_bg'] = isset($input['cta_button_hover_bg']) ? sanitize_hex_color($input['cta_button_hover_bg']) : '';
        $sanitized['cta_button_hover_text'] = isset($input['cta_button_hover_text']) ? sanitize_hex_color($input['cta_button_hover_text']) : '';
        $sanitized['cta_button_border_radius'] = isset($input['cta_button_border_radius']) ? absint($input['cta_button_border_radius']) : 4;
        $sanitized['cta_title_font_size'] = isset($input['cta_title_font_size']) ? absint($input['cta_title_font_size']) : 28;
        $allowed_weights = array('300','400','500','600','700','800');
        $sanitized['cta_title_font_weight'] = isset($input['cta_title_font_weight']) && in_array($input['cta_title_font_weight'], $allowed_weights) ? sanitize_text_field($input['cta_title_font_weight']) : '700';
        $sanitized['cta_text_font_size'] = isset($input['cta_text_font_size']) ? absint($input['cta_text_font_size']) : 16;
        $sanitized['cta_button_font_size'] = isset($input['cta_button_font_size']) ? absint($input['cta_button_font_size']) : 16;
        $sanitized['cta_button_font_weight'] = isset($input['cta_button_font_weight']) && in_array($input['cta_button_font_weight'], $allowed_weights) ? sanitize_text_field($input['cta_button_font_weight']) : '600';
        $sanitized['cta_letter_spacing'] = isset($input['cta_letter_spacing']) ? floatval($input['cta_letter_spacing']) : 0;
        $allowed_container = array('container','full','custom');
        $sanitized['cta_container_width'] = isset($input['cta_container_width']) && in_array($input['cta_container_width'], $allowed_container) ? sanitize_text_field($input['cta_container_width']) : 'container';
        $sanitized['cta_max_width'] = isset($input['cta_max_width']) ? absint($input['cta_max_width']) : 1200;
        
        // Social
        $sanitized['enable_social_icons'] = isset($input['enable_social_icons']) ? 1 : 0;
        
        // Core 4 platforms with enabled toggles
        $core_platforms = array('facebook', 'instagram', 'twitter', 'linkedin');
        foreach ($core_platforms as $platform) {
            $sanitized[$platform . '_enabled'] = isset($input[$platform . '_enabled']) ? 1 : 0;
            $sanitized[$platform . '_url'] = isset($input[$platform . '_url']) ? esc_url_raw($input[$platform . '_url']) : '';
        }
        
        // Custom platform
        $sanitized['custom_social_enabled'] = isset($input['custom_social_enabled']) ? 1 : 0;
        $sanitized['custom_social_label'] = isset($input['custom_social_label']) ? sanitize_text_field($input['custom_social_label']) : '';
        $sanitized['custom_social_icon'] = isset($input['custom_social_icon']) ? sanitize_text_field($input['custom_social_icon']) : 'fas fa-link';
        $sanitized['custom_social_url'] = isset($input['custom_social_url']) ? esc_url_raw($input['custom_social_url']) : '';
        $sanitized['custom_social_color'] = isset($input['custom_social_color']) ? sanitize_hex_color($input['custom_social_color']) : '#666666';
        
        // Display order
        if (isset($input['social_display_order']) && is_array($input['social_display_order'])) {
            $sanitized['social_display_order'] = array_map('sanitize_text_field', $input['social_display_order']);
        } else {
            $sanitized['social_display_order'] = array('facebook', 'instagram', 'twitter', 'linkedin', 'custom');
        }
        
        // Social styling options
        $sanitized['social_icon_style'] = isset($input['social_icon_style']) && in_array($input['social_icon_style'], array('circle','square','rounded','plain')) ? sanitize_text_field($input['social_icon_style']) : 'circle';
        $sanitized['social_icon_size'] = isset($input['social_icon_size']) ? absint($input['social_icon_size']) : 36;
        $sanitized['social_icon_color'] = isset($input['social_icon_color']) ? sanitize_hex_color($input['social_icon_color']) : '';
        $sanitized['social_icon_hover_color'] = isset($input['social_icon_hover_color']) ? sanitize_hex_color($input['social_icon_hover_color']) : '';
        
        // Copyright
        $sanitized['enable_copyright'] = isset($input['enable_copyright']) ? 1 : 0;
        $sanitized['copyright_text'] = wp_kses_post($input['copyright_text']);
        $sanitized['copyright_bg_color'] = sanitize_hex_color($input['copyright_bg_color']);
        $sanitized['copyright_text_color'] = sanitize_hex_color($input['copyright_text_color']);
        $sanitized['copyright_alignment'] = sanitize_text_field($input['copyright_alignment']);
        // New styling fields
        $sanitized['copyright_font_size'] = isset($input['copyright_font_size']) ? absint($input['copyright_font_size']) : 14;
        $allowed_weights = array('light','normal','bold');
        $sanitized['copyright_font_weight'] = isset($input['copyright_font_weight']) && in_array($input['copyright_font_weight'], $allowed_weights) ? sanitize_text_field($input['copyright_font_weight']) : 'normal';
        $sanitized['copyright_letter_spacing'] = isset($input['copyright_letter_spacing']) ? floatval($input['copyright_letter_spacing']) : 0;
        $sanitized['copyright_padding_top'] = isset($input['copyright_padding_top']) ? absint($input['copyright_padding_top']) : 20;
        $sanitized['copyright_padding_bottom'] = isset($input['copyright_padding_bottom']) ? absint($input['copyright_padding_bottom']) : 20;
        $sanitized['copyright_border_top'] = isset($input['copyright_border_top']) ? 1 : 0;
        $sanitized['copyright_border_color'] = isset($input['copyright_border_color']) ? sanitize_hex_color($input['copyright_border_color']) : '#333333';
        $sanitized['copyright_border_width'] = isset($input['copyright_border_width']) ? absint($input['copyright_border_width']) : 1;
        $sanitized['copyright_link_color'] = isset($input['copyright_link_color']) ? sanitize_hex_color($input['copyright_link_color']) : '';
        $sanitized['copyright_link_hover_color'] = isset($input['copyright_link_hover_color']) ? sanitize_hex_color($input['copyright_link_hover_color']) : '';
        // Advanced: custom CSS / JS
        $sanitized['custom_footer_css'] = isset($input['custom_footer_css']) ? wp_strip_all_tags($input['custom_footer_css']) : '';
        $sanitized['custom_footer_js'] = isset($input['custom_footer_js']) ? wp_strip_all_tags($input['custom_footer_js']) : '';
        
        return $sanitized;
    }
}

// Initialize
if (is_admin()) {
    new RossFooterOptions();
}