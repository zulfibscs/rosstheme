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
    if (has_custom_logo()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        $logo_alt = get_post_meta($logo_id, '_wp_attachment_image_alt', true) ?: get_bloginfo('name');
        return '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr($logo_alt) . '" class="logo-img">';
    }
    return '<span class="logo-text">' . get_bloginfo('name') . '</span>';
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

    function end_el(&$output, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
?>

<!-- Responsive Header -->
<header class="responsive-header header-contained" id="mainHeader">
    <!-- Top Bar -->
    <div class="top-bar show" id="topBar">
        <div class="container">
            <div class="top-bar-inner">
                <div class="topbar-left">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <a href="tel:+11234567890">+1 (123) 456-7890</a>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:info@example.com">info@example.com</a>
                    </div>
                </div>
                <div class="topbar-right">
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="LinkedIn">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Bar -->
    <div class="announcement-bar show" id="announcementBar">
        <div class="container">
            <div class="announcement-content">
                🎉 Special Offer: Get 20% off on all services this month! Limited time offer.
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <div class="header-inner">
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
                    <button class="mobile-toggle" id="mobileToggle" aria-label="Menu" aria-expanded="false">
                        <span class="toggle-bar"></span>
                        <span class="toggle-bar"></span>
                        <span class="toggle-bar"></span>
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