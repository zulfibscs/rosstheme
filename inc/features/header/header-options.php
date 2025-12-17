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
        
        $this->add_layout_section();
        $this->add_logo_section();
        $this->add_topbar_section();
        $this->add_announcement_section();
        $this->add_navigation_section();
        $this->add_cta_section();
        $this->add_appearance_section();
    }
    
    private function add_layout_section() {
        add_settings_section(
            'ross_header_layout_section',
            '🧱 Layout & Structure',
            array($this, 'layout_section_callback'),
            'ross-theme-header-layout'
        );
        
        add_settings_field(
            'header_style',
            'Header Style (Legacy)',
            array($this, 'header_style_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );
        
        add_settings_field(
            'header_width',
            'Header Width',
            array($this, 'header_width_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );
        add_settings_field(
            'header_center',
            'Center Header',
            array($this, 'header_center_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );
        
        add_settings_field(
            'header_padding',
            'Header Padding (px)',
            array($this, 'header_padding_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );

        add_settings_field(
            'header_margin',
            'Header Margin (px)',
            array($this, 'header_margin_callback'),
            'ross-theme-header-layout',
            'ross_header_layout_section'
        );
        
        add_settings_field(
            'sticky_header',
            'Sticky Header',
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
            'Sticky Scroll Threshold (px)',
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
            'header_height',
            'Header Height (px)',
            array($this, 'header_height_callback'),
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
            'mobile_logo',
            'Mobile Logo (Optional)',
            array($this, 'mobile_logo_callback'),
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
            'mobile_logo_width',
            'Mobile Logo Max Width (px)',
            array($this, 'mobile_logo_width_callback'),
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
            '☎️ Top Bar Settings',
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
            'topbar_icon_color',
            'Icon Color',
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
            'mobile_breakpoint',
            'Mobile Breakpoint (px)',
            array($this, 'mobile_breakpoint_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'mobile_menu_style',
            'Mobile Menu Style',
            array($this, 'mobile_menu_style_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'hamburger_animation',
            'Hamburger Icon Animation',
            array($this, 'hamburger_animation_callback'),
            'ross-theme-header-nav',
            'ross_header_nav_section'
        );
        
        add_settings_field(
            'mobile_menu_position',
            'Mobile Menu Position',
            array($this, 'mobile_menu_position_callback'),
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
        // Intentionally left blank per front-end design request (removed description)
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
    }
    
    private function add_appearance_section() {
        add_settings_section(
            'ross_header_appearance_section',
            '🌗 Header Appearance',
            array($this, 'appearance_section_callback'),
            'ross-theme-header-appearance'
        );
        
        add_settings_field(
            'header_bg_color',
            'Background Color',
            array($this, 'header_bg_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'header_opacity',
            'Header Opacity',
            array($this, 'header_opacity_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'header_text_color',
            'Text Color',
            array($this, 'header_text_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'header_link_hover_color',
            'Link Hover Color',
            array($this, 'header_link_hover_color_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'transparent_homepage',
            'Transparent Header on Homepage',
            array($this, 'transparent_homepage_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'header_border_enable',
            'Enable Bottom Border',
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
            'header_border_width',
            'Border Width (px)',
            array($this, 'header_border_width_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'header_font_family',
            'Header Font Family',
            array($this, 'header_font_family_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
        
        add_settings_field(
            'header_font_weight',
            'Header Font Weight',
            array($this, 'header_font_weight_callback'),
            'ross-theme-header-appearance',
            'ross_header_appearance_section'
        );
    }
    
    // Section Callbacks
    public function layout_section_callback() {
        echo '<p>Control the overall structure and behavior of your header.</p>';
    }
    
    public function logo_section_callback() {
        echo '<p>Configure your logo and branding elements.</p>';
    }
    
    public function topbar_section_callback() {
        echo '<p>Settings for the optional top bar above the main header.</p>';
        echo '<style>\n/* Two-column layout for topbar section fields */\n#ross-theme-header-topbar .form-table { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }\n#ross-theme-header-topbar .form-table tr { display:block; padding:0; margin:0; }\n#ross-theme-header-topbar .form-table td { display:block; padding:8px 12px; }\n@media (max-width:800px){ #ross-theme-header-topbar .form-table { grid-template-columns: 1fr; } }\n</style>';
    }
    
    public function nav_section_callback() {
        echo '<p>Customize your navigation menu appearance and behavior.</p>';
    }
    
    public function cta_section_callback() {
        echo '<p>Add search functionality and call-to-action buttons.</p>';
    }
    
    public function appearance_section_callback() {
        echo '<p>Control colors and visual appearance of the header.</p>';
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
            <option value="full" <?php selected($value, 'full'); ?>>Full Width</option>
            <option value="contained" <?php selected($value, 'contained'); ?>>Contained</option>
        </select>
        <?php
    }

    public function header_center_callback() {
        $value = isset($this->options['header_center']) ? $this->options['header_center'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_center]" value="1" <?php checked(1, $value); ?> />
        <label for="header_center">Center header content (logo & menu)</label>
        <p class="description">When enabled the header inner content will be centered horizontally.</p>
        <?php
    }

    public function header_padding_callback() {
        $top = isset($this->options['header_padding_top']) ? absint($this->options['header_padding_top']) : 20;
        $right = isset($this->options['header_padding_right']) ? absint($this->options['header_padding_right']) : 0;
        $bottom = isset($this->options['header_padding_bottom']) ? absint($this->options['header_padding_bottom']) : 20;
        $left = isset($this->options['header_padding_left']) ? absint($this->options['header_padding_left']) : 0;
        ?>
        <div style="display:flex; gap:6px; align-items:center;">
            <input type="number" name="ross_theme_header_options[header_padding_top]" value="<?php echo esc_attr($top); ?>" class="small-text" />
            <label style="margin-right:8px;">Top</label>
            <input type="number" name="ross_theme_header_options[header_padding_right]" value="<?php echo esc_attr($right); ?>" class="small-text" />
            <label style="margin-right:8px;">Right</label>
            <input type="number" name="ross_theme_header_options[header_padding_bottom]" value="<?php echo esc_attr($bottom); ?>" class="small-text" />
            <label style="margin-right:8px;">Bottom</label>
            <input type="number" name="ross_theme_header_options[header_padding_left]" value="<?php echo esc_attr($left); ?>" class="small-text" />
            <label style="margin-right:8px;">Left</label>
        </div>
        <p class="description">Set header padding in pixels for each side.</p>
        <?php
    }

    public function header_margin_callback() {
        $top = isset($this->options['header_margin_top']) ? absint($this->options['header_margin_top']) : 0;
        $right = isset($this->options['header_margin_right']) ? absint($this->options['header_margin_right']) : 0;
        $bottom = isset($this->options['header_margin_bottom']) ? absint($this->options['header_margin_bottom']) : 0;
        $left = isset($this->options['header_margin_left']) ? absint($this->options['header_margin_left']) : 0;
        ?>
        <div style="display:flex; gap:6px; align-items:center;">
            <input type="number" name="ross_theme_header_options[header_margin_top]" value="<?php echo esc_attr($top); ?>" class="small-text" />
            <label style="margin-right:8px;">Top</label>
            <input type="number" name="ross_theme_header_options[header_margin_right]" value="<?php echo esc_attr($right); ?>" class="small-text" />
            <label style="margin-right:8px;">Right</label>
            <input type="number" name="ross_theme_header_options[header_margin_bottom]" value="<?php echo esc_attr($bottom); ?>" class="small-text" />
            <label style="margin-right:8px;">Bottom</label>
            <input type="number" name="ross_theme_header_options[header_margin_left]" value="<?php echo esc_attr($left); ?>" class="small-text" />
            <label style="margin-right:8px;">Left</label>
        </div>
        <p class="description">Set header margin in pixels for each side.</p>
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
        $value = isset($this->options['sticky_header_height']) ? $this->options['sticky_header_height'] : '60';
        ?>
        <input type="number" name="ross_theme_header_options[sticky_header_height]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <p class="description">Header height when sticky and shrunk</p>
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
    
    public function mobile_logo_callback() {
        $value = isset($this->options['mobile_logo']) ? $this->options['mobile_logo'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[mobile_logo]" id="mobile_logo" value="<?php echo esc_url($value); ?>" class="regular-text" />
        <input type="button" class="button ross-upload-button" data-target="mobile_logo" value="Upload Mobile Logo" />
        <?php if ($value): ?>
            <p><img src="<?php echo esc_url($value); ?>" style="max-width: 150px; height: auto; margin-top: 10px;" /></p>
        <?php endif; ?>
        <p class="description">Optional separate logo for mobile devices (typically smaller or simplified).</p>
        <?php
    }
    
    public function logo_width_callback() {
        $value = isset($this->options['logo_width']) ? $this->options['logo_width'] : '200';
        ?>
        <input type="number" name="ross_theme_header_options[logo_width]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function mobile_logo_width_callback() {
        $value = isset($this->options['mobile_logo_width']) ? $this->options['mobile_logo_width'] : '120';
        ?>
        <input type="number" name="ross_theme_header_options[mobile_logo_width]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <p class="description">Maximum width for mobile logo (default: 120px).</p>
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

    public function topbar_icon_color_callback() {
        $value = isset($this->options['topbar_icon_color']) ? $this->options['topbar_icon_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[topbar_icon_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <p class="description">Color for social and custom icons in the top bar (overrides text color).</p>
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
        <input type="number" name="ross_theme_header_options[menu_font_size]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <?php
    }
    
    public function active_item_color_callback() {
        $value = isset($this->options['active_item_color']) ? $this->options['active_item_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[active_item_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }

    public function menu_hover_color_callback() {
        $value = isset($this->options['menu_hover_color']) ? $this->options['menu_hover_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[menu_hover_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <p class="description">Color applied when hovering menu links (desktop).</p>
        <?php
    }

    public function menu_bg_color_callback() {
        $value = isset($this->options['menu_bg_color']) ? $this->options['menu_bg_color'] : '';
        ?>
        <input type="text" name="ross_theme_header_options[menu_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="" />
        <p class="description">Optional background color for the menu area. Leave empty for transparent.</p>
        <?php
    }

    public function menu_border_color_callback() {
        $value = isset($this->options['menu_border_color']) ? $this->options['menu_border_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[menu_border_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <p class="description">Color for the underline/border used by menu items (and active indicator).</p>
        <?php
    }
    
    public function mobile_breakpoint_callback() {
        $value = isset($this->options['mobile_breakpoint']) ? $this->options['mobile_breakpoint'] : '768';
        ?>
        <input type="number" name="ross_theme_header_options[mobile_breakpoint]" value="<?php echo esc_attr($value); ?>" class="small-text" /> px
        <p class="description">Screen width at which mobile menu activates</p>
        <?php
    }
    
    public function menu_hover_effect_callback() {
        $value = isset($this->options['menu_hover_effect']) ? $this->options['menu_hover_effect'] : 'underline';
        ?>
        <select name="ross_theme_header_options[menu_hover_effect]">
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
        <select name="ross_theme_header_options[menu_hover_underline_style]">
            <option value="slide" <?php selected($value, 'slide'); ?>>Slide In</option>
            <option value="fade" <?php selected($value, 'fade'); ?>>Fade In</option>
            <option value="instant" <?php selected($value, 'instant'); ?>>Instant</option>
        </select>
        <p class="description">Animation style for underline hover effect</p>
        <?php
    }
    
    public function mobile_menu_style_callback() {
        $value = isset($this->options['mobile_menu_style']) ? $this->options['mobile_menu_style'] : 'slide';
        ?>
        <select name="ross_theme_header_options[mobile_menu_style]">
            <option value="slide" <?php selected($value, 'slide'); ?>>Slide from Side</option>
            <option value="dropdown" <?php selected($value, 'dropdown'); ?>>Dropdown</option>
            <option value="fullscreen" <?php selected($value, 'fullscreen'); ?>>Full Screen Overlay</option>
            <option value="push" <?php selected($value, 'push'); ?>>Push Content</option>
        </select>
        <p class="description">Style of mobile menu presentation</p>
        <?php
    }
    
    public function hamburger_animation_callback() {
        $value = isset($this->options['hamburger_animation']) ? $this->options['hamburger_animation'] : 'collapse';
        ?>
        <select name="ross_theme_header_options[hamburger_animation]">
            <option value="collapse" <?php selected($value, 'collapse'); ?>>Collapse to X</option>
            <option value="spin" <?php selected($value, 'spin'); ?>>Spin to X</option>
            <option value="arrow" <?php selected($value, 'arrow'); ?>>Arrow</option>
            <option value="minimal" <?php selected($value, 'minimal'); ?>>Minimal Fade</option>
        </select>
        <p class="description">Animation for hamburger menu icon</p>
        <?php
    }
    
    public function mobile_menu_position_callback() {
        $value = isset($this->options['mobile_menu_position']) ? $this->options['mobile_menu_position'] : 'left';
        ?>
        <select name="ross_theme_header_options[mobile_menu_position]">
            <option value="left" <?php selected($value, 'left'); ?>>Left Side</option>
            <option value="right" <?php selected($value, 'right'); ?>>Right Side</option>
            <option value="top" <?php selected($value, 'top'); ?>>From Top</option>
        </select>
        <p class="description">Position for slide-in mobile menu</p>
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
    
    // Field Callbacks - Appearance Section
    public function header_bg_color_callback() {
        $value = isset($this->options['header_bg_color']) ? $this->options['header_bg_color'] : '#ffffff';
        ?>
        <input type="text" name="ross_theme_header_options[header_bg_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#ffffff" />
        <?php
    }
    
    public function header_text_color_callback() {
        $value = isset($this->options['header_text_color']) ? $this->options['header_text_color'] : '#333333';
        ?>
        <input type="text" name="ross_theme_header_options[header_text_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#333333" />
        <?php
    }
    
    public function header_link_hover_color_callback() {
        $value = isset($this->options['header_link_hover_color']) ? $this->options['header_link_hover_color'] : '#E5C902';
        ?>
        <input type="text" name="ross_theme_header_options[header_link_hover_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#E5C902" />
        <?php
    }
    
    public function transparent_homepage_callback() {
        $value = isset($this->options['transparent_homepage']) ? $this->options['transparent_homepage'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[transparent_homepage]" value="1" <?php checked(1, $value); ?> />
        <label for="transparent_homepage">Make header transparent on homepage</label>
        <?php
    }
    
    public function header_opacity_callback() {
        $value = isset($this->options['header_opacity']) ? $this->options['header_opacity'] : '1.0';
        ?>
        <input type="range" name="ross_theme_header_options[header_opacity]" min="0" max="1" step="0.1" value="<?php echo esc_attr($value); ?>" class="ross-range-slider" />
        <span class="ross-range-value"><?php echo esc_html($value); ?></span>
        <p class="description">Header background opacity (0 = fully transparent, 1 = fully opaque)</p>
        <script>
        jQuery(function($) {
            $('.ross-range-slider').on('input', function() {
                $(this).next('.ross-range-value').text($(this).val());
            });
        });
        </script>
        <?php
    }
    
    public function header_border_enable_callback() {
        $value = isset($this->options['header_border_enable']) ? $this->options['header_border_enable'] : 0;
        ?>
        <input type="checkbox" name="ross_theme_header_options[header_border_enable]" value="1" <?php checked(1, $value); ?> />
        <label>Add bottom border to header</label>
        <?php
    }
    
    public function header_border_color_callback() {
        $value = isset($this->options['header_border_color']) ? $this->options['header_border_color'] : '#e0e0e0';
        ?>
        <input type="text" name="ross_theme_header_options[header_border_color]" value="<?php echo esc_attr($value); ?>" class="color-picker" data-default-color="#e0e0e0" />
        <?php
    }
    
    public function header_border_width_callback() {
        $value = isset($this->options['header_border_width']) ? $this->options['header_border_width'] : '1';
        ?>
        <input type="number" name="ross_theme_header_options[header_border_width]" value="<?php echo esc_attr($value); ?>" class="small-text" min="1" max="10" /> px
        <?php
    }
    
    public function header_font_family_callback() {
        $value = isset($this->options['header_font_family']) ? $this->options['header_font_family'] : 'inherit';
        ?>
        <select name="ross_theme_header_options[header_font_family]">
            <option value="inherit" <?php selected($value, 'inherit'); ?>>Theme Default</option>
            <option value="Arial, sans-serif" <?php selected($value, 'Arial, sans-serif'); ?>>Arial</option>
            <option value="'Helvetica Neue', sans-serif" <?php selected($value, "'Helvetica Neue', sans-serif"); ?>>Helvetica</option>
            <option value="Georgia, serif" <?php selected($value, 'Georgia, serif'); ?>>Georgia</option>
            <option value="'Times New Roman', serif" <?php selected($value, "'Times New Roman', serif"); ?>>Times New Roman</option>
            <option value="'Courier New', monospace" <?php selected($value, "'Courier New', monospace"); ?>>Courier New</option>
        </select>
        <p class="description">Font family for header text</p>
        <?php
    }
    
    public function header_font_weight_callback() {
        $value = isset($this->options['header_font_weight']) ? $this->options['header_font_weight'] : '400';
        ?>
        <select name="ross_theme_header_options[header_font_weight]">
            <option value="300" <?php selected($value, '300'); ?>>Light (300)</option>
            <option value="400" <?php selected($value, '400'); ?>>Normal (400)</option>
            <option value="500" <?php selected($value, '500'); ?>>Medium (500)</option>
            <option value="600" <?php selected($value, '600'); ?>>Semi-Bold (600)</option>
            <option value="700" <?php selected($value, '700'); ?>>Bold (700)</option>
        </select>
        <p class="description">Font weight for header menu items</p>
        <?php
    }
    
    // Field Callbacks - Layout Section (Sticky Behavior)
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
        $sanitized['topbar_icon_hover_color'] = isset($input['topbar_icon_hover_color']) ? sanitize_hex_color($input['topbar_icon_hover_color']) : $sanitized['topbar_icon_color'];
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
        $sanitized['mobile_breakpoint'] = absint($input['mobile_breakpoint']);
        
        // Navigation - Hover Effects
        $sanitized['menu_hover_effect'] = isset($input['menu_hover_effect']) ? sanitize_text_field($input['menu_hover_effect']) : 'underline';
        $sanitized['menu_hover_underline_style'] = isset($input['menu_hover_underline_style']) ? sanitize_text_field($input['menu_hover_underline_style']) : 'slide';
        
        // Navigation - Mobile
        $sanitized['mobile_menu_style'] = isset($input['mobile_menu_style']) ? sanitize_text_field($input['mobile_menu_style']) : 'slide';
        $sanitized['hamburger_animation'] = isset($input['hamburger_animation']) ? sanitize_text_field($input['hamburger_animation']) : 'collapse';
        $sanitized['mobile_menu_position'] = isset($input['mobile_menu_position']) ? sanitize_text_field($input['mobile_menu_position']) : 'left';
        
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