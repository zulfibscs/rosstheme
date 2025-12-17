<?php
/**
 * Footer Template: E-commerce
 * Modern retail layout - 100% dynamic with admin options
 */

$template_file = get_template_directory() . '/inc/features/footer/templates/ecommerce.php';
$template_data = file_exists($template_file) ? include($template_file) : array();
$footer_options = ross_get_footer_options();

// Dynamic styling from admin
$bg_color = !empty($footer_options['styling_bg_color']) ? $footer_options['styling_bg_color'] : ($template_data['bg'] ?? '#ffffff');
$text_color = !empty($footer_options['styling_text_color']) ? $footer_options['styling_text_color'] : ($template_data['text'] ?? '#0b2140');
$accent_color = !empty($footer_options['styling_link_color']) ? $footer_options['styling_link_color'] : ($template_data['accent'] ?? '#b02a2a');
$heading_color = !empty($footer_options['styling_heading_color']) ? $footer_options['styling_heading_color'] : $text_color;
$link_hover = !empty($footer_options['styling_link_hover_color']) ? $footer_options['styling_link_hover_color'] : $accent_color;
$font_size = !empty($footer_options['styling_font_size']) ? $footer_options['styling_font_size'] : '15';
$padding_top = !empty($footer_options['styling_padding_top']) ? $footer_options['styling_padding_top'] : '70';
$padding_bottom = !empty($footer_options['styling_padding_bottom']) ? $footer_options['styling_padding_bottom'] : '40';

$columns = $template_data['columns'] ?? 4;
$show_widgets = ross_theme_should_show_footer_widgets();
?>

<footer class="site-footer footer-ecommerce" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>; padding: <?php echo esc_attr($padding_top); ?>px 0 <?php echo esc_attr($padding_bottom); ?>px; font-size: <?php echo esc_attr($font_size); ?>px;">

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
                                    <h4 class="footer-column-title" style="color: <?php echo esc_attr($heading_color); ?>;"><?php echo esc_html($col['title']); ?></h4>
                                <?php endif; ?>
                                
                                <?php if (!empty($col['html'])): ?>
                                    <div class="footer-column-content">
                                        <?php echo wp_kses_post($col['html']); ?>
                                    </div>
                                <?php elseif (!empty($col['items']) && is_array($col['items'])): ?>
                                    <ul class="footer-column-list">
                                        <?php foreach ($col['items'] as $item): ?>
                                            <li><a href="#" style="color: <?php echo esc_attr($text_color); ?>;"><?php echo esc_html($item); ?></a></li>
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
/* E-commerce Footer - Modern Retail Design (100% Dynamic) */
.footer-ecommerce {
    position: relative;
    border-top: 3px solid <?php echo esc_attr($accent_color); ?>;
}

/* Footer Columns */
.footer-ecommerce .footer-columns {
    display: grid;
    gap: 40px;
    margin-bottom: 45px;
}

.footer-ecommerce .footer-columns-1 { grid-template-columns: 1fr; }
.footer-ecommerce .footer-columns-2 { grid-template-columns: repeat(2, 1fr); }
.footer-ecommerce .footer-columns-3 { grid-template-columns: repeat(3, 1fr); }
.footer-ecommerce .footer-columns-4 { grid-template-columns: repeat(4, 1fr); }

.footer-ecommerce .footer-column-title {
    font-size: 1.15rem;
    font-weight: 700;
    margin: 0 0 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
    padding-bottom: 12px;
}

.footer-ecommerce .footer-column-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: <?php echo esc_attr($accent_color); ?>;
    border-radius: 2px;
}

.footer-ecommerce .footer-column-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-ecommerce .footer-column-list li {
    margin: 12px 0;
    transition: transform 0.2s ease;
}

.footer-ecommerce .footer-column-list li:hover {
    transform: translateX(5px);
}

.footer-ecommerce .footer-column-list a {
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
    position: relative;
}

.footer-ecommerce .footer-column-list a::before {
    content: '›';
    margin-right: 6px;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: <?php echo esc_attr($accent_color); ?>;
}

.footer-ecommerce .footer-column-list a:hover {
    color: <?php echo esc_attr($accent_color); ?> !important;
}

.footer-ecommerce .footer-column-list a:hover::before {
    opacity: 1;
}

.footer-ecommerce .footer-column-content {
    line-height: 1.7;
    opacity: 0.9;
}

/* Social Icons */
.footer-ecommerce .footer-social-icons {
    text-align: center;
    padding: 35px 0 0;
    border-top: 1px solid rgba(0,0,0,0.08);
    margin-top: 20px;
}

/* Responsive Design */
@media (max-width: 992px) {
    .footer-ecommerce .footer-columns-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .footer-ecommerce .footer-columns {
        grid-template-columns: 1fr;
        gap: 35px;
    }
}

@media (max-width: 480px) {
    .footer-ecommerce .footer-column-list li:hover {
        transform: translateX(3px);
    }
}
</style>
