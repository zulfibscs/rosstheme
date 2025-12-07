<?php
/**
 * Admin Pages Module
 */

// Hook to enqueue scripts for Ross Theme pages - PRIORITY 1 (FIRST)
function ross_theme_enqueue_admin_scripts($hook) {
    // Check if we're on a Ross theme admin page
    if (strpos($hook, 'ross-theme') !== false) {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
    }
}
add_action('admin_enqueue_scripts', 'ross_theme_enqueue_admin_scripts', 1);

// Handle Footer Settings Reset Requests
function ross_theme_handle_footer_reset() {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    
    // Reset all footer settings
    if (isset($_POST['ross_reset_all_footer']) && $_POST['ross_reset_all_footer'] == '1') {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'ross_reset_footer')) {
            wp_die('Security check failed');
        }
        
        delete_option('ross_theme_footer_options');
        wp_redirect(add_query_arg('reset', 'all', admin_url('admin.php?page=ross-theme-footer')));
        exit;
    }
    
    // Reset specific section
    if (isset($_POST['ross_reset_footer_section']) && !empty($_POST['ross_reset_footer_section'])) {
        if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'ross_reset_footer_section')) {
            wp_die('Security check failed');
        }
        
        $section = sanitize_text_field($_POST['ross_reset_footer_section']);
        $options = get_option('ross_theme_footer_options', array());
        
        // Define section field prefixes
        $section_fields = array(
            'layout' => array('footer_template', 'footer_columns', 'enable_widgets'),
            'styling' => array('styling_bg_color', 'styling_text_color', 'styling_link_color', 'styling_font_size', 'styling_heading_color'),
            'cta' => array('enable_cta', 'cta_title', 'cta_text', 'cta_button_text', 'cta_button_url', 'cta_bg_color', 'cta_text_color', 'cta_button_bg', 'cta_button_text_color', 'cta_alignment', 'cta_title_font_size', 'cta_text_font_size', 'cta_title_font_weight', 'cta_text_font_weight', 'cta_border_width', 'cta_border_style', 'cta_border_color', 'cta_border_radius', 'cta_box_shadow', 'cta_shadow_color', 'cta_shadow_blur', 'cta_button_hover_bg', 'cta_button_hover_text', 'cta_button_hover_transform', 'cta_container_width', 'cta_max_width'),
            'social' => array('enable_social_icons', 'facebook_enabled', 'facebook_url', 'instagram_enabled', 'instagram_url', 'twitter_enabled', 'twitter_url', 'linkedin_enabled', 'linkedin_url', 'custom_social_enabled', 'custom_social_url', 'custom_social_label', 'custom_social_icon', 'custom_social_color', 'social_icon_style', 'social_icon_size', 'social_icon_color', 'social_icon_hover_color', 'social_display_order'),
            'copyright' => array('enable_copyright', 'copyright_text', 'copyright_bg', 'copyright_text_color', 'copyright_alignment')
        );
        
        if (isset($section_fields[$section])) {
            foreach ($section_fields[$section] as $field) {
                unset($options[$field]);
            }
            update_option('ross_theme_footer_options', $options);
        }
        
        wp_redirect(add_query_arg('reset', $section, admin_url('admin.php?page=ross-theme-footer')));
        exit;
    }
}
add_action('admin_init', 'ross_theme_handle_footer_reset');

// Show reset success notices
function ross_theme_footer_reset_notices() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'ross-theme-footer') {
        return;
    }
    
    if (isset($_GET['reset'])) {
        $section = sanitize_text_field($_GET['reset']);
        $message = '';
        
        if ($section === 'all') {
            $message = '✅ All footer settings have been reset to defaults.';
        } else {
            $section_names = array(
                'layout' => 'Layout & Templates',
                'styling' => 'Styling',
                'cta' => 'Call to Action',
                'social' => 'Social Icons',
                'copyright' => 'Copyright'
            );
            $section_name = isset($section_names[$section]) ? $section_names[$section] : $section;
            $message = '✅ ' . $section_name . ' section has been reset to defaults.';
        }
        
        echo '<div class="notice notice-warning is-dismissible" style="border-left-color: #dc3545; background: #fff5f5;"><p style="color: #dc3545; font-weight: 600;">' . esc_html($message) . '</p></div>';
    }
}
add_action('admin_notices', 'ross_theme_footer_reset_notices');

function ross_theme_admin_menu() {
    add_menu_page(
        'Ross Theme Settings',
        'Ross Theme', 
        'manage_options',
        'ross-theme',
        'ross_theme_main_page',
        'dashicons-admin-customizer',
        60
    );
    
    add_submenu_page(
        'ross-theme',
        'Header Options',
        'Header Options',
        'manage_options', 
        'ross-theme-header',
        'ross_theme_header_page'
    );
    
    add_submenu_page(
        'ross-theme', 
        'Footer Options',
        'Footer Options',
        'manage_options',
        'ross-theme-footer',
        'ross_theme_footer_page'
    );
    
    add_submenu_page(
        'ross-theme',
        'General Settings', 
        'General Settings',
        'manage_options',
        'ross-theme-general',
        'ross_theme_general_page'
    );
    
    // Note: Top Bar Settings are handled via WordPress Customizer (customize.php)
    // Reset Settings submenu is handled by RossThemeResetUtility
}
add_action('admin_menu', 'ross_theme_admin_menu');

function ross_theme_main_page() {
    ?>
    <div class="wrap">
        <h1>Ross Theme Settings</h1>
        <div class="card">
            <h2>Welcome to Ross Theme</h2>
            <p>Use the submenus to configure different aspects of your theme:</p>
            <ul>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-homepage-templates'); ?>">🏠 Homepage Templates</a>:</strong> Choose pre-designed homepage templates</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-header'); ?>">Header Options</a>:</strong> Configure logo, navigation, and header layout</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-footer'); ?>">Footer Options</a>:</strong> Setup footer layout, widgets, and copyright</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-general'); ?>">General Settings</a>:</strong> Customize colors, typography, and global settings</li>
                <li><strong><a href="<?php echo admin_url('customize.php'); ?>">🎯 Top Bar Settings</a>:</strong> Configure top bar in WordPress Customizer</li>
                <li><strong><a href="<?php echo admin_url('admin.php?page=ross-theme-reset'); ?>">Reset Settings</a>:</strong> Reset all settings to defaults</li>
            </ul>
        </div>
    </div>
    <?php
}

function ross_theme_header_page() {
    ?>
    <div class="wrap ross-theme-admin">
        <h1>Header Options</h1>
        <?php settings_errors(); ?>
        
        <!-- Tab Navigation -->
        <div class="ross-tabs-nav">
            <button class="ross-tab-btn active" data-tab="templates">📐 Templates</button>
            <button class="ross-tab-btn" data-tab="layout">🧱 Layout & Structure</button>
            <button class="ross-tab-btn" data-tab="logo">🧭 Logo & Branding</button>
            <button class="ross-tab-btn" data-tab="topbar">☎️ Top Bar</button>
            <button class="ross-tab-btn" data-tab="announcement">📣 Announcement</button>
            <button class="ross-tab-btn" data-tab="navigation">🔗 Navigation</button>
            <button class="ross-tab-btn" data-tab="cta">🔍 CTA & Search</button>
            <button class="ross-tab-btn" data-tab="appearance">🌗 Appearance</button>
        </div>
        
        <form method="post" action="options.php" class="ross-form-tabbed">
            <?php
            settings_fields('ross_theme_header_group');
            ?>
            
            <!-- Templates Tab -->
            <div class="ross-tab-content active" id="tab-templates">
                <?php
                // Include header templates admin interface
                require_once dirname(__FILE__) . '/header-templates-admin.php';
                ross_theme_render_header_templates_admin();
                ?>
            </div>
            
            <!-- Layout & Structure Tab -->
            <div class="ross-tab-content" id="tab-layout">
                <?php do_settings_sections('ross-theme-header-layout'); ?>
            </div>
            
            <!-- Logo & Branding Tab -->
            <div class="ross-tab-content" id="tab-logo">
                <?php do_settings_sections('ross-theme-header-logo'); ?>
            </div>
            
            <!-- Top Bar Tab -->
            <div class="ross-tab-content" id="tab-topbar">
                <?php
                // Include the improved topbar admin interface
                require_once dirname(__FILE__) . '/topbar-admin-improved.php';
                ross_theme_render_topbar_admin_improved();
                ?>
                
                <!-- Enqueue improved admin assets -->
                <style>
                    <?php include_once dirname(__FILE__) . '/../../assets/css/admin/topbar-admin-improved.css'; ?>
                </style>
                
                <script>
                    <?php include_once dirname(__FILE__) . '/../../assets/js/admin/topbar-admin-improved.js'; ?>
                </script>
            </div>
            
            <!-- Announcement Tab -->
            <div class="ross-tab-content" id="tab-announcement">
                <?php
                // Include announcement admin interface (moved out of Top Bar)
                require_once dirname(__FILE__) . '/announcement-admin.php';
                ?>
            </div>

            <!-- Navigation Tab -->
            <div class="ross-tab-content" id="tab-navigation">
                <?php do_settings_sections('ross-theme-header-nav'); ?>
            </div>
            
            <!-- CTA & Search Tab -->
            <div class="ross-tab-content" id="tab-cta">
                <?php do_settings_sections('ross-theme-header-cta'); ?>
            </div>
            
            <!-- Appearance Tab -->
            <div class="ross-tab-content" id="tab-appearance">
                <?php do_settings_sections('ross-theme-header-appearance'); ?>
            </div>
            
            <?php submit_button('Save Header Settings', 'primary', 'submit', true, array('class' => 'button-large ross-submit')); ?>
        </form>
    </div>
    
    <style>
        .ross-theme-admin .ross-tabs-nav {
            display: flex;
            gap: 0.5rem;
            margin: 1.5rem 0;
            border-bottom: 2px solid #ccc;
            flex-wrap: wrap;
        }
        .ross-theme-admin .ross-tab-btn {
            padding: 0.75rem 1.2rem;
            background: #f1f1f1;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        .ross-theme-admin .ross-tab-btn:hover {
            background: #e9e9e9;
        }
        .ross-theme-admin .ross-tab-btn.active {
            background: white;
            border-bottom-color: #0073aa;
            color: #0073aa;
        }
        .ross-theme-admin .ross-tab-content {
            display: none;
            background: white;
            padding: 2rem;
            border: 1px solid #ccc;
            margin-bottom: 2rem;
        }
        .ross-theme-admin .ross-tab-content.active {
            display: block;
        }
        .ross-theme-admin .ross-form-fields {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        @media (max-width: 1000px) {
            .ross-theme-admin .ross-form-fields {
                grid-template-columns: 1fr;
            }
        }
        
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var tabBtns = document.querySelectorAll('.ross-tab-btn');
        var tabContents = document.querySelectorAll('.ross-tab-content');
        
        tabBtns.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var tabId = this.getAttribute('data-tab');
                
                // Deactivate all tabs
                tabBtns.forEach(function(b) { b.classList.remove('active'); });
                tabContents.forEach(function(c) { c.classList.remove('active'); });
                
                // Activate clicked tab
                this.classList.add('active');
                document.getElementById('tab-' + tabId).classList.add('active');
            });
        });
    });
    </script>
    
    <script type="text/javascript">
    (function() {
        function initMediaUploaders() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                return;
            }
            
            var buttons = document.querySelectorAll('.ross-upload-button');
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = this.getAttribute('data-target');
                    if (!targetId) return;
                    
                    var frame = wp.media({
                        title: 'Select Image',
                        button: {text: 'Select'},
                        multiple: false
                    });
                    
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        var input = document.getElementById(targetId);
                        if (input) {
                            input.value = attachment.url;
                        }
                    });
                    
                    frame.open();
                    return false;
                });
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMediaUploaders);
        } else {
            initMediaUploaders();
        }
    })();
    </script>
    <?php
}

function ross_theme_footer_page() {
    ?>
    <div class="wrap ross-theme-admin ross-footer-admin">
        <div class="ross-admin-header">
            <div class="ross-header-content">
                <div class="ross-header-left">
                    <h1>⚙️ Footer Customization</h1>
                    <p  class="ross-admin-description">Design and customize your website footer with powerful controls</p>
                </div>
                <div class="ross-header-actions">
                    <button type="button" class="ross-reset-all-btn" onclick="rossResetAllFooterSettings()">
                        <span class="dashicons dashicons-image-rotate"></span> Reset All Settings
                    </button>
                    <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="ross-view-site-btn">
                        <span class="dashicons dashicons-external"></span> View Site
                    </a>
                </div>
            </div>
        </div>
        <?php settings_errors(); ?>

        <div class="ross-tabs-nav">
            <button class="ross-tab-btn active" data-tab="layout">
                <span class="tab-icon">🧱</span>
                <span class="tab-label">Layout & Templates</span>
            </button>
            <button class="ross-tab-btn" data-tab="styling">
                <span class="tab-icon">🎨</span>
                <span class="tab-label">Styling</span>
            </button>
            <button class="ross-tab-btn" data-tab="cta">
                <span class="tab-icon">📢</span>
                <span class="tab-label">Call to Action</span>
            </button>
            <button class="ross-tab-btn" data-tab="social">
                <span class="tab-icon">🌍</span>
                <span class="tab-label">Social Icons</span>
            </button>
            <button class="ross-tab-btn" data-tab="copyright">
                <span class="tab-icon">©</span>
                <span class="tab-label">Copyright</span>
            </button>
        </div>

        <div class="ross-admin-layout">
            <form method="post" action="options.php" class="ross-form-tabbed ross-settings-form">
                <?php settings_fields('ross_theme_footer_group'); ?>

            <div class="ross-tab-content active" id="tab-layout">
                
                <div class="ross-single-column">
                    <?php do_settings_sections('ross-theme-footer-layout'); ?>
                </div>
            </div>                <div class="ross-tab-content" id="tab-styling">
                    <div class="ross-section-header">
                        <div class="ross-section-title">
                            <h2>🎨 Footer Styling</h2>
                            <p class="ross-section-desc">Customize colors, fonts, and visual appearance</p>
                        </div>
                        <button type="button" class="ross-reset-section-btn" onclick="rossResetSection('styling')">
                            <span class="dashicons dashicons-image-rotate"></span> Reset Section
                        </button>
                    </div>
                    <div class="ross-split-layout">
                        <div class="ross-settings-column">
                            <?php do_settings_sections('ross-theme-footer-styling'); ?>
                        </div>
                        <div class="ross-preview-column">
                            <div class="ross-preview-sticky">
                                <div class="ross-preview-header">
                                    <div class="ross-preview-title">
                                        <h3>🎨 Live Preview</h3>
                                        <p>Styling changes appear here</p>
                                    </div>
                                    <button type="button" class="ross-refresh-preview-btn" onclick="rossRefreshPreview('styling')" title="Refresh Preview">
                                        <span class="dashicons dashicons-update"></span>
                                    </button>
                                </div>
                                <div id="ross-styling-preview" class="ross-preview-box">
                                    <div class="preview-footer-sample">
                                        <div class="preview-footer-content">
                                            <div class="preview-footer-column">
                                                <h4>About Us</h4>
                                                <p>Sample footer text with styling applied in real-time</p>
                                                <a href="#" class="preview-link">Sample Link</a>
                                            </div>
                                            <div class="preview-footer-column">
                                                <h4>Quick Links</h4>
                                                <ul>
                                                    <li><a href="#" class="preview-link">Home</a></li>
                                                    <li><a href="#" class="preview-link">About</a></li>
                                                    <li><a href="#" class="preview-link">Contact</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="ross-tab-content" id="tab-cta">
                <div class="ross-section-header">
                    <div class="ross-section-title">
                        <h2>📢 Call to Action Section</h2>
                        <p class="ross-section-desc">Create a powerful CTA above your footer to drive conversions</p>
                    </div>
                    <button type="button" class="ross-reset-section-btn" onclick="rossResetSection('cta')">
                        <span class="dashicons dashicons-image-rotate"></span> Reset Section
                    </button>
                </div>
                
                <div class="ross-cta-subtabs-wrapper">
                    <div class="ross-cta-subtabs-nav">
                        <button type="button" class="ross-cta-subtab-btn active" data-section="ross_footer_cta_visibility">
                            <span class="subtab-icon">⚙️</span> Visibility
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_content">
                            <span class="subtab-icon">📝</span> Content
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_layout">
                            <span class="subtab-icon">📐</span> Layout
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_styling">
                            <span class="subtab-icon">🎨</span> Styling
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_typography">
                            <span class="subtab-icon">✍️</span> Typography
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_spacing">
                            <span class="subtab-icon">📏</span> Spacing
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_animation">
                            <span class="subtab-icon">🎬</span> Animation
                        </button>
                        <button type="button" class="ross-cta-subtab-btn" data-section="ross_footer_cta_advanced">
                            <span class="subtab-icon">🔧</span> Advanced
                        </button>
                    </div>
                </div>
                
                <div class="ross-split-layout">
                    <div class="ross-settings-column">
                        <?php do_settings_sections('ross-theme-footer-cta'); ?>
                    </div>
                    <div class="ross-preview-column">
                        <div class="ross-preview-sticky">
                            <div class="ross-preview-header">
                                <div class="ross-preview-title">
                                    <h3>📢 CTA Preview</h3>
                                    <p>See your changes in real-time</p>
                                </div>
                                <button type="button" class="ross-refresh-preview-btn" onclick="rossRefreshPreview('cta')" title="Refresh Preview">
                                    <span class="dashicons dashicons-update"></span>
                                </button>
                            </div>
                            <div id="ross-cta-preview" class="ross-preview-box">
                                <div class="preview-cta-sample">
                                    <h2 class="cta-title">Ready to Get Started?</h2>
                                    <p class="cta-text">Join thousands of satisfied customers today</p>
                                    <a href="#" class="cta-button">Get Started Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>                <div class="ross-tab-content" id="tab-social">
                    <div class="ross-section-header">
                        <div class="ross-section-title">
                            <h2>🌍 Social Media Icons</h2>
                            <p class="ross-section-desc">Connect with your audience through social media links</p>
                        </div>
                        <button type="button" class="ross-reset-section-btn" onclick="rossResetSection('social')">
                            <span class="dashicons dashicons-image-rotate"></span> Reset Section
                        </button>
                    </div>
                    <div class="ross-split-layout">
                        <div class="ross-settings-column">
                            <?php do_settings_sections('ross-theme-footer-social'); ?>
                        </div>
                        <div class="ross-preview-column">
                            <div class="ross-preview-sticky">
                                <div class="ross-preview-header">
                                    <div class="ross-preview-title">
                                        <h3>🌍 Social Icons Preview</h3>
                                        <p>Preview your social icons</p>
                                    </div>
                                    <button type="button" class="ross-refresh-preview-btn" onclick="rossRefreshPreview('social')" title="Refresh Preview">
                                        <span class="dashicons dashicons-update"></span>
                                    </button>
                                </div>
                                <div id="ross-social-preview" class="ross-preview-box">
                                    <div class="preview-social-sample">
                                        <div class="social-icons-preview">
                                            <a href="#" class="social-icon facebook" title="Facebook">
                                                <i class="fab fa-facebook-f"></i>
                                                <span class="social-label">Facebook</span>
                                            </a>
                                            <a href="#" class="social-icon twitter" title="Twitter / X">
                                                <i class="fab fa-twitter"></i>
                                                <span class="social-label">Twitter</span>
                                            </a>
                                            <a href="#" class="social-icon instagram" title="Instagram">
                                                <i class="fab fa-instagram"></i>
                                                <span class="social-label">Instagram</span>
                                            </a>
                                            <a href="#" class="social-icon linkedin" title="LinkedIn">
                                                <i class="fab fa-linkedin-in"></i>
                                                <span class="social-label">LinkedIn</span>
                                            </a>
                                        </div>
                                        <p class="social-preview-help">💡 Customize icon size, style, and colors in Social settings</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ross-tab-content" id="tab-copyright">
                    
                    <div class="ross-split-layout">
                        <div class="ross-settings-column">
                            <?php do_settings_sections('ross-theme-footer-copyright'); ?>
                        </div>
                        <div class="ross-preview-column">
                            <div class="ross-preview-sticky">
                                <div class="ross-preview-header">
                                    <div class="ross-preview-title">
                                        <h3>© Copyright Preview</h3>
                                        <p>Preview copyright text</p>
                                    </div>
                                    <button type="button" class="ross-refresh-preview-btn" onclick="rossRefreshPreview('copyright')" title="Refresh Preview">
                                        <span class="dashicons dashicons-update"></span>
                                    </button>
                                </div>
                                <div id="ross-copyright-preview" class="ross-preview-box">
                                    <div class="preview-copyright-sample">
                                        <p>&copy; <?php echo date('Y'); ?> Your Company. All rights reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ross-submit-section">
                    <?php submit_button('💾 Save Footer Settings', 'primary large', 'submit', true, array('class' => 'ross-submit-btn')); ?>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* ===== MODERN ADMIN STYLING ===== */
        .ross-footer-admin {
            background: #f0f2f5;
            margin-left: -20px;
            margin-right: -20px;
            padding: 0 20px 40px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
        }
        
        /* Header Section */
        .ross-admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            margin: 0 -20px 2.5rem;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 6px 30px rgba(102,126,234,0.25);
        }
        
        .ross-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2rem 2.5rem;
            gap: 2rem;
        }
        
        .ross-header-left h1 {
            color: white;
            font-size: 2.2rem;
            margin: 0 0 0.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .ross-admin-description {
            color: rgba(255,255,255,0.92);
            margin: 0;
            font-size: 1.05rem;
            font-weight: 400;
        }
        
        .ross-header-actions {
            display: flex;
            gap: 1rem;
            flex-shrink: 0;
        }
        
        .ross-reset-all-btn,
        .ross-view-site-btn {
            padding: 0.65rem 1.4rem;
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            backdrop-filter: blur(10px);
        }
        
        .ross-reset-all-btn:hover,
        .ross-view-site-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            color: white;
        }
        
        .ross-reset-all-btn .dashicons,
        .ross-view-site-btn .dashicons {
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
        
        /* Tab Navigation */
        .ross-tabs-nav {
            display: flex;
            gap: 0.75rem;
            margin: 0 0 2rem;
            flex-wrap: wrap;
            background: white;
            padding: 1.2rem;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        
        .ross-tab-btn {
            padding: 0.85rem 1.6rem;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            white-space: nowrap;
        }
        
        .ross-tab-btn .tab-icon {
            font-size: 1.1rem;
        }
        
        .ross-tab-btn:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }
        
        .ross-tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 6px 20px rgba(102,126,234,0.35);
            transform: translateY(-2px);
        }
        
        /* Layout Containers */
        .ross-admin-layout {
            background: white;
            border-radius: 14px;
            padding: 2.5rem;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        }
        
        .ross-tab-content {
            display: none;
        }
        
        .ross-tab-content.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Section Headers */
        .ross-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 3px solid #e9ecef;
        }
        
        .ross-section-title h2 {
            font-size: 1.6rem;
            color: #2c3e50;
            margin: 0 0 0.4rem;
            font-weight: 700;
        }
        
        .ross-section-desc {
            color: #6c757d;
            font-size: 1rem;
            margin: 0;
            line-height: 1.5;
        }
        
        .ross-reset-section-btn {
            padding: 0.75rem 1.6rem;
            background: white;
            border: 2px solid #dc3545;
            border-radius: 10px;
            color: #dc3545;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(220,53,69,0.1);
        }
        
        .ross-reset-section-btn:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220,53,69,0.35);
        }
        
        .ross-reset-section-btn .dashicons {
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
        
        /* Split Layout for Preview */
        .ross-split-layout {
            display: grid;
            grid-template-columns: 1fr 420px;
            gap: 2.5rem;
            align-items: start;
        }
        
        .ross-settings-column {
            background: white;
            min-width: 0; /* Prevent overflow */
        }
        
        .ross-preview-column {
            position: relative;
        }
        
        .ross-preview-sticky {
            position: sticky;
            top: 32px;
        }
        
        .ross-preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.3rem 1.6rem;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .ross-preview-title h3 {
            margin: 0 0 0.3rem;
            font-size: 1.15rem;
            color: white;
            font-weight: 600;
        }
        
        .ross-preview-title p {
            margin: 0;
            font-size: 0.88rem;
            color: rgba(255,255,255,0.88);
        }
        
        .ross-refresh-preview-btn {
            background: rgba(255,255,255,0.2);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            color: white;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }
        
        .ross-refresh-preview-btn:hover {
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.5);
            transform: rotate(180deg);
        }
        
        .ross-refresh-preview-btn .dashicons {
            font-size: 18px;
            width: 18px;
            height: 18px;
        }
        
        .ross-preview-box {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 2.5rem;
            min-height: 250px;
        }
        
        /* Preview Samples - Enhanced */
        .preview-footer-sample {
            background: #2c3e50;
            color: white;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .preview-footer-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
        }
        
        .preview-footer-column h4 {
            color: white;
            margin: 0 0 1rem;
            font-size: 1.15rem;
            font-weight: 600;
        }
        
        .preview-footer-column p {
            color: #ecf0f1;
            line-height: 1.7;
            margin: 0 0 0.6rem;
            font-size: 0.95rem;
        }
        
        .preview-footer-column a.preview-link {
            color: #3498db;
            text-decoration: none;
            display: block;
            margin: 0.3rem 0;
            transition: color 0.2s ease;
        }
        
        .preview-footer-column a.preview-link:hover {
            color: #5dade2;
        }
        
        .preview-footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .preview-footer-column ul li {
            margin: 0.4rem 0;
        }
        
        .preview-cta-sample {
            text-align: center;
            padding: 3.5rem 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .preview-cta-sample .cta-title {
            margin: 0 0 1.2rem;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            line-height: 1.3;
        }
        
        .preview-cta-sample .cta-text {
            margin: 0 0 1.8rem;
            font-size: 1.15rem;
            color: rgba(255,255,255,0.92);
            line-height: 1.6;
        }
        
        .preview-cta-sample .cta-button {
            display: inline-block;
            padding: 0.9rem 2.5rem;
            background: white;
            color: #667eea;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .preview-cta-sample .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        
        .preview-social-sample {
            text-align: center;
            padding: 2.5rem;
        }
        
        .social-icons-preview {
            display: flex;
            gap: 1.2rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .social-icons-preview .social-icon {
            position: relative;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.4rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            text-decoration: none;
        }
        
        .social-icon.facebook { background: #1877f2; }
        .social-icon.twitter { background: #1da1f2; }
        .social-icon.instagram { 
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); 
        }
        .social-icon.linkedin { background: #0077b5; }
        
        .social-icon:hover {
            transform: translateY(-6px) scale(1.08);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        
        .social-icon .social-label {
            position: absolute;
            bottom: -28px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 600;
            color: #495057;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .social-icon:hover .social-label {
            opacity: 1;
        }
        
        .social-preview-help {
            margin: 40px 0 0;
            padding: 12px 16px;
            background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
            border-left: 4px solid #ffc107;
            border-radius: 6px;
            font-size: 13px;
            color: #856404;
            text-align: center;
            line-height: 1.5;
        }
        
        .preview-copyright-sample {
            text-align: center;
            padding: 1.8rem;
            background: #2c3e50;
            color: #ecf0f1;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .preview-copyright-sample p {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        /* Submit Section */
        .ross-submit-section {
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 3px solid #e9ecef;
            text-align: center;
            position: relative;
            z-index: 100;
            clear: both;
        }
        
        .ross-submit-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border: none !important;
            padding: 1.1rem 3.5rem !important;
            font-size: 1.15rem !important;
            border-radius: 50px !important;
            box-shadow: 0 6px 20px rgba(102,126,234,0.35) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-weight: 600 !important;
            letter-spacing: 0.3px !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            position: relative !important;
            z-index: 101 !important;
        }
        
        .ross-submit-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 10px 30px rgba(102,126,234,0.45) !important;
        }
        
        .ross-submit-btn:active {
            transform: translateY(-1px) !important;
        }
        
        .ross-submit-btn:focus {
            outline: 3px solid rgba(102,126,234,0.4) !important;
            outline-offset: 2px !important;
        }
        
        /* Form Table Improvements */
        .ross-settings-form .form-table {
            background: white;
            border-radius: 10px;
            margin: 1rem 0 2rem;
        }
        
        .ross-settings-form .form-table th {
            padding: 1.4rem 1.2rem;
            font-weight: 600;
            color: #495057;
            width: 240px;
            font-size: 0.95rem;
            vertical-align: middle;
        }
        
        .ross-settings-form .form-table td {
            padding: 1.4rem 1.2rem;
            vertical-align: middle;
        }
        
        .ross-settings-form .form-table input[type="text"],
        .ross-settings-form .form-table input[type="number"],
        .ross-settings-form .form-table input[type="url"],
        .ross-settings-form .form-table textarea,
        .ross-settings-form .form-table select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.65rem 0.9rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            width: 100%;
            max-width: 100%;
        }
        
        .ross-settings-form .form-table input[type="text"]:focus,
        .ross-settings-form .form-table input[type="number"]:focus,
        .ross-settings-form .form-table input[type="url"]:focus,
        .ross-settings-form .form-table textarea:focus,
        .ross-settings-form .form-table select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
            outline: none;
        }
        
        .ross-settings-form .form-table textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .ross-settings-form .form-table input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 8px;
            cursor: pointer;
        }
        
        /* Section Subsection Headers */
        .ross-settings-form h2 {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.2rem 1.8rem;
            border-radius: 10px;
            border-left: 5px solid #667eea;
            margin: 2.5rem 0 1.5rem;
            font-size: 1.25rem;
            color: #2c3e50;
            font-weight: 700;
        }
        
        /* CTA Subtabs */
        .ross-cta-subtabs-wrapper {
            margin-bottom: 2rem;
        }
        
        .ross-cta-subtabs-nav {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        
        .ross-cta-subtab-btn {
            padding: 0.6rem 1.2rem;
            background: white;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        
        .ross-cta-subtab-btn .subtab-icon {
            font-size: 1rem;
        }
        
        .ross-cta-subtab-btn:hover {
            background: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .ross-cta-subtab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 3px 12px rgba(102,126,234,0.3);
        }
        
        .ross-cta-section-wrapper {
            display: none;
        }
        
        .ross-cta-section-wrapper.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        /* Field Descriptions */
        .ross-settings-form .description {
            color: #6c757d;
            font-size: 0.88rem;
            margin-top: 0.5rem;
            line-height: 1.5;
        }
        
        /* Color Picker Inputs */
        input[type="color"] {
            height: 40px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        input[type="color"]:hover {
            border-color: #667eea;
        }
        
        /* Responsive Design */
        @media (max-width: 1400px) {
            .ross-split-layout {
                grid-template-columns: 1fr 380px;
                gap: 2rem;
            }
        }
        
        @media (max-width: 1280px) {
            .ross-split-layout {
                grid-template-columns: 1fr 350px;
                gap: 1.5rem;
            }
            
            .ross-header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .ross-header-actions {
                width: 100%;
            }
        }
        
        @media (max-width: 1024px) {
            .ross-split-layout {
                grid-template-columns: 1fr;
            }
            
            .ross-preview-sticky {
                position: static;
            }
            
            .ross-preview-column {
                order: -1; /* Show preview on top on mobile */
            }
            
            .ross-tabs-nav {
                padding: 0.8rem;
                gap: 0.5rem;
            }
            
            .ross-tab-btn {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
            }
            
            .ross-tab-btn .tab-label {
                display: none;
            }
            
            .ross-tab-btn .tab-icon {
                font-size: 1.3rem;
            }
        }
        
        @media (max-width: 768px) {
            .ross-footer-admin {
                margin-left: -10px;
                margin-right: -10px;
                padding: 0 10px 30px;
            }
            
            .ross-admin-header {
                margin: 0 -10px 1.5rem;
                border-radius: 0 0 12px 12px;
            }
            
            .ross-header-content {
                padding: 1.5rem;
            }
            
            .ross-header-left h1 {
                font-size: 1.6rem;
            }
            
            .ross-admin-description {
                font-size: 0.95rem;
            }
            
            .ross-header-actions {
                flex-direction: column;
                gap: 0.6rem;
            }
            
            .ross-reset-all-btn,
            .ross-view-site-btn {
                justify-content: center;
                width: 100%;
            }
            
            .ross-admin-layout {
                padding: 1.5rem;
            }
            
            .ross-section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .ross-reset-section-btn {
                width: 100%;
                justify-content: center;
            }
            
            .ross-settings-form .form-table th {
                width: 100%;
                display: block;
                padding-bottom: 0.5rem;
            }
            
            .ross-settings-form .form-table td {
                display: block;
                width: 100%;
                padding-top: 0.5rem;
            }
            
            .ross-cta-subtabs-nav {
                gap: 0.5rem;
            }
            
            .ross-cta-subtab-btn {
                padding: 0.55rem 1rem;
                font-size: 0.85rem;
            }
            
            .preview-footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .ross-tabs-nav {
                padding: 0.6rem;
            }
            
            .ross-tab-btn {
                flex: 1;
                justify-content: center;
                min-width: 60px;
            }
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Tab Switching
        var tabBtns = document.querySelectorAll('.ross-tab-btn');
        var tabContents = document.querySelectorAll('.ross-tab-content');
        tabBtns.forEach(function(btn){
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var tab = this.getAttribute('data-tab');
                tabBtns.forEach(function(b){ b.classList.remove('active'); });
                tabContents.forEach(function(c){ c.classList.remove('active'); });
                this.classList.add('active');
                document.getElementById('tab-' + tab).classList.add('active');
            });
        });
        
        // CTA Subtab Switching
        var ctaSubtabBtns = document.querySelectorAll('.ross-cta-subtab-btn');
        if (ctaSubtabBtns.length > 0) {
            var ctaSectionMapping = {
                'ross_footer_cta_visibility': 'Visibility',
                'ross_footer_cta_content': 'Content',
                'ross_footer_cta_layout': 'Layout',
                'ross_footer_cta_styling': 'Styling',
                'ross_footer_cta_typography': 'Typography',
                'ross_footer_cta_spacing': 'Spacing & Dimensions',
                'ross_footer_cta_animation': 'Animation',
                'ross_footer_cta_advanced': 'Advanced'
            };
            
            // Add IDs to section headings and wrap them
            function wrapCtaSections() {
                var allH2 = document.querySelectorAll('#tab-cta h2');
                
                allH2.forEach(function(heading) {
                    var headingText = heading.textContent.trim();
                    var sectionId = null;
                    
                    // Match heading text to section ID
                    for (var id in ctaSectionMapping) {
                        if (ctaSectionMapping[id] === headingText) {
                            sectionId = id;
                            break;
                        }
                    }
                    
                    if (!sectionId) return;
                    
                    // Add ID to heading
                    heading.id = sectionId;
                    
                    // Check if already wrapped
                    if (heading.parentElement && heading.parentElement.classList.contains('ross-cta-section-wrapper')) {
                        return;
                    }
                    
                    // Find description paragraph
                    var description = heading.nextElementSibling;
                    
                    // Find the associated table
                    var table = description && description.tagName === 'P' ? description.nextElementSibling : heading.nextElementSibling;
                    while (table && table.tagName !== 'TABLE') {
                        table = table.nextElementSibling;
                    }
                    
                    if (!table) return;
                    
                    // Create wrapper
                    var wrapper = document.createElement('div');
                    wrapper.className = 'ross-cta-section-wrapper';
                    wrapper.setAttribute('data-section', sectionId);
                    
                    // Insert wrapper and move elements
                    heading.parentNode.insertBefore(wrapper, heading);
                    wrapper.appendChild(heading);
                    if (description && description.tagName === 'P') {
                        wrapper.appendChild(description);
                    }
                    wrapper.appendChild(table);
                });
            }
            
            // Show specific section
            function showCtaSection(sectionId) {
                var wrappers = document.querySelectorAll('.ross-cta-section-wrapper');
                wrappers.forEach(function(wrapper) {
                    wrapper.classList.remove('active');
                    if (wrapper.getAttribute('data-section') === sectionId) {
                        wrapper.classList.add('active');
                    }
                });
            }
            
            // Initialize
            wrapCtaSections();
            showCtaSection('ross_footer_cta_visibility');
            
            // Subtab click handlers
            ctaSubtabBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var section = this.getAttribute('data-section');
                    
                    // Update active state
                    ctaSubtabBtns.forEach(function(b) { b.classList.remove('active'); });
                    this.classList.add('active');
                    
                    // Show section
                    showCtaSection(section);
                });
            });
        }
        
        // Live Preview for CTA
        function updateCtaPreview() {
            var preview = document.querySelector('#ross-cta-preview .preview-cta-sample');
            if (!preview) return;
            
            // Get option name prefix
            var opts = window.ross_theme_footer_options || {};
            
            // Title
            var titleInput = document.querySelector('input[name="ross_theme_footer_options[cta_title]"]');
            if (titleInput) {
                var titleEl = preview.querySelector('.cta-title');
                if (titleEl) titleEl.textContent = titleInput.value || 'Ready to Get Started?';
            }
            
            // Text
            var textInput = document.querySelector('textarea[name="ross_theme_footer_options[cta_text]"]');
            if (textInput) {
                var textEl = preview.querySelector('.cta-text');
                if (textEl) textEl.textContent = textInput.value || 'Join thousands of satisfied customers today';
            }
            
            // Button Text
            var btnTextInput = document.querySelector('input[name="ross_theme_footer_options[cta_button_text]"]');
            if (btnTextInput) {
                var btnEl = preview.querySelector('.cta-button');
                if (btnEl) btnEl.textContent = btnTextInput.value || 'Get Started Now';
            }
            
            // Background Color
            var bgColorInput = document.querySelector('input[name="ross_theme_footer_options[cta_bg_color]"]');
            if (bgColorInput && bgColorInput.value) {
                preview.style.background = bgColorInput.value;
            }
            
            // Text Color
            var textColorInput = document.querySelector('input[name="ross_theme_footer_options[cta_text_color]"]');
            if (textColorInput && textColorInput.value) {
                var titleEl = preview.querySelector('.cta-title');
                var textEl = preview.querySelector('.cta-text');
                if (titleEl) titleEl.style.color = textColorInput.value;
                if (textEl) textEl.style.color = textColorInput.value;
            }
            
            // Button Background
            var btnBgInput = document.querySelector('input[name="ross_theme_footer_options[cta_button_bg]"]');
            if (btnBgInput && btnBgInput.value) {
                var btnEl = preview.querySelector('.cta-button');
                if (btnEl) btnEl.style.background = btnBgInput.value;
            }
            
            // Button Text Color
            var btnTextColorInput = document.querySelector('input[name="ross_theme_footer_options[cta_button_text_color]"]');
            if (btnTextColorInput && btnTextColorInput.value) {
                var btnEl = preview.querySelector('.cta-button');
                if (btnEl) btnEl.style.color = btnTextColorInput.value;
            }
            
            // Title Font Size
            var titleSizeInput = document.querySelector('input[name="ross_theme_footer_options[cta_title_font_size]"]');
            if (titleSizeInput && titleSizeInput.value) {
                var titleEl = preview.querySelector('.cta-title');
                if (titleEl) titleEl.style.fontSize = titleSizeInput.value + 'px';
            }
            
            // Text Font Size
            var textSizeInput = document.querySelector('input[name="ross_theme_footer_options[cta_text_font_size]"]');
            if (textSizeInput && textSizeInput.value) {
                var textEl = preview.querySelector('.cta-text');
                if (textEl) textEl.style.fontSize = textSizeInput.value + 'px';
            }
            
            // Border
            var borderWidth = document.querySelector('input[name="ross_theme_footer_options[cta_border_width]"]');
            var borderStyle = document.querySelector('select[name="ross_theme_footer_options[cta_border_style]"]');
            var borderColor = document.querySelector('input[name="ross_theme_footer_options[cta_border_color]"]');
            var borderRadius = document.querySelector('input[name="ross_theme_footer_options[cta_border_radius]"]');
            
            if (borderWidth && borderWidth.value && parseInt(borderWidth.value) > 0) {
                var style = borderStyle ? borderStyle.value : 'solid';
                var color = borderColor && borderColor.value ? borderColor.value : '#cccccc';
                preview.style.border = borderWidth.value + 'px ' + style + ' ' + color;
            }
            
            if (borderRadius && borderRadius.value) {
                preview.style.borderRadius = borderRadius.value + 'px';
            }
            
            // Box Shadow
            var shadowEnabled = document.querySelector('input[name="ross_theme_footer_options[cta_box_shadow]"]');
            if (shadowEnabled && shadowEnabled.checked) {
                var shadowColor = document.querySelector('input[name="ross_theme_footer_options[cta_shadow_color]"]');
                var shadowBlur = document.querySelector('input[name="ross_theme_footer_options[cta_shadow_blur]"]');
                var color = shadowColor && shadowColor.value ? shadowColor.value : 'rgba(0,0,0,0.1)';
                var blur = shadowBlur && shadowBlur.value ? shadowBlur.value : '10';
                preview.style.boxShadow = '0 4px ' + blur + 'px ' + color;
            } else {
                preview.style.boxShadow = 'none';
            }
        }
        
        // Live Preview for Styling
        function updateStylingPreview() {
            var preview = document.querySelector('#ross-styling-preview .preview-footer-sample');
            if (!preview) return;
            
            // Background Color
            var bgInput = document.querySelector('input[name="ross_theme_footer_options[styling_bg_color]"]');
            if (bgInput && bgInput.value) {
                preview.style.background = bgInput.value;
            }
            
            // Text Color
            var textColorInput = document.querySelector('input[name="ross_theme_footer_options[styling_text_color]"]');
            if (textColorInput && textColorInput.value) {
                preview.style.color = textColorInput.value;
                var paragraphs = preview.querySelectorAll('p');
                paragraphs.forEach(function(p) { p.style.color = textColorInput.value; });
            }
            
            // Link Color
            var linkColorInput = document.querySelector('input[name="ross_theme_footer_options[styling_link_color]"]');
            if (linkColorInput && linkColorInput.value) {
                var links = preview.querySelectorAll('a');
                links.forEach(function(a) { a.style.color = linkColorInput.value; });
            }
            
            // Font Size
            var fontSizeInput = document.querySelector('input[name="ross_theme_footer_options[styling_font_size]"]');
            if (fontSizeInput && fontSizeInput.value) {
                preview.style.fontSize = fontSizeInput.value + 'px';
            }
        }
        
        // Live Preview for Copyright
        function updateCopyrightPreview() {
            var preview = document.querySelector('#ross-copyright-preview .preview-copyright-sample p');
            if (!preview) return;
            
            var textInput = document.querySelector('textarea[name="ross_theme_footer_options[copyright_text]"]');
            if (textInput && textInput.value) {
                preview.innerHTML = textInput.value;
            }
        }
        
        // Attach live preview listeners
        var ctaInputs = document.querySelectorAll('#tab-cta input, #tab-cta textarea, #tab-cta select');
        ctaInputs.forEach(function(input) {
            input.addEventListener('input', updateCtaPreview);
            input.addEventListener('change', updateCtaPreview);
        });
        
        var stylingInputs = document.querySelectorAll('#tab-styling input, #tab-styling select');
        stylingInputs.forEach(function(input) {
            input.addEventListener('input', updateStylingPreview);
            input.addEventListener('change', updateStylingPreview);
        });
        
        var copyrightInputs = document.querySelectorAll('#tab-copyright textarea');
        copyrightInputs.forEach(function(input) {
            input.addEventListener('input', updateCopyrightPreview);
        });
        
        // Initialize previews with current values
        setTimeout(function() {
            updateCtaPreview();
            updateStylingPreview();
            updateCopyrightPreview();
        }, 100);
    });
    
    // ===== RESET FUNCTIONS =====
    function rossResetAllFooterSettings() {
        if (!confirm('⚠️ Are you sure you want to reset ALL footer settings to defaults?\n\nThis will erase all your customizations including:\n• Layout & Templates\n• Styling\n• Call to Action\n• Social Icons\n• Copyright\n\nThis action cannot be undone!')) {
            return;
        }
        
        if (confirm('🚨 FINAL WARNING: This will permanently delete all footer customizations. Continue?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = window.location.href;
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ross_reset_all_footer';
            input.value = '1';
            
            var nonce = document.createElement('input');
            nonce.type = 'hidden';
            nonce.name = '_wpnonce';
            nonce.value = '<?php echo wp_create_nonce("ross_reset_footer"); ?>';
            
            form.appendChild(input);
            form.appendChild(nonce);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function rossResetSection(section) {
        var sectionNames = {
            'layout': 'Layout & Templates',
            'styling': 'Styling',
            'cta': 'Call to Action',
            'social': 'Social Icons',
            'copyright': 'Copyright'
        };
        
        var sectionName = sectionNames[section] || section;
        
        if (!confirm('⚠️ Reset ' + sectionName + ' section to defaults?\n\nThis will clear all customizations in this section.\n\nContinue?')) {
            return;
        }
        
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.href;
        
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ross_reset_footer_section';
        input.value = section;
        
        var nonce = document.createElement('input');
        nonce.type = 'hidden';
        nonce.name = '_wpnonce';
        nonce.value = '<?php echo wp_create_nonce("ross_reset_footer_section"); ?>';
        
        form.appendChild(input);
        form.appendChild(nonce);
        document.body.appendChild(form);
        form.submit();
    }
    
    function rossRefreshPreview(previewType) {
        var btn = event.target.closest('.ross-refresh-preview-btn');
        if (btn) {
            btn.classList.add('spinning');
            btn.style.pointerEvents = 'none';
        }
        
        setTimeout(function() {
            if (previewType === 'cta') {
                var preview = document.querySelector('#ross-cta-preview .preview-cta-sample');
                if (preview) {
                    preview.style.opacity = '0.5';
                    setTimeout(function() {
                        updateCtaPreview();
                        preview.style.opacity = '1';
                    }, 200);
                }
            } else if (previewType === 'styling') {
                var preview = document.querySelector('#ross-styling-preview .preview-footer-sample');
                if (preview) {
                    preview.style.opacity = '0.5';
                    setTimeout(function() {
                        updateStylingPreview();
                        preview.style.opacity = '1';
                    }, 200);
                }
            } else if (previewType === 'copyright') {
                var preview = document.querySelector('#ross-copyright-preview .preview-copyright-sample');
                if (preview) {
                    preview.style.opacity = '0.5';
                    setTimeout(function() {
                        updateCopyrightPreview();
                        preview.style.opacity = '1';
                    }, 200);
                }
            }
            
            if (btn) {
                setTimeout(function() {
                    btn.classList.remove('spinning');
                    btn.style.pointerEvents = 'auto';
                }, 400);
            }
        }, 100);
    }
    
    // Add CSS for spinning animation
    var style = document.createElement('style');
    style.textContent = '.spinning { animation: spin 0.5s linear; } @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }';
    document.head.appendChild(style);
    </script>

    <?php
}

function ross_theme_general_page() {
    ?>
    <div class="wrap">
        <h1>General Settings</h1>
        <?php settings_errors(); ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('ross_theme_general_group');
            do_settings_sections('ross-theme-general');
            submit_button('Save General Settings');
            ?>
        </form>
    </div>
    
    <script type="text/javascript">
    (function() {
        function initMediaUploaders() {
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                return;
            }
            
            var buttons = document.querySelectorAll('.ross-upload-button');
            buttons.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = this.getAttribute('data-target');
                    if (!targetId) return;
                    
                    var frame = wp.media({
                        title: 'Select Image',
                        button: {text: 'Select'},
                        multiple: false
                    });
                    
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        var input = document.getElementById(targetId);
                        if (input) {
                            input.value = attachment.url;
                        }
                    });
                    
                    frame.open();
                    return false;
                });
            });
        }
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMediaUploaders);
        } else {
            initMediaUploaders();
        }
    })();
    </script>
    <?php
}