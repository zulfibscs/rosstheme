<?php
/**
 * Test Social Icons Rendering
 * Add this to footer.php temporarily to debug
 */

// Get footer options
$footer_options = get_option('ross_theme_footer_options', array());

echo '<!-- SOCIAL ICONS DEBUG START -->';
echo '<!-- Enable Social Icons: ' . ($footer_options['enable_social_icons'] ?? 'NOT SET') . ' -->';
echo '<!-- Facebook Enabled: ' . ($footer_options['facebook_enabled'] ?? 'NOT SET') . ' -->';
echo '<!-- Facebook URL: ' . ($footer_options['facebook_url'] ?? 'NOT SET') . ' -->';
echo '<!-- Instagram Enabled: ' . ($footer_options['instagram_enabled'] ?? 'NOT SET') . ' -->';
echo '<!-- Instagram URL: ' . ($footer_options['instagram_url'] ?? 'NOT SET') . ' -->';
echo '<!-- Twitter Enabled: ' . ($footer_options['twitter_enabled'] ?? 'NOT SET') . ' -->';
echo '<!-- Twitter URL: ' . ($footer_options['twitter_url'] ?? 'NOT SET') . ' -->';
echo '<!-- LinkedIn Enabled: ' . ($footer_options['linkedin_enabled'] ?? 'NOT SET') . ' -->';
echo '<!-- LinkedIn URL: ' . ($footer_options['linkedin_url'] ?? 'NOT SET') . ' -->';

echo '<!-- ross_theme_should_show_social_icons(): ' . (ross_theme_should_show_social_icons() ? 'TRUE' : 'FALSE') . ' -->';
echo '<!-- Function exists: ' . (function_exists('rosstheme_render_footer_social') ? 'YES' : 'NO') . ' -->';

echo '<!-- SOCIAL ICONS DEBUG END -->';

// Try to render
if (ross_theme_should_show_social_icons() && function_exists('rosstheme_render_footer_social')) {
    echo '<!-- RENDERING SOCIAL ICONS -->';
    rosstheme_render_footer_social();
    echo '<!-- SOCIAL ICONS RENDERED -->';
} else {
    echo '<!-- SOCIAL ICONS NOT RENDERING - CHECK CONDITIONS -->';
}
