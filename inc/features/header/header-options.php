<?php
/**
 * Header Options Module
 * Controls everything visible in the site header
 */

class RossHeaderOptions {
    
    private $options;
    
    public function __construct() {
        $this->options = get_option('ross_theme_header_options');
        add_action('admin_init', array($this, 'register_header_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_header_scripts'), 0);
        
        // AJAX handlers for header templates
        add_action('wp_ajax_ross_apply_header_template', array($this, 'ajax_apply_header_template'));
        add_action('wp_ajax_ross_preview_header_template', array($this, 'ajax_preview_header_template'));
        add_action('wp_ajax_ross_restore_header_backup', array($this, 'ajax_restore_header_backup'));
        add_action('wp_ajax_ross_delete_header_backup', array($this, 'ajax_delete_header_backup'));

            // Show custom success notice after saving
            add_action('admin_notices', array($this, 'show_settings_saved_notice'));
            add_action('update_option_ross_theme_header_options', array($this, 'on_header_options_updated'), 10, 3);
    }
    
    public function enqueue_header_scripts($hook) {
        // Enqueue on any Ross Theme header admin page (covers submenu hook variations)
        if (strpos($hook, 'ross-theme-header') !== false || strpos($hook, 'ross-theme') !== false) {
            // CRITICAL: Enqueue media library FIRST
            wp_enqueue_media();
            
            // Then jQuery
            wp_enqueue_script('jquery');
            
            // Then color picker
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            // WP editor for rich announcement editing
            if ( function_exists('wp_enqueue_editor') ) {
                wp_enqueue_editor();
            } else {
                wp_enqueue_script('wp-editor');
            }
            
            // Finally our uploader script and header admin helpers
            wp_enqueue_script('ross-uploader-standalone', get_template_directory_uri() . '/assets/js/admin/uploader-standalone.js', array(), '1.0.0', false);
            wp_enqueue_script('ross-header-admin', get_template_directory_uri() . '/assets/js/admin/header-options.js', array('jquery', 'wp-color-picker'), '1.0.0', true);
        }
    }
    
    public function register_header_settings() {
        register_setting(
            'ross_theme_header_group',
            'ross_theme_header_options',
            array($this, 'sanitize_header_options')
        );
        
        // ===== GENERAL SETTINGS TAB =====
        $this->add_layout_section();
        $this->add_logo_section();
        $this->add_navigation_section();
        
        // ===== CONTENT & FEATURES TAB =====
        $this->add_topbar_section();
        $this->add_announcement_section();
        $this->add_cta_section();
        
        // ===== STYLING & APPEARANCE TAB =====
        $this->add_appearance_section();
        
        // ===== MOBILE & TABLET TAB =====
        $this->add_mobile_section();
    }
    
    private function add_layout_section() {
        add_settings_section(
            'ross_header_layout_section',
            '🧱 Layout & Structure',
            array($this, 'layout_section_callback'),
            'ross-theme-header-layout'
        );

        // ===== BASIC LAYOUT =====
        add_settings_field(
            'header_style',
            'Header Style (Legacy)',
            array($this, 'header_style_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'header_width',
            'Header Container Width',
            array($this, 'header_width_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'header_center',
            'Center Header Content',
            array($this, 'header_center_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        // ===== STICKY HEADER =====
        add_settings_field(
            'sticky_header',
            'Enable Sticky Header',
            array($this, 'sticky_header_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'sticky_behavior',
            'Sticky Behavior',
            array($this, 'sticky_behavior_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'sticky_scroll_threshold',
            'Scroll Threshold (px)',
            array($this, 'sticky_scroll_threshold_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'sticky_shrink_header',
            'Shrink Header When Sticky',
            array($this, 'sticky_shrink_header_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'sticky_header_height',
            'Sticky Header Height (px)',
            array($this, 'sticky_header_height_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'sticky_animation_duration',
            'Animation Duration (ms)',
            array($this, 'sticky_animation_duration_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'sticky_easing',
            'Animation Easing',
            array($this, 'sticky_easing_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        // ===== GLASSMORPHISM EFFECTS =====
        add_settings_field(
            'header_glass_effect',
            'Glass Effect',
            array($this, 'header_glass_effect_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'transparent_homepage',
            'Transparent on Homepage',
            array($this, 'transparent_homepage_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );
    }
    
    private function add_logo_section() {
        add_settings_section(
            'ross_header_logo_section',
            '🧭 Logo & Branding',
            array($this, 'logo_section_callback'),
            'ross-theme-header-logo'
        );
        
        add_settings_field(
            'logo_upload',
            'Upload Logo',
            array($this, 'logo_upload_callback'),
            'ross-theme-header-logo',
            'ross_header_logo_section'
        );
        
        add_settings_field(
            'logo_dark',
            'Dark Version Logo',
            array($this, 'logo_dark_callback'),
            'ross-theme-header-logo',
            'ross_header_logo_section'
        );
        
        add_settings_field(
            'logo_width',
            'Logo Max Width (px)',
            array($this, 'logo_width_callback'),
            'ross-theme-header-logo',
            'ross_header_logo_section'
        );
        
        add_settings_field(
            'logo_padding',
            'Logo Padding (px)',
            array($this, 'logo_padding_callback'),
            'ross-theme-header-logo',
            'ross_header_logo_section'
        );
        
        add_settings_field(
            'show_site_title',
            'Show Site Title',
            array($this, 'show_site_title_callback'),
            'ross-theme-header-logo',
            'ross_header_logo_section'
        );
    }
    
    private function add_topbar_section() {
        add_settings_section(
            'ross_header_topbar_section',
            '☎️ Top Bar',
            array($this, 'topbar_section_callback'),
            'ross-theme-header-topbar'
        );
        
        add_settings_field(
            'enable_topbar',
            'Enable Top Bar',
            array($this, 'enable_topbar_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );
        
        add_settings_field(
            'topbar_left_content',
            'Left Section Content',
            array($this, 'topbar_left_content_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'enable_topbar_left',
            'Enable Left Section',
            array($this, 'enable_topbar_left_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );
        
        add_settings_field(
            'topbar_bg_color',
            'Background Color',
            array($this, 'topbar_bg_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );
        
        add_settings_field(
            'topbar_text_color',
            'Text Color',
            array($this, 'topbar_text_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_icon_hover_color',
            'Icon Hover Color',
            array($this, 'topbar_icon_hover_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_icon_color',
            'Phone Number Color',
            array($this, 'topbar_icon_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        // Additional topbar fields
        add_settings_field(
            'enable_social',
            'Enable Social Icons',
            array($this, 'enable_social_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_facebook',
            'Facebook URL',
            array($this, 'social_facebook_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_twitter',
            'Twitter URL',
            array($this, 'social_twitter_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_linkedin',
            'LinkedIn URL',
            array($this, 'social_linkedin_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'phone_number',
            'Phone Number',
            array($this, 'phone_number_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'enable_announcement',
            'Enable Announcement',
            array($this, 'enable_announcement_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'announcement_text',
            'Announcement Text',
            array($this, 'announcement_text_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'color_palette',
            'Color Palette',
            array($this, 'color_palette_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );
        
        add_settings_field(
            'announcement_animation',
            'Announcement Animation',
            array($this, 'announcement_animation_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        // Announcement styling fields (moved to Announcement section but keep callbacks available)
        add_settings_field(
            'announcement_bg_color',
            'Announcement Background',
            array($this, 'announcement_bg_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'announcement_text_color',
            'Announcement Text Color',
            array($this, 'announcement_text_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'announcement_font_size',
            'Announcement Font Size',
            array($this, 'announcement_font_size_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_links',
            'Social Links (custom)',
            array($this, 'social_links_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_size',
            'Social Icon Size',
            array($this, 'social_icon_size_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_shape',
            'Social Icon Shape',
            array($this, 'social_icon_shape_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_color',
            'Social Icon Color',
            array($this, 'social_icon_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_bg_color',
            'Social Icon Background',
            array($this, 'social_icon_bg_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_effect',
            'Social Icon Effect',
            array($this, 'social_icon_effect_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_border_color',
            'Social Icon Border Color',
            array($this, 'social_icon_border_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_border_size',
            'Social Icon Border Size',
            array($this, 'social_icon_border_size_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'social_icon_width',
            'Social Icon Width',
            array($this, 'social_icon_width_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        // Style Enhancements
        add_settings_field(
            'topbar_shadow_enable',
            'Enable Drop Shadow',
            array($this, 'topbar_shadow_enable_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_gradient_enable',
            'Enable Gradient Background',
            array($this, 'topbar_gradient_enable_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_gradient_color1',
            'Gradient Color 1',
            array($this, 'topbar_gradient_color1_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_gradient_color2',
            'Gradient Color 2',
            array($this, 'topbar_gradient_color2_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_border_color',
            'Border Bottom Color',
            array($this, 'topbar_border_color_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        add_settings_field(
            'topbar_border_width',
            'Border Bottom Width (px)',
            array($this, 'topbar_border_width_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );

        // Custom Icon Links
        add_settings_field(
            'topbar_custom_icon_links',
            'Custom Icon Links',
            array($this, 'topbar_custom_icon_links_callback'),
            'ross-theme-header-topbar',
            'ross_header_topbar_section'
        );
    }
    
    private function add_navigation_section() {
        add_settings_section(
            'ross_header_nav_section',
            '🧭 Navigation Menu',
            array($this, 'nav_section_callback'),
            'ross-theme-header-nav'
        );
        
        add_settings_field(
            'menu_alignment',
            'Menu Alignment',
            array($this, 'menu_alignment_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_font_size',
            'Menu Font Size (px)',
            array($this, 'menu_font_size_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'active_item_color',
            'Active Item Color',
            array($this, 'active_item_color_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_hover_color',
            'Menu Hover Color',
            array($this, 'menu_hover_color_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );

        add_settings_field(
            'menu_bg_color',
            'Menu Background Color',
            array($this, 'menu_bg_color_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );

        add_settings_field(
            'menu_border_color',
            'Menu Border Color',
            array($this, 'menu_border_color_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_hover_effect',
            'Menu Item Hover Effect',
            array($this, 'menu_hover_effect_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_hover_underline_style',
            'Hover Underline Style',
            array($this, 'menu_hover_underline_style_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_font_family',
            'Menu Font Family',
            array($this, 'menu_font_family_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_font_weight',
            'Menu Font Weight',
            array($this, 'menu_font_weight_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_letter_spacing',
            'Menu Letter Spacing (px)',
            array($this, 'menu_letter_spacing_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'menu_text_transform',
            'Menu Text Transform',
            array($this, 'menu_text_transform_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
    }

    /**
     * Announcement Section (moved out of Top Bar)
     */
    private function add_announcement_section() {
        add_settings_section(
            'ross_header_announcement_section',
            '📣 Announcement',
            array($this, 'announcement_section_callback'),
            'ross-theme-header-announcement'
        );

        add_settings_field(
            'enable_announcement',
            'Enable Announcement',
            array($this, 'enable_announcement_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_text',
            'Announcement Text',
            array($this, 'announcement_text_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_animation',
            'Announcement Animation',
            array($this, 'announcement_animation_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_bg_color',
            'Announcement Background',
            array($this, 'announcement_bg_color_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_text_color',
            'Announcement Text Color',
            array($this, 'announcement_text_color_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_font_size',
            'Announcement Font Size',
            array($this, 'announcement_font_size_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );
        
        add_settings_field(
            'announcement_sticky',
            'Sticky Announcement',
            array($this, 'announcement_sticky_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_sticky_offset',
            'Sticky Offset (px)',
            array($this, 'announcement_sticky_offset_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        add_settings_field(
            'announcement_position',
            'Announcement Position',
            array($this, 'announcement_position_callback'),
            'ross-theme-header-announcement',
            'ross_header_announcement_section'
        );

        // Announcement styling fields (colors, font size) are still registered as topbar style fields
    }

    public function announcement_section_callback() {
        echo '<p>Create and customize announcement messages that appear in your header.</p>';
    }

    public function announcement_bg_color_callback() {
        $value = isset($this->options['announcement_bg_color']) ? $this->options['announcement_bg_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[announcement_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function announcement_text_color_callback() {
        $value = isset($this->options['announcement_text_color']) ? $this->options['announcement_text_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_header_options[announcement_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }

    public function announcement_font_size_callback() {
        $value = isset($this->options['announcement_font_size']) ? $this->options['announcement_font_size'] : '14px';
        ?>
        <select name="ross_theme_header_options[announcement_font_size]">
            <option value="12px" <?php selected($value, '12px'); ?>>Small (12px)</option>
            <option value="14px" <?php selected($value, '14px'); ?>>Medium (14px)</option>
            <option value="16px" <?php selected($value, '16px'); ?>>Large (16px)</option>
            <option value="18px" <?php selected($value, '18px'); ?>>Extra Large (18px)</option>
        </select>
        <?php
    }

    public function announcement_sticky_callback() {
        $value = isset($this->options['announcement_sticky']) ? $this->options['announcement_sticky'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[announcement_sticky]" value="1" <?php checked(1, $value); ?> />
        <label for="announcement_sticky">Enable sticky announcement (keeps strip visible while scrolling)</label>
        <?php
    }

    public function announcement_sticky_offset_callback() {
        $value = isset($this->options['announcement_sticky_offset']) ? $this->options['announcement_sticky_offset'] : 0;
        ?>
        <input type="number" name="ross_theme_header_options[announcement_sticky_offset]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <p class="description">Optional offset from the top when sticky (e.g., to avoid overlapping a sticky admin/topbar). Default 0.</p>
        <?php
    }

    public function announcement_position_callback() {
        $value = isset($this->options['announcement_position']) ? $this->options['announcement_position'] : 'top_of_topbar';
        ?>
        <select name="ross_theme_header_options[announcement_position]">
            <option value="top_of_topbar" <?php selected($value, 'top_of_topbar'); ?>>Top of Topbar (topmost)</option>
            <option value="below_topbar" <?php selected($value, 'below_topbar'); ?>>Below Topbar (top of header)</option>
            <option value="below_header" <?php selected($value, 'below_header'); ?>>Below Header (after header)</option>
        </select>
        <p class="description">Choose the announcement placement: top of page, between topbar and header, or below the header.</p>
        <?php
    }
    
    private function add_cta_section() {
        add_settings_section(
            'ross_header_cta_section',
            '🔍 Search & CTA (Right Area)',
            array($this, 'cta_section_callback'),
            'ross-theme-header-cta'
        );
        
        add_settings_field(
            'enable_search',
            'Enable Search Icon',
            array($this, 'enable_search_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'search_type',
            'Search Display Type',
            array($this, 'search_type_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'search_placeholder',
            'Search Placeholder Text',
            array($this, 'search_placeholder_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'enable_cta_button',
            'Enable CTA Button',
            array($this, 'enable_cta_button_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_text',
            'Button Text',
            array($this, 'cta_button_text_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_color',
            'Button Background Color',
            array($this, 'cta_button_color_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_text_color',
            'Button Text Color',
            array($this, 'cta_button_text_color_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );

        add_settings_field(
            'cta_button_hover_text_color',
            'Button Hover Text Color',
            array($this, 'cta_button_hover_text_color_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );

        add_settings_field(
            'cta_button_url',
            'Button Link (URL)',
            array($this, 'cta_button_url_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_style',
            'Button Style',
            array($this, 'cta_button_style_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_size',
            'Button Size',
            array($this, 'cta_button_size_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_font_size',
            'Button Font Size',
            array($this, 'cta_button_font_size_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_border_radius',
            'Button Border Radius',
            array($this, 'cta_button_border_radius_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_hover_effect',
            'Button Hover Effect',
            array($this, 'cta_button_hover_effect_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_text_hover_effect',
            'Button Text Hover Effect',
            array($this, 'cta_button_text_hover_effect_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        // Enhanced Search Settings
        add_settings_field(
            'search_icon_style',
            'Search Icon Style',
            array($this, 'search_icon_style_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'search_animation',
            'Search Animation',
            array($this, 'search_animation_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'search_mobile_hide',
            'Hide Search on Mobile',
            array($this, 'search_mobile_hide_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        // Enhanced CTA Settings
        add_settings_field(
            'cta_button_icon',
            'Button Icon',
            array($this, 'cta_button_icon_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_icon_position',
            'Icon Position',
            array($this, 'cta_button_icon_position_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_shadow',
            'Button Shadow',
            array($this, 'cta_button_shadow_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_target',
            'Link Target',
            array($this, 'cta_button_target_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_mobile_hide',
            'Hide CTA on Mobile',
            array($this, 'cta_button_mobile_hide_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'cta_button_position',
            'CTA Position in Header',
            array($this, 'cta_button_position_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        // Layout Settings
        add_settings_field(
            'header_actions_order',
            'Actions Order (Search/CTA)',
            array($this, 'header_actions_order_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
        
        add_settings_field(
            'header_actions_spacing',
            'Actions Spacing',
            array($this, 'header_actions_spacing_callback'),
            'ross-theme-header-cta',
            'ross_header_cta_section'
        );
    }
    
    private function add_appearance_section() {
        add_settings_section(
            'ross_header_appearance_section',
            '� Header Appearance & Styling',
            array($this, 'appearance_section_callback'),
            'ross-theme-header-appearance'
        );

        // ===== BACKGROUND =====
        add_settings_field(
            'header_bg_color',
            'Background Color',
            array($this, 'header_bg_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        // ===== TYPOGRAPHY =====
        add_settings_field(
            'header_text_color',
            'Text Color',
            array($this, 'header_text_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_font_size',
            'Font Size',
            array($this, 'header_font_size_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_font_weight',
            'Font Weight',
            array($this, 'header_font_weight_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        // ===== BORDER & SHADOW =====
        add_settings_field(
            'header_border_enable',
            'Enable Border',
            array($this, 'header_border_enable_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_border_color',
            'Border Color',
            array($this, 'header_border_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_shadow_enable',
            'Enable Shadow',
            array($this, 'header_shadow_enable_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_shadow_color',
            'Shadow Color',
            array($this, 'header_shadow_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        // ===== SPACING =====
        add_settings_field(
            'header_height',
            'Header Height',
            array($this, 'header_height_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_padding_top',
            'Padding Top',
            array($this, 'header_padding_top_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        add_settings_field(
            'header_padding_bottom',
            'Padding Bottom',
            array($this, 'header_padding_bottom_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        // ===== EFFECTS =====
        add_settings_field(
            'header_opacity',
            'Header Opacity',
            array($this, 'header_opacity_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );

        // ===== SPECIAL =====
        add_settings_field(
            'header_custom_css',
            'Custom CSS',
            array($this, 'header_custom_css_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
    }

    private function add_mobile_section() {
        add_settings_section(
            'ross_header_mobile_section',
            '📱 Mobile & Tablet Settings',
            array($this, 'mobile_section_callback'),
            'ross-theme-header-mobile'
        );

        // ===== MOBILE VISIBILITY =====
        add_settings_field(
            'sticky_hide_mobile',
            'Hide Sticky Header on Mobile',
            array($this, 'sticky_hide_mobile_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        add_settings_field(
            'search_mobile_hide',
            'Hide Search on Mobile',
            array($this, 'search_mobile_hide_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        add_settings_field(
            'cta_button_mobile_hide',
            'Hide CTA Button on Mobile',
            array($this, 'cta_button_mobile_hide_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        // ===== MOBILE LAYOUT =====
        add_settings_field(
            'mobile_menu_breakpoint',
            'Mobile Menu Breakpoint (px)',
            array($this, 'mobile_menu_breakpoint_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        add_settings_field(
            'mobile_header_height',
            'Mobile Header Height (px)',
            array($this, 'mobile_header_height_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        add_settings_field(
            'mobile_logo_size',
            'Mobile Logo Size (px)',
            array($this, 'mobile_logo_size_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        // ===== TABLET SETTINGS =====
        add_settings_field(
            'tablet_menu_breakpoint',
            'Tablet Menu Breakpoint (px)',
            array($this, 'tablet_menu_breakpoint_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );

        add_settings_field(
            'tablet_header_height',
            'Tablet Header Height (px)',
            array($this, 'tablet_header_height_callback'),
            'ross-theme-header-mobile',
            'ross_header_mobile_section'
        );
    }

    // Section Callbacks
    public function layout_section_callback() {
        echo '<p><strong>Container Width:</strong> Choose whether header spans full browser width or is constrained to page container.</p>';
        echo '<p><strong>Content Alignment:</strong> Center-align logo and navigation within the header (only available with Full Browser Width).</p>';
        echo '<p><strong>Sticky Header:</strong> Configure sticky behavior, animations, and effects.</p>';
    }
    
    public function logo_section_callback() {
        echo '<p>Configure your logo and branding elements.</p>';
    }
    
    public function topbar_section_callback() {
        echo '<p><strong>Basic Settings:</strong> Enable/disable top bar and configure left section content.</p>';
        echo '<p><strong>Social Icons:</strong> Add social media links and phone number to the top bar.</p>';
        echo '<p><strong>Styling:</strong> Customize colors and borders for the top bar appearance.</p>';
    }
    
    public function nav_section_callback() {
        echo '<p>Customize the appearance and behavior of your navigation menu items.</p>';
    }
    
    public function cta_section_callback() {
        echo '<p><strong>Search:</strong> Configure search icon and functionality.</p>';
        echo '<p><strong>CTA Button:</strong> Add and style a call-to-action button in the header.</p>';
    }
    
    public function appearance_section_callback() {
        echo '<p>Customize the visual appearance of your header with essential styling options.</p>';
        echo '<style>
        /* Clean Header Appearance Layout */
        #ross-theme-header-appearance .form-table { margin-top: 20px; }
        #ross-theme-header-appearance .form-table th { 
            background: #f8f9fa; 
            padding: 15px 12px 8px; 
            font-weight: 600; 
            color: #495057; 
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        #ross-theme-header-appearance .form-table td { 
            padding: 12px; 
            border-bottom: 1px solid #e9ecef;
        }
        #ross-theme-header-appearance .form-table tr:first-child th,
        #ross-theme-header-appearance .form-table tr:first-child td {
            border-top: 2px solid #007cba;
        }
        #ross-theme-header-appearance .form-table tr:nth-child(5) th,
        #ross-theme-header-appearance .form-table tr:nth-child(5) td {
            border-top: 2px solid #28a745;
        }
        #ross-theme-header-appearance .form-table tr:nth-child(9) th,
        #ross-theme-header-appearance .form-table tr:nth-child(9) td {
            border-top: 2px solid #dc3545;
        }
        #ross-theme-header-appearance .form-table tr:nth-child(13) th,
        #ross-theme-header-appearance .form-table tr:nth-child(13) td {
            border-top: 2px solid #ffc107;
        }
        #ross-theme-header-appearance .form-table tr:nth-child(16) th,
        #ross-theme-header-appearance .form-table tr:nth-child(16) td {
            border-top: 2px solid #17a2b8;
        }
        #ross-theme-header-appearance .form-table tr:nth-child(19) th,
        #ross-theme-header-appearance .form-table tr:nth-child(19) td {
            border-top: 2px solid #6f42c1;
        }
        #ross-theme-header-appearance .color-picker { border-radius: 4px; }
        #ross-theme-header-appearance input[type="number"] { width: 80px; }
        #ross-theme-header-appearance select { min-width: 150px; }
        </style>';

        // Add section headers
        echo '<h3 style="color: #007cba; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">🎨 Background Settings</h3>';
        echo '<h3 style="color: #28a745; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">📝 Typography Settings</h3>';
        echo '<h3 style="color: #dc3545; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">🔲 Border & Shadow Settings</h3>';
        echo '<h3 style="color: #ffc107; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">📏 Spacing Settings</h3>';
        echo '<h3 style="color: #17a2b8; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">✨ Effects Settings</h3>';
        echo '<h3 style="color: #6f42c1; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">⚙️ Special Settings</h3>';
    }

    public function mobile_section_callback() {
        echo '<p>Configure how your header behaves on mobile and tablet devices. These settings only affect screens smaller than desktop.</p>';
        echo '<style>
        /* Mobile Section Layout */
        #ross-theme-header-mobile .form-table { margin-top: 20px; }
        #ross-theme-header-mobile .form-table th {
            background: #f8f9fa;
            padding: 15px 12px 8px;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        #ross-theme-header-mobile .form-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        #ross-theme-header-mobile .form-table tr:nth-child(4) th,
        #ross-theme-header-mobile .form-table tr:nth-child(4) td {
            border-top: 2px solid #007cba;
        }
        #ross-theme-header-mobile .form-table tr:nth-child(7) th,
        #ross-theme-header-mobile .form-table tr:nth-child(7) td {
            border-top: 2px solid #28a745;
        }
        #ross-theme-header-mobile input[type="number"] { width: 80px; }
        </style>';

        // Add section headers
        echo '<h3 style="color: #dc3545; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">👁️ Mobile Visibility</h3>';
        echo '<h3 style="color: #007cba; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">📱 Mobile Layout</h3>';
        echo '<h3 style="color: #28a745; margin: 30px 0 10px; font-size: 16px; font-weight: 600;">📲 Tablet Settings</h3>';
    }

    // ===== MOBILE CALLBACK METHODS =====
    public function mobile_menu_breakpoint_callback() {
        $value = isset($this->options['mobile_menu_breakpoint']) ? $this->options['mobile_menu_breakpoint'] : '768';
        ?>
        <input type="number" name="ross_theme_header_options[mobile_menu_breakpoint]" value="<?php echo esc_attr($value); ?>" min="320" max="1024" step="1" />
        <p class="description">Screen width in pixels where the mobile menu activates (default: 768px)</p>
        <?php
    }

    public function mobile_header_height_callback() {
        $value = isset($this->options['mobile_header_height']) ? $this->options['mobile_header_height'] : '60';
        ?>
        <input type="number" name="ross_theme_header_options[mobile_header_height]" value="<?php echo esc_attr($value); ?>" min="40" max="120" step="1" />
        <p class="description">Header height on mobile devices in pixels (default: 60px)</p>
        <?php
    }

    public function mobile_logo_size_callback() {
        $value = isset($this->options['mobile_logo_size']) ? $this->options['mobile_logo_size'] : '35';
        ?>
        <input type="number" name="ross_theme_header_options[mobile_logo_size]" value="<?php echo esc_attr($value); ?>" min="20" max="80" step="1" />
        <p class="description">Logo height on mobile devices in pixels (default: 35px)</p>
        <?php
    }

    public function tablet_menu_breakpoint_callback() {
        $value = isset($this->options['tablet_menu_breakpoint']) ? $this->options['tablet_menu_breakpoint'] : '1024';
        ?>
        <input type="number" name="ross_theme_header_options[tablet_menu_breakpoint]" value="<?php echo esc_attr($value); ?>" min="768" max="1200" step="1" />
        <p class="description">Screen width in pixels where tablet layout activates (default: 1024px)</p>
        <?php
    }

    public function tablet_header_height_callback() {
        $value = isset($this->options['tablet_header_height']) ? $this->options['tablet_header_height'] : '70';
        ?>
        <input type="number" name="ross_theme_header_options[tablet_header_height]" value="<?php echo esc_attr($value); ?>" min="50" max="120" step="1" />
        <p class="description">Header height on tablet devices in pixels (default: 70px)</p>
        <?php
    }

    public function header_style_callback() {
        $value = isset($this->options['header_style']) ? $this->options['header_style'] : 'default';
        ?>
        <select name="ross_theme_header_options[header_style]" id="header_style">
            <option value="default" <?php selected($value, 'default'); ?>>Default (Logo Left, Menu Center)</option>
            <option value="centered" <?php selected($value, 'centered'); ?>>Centered (Logo Center, Menu Below)</option>
            <option value="transparent" <?php selected($value, 'transparent'); ?>>Transparent (For Hero Sections)</option>
            <option value="minimal" <?php selected($value, 'minimal'); ?>>Minimal (Clean & Simple)</option>
        </select>
        <p class="description">Choose a header layout style. For advanced features, use the Template System above.</p>
        <?php
    }
    
    public function header_width_callback() {
        $value = isset($this->options['header_width']) ? $this->options['header_width'] : 'contained';
        ?>
        <select name="ross_theme_header_options[header_width]" id="header_width">
            <option value="full" <?php selected($value, 'full'); ?>>Full Browser Width</option>
            <option value="contained" <?php selected($value, 'contained'); ?>>Constrained to Container</option>
        </select>
        <p class="description">Choose whether header spans full browser width or is constrained to page container width. Centering is only available with Full Browser Width.</p>
        <?php
    }

    public function header_center_callback() {
        $value = isset($this->options['header_center']) ? $this->options['header_center'] : 0;
        $header_width = isset($this->options['header_width']) ? $this->options['header_width'] : 'contained';

        // Always show the field - JavaScript will handle enabling/disabling
        $is_disabled = ($header_width !== 'full');
        $disabled_attr = $is_disabled ? 'disabled="disabled"' : '';
        $label_color = $is_disabled ? 'color: #999;' : '';
        $desc_color = $is_disabled ? 'color: #666;' : '';
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_center]" value="1" <?php checked(1, $value); ?> <?php echo $disabled_attr; ?> />
        <label for="header_center" style="<?php echo $label_color; ?>">Center-align logo and navigation within header</label>
        <p class="description" style="<?php echo $desc_color; ?>">
            <?php if ($is_disabled): ?>
                Centering is only available when Header Container Width is set to "Full Browser Width".
            <?php else: ?>
                Centers the header content (logo and menu) horizontally within the full-width header.
            <?php endif; ?>
        </p>
        <?php
    }

    public function sticky_header_callback() {
        $value = isset($this->options['sticky_header']) ? $this->options['sticky_header'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[sticky_header]" value="1" <?php checked(1, $value); ?> />
        <label for="sticky_header">Enable sticky header on scroll</label>
        <?php
    }
    
    public function header_height_callback() {
        $value = isset($this->options['header_height']) ? $this->options['header_height'] : '80';
        ?>
        <input type="number" name="ross_theme_header_options[header_height]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function sticky_behavior_callback() {
        $value = isset($this->options['sticky_behavior']) ? $this->options['sticky_behavior'] : 'always';
        ?>
        <select name="ross_theme_header_options[sticky_behavior]">
            <option value="always" <?php selected($value, 'always'); ?>>Always Sticky</option>
            <option value="scroll_up" <?php selected($value, 'scroll_up'); ?>>Show on Scroll Up</option>
            <option value="after_scroll" <?php selected($value, 'after_scroll'); ?>>After Threshold</option>
        </select>
        <p class="description">When sticky header becomes active</p>
        <?php
    }
    
    public function sticky_scroll_threshold_callback() {
        $value = isset($this->options['sticky_scroll_threshold']) ? $this->options['sticky_scroll_threshold'] : '100';
        ?>
        <input type="number" name="ross_theme_header_options[sticky_scroll_threshold]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <p class="description">Scroll distance before sticky activates (for "After Threshold" behavior)</p>
        <?php
    }
    
    public function sticky_shrink_header_callback() {
        $value = isset($this->options['sticky_shrink_header']) ? $this->options['sticky_shrink_header'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[sticky_shrink_header]" value="1" <?php checked(1, $value); ?> />
        <label>Reduce header height when sticky</label>
        <?php
    }
    
    public function sticky_header_height_callback() {
        $value = isset($this->options['sticky_header_height']) ? $this->options['sticky_header_height'] : '70';
        ?>
        <input type="number" name="ross_theme_header_options[sticky_header_height]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <p class="description">Header height when sticky and shrunk (default: 70px for better appearance)</p>
        <?php
    }

    public function sticky_animation_duration_callback() {
        $value = isset($this->options['sticky_animation_duration']) ? $this->options['sticky_animation_duration'] : '300';
        ?>
        <input type="number" name="ross_theme_header_options[sticky_animation_duration]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="2000" /> ms
        <p class="description">Animation duration in milliseconds (0-2000ms)</p>
        <?php
    }

    public function sticky_easing_callback() {
        $value = isset($this->options['sticky_easing']) ? $this->options['sticky_easing'] : 'ease-out';
        ?>
        <select name="ross_theme_header_options[sticky_easing]">
            <option value="ease" <?php selected($value, 'ease'); ?>>Ease</option>
            <option value="ease-in" <?php selected($value, 'ease-in'); ?>>Ease In</option>
            <option value="ease-out" <?php selected($value, 'ease-out'); ?>>Ease Out</option>
            <option value="ease-in-out" <?php selected($value, 'ease-in-out'); ?>>Ease In Out</option>
            <option value="linear" <?php selected($value, 'linear'); ?>>Linear</option>
        </select>
        <p class="description">Animation easing function for smooth transitions</p>
        <?php
    }

    public function sticky_hide_mobile_callback() {
        $value = isset($this->options['sticky_hide_mobile']) ? $this->options['sticky_hide_mobile'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[sticky_hide_mobile]" value="1" <?php checked(1, $value); ?> />
        <label>Hide sticky header on mobile devices</label>
        <p class="description">Completely hide the sticky header on screens smaller than the mobile breakpoint</p>
        <?php
    }
    
    // Field Callbacks - Logo Section
    public function logo_upload_callback() {
        $value = isset($this->options['logo_upload']) ? $this->options['logo_upload'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[logo_upload]" id="logo_upload" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <input type="button" class="button ross-upload-button" data-target="logo_upload" value="Upload Logo" />
        <?php if ($value): ?>
            <p><img src="<?php echo esc_url($value); ?>" style="max-width: 200px; height: auto; margin-top: 10px;" /></p>
        <?php endif; ?>
        <?php
    }
    
    public function logo_dark_callback() {
        $value = isset($this->options['logo_dark']) ? $this->options['logo_dark'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[logo_dark]" id="logo_dark" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <input type="button" class="button ross-upload-button" data-target="logo_dark" value="Upload Dark Logo" />
        <?php if ($value): ?>
            <p><img src="<?php echo esc_url($value); ?>" style="max-width: 200px; height: auto; margin-top: 10px;" /></p>
        <?php endif; ?>
        <?php
    }
    
    public function logo_width_callback() {
        $value = isset($this->options['logo_width']) ? $this->options['logo_width'] : '200';
        ?>
        <input type="number" name="ross_theme_header_options[logo_width]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function logo_padding_callback() {
        $value = isset($this->options['logo_padding']) ? $this->options['logo_padding'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[logo_padding]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" /> px
        <p class="description">Add padding around the logo (default: 0px).</p>
        <?php
    }
    
    public function show_site_title_callback() {
        $value = isset($this->options['show_site_title']) ? $this->options['show_site_title'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_header_options[show_site_title]" value="1" <?php checked(1, $value); ?> />
        <label for="show_site_title">Display site title alongside logo</label>
        <?php
    }
    
    // Field Callbacks - Top Bar Section
    public function enable_topbar_callback() {
        $value = isset($this->options['enable_topbar']) ? $this->options['enable_topbar'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[enable_topbar]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_topbar">Enable top bar</label>
        <?php
    }
    
    public function topbar_left_content_callback() {
        $value = isset($this->options['topbar_left_content']) ? $this->options['topbar_left_content'] : '';
        // Use WP Editor for richer content in the left section
        $editor_id = 'ross_topbar_left_editor';
        $settings = array(
            'textarea_name' => 'ross_theme_header_options[topbar_left_content]',
            'textarea_rows' => 3,
            'teeny' => true,
            'media_buttons' => false,
        );
        wp_editor($value, $editor_id, $settings);
        echo '<p class="description">Use the editor for rich content: small text, icons, links or HTML. Keep it concise for the top bar.</p>';
    }
    
    public function topbar_bg_color_callback() {
        $value = isset($this->options['topbar_bg_color']) ? $this->options['topbar_bg_color'] : '#001946';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <?php
    }
    
    public function topbar_text_color_callback() {
        $value = isset($this->options['topbar_text_color']) ? $this->options['topbar_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }

    public function topbar_icon_hover_color_callback() {
        $value = isset($this->options['topbar_icon_hover_color']) ? $this->options['topbar_icon_hover_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_icon_hover_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <p class="description">Hover color for social icons and phone numbers in the top bar.</p>
        <?php
    }

    public function topbar_icon_color_callback() {
        $value = isset($this->options['topbar_icon_color']) ? $this->options['topbar_icon_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_icon_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <p class="description">Color for phone numbers in the top bar.</p>
        <?php
    }

    // Additional Topbar callbacks
    public function enable_social_callback() {
        $value = isset($this->options['enable_social']) ? $this->options['enable_social'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[enable_social]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_social">Show social icons in the top bar</label>
        <?php
    }

    public function social_facebook_callback() {
        $value = isset($this->options['social_facebook']) ? $this->options['social_facebook'] : '';
        ?>
        <input type="url" name="ross_theme_header_options[social_facebook]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://facebook.com/yourpage" />
        <?php
    }

    public function social_twitter_callback() {
        $value = isset($this->options['social_twitter']) ? $this->options['social_twitter'] : '';
        ?>
        <input type="url" name="ross_theme_header_options[social_twitter]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://twitter.com/yourprofile" />
        <?php
    }

    public function social_linkedin_callback() {
        $value = isset($this->options['social_linkedin']) ? $this->options['social_linkedin'] : '';
        ?>
        <input type="url" name="ross_theme_header_options[social_linkedin]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://linkedin.com/company/yourcompany" />
        <?php
    }

    public function phone_number_callback() {
        $value = isset($this->options['phone_number']) ? $this->options['phone_number'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[phone_number]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="e.g., +44 20 7123 4567" />
        <?php
    }

    public function enable_announcement_callback() {
        $value = isset($this->options['enable_announcement']) ? $this->options['enable_announcement'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[enable_announcement]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_announcement">Enable announcement (centered marquee)</label>
        <?php
    }

    public function announcement_text_callback() {
        $value = isset($this->options['announcement_text']) ? $this->options['announcement_text'] : '';
        // Use WP editor for HTML-capable announcement content
        $editor_id = 'ross_announcement_editor';
        $settings = array(
            'textarea_name' => 'ross_theme_header_options[announcement_text]',
            'textarea_rows' => 4,
            'teeny' => true,
            'media_buttons' => false,
        );
        wp_editor($value, $editor_id, $settings);
        echo '<p class="description">You can use basic HTML. Links and icons will be rendered in the top bar.</p>';
        
        // Keep backward compatibility if JS needs a simple field
        echo '<input type="hidden" id="announcement_editor_placeholder" />';
    }

    public function enable_topbar_left_callback() {
        $value = isset($this->options['enable_topbar_left']) ? $this->options['enable_topbar_left'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_header_options[enable_topbar_left]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_topbar_left">Show left section (phone/text/custom html)</label>
        <?php
    }

    public function announcement_animation_callback() {
        // Keep the control minimal: only Slide and Marquee per design request
        $value = isset($this->options['announcement_animation']) ? $this->options['announcement_animation'] : 'marquee';
        ?>
        <select name="ross_theme_header_options[announcement_animation]" id="announcement_animation">
            <option value="marquee" <?php selected($value, 'marquee'); ?>>Marquee (scroll)</option>
            <option value="slide" <?php selected($value, 'slide'); ?>>Slide</option>
        </select>
        <p class="description">Choose how the announcement will animate in the top bar.</p>
        <?php
    }

    public function social_links_callback() {
        $links = isset($this->options['social_links']) ? $this->options['social_links'] : array();
        if (!is_array($links)) $links = array();
        ?>
        <div id="ross-social-links-repeater">
            <div class="ross-social-template" style="display:none;">
                <div class="ross-social-item">
                    <input type="text" name="ross_theme_header_options[social_links][__index__][icon]" value="" placeholder="Icon (emoji or class) e.g. 🔵 or fa-facebook" class="regular-text" />
                    <input type="url" name="ross_theme_header_options[social_links][__index__][url]" value="" placeholder="https://example.com" class="regular-text" />
                    <button class="button ross-social-remove">Remove</button>
                </div>
            </div>
            <?php foreach ($links as $i => $link): ?>
                <div class="ross-social-item">
                    <input type="text" name="ross_theme_header_options[social_links][<?php echo $i; ?>][icon]" value="<?php echo esc_attr($link['icon']); ?>" placeholder="Icon (emoji or class)" class="regular-text" />
                    <input type="url" name="ross_theme_header_options[social_links][<?php echo $i; ?>][url]" value="<?php echo esc_attr($link['url']); ?>" placeholder="https://example.com" class="regular-text" />
                    <button class="button ross-social-remove">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <p>
            <button id="ross-social-add" class="button">Add Social Link</button>
            <span class="description">Add icon and URL pairs. Icons can be emojis or CSS classes (e.g., fa-facebook) if your site includes icon fonts.</span>
        </p>
        <?php
    }

    public function social_icon_size_callback() {
        $value = isset($this->options['social_icon_size']) ? $this->options['social_icon_size'] : 'medium';
        ?>
        <select name="ross_theme_header_options[social_icon_size]">
            <option value="small" <?php selected($value, 'small'); ?>>Small</option>
            <option value="medium" <?php selected($value, 'medium'); ?>>Medium</option>
            <option value="large" <?php selected($value, 'large'); ?>>Large</option>
        </select>
        <p class="description">Choose the size of social icons in the top bar.</p>
        <?php
    }

    public function social_icon_shape_callback() {
        $value = isset($this->options['social_icon_shape']) ? $this->options['social_icon_shape'] : 'circle';
        ?>
        <select name="ross_theme_header_options[social_icon_shape]">
            <option value="circle" <?php selected($value, 'circle'); ?>>Circle</option>
            <option value="square" <?php selected($value, 'square'); ?>>Square</option>
            <option value="rounded" <?php selected($value, 'rounded'); ?>>Rounded</option>
        </select>
        <p class="description">Choose the shape of social icons in the top bar.</p>
        <?php
    }

    public function social_icon_color_callback() {
        $value = isset($this->options['social_icon_color']) ? $this->options['social_icon_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[social_icon_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <p class="description">Choose the color of social icons.</p>
        <?php
    }

    public function social_icon_bg_color_callback() {
        $value = isset($this->options['social_icon_bg_color']) ? $this->options['social_icon_bg_color'] : 'transparent';
        ?>
        <input type="text" name="ross_theme_header_options[social_icon_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="transparent" />
        <p class="description">Choose the background color of social icons. Use 'transparent' for no background.</p>
        <?php
    }

    public function social_icon_effect_callback() {
        $value = isset($this->options['social_icon_effect']) ? $this->options['social_icon_effect'] : 'none';
        ?>
        <select name="ross_theme_header_options[social_icon_effect]">
            <option value="none" <?php selected($value, 'none'); ?>>No Effect</option>
            <option value="bounce" <?php selected($value, 'bounce'); ?>>Bounce</option>
            <option value="pulse" <?php selected($value, 'pulse'); ?>>Pulse</option>
            <option value="rotate" <?php selected($value, 'rotate'); ?>>Rotate</option>
            <option value="scale" <?php selected($value, 'scale'); ?>>Scale</option>
        </select>
        <p class="description">Choose a hover effect for social icons.</p>
        <?php
    }

    public function social_icon_border_color_callback() {
        $value = isset($this->options['social_icon_border_color']) ? $this->options['social_icon_border_color'] : 'transparent';
        ?>
        <input type="text" name="ross_theme_header_options[social_icon_border_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="transparent" />
        <p class="description">Choose the border color of social icons. Use 'transparent' for no border.</p>
        <?php
    }

    public function social_icon_border_size_callback() {
        $value = isset($this->options['social_icon_border_size']) ? $this->options['social_icon_border_size'] : '0';
        ?>
        <select name="ross_theme_header_options[social_icon_border_size]">
            <option value="0" <?php selected($value, '0'); ?>>No Border</option>
            <option value="1" <?php selected($value, '1'); ?>>1px</option>
            <option value="2" <?php selected($value, '2'); ?>>2px</option>
            <option value="3" <?php selected($value, '3'); ?>>3px</option>
        </select>
        <p class="description">Choose the border thickness for social icons.</p>
        <?php
    }

    public function social_icon_width_callback() {
        $value = isset($this->options['social_icon_width']) ? $this->options['social_icon_width'] : '32';
        ?>
        <input type="number" name="ross_theme_header_options[social_icon_width]" value="<?php echo esc_attr($value); ?>" min="20" max="100" step="1" />
        <p class="description">Set the width of social icons in pixels (20-100px). Height will match width for square icons.</p>
        <?php
    }

    public function color_palette_callback() {
        $selected = isset($this->options['color_palette']) ? $this->options['color_palette'] : 'professional';
        
        $palettes = array(
            'professional' => array(
                'name' => 'Professional',
                'topbar_bg' => '#1A1A1A',
                'topbar_text' => '#FFFFFF',
                'header_bg' => '#FFFFFF',
                'header_text' => '#333333'
            ),
            'dark' => array(
                'name' => 'Dark Modern',
                'topbar_bg' => '#0A0E27',
                'topbar_text' => '#E0E0E0',
                'header_bg' => '#1A1A2E',
                'header_text' => '#E0E0E0'
            ),
            'light' => array(
                'name' => 'Light Modern',
                'topbar_bg' => '#F5F5F5',
                'topbar_text' => '#333333',
                'header_bg' => '#FFFFFF',
                'header_text' => '#333333'
            ),
            'colorful' => array(
                'name' => 'Colorful',
                'topbar_bg' => '#E5C902',
                'topbar_text' => '#333333',
                'header_bg' => '#FFFFFF',
                'header_text' => '#333333'
            ),
            'ocean' => array(
                'name' => 'Ocean Blue',
                'topbar_bg' => '#1E3A5F',
                'topbar_text' => '#FFFFFF',
                'header_bg' => '#F0F4F8',
                'header_text' => '#1E3A5F'
            )
        );
        ?>
        <div class="color-palette-selector">
            <?php foreach ($palettes as $key => $palette): ?>
                <label class="palette-option <?php echo $selected === $key ? 'selected' : ''; ?>" onclick="rossPaletteSelect('<?php echo esc_attr($key); ?>')">
                    <input type="radio" name="ross_theme_header_options[color_palette]" value="<?php echo esc_attr($key); ?>" <?php checked($selected, $key); ?> style="display:none;" />
                    <span class="palette-name"><?php echo esc_html($palette['name']); ?></span>
                    <div class="palette-preview">
                        <div class="palette-color" style="background-color: <?php echo esc_attr($palette['topbar_bg']); ?>; width: 20px; height: 20px; border: 2px solid #ddd; border-radius: 4px; display: inline-block; margin-right: 5px;"></div>
                        <div class="palette-color" style="background-color: <?php echo esc_attr($palette['header_bg']); ?>; width: 20px; height: 20px; border: 2px solid #ddd; border-radius: 4px; display: inline-block;"></div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>
        <p class="description">Choose a color palette for your header and topbar. You can still customize individual colors below.</p>
        <script>
            function rossPaletteSelect(palette) {
                document.querySelector('input[name="ross_theme_header_options[color_palette]"][value="' + palette + '"]').checked = true;
                
                // Auto-fill colors based on palette selection
                var palettes = {
                    'professional': { topbar_bg: '#1A1A1A', topbar_text: '#FFFFFF', header_bg: '#FFFFFF', header_text: '#333333' },
                    'dark': { topbar_bg: '#0A0E27', topbar_text: '#E0E0E0', header_bg: '#1A1A2E', header_text: '#E0E0E0' },
                    'light': { topbar_bg: '#F5F5F5', topbar_text: '#333333', header_bg: '#FFFFFF', header_text: '#333333' },
                    'colorful': { topbar_bg: '#E5C902', topbar_text: '#333333', header_bg: '#FFFFFF', header_text: '#333333' },
                    'ocean': { topbar_bg: '#1E3A5F', topbar_text: '#FFFFFF', header_bg: '#F0F4F8', header_text: '#1E3A5F' }
                };
                
                if (palettes[palette]) {
                    // Try to find and update color picker fields
                    var topbarBgInput = document.querySelector('input[name="ross_theme_header_options[topbar_bg_color]"]');
                    var topbarTextInput = document.querySelector('input[name="ross_theme_header_options[topbar_text_color]"]');
                    var headerBgInput = document.querySelector('input[name="ross_theme_header_options[header_bg_color]"]');
                    var headerTextInput = document.querySelector('input[name="ross_theme_header_options[header_text_color]"]');
                    
                    if (topbarBgInput) topbarBgInput.value = palettes[palette].topbar_bg;
                    if (topbarTextInput) topbarTextInput.value = palettes[palette].topbar_text;
                    if (headerBgInput) headerBgInput.value = palettes[palette].header_bg;
                    if (headerTextInput) headerTextInput.value = palettes[palette].header_text;
                }
                
                // Update visual selection
                document.querySelectorAll('.palette-option').forEach(function(el) {
                    el.classList.remove('selected');
                });
                event.currentTarget.classList.add('selected');
            }
        </script>
        <style>
            .color-palette-selector {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 15px;
                margin-bottom: 15px;
            }
            
            .palette-option {
                border: 2px solid #ddd;
                border-radius: 8px;
                padding: 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
            }
            
            .palette-option:hover {
                border-color: #999;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            
            .palette-option.selected {
                border-color: #E5C902;
                background-color: #fffaf0;
                box-shadow: 0 2px 12px rgba(229, 201, 2, 0.3);
            }
            
            .palette-name {
                display: block;
                font-weight: 600;
                margin-bottom: 8px;
                font-size: 14px;
            }
            
            .palette-preview {
                display: flex;
                justify-content: center;
                gap: 8px;
            }
        </style>
        <?php
    }

    // Style Enhancement Callbacks
    public function topbar_shadow_enable_callback() {
        $value = isset($this->options['topbar_shadow_enable']) ? $this->options['topbar_shadow_enable'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[topbar_shadow_enable]" value="1" <?php checked(1, $value); ?> />
        <label for="topbar_shadow_enable">Add drop shadow to top bar</label>
        <?php
    }

    public function topbar_gradient_enable_callback() {
        $value = isset($this->options['topbar_gradient_enable']) ? $this->options['topbar_gradient_enable'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[topbar_gradient_enable]" value="1" <?php checked(1, $value); ?> />
        <label for="topbar_gradient_enable">Use gradient instead of solid background</label>
        <?php
    }

    public function topbar_gradient_color1_callback() {
        $value = isset($this->options['topbar_gradient_color1']) ? $this->options['topbar_gradient_color1'] : '#001946';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_gradient_color1]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#001946" />
        <p class="description">First gradient color (start)</p>
        <?php
    }

    public function topbar_gradient_color2_callback() {
        $value = isset($this->options['topbar_gradient_color2']) ? $this->options['topbar_gradient_color2'] : '#003d7a';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_gradient_color2]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#003d7a" />
        <p class="description">Second gradient color (end)</p>
        <?php
    }

    public function topbar_border_color_callback() {
        $value = isset($this->options['topbar_border_color']) ? $this->options['topbar_border_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_border_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function topbar_border_width_callback() {
        $value = isset($this->options['topbar_border_width']) ? $this->options['topbar_border_width'] : 0;
        ?>
        <input type="number" name="ross_theme_header_options[topbar_border_width]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="5" /> px
        <p class="description">Border width at bottom of top bar (0-5px)</p>
        <?php
    }

    public function topbar_custom_icon_links_callback() {
        $links = isset($this->options['topbar_custom_icon_links']) ? $this->options['topbar_custom_icon_links'] : array();
        if (!is_array($links)) $links = array();
        ?>
        <div id="ross-icon-links-repeater" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
            <div class="ross-icon-link-template" style="display:none;">
                <div class="ross-icon-link-item" style="background: white; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; display: flex; gap: 10px; align-items: flex-start;">
                    <div style="flex: 1;">
                        <input type="text" name="ross_theme_header_options[topbar_custom_icon_links][__index__][icon]" value="" placeholder="Icon (emoji or icon class) e.g. 📱 or fas fa-phone" class="regular-text" style="width: 100%; margin-bottom: 8px;" />
                        <input type="url" name="ross_theme_header_options[topbar_custom_icon_links][__index__][url]" value="" placeholder="https://example.com or tel:+1234567890" class="regular-text" style="width: 100%; margin-bottom: 8px;" />
                        <input type="text" name="ross_theme_header_options[topbar_custom_icon_links][__index__][title]" value="" placeholder="Tooltip title (optional)" class="regular-text" style="width: 100%;" />
                    </div>
                    <button type="button" class="button button-secondary ross-icon-link-remove" style="margin-top: 0;">Remove</button>
                </div>
            </div>
            <?php foreach ($links as $i => $link): ?>
                <div class="ross-icon-link-item" style="background: white; padding: 15px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; display: flex; gap: 10px; align-items: flex-start;">
                    <div style="flex: 1;">
                        <input type="text" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $i; ?>][icon]" value="<?php echo esc_attr($link['icon']); ?>" placeholder="Icon (emoji or icon class)" class="regular-text" style="width: 100%; margin-bottom: 8px;" />
                        <input type="url" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $i; ?>][url]" value="<?php echo esc_attr($link['url']); ?>" placeholder="https://example.com or tel:+1234567890" class="regular-text" style="width: 100%; margin-bottom: 8px;" />
                        <input type="text" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $i; ?>][title]" value="<?php echo esc_attr(isset($link['title']) ? $link['title'] : ''); ?>" placeholder="Tooltip title (optional)" class="regular-text" style="width: 100%;" />
                    </div>
                    <button type="button" class="button button-secondary ross-icon-link-remove" style="margin-top: 0;">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <p style="margin-top: 12px;">
            <button type="button" id="ross-icon-link-add" class="button button-primary">+ Add Icon Link</button>
        </p>
        <p class="description" style="margin-top: 10px;">Add custom icon links to appear in the top bar. Examples:<br>
        • Email: icon="✉️", url="mailto:info@example.com"<br>
        • Phone: icon="📱", url="tel:+1234567890"<br>
        • WhatsApp: icon="💬", url="https://wa.me/1234567890"
        </p>
        <script type="text/javascript">
        (function() {
            var itemIndex = <?php echo count($links); ?>;
            
            document.getElementById('ross-icon-link-add').addEventListener('click', function(e) {
                e.preventDefault();
                var template = document.querySelector('.ross-icon-link-template').innerHTML;
                var newHtml = template.replace(/__index__/g, itemIndex);
                var newItem = document.createElement('div');
                newItem.innerHTML = newHtml;
                document.getElementById('ross-icon-links-repeater').insertBefore(newItem.firstElementChild, document.getElementById('ross-icon-link-add').parentElement);
                itemIndex++;
                attachRemoveListeners();
            });
            
            function attachRemoveListeners() {
                document.querySelectorAll('.ross-icon-link-remove').forEach(function(btn) {
                    btn.onclick = function(e) {
                        e.preventDefault();
                        this.closest('.ross-icon-link-item').remove();
                    };
                });
            }
            
            attachRemoveListeners();
        })();
        </script>
        <?php
    }
    
    // Field Callbacks - Navigation Section
    public function menu_alignment_callback() {
        $value = isset($this->options['menu_alignment']) ? $this->options['menu_alignment'] : 'left';
        ?>
        <select name="ross_theme_header_options[menu_alignment]" id="menu_alignment">
            <option value="left" <?php selected($value, 'left'); ?>>Left</option>
            <option value="center" <?php selected($value, 'center'); ?>>Center</option>
            <option value="right" <?php selected($value, 'right'); ?>>Right</option>
        </select>
        <?php
    }
    
    public function menu_font_size_callback() {
        $value = isset($this->options['menu_font_size']) ? $this->options['menu_font_size'] : '16';
        ?>
        <input type="number" name="ross_theme_header_options[menu_font_size]" id="menu_font_size" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function active_item_color_callback() {
        $value = isset($this->options['active_item_color']) ? $this->options['active_item_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[active_item_color]" id="active_item_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function menu_hover_color_callback() {
        $value = isset($this->options['menu_hover_color']) ? $this->options['menu_hover_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[menu_hover_color]" id="menu_hover_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <p class="description">Color applied when hovering menu links (desktop).</p>
        <?php
    }

    public function menu_bg_color_callback() {
        $value = isset($this->options['menu_bg_color']) ? $this->options['menu_bg_color'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[menu_bg_color]" id="menu_bg_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="" />
        <p class="description">Optional background color for the menu area. Leave empty for transparent.</p>
        <?php
    }

    public function menu_border_color_callback() {
        $value = isset($this->options['menu_border_color']) ? $this->options['menu_border_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[menu_border_color]" id="menu_border_color" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <p class="description">Color for the underline/border used by menu items (and active indicator).</p>
        <?php
    }
    
    public function menu_hover_effect_callback() {
        $value = isset($this->options['menu_hover_effect']) ? $this->options['menu_hover_effect'] : 'underline';
        ?>
        <select name="ross_theme_header_options[menu_hover_effect]" id="menu_hover_effect">
            <option value="underline" <?php selected($value, 'underline'); ?>>Underline</option>
            <option value="background" <?php selected($value, 'background'); ?>>Background Color</option>
            <option value="none" <?php selected($value, 'none'); ?>>None</option>
        </select>
        <p class="description">Visual effect when hovering over menu items</p>
        <?php
    }
    
    public function menu_hover_underline_style_callback() {
        $value = isset($this->options['menu_hover_underline_style']) ? $this->options['menu_hover_underline_style'] : 'slide';
        ?>
        <select name="ross_theme_header_options[menu_hover_underline_style]" id="menu_hover_underline_style">
            <option value="slide" <?php selected($value, 'slide'); ?>>Slide In</option>
            <option value="fade" <?php selected($value, 'fade'); ?>>Fade In</option>
            <option value="instant" <?php selected($value, 'instant'); ?>>Instant</option>
        </select>
        <p class="description">Animation style for underline hover effect</p>
        <?php
    }
    
    public function menu_font_family_callback() {
        $value = isset($this->options['menu_font_family']) ? $this->options['menu_font_family'] : 'inherit';
        ?>
        <select name="ross_theme_header_options[menu_font_family]" id="menu_font_family">
            <option value="inherit" <?php selected($value, 'inherit'); ?>>Inherit from Theme</option>
            <option value="Arial, sans-serif" <?php selected($value, 'Arial, sans-serif'); ?>>Arial</option>
            <option value="'Helvetica Neue', Helvetica, Arial, sans-serif" <?php selected($value, "'Helvetica Neue', Helvetica, Arial, sans-serif"); ?>>Helvetica</option>
            <option value="'Courier New', monospace" <?php selected($value, "'Courier New', monospace"); ?>>Courier New</option>
            <option value="Georgia, serif" <?php selected($value, "Georgia, serif"); ?>>Georgia</option>
            <option value="'Times New Roman', Times, serif" <?php selected($value, "'Times New Roman', Times, serif"); ?>>Times New Roman</option>
            <option value="'Trebuchet MS', sans-serif" <?php selected($value, "'Trebuchet MS', sans-serif"); ?>>Trebuchet MS</option>
            <option value="Verdana, sans-serif" <?php selected($value, "Verdana, sans-serif"); ?>>Verdana</option>
        </select>
        <p class="description">Font family for menu items</p>
        <?php
    }
    
    public function menu_font_weight_callback() {
        $value = isset($this->options['menu_font_weight']) ? $this->options['menu_font_weight'] : '600';
        ?>
        <select name="ross_theme_header_options[menu_font_weight]" id="menu_font_weight">
            <option value="300" <?php selected($value, '300'); ?>>Light (300)</option>
            <option value="400" <?php selected($value, '400'); ?>>Regular (400)</option>
            <option value="500" <?php selected($value, '500'); ?>>Medium (500)</option>
            <option value="600" <?php selected($value, '600'); ?>>Semi Bold (600)</option>
            <option value="700" <?php selected($value, '700'); ?>>Bold (700)</option>
            <option value="800" <?php selected($value, '800'); ?>>Extra Bold (800)</option>
            <option value="900" <?php selected($value, '900'); ?>>Black (900)</option>
        </select>
        <p class="description">Font weight for menu items</p>
        <?php
    }
    
    public function menu_letter_spacing_callback() {
        $value = isset($this->options['menu_letter_spacing']) ? $this->options['menu_letter_spacing'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[menu_letter_spacing]" id="menu_letter_spacing" value="<?php echo esc_attr($value); ?>" step="0.1" min="-2" max="5" />
        <p class="description">Letter spacing in pixels (can be negative)</p>
        <?php
    }
    
    public function menu_text_transform_callback() {
        $value = isset($this->options['menu_text_transform']) ? $this->options['menu_text_transform'] : 'none';
        ?>
        <select name="ross_theme_header_options[menu_text_transform]" id="menu_text_transform">
            <option value="none" <?php selected($value, 'none'); ?>>None</option>
            <option value="uppercase" <?php selected($value, 'uppercase'); ?>>Uppercase</option>
            <option value="lowercase" <?php selected($value, 'lowercase'); ?>>Lowercase</option>
            <option value="capitalize" <?php selected($value, 'capitalize'); ?>>Capitalize</option>
        </select>
        <p class="description">Text transformation for menu items</p>
        <?php
    }
    
    // Field Callbacks - CTA Section
    public function enable_search_callback() {
        $value = isset($this->options['enable_search']) ? $this->options['enable_search'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_header_options[enable_search]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_search">Show search icon in header</label>
        <?php
    }
    
    public function search_type_callback() {
        $value = isset($this->options['search_type']) ? $this->options['search_type'] : 'modal';
        ?>
        <select name="ross_theme_header_options[search_type]">
            <option value="modal" <?php selected($value, 'modal'); ?>>Modal Overlay</option>
            <option value="dropdown" <?php selected($value, 'dropdown'); ?>>Dropdown</option>
            <option value="inline" <?php selected($value, 'inline'); ?>>Inline Expand</option>
        </select>
        <p class="description">How search displays when icon is clicked</p>
        <?php
    }
    
    public function search_placeholder_callback() {
        $value = isset($this->options['search_placeholder']) ? $this->options['search_placeholder'] : 'Search...';
        ?>
        <input type="text" name="ross_theme_header_options[search_placeholder]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <p class="description">Placeholder text in search field</p>
        <?php
    }
    
    public function enable_cta_button_callback() {
        $value = isset($this->options['enable_cta_button']) ? $this->options['enable_cta_button'] : 1;
        ?>
        <input type="checkbox" name="ross_theme_header_options[enable_cta_button]" value="1" <?php checked(1, $value); ?> />
        <label for="enable_cta_button">Show CTA button in header</label>
        <?php
    }
    
    public function cta_button_text_callback() {
        $value = isset($this->options['cta_button_text']) ? $this->options['cta_button_text'] : 'Get Free Consultation';
        ?>
        <input type="text" name="ross_theme_header_options[cta_button_text]" value="<?php echo esc_attr($value); ?>" class="regular-text" />
        <?php
    }
    
    public function cta_button_color_callback() {
        $value = isset($this->options['cta_button_color']) ? $this->options['cta_button_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[cta_button_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function cta_button_url_callback() {
        $value = isset($this->options['cta_button_url']) ? $this->options['cta_button_url'] : '/contact';
        ?>
        <input type="text" name="ross_theme_header_options[cta_button_url]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://example.com/contact or #anchor" />
        <p class="description">Full URL for the CTA button (e.g., https://example.com/contact). Leave empty to use site contact page.</p>
        <?php
    }
    
    public function cta_button_text_color_callback() {
        $value = isset($this->options['cta_button_text_color']) ? $this->options['cta_button_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[cta_button_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <p class="description">Text color for CTA button</p>
        <?php
    }
    
    public function cta_button_hover_text_color_callback() {
        $value = isset($this->options['cta_button_hover_text_color']) ? $this->options['cta_button_hover_text_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[cta_button_hover_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <p class="description">Text color for CTA button on hover</p>
        <?php
    }
    
    public function cta_button_style_callback() {
        $value = isset($this->options['cta_button_style']) ? $this->options['cta_button_style'] : 'solid';
        ?>
        <select name="ross_theme_header_options[cta_button_style]">
            <option value="solid" <?php selected($value, 'solid'); ?>>Solid</option>
            <option value="outline" <?php selected($value, 'outline'); ?>>Outline</option>
            <option value="ghost" <?php selected($value, 'ghost'); ?>>Ghost</option>
            <option value="gradient" <?php selected($value, 'gradient'); ?>>Gradient</option>
        </select>
        <p class="description">Visual style for CTA button</p>
        <?php
    }
    
    public function cta_button_size_callback() {
        $value = isset($this->options['cta_button_size']) ? $this->options['cta_button_size'] : 'medium';
        ?>
        <select name="ross_theme_header_options[cta_button_size]">
            <option value="small" <?php selected($value, 'small'); ?>>Small</option>
            <option value="medium" <?php selected($value, 'medium'); ?>>Medium</option>
            <option value="large" <?php selected($value, 'large'); ?>>Large</option>
        </select>
        <p class="description">Size of the CTA button</p>
        <?php
    }
    
    public function cta_button_font_size_callback() {
        $value = isset($this->options['cta_button_font_size']) ? $this->options['cta_button_font_size'] : '16';
        ?>
        <input type="number" name="ross_theme_header_options[cta_button_font_size]" value="<?php echo esc_attr($value); ?>" min="10" max="32" step="1" />
        <span>px</span>
        <p class="description">Font size for CTA button text (10-32px)</p>
        <?php
    }
    
    public function cta_button_border_radius_callback() {
        $value = isset($this->options['cta_button_border_radius']) ? $this->options['cta_button_border_radius'] : '8';
        ?>
        <input type="number" name="ross_theme_header_options[cta_button_border_radius]" value="<?php echo esc_attr($value); ?>" min="0" max="50" step="1" />
        <span>px</span>
        <p class="description">Border radius for rounded corners (0-50px)</p>
        <?php
    }
    
    public function cta_button_hover_effect_callback() {
        $value = isset($this->options['cta_button_hover_effect']) ? $this->options['cta_button_hover_effect'] : 'scale';
        ?>
        <select name="ross_theme_header_options[cta_button_hover_effect]">
            <option value="none" <?php selected($value, 'none'); ?>>No Effect</option>
            <option value="scale" <?php selected($value, 'scale'); ?>>Scale Up</option>
            <option value="glow" <?php selected($value, 'glow'); ?>>Glow</option>
            <option value="slide" <?php selected($value, 'slide'); ?>>Slide</option>
            <option value="bounce" <?php selected($value, 'bounce'); ?>>Bounce</option>
        </select>
        <p class="description">Animation effect when hovering over CTA button</p>
        <?php
    }
    
    public function cta_button_text_hover_effect_callback() {
        $value = isset($this->options['cta_button_text_hover_effect']) ? $this->options['cta_button_text_hover_effect'] : 'none';
        ?>
        <select name="ross_theme_header_options[cta_button_text_hover_effect]">
            <option value="none" <?php selected($value, 'none'); ?>>No Effect</option>
            <option value="fade" <?php selected($value, 'fade'); ?>>Fade In/Out</option>
            <option value="slide-up" <?php selected($value, 'slide-up'); ?>>Slide Up</option>
            <option value="slide-down" <?php selected($value, 'slide-down'); ?>>Slide Down</option>
            <option value="scale-text" <?php selected($value, 'scale-text'); ?>>Scale Text</option>
            <option value="glow-text" <?php selected($value, 'glow-text'); ?>>Text Glow</option>
        </select>
        <p class="description">Animation effect for CTA button text on hover</p>
        <?php
    }
    
    // Enhanced Search Callbacks
    public function search_icon_style_callback() {
        $value = isset($this->options['search_icon_style']) ? $this->options['search_icon_style'] : 'magnifying-glass';
        ?>
        <select name="ross_theme_header_options[search_icon_style]">
            <option value="magnifying-glass" <?php selected($value, 'magnifying-glass'); ?>>Magnifying Glass</option>
            <option value="search-text" <?php selected($value, 'search-text'); ?>>Search Text</option>
            <option value="search-circle" <?php selected($value, 'search-circle'); ?>>Search Circle</option>
            <option value="minimal" <?php selected($value, 'minimal'); ?>>Minimal Dot</option>
        </select>
        <p class="description">Visual style for the search icon</p>
        <?php
    }
    
    public function search_animation_callback() {
        $value = isset($this->options['search_animation']) ? $this->options['search_animation'] : 'none';
        ?>
        <select name="ross_theme_header_options[search_animation]">
            <option value="none" <?php selected($value, 'none'); ?>>No Animation</option>
            <option value="pulse" <?php selected($value, 'pulse'); ?>>Pulse</option>
            <option value="bounce" <?php selected($value, 'bounce'); ?>>Bounce</option>
            <option value="rotate" <?php selected($value, 'rotate'); ?>>Rotate</option>
            <option value="glow" <?php selected($value, 'glow'); ?>>Glow</option>
        </select>
        <p class="description">Animation effect for the search icon</p>
        <?php
    }
    
    public function search_mobile_hide_callback() {
        $value = isset($this->options['search_mobile_hide']) ? $this->options['search_mobile_hide'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[search_mobile_hide]" value="1" <?php checked(1, $value); ?> />
        <label for="search_mobile_hide">Hide search icon on mobile devices</label>
        <?php
    }
    
    // Enhanced CTA Callbacks
    public function cta_button_icon_callback() {
        $value = isset($this->options['cta_button_icon']) ? $this->options['cta_button_icon'] : 'none';
        ?>
        <select name="ross_theme_header_options[cta_button_icon]">
            <option value="none" <?php selected($value, 'none'); ?>>No Icon</option>
            <option value="arrow-right" <?php selected($value, 'arrow-right'); ?>>Arrow Right →</option>
            <option value="arrow-left" <?php selected($value, 'arrow-left'); ?>>← Arrow Left</option>
            <option value="phone" <?php selected($value, 'phone'); ?>>📞 Phone</option>
            <option value="email" <?php selected($value, 'email'); ?>>✉️ Email</option>
            <option value="chat" <?php selected($value, 'chat'); ?>>💬 Chat</option>
            <option value="download" <?php selected($value, 'download'); ?>>📥 Download</option>
            <option value="play" <?php selected($value, 'play'); ?>>▶️ Play</option>
            <option value="star" <?php selected($value, 'star'); ?>>⭐ Star</option>
            <option value="heart" <?php selected($value, 'heart'); ?>>❤️ Heart</option>
        </select>
        <p class="description">Icon to display in the CTA button</p>
        <?php
    }
    
    public function cta_button_icon_position_callback() {
        $value = isset($this->options['cta_button_icon_position']) ? $this->options['cta_button_icon_position'] : 'left';
        ?>
        <select name="ross_theme_header_options[cta_button_icon_position]">
            <option value="left" <?php selected($value, 'left'); ?>>Left of Text</option>
            <option value="right" <?php selected($value, 'right'); ?>>Right of Text</option>
        </select>
        <p class="description">Position of the icon relative to button text</p>
        <?php
    }
    
    public function cta_button_shadow_callback() {
        $value = isset($this->options['cta_button_shadow']) ? $this->options['cta_button_shadow'] : 'none';
        ?>
        <select name="ross_theme_header_options[cta_button_shadow]">
            <option value="none" <?php selected($value, 'none'); ?>>No Shadow</option>
            <option value="small" <?php selected($value, 'small'); ?>>Small Shadow</option>
            <option value="medium" <?php selected($value, 'medium'); ?>>Medium Shadow</option>
            <option value="large" <?php selected($value, 'large'); ?>>Large Shadow</option>
            <option value="glow" <?php selected($value, 'glow'); ?>>Glow Effect</option>
        </select>
        <p class="description">Shadow effect for the CTA button</p>
        <?php
    }
    
    public function cta_button_target_callback() {
        $value = isset($this->options['cta_button_target']) ? $this->options['cta_button_target'] : '_self';
        ?>
        <select name="ross_theme_header_options[cta_button_target]">
            <option value="_self" <?php selected($value, '_self'); ?>>Same Window</option>
            <option value="_blank" <?php selected($value, '_blank'); ?>>New Window/Tab</option>
        </select>
        <p class="description">How the CTA button link opens</p>
        <?php
    }
    
    public function cta_button_mobile_hide_callback() {
        $value = isset($this->options['cta_button_mobile_hide']) ? $this->options['cta_button_mobile_hide'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[cta_button_mobile_hide]" value="1" <?php checked(1, $value); ?> />
        <label for="cta_button_mobile_hide">Hide CTA button on mobile devices</label>
        <?php
    }
    
    public function cta_button_position_callback() {
        $value = isset($this->options['cta_button_position']) ? $this->options['cta_button_position'] : 'right';
        ?>
        <select name="ross_theme_header_options[cta_button_position]">
            <option value="left" <?php selected($value, 'left'); ?>>Left Side</option>
            <option value="center" <?php selected($value, 'center'); ?>>Center</option>
            <option value="right" <?php selected($value, 'right'); ?>>Right Side</option>
        </select>
        <p class="description">Position of CTA button within the header actions area</p>
        <?php
    }
    
    // Layout Callbacks
    public function header_actions_order_callback() {
        $value = isset($this->options['header_actions_order']) ? $this->options['header_actions_order'] : 'search-cta';
        ?>
        <select name="ross_theme_header_options[header_actions_order]">
            <option value="search-cta" <?php selected($value, 'search-cta'); ?>>Search → CTA</option>
            <option value="cta-search" <?php selected($value, 'cta-search'); ?>>CTA → Search</option>
        </select>
        <p class="description">Order of search and CTA elements in header</p>
        <?php
    }
    
    public function header_actions_spacing_callback() {
        $value = isset($this->options['header_actions_spacing']) ? $this->options['header_actions_spacing'] : '15';
        ?>
        <input type="number" name="ross_theme_header_options[header_actions_spacing]" value="<?php echo esc_attr($value); ?>" min="0" max="50" step="1" />
        <span>px</span>
        <p class="description">Spacing between search and CTA elements (0-50px)</p>
        <?php
    }
    
    // Field Callbacks - Layout Section (Sticky Behavior)
    
    // ===== ENTERPRISE APPEARANCE CALLBACKS =====
    
    // Background Section Callbacks
    public function header_bg_color_callback() {
        $value = isset($this->options['header_bg_color']) ? $this->options['header_bg_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[header_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <p class="description">Primary background color</p>
        <?php
    }
    
    public function header_bg_image_callback() {
        $value = isset($this->options['header_bg_image']) ? $this->options['header_bg_image'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[header_bg_image]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="https://example.com/image.jpg" />
        <button type="button" class="button upload-image-button" data-target="header_bg_image">Upload Image</button>
        <p class="description">Background image URL</p>
        <?php
    }
    
    public function header_bg_image_position_callback() {
        $value = isset($this->options['header_bg_image_position']) ? $this->options['header_bg_image_position'] : 'center center';
        ?>
        <select name="ross_theme_header_options[header_bg_image_position]">
            <option value="left top" <?php selected($value, 'left top'); ?>>Top Left</option>
            <option value="center top" <?php selected($value, 'center top'); ?>>Top Center</option>
            <option value="right top" <?php selected($value, 'right top'); ?>>Top Right</option>
            <option value="left center" <?php selected($value, 'left center'); ?>>Center Left</option>
            <option value="center center" <?php selected($value, 'center center'); ?>>Center</option>
            <option value="right center" <?php selected($value, 'right center'); ?>>Center Right</option>
            <option value="left bottom" <?php selected($value, 'left bottom'); ?>>Bottom Left</option>
            <option value="center bottom" <?php selected($value, 'center bottom'); ?>>Bottom Center</option>
            <option value="right bottom" <?php selected($value, 'right bottom'); ?>>Bottom Right</option>
        </select>
        <p class="description">Background image position</p>
        <?php
    }
    
    public function header_bg_image_size_callback() {
        $value = isset($this->options['header_bg_image_size']) ? $this->options['header_bg_image_size'] : 'cover';
        ?>
        <select name="ross_theme_header_options[header_bg_image_size]">
            <option value="auto" <?php selected($value, 'auto'); ?>>Auto</option>
            <option value="cover" <?php selected($value, 'cover'); ?>>Cover</option>
            <option value="contain" <?php selected($value, 'contain'); ?>>Contain</option>
            <option value="100% 100%" <?php selected($value, '100% 100%'); ?>>Stretch</option>
        </select>
        <p class="description">Background image sizing</p>
        <?php
    }
    
    public function header_bg_image_repeat_callback() {
        $value = isset($this->options['header_bg_image_repeat']) ? $this->options['header_bg_image_repeat'] : 'no-repeat';
        ?>
        <select name="ross_theme_header_options[header_bg_image_repeat]">
            <option value="no-repeat" <?php selected($value, 'no-repeat'); ?>>No Repeat</option>
            <option value="repeat" <?php selected($value, 'repeat'); ?>>Repeat</option>
            <option value="repeat-x" <?php selected($value, 'repeat-x'); ?>>Repeat Horizontally</option>
            <option value="repeat-y" <?php selected($value, 'repeat-y'); ?>>Repeat Vertically</option>
        </select>
        <p class="description">Background image repeat behavior</p>
        <?php
    }
    
    public function header_bg_overlay_callback() {
        $value = isset($this->options['header_bg_overlay']) ? $this->options['header_bg_overlay'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_bg_overlay]" value="1" <?php checked(1, $value); ?> />
        <label>Enable background overlay</label>
        <?php
    }
    
    public function header_bg_overlay_color_callback() {
        $value = isset($this->options['header_bg_overlay_color']) ? $this->options['header_bg_overlay_color'] : 'rgba(0,0,0,0.5)';
        ?>
        <input type="text" name="ross_theme_header_options[header_bg_overlay_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="rgba(0,0,0,0.5)" data-alpha="true" />
        <p class="description">Overlay color with transparency</p>
        <?php
    }
    
    public function header_bg_pattern_callback() {
        $value = isset($this->options['header_bg_pattern']) ? $this->options['header_bg_pattern'] : 'none';
        ?>
        <select name="ross_theme_header_options[header_bg_pattern]">
            <option value="none" <?php selected($value, 'none'); ?>>No Pattern</option>
            <option value="dots" <?php selected($value, 'dots'); ?>>Dots</option>
            <option value="lines" <?php selected($value, 'lines'); ?>>Lines</option>
            <option value="grid" <?php selected($value, 'grid'); ?>>Grid</option>
            <option value="diagonal" <?php selected($value, 'diagonal'); ?>>Diagonal Lines</option>
        </select>
        <p class="description">Built-in background pattern</p>
        <?php
    }
    
    public function header_bg_pattern_color_callback() {
        $value = isset($this->options['header_bg_pattern_color']) ? $this->options['header_bg_pattern_color'] : '#e5e7eb';
        ?>
        <input type="text" name="ross_theme_header_options[header_bg_pattern_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#e5e7eb" />
        <p class="description">Pattern color</p>
        <?php
    }
    
    // Typography Section Callbacks
    public function header_text_color_callback() {
        $value = isset($this->options['header_text_color']) ? $this->options['header_text_color'] : '#333333';
        ?>
        <input type="text" name="ross_theme_header_options[header_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#333333" />
        <p class="description">Primary text color for header elements</p>
        <?php
    }
    
    public function header_link_color_callback() {
        $value = isset($this->options['header_link_color']) ? $this->options['header_link_color'] : '#007cba';
        ?>
        <input type="text" name="ross_theme_header_options[header_link_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#007cba" />
        <p class="description">Link color for navigation items</p>
        <?php
    }
    
    public function header_link_hover_color_callback() {
        $value = isset($this->options['header_link_hover_color']) ? $this->options['header_link_hover_color'] : '#005a87';
        ?>
        <input type="text" name="ross_theme_header_options[header_link_hover_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#005a87" />
        <p class="description">Link hover color</p>
        <?php
    }
    
    public function header_font_size_callback() {
        $value = isset($this->options['header_font_size']) ? $this->options['header_font_size'] : '16';
        ?>
        <input type="number" name="ross_theme_header_options[header_font_size]" value="<?php echo esc_attr($value); ?>" class="small-text" min="10" max="24" /> px
        <p class="description">Base font size for header text</p>
        <?php
    }
    
    public function header_font_family_callback() {
        $value = isset($this->options['header_font_family']) ? $this->options['header_font_family'] : 'inherit';
        ?>
        <select name="ross_theme_header_options[header_font_family]">
            <option value="inherit" <?php selected($value, 'inherit'); ?>>Inherit from theme</option>
            <option value="Arial, sans-serif" <?php selected($value, 'Arial, sans-serif'); ?>>Arial</option>
            <option value="Helvetica, sans-serif" <?php selected($value, 'Helvetica, sans-serif'); ?>>Helvetica</option>
            <option value="Georgia, serif" <?php selected($value, 'Georgia, serif'); ?>>Georgia</option>
            <option value="Times New Roman, serif" <?php selected($value, 'Times New Roman, serif'); ?>>Times New Roman</option>
            <option value="Verdana, sans-serif" <?php selected($value, 'Verdana, sans-serif'); ?>>Verdana</option>
            <option value="Tahoma, sans-serif" <?php selected($value, 'Tahoma, sans-serif'); ?>>Tahoma</option>
            <option value="Trebuchet MS, sans-serif" <?php selected($value, 'Trebuchet MS, sans-serif'); ?>>Trebuchet MS</option>
        </select>
        <p class="description">Font family for header text</p>
        <?php
    }
    
    public function header_font_weight_callback() {
        $value = isset($this->options['header_font_weight']) ? $this->options['header_font_weight'] : '400';
        ?>
        <select name="ross_theme_header_options[header_font_weight]">
            <option value="100" <?php selected($value, '100'); ?>>Thin (100)</option>
            <option value="200" <?php selected($value, '200'); ?>>Extra Light (200)</option>
            <option value="300" <?php selected($value, '300'); ?>>Light (300)</option>
            <option value="400" <?php selected($value, '400'); ?>>Normal (400)</option>
            <option value="500" <?php selected($value, '500'); ?>>Medium (500)</option>
            <option value="600" <?php selected($value, '600'); ?>>Semi Bold (600)</option>
            <option value="700" <?php selected($value, '700'); ?>>Bold (700)</option>
            <option value="800" <?php selected($value, '800'); ?>>Extra Bold (800)</option>
            <option value="900" <?php selected($value, '900'); ?>>Black (900)</option>
        </select>
        <p class="description">Font weight for header text</p>
        <?php
    }
    
    public function header_line_height_callback() {
        $value = isset($this->options['header_line_height']) ? $this->options['header_line_height'] : '1.5';
        ?>
        <input type="number" name="ross_theme_header_options[header_line_height]" value="<?php echo esc_attr($value); ?>" class="small-text" min="1" max="3" step="0.1" />
        <p class="description">Line height for header text</p>
        <?php
    }
    
    public function header_letter_spacing_callback() {
        $value = isset($this->options['header_letter_spacing']) ? $this->options['header_letter_spacing'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[header_letter_spacing]" value="<?php echo esc_attr($value); ?>" class="small-text" min="-2" max="5" step="0.1" /> px
        <p class="description">Letter spacing for header text</p>
        <?php
    }
    
    public function header_text_transform_callback() {
        $value = isset($this->options['header_text_transform']) ? $this->options['header_text_transform'] : 'none';
        ?>
        <select name="ross_theme_header_options[header_text_transform]">
            <option value="none" <?php selected($value, 'none'); ?>>None</option>
            <option value="uppercase" <?php selected($value, 'uppercase'); ?>>Uppercase</option>
            <option value="lowercase" <?php selected($value, 'lowercase'); ?>>Lowercase</option>
            <option value="capitalize" <?php selected($value, 'capitalize'); ?>>Capitalize</option>
        </select>
        <p class="description">Text transformation for header elements</p>
        <?php
    }
    
    // Border Section Callbacks
    public function header_border_top_callback() {
        $value = isset($this->options['header_border_top']) ? $this->options['header_border_top'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_border_top]" value="1" <?php checked(1, $value); ?> />
        <label>Enable top border</label>
        <?php
    }
    
    public function header_border_top_width_callback() {
        $value = isset($this->options['header_border_top_width']) ? $this->options['header_border_top_width'] : '1';
        ?>
        <input type="number" name="ross_theme_header_options[header_border_top_width]" value="<?php echo esc_attr($value); ?>" class="small-text" min="1" max="10" /> px
        <p class="description">Top border width</p>
        <?php
    }
    
    public function header_border_top_color_callback() {
        $value = isset($this->options['header_border_top_color']) ? $this->options['header_border_top_color'] : '#e5e7eb';
        ?>
        <input type="text" name="ross_theme_header_options[header_border_top_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#e5e7eb" />
        <p class="description">Top border color</p>
        <?php
    }
    
    public function header_border_bottom_callback() {
        $value = isset($this->options['header_border_bottom']) ? $this->options['header_border_bottom'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_border_bottom]" value="1" <?php checked(1, $value); ?> />
        <label>Enable bottom border</label>
        <?php
    }
    
    public function header_border_bottom_width_callback() {
        $value = isset($this->options['header_border_bottom_width']) ? $this->options['header_border_bottom_width'] : '1';
        ?>
        <input type="number" name="ross_theme_header_options[header_border_bottom_width]" value="<?php echo esc_attr($value); ?>" class="small-text" min="1" max="10" /> px
        <p class="description">Bottom border width</p>
        <?php
    }
    
    public function header_border_bottom_color_callback() {
        $value = isset($this->options['header_border_bottom_color']) ? $this->options['header_border_bottom_color'] : '#e5e7eb';
        ?>
        <input type="text" name="ross_theme_header_options[header_border_bottom_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#e5e7eb" />
        <p class="description">Bottom border color</p>
        <?php
    }
    
    public function header_border_radius_callback() {
        $value = isset($this->options['header_border_radius']) ? $this->options['header_border_radius'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[header_border_radius]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="50" /> px
        <p class="description">Border radius for rounded corners</p>
        <?php
    }
    
    public function header_border_enable_callback() {
        $value = isset($this->options['header_border_enable']) ? $this->options['header_border_enable'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_border_enable]" value="1" <?php checked(1, $value); ?> />
        <label>Enable borders</label>
        <?php
    }
    
    public function header_border_color_callback() {
        $value = isset($this->options['header_border_color']) ? $this->options['header_border_color'] : '#e5e7eb';
        ?>
        <input type="text" name="ross_theme_header_options[header_border_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#e5e7eb" />
        <p class="description">Border color</p>
        <?php
    }
    
    public function header_border_width_callback() {
        $value = isset($this->options['header_border_width']) ? $this->options['header_border_width'] : '1';
        ?>
        <input type="number" name="ross_theme_header_options[header_border_width]" value="<?php echo esc_attr($value); ?>" class="small-text" min="1" max="10" /> px
        <p class="description">Border width</p>
        <?php
    }
    
    // Shadow Section Callbacks
    public function header_box_shadow_callback() {
        $value = isset($this->options['header_box_shadow']) ? $this->options['header_box_shadow'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_box_shadow]" value="1" <?php checked(1, $value); ?> />
        <label>Enable box shadow</label>
        <?php
    }
    
    public function header_shadow_color_callback() {
        $value = isset($this->options['header_shadow_color']) ? $this->options['header_shadow_color'] : 'rgba(0,0,0,0.1)';
        ?>
        <input type="text" name="ross_theme_header_options[header_shadow_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="rgba(0,0,0,0.1)" data-alpha="true" />
        <p class="description">Shadow color with transparency</p>
        <?php
    }
    
    public function header_shadow_x_callback() {
        $value = isset($this->options['header_shadow_x']) ? $this->options['header_shadow_x'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[header_shadow_x]" value="<?php echo esc_attr($value); ?>" class="small-text" min="-20" max="20" /> px
        <p class="description">Horizontal shadow offset</p>
        <?php
    }
    
    public function header_shadow_y_callback() {
        $value = isset($this->options['header_shadow_y']) ? $this->options['header_shadow_y'] : '2';
        ?>
        <input type="number" name="ross_theme_header_options[header_shadow_y]" value="<?php echo esc_attr($value); ?>" class="small-text" min="-20" max="20" /> px
        <p class="description">Vertical shadow offset</p>
        <?php
    }
    
    public function header_shadow_blur_callback() {
        $value = isset($this->options['header_shadow_blur']) ? $this->options['header_shadow_blur'] : '4';
        ?>
        <input type="number" name="ross_theme_header_options[header_shadow_blur]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="50" /> px
        <p class="description">Shadow blur radius</p>
        <?php
    }
    
    public function header_shadow_spread_callback() {
        $value = isset($this->options['header_shadow_spread']) ? $this->options['header_shadow_spread'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[header_shadow_spread]" value="<?php echo esc_attr($value); ?>" class="small-text" min="-20" max="20" /> px
        <p class="description">Shadow spread radius</p>
        <?php
    }
    
    // Spacing Section Callbacks
    public function header_padding_top_callback() {
        $value = isset($this->options['header_padding_top']) ? $this->options['header_padding_top'] : '15';
        ?>
        <input type="number" name="ross_theme_header_options[header_padding_top]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="100" /> px
        <p class="description">Top padding</p>
        <?php
    }
    
    public function header_padding_bottom_callback() {
        $value = isset($this->options['header_padding_bottom']) ? $this->options['header_padding_bottom'] : '15';
        ?>
        <input type="number" name="ross_theme_header_options[header_padding_bottom]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="100" /> px
        <p class="description">Bottom padding</p>
        <?php
    }
    
    public function header_margin_top_callback() {
        $value = isset($this->options['header_margin_top']) ? $this->options['header_margin_top'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[header_margin_top]" value="<?php echo esc_attr($value); ?>" class="small-text" min="-50" max="100" /> px
        <p class="description">Top margin</p>
        <?php
    }
    
    public function header_margin_bottom_callback() {
        $value = isset($this->options['header_margin_bottom']) ? $this->options['header_margin_bottom'] : '0';
        ?>
        <input type="number" name="ross_theme_header_options[header_margin_bottom]" value="<?php echo esc_attr($value); ?>" class="small-text" min="-50" max="100" /> px
        <p class="description">Bottom margin</p>
        <?php
    }
    
    // Effects Section Callbacks
    public function header_opacity_callback() {
        $value = isset($this->options['header_opacity']) ? $this->options['header_opacity'] : '1';
        ?>
        <input type="number" name="ross_theme_header_options[header_opacity]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="1" step="0.1" />
        <p class="description">Header opacity (0 = transparent, 1 = opaque)</p>
        <?php
    }
    
    public function header_blur_callback() {
        $value = isset($this->options['header_blur']) ? $this->options['header_blur'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_blur]" value="1" <?php checked(1, $value); ?> />
        <label>Enable backdrop blur effect</label>
        <?php
    }
    
    public function header_blur_amount_callback() {
        $value = isset($this->options['header_blur_amount']) ? $this->options['header_blur_amount'] : '10';
        ?>
        <input type="number" name="ross_theme_header_options[header_blur_amount]" value="<?php echo esc_attr($value); ?>" class="small-text" min="1" max="20" /> px
        <p class="description">Blur intensity</p>
        <?php
    }
    
    public function header_glass_effect_callback() {
        $value = isset($this->options['header_glass_effect']) ? $this->options['header_glass_effect'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_glass_effect]" value="1" <?php checked(1, $value); ?> />
        <label>Enable glass morphism effect</label>
        <?php
    }
    
    public function header_glass_opacity_callback() {
        $value = isset($this->options['header_glass_opacity']) ? $this->options['header_glass_opacity'] : '0.8';
        ?>
        <input type="number" name="ross_theme_header_options[header_glass_opacity]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="1" step="0.1" />
        <p class="description">Glass background opacity</p>
        <?php
    }
    
    public function header_animation_callback() {
        $value = isset($this->options['header_animation']) ? $this->options['header_animation'] : 'none';
        ?>
        <select name="ross_theme_header_options[header_animation]">
            <option value="none" <?php selected($value, 'none'); ?>>No Animation</option>
            <option value="fade-in" <?php selected($value, 'fade-in'); ?>>Fade In</option>
            <option value="slide-down" <?php selected($value, 'slide-down'); ?>>Slide Down</option>
            <option value="scale-in" <?php selected($value, 'scale-in'); ?>>Scale In</option>
        </select>
        <p class="description">Entrance animation for the header</p>
        <?php
    }
    
    public function header_animation_duration_callback() {
        $value = isset($this->options['header_animation_duration']) ? $this->options['header_animation_duration'] : '0.5';
        ?>
        <input type="number" name="ross_theme_header_options[header_animation_duration]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0.1" max="3" step="0.1" /> s
        <p class="description">Animation duration in seconds</p>
        <?php
    }
    
    // Responsive Section Callbacks
    public function transparent_homepage_callback() {
        $value = isset($this->options['transparent_homepage']) ? $this->options['transparent_homepage'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[transparent_homepage]" value="1" <?php checked(1, $value); ?> />
        <label>Make header transparent on homepage</label>
        <p class="description">When enabled, the header will be transparent on the homepage for hero sections</p>
        <?php
    }
    
    public function header_sticky_bg_color_callback() {
        $value = isset($this->options['header_sticky_bg_color']) ? $this->options['header_sticky_bg_color'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[header_sticky_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="" placeholder="Use default color" />
        <p class="description">Background color when header is sticky (leave empty to use default)</p>
        <?php
    }
    
    public function header_mobile_bg_color_callback() {
        $value = isset($this->options['header_mobile_bg_color']) ? $this->options['header_mobile_bg_color'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[header_mobile_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="" placeholder="Use desktop color" />
        <p class="description">Mobile-specific background color (leave empty to use desktop setting)</p>
        <?php
    }
    
    public function header_mobile_padding_callback() {
        $value = isset($this->options['header_mobile_padding']) ? $this->options['header_mobile_padding'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[header_mobile_padding]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="10px 15px" />
        <p class="description">Mobile padding (e.g., "10px 15px" or leave empty for desktop settings)</p>
        <?php
    }
    
    public function header_mobile_height_callback() {
        $value = isset($this->options['header_mobile_height']) ? $this->options['header_mobile_height'] : '';
        ?>
        <input type="number" name="ross_theme_header_options[header_mobile_height]" value="<?php echo esc_attr($value); ?>" class="small-text" min="30" max="150" placeholder="Use desktop height" /> px
        <p class="description">Mobile header height (leave empty to use desktop setting)</p>
        <?php
    }
    
    public function header_tablet_breakpoint_callback() {
        $value = isset($this->options['header_tablet_breakpoint']) ? $this->options['header_tablet_breakpoint'] : '768';
        ?>
        <input type="number" name="ross_theme_header_options[header_tablet_breakpoint]" value="<?php echo esc_attr($value); ?>" class="small-text" min="600" max="900" /> px
        <p class="description">Tablet breakpoint width</p>
        <?php
    }
    
    public function header_mobile_breakpoint_callback() {
        $value = isset($this->options['header_mobile_breakpoint']) ? $this->options['header_mobile_breakpoint'] : '480';
        ?>
        <input type="number" name="ross_theme_header_options[header_mobile_breakpoint]" value="<?php echo esc_attr($value); ?>" class="small-text" min="320" max="600" /> px
        <p class="description">Mobile breakpoint width</p>
        <?php
    }
    
    public function header_custom_css_callback() {
        $value = isset($this->options['header_custom_css']) ? $this->options['header_custom_css'] : '';
        ?>
        <textarea name="ross_theme_header_options[header_custom_css]" rows="8" cols="50" class="large-text code" placeholder=".site-header { /* Your custom CSS here */ }"><?php echo esc_textarea($value); ?></textarea>
        <p class="description">Add custom CSS for the header. This will be applied after all other styles.</p>
        <?php
    }
    
    // ===== MISSING CALLBACKS =====
    
    public function header_bg_overlay_opacity_callback() {
        $value = isset($this->options['header_bg_overlay_opacity']) ? $this->options['header_bg_overlay_opacity'] : '0.5';
        ?>
        <input type="number" name="ross_theme_header_options[header_bg_overlay_opacity]" value="<?php echo esc_attr($value); ?>" class="small-text" min="0" max="1" step="0.1" />
        <p class="description">Overlay opacity (0 = transparent, 1 = opaque)</p>
        <?php
    }
    
    public function header_border_style_callback() {
        $value = isset($this->options['header_border_style']) ? $this->options['header_border_style'] : 'solid';
        ?>
        <select name="ross_theme_header_options[header_border_style]">
            <option value="solid" <?php selected($value, 'solid'); ?>>Solid</option>
            <option value="dashed" <?php selected($value, 'dashed'); ?>>Dashed</option>
            <option value="dotted" <?php selected($value, 'dotted'); ?>>Dotted</option>
            <option value="double" <?php selected($value, 'double'); ?>>Double</option>
        </select>
        <p class="description">Border line style</p>
        <?php
    }
    
    public function header_border_position_callback() {
        $value = isset($this->options['header_border_position']) ? $this->options['header_border_position'] : 'bottom';
        ?>
        <select name="ross_theme_header_options[header_border_position]">
            <option value="top" <?php selected($value, 'top'); ?>>Top</option>
            <option value="bottom" <?php selected($value, 'bottom'); ?>>Bottom</option>
            <option value="both" <?php selected($value, 'both'); ?>>Top & Bottom</option>
        </select>
        <p class="description">Border position</p>
        <?php
    }
    
    public function header_shadow_enable_callback() {
        $value = isset($this->options['header_shadow_enable']) ? $this->options['header_shadow_enable'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_shadow_enable]" value="1" <?php checked(1, $value); ?> />
        <label>Enable header shadow</label>
        <?php
    }
    
    public function header_shadow_type_callback() {
        $value = isset($this->options['header_shadow_type']) ? $this->options['header_shadow_type'] : 'drop';
        ?>
        <select name="ross_theme_header_options[header_shadow_type]">
            <option value="drop" <?php selected($value, 'drop'); ?>>Drop Shadow</option>
            <option value="inner" <?php selected($value, 'inner'); ?>>Inner Shadow</option>
            <option value="glow" <?php selected($value, 'glow'); ?>>Glow Effect</option>
        </select>
        <p class="description">Shadow type</p>
        <?php
    }
    
    // ===== END ENTERPRISE APPEARANCE CALLBACKS =====
    
    public function sanitize_header_options($input) {
        // Debug: log input for admins
        if (current_user_can('manage_options') && defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[ross_theme_sanitize_header_options] Input: ' . json_encode($input));
        }
        $sanitized = array();
        
        // Layout
        $sanitized['header_style'] = sanitize_text_field($input['header_style']);
        $sanitized['header_width'] = sanitize_text_field($input['header_width']);
        $sanitized['header_center'] = isset($input['header_center']) ? 1 : 0;
        $sanitized['sticky_header'] = isset($input['sticky_header']) ? 1 : 0;
        $sanitized['header_height'] = absint($input['header_height']);
        
        // Advanced Sticky Options
        $sanitized['sticky_behavior'] = isset($input['sticky_behavior']) ? sanitize_text_field($input['sticky_behavior']) : 'always';
        $sanitized['sticky_scroll_threshold'] = isset($input['sticky_scroll_threshold']) ? absint($input['sticky_scroll_threshold']) : 100;
        $sanitized['sticky_shrink_header'] = isset($input['sticky_shrink_header']) ? 1 : 0;
        $sanitized['sticky_header_height'] = isset($input['sticky_header_height']) ? absint($input['sticky_header_height']) : 60;
        $sanitized['sticky_animation_duration'] = isset($input['sticky_animation_duration']) ? floatval($input['sticky_animation_duration']) : 0.3;
        $sanitized['sticky_easing'] = isset($input['sticky_easing']) ? sanitize_text_field($input['sticky_easing']) : 'ease-out';
        $sanitized['sticky_hide_mobile'] = isset($input['sticky_hide_mobile']) ? 1 : 0;
    // Padding
    $sanitized['header_padding_top'] = isset($input['header_padding_top']) ? absint($input['header_padding_top']) : 20;
    $sanitized['header_padding_right'] = isset($input['header_padding_right']) ? absint($input['header_padding_right']) : 0;
    $sanitized['header_padding_bottom'] = isset($input['header_padding_bottom']) ? absint($input['header_padding_bottom']) : 20;
    $sanitized['header_padding_left'] = isset($input['header_padding_left']) ? absint($input['header_padding_left']) : 0;
    // Margin
    $sanitized['header_margin_top'] = isset($input['header_margin_top']) ? absint($input['header_margin_top']) : 0;
    $sanitized['header_margin_right'] = isset($input['header_margin_right']) ? absint($input['header_margin_right']) : 0;
    $sanitized['header_margin_bottom'] = isset($input['header_margin_bottom']) ? absint($input['header_margin_bottom']) : 0;
    $sanitized['header_margin_left'] = isset($input['header_margin_left']) ? absint($input['header_margin_left']) : 0;
        
        // Logo
        $sanitized['logo_upload'] = esc_url_raw($input['logo_upload']);
        $sanitized['logo_dark'] = esc_url_raw($input['logo_dark']);
        $sanitized['mobile_logo'] = isset($input['mobile_logo']) ? esc_url_raw($input['mobile_logo']) : '';
        $sanitized['logo_width'] = absint($input['logo_width']);
        $sanitized['logo_padding'] = isset($input['logo_padding']) ? absint($input['logo_padding']) : 0;
        $sanitized['mobile_logo_width'] = isset($input['mobile_logo_width']) ? absint($input['mobile_logo_width']) : 120;
        $sanitized['show_site_title'] = isset($input['show_site_title']) ? 1 : 0;
        
        // Top Bar
        $sanitized['enable_topbar'] = isset($input['enable_topbar']) ? 1 : 0;
        $sanitized['topbar_left_content'] = wp_kses_post($input['topbar_left_content']);
        $sanitized['topbar_bg_color'] = sanitize_hex_color($input['topbar_bg_color']);
        $sanitized['topbar_text_color'] = sanitize_hex_color($input['topbar_text_color']);
        $sanitized['topbar_icon_color'] = isset($input['topbar_icon_color']) ? sanitize_hex_color($input['topbar_icon_color']) : $sanitized['topbar_text_color'];
    // Top Bar - new
    $sanitized['enable_social'] = isset($input['enable_social']) ? 1 : 0;
    $sanitized['social_facebook'] = isset($input['social_facebook']) ? esc_url_raw($input['social_facebook']) : '';
    $sanitized['social_facebook_enabled'] = isset($input['social_facebook_enabled']) ? 1 : 0;
    $sanitized['social_facebook_icon'] = isset($input['social_facebook_icon']) ? sanitize_text_field($input['social_facebook_icon']) : 'fab fa-facebook-f';
    $sanitized['social_twitter'] = isset($input['social_twitter']) ? esc_url_raw($input['social_twitter']) : '';
    $sanitized['social_twitter_enabled'] = isset($input['social_twitter_enabled']) ? 1 : 0;
    $sanitized['social_twitter_icon'] = isset($input['social_twitter_icon']) ? sanitize_text_field($input['social_twitter_icon']) : 'fab fa-twitter';
    $sanitized['social_linkedin'] = isset($input['social_linkedin']) ? esc_url_raw($input['social_linkedin']) : '';
    $sanitized['social_linkedin_enabled'] = isset($input['social_linkedin_enabled']) ? 1 : 0;
    $sanitized['social_linkedin_icon'] = isset($input['social_linkedin_icon']) ? sanitize_text_field($input['social_linkedin_icon']) : 'fab fa-linkedin-in';
    $sanitized['social_instagram'] = isset($input['social_instagram']) ? esc_url_raw($input['social_instagram']) : '';
    $sanitized['social_instagram_enabled'] = isset($input['social_instagram_enabled']) ? 1 : 0;
    $sanitized['social_instagram_icon'] = isset($input['social_instagram_icon']) ? sanitize_text_field($input['social_instagram_icon']) : 'fab fa-instagram';
    $sanitized['social_youtube'] = isset($input['social_youtube']) ? esc_url_raw($input['social_youtube']) : '';
    $sanitized['social_youtube_enabled'] = isset($input['social_youtube_enabled']) ? 1 : 0;
    $sanitized['social_youtube_icon'] = isset($input['social_youtube_icon']) ? sanitize_text_field($input['social_youtube_icon']) : 'fab fa-youtube';
    $sanitized['phone_number'] = isset($input['phone_number']) ? sanitize_text_field($input['phone_number']) : '';
        $sanitized['topbar_email'] = isset($input['topbar_email']) ? sanitize_email($input['topbar_email']) : '';
        $sanitized['enable_announcement'] = isset($input['enable_announcement']) ? 1 : 0;
    // Allow HTML in announcements (basic tags) - sanitize with wp_kses_post
    $sanitized['announcement_text'] = isset($input['announcement_text']) ? wp_kses_post($input['announcement_text']) : '';
    $sanitized['enable_topbar_left'] = isset($input['enable_topbar_left']) ? 1 : 0;
    $sanitized['announcement_animation'] = isset($input['announcement_animation']) ? sanitize_text_field($input['announcement_animation']) : 'marquee';
    $sanitized['announcement_bg_color'] = isset($input['announcement_bg_color']) ? sanitize_hex_color($input['announcement_bg_color']) : '#E5C902';
    $sanitized['announcement_text_color'] = isset($input['announcement_text_color']) ? sanitize_hex_color($input['announcement_text_color']) : '#001946';
    $sanitized['announcement_font_size'] = isset($input['announcement_font_size']) ? sanitize_text_field($input['announcement_font_size']) : '14px';
    $sanitized['announcement_sticky'] = isset($input['announcement_sticky']) ? 1 : 0;
    $sanitized['announcement_sticky_offset'] = isset($input['announcement_sticky_offset']) ? absint($input['announcement_sticky_offset']) : 0;
    $allowed_positions = array('top_of_topbar','below_topbar','below_header');
    $sanitized['announcement_position'] = isset($input['announcement_position']) && in_array($input['announcement_position'], $allowed_positions) ? sanitize_text_field($input['announcement_position']) : 'top_of_topbar';
    // Social links - array of {icon,url}
    $sanitized['social_links'] = array();
    if (isset($input['social_links']) && is_array($input['social_links'])) {
        foreach ($input['social_links'] as $item) {
            if (empty($item['url'])) continue;
            $icon = isset($item['icon']) ? sanitize_text_field($item['icon']) : '';
            $url = esc_url_raw($item['url']);
            if (empty($url)) continue;
            $sanitized['social_links'][] = array('icon' => $icon, 'url' => $url);
        }
    }
    
    // Social icon styling
    $sanitized['social_icon_size'] = isset($input['social_icon_size']) ? sanitize_text_field($input['social_icon_size']) : 'medium';
    $sanitized['social_icon_shape'] = isset($input['social_icon_shape']) ? sanitize_text_field($input['social_icon_shape']) : 'circle';
    $sanitized['social_icon_color'] = isset($input['social_icon_color']) ? sanitize_hex_color($input['social_icon_color']) : '#ffffff';
    $sanitized['social_icon_bg_color'] = isset($input['social_icon_bg_color']) ? sanitize_text_field($input['social_icon_bg_color']) : 'transparent';
    $allowed_effects = array('none', 'bounce', 'pulse', 'rotate', 'scale');
    $sanitized['social_icon_effect'] = isset($input['social_icon_effect']) && in_array($input['social_icon_effect'], $allowed_effects) ? sanitize_text_field($input['social_icon_effect']) : 'none';
    $sanitized['social_icon_border_color'] = isset($input['social_icon_border_color']) ? sanitize_text_field($input['social_icon_border_color']) : 'transparent';
    $allowed_border_sizes = array('0', '1', '2', '3');
    $sanitized['social_icon_border_size'] = isset($input['social_icon_border_size']) && in_array($input['social_icon_border_size'], $allowed_border_sizes) ? sanitize_text_field($input['social_icon_border_size']) : '0';
    $sanitized['social_icon_width'] = isset($input['social_icon_width']) ? absint($input['social_icon_width']) : 32;
    
    $sanitized['color_palette'] = isset($input['color_palette']) ? sanitize_text_field($input['color_palette']) : 'professional';
        
        // Top Bar - Style Enhancements
        $sanitized['topbar_shadow_enable'] = isset($input['topbar_shadow_enable']) ? 1 : 0;
        $sanitized['topbar_gradient_enable'] = isset($input['topbar_gradient_enable']) ? 1 : 0;
        $sanitized['topbar_gradient_color1'] = isset($input['topbar_gradient_color1']) ? sanitize_hex_color($input['topbar_gradient_color1']) : '#001946';
        $sanitized['topbar_gradient_color2'] = isset($input['topbar_gradient_color2']) ? sanitize_hex_color($input['topbar_gradient_color2']) : '#003d7a';
        $sanitized['topbar_border_color'] = isset($input['topbar_border_color']) ? sanitize_hex_color($input['topbar_border_color']) : '#E5C902';
        $sanitized['topbar_border_width'] = isset($input['topbar_border_width']) ? absint($input['topbar_border_width']) : 0;
        $sanitized['topbar_icon_hover_color'] = isset($input['topbar_icon_hover_color']) ? sanitize_hex_color($input['topbar_icon_hover_color']) : $sanitized['social_icon_color'];
        $sanitized['sticky_topbar'] = isset($input['sticky_topbar']) ? 1 : 0;
        $sanitized['topbar_sticky_offset'] = isset($input['topbar_sticky_offset']) ? absint($input['topbar_sticky_offset']) : 0;
        $sanitized['topbar_font_size'] = isset($input['topbar_font_size']) ? absint($input['topbar_font_size']) : 14;
        $sanitized['topbar_alignment'] = isset($input['topbar_alignment']) ? sanitize_text_field($input['topbar_alignment']) : 'left';
        
        // Custom Icon Links
        $sanitized['topbar_custom_icon_links'] = array();
        if (isset($input['topbar_custom_icon_links']) && is_array($input['topbar_custom_icon_links'])) {
            foreach ($input['topbar_custom_icon_links'] as $item) {
                if (empty($item['url']) || empty($item['icon'])) continue;
                $icon = sanitize_text_field($item['icon']);
                $url = esc_url_raw($item['url']);
                $title = isset($item['title']) ? sanitize_text_field($item['title']) : '';
                $enabled = isset($item['enabled']) ? 1 : 0;
                if (empty($url) || empty($icon)) continue;
                $sanitized['topbar_custom_icon_links'][] = array('icon' => $icon, 'url' => $url, 'title' => $title, 'enabled' => $enabled);
            }
        }
        
        // Navigation
        $sanitized['menu_alignment'] = sanitize_text_field($input['menu_alignment']);
        $sanitized['menu_font_size'] = absint($input['menu_font_size']);
        $sanitized['active_item_color'] = sanitize_hex_color($input['active_item_color']);
        $sanitized['menu_hover_color'] = isset($input['menu_hover_color']) ? sanitize_hex_color($input['menu_hover_color']) : $sanitized['active_item_color'];
        $sanitized['menu_bg_color'] = isset($input['menu_bg_color']) && !empty($input['menu_bg_color']) ? sanitize_hex_color($input['menu_bg_color']) : '';
        $sanitized['menu_border_color'] = isset($input['menu_border_color']) ? sanitize_hex_color($input['menu_border_color']) : $sanitized['active_item_color'];
        
        // Navigation - Hover Effects
        $sanitized['menu_hover_effect'] = isset($input['menu_hover_effect']) ? sanitize_text_field($input['menu_hover_effect']) : 'underline';
        $sanitized['menu_hover_underline_style'] = isset($input['menu_hover_underline_style']) ? sanitize_text_field($input['menu_hover_underline_style']) : 'slide';
        
        // Navigation - Typography
        $sanitized['menu_font_family'] = isset($input['menu_font_family']) ? sanitize_text_field($input['menu_font_family']) : 'inherit';
        $sanitized['menu_font_weight'] = isset($input['menu_font_weight']) ? sanitize_text_field($input['menu_font_weight']) : '600';
        $sanitized['menu_letter_spacing'] = isset($input['menu_letter_spacing']) ? floatval($input['menu_letter_spacing']) : 0;
        $sanitized['menu_text_transform'] = isset($input['menu_text_transform']) ? sanitize_text_field($input['menu_text_transform']) : 'none';
        
        // CTA
        $sanitized['enable_search'] = isset($input['enable_search']) ? 1 : 0;
        $sanitized['search_type'] = isset($input['search_type']) ? sanitize_text_field($input['search_type']) : 'modal';
        $sanitized['search_placeholder'] = isset($input['search_placeholder']) ? sanitize_text_field($input['search_placeholder']) : 'Search...';
        $sanitized['enable_cta_button'] = isset($input['enable_cta_button']) ? 1 : 0;
        $sanitized['cta_button_text'] = sanitize_text_field($input['cta_button_text']);
        $sanitized['cta_button_color'] = sanitize_hex_color($input['cta_button_color']);
        $sanitized['cta_button_text_color'] = isset($input['cta_button_text_color']) ? sanitize_hex_color($input['cta_button_text_color']) : '#ffffff';
        $sanitized['cta_button_hover_text_color'] = isset($input['cta_button_hover_text_color']) ? sanitize_hex_color($input['cta_button_hover_text_color']) : '#ffffff';
        $sanitized['cta_button_style'] = isset($input['cta_button_style']) ? sanitize_text_field($input['cta_button_style']) : 'solid';
        $sanitized['cta_button_url'] = isset($input['cta_button_url']) && ! empty($input['cta_button_url']) ? esc_url_raw($input['cta_button_url']) : home_url('/contact');
        $sanitized['cta_button_size'] = isset($input['cta_button_size']) ? sanitize_text_field($input['cta_button_size']) : 'medium';
        $sanitized['cta_button_font_size'] = isset($input['cta_button_font_size']) ? absint($input['cta_button_font_size']) : 16;
        $sanitized['cta_button_border_radius'] = isset($input['cta_button_border_radius']) ? absint($input['cta_button_border_radius']) : 8;
        $allowed_hover_effects = array('none', 'scale', 'glow', 'slide', 'bounce');
        $sanitized['cta_button_hover_effect'] = isset($input['cta_button_hover_effect']) && in_array($input['cta_button_hover_effect'], $allowed_hover_effects) ? sanitize_text_field($input['cta_button_hover_effect']) : 'scale';
        $allowed_text_hover_effects = array('none', 'fade', 'slide-up', 'slide-down', 'scale-text', 'glow-text');
        $sanitized['cta_button_text_hover_effect'] = isset($input['cta_button_text_hover_effect']) && in_array($input['cta_button_text_hover_effect'], $allowed_text_hover_effects) ? sanitize_text_field($input['cta_button_text_hover_effect']) : 'none';
        
        // Enhanced Search Settings
        $sanitized['search_icon_style'] = isset($input['search_icon_style']) ? sanitize_text_field($input['search_icon_style']) : 'magnifying-glass';
        $allowed_animations = array('none', 'pulse', 'bounce', 'rotate', 'glow');
        $sanitized['search_animation'] = isset($input['search_animation']) && in_array($input['search_animation'], $allowed_animations) ? sanitize_text_field($input['search_animation']) : 'none';
        $sanitized['search_mobile_hide'] = isset($input['search_mobile_hide']) ? 1 : 0;
        
        // Enhanced CTA Settings
        $allowed_icons = array('none', 'arrow-right', 'arrow-left', 'phone', 'email', 'chat', 'download', 'play', 'star', 'heart');
        $sanitized['cta_button_icon'] = isset($input['cta_button_icon']) && in_array($input['cta_button_icon'], $allowed_icons) ? sanitize_text_field($input['cta_button_icon']) : 'none';
        $sanitized['cta_button_icon_position'] = isset($input['cta_button_icon_position']) ? sanitize_text_field($input['cta_button_icon_position']) : 'left';
        $allowed_shadows = array('none', 'small', 'medium', 'large', 'glow');
        $sanitized['cta_button_shadow'] = isset($input['cta_button_shadow']) && in_array($input['cta_button_shadow'], $allowed_shadows) ? sanitize_text_field($input['cta_button_shadow']) : 'none';
        $sanitized['cta_button_target'] = isset($input['cta_button_target']) ? sanitize_text_field($input['cta_button_target']) : '_self';
        $sanitized['cta_button_mobile_hide'] = isset($input['cta_button_mobile_hide']) ? 1 : 0;
        $sanitized['cta_button_position'] = isset($input['cta_button_position']) ? sanitize_text_field($input['cta_button_position']) : 'right';
        
        // Layout Settings
        $sanitized['header_actions_order'] = isset($input['header_actions_order']) ? sanitize_text_field($input['header_actions_order']) : 'search-cta';
        $sanitized['header_actions_spacing'] = isset($input['header_actions_spacing']) ? absint($input['header_actions_spacing']) : 15;
        
        // Appearance
        $sanitized['header_bg_color'] = sanitize_hex_color($input['header_bg_color']);
        $sanitized['header_opacity'] = isset($input['header_opacity']) ? floatval($input['header_opacity']) : 1.0;
        $sanitized['header_text_color'] = sanitize_hex_color($input['header_text_color']);
        $sanitized['header_link_hover_color'] = sanitize_hex_color($input['header_link_hover_color']);
        $sanitized['transparent_homepage'] = isset($input['transparent_homepage']) ? 1 : 0;
        
        // Appearance - Shadow & Border
        $sanitized['header_border_enable'] = isset($input['header_border_enable']) ? 1 : 0;
        $sanitized['header_border_color'] = isset($input['header_border_color']) ? sanitize_hex_color($input['header_border_color']) : '#e0e0e0';
        $sanitized['header_border_width'] = isset($input['header_border_width']) ? absint($input['header_border_width']) : 1;
        
        // Appearance - Typography
        $sanitized['header_font_family'] = isset($input['header_font_family']) ? sanitize_text_field($input['header_font_family']) : 'inherit';
        $sanitized['header_font_weight'] = isset($input['header_font_weight']) ? sanitize_text_field($input['header_font_weight']) : '400';
        $sanitized['header_font_size'] = isset($input['header_font_size']) ? absint($input['header_font_size']) : 15;
        $sanitized['header_letter_spacing'] = isset($input['header_letter_spacing']) ? sanitize_text_field($input['header_letter_spacing']) : '1.2px';
        $sanitized['header_text_transform'] = isset($input['header_text_transform']) ? sanitize_text_field($input['header_text_transform']) : 'uppercase';
        // Spacing defaults
        $sanitized['header_padding_top'] = isset($input['header_padding_top']) ? absint($input['header_padding_top']) : 30;
        $sanitized['header_padding_bottom'] = isset($input['header_padding_bottom']) ? absint($input['header_padding_bottom']) : 30;

        // CTA additional controls
        $sanitized['cta_button_padding'] = isset($input['cta_button_padding']) ? sanitize_text_field($input['cta_button_padding']) : (isset($sanitized['cta_button_padding']) ? $sanitized['cta_button_padding'] : '10px 30px');
        $sanitized['cta_button_border_radius'] = isset($input['cta_button_border_radius']) ? sanitize_text_field($input['cta_button_border_radius']) : (isset($sanitized['cta_button_border_radius']) ? $sanitized['cta_button_border_radius'] : '30px');
        $sanitized['cta_button_hover_color'] = isset($input['cta_button_hover_color']) ? sanitize_hex_color($input['cta_button_hover_color']) : (isset($sanitized['cta_button_hover_color']) ? $sanitized['cta_button_hover_color'] : '');

        // Animation / behavior
        $sanitized['header_logo_scale'] = isset($input['header_logo_scale']) ? floatval($input['header_logo_scale']) : 0.92;
        
        // ===== ENTERPRISE APPEARANCE SANITIZATION =====
        
        // Background Settings
        $sanitized['header_bg_color'] = isset($input['header_bg_color']) ? sanitize_hex_color($input['header_bg_color']) : '#ffffff';
        
        // Typography Settings
        $sanitized['header_text_color'] = isset($input['header_text_color']) ? sanitize_hex_color($input['header_text_color']) : '#333333';
        $sanitized['header_link_color'] = isset($input['header_link_color']) ? sanitize_hex_color($input['header_link_color']) : '#007cba';
        $sanitized['header_link_hover_color'] = isset($input['header_link_hover_color']) ? sanitize_hex_color($input['header_link_hover_color']) : '#005a87';
        $sanitized['header_font_size'] = isset($input['header_font_size']) ? absint($input['header_font_size']) : 16;
        $sanitized['header_line_height'] = isset($input['header_line_height']) ? floatval($input['header_line_height']) : 1.5;
        $sanitized['header_letter_spacing'] = isset($input['header_letter_spacing']) ? floatval($input['header_letter_spacing']) : 0;
        $allowed_transforms = array('none', 'uppercase', 'lowercase', 'capitalize');
        $sanitized['header_text_transform'] = isset($input['header_text_transform']) && in_array($input['header_text_transform'], $allowed_transforms) ? sanitize_text_field($input['header_text_transform']) : 'none';
        
        // Border Settings
        $sanitized['header_border_top'] = isset($input['header_border_top']) ? 1 : 0;
        $sanitized['header_border_top_width'] = isset($input['header_border_top_width']) ? absint($input['header_border_top_width']) : 1;
        $sanitized['header_border_top_color'] = isset($input['header_border_top_color']) ? sanitize_hex_color($input['header_border_top_color']) : '#e5e7eb';
        $sanitized['header_border_bottom'] = isset($input['header_border_bottom']) ? 1 : 0;
        $sanitized['header_border_bottom_width'] = isset($input['header_border_bottom_width']) ? absint($input['header_border_bottom_width']) : 1;
        $sanitized['header_border_bottom_color'] = isset($input['header_border_bottom_color']) ? sanitize_hex_color($input['header_border_bottom_color']) : '#e5e7eb';
        $allowed_border_styles = array('solid', 'dashed', 'dotted', 'double');
        $sanitized['header_border_style'] = isset($input['header_border_style']) && in_array($input['header_border_style'], $allowed_border_styles) ? sanitize_text_field($input['header_border_style']) : 'solid';
        $allowed_border_positions = array('top', 'bottom', 'both');
        $sanitized['header_border_position'] = isset($input['header_border_position']) && in_array($input['header_border_position'], $allowed_border_positions) ? sanitize_text_field($input['header_border_position']) : 'bottom';
        $sanitized['header_border_radius'] = isset($input['header_border_radius']) ? absint($input['header_border_radius']) : 0;
        
        // Shadow Settings
        $sanitized['header_box_shadow'] = isset($input['header_box_shadow']) ? 1 : 0;
        $sanitized['header_shadow_enable'] = isset($input['header_shadow_enable']) ? 1 : 0;
        $allowed_shadow_types = array('drop', 'inner', 'glow');
        $sanitized['header_shadow_type'] = isset($input['header_shadow_type']) && in_array($input['header_shadow_type'], $allowed_shadow_types) ? sanitize_text_field($input['header_shadow_type']) : 'drop';
        $sanitized['header_shadow_color'] = isset($input['header_shadow_color']) ? sanitize_text_field($input['header_shadow_color']) : 'rgba(0,0,0,0.1)';
        $sanitized['header_shadow_x'] = isset($input['header_shadow_x']) ? intval($input['header_shadow_x']) : 0;
        $sanitized['header_shadow_y'] = isset($input['header_shadow_y']) ? intval($input['header_shadow_y']) : 2;
        $sanitized['header_shadow_blur'] = isset($input['header_shadow_blur']) ? absint($input['header_shadow_blur']) : 4;
        $sanitized['header_shadow_spread'] = isset($input['header_shadow_spread']) ? intval($input['header_shadow_spread']) : 0;
        
        // Spacing Settings
        $sanitized['header_padding_top'] = isset($input['header_padding_top']) ? absint($input['header_padding_top']) : 15;
        $sanitized['header_padding_bottom'] = isset($input['header_padding_bottom']) ? absint($input['header_padding_bottom']) : 15;
        $sanitized['header_padding_left'] = isset($input['header_padding_left']) ? absint($input['header_padding_left']) : 20;
        $sanitized['header_padding_right'] = isset($input['header_padding_right']) ? absint($input['header_padding_right']) : 20;
        $sanitized['header_margin_top'] = isset($input['header_margin_top']) ? intval($input['header_margin_top']) : 0;
        $sanitized['header_margin_bottom'] = isset($input['header_margin_bottom']) ? intval($input['header_margin_bottom']) : 0;
        
        // Effects Settings
        $sanitized['header_opacity'] = isset($input['header_opacity']) ? floatval($input['header_opacity']) : 1;
        $sanitized['header_blur'] = isset($input['header_blur']) ? 1 : 0;
        $sanitized['header_blur_amount'] = isset($input['header_blur_amount']) ? absint($input['header_blur_amount']) : 10;
        $sanitized['header_glass_effect'] = isset($input['header_glass_effect']) ? 1 : 0;
        $sanitized['header_glass_opacity'] = isset($input['header_glass_opacity']) ? floatval($input['header_glass_opacity']) : 0.8;
        $allowed_animations = array('none', 'fade-in', 'slide-down', 'scale-in');
        $sanitized['header_animation'] = isset($input['header_animation']) && in_array($input['header_animation'], $allowed_animations) ? sanitize_text_field($input['header_animation']) : 'none';
        $sanitized['header_animation_duration'] = isset($input['header_animation_duration']) ? floatval($input['header_animation_duration']) : 0.5;
        
        // Responsive Settings
        $sanitized['header_mobile_bg_color'] = isset($input['header_mobile_bg_color']) ? sanitize_hex_color($input['header_mobile_bg_color']) : '';
        $sanitized['header_mobile_padding'] = isset($input['header_mobile_padding']) ? sanitize_text_field($input['header_mobile_padding']) : '';
        $sanitized['header_mobile_height'] = isset($input['header_mobile_height']) ? absint($input['header_mobile_height']) : '';
        $sanitized['header_tablet_breakpoint'] = isset($input['header_tablet_breakpoint']) ? absint($input['header_tablet_breakpoint']) : 768;
        $sanitized['header_mobile_breakpoint'] = isset($input['header_mobile_breakpoint']) ? absint($input['header_mobile_breakpoint']) : 480;
        
        // Additional Settings
        $sanitized['transparent_homepage'] = isset($input['transparent_homepage']) ? 1 : 0;
        $sanitized['header_sticky_bg_color'] = isset($input['header_sticky_bg_color']) ? sanitize_hex_color($input['header_sticky_bg_color']) : '';
        $sanitized['header_custom_css'] = isset($input['header_custom_css']) ? wp_kses_post($input['header_custom_css']) : '';
        
        // ===== END ENTERPRISE APPEARANCE SANITIZATION =====
        
        // Debug: log sanitized output for admins
        if (current_user_can('manage_options') && defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[ross_theme_sanitize_header_options] Sanitized: ' . json_encode($sanitized));
        }
        
        return $sanitized;
    }

    /**
     * AJAX: Apply header template
     */
    public function ajax_apply_header_template() {
        check_ajax_referer('ross_header_templates', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $template_id = sanitize_text_field($_POST['template_id']);
        $force = isset($_POST['force']) ? (bool) $_POST['force'] : false;
        
        // Load template manager
        require_once get_template_directory() . '/inc/features/header/header-template-manager.php';
        
        $success = ross_theme_apply_header_template($template_id, $force);
        
        if ($success) {
            wp_send_json_success('Template applied successfully');
        } else {
            wp_send_json_error('Failed to apply template');
        }
    }
    
    /**
     * AJAX: Preview header template
     */
    public function ajax_preview_header_template() {
        check_ajax_referer('ross_header_templates', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $template_id = sanitize_text_field($_POST['template_id']);
        
        // Load template manager
        require_once get_template_directory() . '/inc/features/header/header-template-manager.php';
        
        $template = ross_theme_get_header_template($template_id);
        
        if (!$template) {
            wp_send_json_error('Template not found');
        }
        
        ob_start();
        ?>
        <div style="background: <?php echo esc_attr($template['bg']); ?>; color: <?php echo esc_attr($template['text']); ?>; padding: 40px;">
            <h3 style="margin-top: 0;"><?php echo esc_html($template['title']); ?></h3>
            <p><?php echo esc_html($template['description']); ?></p>
            
            <h4>Settings:</h4>
            <ul style="list-style: none; padding: 0;">
                <li><strong>Layout:</strong> <?php echo esc_html($template['layout']); ?></li>
                <li><strong>Logo Position:</strong> <?php echo esc_html($template['logo_position']); ?></li>
                <li><strong>Menu Position:</strong> <?php echo esc_html($template['menu_position']); ?></li>
                <li><strong>Sticky Header:</strong> <?php echo $template['sticky_enabled'] ? 'Yes' : 'No'; ?></li>
                <li><strong>Search:</strong> <?php echo $template['search_enabled'] ? 'Yes' : 'No'; ?></li>
                <li><strong>CTA Button:</strong> <?php echo $template['cta']['enabled'] ? 'Yes' : 'No'; ?></li>
            </ul>
            
            <h4>Color Scheme:</h4>
            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; background: <?php echo esc_attr($template['bg']); ?>; border: 2px solid #ddd; border-radius: 8px;"></div>
                    <small>Background</small>
                </div>
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; background: <?php echo esc_attr($template['text']); ?>; border: 2px solid #ddd; border-radius: 8px;"></div>
                    <small>Text</small>
                </div>
                <div style="text-align: center;">
                    <div style="width: 60px; height: 60px; background: <?php echo esc_attr($template['accent']); ?>; border: 2px solid #ddd; border-radius: 8px;"></div>
                    <small>Accent</small>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        
        wp_send_json_success($html);
    }
    
    /**
     * AJAX: Restore header backup
     */
    public function ajax_restore_header_backup() {
        check_ajax_referer('ross_header_templates', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $backup_id = sanitize_text_field($_POST['backup_id']);
        
        // Load template manager
        require_once get_template_directory() . '/inc/features/header/header-template-manager.php';
        
        $success = ross_theme_restore_header_backup($backup_id);
        
        if ($success) {
            wp_send_json_success('Backup restored successfully');
        } else {
            wp_send_json_error('Failed to restore backup');
        }
    }
    
    /**
     * AJAX: Delete header backup
     */
    public function ajax_delete_header_backup() {
        check_ajax_referer('ross_header_templates', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $backup_id = sanitize_text_field($_POST['backup_id']);
        
        // Load template manager
        require_once get_template_directory() . '/inc/features/header/header-template-manager.php';
        
        $success = ross_theme_delete_header_backup($backup_id);
        
        if ($success) {
            wp_send_json_success('Backup deleted successfully');
        } else {
            wp_send_json_error('Failed to delete backup');
        }
    }
    /**
     * Set transient when header options are updated
     */
    public function on_header_options_updated($old_value, $new_value, $option_name) {
        // Debug: log update
        if (current_user_can('manage_options') && defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[ross_theme_on_header_options_updated] Old: ' . json_encode($old_value) . ' New: ' . json_encode($new_value));
        }
        set_transient('ross_header_settings_saved', 1, 30);
    }

    /**
     * Show custom success notice after saving header settings
     */
    public function show_settings_saved_notice() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'ross-theme-header') {
            return;
        }
        $settings_updated = isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true';
        $transient_set = get_transient('ross_header_settings_saved');
        if ($transient_set) {
            delete_transient('ross_header_settings_saved');
        }
        if ($settings_updated || $transient_set) {
            echo '<div class="notice notice-success is-dismissible" style="background:#ecfdf3;border-left:4px solid #22c55e;color:#14532d;"><p><strong>✅ Header settings saved successfully!</strong> Your changes have been applied. <a href="' . home_url('/') . '" target="_blank">View your site →</a></p></div>';
        }
    }
}
if (is_admin()) {
    new RossHeaderOptions();
}