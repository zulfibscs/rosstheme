<?php
/**
 * Improved Top Bar Admin Interface
 * Modern, clean UI with enhanced functionality
 */

// Don't load directly
if (!defined('ABSPATH')) {
    exit;
}

function ross_theme_render_topbar_admin_improved() {
    $options = get_option('ross_theme_header_options', array());
    $get = function($key, $default = '') use ($options) {
        return isset($options[$key]) ? $options[$key] : $default;
    };
    
    // Debug output for admins
    if (current_user_can('manage_options')) {
        echo '<!-- DEBUG: Admin options loaded: ' . json_encode($options) . ' -->';
        $custom_icons = $get('topbar_custom_icon_links', array());
        echo '<!-- DEBUG: Custom icons in admin: ' . json_encode($custom_icons) . ' -->';
    }
    ?>
    
    <div class="ross-topbar-admin-improved">
        <div class="ross-admin-header">
            <h2>🎯 Top Bar Settings</h2>
            <p>Configure your website's top bar with modern styling and enhanced functionality.</p>
        </div>
        
        <div class="ross-topbar-grid">
            <!-- Left Column: General Settings & Social Icons -->
            <div class="ross-topbar-left">
                <div class="ross-admin-section">
                    <h3>⚙️ General Settings</h3>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[enable_topbar]" value="1" <?php checked(1, $get('enable_topbar', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Top Bar</span>
                        </label>
                        <p class="ross-field-description">Toggle the top bar visibility on the front-end</p>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[enable_topbar_left]" value="1" <?php checked(1, $get('enable_topbar_left', 1)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Left Section</span>
                        </label>
                        <p class="ross-field-description">Show or hide the left area (phone/text/custom HTML)</p>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-field-label">Left Content</label>
                        <?php 
                        $left_val = $get('topbar_left_content', '');
                        $editor_id = 'ross_left_content_editor';
                        wp_editor($left_val, $editor_id, array('textarea_name' => 'ross_theme_header_options[topbar_left_content]', 'textarea_rows' => 3, 'teeny' => true, 'media_buttons' => false));
                        ?>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-field-label">Phone Number</label>
                        <input type="text" name="ross_theme_header_options[phone_number]" value="<?php echo esc_attr($get('phone_number')); ?>" class="ross-input" placeholder="e.g., +44 20 7123 4567" />
                    </div>
                </div>
                
                <div class="ross-admin-section">
                    <h3>🔗 Social Icons</h3>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[enable_social]" value="1" <?php checked(1, $get('enable_social', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Social Icons</span>
                        </label>
                    </div>
                    
                    <div class="ross-social-icons-container">
                        <div class="ross-social-items" id="ross-social-items">
                            <?php
                            $social_platforms = array(
                                'facebook' => array('icon' => 'fab fa-facebook', 'label' => 'Facebook', 'color' => '#1877f2'),
                                'twitter' => array('icon' => 'fab fa-twitter', 'label' => 'Twitter', 'color' => '#1da1f2'),
                                'linkedin' => array('icon' => 'fab fa-linkedin', 'label' => 'LinkedIn', 'color' => '#0077b5'),
                                'instagram' => array('icon' => 'fab fa-instagram', 'label' => 'Instagram', 'color' => '#e4405f'),
                                'youtube' => array('icon' => 'fab fa-youtube', 'label' => 'YouTube', 'color' => '#ff0000')
                            );
                            
                            foreach ($social_platforms as $platform => $data) {
                                $url = $get('social_' . $platform, '');
                                $enabled = $get('social_' . $platform . '_enabled', !empty($url));
                                $icon = $get('social_' . $platform . '_icon', $data['icon']);
                            ?>
                            <div class="ross-social-item" data-platform="<?php echo esc_attr($platform); ?>">
                                <div class="ross-social-drag-handle">⋮⋮</div>
                                <div class="ross-social-toggle">
                                    <label class="ross-switch-label">
                                        <input type="checkbox" name="ross_theme_header_options[social_<?php echo $platform; ?>_enabled]" value="1" <?php checked(1, $enabled); ?> />
                                        <span class="ross-switch"></span>
                                    </label>
                                </div>
                                <div class="ross-social-icon-preview">
                                    <i class="<?php echo esc_attr($icon); ?>" data-default-color="<?php echo esc_attr($data['color']); ?>"></i>
                                </div>
                                <div class="ross-social-fields">
                                    <div class="ross-social-platform"><?php echo esc_html($data['label']); ?></div>
                                    <input type="url" name="ross_theme_header_options[social_<?php echo $platform; ?>]" value="<?php echo esc_attr($url); ?>" class="ross-input ross-input-small" placeholder="URL" />
                                    <input type="text" name="ross_theme_header_options[social_<?php echo $platform; ?>_icon]" value="<?php echo esc_attr($icon); ?>" class="ross-input ross-input-small" placeholder="Icon class" />
                                    <button type="button" class="ross-button ross-button-icon ross-upload-custom-icon" data-platform="<?php echo $platform; ?>" title="Upload Custom Icon">📁</button>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        
                        <!-- Custom Icons Section - Integrated with Social Icons -->
                        <div class="ross-custom-icons-section">
                            <div class="ross-section-header">
                                <h4>➕ Custom Icons</h4>
                                <button type="button" class="ross-button ross-button-small" id="ross-add-social-icon">+ Add Icon</button>
                            </div>
                            <div class="ross-custom-icons-list">
                                <?php
                                $custom_icons = $get('topbar_custom_icon_links', array());
                                if (is_array($custom_icons)) {
                                    foreach ($custom_icons as $index => $icon) {
                                        if (!is_array($icon)) continue;
                                        $enabled = isset($icon['enabled']) ? $icon['enabled'] : (!empty($icon['url']));
                                        $icon_class = $icon['icon'] ?? '';
                                        $url = $icon['url'] ?? '';
                                        $title = $icon['title'] ?? 'Custom Icon';
                                        ?>
                                        <div class="ross-social-item ross-custom-icon-item" data-platform="custom_<?php echo $index; ?>">
                                            <div class="ross-social-drag-handle">⋮⋮</div>
                                            <div class="ross-social-toggle">
                                                <label class="ross-switch-label">
                                                    <input type="checkbox" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $index; ?>][enabled]" value="1" <?php checked(1, $enabled); ?> />
                                                    <span class="ross-switch"></span>
                                                </label>
                                            </div>
                                            <div class="ross-social-icon-preview">
                                                <i class="<?php echo esc_attr($icon_class); ?>" style="color: #6b7280;"></i>
                                            </div>
                                            <div class="ross-social-fields">
                                                <input type="text" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $index; ?>][title]" value="<?php echo esc_attr($title); ?>" class="ross-input ross-input-small" placeholder="Icon name" />
                                                <input type="url" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $index; ?>][url]" value="<?php echo esc_attr($url); ?>" class="ross-input ross-input-small" placeholder="https://" />
                                                <input type="text" name="ross_theme_header_options[topbar_custom_icon_links][<?php echo $index; ?>][icon]" value="<?php echo esc_attr($icon_class); ?>" class="ross-input ross-input-small" placeholder="fab fa-custom" />
                                                <button type="button" class="ross-button ross-button-icon ross-upload-custom-icon" data-platform="custom_<?php echo $index; ?>" title="Upload Custom Icon">📁</button>
                                                <button type="button" class="ross-button ross-button-icon ross-remove-custom-icon" title="Remove">×</button>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="ross-admin-notice" style="background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 6px; padding: 12px; margin-top: 16px;">
                                <p style="margin: 0; color: #0c4a6e;"><strong>💡 Tip:</strong> Add unlimited custom social icons. Use FontAwesome classes like <code>fab fa-discord</code> or <code>fas fa-envelope</code>. Click the "Save Header Settings" button to apply changes.</p>
                            </div>
                        </div>
                        
                        <div class="ross-social-options">
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Size</label>
                                <select name="ross_theme_header_options[social_icon_size]" class="ross-select">
                                    <option value="small" <?php selected($get('social_icon_size', 'medium'), 'small'); ?>>Small</option>
                                    <option value="medium" <?php selected($get('social_icon_size', 'medium'), 'medium'); ?>>Medium</option>
                                    <option value="large" <?php selected($get('social_icon_size', 'medium'), 'large'); ?>>Large</option>
                                </select>
                            </div>
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Shape</label>
                                <select name="ross_theme_header_options[social_icon_shape]" class="ross-select">
                                    <option value="circle" <?php selected($get('social_icon_shape', 'circle'), 'circle'); ?>>Circle</option>
                                    <option value="square" <?php selected($get('social_icon_shape', 'circle'), 'square'); ?>>Square</option>
                                    <option value="rounded" <?php selected($get('social_icon_shape', 'circle'), 'rounded'); ?>>Rounded</option>
                                    <option value="plain" <?php selected($get('social_icon_shape', 'circle'), 'plain'); ?>>Plain</option>
                                </select>
                            </div>
                            
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Width (px)</label>
                                <input type="number" name="ross_theme_header_options[social_icon_width]" value="<?php echo esc_attr($get('social_icon_width', '32')); ?>" min="20" max="100" step="1" class="ross-input" />
                                <p class="ross-field-description">Custom width for social icons (20-100px)</p>
                            </div>
                            
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Effect</label>
                                <select name="ross_theme_header_options[social_icon_effect]" class="ross-select">
                                    <option value="none" <?php selected($get('social_icon_effect', 'none'), 'none'); ?>>No Effect</option>
                                    <option value="bounce" <?php selected($get('social_icon_effect', 'none'), 'bounce'); ?>>Bounce</option>
                                    <option value="pulse" <?php selected($get('social_icon_effect', 'none'), 'pulse'); ?>>Pulse</option>
                                    <option value="rotate" <?php selected($get('social_icon_effect', 'none'), 'rotate'); ?>>Rotate</option>
                                    <option value="scale" <?php selected($get('social_icon_effect', 'none'), 'scale'); ?>>Scale</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="ross-admin-subsection">
                            <h4>🎨 Advanced Styling</h4>
                            
                            <div class="ross-field-group">
                                <label class="ross-field-label">Icon Color</label>
                                <input type="text" name="ross_theme_header_options[social_icon_color]" value="<?php echo esc_attr($get('social_icon_color', '#ffffff')); ?>" class="ross-color-input" data-default-color="#ffffff" />
                            </div>
                            
                            <div class="ross-field-group">
                                <label class="ross-field-label">Background Color</label>
                                <input type="text" name="ross_theme_header_options[social_icon_bg_color]" value="<?php echo esc_attr($get('social_icon_bg_color', 'transparent')); ?>" class="ross-color-input" data-default-color="transparent" />
                            </div>
                            
                            <div class="ross-field-group">
                                <label class="ross-field-label">Border Color</label>
                                <input type="text" name="ross_theme_header_options[social_icon_border_color]" value="<?php echo esc_attr($get('social_icon_border_color', 'transparent')); ?>" class="ross-color-input" data-default-color="transparent" />
                            </div>
                            
                            <div class="ross-field-group">
                                <label class="ross-field-label">Border Size</label>
                                <select name="ross_theme_header_options[social_icon_border_size]" class="ross-select">
                                    <option value="0" <?php selected($get('social_icon_border_size', '0'), '0'); ?>>No Border</option>
                                    <option value="1" <?php selected($get('social_icon_border_size', '0'), '1'); ?>>1px</option>
                                    <option value="2" <?php selected($get('social_icon_border_size', '0'), '2'); ?>>2px</option>
                                    <option value="3" <?php selected($get('social_icon_border_size', '0'), '3'); ?>>3px</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="ross-admin-notice" style="background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 6px; padding: 12px; margin-top: 16px;">
                            <p style="margin: 0; color: #0c4a6e;"><strong>💡 Important:</strong> After adding or modifying custom icons, click the "Save Header Settings" button at the bottom of the page. Your changes will not be saved until you click Save.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Colors & Style (announcement moved to its own tab) -->
            <div class="ross-topbar-right">
                <div class="ross-admin-section">
                    <h3>🎨 Colors</h3>
                    
                    <div class="ross-colors-grid">
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-bg" style="background-color: <?php echo esc_attr($get('topbar_bg_color', '#001946')); ?>;"></span>
                                <span class="ross-color-name">Top Bar Background</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_bg_color]" value="<?php echo esc_attr($get('topbar_bg_color', '#001946')); ?>" data-default-color="#001946" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-text" style="background-color: <?php echo esc_attr($get('topbar_text_color', '#ffffff')); ?>;"></span>
                                <span class="ross-color-name">Text Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_text_color]" value="<?php echo esc_attr($get('topbar_text_color', '#ffffff')); ?>" data-default-color="#ffffff" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-icon" style="background-color: <?php echo esc_attr($get('topbar_icon_color', '#E5C902')); ?>;"></span>
                                <span class="ross-color-name">Social Icon Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_icon_color]" value="<?php echo esc_attr($get('topbar_icon_color', '#E5C902')); ?>" data-default-color="#E5C902" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-icon-hover" style="background-color: <?php echo esc_attr($get('topbar_icon_hover_color', '#ffffff')); ?>;"></span>
                                <span class="ross-color-name">Icon Hover Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_icon_hover_color]" value="<?php echo esc_attr($get('topbar_icon_hover_color', '#ffffff')); ?>" data-default-color="#ffffff" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-border" style="background-color: <?php echo esc_attr($get('topbar_border_color', '#E5C902')); ?>;"></span>
                                <span class="ross-color-name">Border Bottom Color</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_border_color]" value="<?php echo esc_attr($get('topbar_border_color', '#E5C902')); ?>" data-default-color="#E5C902" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-grad1" style="background-color: <?php echo esc_attr($get('topbar_gradient_color1', '#001946')); ?>;"></span>
                                <span class="ross-color-name">Gradient Color 1</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_gradient_color1]" value="<?php echo esc_attr($get('topbar_gradient_color1', '#001946')); ?>" data-default-color="#001946" />
                        </div>
                        
                        <div class="ross-color-item">
                            <label class="ross-color-label">
                                <span class="ross-color-swatch" id="ross-swatch-grad2" style="background-color: <?php echo esc_attr($get('topbar_gradient_color2', '#003d7a')); ?>;"></span>
                                <span class="ross-color-name">Gradient Color 2</span>
                            </label>
                            <input type="text" class="ross-color-input" name="ross_theme_header_options[topbar_gradient_color2]" value="<?php echo esc_attr($get('topbar_gradient_color2', '#003d7a')); ?>" data-default-color="#003d7a" />
                        </div>
                    </div>
                </div>
                
                <div class="ross-admin-section">
                    <h3>⚡ Style Options</h3>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[topbar_gradient_enable]" value="1" <?php checked(1, $get('topbar_gradient_enable', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Gradient Background</span>
                        </label>
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-field-label">Border Bottom Width (px)</label>
                        <input type="number" min="0" max="5" name="ross_theme_header_options[topbar_border_width]" value="<?php echo esc_attr($get('topbar_border_width', 0)); ?>" class="ross-input ross-input-small" /> px
                    </div>
                    
                    <div class="ross-field-group">
                        <label class="ross-switch-label">
                            <input type="checkbox" name="ross_theme_header_options[topbar_shadow_enable]" value="1" <?php checked(1, $get('topbar_shadow_enable', 0)); ?> />
                            <span class="ross-switch"></span>
                            <span class="ross-label-text">Enable Drop Shadow</span>
                        </label>
                    </div>
                </div>
                
                <!-- Live Preview -->
                <div class="ross-admin-section">
                    <h3>👁️ Live Preview</h3>
                    <div id="ross-topbar-preview">
                        <div id="ross-topbar-preview-bar">
                                    <div id="ross-preview-left">Left Content</div>
                                    <div id="ross-preview-center">Announcement text appears here</div>
                                    <div id="ross-preview-right" class="ross-preview-social"></div>
                                </div>
                        <div class="ross-preview-note">Preview updates live as you change settings</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    (function(){
        // Live preview for social icons in Top Bar admin — robust selectors and delegation
        function buildPreviewIcons() {
            var container = document.querySelector('#ross-preview-right');
            if (!container) return;
            container.innerHTML = '';

            var adminRoot = document.querySelector('.ross-topbar-admin-improved') || document;

            var platforms = ['facebook','twitter','linkedin','instagram','youtube'];
            platforms.forEach(function(p){
                var urlInput = adminRoot.querySelector('input[name="ross_theme_header_options[social_' + p + ']"]');
                var iconInput = adminRoot.querySelector('input[name="ross_theme_header_options[social_' + p + '_icon]"]');
                var enabledInput = adminRoot.querySelector('input[name="ross_theme_header_options[social_' + p + '_enabled]"]');
                var url = urlInput ? urlInput.value.trim() : '';
                var icon = iconInput ? iconInput.value.trim() : '';
                var enabled = enabledInput ? enabledInput.checked : !!url;

                if (!enabled || !url) return;

                var a = document.createElement('a');
                a.href = url;
                a.target = '_blank';
                a.className = 'preview-social-link';

                if (icon) {
                    if (icon.indexOf('<') === 0) {
                        a.innerHTML = icon;
                    } else {
                        var i = document.createElement('i');
                        i.className = icon;
                        a.appendChild(i);
                    }
                } else {
                    a.textContent = p.charAt(0).toUpperCase();
                }

                container.appendChild(a);
            });

            // Custom icons: find url/icon pairs under topbar_custom_icon_links (supports numeric indexes)
            var customUrlFields = adminRoot.querySelectorAll('input[name^="ross_theme_header_options[topbar_custom_icon_links]"][name$="[url]"]');
            customUrlFields.forEach(function(urlField){
                // Determine corresponding icon field by replacing [url] with [icon] in the name
                var name = urlField.getAttribute('name');
                var iconName = name.replace('[url]','[icon]');
                var iconField = adminRoot.querySelector('input[name="' + iconName + '"]');
                var u = urlField.value.trim();
                var ic = iconField ? iconField.value.trim() : '';
                if (u && ic) {
                    var a = document.createElement('a');
                    a.href = u; a.target = '_blank'; a.className = 'preview-social-link';
                    if (ic.indexOf('<') === 0) {
                        a.innerHTML = ic;
                    } else {
                        var i = document.createElement('i'); i.className = ic; a.appendChild(i);
                    }
                    container.appendChild(a);
                }
            });

            // Apply size/shape classes from the options selects
            var size = adminRoot.querySelector('select[name="ross_theme_header_options[social_icon_size]"]');
            var shape = adminRoot.querySelector('select[name="ross_theme_header_options[social_icon_shape]"]');
            var sizeClass = size ? 'social-link--' + (size.value || 'medium') : 'social-link--medium';
            var shapeClass = shape ? 'social-link--' + (shape.value || 'circle') : 'social-link--circle';
            var links = container.querySelectorAll('a.preview-social-link');
            links.forEach(function(a){
                a.className = 'preview-social-link ' + sizeClass + ' ' + shapeClass;
            });
        }

        // Attach a delegated input/change listener to the admin container for live updates
        document.addEventListener('DOMContentLoaded', function(){
            buildPreviewIcons();
            
            // Add custom icon functionality - REMOVED: handled by jQuery in JS file
            
            var adminRoot = document.querySelector('.ross-topbar-admin-improved') || document;
            adminRoot.addEventListener('input', function(e){
                if (!e.target || !e.target.name) return;
                var n = e.target.name;
                if (n.indexOf('social_') !== -1 || n.indexOf('topbar_custom_icon_links') !== -1 || n.indexOf('social_icon_size') !== -1 || n.indexOf('social_icon_shape') !== -1) {
                    buildPreviewIcons();
                }
            });
            adminRoot.addEventListener('change', function(e){
                var n = e.target && e.target.name ? e.target.name : '';
                if (n.indexOf('social_') !== -1 || n.indexOf('topbar_custom_icon_links') !== -1 || n.indexOf('social_icon_size') !== -1 || n.indexOf('social_icon_shape') !== -1) {
                    buildPreviewIcons();
                }
            });
            
            // Handle remove custom icon buttons
            adminRoot.addEventListener('click', function(e){
                if (e.target && e.target.classList.contains('ross-remove-custom-icon')) {
                    e.preventDefault();
                    e.target.closest('.ross-custom-icon-item').remove();
                    buildPreviewIcons();
                }
            });
        });
        
    })();
    </script>

            <?php
}
