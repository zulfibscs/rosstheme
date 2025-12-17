<?php
/**
 * Footer Template: Minimal Modern
 * Ultra-clean SaaS design - 100% dynamic with admin options
 */

$template_file = get_template_directory() . '/inc/features/footer/templates/minimal-modern.php';
$template_data = file_exists($template_file) ? include($template_file) : array();
$footer_options = ross_get_footer_options();

// Dynamic styling from admin
$bg_color = !empty($footer_options['styling_bg_color']) ? $footer_options['styling_bg_color'] : ($template_data['bg'] ?? '#fafafa');
$text_color = !empty($footer_options['styling_text_color']) ? $footer_options['styling_text_color'] : ($template_data['text'] ?? '#0b2140');
$accent_color = !empty($footer_options['styling_link_color']) ? $footer_options['styling_link_color'] : ($template_data['accent'] ?? '#0b66a6');
$heading_color = !empty($footer_options['styling_heading_color']) ? $footer_options['styling_heading_color'] : $text_color;
$link_hover = !empty($footer_options['styling_link_hover_color']) ? $footer_options['styling_link_hover_color'] : $accent_color;
$font_size = !empty($footer_options['styling_font_size']) ? $footer_options['styling_font_size'] : '15';
$padding_top = !empty($footer_options['styling_padding_top']) ? $footer_options['styling_padding_top'] : '60';
$padding_bottom = !empty($footer_options['styling_padding_bottom']) ? $footer_options['styling_padding_bottom'] : '50';

$columns = $template_data['columns'] ?? 1;
$show_widgets = ross_theme_should_show_footer_widgets();
?>

<footer class="site-footer footer-minimal-modern" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>; padding: <?php echo esc_attr($padding_top); ?>px 0 <?php echo esc_attr($padding_bottom); ?>px; font-size: <?php echo esc_attr($font_size); ?>px;">
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
                                    <h4 class="footer-section-title" style="color: <?php echo esc_attr($heading_color); ?>;">
                                        <?php echo esc_html($col['title']); ?>
                                    </h4>
                                <?php endif; ?>
                                
                                <?php if (!empty($col['items']) && is_array($col['items'])): ?>
                                    <ul class="footer-links-inline">
                                        <?php foreach ($col['items'] as $item): ?>
                                            <li><a href="#" style="color: <?php echo esc_attr($text_color); ?>;"><?php echo esc_html($item); ?></a></li>
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
/* Minimal Modern Footer - Ultra-Clean SaaS Design (100% Dynamic) */
.footer-minimal-modern {
    border-top: 1px solid rgba(0,0,0,0.06);
    position: relative;
}

.footer-minimal-modern .footer-content-centered {
    text-align: center;
    max-width: 900px;
    margin: 0 auto;
}

/* Link Groups */
.footer-minimal-modern .footer-section {
    margin-bottom: 30px;
}

.footer-minimal-modern .footer-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    margin: 0 0 15px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    opacity: 0.6;
}

.footer-minimal-modern .footer-links-inline {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 25px;
    flex-wrap: wrap;
}

.footer-minimal-modern .footer-links-inline li {
    position: relative;
}

.footer-minimal-modern .footer-links-inline li:not(:last-child)::after {
    content: '•';
    position: absolute;
    right: -15px;
    opacity: 0.3;
}

.footer-minimal-modern .footer-links-inline a {
    text-decoration: none;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    opacity: 0.85;
}

.footer-minimal-modern .footer-links-inline a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: <?php echo esc_attr($accent_color); ?>;
    transition: width 0.3s ease;
}

.footer-minimal-modern .footer-links-inline a:hover {
    opacity: 1;
    color: <?php echo esc_attr($accent_color); ?> !important;
}

.footer-minimal-modern .footer-links-inline a:hover::after {
    width: 100%;
}

/* Social Icons */
.footer-minimal-modern .footer-social-icons {
    text-align: center;
    padding: 35px 0 0;
    border-top: 1px solid rgba(0,0,0,0.06);
    margin-top: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-minimal-modern .footer-content-centered {
        max-width: 100%;
        padding: 0 20px;
    }
    
    .footer-minimal-modern .footer-links-inline {
        gap: 15px;
        flex-direction: column;
    }
    
    .footer-minimal-modern .footer-links-inline li:not(:last-child)::after {
        display: none;
    }
}
</style>
