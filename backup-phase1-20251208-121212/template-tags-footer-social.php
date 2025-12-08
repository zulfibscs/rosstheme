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
        
        // Get display order (fallback to all known platforms + custom)
        $display_order = isset( $footer_options['social_display_order'] ) && is_array( $footer_options['social_display_order'] ) 
            ? $footer_options['social_display_order'] 
            : array('facebook', 'instagram', 'twitter', 'linkedin', 'custom');

        // Legacy URLs support (old option keys)
        $legacy_urls = array(
            'facebook'  => $footer_options['facebook_url']    ?? ($footer_options['social_facebook']  ?? ''),
            'instagram' => $footer_options['instagram_url']   ?? ($footer_options['social_instagram'] ?? ''),
            'twitter'   => $footer_options['twitter_url']     ?? ($footer_options['social_twitter']   ?? ''),
            'linkedin'  => $footer_options['linkedin_url']    ?? ($footer_options['social_linkedin']  ?? ''),
            // Legacy + extended platforms (YouTube / Pinterest) mapped to Font Awesome icons
            'youtube'   => $footer_options['youtube_url']     ?? ($footer_options['social_youtube']   ?? ''),
            'pinterest' => $footer_options['pinterest_url']   ?? ($footer_options['social_pinterest'] ?? ''),
        );

        // Optional per-platform enable flags (default to on if missing)
        $legacy_enabled = array(
            'youtube'   => isset( $footer_options['youtube_enabled'] )   ? $footer_options['youtube_enabled']   : 1,
            'pinterest' => isset( $footer_options['pinterest_enabled'] ) ? $footer_options['pinterest_enabled'] : 1,
        );

        // Collect Customizer repeater icons (JSON)
        $json_icons = array();
        if ( ! empty( $footer_options['social_icons_json'] ) ) {
            $decoded = json_decode( $footer_options['social_icons_json'], true );
            if ( is_array( $decoded ) ) {
                $json_icons = $decoded;
            }
        }

        // If legacy platforms exist but are missing from display order, append them
        foreach ($legacy_urls as $plat => $url) {
            if (!empty($url) && !in_array($plat, $display_order, true)) {
                $display_order[] = $plat;
            }
        }

        // If Customizer repeater is present, prefer its order first
        if ( ! empty( $json_icons ) ) {
            $custom_order = array();
            $known_platforms = array('facebook','instagram','twitter','linkedin','youtube','pinterest','tiktok','telegram','whatsapp','github');
            foreach ( $json_icons as $idx => $item ) {
                $icon_class = isset( $item['icon'] ) ? strtolower( $item['icon'] ) : '';
                $platform = '';
                foreach ( $known_platforms as $plat ) {
                    if ( false !== strpos( $icon_class, $plat ) || ( 'twitter' === $plat && false !== strpos( $icon_class, 'x-' ) ) ) {
                        $platform = $plat;
                        break;
                    }
                }
                if ( empty( $platform ) ) {
                    $platform = ! empty( $item['id'] ) ? sanitize_title( $item['id'] ) : 'custom-' . $idx;
                }
                $custom_order[] = $platform;
            }
            if ( ! empty( $custom_order ) ) {
                $display_order = array_values( array_unique( array_merge( $custom_order, $display_order ) ) );
            }
        }

        // Collect enabled platforms
        $social_links = array();
        
        foreach ( $display_order as $platform_key ) {
            // Handle core platforms
            if ( isset( $core_platforms[$platform_key] ) ) {
                $is_enabled = isset( $footer_options[$platform_key . '_enabled'] ) ? $footer_options[$platform_key . '_enabled'] : 1;
                $url = isset( $footer_options[$platform_key . '_url'] ) ? $footer_options[$platform_key . '_url'] : '';
                // Legacy fallback if empty
                if (empty($url) && !empty($legacy_urls[$platform_key])) {
                    $url = $legacy_urls[$platform_key];
                }
                
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
            // Legacy extra platforms (YouTube / Pinterest) handled generically
            elseif ( isset($legacy_urls[$platform_key]) && !empty($legacy_urls[$platform_key]) && (!isset($legacy_enabled[$platform_key]) || $legacy_enabled[$platform_key]) ) {
                $social_links[] = array(
                    'platform' => $platform_key,
                    'url' => $legacy_urls[$platform_key],
                    'icon' => 'fab fa-' . $platform_key,
                    'label' => ucfirst($platform_key)
                );
            }
        }

        if ( ! empty( $json_icons ) ) {
            $existing_platforms = wp_list_pluck( $social_links, 'platform' );
            $known_platforms = array('facebook','instagram','twitter','linkedin','youtube','pinterest','tiktok','telegram','whatsapp','github');

            foreach ( $json_icons as $idx => $item ) {
                $icon_class = isset( $item['icon'] ) ? $item['icon'] : '';
                $url        = isset( $item['url'] ) ? $item['url'] : '';

                if ( empty( $icon_class ) || empty( $url ) ) {
                    continue;
                }

                $icon_lc  = strtolower( $icon_class );
                $platform = '';
                foreach ( $known_platforms as $plat ) {
                    if ( false !== strpos( $icon_lc, $plat ) || ( 'twitter' === $plat && false !== strpos( $icon_lc, 'x-' ) ) ) {
                        $platform = $plat;
                        break;
                    }
                }

                if ( empty( $platform ) ) {
                    $platform = ! empty( $item['id'] ) ? sanitize_title( $item['id'] ) : 'custom-' . $idx;
                }

                if ( in_array( $platform, $existing_platforms, true ) ) {
                    continue;
                }

                $label = ! empty( $item['aria_label'] ) ? $item['aria_label'] : ucfirst( $platform );

                $social_links[] = array(
                    'platform' => $platform,
                    'url'      => $url,
                    'icon'     => $icon_class,
                    'label'    => $label,
                );
                $existing_platforms[] = $platform;
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

        // Inline CSS variables for size/color to ensure custom styles always apply
        $base_inline = '--social-size: ' . esc_attr( $size ) . 'px;';
        
        // Build CSS classes
        $container_class = 'footer-social ross-social-icons';
        $icon_class = 'social-icon social-icon--' . esc_attr( $style );
        
        ?>
        <div class="<?php echo esc_attr( $container_class ); ?>" data-style="<?php echo esc_attr( $style ); ?>">
            <?php foreach ( $social_links as $link ): 
                $inline_style = $base_inline;
                if ( $link['platform'] === 'custom' ) {
                    $inline_style .= '--social-color: ' . esc_attr( $custom_color ) . ';';
                } elseif ( ! empty( $color ) ) {
                    $inline_style .= '--social-color: ' . esc_attr( $color ) . ';';
                }
                if ( ! empty( $hover_color ) ) {
                    $inline_style .= '--social-hover: ' . esc_attr( $hover_color ) . ';';
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

/**
 * Alias function for backward compatibility
 * Renders footer social media icons
 * 
 * @since 1.0.0
 */
if (!function_exists('ross_footer_social_icons')) {
    function ross_footer_social_icons() {
        rosstheme_render_footer_social();
    }
}