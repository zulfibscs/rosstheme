<?php
/**
 * Footer Copyright Bar
 * Displays copyright text with full styling control
 * Also handles custom footer HTML/CSS/JS output
 */

$footer_options = ross_get_footer_options();
$show_copyright = isset($footer_options['enable_copyright']) ? (bool) $footer_options['enable_copyright'] : true;
$show_custom_footer = !empty($footer_options['enable_custom_footer']);

// If neither copyright nor custom footer is enabled, don't render anything
if (!$show_copyright && !$show_custom_footer) {
    return;
}

// Get copyright settings
$copyright_text = ross_theme_get_copyright_text();
$copyright_bg = $footer_options['copyright_bg'] ?? $footer_options['copyright_bg_color'] ?? '#000000';
$copyright_text_color = $footer_options['copyright_text_color'] ?? '#ffffff';
$copyright_alignment = $footer_options['copyright_alignment'] ?? 'center';
$container_width = $footer_options['footer_width'] ?? 'boxed';

// Render copyright bar if enabled
if ($show_copyright) {
?>
<div class="footer-copyright" style="background-color: <?php echo esc_attr($copyright_bg); ?>; color: <?php echo esc_attr($copyright_text_color); ?>; text-align: <?php echo esc_attr($copyright_alignment); ?>;">
    <div class="<?php echo esc_attr($container_width === 'full' ? 'container-fluid' : 'container'); ?>">
        <div class="copyright-content">
            <?php echo wp_kses_post($copyright_text); ?>
        </div>
        
    </div>
</div>
<?php
}
?>
