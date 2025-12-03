<?php
/**
 * Enhanced Footer Social Icons Rendering (V2 - 4 Core + Custom)
 */
if ( ! function_exists( 'rosstheme_render_footer_social' ) ) {
    function rosstheme_render_footer_social() {
        $footer_options = get_option( 'ross_theme_footer_options', array() );
        
        // Check if social icons are enabled
        $enabled = isset( $footer_options['enable_social_icons'] ) ? $footer_options['enable_social_icons'] : true;
        if ( ! $enabled ) {
            return;
        }
        
        // Core 4 platforms with Font Awesome icons
        $core_platforms = array(
            'facebook' => array('icon' => 'fab fa-facebook-f', 'label' => 'Facebook'),
            'instagram' => array('icon' => 'fab fa-instagram', 'label' => 'Instagram'),
            'twitter' => array('icon' => 'fab fa-twitter', 'label' => 'Twitter'),
            'linkedin' => array('icon' => 'fab fa-linkedin-in', 'label' => 'LinkedIn'),
        );
        
        // Get display order
        $display_order = isset( $footer_options['social_display_order'] ) && is_array( $footer_options['social_display_order'] ) 
            ? $footer_options['social_display_order'] 
            : array('facebook', 'instagram', 'twitter', 'linkedin', 'custom');
        
        // Collect enabled platforms
        $social_links = array();
        
        foreach ( $display_order as $platform_key ) {
            // Handle core platforms
            if ( isset( $core_platforms[$platform_key] ) ) {
                $is_enabled = isset( $footer_options[$platform_key . '_enabled'] ) ? $footer_options[$platform_key . '_enabled'] : 1;
                $url = isset( $footer_options[$platform_key . '_url'] ) ? $footer_options[$platform_key . '_url'] : '';
                
                if ( $is_enabled && ! empty( $url ) ) {
                    $social_links[] = array(
                        'platform' => $platform_key,
                        'url' => $url,
                        'icon' => $core_platforms[$platform_key]['icon'],
                        'label' => $core_platforms[$platform_key]['label']
                    );
                }
            }
            // Handle custom platform
            elseif ( $platform_key === 'custom' ) {
                $is_enabled = isset( $footer_options['custom_social_enabled'] ) ? $footer_options['custom_social_enabled'] : 0;
                $url = isset( $footer_options['custom_social_url'] ) ? $footer_options['custom_social_url'] : '';
                $label = isset( $footer_options['custom_social_label'] ) ? $footer_options['custom_social_label'] : 'Custom';
                $icon = isset( $footer_options['custom_social_icon'] ) ? $footer_options['custom_social_icon'] : 'fas fa-link';
                
                if ( $is_enabled && ! empty( $url ) ) {
                    $social_links[] = array(
                        'platform' => 'custom',
                        'url' => $url,
                        'icon' => $icon,
                        'label' => $label
                    );
                }
            }
        }
        
        // If no links, don't render anything
        if ( empty( $social_links ) ) {
            return;
        }
        
        // Get styling options
        $style = isset( $footer_options['social_icon_style'] ) ? $footer_options['social_icon_style'] : 'circle';
        $size = isset( $footer_options['social_icon_size'] ) ? intval( $footer_options['social_icon_size'] ) : 36;
        $color = isset( $footer_options['social_icon_color'] ) ? $footer_options['social_icon_color'] : '';
        $hover_color = isset( $footer_options['social_icon_hover_color'] ) ? $footer_options['social_icon_hover_color'] : '';
        $custom_color = isset( $footer_options['custom_social_color'] ) ? $footer_options['custom_social_color'] : '#666666';
        
        // Build CSS classes
        $container_class = 'footer-social ross-social-icons';
        $icon_class = 'social-icon social-icon--' . esc_attr( $style );
        
        ?>
        <div class="<?php echo esc_attr( $container_class ); ?>" data-style="<?php echo esc_attr( $style ); ?>">
            <?php foreach ( $social_links as $link ): 
                $inline_style = '';
                if ( ! empty( $color ) || $link['platform'] === 'custom' ) {
                    $inline_style = '--social-size: ' . esc_attr( $size ) . 'px;';
                    if ( $link['platform'] === 'custom' ) {
                        $inline_style .= '--social-color: ' . esc_attr( $custom_color ) . ';';
                    } elseif ( ! empty( $color ) ) {
                        $inline_style .= '--social-color: ' . esc_attr( $color ) . ';';
                    }
                    if ( ! empty( $hover_color ) ) {
                        $inline_style .= '--social-hover: ' . esc_attr( $hover_color ) . ';';
                    }
                }
            ?>
                <a 
                    href="<?php echo esc_url( $link['url'] ); ?>" 
                    class="<?php echo esc_attr( $icon_class ); ?>" 
                    target="_blank" 
                    rel="noopener noreferrer"
                    aria-label="<?php echo esc_attr( $link['label'] ); ?>"
                    data-platform="<?php echo esc_attr( $link['platform'] ); ?>"
                    <?php if ( ! empty( $inline_style ) ): ?>style="<?php echo $inline_style; ?>"<?php endif; ?>
                >
                    <i class="<?php echo esc_attr( $link['icon'] ); ?>"></i>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
