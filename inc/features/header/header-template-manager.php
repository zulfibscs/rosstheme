<?php
/**
 * Header Template Manager
 * Central system for managing header templates similar to footer system
 * 
 * This file loads header templates from the templates/ directory and provides
 * functions for template management, preview, and application
 */

if (!defined('ABSPATH')) exit;

/**
 * Load all header templates from the templates directory
 * 
 * @return array Array of header template configurations
 */
function ross_theme_load_header_templates() {
    $templates = array();
    $template_dir = get_template_directory() . '/inc/features/header/templates/';
    
    if (!is_dir($template_dir)) {
        return $templates;
    }
    
    $files = glob($template_dir . '*.php');
    
    foreach ($files as $file) {
        if (basename($file) === 'index.php') continue;
        
        $template_data = include $file;
        
        if (is_array($template_data) && isset($template_data['id'])) {
            $templates[$template_data['id']] = $template_data;
        }
    }
    
    // Store templates in options for easy access
    $stored = get_option('ross_theme_header_templates', array());
    if ($stored !== $templates) {
        update_option('ross_theme_header_templates', $templates);
    }
    
    return $templates;
}

/**
 * Get a specific header template by ID
 * 
 * @param string $template_id Template identifier
 * @return array|false Template configuration or false if not found
 */
function ross_theme_get_header_template($template_id) {
    $templates = ross_theme_load_header_templates();
    return isset($templates[$template_id]) ? $templates[$template_id] : false;
}

/**
 * Get currently active header template
 * 
 * @return string Current template ID
 */
function ross_theme_get_active_header_template() {
    $options = get_option('ross_theme_header_options', array());
    return isset($options['header_template']) ? $options['header_template'] : 'business-classic';
}

/**
 * Apply a header template (backup current settings first)
 * 
 * @param string $template_id Template to apply
 * @return bool Success status
 */
function ross_theme_apply_header_template($template_id) {
    $template = ross_theme_get_header_template($template_id);
    
    if (!$template) {
        return false;
    }
    
    // Get current options
    $current_options = get_option('ross_theme_header_options', array());
    
    // Create backup
    $backups = get_option('ross_theme_header_backups', array());
    $backup_id = 'backup_' . time();
    $backups[$backup_id] = array(
        'timestamp' => current_time('mysql'),
        'template' => isset($current_options['header_template']) ? $current_options['header_template'] : 'custom',
        'options' => $current_options
    );
    
    // Keep only last 10 backups
    if (count($backups) > 10) {
        $backups = array_slice($backups, -10, 10, true);
    }
    
    update_option('ross_theme_header_backups', $backups);
    
    // Merge template settings with current options
    $new_options = array_merge($current_options, array(
        'header_template' => $template_id,
        'header_bg_color' => $template['bg'],
        'header_text_color' => $template['text'],
        'header_link_hover_color' => $template['hover'],
        'header_accent_color' => $template['accent'],
        'sticky_header' => $template['sticky_enabled'] ? 1 : 0,
        'enable_search' => $template['search_enabled'] ? 1 : 0,
        'enable_cta_button' => $template['cta']['enabled'] ? 1 : 0,
        'cta_button_text' => $template['cta']['text'],
        'cta_button_url' => $template['cta']['url'],
        'cta_button_color' => $template['cta']['bg'],
        'header_padding_top' => $template['padding_top'],
        'header_padding_bottom' => $template['padding_bottom'],
        'header_width' => $template['container_width'],
        'header_font_size' => $template['font_size'],
        'header_font_weight' => $template['font_weight'],
    ));
    
    update_option('ross_theme_header_options', $new_options);
    
    return true;
}

/**
 * Restore header from backup
 * 
 * @param string $backup_id Backup identifier
 * @return bool Success status
 */
function ross_theme_restore_header_backup($backup_id) {
    $backups = get_option('ross_theme_header_backups', array());
    
    if (!isset($backups[$backup_id])) {
        return false;
    }
    
    $backup = $backups[$backup_id];
    update_option('ross_theme_header_options', $backup['options']);
    
    return true;
}

/**
 * Get all header backups
 * 
 * @return array List of backups
 */
function ross_theme_get_header_backups() {
    return get_option('ross_theme_header_backups', array());
}

/**
 * Delete a specific backup
 * 
 * @param string $backup_id Backup identifier
 * @return bool Success status
 */
function ross_theme_delete_header_backup($backup_id) {
    $backups = get_option('ross_theme_header_backups', array());
    
    if (!isset($backups[$backup_id])) {
        return false;
    }
    
    unset($backups[$backup_id]);
    update_option('ross_theme_header_backups', $backups);
    
    return true;
}

// Initialize templates on admin init
add_action('admin_init', 'ross_theme_load_header_templates');
