<?php
/**
 * Default Header - Logo Left, Navigation Center, CTA Right
 */

$options = ross_theme_get_header_options();
$logo_url = $options['logo_upload'] ?: get_template_directory_uri() . '/assets/img/logo.png';
$site_title = get_bloginfo('name');
$inline_style = ross_theme_get_header_inline_style();
?>

<header class="<?php echo esc_attr(ross_theme_header_classes()); ?>" style="<?php echo $inline_style; ?>">
    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
    <div class="container">
        <div class="header-inner">
    <?php else : ?>
        <div class="header-inner">
    <?php endif; ?>
        
            <!-- Logo Left -->
            <div class="header-logo">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" style="display:flex;align-items:center;gap:0.6rem;">
                    <?php
                    $has_logo = ($options['logo_upload'] || file_exists(get_template_directory() . '/assets/img/logo.png'));
                    $mobile_logo = isset($options['mobile_logo']) ? $options['mobile_logo'] : '';
                    $mobile_logo_width = isset($options['mobile_logo_width']) ? absint($options['mobile_logo_width']) : 120;
                    ?>
                    <?php if ($has_logo): ?>
                        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_title); ?>" class="desktop-logo" style="max-width: <?php echo esc_attr($options['logo_width']); ?>px; height: auto; display:block;">
                        <?php if (!empty($mobile_logo)): ?>
                            <img src="<?php echo esc_url($mobile_logo); ?>" alt="<?php echo esc_attr($site_title); ?>" class="mobile-logo" style="max-width: <?php echo esc_attr($mobile_logo_width); ?>px; height: auto; display:none;">
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ( ! empty( $options['show_site_title'] ) ) : ?>
                        <div class="logo-text"><?php echo esc_html($site_title); ?></div>
                    <?php elseif ( empty( $options['logo_upload'] ) && ! file_exists( get_template_directory() . '/assets/img/logo.png' ) ) : ?>
                        <!-- fallback: no logo uploaded, show site title -->
                        <div class="logo-text"><?php echo esc_html($site_title); ?></div>
                    <?php endif; ?>
                </a>
            </div>

            <!-- Navigation Center -->
            <button class="menu-toggle" aria-expanded="false" aria-controls="primary-menu" aria-label="Toggle navigation menu">
                <span class="hamburger">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </span>
            </button>
            <nav class="header-navigation" id="primary-menu">
                <?php
                if (has_nav_menu('primary')) {
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_class' => 'primary-menu',
                        'container' => false,
                    ));
                } else {
                    echo '<ul class="primary-menu">';
                    echo '<li><a href="' . home_url() . '">Home</a></li>';
                    echo '<li><a href="' . home_url('/about') . '">About</a></li>';
                    echo '<li><a href="' . home_url('/services') . '">Services</a></li>';
                    echo '<li><a href="' . home_url('/contact') . '">Contact</a></li>';
                    echo '</ul>';
                }
                ?>
            </nav>

            <!-- Header Actions Right -->
            <div class="header-actions" style="gap: <?php echo esc_attr($options['header_actions_spacing'] ?? '15'); ?>px;">
                <?php
                // Determine order of elements
                $actions_order = isset($options['header_actions_order']) ? $options['header_actions_order'] : 'search-cta';
                $show_search = ross_theme_header_feature_enabled('search') && (!wp_is_mobile() || empty($options['search_mobile_hide']));
                $show_cta = ross_theme_header_feature_enabled('cta_button') && (!wp_is_mobile() || empty($options['cta_button_mobile_hide']));

                $elements = array();
                if ($actions_order === 'search-cta') {
                    if ($show_search) $elements[] = 'search';
                    if ($show_cta) $elements[] = 'cta';
                } else {
                    if ($show_cta) $elements[] = 'cta';
                    if ($show_search) $elements[] = 'search';
                }

                foreach ($elements as $element):
                    if ($element === 'search'):
                ?>
                    <div class="header-search">
                        <button class="search-toggle <?php echo esc_attr($options['search_icon_style'] ?? 'magnifying-glass'); ?> <?php echo esc_attr($options['search_animation'] ?? 'none'); ?>" aria-label="Open search">
                            <?php
                            $icon_style = $options['search_icon_style'] ?? 'magnifying-glass';
                            switch ($icon_style) {
                                case 'search-text':
                                    echo '<span class="search-text">Search</span>';
                                    break;
                                case 'search-circle':
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>';
                                    break;
                                case 'minimal':
                                    echo '<span class="search-dot">•</span>';
                                    break;
                                default: // magnifying-glass
                                    echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>';
                            }
                            ?>
                        </button>
                    </div>
                <?php
                    elseif ($element === 'cta'):
                        $cta_url = ! empty($options['cta_button_url']) ? $options['cta_button_url'] : home_url('/contact');
                        $cta_style = isset($options['cta_button_style']) ? $options['cta_button_style'] : 'solid';
                        $cta_size = isset($options['cta_button_size']) ? $options['cta_button_size'] : 'medium';
                        $cta_border_radius = isset($options['cta_button_border_radius']) ? $options['cta_button_border_radius'] : '8';
                        $cta_hover_effect = isset($options['cta_button_hover_effect']) ? $options['cta_button_hover_effect'] : 'scale';
                        $cta_text_hover_effect = isset($options['cta_button_text_hover_effect']) ? $options['cta_button_text_hover_effect'] : 'none';
                        $cta_text_color = isset($options['cta_button_text_color']) ? $options['cta_button_text_color'] : '#ffffff';
                        $cta_hover_text_color = isset($options['cta_button_hover_text_color']) ? $options['cta_button_hover_text_color'] : '#ffffff';
                        $cta_font_size = isset($options['cta_button_font_size']) ? $options['cta_button_font_size'] : '16';
                        $cta_icon = isset($options['cta_button_icon']) ? $options['cta_button_icon'] : 'none';
                        $cta_icon_position = isset($options['cta_button_icon_position']) ? $options['cta_button_icon_position'] : 'left';
                        $cta_shadow = isset($options['cta_button_shadow']) ? $options['cta_button_shadow'] : 'none';
                        $cta_target = isset($options['cta_button_target']) ? $options['cta_button_target'] : '_self';
                        $cta_position = isset($options['cta_button_position']) ? $options['cta_button_position'] : 'right';

                        $button_classes = 'header-cta-button cta-style-' . esc_attr($cta_style) . ' cta-size-' . esc_attr($cta_size) . ' cta-hover-' . esc_attr($cta_hover_effect) . ' cta-text-hover-' . esc_attr($cta_text_hover_effect) . ' cta-shadow-' . esc_attr($cta_shadow) . ' cta-position-' . esc_attr($cta_position);
                        $button_styles = 'border-radius: ' . esc_attr($cta_border_radius) . 'px; font-size: ' . esc_attr($cta_font_size) . 'px; --cta-text-color: ' . esc_attr($cta_text_color) . '; --cta-hover-text-color: ' . esc_attr($cta_hover_text_color) . '; --cta-bg-color: ' . esc_attr($options['cta_button_color']) . ';';

                        if ($cta_style === 'solid') {
                            $button_styles .= 'background: ' . esc_attr($options['cta_button_color']) . ';';
                        } elseif ($cta_style === 'outline') {
                            $button_styles .= 'border: 2px solid ' . esc_attr($options['cta_button_color']) . '; background: transparent;';
                        } elseif ($cta_style === 'ghost') {
                            $button_styles .= 'background: transparent;';
                        } elseif ($cta_style === 'gradient') {
                            $button_styles .= 'background: linear-gradient(135deg, ' . esc_attr($options['cta_button_color']) . ', ' . esc_attr($options['cta_button_color']) . 'dd);';
                        }

                        // Add icon
                        $icon_html = '';
                        if ($cta_icon !== 'none') {
                            $icon_class = 'cta-icon cta-icon-' . esc_attr($cta_icon);
                            $icon_html = '<span class="' . esc_attr($icon_class) . '">' . ross_theme_get_cta_icon($cta_icon) . '</span>';
                        }
                ?>
                    <a href="<?php echo esc_url($cta_url); ?>" target="<?php echo esc_attr($cta_target); ?>" class="<?php echo esc_attr($button_classes); ?>" style="<?php echo esc_attr($button_styles); ?>">
                        <?php if ($cta_icon_position === 'left' && $icon_html): ?>
                            <?php echo $icon_html; ?>
                        <?php endif; ?>
                        <span class="cta-text"><?php echo esc_html($options['cta_button_text']); ?></span>
                        <?php if ($cta_icon_position === 'right' && $icon_html): ?>
                            <?php echo $icon_html; ?>
                        <?php endif; ?>
                    </a>
                <?php
                    endif;
                endforeach;
                ?>
            </div>

    <?php if ( isset($options['header_width']) && $options['header_width'] === 'contained' ) : ?>
        </div>
    </div>
    <?php else : ?>
        </div>
    <?php endif; ?>

    <!-- Mobile Menu JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navigation = document.querySelector('.header-navigation');

        if (menuToggle && navigation) {
            menuToggle.addEventListener('click', function() {
                const isOpen = navigation.classList.contains('open');
                navigation.classList.toggle('open');
                menuToggle.classList.toggle('open');
                menuToggle.setAttribute('aria-expanded', !isOpen);
            });

            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!menuToggle.contains(event.target) && !navigation.contains(event.target)) {
                    navigation.classList.remove('open');
                    menuToggle.classList.remove('open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && navigation.classList.contains('open')) {
                    navigation.classList.remove('open');
                    menuToggle.classList.remove('open');
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
    </script>
</header>