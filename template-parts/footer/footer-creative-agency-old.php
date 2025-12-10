<?php
/**
 * Footer Template Part: Creative Agency
 * Bold dark design for creative studios and agencies
 */

$template_file = get_template_directory() . '/inc/features/footer/templates/creative-agency.php';
$template_data = file_exists($template_file) ? include($template_file) : array();
$footer_options = get_option('ross_theme_footer_options', array());

// Get colors from saved options or template defaults
$bg_color = $footer_options['styling_bg_color'] ?? $template_data['bg'] ?? '#0c0c0d';
$text_color = $footer_options['styling_text_color'] ?? $template_data['text'] ?? '#f3f4f6';
$accent_color = $footer_options['styling_link_color'] ?? $template_data['accent'] ?? '#E5C902';
$columns = $template_data['columns'] ?? 4;
$show_widgets = ross_theme_should_show_footer_widgets();
?>

<footer class="site-footer footer-creative-agency" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
    <div class="footer-main">
        <div class="<?php echo esc_attr($footer_options['footer_width'] === 'full' ? 'container-fluid' : 'container'); ?>">
            <div class="footer-columns footer-columns-<?php echo esc_attr($columns); ?>">
                <?php if ($show_widgets): ?>
                    <?php for ($i = 1; $i <= $columns; $i++): ?>
                        <?php if (is_active_sidebar('footer-' . $i)): ?>
                            <div class="footer-column footer-widget-area">
                                <?php dynamic_sidebar('footer-' . $i); ?>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                <?php else: ?>
                    <?php if (!empty($template_data['cols']) && is_array($template_data['cols'])): ?>
                        <?php foreach ($template_data['cols'] as $col): ?>
                            <div class="footer-column">
                                <?php if (!empty($col['title'])): ?>
                                    <h4 class="footer-column-title"><?php echo esc_html($col['title']); ?></h4>
                                <?php endif; ?>
                                
                                <?php if (!empty($col['html'])): ?>
                                    <div class="footer-column-content">
                                        <?php echo wp_kses_post($col['html']); ?>
                                    </div>
                                <?php elseif (!empty($col['items']) && is_array($col['items'])): ?>
                                    <ul class="footer-column-list">
                                        <?php foreach ($col['items'] as $item): ?>
                                            <li><?php echo esc_html($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <?php if (ross_theme_should_show_social_icons()): ?>
                <div class="footer-social-icons">
                    <?php ross_footer_social_icons(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</footer>

<style>
.footer-creative-agency {
    padding: 60px 0 40px;
}
.footer-creative-agency .footer-columns {
    display: grid;
    gap: 40px;
    margin-bottom: 40px;
}
.footer-creative-agency .footer-columns-1 { grid-template-columns: 1fr; }
.footer-creative-agency .footer-columns-2 { grid-template-columns: repeat(2, 1fr); }
.footer-creative-agency .footer-columns-3 { grid-template-columns: repeat(3, 1fr); }
.footer-creative-agency .footer-columns-4 { grid-template-columns: repeat(4, 1fr); }
.footer-creative-agency .footer-column-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 1.5rem;
    color: inherit;
}
.footer-creative-agency .footer-column-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-creative-agency .footer-column-list li {
    margin-bottom: 0.75rem;
    opacity: 0.8;
}
.footer-creative-agency .footer-column-content {
    opacity: 0.7;
    line-height: 1.6;
}
.footer-creative-agency .footer-social-icons {
    text-align: center;
    padding-top: 40px;
    border-top: 1px solid rgba(255,255,255,0.1);
}
@media (max-width: 768px) {
    .footer-creative-agency .footer-columns-2,
    .footer-creative-agency .footer-columns-3,
    .footer-creative-agency .footer-columns-4 {
        grid-template-columns: 1fr;
    }
}
</style>
