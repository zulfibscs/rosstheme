<?php
/**
 * Customizer controls for Footer Social Icons
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'customize_register', function( WP_Customize_Manager $wp_customize ) {
    // Minimal registration for the example; comprehensive settings should be inserted as needed.
    $wp_customize->add_panel( 'footer_social_panel', array(
        'title' => __( 'Footer → Social Icons', 'rosstheme' ),
        'priority' => 160,
    ) );
    $wp_customize->add_section( 'footer_social_general', array('title' => __( 'General', 'rosstheme' ), 'panel' => 'footer_social_panel', 'priority' => 10));
    $wp_customize->add_setting( 'footer_social_enable', array('default' => true, 'sanitize_callback' => 'rosstheme_sanitize_checkbox', 'transport' => 'postMessage') );
    $wp_customize->add_control( 'footer_social_enable', array('label' => __( 'Enable Social Icons', 'rosstheme' ), 'section' => 'footer_social_general', 'type' => 'checkbox') );

    // JSON list setting
    $wp_customize->add_setting( 'footer_social_icons', array('default' => json_encode(array()), 'sanitize_callback' => 'rosstheme_sanitize_icon_list', 'transport' => 'postMessage') );
    if ( class_exists( 'WP_Customize_Control' ) ) {
        // Register custom repeater control if not already loaded
        if ( ! class_exists( 'Rosstheme_Footer_Social_Repeater_Control' ) ) {
            class Rosstheme_Footer_Social_Repeater_Control extends WP_Customize_Control {
                public $type = 'rosstheme_repeater';
                public function enqueue() {
                    $admin_js = get_template_directory() . '/assets/js/admin/footer-social-customizer.js';
                    if ( file_exists( $admin_js ) ) {
                        wp_enqueue_script( 'ross-footer-social-admin', get_template_directory_uri() . '/assets/js/admin/footer-social-customizer.js', array( 'jquery', 'customize-controls', 'wp-util', 'jquery-ui-sortable' ), filemtime( $admin_js ), true );
                        wp_enqueue_media();
                    }
                    $admin_css = get_template_directory() . '/assets/css/admin/footer-social-customizer.css';
                    if ( file_exists( $admin_css ) ) {
                        wp_enqueue_style( 'ross-footer-social-admin-css', get_template_directory_uri() . '/assets/css/admin/footer-social-customizer.css', array(), filemtime( $admin_css ) );
                    }
                }
                public function render_content() {
                    ?>
                    <label>
                        <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    </label>
                    <div class="rosstheme-repeater-wrap" role="region" aria-label="Social icons repeater" data-setting-id="<?php echo esc_attr( $this->id ); ?>">
                        <div class="rosstheme-repeater-list"></div>
                        <p>
                            <button type="button" class="button rosstheme-repeater-add" aria-label="Add social icon"><?php esc_html_e( 'Add Icon', 'rosstheme' ); ?></button>
                        </p>
                    </div>
                    <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
                    <script type="text/html" id="tmpl-rosstheme-repeater-item">
                        <div class="rosstheme-repeater-item" data-id="{{ data.id }}" role="listitem">
                            <span class="rosstheme-drag-handle" title="Drag to reorder" role="button" tabindex="0" aria-label="Drag to reorder">☰</span>
                            <# if ( data.previewHtml ) { #>
                                <span class="rosstheme-list-preview">{{{ data.previewHtml }}}</span>
                            <# } else { #>
                                <span class="rosstheme-list-preview rosstheme-list-preview-empty"></span>
                            <# } #>
                            <div class="rosstheme-repeater-item-main" role="group"><strong>{{ data.iconLabel }}</strong> — <small>{{ data.urlLabel }}</small></div>
                            <div class="rosstheme-repeater-item-actions">
                                <button class="button rosstheme-repeater-edit" aria-label="Edit social item"><?php esc_html_e( 'Edit', 'rosstheme' ); ?></button>
                                <button class="button rosstheme-repeater-remove" aria-label="Remove social item"><?php esc_html_e( 'Remove', 'rosstheme' ); ?></button>
                                <button class="button rosstheme-repeater-up" aria-label="Move up">▲</button>
                                <button class="button rosstheme-repeater-down" aria-label="Move down">▼</button>
                            </div>
                        </div>
                    </script>
                    <script type="text/html" id="tmpl-rosstheme-repeater-modal">
                        <div class="rosstheme-repeater-modal" role="dialog" aria-modal="true" aria-labelledby="rosstheme-repeater-title">
                            <div class="rosstheme-repeater-modal-inner">
                                <div class="rosstheme-modal-left">
                                    <p><label><?php esc_html_e( 'Icon (class or name):', 'rosstheme' ); ?><br><input class="rosstheme-field-icon widefat" type="text" value="{{ data.icon }}"/></label></p>
                                    <p><label><?php esc_html_e( 'URL:', 'rosstheme' ); ?><br><input class="rosstheme-field-url widefat" type="text" value="{{ data.url }}"/></label></p>
                                    <p><label><input class="rosstheme-field-newtab" type="checkbox" {{ data.new_tab ? 'checked' : '' }}/> <?php esc_html_e( 'Open in new tab', 'rosstheme' ); ?></label></p>
                                    <p><label><?php esc_html_e( 'ARIA label:', 'rosstheme' ); ?><br><input class="rosstheme-field-aria widefat" type="text" value="{{ data.aria_label }}"/></label></p>
                                    <p><label><?php esc_html_e( 'Tooltip:', 'rosstheme' ); ?><br><input class="rosstheme-field-tooltip widefat" type="text" value="{{ data.tooltip }}"/></label></p>
                                    <p><button class="button rosstheme-upload-svg"><?php esc_html_e( 'Upload/select SVG', 'rosstheme' ); ?></button> <span class="rosstheme-upload-info">{{ data.svg_attachment_id }}</span></p>
                                </div>
                                <div class="rosstheme-modal-right">
                                    <div class="rosstheme-preview" aria-hidden="true"></div>
                                </div>
                            </div>
                            <p class="rosstheme-modal-actions"><button class="button primary rosstheme-save"><?php esc_html_e( 'Save', 'rosstheme' ); ?></button> <button class="button rosstheme-cancel"><?php esc_html_e( 'Cancel', 'rosstheme' ); ?></button></p>
                        </div>
                    </script>
                    <?php
                }
            }
        }
        $wp_customize->add_control( new Rosstheme_Footer_Social_Repeater_Control( $wp_customize, 'footer_social_icons', array('label' => __( 'Social icons', 'rosstheme' ), 'section' => 'footer_social_general', 'settings' => 'footer_social_icons') ) );
    }
} );

add_action( 'customize_preview_init', function() {
    $preview_js = get_template_directory() . '/assets/js/customize-preview.js';
    if ( file_exists( $preview_js ) ) {
        wp_enqueue_script( 'ross-footer-social-preview', get_template_directory_uri() . '/assets/js/customize-preview.js', array( 'customize-preview', 'jquery' ), filemtime( $preview_js ), true );
    }
} );

/**
 * Sync customizer settings to legacy footer option array when Customizer is saved.
 */
add_action( 'customize_save_after', function( $wp_customize ) {
    // Read current theme_mods
    $icons_raw = get_theme_mod( 'footer_social_icons', '[]' );
    $enabled = get_theme_mod( 'footer_social_enable', null );
    // normalise
    $icons = json_decode( $icons_raw, true );
    if ( ! is_array( $icons ) ) $icons = array();

    $footer_opts = get_option( 'ross_theme_footer_options', array() );
    // Reset known platforms before remapping
    $known = array('facebook','instagram','twitter','linkedin','youtube','pinterest');
    foreach ( $known as $plat ) {
        $footer_opts[ $plat . '_url' ] = $footer_opts[ $plat . '_url' ] ?? '';
        $footer_opts[ $plat . '_enabled' ] = $footer_opts[ $plat . '_enabled' ] ?? 0;
    }

    // map common social URLs to option keys + build display order
    $order = array();
    foreach ( $icons as $it ) {
        $icon = isset( $it['icon'] ) ? strtolower( $it['icon'] ) : '';
        $url = isset( $it['url'] ) ? esc_url_raw( $it['url'] ) : '';
        if ( empty( $icon ) || empty( $url ) ) {
            continue;
        }

        $platform = '';
        foreach ( $known as $plat ) {
            if ( false !== strpos( $icon, $plat ) || ( 'twitter' === $plat && false !== strpos( $icon, 'x-' ) ) ) {
                $platform = $plat;
                break;
            }
        }

        if ( $platform ) {
            $footer_opts[ $platform . '_url' ] = $url;
            $footer_opts[ $platform . '_enabled' ] = 1;
            if ( ! in_array( $platform, $order, true ) ) {
                $order[] = $platform;
            }
        }
    }

    if ( ! empty( $order ) ) {
        $footer_opts['social_display_order'] = $order;
    }
    // also save the raw icons JSON under a key for future use
    $footer_opts['social_icons_json'] = json_encode( $icons );
    if ( null !== $enabled ) $footer_opts['enable_social_icons'] = (bool) $enabled;
    update_option( 'ross_theme_footer_options', $footer_opts );
} );

// Simplified sanitizers (re-using stubs from theme)
if ( ! function_exists( 'rosstheme_sanitize_checkbox' ) ) {
    function rosstheme_sanitize_checkbox( $val ) { return (bool) $val; }
}
if ( ! function_exists( 'rosstheme_sanitize_icon_list' ) ) {
    function rosstheme_sanitize_icon_list( $val ) { $decoded = json_decode( $val, true ); return json_encode( is_array($decoded) ? $decoded : array() ); }
}
