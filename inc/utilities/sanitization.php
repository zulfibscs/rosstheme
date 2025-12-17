<?php
/**
 * Theme Sanitization Functions
 * Provides secure sanitization callbacks for theme options and customizer
 */

if (!defined('ABSPATH')) exit;

/**
 * Sanitize checkbox values
 */
function ross_sanitize_checkbox($input) {
    return (isset($input) && $input === true) ? 1 : 0;
}

/**
 * Sanitize integer values
 */
function ross_sanitize_integer($input) {
    return absint($input);
}

/**
 * Sanitize float values
 */
function ross_sanitize_float($input) {
    return floatval($input);
}

/**
 * Sanitize color values (hex codes)
 */
function ross_sanitize_color($input) {
    return sanitize_hex_color($input);
}

/**
 * Sanitize URL values
 */
function ross_sanitize_url($input) {
    return esc_url_raw($input);
}

/**
 * Sanitize icon list (JSON array)
 */
function ross_sanitize_icon_list($input) {
    if (empty($input)) {
        return json_encode(array());
    }

    $decoded = json_decode($input, true);
    if (!is_array($decoded)) {
        return json_encode(array());
    }

    $sanitized = array();
    foreach ($decoded as $item) {
        if (isset($item['icon']) && isset($item['url'])) {
            $sanitized[] = array(
                'icon' => sanitize_text_field($item['icon']),
                'url' => esc_url_raw($item['url'])
            );
        }
    }

    return json_encode($sanitized);
}