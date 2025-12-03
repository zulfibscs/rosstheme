<?php
/**
 * Footer Template Part: Business Professional
 * Fully dynamic layout with 4 columns and social icons
 * All settings controlled from Ross Theme Settings → Footer
 * 
 * Structure:
 * - Four Columns (controlled by Layout & content settings)
 * - Social Icons (in column 1, controlled by Social tab)
 * 
 * Note: CTA and Copyright are handled by footer.php, not this template
 */

$footer_options = get_option('ross_theme_footer_options', array());

// Get styling options
$bg_color = $footer_options['styling_bg_color'] ?? '#1a1a1a';
$text_color = $footer_options['styling_text_color'] ?? '#e0e0e0';
$accent_color = $footer_options['styling_link_color'] ?? '#3498db';
$footer_width = $footer_options['footer_width'] ?? 'boxed';
$container_class = ($footer_width === 'full') ? 'container-fluid' : 'container';

// Column settings
$num_columns = isset($footer_options['footer_columns']) ? intval($footer_options['footer_columns']) : 4;
$show_template_content = isset($footer_options['use_template_content']) ? (bool)$footer_options['use_template_content'] : false;
?>

<footer class="site-footer footer-business-professional" style="background-color: <?php echo esc_attr($bg_color); ?>; color: <?php echo esc_attr($text_color); ?>;">
    <div class="footer-main">
        <div class="<?php echo esc_attr($container_class); ?>">
            
            <div class="footer-columns footer-columns-<?php echo esc_attr($num_columns); ?>">
                
                <?php if ($show_template_content): ?>
                    <?php
                    // TEMPLATE MODE: Use static template content
                    $template_file = get_template_directory() . '/inc/features/footer/templates/business-professional.php';
                    $template_data = file_exists($template_file) ? include($template_file) : array();
                    $template_cols = $template_data['cols'] ?? array();
                    
                    // Limit to selected number of columns
                    $template_cols = array_slice($template_cols, 0, $num_columns);
                    
                    foreach ($template_cols as $index => $col):
                    ?>
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
                                        <li><a href="#"><?php echo esc_html($item); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            
                            <?php 
                            // Add social icons to first column if enabled
                            if ($index === 0 && ross_theme_should_show_social_icons()): 
                            ?>
                                <div class="footer-column-social" style="margin-top: 20px;">
                                    <?php ross_footer_social_icons(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                    endforeach;
                    ?>
                    
                <?php else: ?>
                    <?php
                    // WIDGET MODE: Use WordPress widget areas
                    for ($i = 1; $i <= $num_columns; $i++): 
                        if (is_active_sidebar('footer-' . $i)): 
                    ?>
                        <div class="footer-column footer-widget-area">
                            <?php dynamic_sidebar('footer-' . $i); ?>
                            
                            <?php 
                            // Add social icons to first column if enabled
                            if ($i === 1 && ross_theme_should_show_social_icons()): 
                            ?>
                                <div class="footer-widget-social" style="margin-top: 20px;">
                                    <?php ross_footer_social_icons(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                        endif;
                    endfor;
                    ?>
                <?php endif; ?>
                
            </div>
            
        </div>
    </div>
</footer>

<style>
.footer-business-professional {
    padding: 60px 0 30px;
}
.footer-business-professional .footer-columns {
    display: grid;
    gap: 30px;
    margin-bottom: 40px;
}
.footer-business-professional .footer-columns-1 { grid-template-columns: 1fr; }
.footer-business-professional .footer-columns-2 { grid-template-columns: repeat(2, 1fr); }
.footer-business-professional .footer-columns-3 { grid-template-columns: repeat(3, 1fr); }
.footer-business-professional .footer-columns-4 { grid-template-columns: repeat(4, 1fr); }
.footer-business-professional .footer-column-title {
    font-size: 1rem;
    font-weight: 600;
    margin: 0 0 1.25rem;
    color: inherit;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.footer-business-professional .footer-column-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.footer-business-professional .footer-column-list li {
    margin-bottom: 0.75rem;
}
.footer-business-professional .footer-column-list a {
    color: <?php echo esc_attr($text_color); ?>;
    opacity: 0.8;
    text-decoration: none;
    transition: opacity 0.3s ease;
}
.footer-business-professional .footer-column-list a:hover {
    opacity: 1;
    color: <?php echo esc_attr($accent_color); ?>;
}
.footer-business-professional .footer-column-content {
    line-height: 1.6;
    opacity: 0.8;
}
.footer-business-professional .footer-social-icons {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid rgba(255,255,255,0.1);
}
@media (max-width: 768px) {
    .footer-business-professional .footer-columns-2,
    .footer-business-professional .footer-columns-3,
    .footer-business-professional .footer-columns-4 {
        grid-template-columns: 1fr;
    }
}
</style>
