<?php
/**
 * Header Template Part - Modern Professional
 * Fully dynamic based on Ross Theme options
 * 
 * Usage: Place in template-parts/header/header-modern.php
 * 
 * This template uses:
 * - Ross Theme → Header options
 * - Ross Theme → General options
 * - All settings are dynamic and customizable
 */

if (!defined('ABSPATH')) exit;

// Get theme options
$header_options = ross_theme_get_header_options();
$general_options = ross_get_general_options();

// Header styling from options
$header_bg = $header_options['header_bg_color'] ?? '#ffffff';
$header_text = $header_options['header_text_color'] ?? '#333333';
$header_hover = $header_options['header_link_hover_color'] ?? '#E5C902';
$sticky_enabled = $header_options['sticky_header'] ?? 0;
$header_width = $header_options['header_width'] ?? 'contained';
$header_center = $header_options['header_center'] ?? 0;
$enable_search = $header_options['enable_search'] ?? 1;
$enable_cta = $header_options['enable_cta_button'] ?? 1;
$cta_text = $header_options['cta_button_text'] ?? 'Get Started';
$cta_color = $header_options['cta_button_color'] ?? '#E5C902';

// Logo options
$logo_url = $header_options['logo_upload'] ?? '';
$logo_width = $header_options['logo_width'] ?? '200';
$show_site_title = $header_options['show_site_title'] ?? 1;

// Menu options
$menu_alignment = $header_options['menu_alignment'] ?? 'left';
$menu_font_size = $header_options['menu_font_size'] ?? '16';

// Padding from options
$padding_top = $header_options['header_padding_top'] ?? '20';
$padding_bottom = $header_options['header_padding_bottom'] ?? '20';

// Sticky class
$sticky_class = $sticky_enabled ? 'ross-sticky-header' : '';
$center_class = $header_center ? 'ross-header-centered' : '';
$width_class = $header_width === 'full' ? 'ross-header-full-width' : 'ross-header-contained';
?>

<header id="masthead" class="site-header ross-header-modern <?php echo esc_attr($sticky_class . ' ' . $center_class . ' ' . $width_class); ?>" 
        data-sticky="<?php echo esc_attr($sticky_enabled); ?>"
        style="background-color: <?php echo esc_attr($header_bg); ?>; color: <?php echo esc_attr($header_text); ?>; padding: <?php echo esc_attr($padding_top); ?>px 0 <?php echo esc_attr($padding_bottom); ?>px;">
    
    <div class="ross-header-container <?php echo $header_width === 'contained' ? 'container' : 'container-fluid'; ?>">
        <div class="ross-header-inner">
            
            <!-- Logo & Site Branding -->
            <div class="ross-site-branding">
                <?php if ($logo_url): ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="ross-site-logo" rel="home">
                        <img src="<?php echo esc_url($logo_url); ?>" 
                             alt="<?php bloginfo('name'); ?>" 
                             style="max-width: <?php echo esc_attr($logo_width); ?>px; height: auto;">
                    </a>
                <?php endif; ?>
                
                <?php if ($show_site_title && !$logo_url): ?>
                    <div class="ross-site-identity">
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home" style="color: <?php echo esc_attr($header_text); ?>;">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description):
                        ?>
                            <p class="site-description" style="color: <?php echo esc_attr($header_text); ?>;">
                                <?php echo esc_html($description); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Navigation & Actions -->
            <div class="ross-header-navigation" style="text-align: <?php echo esc_attr($menu_alignment); ?>;">
                
                <!-- Mobile Menu Toggle -->
                <button class="ross-mobile-menu-toggle" aria-label="Toggle Menu" aria-expanded="false">
                    <span class="ross-menu-icon"></span>
                    <span class="ross-menu-icon"></span>
                    <span class="ross-menu-icon"></span>
                </button>
                
                <!-- Primary Navigation -->
                <nav id="site-navigation" class="main-navigation" aria-label="Primary Navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class'     => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'items_wrap'     => '<ul id="%1$s" class="%2$s" style="font-size: ' . esc_attr($menu_font_size) . 'px;">%3$s</ul>',
                    ));
                    ?>
                </nav>
                
                <!-- Header Actions -->
                <div class="ross-header-actions">
                    
                    <?php if ($enable_search): ?>
                    <!-- Search Toggle -->
                    <button class="ross-search-toggle" aria-label="Toggle Search" style="color: <?php echo esc_attr($header_text); ?>;">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.5 3C5.46243 3 3 5.46243 3 8.5C3 11.5376 5.46243 14 8.5 14C9.83879 14 11.0659 13.5217 12.0196 12.7266L16.1464 16.8536C16.3417 17.0488 16.6583 17.0488 16.8536 16.8536C17.0488 16.6583 17.0488 16.3417 16.8536 16.1464L12.7266 12.0196C13.5217 11.0659 14 9.83879 14 8.5C14 5.46243 11.5376 3 8.5 3ZM4 8.5C4 6.01472 6.01472 4 8.5 4C10.9853 4 13 6.01472 13 8.5C13 10.9853 10.9853 13 8.5 13C6.01472 13 4 10.9853 4 8.5Z"/>
                        </svg>
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($enable_cta): ?>
                    <!-- CTA Button -->
                    <a href="#contact" class="ross-cta-button" style="background-color: <?php echo esc_attr($cta_color); ?>; color: <?php echo esc_attr($header_text); ?>;">
                        <?php echo esc_html($cta_text); ?>
                    </a>
                    <?php endif; ?>
                    
                </div>
                
            </div>
            
        </div>
    </div>
    
</header>

<style>
/* Dynamic header styles - Responsive */
.ross-header-modern {
    position: relative;
    z-index: 999;
    transition: all 0.3s ease;
}

.ross-header-modern.ross-sticky-header.scrolled {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.ross-header-container {
    max-width: <?php echo esc_attr($general_options['container_width'] ?? '1200'); ?>px;
    margin: 0 auto;
    padding: 0 20px;
}

.ross-header-full-width .ross-header-container {
    max-width: 100%;
}

.ross-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
}

.ross-header-centered .ross-header-inner {
    justify-content: center;
    text-align: center;
}

.ross-site-branding {
    flex-shrink: 0;
}

.ross-site-logo img {
    display: block;
    max-height: 60px;
    width: auto;
}

.site-title {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
    line-height: 1.2;
}

.site-title a {
    text-decoration: none;
    transition: opacity 0.3s ease;
}

.site-title a:hover {
    opacity: 0.8;
    color: <?php echo esc_attr($header_hover); ?> !important;
}

.site-description {
    margin: 5px 0 0;
    font-size: 14px;
    opacity: 0.8;
}

.ross-header-navigation {
    display: flex;
    align-items: center;
    gap: 30px;
    flex-grow: 1;
    justify-content: flex-end;
}

.ross-header-centered .ross-header-navigation {
    justify-content: center;
    width: 100%;
}

.ross-mobile-menu-toggle {
    display: none;
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    flex-direction: column;
    gap: 4px;
}

.ross-menu-icon {
    display: block;
    width: 25px;
    height: 3px;
    background-color: <?php echo esc_attr($header_text); ?>;
    transition: all 0.3s ease;
}

.main-navigation {
    flex-shrink: 0;
}

.primary-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    gap: 25px;
    align-items: center;
}

.primary-menu li a {
    color: <?php echo esc_attr($header_text); ?>;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    display: block;
    padding: 5px 0;
}

.primary-menu li a:hover,
.primary-menu li.current-menu-item a {
    color: <?php echo esc_attr($header_hover); ?>;
}

.ross-header-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.ross-search-toggle {
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    transition: opacity 0.3s ease;
}

.ross-search-toggle:hover {
    opacity: 0.7;
}

.ross-cta-button {
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    font-size: 15px;
    transition: all 0.3s ease;
    display: inline-block;
}

.ross-cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Responsive Breakpoints */
@media (max-width: 1024px) {
    .primary-menu {
        gap: 20px;
    }
    
    .ross-header-navigation {
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .ross-mobile-menu-toggle {
        display: flex;
        order: 3;
    }
    
    .ross-header-navigation {
        width: 100%;
        order: 4;
    }
    
    .main-navigation {
        display: none;
        width: 100%;
        margin-top: 20px;
    }
    
    .main-navigation.active {
        display: block;
    }
    
    .primary-menu {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .ross-header-actions {
        order: 2;
    }
    
    .ross-cta-button {
        padding: 10px 20px;
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .ross-site-logo img {
        max-height: 50px;
        max-width: 150px;
    }
    
    .site-title {
        font-size: 20px;
    }
    
    .ross-header-container {
        padding: 0 15px;
    }
}
</style>

<script>
// Sticky header functionality
(function() {
    const header = document.querySelector('.ross-sticky-header');
    if (!header) return;
    
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
    
    // Mobile menu toggle
    const mobileToggle = document.querySelector('.ross-mobile-menu-toggle');
    const navigation = document.querySelector('.main-navigation');
    
    if (mobileToggle && navigation) {
        mobileToggle.addEventListener('click', function() {
            navigation.classList.toggle('active');
            const isExpanded = navigation.classList.contains('active');
            this.setAttribute('aria-expanded', isExpanded);
        });
    }
})();
</script>
