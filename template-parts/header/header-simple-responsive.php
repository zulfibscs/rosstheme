<?php
/**
 * Simple Responsive Header Template
 * One template for both desktop and mobile using CSS variables
 */

$options = ross_theme_get_header_options();
$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$site_title = get_bloginfo('name');

// Get responsive settings with fallbacks
$mobile_breakpoint = isset($options['mobile_breakpoint']) ? absint($options['mobile_breakpoint']) : 768;

// Desktop settings (with fallbacks to main settings)
$desktop_height = isset($options['desktop_header_height']) ? absint($options['desktop_header_height']) : (isset($options['header_height']) ? absint($options['header_height']) : 80);
$desktop_logo_width = isset($options['desktop_logo_max_width']) ? absint($options['desktop_logo_max_width']) : (isset($options['logo_width']) ? absint($options['logo_width']) : 200);
$desktop_menu_align = isset($options['desktop_menu_alignment']) ? $options['desktop_menu_alignment'] : 'center';
$desktop_cta_visible = isset($options['desktop_cta_visibility']) ? $options['desktop_cta_visibility'] : (isset($options['enable_cta_button']) ? $options['enable_cta_button'] : 1);
$desktop_search_visible = isset($options['desktop_search_visibility']) ? $options['desktop_search_visibility'] : (isset($options['enable_search']) ? $options['enable_search'] : 1);

// Mobile settings (with fallbacks to main settings)
$mobile_height = isset($options['mobile_header_height']) ? absint($options['mobile_header_height']) : 60;
$mobile_logo_width = isset($options['mobile_logo_max_width']) ? absint($options['mobile_logo_max_width']) : 120;
$mobile_cta_visible = isset($options['mobile_cta_visibility']) ? $options['mobile_cta_visibility'] : (isset($options['enable_cta_button']) ? $options['enable_cta_button'] : 1);
$mobile_search_visible = isset($options['mobile_search_visibility']) ? $options['mobile_search_visibility'] : (isset($options['enable_search']) ? $options['enable_search'] : 1);
$mobile_menu_style = isset($options['mobile_menu_style']) ? $options['mobile_menu_style'] : 'slide';

// CSS Variables for responsive styling
$css_vars = "
    --mobile-breakpoint: {$mobile_breakpoint}px;
    --header-height: {$desktop_height}px;
    --header-padding: 20px;
    --logo-width: {$desktop_logo_width}px;
";

// Mobile overrides will be handled in CSS with media queries
?>

<header class="<?php echo esc_attr(ross_theme_header_classes()); ?> header-simple-responsive" style="<?php echo esc_attr($css_vars); ?>">
    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
    <div class="container">
        <div class="header-inner">
    <?php else : ?>
        <div class="header-inner">
    <?php endif; ?>

        <!-- Logo Section -->
        <div class="header-logo">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                <?php if ($options['logo_upload'] || file_exists(get_template_directory() . '/assets/img/logo.png')): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_title); ?>" class="logo-image">
                <?php endif; ?>

                <?php if ( ! empty( $options['show_site_title'] ) ) : ?>
                    <div class="logo-text"><?php echo esc_html($site_title); ?></div>
                <?php elseif ( empty( $options['logo_upload'] ) && ! file_exists( get_template_directory() . '/assets/img/logo.png' ) ) : ?>
                    <div class="logo-text"><?php echo esc_html($site_title); ?></div>
                <?php endif; ?>
            </a>
        </div>

        <!-- Navigation Menu -->
        <nav class="header-navigation" data-menu-align="<?php echo esc_attr($desktop_menu_align); ?>">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'primary-menu',
                'container'      => false,
                'fallback_cb'    => 'ross_theme_default_menu',
            ));
            ?>
        </nav>

        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle navigation menu">
            <span class="hamburger">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </span>
        </button>

        <!-- Search Icon (Desktop & Mobile) -->
        <?php if ($desktop_search_visible || $mobile_search_visible) : ?>
        <div class="header-search" data-desktop="<?php echo $desktop_search_visible ? '1' : '0'; ?>" data-mobile="<?php echo $mobile_search_visible ? '1' : '0'; ?>">
            <button class="search-toggle" aria-label="Toggle search">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>
        </div>
        <?php endif; ?>

        <!-- CTA Button (Desktop & Mobile) -->
        <?php if ($desktop_cta_visible || $mobile_cta_visible) : ?>
        <div class="header-cta" data-desktop="<?php echo $desktop_cta_visible ? '1' : '0'; ?>" data-mobile="<?php echo $mobile_cta_visible ? '1' : '0'; ?>">
            <a href="<?php echo esc_url(!empty($options['cta_button_url']) ? $options['cta_button_url'] : '#'); ?>"
               class="header-cta-button"
               target="<?php echo !empty($options['cta_button_target']) ? '_blank' : '_self'; ?>">
                <?php echo esc_html(!empty($options['cta_button_text']) ? $options['cta_button_text'] : 'Get Started'); ?>
            </a>
        </div>
        <?php endif; ?>

        <!-- Mobile Menu Overlay -->
        <div class="mobile-menu-overlay" data-menu-style="<?php echo esc_attr($mobile_menu_style); ?>">
            <nav class="mobile-menu-nav" id="mobile-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'mobile-primary-menu',
                    'menu_class'     => 'mobile-menu',
                    'container'      => false,
                    'fallback_cb'    => 'ross_theme_default_menu',
                ));
                ?>
            </nav>
        </div>

        <!-- Search Overlay -->
        <div class="search-overlay">
            <div class="search-overlay-content">
                <form class="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search" name="s" placeholder="Search..." value="<?php echo get_search_query(); ?>" autofocus>
                    <button type="submit" class="search-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <path d="m21 21-4.35-4.35"></path>
                        </svg>
                    </button>
                </form>
                <button class="search-close" aria-label="Close search">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
        </div><!-- .header-inner -->
    </div><!-- .container -->
    <?php else : ?>
        </div><!-- .header-inner -->
    <?php endif; ?>
</header>