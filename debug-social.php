<?php
/**
 * Debug Social Icons Settings
 * Visit: http://theme.dev/wp-content/themes/rosstheme5/rosstheme/debug-social.php
 */

// Load WordPress
require_once('../../../../../wp-load.php');

// Get footer options
$footer_options = get_option('ross_theme_footer_options', array());

echo '<h1>Social Icons Debug</h1>';

echo '<h2>Enable Social Icons</h2>';
echo '<pre>';
var_dump($footer_options['enable_social_icons'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>Facebook</h2>';
echo '<pre>';
echo 'Enabled: ';
var_dump($footer_options['facebook_enabled'] ?? 'NOT SET');
echo "\n";
echo 'URL: ' . ($footer_options['facebook_url'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>Instagram</h2>';
echo '<pre>';
echo 'Enabled: ';
var_dump($footer_options['instagram_enabled'] ?? 'NOT SET');
echo "\n";
echo 'URL: ' . ($footer_options['instagram_url'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>Twitter</h2>';
echo '<pre>';
echo 'Enabled: ';
var_dump($footer_options['twitter_enabled'] ?? 'NOT SET');
echo "\n";
echo 'URL: ' . ($footer_options['twitter_url'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>LinkedIn</h2>';
echo '<pre>';
echo 'Enabled: ';
var_dump($footer_options['linkedin_enabled'] ?? 'NOT SET');
echo "\n";
echo 'URL: ' . ($footer_options['linkedin_url'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>Custom Platform</h2>';
echo '<pre>';
echo 'Enabled: ';
var_dump($footer_options['custom_social_enabled'] ?? 'NOT SET');
echo "\n";
echo 'Label: ' . ($footer_options['custom_social_label'] ?? 'NOT SET') . "\n";
echo 'Icon: ' . ($footer_options['custom_social_icon'] ?? 'NOT SET') . "\n";
echo 'URL: ' . ($footer_options['custom_social_url'] ?? 'NOT SET') . "\n";
echo 'Color: ' . ($footer_options['custom_social_color'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>Display Order</h2>';
echo '<pre>';
var_dump($footer_options['social_display_order'] ?? 'NOT SET');
echo '</pre>';

echo '<h2>All Footer Options Keys (Social-related only)</h2>';
echo '<pre>';
foreach ($footer_options as $key => $value) {
    if (strpos($key, 'social') !== false || in_array($key, ['facebook_enabled', 'facebook_url', 'instagram_enabled', 'instagram_url', 'twitter_enabled', 'twitter_url', 'linkedin_enabled', 'linkedin_url'])) {
        echo $key . ' => ';
        var_dump($value);
        echo "\n";
    }
}
echo '</pre>';

echo '<hr>';
echo '<h2>Test Rendering Function</h2>';
echo '<div style="background: #f0f0f0; padding: 20px;">';
if (function_exists('rosstheme_render_footer_social')) {
    rosstheme_render_footer_social();
    echo '<p><em>^ Social icons should appear above ^</em></p>';
} else {
    echo '<p style="color: red;">Function rosstheme_render_footer_social() does not exist!</p>';
}
echo '</div>';
