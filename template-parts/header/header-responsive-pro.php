<?php
/**
 * Elementor-Style Responsive Header Template
 * Uses responsive settings with CSS variables for clean responsive behavior
 */

$options = ross_theme_get_header_options();
$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$site_title = get_bloginfo('name');

// Get responsive settings with fallbacks
$mobile_breakpoint = isset($options['mobile_breakpoint']) ? absint($options['mobile_breakpoint']) : 768;

// Header Height (Responsive)
$header_height_responsive = isset($options['header_height_responsive']) ? $options['header_height_responsive'] : array('desktop' => 80, 'mobile' => 60);
$header_height_desktop = isset($header_height_responsive['desktop']) ? absint($header_height_responsive['desktop']) : 80;
$header_height_mobile = isset($header_height_responsive['mobile']) ? absint($header_height_responsive['mobile']) : 60;

// Logo Max Width (Responsive)
$logo_max_width_responsive = isset($options['logo_max_width_responsive']) ? $options['logo_max_width_responsive'] : array('desktop' => 200, 'mobile' => 150);
$logo_width_desktop = isset($logo_max_width_responsive['desktop']) ? absint($logo_max_width_responsive['desktop']) : 200;
$logo_width_mobile = isset($logo_max_width_responsive['mobile']) ? absint($logo_max_width_responsive['mobile']) : 150;

// CTA Visibility (Responsive)
$cta_visibility_responsive = isset($options['cta_visibility_responsive']) ? $options['cta_visibility_responsive'] : array('desktop' => true, 'mobile' => false);
$cta_visible_desktop = isset($cta_visibility_responsive['desktop']) ? (bool)$cta_visibility_responsive['desktop'] : true;
$cta_visible_mobile = isset($cta_visibility_responsive['mobile']) ? (bool)$cta_visibility_responsive['mobile'] : false;

// Search Visibility (Responsive)
$search_visibility_responsive = isset($options['search_visibility_responsive']) ? $options['search_visibility_responsive'] : array('desktop' => true, 'mobile' => true);
$search_visible_desktop = isset($search_visibility_responsive['desktop']) ? (bool)$search_visibility_responsive['desktop'] : true;
$search_visible_mobile = isset($search_visibility_responsive['mobile']) ? (bool)$search_visibility_responsive['mobile'] : true;

// Header Padding (Responsive)
$header_padding_responsive = isset($options['header_padding_responsive']) ? $options['header_padding_responsive'] : array('desktop' => '20', 'mobile' => '15');
$header_padding_desktop = isset($header_padding_responsive['desktop']) ? $header_padding_responsive['desktop'] : '20';
$header_padding_mobile = isset($header_padding_responsive['mobile']) ? $header_padding_responsive['mobile'] : '15';

// Header Margin (Responsive)
$header_margin_responsive = isset($options['header_margin_responsive']) ? $options['header_margin_responsive'] : array('desktop' => '0', 'mobile' => '0');
$header_margin_desktop = isset($header_margin_responsive['desktop']) ? $header_margin_responsive['desktop'] : '0';
$header_margin_mobile = isset($header_margin_responsive['mobile']) ? $header_margin_responsive['mobile'] : '0';

// Menu alignment (still using single setting for now)
$desktop_menu_align = isset($options['desktop_menu_alignment']) ? $options['desktop_menu_alignment'] : 'center';

// Mobile menu style
$mobile_menu_style = isset($options['mobile_menu_style']) ? $options['mobile_menu_style'] : 'slide';

// CSS Variables for responsive styling
$css_vars = "
    --mobile-breakpoint: {$mobile_breakpoint}px;
    --header-height: {$header_height_desktop}px;
    --header-height-mobile: {$header_height_mobile}px;
    --logo-width: {$logo_width_desktop}px;
    --logo-width-mobile: {$logo_width_mobile}px;
    --header-padding: {$header_padding_desktop}px;
    --header-padding-mobile: {$header_padding_mobile}px;
    --header-margin: {$header_margin_desktop}px;
    --header-margin-mobile: {$header_margin_mobile}px;
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
        <?php if ($search_visible_desktop || $search_visible_mobile) : ?>
        <div class="header-search" data-desktop="<?php echo $search_visible_desktop ? '1' : '0'; ?>" data-mobile="<?php echo $search_visible_mobile ? '1' : '0'; ?>">
            <button class="search-toggle" aria-label="Toggle search">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>
        </div>
        <?php endif; ?>

        <!-- CTA Button (Desktop & Mobile) -->
        <?php if ($cta_visible_desktop || $cta_visible_mobile) : ?>
        <div class="header-cta" data-desktop="<?php echo $cta_visible_desktop ? '1' : '0'; ?>" data-mobile="<?php echo $cta_visible_mobile ? '1' : '0'; ?>">
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