<?php
/**
 * Header Template: Business Classic - Frontend Rendering
 * Professional layout: Logo Left, Navigation Center, CTA Right
 */

if (!defined('ABSPATH')) exit;

// Load template manager to get template settings
require_once get_template_directory() . '/inc/features/header/header-template-manager.php';

$template = ross_theme_get_header_template('business-classic');
$options = ross_theme_get_header_options();

// Merge template defaults with user customizations
$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$logo_width = $options['logo_width'] ?? $template['logo_width'] ?? '200';
$site_title = get_bloginfo('name');

// Colors (user can override)
$bg = $options['header_bg_color'] ?? $template['bg'];
$text = $options['header_text_color'] ?? $template['text'];
$accent = $options['header_accent_color'] ?? $template['accent'];
$hover = $options['header_link_hover_color'] ?? $template['hover'];

// Sticky settings
$sticky_enabled = isset($options['sticky_header']) ? $options['sticky_header'] : $template['sticky_enabled'];
$sticky_class = $sticky_enabled ? 'header-sticky' : '';

// CTA settings
$cta_enabled = isset($options['enable_cta_button']) ? $options['enable_cta_button'] : $template['cta']['enabled'];
$cta_text = $options['cta_button_text'] ?? $template['cta']['text'];
$cta_url = $options['cta_button_url'] ?? $template['cta']['url'];
$cta_bg = $options['cta_button_color'] ?? $template['cta']['bg'];

// Search
$search_enabled = isset($options['enable_search']) ? $options['enable_search'] : $template['search_enabled'];

// Container width
$container_class = ($options['header_width'] ?? $template['container_width']) === 'contained' ? 'container' : '';

?>

<header id="masthead" class="site-header header-business-classic <?php echo esc_attr($sticky_class); ?>" 
        style="background-color: <?php echo esc_attr($bg); ?>; color: <?php echo esc_attr($text); ?>; border-bottom: 1px solid <?php echo esc_attr($template['border_bottom']); ?>;">
    
    <div class="<?php echo esc_attr($container_class); ?>">
        <div class="header-inner" style="display: flex; align-items: center; justify-content: space-between; padding: <?php echo esc_attr($template['padding_top']); ?>px 20px <?php echo esc_attr($template['padding_bottom']); ?>px;">
            
            <!-- Logo Left -->
            <div class="header-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                    <?php if ($options['logo_upload'] || file_exists(get_template_directory() . '/assets/img/logo.png')): ?>
                        <img src="<?php echo esc_url($logo_url); ?>" 
                             alt="<?php echo esc_attr($site_title); ?>" 
                             style="max-width: <?php echo esc_attr($logo_width); ?>px; height: auto; display: block;">
                    <?php endif; ?>
                    
                    <?php if (!empty($options['show_site_title']) || empty($options['logo_upload'])): ?>
                        <span class="logo-text" style="margin-left: 10px; font-size: 24px; font-weight: 700;"><?php echo esc_html($site_title); ?></span>
                    <?php endif; ?>
                </a>
            </div>
            
            <!-- Navigation Center -->
            <nav class="header-navigation" style="flex: 1; text-align: center; margin: 0 30px;">
                <button class="menu-toggle" aria-expanded="false" aria-controls="primary-menu" style="display: none; background: none; border: none; font-size: 24px; cursor: pointer; color: <?php echo esc_attr($text); ?>;">☰</button>
                
                <div id="primary-menu">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class' => 'primary-menu',
                            'container' => false,
                        ));
                    } else {
                        echo '<ul class="primary-menu" style="list-style: none; margin: 0; padding: 0; display: flex; gap: 30px; justify-content: center;">';
                        echo '<li><a href="' . esc_url(home_url()) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: ' . esc_attr($template['font_weight']) . '; font-size: ' . esc_attr($template['font_size']) . ';">Home</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/about')) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: ' . esc_attr($template['font_weight']) . '; font-size: ' . esc_attr($template['font_size']) . ';">About</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/services')) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: ' . esc_attr($template['font_weight']) . '; font-size: ' . esc_attr($template['font_size']) . ';">Services</a></li>';
                        echo '<li><a href="' . esc_url(home_url('/contact')) . '" style="text-decoration: none; color: ' . esc_attr($text) . '; font-weight: ' . esc_attr($template['font_weight']) . '; font-size: ' . esc_attr($template['font_size']) . ';">Contact</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            </nav>
            
            <!-- Header Actions Right -->
            <div class="header-actions" style="display: flex; align-items: center; gap: 15px;">
                <?php if ($search_enabled): ?>
                    <div class="header-search">
                        <button class="search-toggle" style="background: none; border: none; cursor: pointer; color: <?php echo esc_attr($text); ?>; padding: 8px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if ($cta_enabled): ?>
                    <a href="<?php echo esc_url($cta_url); ?>" 
                       class="cta-button" 
                       style="background: <?php echo esc_attr($cta_bg); ?>; color: <?php echo esc_attr($template['cta']['color']); ?>; padding: <?php echo esc_attr($template['cta']['padding']); ?>; border-radius: <?php echo esc_attr($template['cta']['border_radius']); ?>; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    
</header>

<style>
    .header-business-classic .primary-menu {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 30px;
        justify-content: center;
    }
    .header-business-classic .primary-menu a {
        text-decoration: none;
        color: <?php echo esc_attr($text); ?>;
        font-weight: <?php echo esc_attr($template['font_weight']); ?>;
        font-size: <?php echo esc_attr($template['font_size']); ?>;
        letter-spacing: <?php echo esc_attr($template['letter_spacing']); ?>;
        transition: color 0.3s;
    }
    .header-business-classic .primary-menu a:hover {
        color: <?php echo esc_attr($hover); ?>;
    }
    .header-business-classic.header-sticky {
        position: sticky;
        top: 0;
        z-index: 9999;
        box-shadow: 0 2px 10px <?php echo esc_attr($template['sticky_shadow']); ?>;
    }
    .header-business-classic .cta-button:hover {
        background: <?php echo esc_attr($template['cta']['hover_bg']); ?>;
        transform: translateY(-2px);
    }
    
    /* Mobile Responsive */
    @media (max-width: <?php echo esc_attr($template['mobile_breakpoint']); ?>px) {
        .header-business-classic .header-inner {
            flex-wrap: wrap;
        }
        .header-business-classic .menu-toggle {
            display: block !important;
        }
        .header-business-classic .header-navigation {
            order: 3;
            width: 100%;
            margin: 15px 0 0 !important;
        }
        .header-business-classic #primary-menu {
            display: none;
        }
        .header-business-classic #primary-menu.open {
            display: block !important;
        }
        .header-business-classic .primary-menu {
            flex-direction: column;
            gap: 10px;
            text-align: left;
        }
    }
</style>
