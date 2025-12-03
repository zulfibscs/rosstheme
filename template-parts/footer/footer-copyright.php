<?php
/**
 * Footer Copyright Bar
 * Displays copyright text and optional links
 */

$footer_options = get_option('ross_theme_footer_options', array());

// Check if copyright should be displayed
if (!ross_theme_should_show_copyright()) {
    return;
}

$copyright_text = $footer_options['copyright_text'] ?? '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.';
$copyright_bg = $footer_options['copyright_bg'] ?? '#000000';
$copyright_text_color = $footer_options['copyright_text_color'] ?? '#ffffff';
$copyright_alignment = $footer_options['copyright_alignment'] ?? 'center';
?>

<div class="footer-copyright" style="background-color: <?php echo esc_attr($copyright_bg); ?>; color: <?php echo esc_attr($copyright_text_color); ?>; text-align: <?php echo esc_attr($copyright_alignment); ?>;">
    <div class="<?php echo esc_attr($footer_options['footer_width'] === 'full' ? 'container-fluid' : 'container'); ?>">
        <div class="copyright-content">
            <?php echo wp_kses_post($copyright_text); ?>
        </div>
    </div>
</div>

<style>
.footer-copyright {
    padding: 20px 0;
    font-size: 0.9rem;
}
.footer-copyright .copyright-content {
    opacity: 0.85;
}
</style>
