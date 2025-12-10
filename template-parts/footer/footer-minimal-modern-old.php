<?php
/**
 * Footer Template Part: Minimal Modern
 * Ultra-clean design for SaaS and tech startups
 */

$template_file = get_template_directory() . '/inc/features/footer/templates/minimal-modern.php';
$template_data = file_exists($template_file) ? include($template_file) : array();
$footer_options = get_option('ross_theme_footer_options', array());

$bg_color = $footer_options['styling_bg_color'] ?? $template_data['bg'] ?? '#fafafa';
$text_color = $footer_options['styling_text_color'] ?? $template_data['text'] ?? '#0b2140';
$accent_color = $footer_options['styling_link_color'] ?? $template_data['accent'] ?? '#0b66a6';
$columns = $template_data['columns'] ?? 1;
$show_widgets = ross_theme_should_show_footer_widgets();
?>

<footer class="site-footer footer-minimal-modern" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
    <div class="footer-main">
        <div class="<?php echo esc_attr($footer_options['footer_width'] === 'full' ? 'container-fluid' : 'container'); ?>">
            <div class="footer-content-centered">
                <?php if ($show_widgets): ?>
                    <?php if (is_active_sidebar('footer-1')): ?>
                        <div class="footer-widget-area">
                            <?php dynamic_sidebar('footer-1'); ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if (!empty($template_data['cols']) && is_array($template_data['cols'])): ?>
                        <?php foreach ($template_data['cols'] as $col): ?>
                            <div class="footer-section">
                                <?php if (!empty($col['title'])): ?>
                                    <h4 class="footer-section-title"><?php echo esc_html($col['title']); ?></h4>
                                <?php endif; ?>
                                
                                <?php if (!empty($col['items']) && is_array($col['items'])): ?>
                                    <ul class="footer-links-inline">
                                        <?php foreach ($col['items'] as $item): ?>
                                            <li><?php echo esc_html($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (ross_theme_should_show_social_icons()): ?>
                    <div class="footer-social-icons">
                        <?php ross_footer_social_icons(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-minimal-modern {
    padding: 40px 0;
    border-top: 1px solid rgba(0,0,0,0.05);
}
.footer-minimal-modern .footer-content-centered {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}
.footer-minimal-modern .footer-section-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0 0 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}
.footer-minimal-modern .footer-links-inline {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
}
.footer-minimal-modern .footer-links-inline li {
    font-size: 0.95rem;
}
.footer-minimal-modern .footer-social-icons {
    margin-top: 2rem;
}
@media (max-width: 768px) {
    .footer-minimal-modern .footer-links-inline {
        flex-direction: column;
        gap: 0.75rem;
    }
}
</style>
