<?php
/**
 * Footer Template: Creative Agency
 * Bold dark design - 100% dynamic with admin options
 */

$template_file = get_template_directory() . '/inc/features/footer/templates/creative-agency.php';
$template_data = file_exists($template_file) ? include($template_file) : array();
$footer_options = get_option('ross_theme_footer_options', array());

// Dynamic styling from admin
$bg_color = !empty($footer_options['styling_bg_color']) ? $footer_options['styling_bg_color'] : ($template_data['bg'] ?? '#0c0c0d');
$text_color = !empty($footer_options['styling_text_color']) ? $footer_options['styling_text_color'] : ($template_data['text'] ?? '#f3f4f6');
$accent_color = !empty($footer_options['styling_link_color']) ? $footer_options['styling_link_color'] : ($template_data['accent'] ?? '#E5C902');
$heading_color = !empty($footer_options['styling_heading_color']) ? $footer_options['styling_heading_color'] : $text_color;
$link_hover = !empty($footer_options['styling_link_hover_color']) ? $footer_options['styling_link_hover_color'] : $accent_color;
$font_size = !empty($footer_options['styling_font_size']) ? $footer_options['styling_font_size'] : '15';
$padding_top = !empty($footer_options['styling_padding_top']) ? $footer_options['styling_padding_top'] : '80';
$padding_bottom = !empty($footer_options['styling_padding_bottom']) ? $footer_options['styling_padding_bottom'] : '50';

$columns = $template_data['columns'] ?? 4;
$show_widgets = ross_theme_should_show_footer_widgets();
?>

<footer class="site-footer footer-creative-agency" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>; padding: <?php echo esc_attr($padding_top); ?>px 0 <?php echo esc_attr($padding_bottom); ?>px; font-size: <?php echo esc_attr($font_size); ?>px;">
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
                                    <h4 class="footer-column-title" style="color: <?php echo esc_attr($heading_color); ?>;">
                                        <?php echo esc_html($col['title']); ?>
                                    </h4>
                                <?php endif; ?>
                                
                                <?php if (!empty($col['html'])): ?>
                                    <div class="footer-column-content" style="color: <?php echo esc_attr($text_color); ?>;">
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
/* Creative Agency Footer - Bold Dark Design (100% Dynamic) */
.footer-creative-agency {
    position: relative;
    overflow: hidden;
}

/* Subtle gradient overlay */
.footer-creative-agency::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 150px;
    background: linear-gradient(180deg, <?php echo esc_attr($accent_color); ?>08 0%, transparent 100%);
    pointer-events: none;
}

.footer-creative-agency .footer-main {
    position: relative;
    z-index: 1;
}

/* Footer Columns */
.footer-creative-agency .footer-columns {
    display: grid;
    gap: 50px;
    margin-bottom: 40px;
}

.footer-creative-agency .footer-columns-1 { grid-template-columns: 1fr; }
.footer-creative-agency .footer-columns-2 { grid-template-columns: repeat(2, 1fr); }
.footer-creative-agency .footer-columns-3 { grid-template-columns: repeat(3, 1fr); }
.footer-creative-agency .footer-columns-4 { grid-template-columns: repeat(4, 1fr); }

.footer-creative-agency .footer-column-title {
    font-size: 1.2rem;
    font-weight: 800;
    margin: 0 0 25px;
    text-transform: uppercase;
    letter-spacing: 2px;
    position: relative;
    display: inline-block;
}

.footer-creative-agency .footer-column-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, <?php echo esc_attr($accent_color); ?>, transparent);
}

.footer-creative-agency .footer-column-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-creative-agency .footer-column-list li {
    margin: 15px 0;
    position: relative;
    padding-left: 20px;
}

.footer-creative-agency .footer-column-list li::before {
    content: '▸';
    position: absolute;
    left: 0;
    color: <?php echo esc_attr($accent_color); ?>;
    font-size: 1.2em;
    transition: transform 0.3s ease;
}

.footer-creative-agency .footer-column-list li:hover::before {
    transform: translateX(5px);
}

.footer-creative-agency .footer-column-list a {
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.05rem;
    font-weight: 500;
}

.footer-creative-agency .footer-column-list a:hover {
    color: <?php echo esc_attr($accent_color); ?> !important;
    letter-spacing: 1px;
}

.footer-creative-agency .footer-column-content {
    line-height: 1.8;
    opacity: 0.85;
    font-size: 1.05rem;
}

/* Social Icons */
.footer-creative-agency .footer-social-icons {
    text-align: center;
    padding: 40px 0 0;
    border-top: 2px solid <?php echo esc_attr($accent_color); ?>40;
    margin-top: 20px;
}

/* Responsive Design */
@media (max-width: 992px) {
    .footer-creative-agency .footer-columns-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .footer-creative-agency .footer-columns {
        grid-template-columns: 1fr;
        gap: 40px;
    }
}

@media (max-width: 480px) {
    .footer-creative-agency .footer-column-list a:hover {
        letter-spacing: 0.5px;
    }
}
</style>
