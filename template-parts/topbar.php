<?php
/**
 * Top Bar Template
 * Displays the top bar with social icons, phone number, and custom content
 */

// Get header options
$options = get_option('ross_theme_header_options', array());
$enable_topbar = isset($options['enable_topbar']) ? $options['enable_topbar'] : 0;

if (!$enable_topbar) return;

// Helper function to get option with default
$get = function($key, $default = '') use ($options) {
    return isset($options[$key]) ? $options[$key] : $default;
};

// Top bar settings
$bg_color = $get('topbar_bg_color', '#001946');
$text_color = $get('topbar_text_color', '#ffffff');
$border_color = $get('topbar_border_color', '#E5C902');
$border_width = $get('topbar_border_width', 0);

// Social icons settings
$enable_social = $get('enable_social', 0);
$social_icon_size = $get('social_icon_size', 'medium');
$social_icon_shape = $get('social_icon_shape', 'circle');
$social_icon_color = $get('social_icon_color', '#ffffff');
$social_icon_bg_color = $get('social_icon_bg_color', 'transparent');
$social_icon_bg_hover_color = $get('social_icon_bg_hover_color', '');
$social_icon_border_color = $get('social_icon_border_color', 'transparent');
$social_icon_border_size = $get('social_icon_border_size', '0');
$social_icon_hover_color = $get('social_icon_hover_color', '#E5C902');
$social_icon_effect = $get('social_icon_effect', 'none');
$social_icon_width = $get('social_icon_width', '32');

// Left content settings
$enable_left = $get('enable_topbar_left', 1);
$left_content = $get('topbar_left_content', '');
$phone_number = $get('phone_number', '');

// Custom icon links
$custom_icons = $get('topbar_custom_icon_links', array());
if (!is_array($custom_icons)) $custom_icons = array();

// Build CSS classes
$topbar_classes = array('site-topbar');
$social_classes = array('topbar-social-links');

if ($social_icon_size) $social_classes[] = 'social-link--' . $social_icon_size;
if ($social_icon_shape) $social_classes[] = 'social-link--' . $social_icon_shape;
if ($social_icon_effect && $social_icon_effect !== 'none') $social_classes[] = 'social-link--' . $social_icon_effect;

// Inline styles
$topbar_styles = array();
if ($bg_color) $topbar_styles[] = "background-color: {$bg_color}";
if ($text_color) $topbar_styles[] = "color: {$text_color}";
if ($border_width > 0 && $border_color) $topbar_styles[] = "border-bottom: {$border_width}px solid {$border_color}";

$social_styles = array();
if ($social_icon_color) $social_styles[] = "--social-icon-color: {$social_icon_color}";
if ($social_icon_bg_color) $social_styles[] = "--social-bg-color: {$social_icon_bg_color}";
if ($social_icon_bg_hover_color) $social_styles[] = "--social-bg-hover-color: {$social_icon_bg_hover_color}";
if ($social_icon_border_color) $social_styles[] = "--social-border-color: {$social_icon_border_color}";
if ($social_icon_border_size) $social_styles[] = "--social-border-size: {$social_icon_border_size}px";
if ($social_icon_hover_color) $social_styles[] = "--social-hover-color: {$social_icon_hover_color}";
if ($social_icon_width) $social_styles[] = "--social-icon-width: {$social_icon_width}px";
?>

<div class="<?php echo esc_attr(implode(' ', $topbar_classes)); ?>" style="<?php echo esc_attr(implode('; ', $topbar_styles)); ?>">
    <div class="topbar-inner">
        <!-- Left Section -->
        <?php if ($enable_left): ?>
            <div class="topbar-left">
                <?php if ($left_content): ?>
                    <div class="topbar-left-content">
                        <?php echo wp_kses_post($left_content); ?>
                    </div>
                <?php endif; ?>

                <?php if ($phone_number): ?>
                    <div class="topbar-phone">
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone_number)); ?>" class="topbar-phone-link" style="color: <?php echo esc_attr($text_color); ?>;">
                            <i class="fas fa-phone" style="margin-right: 8px;"></i>
                            <?php echo esc_html($phone_number); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Right Section - Social Icons -->
        <?php if ($enable_social || !empty($custom_icons)): ?>
            <div class="topbar-right">
                <div class="<?php echo esc_attr(implode(' ', $social_classes)); ?>" style="<?php echo esc_attr(implode('; ', $social_styles)); ?>">
                    <?php
                    // Standard social platforms
                    $platforms = array(
                        'facebook' => array('icon' => 'fab fa-facebook-f', 'url' => $get('social_facebook')),
                        'twitter' => array('icon' => 'fab fa-twitter', 'url' => $get('social_twitter')),
                        'linkedin' => array('icon' => 'fab fa-linkedin-in', 'url' => $get('social_linkedin')),
                        'instagram' => array('icon' => 'fab fa-instagram', 'url' => $get('social_instagram')),
                        'youtube' => array('icon' => 'fab fa-youtube', 'url' => $get('social_youtube'))
                    );

                    foreach ($platforms as $platform => $data) {
                        $url = $data['url'];
                        $icon = $get('social_' . $platform . '_icon', $data['icon']);
                        $enabled = $get('social_' . $platform . '_enabled', !empty($url));

                        if ($enabled && $url) {
                            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" class="social-link social-link--' . esc_attr($platform) . '" title="' . esc_attr(ucfirst($platform)) . '">';
                            echo '<i class="' . esc_attr($icon) . '"></i>';
                            echo '</a>';
                        }
                    }

                    // Custom icons
                    foreach ($custom_icons as $icon_data) {
                        if (!is_array($icon_data) || empty($icon_data['url']) || empty($icon_data['icon'])) continue;

                        $url = $icon_data['url'];
                        $icon = $icon_data['icon'];
                        $title = isset($icon_data['title']) ? $icon_data['title'] : '';
                        $enabled = isset($icon_data['enabled']) ? $icon_data['enabled'] : true;

                        if ($enabled) {
                            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" class="social-link social-link--custom" title="' . esc_attr($title) . '">';
                            if (strpos($icon, '<') === 0) {
                                echo wp_kses_post($icon);
                            } else {
                                echo '<i class="' . esc_attr($icon) . '"></i>';
                            }
                            echo '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>