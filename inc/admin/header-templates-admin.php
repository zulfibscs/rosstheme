<?php
/**
 * Header Templates Administration Interface
 * Provides UI for previewing, selecting, and customizing header templates
 * 
 * Modeled after the footer template system for consistency
 */

if (!defined('ABSPATH')) exit;

/**
 * Render the Header Templates admin page
 */
function ross_theme_render_header_templates_admin() {
    // Load template manager
    if (!function_exists('ross_theme_load_header_templates')) {
        require_once get_template_directory() . '/inc/features/header/header-template-manager.php';
    }
    
    $templates = ross_theme_load_header_templates();
    $current_template = ross_theme_get_active_header_template();
    $backups = ross_theme_get_header_backups();
    
    ?>
    <div class="ross-header-templates-section">
        <div class="ross-section-header">
            <h2>📐 Header Templates</h2>
            <p class="description">Choose from professionally designed header layouts. Each template can be fully customized after applying.</p>
        </div>

        <!-- Template Grid -->
        <div class="ross-templates-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 25px 0;">
            <?php foreach ($templates as $template_id => $template): ?>
                <div class="ross-template-card <?php echo ($current_template === $template_id) ? 'active' : ''; ?>" data-template-id="<?php echo esc_attr($template_id); ?>">
                    
                    <!-- Template Preview -->
                    <div class="ross-template-preview" style="background: <?php echo esc_attr($template['bg']); ?>; color: <?php echo esc_attr($template['text']); ?>; padding: 20px; border-radius: 8px 8px 0 0; min-height: 150px; position: relative;">
                        
                        <?php if ($current_template === $template_id): ?>
                            <span class="ross-active-badge" style="position: absolute; top: 10px; right: 10px; background: <?php echo esc_attr($template['accent']); ?>; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">Active</span>
                        <?php endif; ?>
                        
                        <!-- Mini header preview -->
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(<?php echo $template['bg'] === '#ffffff' ? '0,0,0' : '255,255,255'; ?>, 0.1);">
                            <div style="font-weight: 700; font-size: 18px;">Logo</div>
                            <div style="display: flex; gap: 15px; font-size: 13px;">
                                <span>Home</span>
                                <span>About</span>
                                <span>Services</span>
                            </div>
                            <?php if ($template['cta']['enabled']): ?>
                                <div style="background: <?php echo esc_attr($template['cta']['bg']); ?>; color: <?php echo esc_attr($template['cta']['color']); ?>; padding: 6px 14px; border-radius: <?php echo esc_attr($template['cta']['border_radius']); ?>; font-size: 12px; font-weight: 600;">
                                    <?php echo esc_html($template['cta']['text']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Template Features -->
                        <div style="margin-top: 15px; display: flex; gap: 8px; flex-wrap: wrap; font-size: 11px;">
                            <?php if ($template['sticky_enabled']): ?>
                                <span style="background: rgba(<?php echo $template['bg'] === '#ffffff' ? '0,0,0' : '255,255,255'; ?>, 0.1); padding: 3px 8px; border-radius: 4px;">📌 Sticky</span>
                            <?php endif; ?>
                            <?php if ($template['search_enabled']): ?>
                                <span style="background: rgba(<?php echo $template['bg'] === '#ffffff' ? '0,0,0' : '255,255,255'; ?>, 0.1); padding: 3px 8px; border-radius: 4px;">🔍 Search</span>
                            <?php endif; ?>
                            <span style="background: rgba(<?php echo $template['bg'] === '#ffffff' ? '0,0,0' : '255,255,255'; ?>, 0.1); padding: 3px 8px; border-radius: 4px;">📱 Responsive</span>
                        </div>
                    </div>
                    
                    <!-- Template Info -->
                    <div class="ross-template-info" style="padding: 15px; background: #fff; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <span style="font-size: 24px;"><?php echo esc_html($template['icon']); ?></span>
                            <h3 style="margin: 0; font-size: 16px; font-weight: 600;"><?php echo esc_html($template['title']); ?></h3>
                        </div>
                        <p style="margin: 0 0 15px; color: #6b7280; font-size: 13px; line-height: 1.5;"><?php echo esc_html($template['description']); ?></p>
                        
                        <!-- Action Buttons -->
                        <div style="display: flex; gap: 8px;">
                            <?php if ($current_template === $template_id): ?>
                                <button type="button" class="button button-secondary" disabled style="flex: 1;">Currently Active</button>
                            <?php else: ?>
                                <button type="button" class="button button-primary ross-apply-header-template" data-template-id="<?php echo esc_attr($template_id); ?>" style="flex: 1;">Apply Template</button>
                            <?php endif; ?>
                            <button type="button" class="button ross-preview-header-template" data-template-id="<?php echo esc_attr($template_id); ?>">Preview</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Backups Section -->
        <?php if (!empty($backups)): ?>
            <div class="ross-backups-section" style="margin-top: 40px; padding: 20px; background: #f9fafb; border-radius: 8px;">
                <h3 style="margin-top: 0;">💾 Header Backups</h3>
                <p class="description">Restore a previous header configuration from the list below.</p>
                
                <div class="ross-backups-list" style="margin-top: 15px;">
                    <?php foreach (array_reverse($backups, true) as $backup_id => $backup): ?>
                        <div class="ross-backup-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #fff; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 10px;">
                            <div>
                                <strong><?php echo esc_html($backup['template']); ?></strong>
                                <span style="color: #6b7280; margin-left: 10px; font-size: 13px;">
                                    <?php echo esc_html(date('M d, Y - h:i A', strtotime($backup['timestamp']))); ?>
                                </span>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" class="button button-small ross-restore-header-backup" data-backup-id="<?php echo esc_attr($backup_id); ?>">Restore</button>
                                <button type="button" class="button button-small button-link-delete ross-delete-header-backup" data-backup-id="<?php echo esc_attr($backup_id); ?>">Delete</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Template Preview Modal -->
        <div id="ross-header-template-preview-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 100000; align-items: center; justify-content: center;">
            <div style="background: #fff; width: 90%; max-width: 1200px; max-height: 90vh; overflow-y: auto; border-radius: 12px; position: relative;">
                <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="margin: 0;">Template Preview</h2>
                    <button type="button" class="button" id="ross-close-preview-modal" style="padding: 8px 12px;">✕ Close</button>
                </div>
                <div id="ross-header-preview-content" style="padding: 30px;">
                    <!-- Preview content loaded via AJAX -->
                </div>
            </div>
        </div>

    </div>

    <style>
        .ross-template-card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .ross-template-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .ross-template-card.active {
            box-shadow: 0 0 0 3px #0b66a6;
        }
        .ross-apply-header-template:hover {
            background: #084578 !important;
            border-color: #084578 !important;
        }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Apply Template
        $('.ross-apply-header-template').on('click', function() {
            var templateId = $(this).data('template-id');
            
            if (!confirm('Apply this header template? Your current header settings will be backed up.')) {
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'ross_apply_header_template',
                    template_id: templateId,
                    nonce: '<?php echo wp_create_nonce('ross_header_templates'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ Header template applied successfully!');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + (response.data || 'Failed to apply template'));
                    }
                },
                error: function() {
                    alert('❌ Error: Failed to communicate with server');
                }
            });
        });

        // Preview Template
        $('.ross-preview-header-template').on('click', function() {
            var templateId = $(this).data('template-id');
            
            $('#ross-header-template-preview-modal').css('display', 'flex');
            $('#ross-header-preview-content').html('<p>Loading preview...</p>');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'ross_preview_header_template',
                    template_id: templateId,
                    nonce: '<?php echo wp_create_nonce('ross_header_templates'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $('#ross-header-preview-content').html(response.data);
                    } else {
                        $('#ross-header-preview-content').html('<p>Error loading preview</p>');
                    }
                }
            });
        });

        // Close Preview Modal
        $('#ross-close-preview-modal').on('click', function() {
            $('#ross-header-template-preview-modal').hide();
        });

        // Restore Backup
        $('.ross-restore-header-backup').on('click', function() {
            var backupId = $(this).data('backup-id');
            
            if (!confirm('Restore this header backup?')) {
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'ross_restore_header_backup',
                    backup_id: backupId,
                    nonce: '<?php echo wp_create_nonce('ross_header_templates'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ Header backup restored successfully!');
                        location.reload();
                    } else {
                        alert('❌ Error: ' + (response.data || 'Failed to restore backup'));
                    }
                }
            });
        });

        // Delete Backup
        $('.ross-delete-header-backup').on('click', function() {
            var backupId = $(this).data('backup-id');
            
            if (!confirm('Delete this backup permanently?')) {
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'ross_delete_header_backup',
                    backup_id: backupId,
                    nonce: '<?php echo wp_create_nonce('ross_header_templates'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ Backup deleted successfully');
                        location.reload();
                    } else {
                        alert('❌ Error: Failed to delete backup');
                    }
                }
            });
        });
    });
    </script>
    <?php
}
