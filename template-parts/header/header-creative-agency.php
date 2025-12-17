<?php
/**
 * Header Template: Creative Agency - Frontend Rendering
 * Modern, premium, fully responsive and option-driven implementation.
 */

if (!defined('ABSPATH')) exit;

require_once get_template_directory() . '/inc/features/header/header-template-manager.php';

$template = ross_theme_get_header_template('creative-agency');
$options = ross_theme_get_header_options();

$logo_url = !empty($options['logo_upload']) ? $options['logo_upload'] : get_template_directory_uri() . '/assets/img/logo.png';
$logo_width = !empty($options['logo_width']) ? intval($options['logo_width']) : 200;
$font_size = isset($options['header_font_size']) && $options['header_font_size'] !== '' ? (is_numeric($options['header_font_size']) ? $options['header_font_size'] . 'px' : $options['header_font_size']) : ($template['font_size'] ?? '15px');
$letter_spacing = isset($options['header_letter_spacing']) && $options['header_letter_spacing'] !== '' ? (is_numeric($options['header_letter_spacing']) ? $options['header_letter_spacing'] . 'px' : $options['header_letter_spacing']) : ($template['letter_spacing'] ?? '1.2px');
$text_transform = isset($options['header_text_transform']) && $options['header_text_transform'] !== '' ? $options['header_text_transform'] : ($template['text_transform'] ?? 'uppercase');
$site_title = get_bloginfo('name');

$bg = $options['header_bg_color'] ?? $template['bg'];
$text = $options['header_text_color'] ?? $template['text'];
$accent = $options['header_accent_color'] ?? $template['accent'];
$hover = $options['header_link_hover_color'] ?? $template['hover'];

$sticky_enabled = isset($options['sticky_header']) ? (bool) $options['sticky_header'] : (bool) ($template['sticky_enabled'] ?? false);
$sticky_class = $sticky_enabled ? 'is-sticky-enabled' : '';

$cta_enabled = isset($options['enable_cta_button']) ? (bool) $options['enable_cta_button'] : (bool) ($template['cta']['enabled'] ?? false);
$cta_text = $options['cta_button_text'] ?? $template['cta']['text'] ?? '';
$cta_url = $options['cta_button_url'] ?? $template['cta']['url'] ?? '#';

$mobile_breakpoint = isset($options['mobile_breakpoint']) ? intval($options['mobile_breakpoint']) : intval($template['mobile_breakpoint'] ?? 768);
$logo_margin_bottom = isset($options['logo_margin_bottom']) ? intval($options['logo_margin_bottom']) : intval($template['logo_margin_bottom'] ?? 20);

// Inline style variables for CSS theming
$vars = sprintf(
    'style="--header-bg:%s;--header-text:%s;--header-accent:%s;--header-hover:%s;--logo-width:%spx;"',
    esc_attr($bg), esc_attr($text), esc_attr($accent), esc_attr($hover), esc_attr($logo_width)
);

?>

<a class="skip-link" href="#content" style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;">Skip to content</a>

<header id="masthead" class="site-header header-creative-agency <?php echo esc_attr($sticky_class); ?>" <?php echo $vars; ?> aria-label="Site header">
    <div class="header-wrap <?php echo esc_attr(($options['header_width'] ?? $template['container_width']) === 'contained' ? 'container' : 'fullwidth'); ?>">
        <div class="header-inner" style="padding: <?php echo esc_attr($options['header_padding_top'] ?? $template['padding_top'] ?? 30); ?>px 20px <?php echo esc_attr($options['header_padding_bottom'] ?? $template['padding_bottom'] ?? 30); ?>px;">

            <div class="header-top" style="text-align:center; margin-bottom: <?php echo esc_attr($logo_margin_bottom); ?>px;">
                <div class="header-brand">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-link" aria-label="Homepage">
                            <?php if (!empty($logo_url)): ?>
                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_title); ?>" class="site-logo" style="max-width: <?php echo esc_attr($logo_width); ?>px; height: auto;" onerror="this.style.display='none'" />
                            <?php else: ?>
                                <span class="logo-text"><?php echo esc_html($site_title); ?></span>
                            <?php endif; ?>
                    </a>
                </div>
            </div>

            <div class="header-nav-actions">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Open menu">
                    <span class="hamburger" aria-hidden="true"></span>
                </button>

                <nav id="primary-menu" class="primary-nav" aria-label="Primary navigation" aria-hidden="true">
                    <?php
                    if (has_nav_menu('primary')) {
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class' => 'primary-menu-creative',
                            'container' => false,
                        ));
                    } else {
                        echo '<ul class="primary-menu-creative" role="menubar">';
                        $items = array(
                            array('href' => home_url('/'), 'label' => 'Home'),
                            array('href' => home_url('/portfolio'), 'label' => 'Portfolio'),
                            array('href' => home_url('/services'), 'label' => 'Services'),
                            array('href' => home_url('/contact'), 'label' => 'Contact'),
                        );
                        foreach ($items as $item) {
                            echo '<li role="none"><a role="menuitem" href="' . esc_url($item['href']) . '">' . esc_html($item['label']) . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </nav>

                <?php if ($cta_enabled): ?>
                    <div class="header-cta">
                        <a href="<?php echo esc_url($cta_url); ?>" class="cta-button" role="button"><?php echo esc_html($cta_text); ?></a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</header>

<style>
    :root { box-sizing: border-box; }
    .header-creative-agency { background: var(--header-bg); color: var(--header-text); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; }
    .header-creative-agency .header-wrap.fullwidth { width: 100%; }
    .header-creative-agency .header-wrap.container { max-width: 1200px; margin: 0 auto; }
    .header-creative-agency .header-inner { display: flex; flex-direction: column; align-items: center; gap: 12px; }
    .header-creative-agency .header-brand .site-logo { display: block; margin: 0 auto; transition: transform 0.25s ease; }
    .header-creative-agency .logo-text { font-size: 28px; font-weight: 800; letter-spacing: 2px; }

    .header-creative-agency .header-nav-actions { display: flex; align-items: center; gap: 20px; width: 100%; justify-content: center; }
    .header-creative-agency .menu-toggle { background: transparent; border: 0; cursor: pointer; display: none; padding: 8px; }
    .header-creative-agency .menu-toggle .hamburger { display: inline-block; width: 28px; height: 2px; background: var(--header-text); position: relative; }
    .header-creative-agency .menu-toggle .hamburger::before, .header-creative-agency .menu-toggle .hamburger::after { content: ''; position: absolute; left: 0; width: 28px; height: 2px; background: var(--header-text); transition: transform 0.2s ease, top 0.15s ease; }
    .header-creative-agency .menu-toggle .hamburger::before { top: -8px; }
    .header-creative-agency .menu-toggle .hamburger::after { top: 8px; }
    .header-creative-agency .menu-toggle.open .hamburger { background: transparent; }
    .header-creative-agency .menu-toggle.open .hamburger::before { transform: rotate(45deg); top: 0; }
    .header-creative-agency .menu-toggle.open .hamburger::after { transform: rotate(-45deg); top: 0; }

    .header-creative-agency .primary-nav { display: block; }
    .header-creative-agency .primary-menu-creative { list-style: none; margin: 0; padding: 0; display: flex; gap: 36px; justify-content: center; align-items: center; }
    .header-creative-agency .primary-menu-creative a { color: var(--header-text); text-decoration: none; font-weight: 600; font-size: <?php echo esc_attr($font_size); ?>; letter-spacing: <?php echo esc_attr($letter_spacing); ?>; text-transform: <?php echo esc_attr($text_transform); ?>; padding: 6px 4px; position: relative; }
    .header-creative-agency .primary-menu-creative a::after { content: ''; position: absolute; left: 50%; transform: translateX(-50%); bottom: -8px; width: 0; height: 3px; background: var(--header-accent); transition: width 0.28s ease; }
    .header-creative-agency .primary-menu-creative a:hover { color: var(--header-hover); }
    .header-creative-agency .primary-menu-creative a:hover::after { width: 80%; }

    .header-creative-agency .header-cta .cta-button { display: inline-block; background: transparent; color: var(--header-accent); border: 2px solid var(--header-accent); padding: <?php echo esc_attr($options['cta_button_padding'] ?? $template['cta']['padding'] ?? '10px 30px'); ?>; border-radius: <?php echo esc_attr($options['cta_button_border_radius'] ?? $template['cta']['border_radius'] ?? '30px'); ?>; text-decoration: none; font-weight: 700; transition: all 0.25s ease; }
    .header-creative-agency .header-cta .cta-button:hover { background: var(--header-accent); color: <?php echo esc_attr($options['cta_button_hover_color'] ?? $template['cta']['hover_color'] ?? '#000'); ?>; transform: translateY(-3px); }

    .header-creative-agency.is-sticky.shrink .site-logo { transform: scale(<?php echo esc_attr($options['header_logo_scale'] ?? $template['animation']['logo_scale'] ?? '0.92'); ?>); }

    .skip-link:focus { position: static; left: auto; width: auto; height: auto; overflow: visible; padding: 8px 12px; background: #ffffff; color: #111111; border-radius: 4px; z-index: 9999; }

    /* Responsive */
    @media (max-width: <?php echo esc_attr($mobile_breakpoint); ?>px) {
        .header-creative-agency .header-inner { gap: 8px; }
        .header-creative-agency .primary-menu-creative { flex-direction: column; gap: 12px; }
        .header-creative-agency .menu-toggle { display: block; }
        .header-creative-agency .primary-nav { display: none; width: 100%; }
        .header-creative-agency .primary-nav.open { display: block; }
        .header-creative-agency .header-cta { order: 3; }
    }

    /* Small improvements for high-contrast and motion */
    @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
</style>

<script>
    (function(){
        var header = document.querySelector('.header-creative-agency');
        var btn = header && header.querySelector('.menu-toggle');
        var nav = header && header.querySelector('.primary-nav');
        if (btn && nav) {
            var lastFocus = null;
            var focusableSelector = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex]:not([tabindex="-1"])';

            function trapHandler(e) {
                if (e.key === 'Escape') {
                    if (nav.classList.contains('open')) {
                        closeMenu();
                    }
                }
                if (e.key === 'Tab' && nav.classList.contains('open')) {
                    var nodes = Array.prototype.slice.call(nav.querySelectorAll(focusableSelector)).filter(function(el){ return el.offsetParent !== null; });
                    if (nodes.length === 0) return;
                    var first = nodes[0], last = nodes[nodes.length - 1];
                    if (e.shiftKey) {
                        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
                    } else {
                        if (document.activeElement === last) { e.preventDefault(); first.focus(); }
                    }
                }
            }

            function openMenu() {
                lastFocus = document.activeElement;
                btn.setAttribute('aria-expanded','true');
                btn.classList.add('open');
                nav.classList.add('open');
                nav.setAttribute('aria-hidden','false');
                document.body.classList.add('mobile-menu-open');
                document.addEventListener('keydown', trapHandler);
                var first = nav.querySelector(focusableSelector); if (first) first.focus();
            }

            function closeMenu() {
                btn.setAttribute('aria-expanded','false');
                btn.classList.remove('open');
                nav.classList.remove('open');
                nav.setAttribute('aria-hidden','true');
                document.body.classList.remove('mobile-menu-open');
                document.removeEventListener('keydown', trapHandler);
                if (lastFocus) lastFocus.focus();
            }

            btn.addEventListener('click', function(){
                var expanded = this.getAttribute('aria-expanded') === 'true';
                if (expanded) closeMenu(); else openMenu();
            });
        }

        // Add sticky class on scroll if enabled (align with dynamic-css selectors)
        if (header && header.classList.contains('is-sticky-enabled')) {
            var stickyShrink = <?php echo (isset($options['sticky_shrink_header']) && $options['sticky_shrink_header']) ? 'true' : 'false'; ?>;
            window.addEventListener('scroll', function(){
                var st = window.pageYOffset || document.documentElement.scrollTop;
                if (st > 60) {
                    header.classList.add('is-sticky');
                    document.body.classList.add('has-sticky-header');
                    if (stickyShrink) {
                        header.classList.add('shrink');
                        document.body.classList.add('is-sticky');
                    }
                } else {
                    header.classList.remove('is-sticky');
                    header.classList.remove('shrink');
                    document.body.classList.remove('has-sticky-header');
                    document.body.classList.remove('is-sticky');
                }
            }, { passive: true });
        }
    })();
</script>
