<?php
/**
 * Template Name: Modern Complete Header
 * Description: Complete responsive header template with modern design
 */

// Get theme options
$options = get_option('ross_theme_header_options', array());
$config = isset($options['modern-complete']) ? $options['modern-complete'] : array();

// Merge with defaults
$defaults = include get_template_directory() . '/inc/features/header/templates/modern-complete.php';
$config = wp_parse_args($config, $defaults);

// Helper functions
function ross_responsive_modern_get_logo() {
    $opts = function_exists('ross_theme_get_header_options') ? ross_theme_get_header_options() : get_option('ross_theme_header_options', array());
    $logo_height = !empty($opts['logo_height']) ? intval($opts['logo_height']) : 0;
    $show_site_title = isset($opts['show_site_title']) ? (bool) $opts['show_site_title'] : true;

    if (has_custom_logo()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        $logo_alt = get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name');
        $style = '';
        if ($logo_height > 0) {
            $style = ' style="max-height: ' . $logo_height . 'px; width: auto; height: auto;"';
        }
        return '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($logo_alt) . '" class="logo-img"' . $style . '>';
    }

    if ($show_site_title) {
        return '<span class="logo-text">' . get_bloginfo('name') . '</span>';
    }

    return '<span class="logo-text sr-only">' . get_bloginfo('name') . '</span>';
}

function ross_responsive_modern_get_menu($location = 'primary') {
    if (!has_nav_menu($location)) {
        return '<ul class="nav-menu menu-left"><li class="menu-item active"><a href="#" class="menu-link">Home</a></li><li class="menu-item"><a href="#" class="menu-link">About</a></li><li class="menu-item has-dropdown"><a href="#" class="menu-link">Services</a><ul class="dropdown-menu"><li class="dropdown-item"><a href="#" class="dropdown-link">Web Development</a></li><li class="dropdown-item"><a href="#" class="dropdown-link">Mobile Apps</a></li><li class="dropdown-item"><a href="#" class="dropdown-link">UI/UX Design</a></li><li class="dropdown-item"><a href="#" class="dropdown-link">Digital Marketing</a></li></ul></li><li class="menu-item"><a href="#" class="menu-link">Portfolio</a></li><li class="menu-item has-dropdown"><a href="#" class="menu-link">Resources</a><ul class="dropdown-menu"><li class="dropdown-item"><a href="#" class="dropdown-link">Blog</a></li><li class="dropdown-item"><a href="#" class="dropdown-link">Case Studies</a></li><li class="dropdown-item"><a href="#" class="dropdown-link">Whitepapers</a></li></ul></li><li class="menu-item"><a href="#" class="menu-link">Contact</a></li></ul>';
    }

    $menu_args = array(
        'theme_location' => $location,
        'container' => false,
        'menu_class' => 'nav-menu menu-left',
        'depth' => 2,
        'walker' => new Ross_Responsive_Modern_Walker(),
        'fallback_cb' => false
    );

    ob_start();
    wp_nav_menu($menu_args);
    return ob_get_clean();
}

function ross_responsive_modern_get_mobile_menu($location = 'primary') {
    if (!has_nav_menu($location)) {
        return '<ul class="mobile-menu" id="mobileMenu"><li class="mobile-menu-item"><a href="#" class="mobile-menu-link">Home</a></li><li class="mobile-menu-item"><a href="#" class="mobile-menu-link">About</a></li><li class="mobile-menu-item has-dropdown"><a href="#" class="mobile-menu-link">Services<button class="dropdown-toggle" aria-label="Toggle Services"><i class="fas fa-chevron-down"></i></button></a><div class="mobile-dropdown"><a href="#" class="mobile-menu-link">Web Development</a><a href="#" class="mobile-menu-link">Mobile Apps</a><a href="#" class="mobile-menu-link">UI/UX Design</a><a href="#" class="mobile-menu-link">Digital Marketing</a></div></li><li class="mobile-menu-item"><a href="#" class="mobile-menu-link">Portfolio</a></li><li class="mobile-menu-item has-dropdown"><a href="#" class="mobile-menu-link">Resources<button class="dropdown-toggle" aria-label="Toggle Resources"><i class="fas fa-chevron-down"></i></button></a><div class="mobile-dropdown"><a href="#" class="mobile-menu-link">Blog</a><a href="#" class="mobile-menu-link">Case Studies</a><a href="#" class="mobile-menu-link">Whitepapers</a></div></li><li class="mobile-menu-item"><a href="#" class="mobile-menu-link">Contact</a></li></ul>';
    }

    $menu_args = array(
        'theme_location' => $location,
        'container' => false,
        'menu_class' => 'mobile-menu',
        'menu_id' => 'mobileMenu',
        'depth' => 2,
        'walker' => new Ross_Responsive_Modern_Mobile_Walker(),
        'fallback_cb' => false
    );

    ob_start();
    wp_nav_menu($menu_args);
    return ob_get_clean();
}

// Custom walker for desktop menu
class Ross_Responsive_Modern_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<ul class="dropdown-menu">';
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item';
        if ($args->walker->has_children) {
            $classes[] = 'has-dropdown';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ' class="menu-link"';

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

// Custom walker for mobile menu
class Ross_Responsive_Modern_Mobile_Walker extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        $output .= '<div class="mobile-dropdown">';
    }

    function end_lvl(&$output, $depth = 0, $args = null) {
        $output .= '</div>';
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'mobile-menu-item';
        if ($args->walker->has_children) {
            $classes[] = 'has-dropdown';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= ' class="mobile-menu-link"';

        $item_output = $args->before;

        if ($args->walker->has_children) {
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '<button class="dropdown-toggle" aria-label="Toggle ' . esc_attr($item->title) . '"><i class="fas fa-chevron-down"></i></button>';
            $item_output .= '</a>';
        } else {
            $item_output .= '<a' . $attributes . '>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</a>';
        }

        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
?>

<!-- Responsive Header -->
<header class="<?php echo esc_attr(ross_theme_header_classes()); ?>" id="mainHeader">

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <div class="header-inner" style="<?php echo esc_attr(ross_theme_get_header_inline_style()); ?>">
                <!-- Logo -->
                <div class="header-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
                        <?php echo ross_responsive_modern_get_logo(); ?>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="desktop-nav" aria-label="Main Navigation">
                    <?php echo ross_responsive_modern_get_menu(); ?>
                </nav>

                <!-- Header Actions -->
                <div class="header-actions">
                    <!-- Search -->
                    <div class="header-search">
                        <button class="search-toggle" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="search-overlay">
                            <button class="search-close" aria-label="Close Search">
                                <i class="fas fa-times"></i>
                            </button>
                            <div class="search-container">
                                <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                                    <input type="search"
                                           class="search-input"
                                           placeholder="Search for products, articles, guides..."
                                           name="s"
                                           aria-label="Search">
                                    <button type="submit" class="search-submit" aria-label="Submit Search">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <a href="#" class="cta-button">
                        <span>Get Started</span>
                        <i class="fas fa-arrow-right cta-icon"></i>
                    </a>

                    <!-- Mobile Menu Toggle -->
                    <button class="menu-toggle" aria-expanded="false" aria-controls="primary-menu" aria-label="Toggle navigation menu">
                        <span class="hamburger">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    
    <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>
    <div class="mobile-nav-drawer" id="mobileNavDrawer">
        <div class="mobile-nav-header">
            <div class="mobile-logo"><?php echo esc_html(get_bloginfo('name')); ?></div>
            <button class="mobile-close" id="mobileClose" aria-label="Close Menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php echo ross_responsive_modern_get_mobile_menu(); ?>
        <div class="mobile-cta-container">
            <a href="#" class="cta-button mobile-cta">
                <span>Get Started</span>
                <i class="fas fa-arrow-right cta-icon"></i>
            </a>
        </div>
    </div>
</header>