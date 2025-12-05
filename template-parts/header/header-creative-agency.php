<?php
/**
 * Header Template: Creative Agency - Frontend Rendering
 * Bold centered design perfect for creative studios and agencies
 */

if (!defined('ABSPATH')) exit;

require_once get_template_directory() . '/inc/features/header/header-template-manager.php';

$template = ross_theme_get_header_template('creative-agency');
$options = ross_theme_get_header_options();

$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$logo_width = $options['logo_width'] ?? '200';
$site_title = get_bloginfo('name');

$bg = $options['header_bg_color'] ?? $template['bg'];
$text = $options['header_text_color'] ?? $template['text'];
$accent = $options['header_accent_color'] ?? $template['accent'];
$hover = $options['header_link_hover_color'] ?? $template['hover'];

$sticky_enabled = isset($options['sticky_header']) ? $options['sticky_header'] : $template['sticky_enabled'];
$sticky_class = $sticky_enabled ? 'header-sticky' : '';

$cta_enabled = isset($options['enable_cta_button']) ? $options['enable_cta_button'] : $template['cta']['enabled'];
$cta_text = $options['cta_button_text'] ?? $template['cta']['text'];
$cta_url = $options['cta_button_url'] ?? $template['cta']['url'];

$search_enabled = isset($options['enable_search']) ? $options['enable_search'] : $template['search_enabled'];
$container_class = ($options['header_width'] ?? $template['container_width']) === 'contained' ? 'container' : '';

?>

<header id="masthead" class="site-header header-creative-agency <?php echo esc_attr($sticky_class); ?>" 
        style="background-color: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($text); ?>;">
    
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="header-inner-stacked" style="text-align: center; padding: <?php echo esc_attr($template['padding_top']); ?>px 20px <?php echo esc_attr($template['padding_bottom']); ?>px;">
            
            <!-- Logo Centered -->
            <div class="header-logo-centered" style="margin-bottom: <?php echo esc_attr($template['logo_margin_bottom'] ?? 20); ?>px;">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="display: inline-flex; align-items: center; text-decoration: none; color: inherit;">
                    <?php if ($options['logo_upload'] || file_exists(get_template_directory() . '/assets/img/logo.png')): ?>
                        <img src="<?php echo esc_url($logo_url); ?>" 
                             alt="<?php echo esc_attr($site_title); ?>" 
                             style="max-width: <?php echo esc_attr($logo_width); ?>px; height: auto; display: block; margin: 0 auto;">
                    <?php else: ?>
                        <span class="logo-text" style="font-size: 28px; font-weight: 700; letter-spacing: 2px;"><?php echo esc_html($site_title); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- Navigation Centered -->
            <nav class="header-navigation-centered">
                <button class="menu-toggle" aria-expanded="false" style="display: none; background: none; border: 1px solid <?php echo esc_attr($text); ?>; color: <?php echo esc_attr($text); ?>; padding: 8px 16px; cursor: pointer; border-radius: 4px;">MENU</button>
                
                <div id="primary-menu">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class' => 'primary-menu-creative',
                            'container' => false,
                        ));
                    } else {
                        echo '<ul class="primary-menu-creative" style="list-style: none; margin: 0; padding: 0; display: flex; gap: 40px; justify-content: center; flex-wrap: wrap;">';
                        echo '<li><a href="' . esc_url(home_url()) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: 600; font-size: 15px; letter-spacing: 1.2px; text-transform: uppercase;">HOME</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/portfolio')) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: 600; font-size: 15px; letter-spacing: 1.2px; text-transform: uppercase;">PORTFOLIO</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/services')) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: 600; font-size: 15px; letter-spacing: 1.2px; text-transform: uppercase;">SERVICES</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/contact')) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: 600; font-size: 15px; letter-spacing: 1.2px; text-transform: uppercase;">CONTACT</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            </nav>
            
            <!-- Actions -->
            <?php if ($cta_enabled): ?>
                <div class="header-cta-centered" style="margin-top: 20px;">
                    <a href="<?php echo esc_url($cta_url); ?>" 
                       class="cta-button-creative" 
                       style="display: inline-block; background: <?php echo esc_attr($template['cta']['bg']); ?>; color: <?php echo esc_attr($template['cta']['color']); ?>; border: 2px solid <?php echo esc_attr($template['cta']['border_color'] ?? $accent); ?>; padding: <?php echo esc_attr($template['cta']['padding']); ?>; border-radius: <?php echo esc_attr($template['cta']['border_radius']); ?>; text-decoration: none; font-weight: 600; transition: all 0.4s;">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
    
</header>

<style>
    .header-creative-agency .primary-menu-creative {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 40px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .header-creative-agency .primary-menu-creative a {
        text-decoration: none;
        color: <?php echo esc_attr($text); ?>;
        font-weight: 600;
        font-size: <?php echo esc_attr($template['font_size']); ?>;
        letter-spacing: <?php echo esc_attr($template['letter_spacing']); ?>;
        text-transform: <?php echo esc_attr($template['text_transform'] ?? 'none'); ?>;
        transition: color 0.3s;
        position: relative;
    }
    .header-creative-agency .primary-menu-creative a:hover {
        color: <?php echo esc_attr($hover); ?>;
    }
    .header-creative-agency .primary-menu-creative a::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: <?php echo esc_attr($accent); ?>;
        transition: width 0.3s;
    }
    .header-creative-agency .primary-menu-creative a:hover::after {
        width: 100%;
    }
    .header-creative-agency.header-sticky {
        position: sticky;
        top: 0;
        z-index: 9999;
        box-shadow: 0 4px 20px <?php echo esc_attr($template['sticky_shadow']); ?>;
    }
    .header-creative-agency .cta-button-creative:hover {
        background: <?php echo esc_attr($template['cta']['hover_bg'] ?? $accent); ?>;
        color: <?php echo esc_attr($template['cta']['hover_color'] ?? '#000'); ?>;
        transform: scale(1.05);
    }
    
    @media (max-width: <?php echo esc_attr($template['mobile_breakpoint']); ?>px) {
        .header-creative-agency .menu-toggle {
            display: block !important;
            margin: 0 auto;
        }
        .header-creative-agency #primary-menu {
            display: none;
            margin-top: 20px;
        }
        .header-creative-agency #primary-menu.open {
            display: block !important;
        }
        .header-creative-agency .primary-menu-creative {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>
