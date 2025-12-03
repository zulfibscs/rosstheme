<?php
/**
 * Theme template helper: rosstheme_render_footer_social
 */
if ( ! function_exists( 'rosstheme_render_footer_social' ) ) {
    function rosstheme_render_footer_social() {
        $enabled = get_theme_mod( 'footer_social_enable', null );
        if ( null === $enabled ) {
            // no theme mod defined; fall back to option
            $footer_options = get_option( 'ross_theme_footer_options', array() );
            $enabled = isset( $footer_options['enable_social_icons'] ) ? $footer_options['enable_social_icons'] : true;
        }
        if ( ! $enabled ) {
            return;
        }
        $raw = get_theme_mod( 'footer_social_icons', '[]' );
        $items = json_decode( $raw, true );
        if ( ! is_array( $items ) || empty( $items ) ) {
            // No theme_mod icons — try options array stored under ross_theme_footer_options
            $footer_options = get_option( 'ross_theme_footer_options', array() );
            // Try legacy entries
            $items = array();
            if ( ! empty( $footer_options['facebook_url'] ) ) $items[] = array( 'icon' => 'facebook', 'url' => $footer_options['facebook_url'] );
            if ( ! empty( $footer_options['linkedin_url'] ) ) $items[] = array( 'icon' => 'linkedin', 'url' => $footer_options['linkedin_url'] );
            if ( ! empty( $footer_options['instagram_url'] ) ) $items[] = array( 'icon' => 'instagram', 'url' => $footer_options['instagram_url'] );
            if ( empty( $items ) ) return; // nothing to render
        }

        echo '<div class="footer-social">';
        foreach ( $items as $it ) {
            $url = isset( $it['url'] ) ? esc_url( $it['url'] ) : '#';
            $text = isset( $it['icon'] ) ? esc_html( ucfirst( $it['icon'] ) ) : 'Social';
            $target = ! empty( $it['new_tab'] ) ? ' target="_blank" rel="noopener noreferrer"' : '';
            echo '<a href="' . $url . '" class="social-icon"' . $target . '>' . $text . '</a>';
        }
        echo '</div>';
    }
}
