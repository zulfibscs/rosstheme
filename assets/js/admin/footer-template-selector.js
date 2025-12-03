/**
 * Footer Template Selector - Admin Interactions
 * Handles preview, apply, and sync functionality
 */

(function($) {
    'use strict';

    // Wait for DOM ready
    $(document).ready(function() {
        initTemplateSelector();
        initTemplateActions();
        initBackupActions();
        initConfirmModal();
    });

    /**
     * Initialize template card selection
     */
    function initTemplateSelector() {
        // Handle card clicks
        $(document).on('click', '.ross-template-card', function() {
            const $card = $(this);
            const $radio = $card.find('input[type="radio"]');
            
            // Update selection
            $('.ross-template-card').removeClass('selected');
            $card.addClass('selected');
            $radio.prop('checked', true);
            
            // Hide preview when changing selection
            $('#ross-template-preview').hide().removeClass('visible');
        });

        // Mark initially selected card
        const $selectedRadio = $('.ross-footer-templates input[type="radio"]:checked');
        if ($selectedRadio.length) {
            $selectedRadio.closest('.ross-template-card').addClass('selected');
        }
    }

    /**
     * Initialize template action buttons
     */
    function initTemplateActions() {
        // Preview Selected Template
        $('#ross-preview-template').on('click', function(e) {
            e.preventDefault();
            
            const selectedTemplate = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
            
            if (!selectedTemplate) {
                showNotice('Please select a template first.', 'error');
                return;
            }

            previewTemplate(selectedTemplate);
        });

        // Apply Template
        $('#ross-apply-template').on('click', function(e) {
            e.preventDefault();
            
            const selectedTemplate = $('input[name="ross_theme_footer_options[footer_template]"]:checked').val();
            
            if (!selectedTemplate) {
                showNotice('Please select a template first.', 'error');
                return;
            }

            showConfirm(
                'Are you sure you want to apply this template? This will create a backup of your current footer settings.',
                function() {
                    applyTemplate(selectedTemplate);
                }
            );
        });

        // Sync Templates
        $('#ross-sync-templates').on('click', function(e) {
            e.preventDefault();
            syncTemplates();
        });
    }

    /**
     * Preview template
     */
    function previewTemplate(templateId) {
        const $preview = $('#ross-template-preview');
        const $previewBody = $preview.find('.ross-template-preview-body');
        
        if (!$previewBody.length) {
            // Create preview structure if it doesn't exist
            $preview.html(`
                <div class="ross-template-preview-header">
                    <h4>Template Preview</h4>
                    <button type="button" class="ross-template-preview-close">&times;</button>
                </div>
                <div class="ross-template-preview-body"></div>
            `);
        }

        // Check for hidden preview first (client-side)
        const $hiddenPreview = $('#ross-preview-' + templateId);
        if ($hiddenPreview.length) {
            $preview.find('.ross-template-preview-body').html($hiddenPreview.clone());
            $preview.show().addClass('visible');
            scrollToElement($preview);
            return;
        }

        // Fallback to AJAX preview
        const $btn = $('#ross-preview-template');
        $btn.addClass('ross-loading').prop('disabled', true);

        $.ajax({
            url: rossFooterAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ross_get_footer_template_preview',
                template: templateId,
                nonce: rossFooterAdmin.nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $preview.find('.ross-template-preview-body').html(response.data.html);
                    $preview.show().addClass('visible');
                    scrollToElement($preview);
                } else {
                    showNotice('Preview not available for this template.', 'warning');
                }
            },
            error: function() {
                showNotice('Failed to load preview. Please try again.', 'error');
            },
            complete: function() {
                $btn.removeClass('ross-loading').prop('disabled', false);
            }
        });
    }

    /**
     * Apply template
     */
    function applyTemplate(templateId) {
        const $btn = $('#ross-apply-template');
        $btn.addClass('ross-loading').prop('disabled', true);

        $.ajax({
            url: rossFooterAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ross_apply_footer_template',
                template: templateId,
                nonce: rossFooterAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('✅ Template applied successfully! The page will reload to show your changes...', 'success');
                    
                    // Refresh backups list
                    refreshBackupsList();
                    
                    // Reload page to show template content on frontend
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotice(response.data || 'Failed to apply template.', 'error');
                }
            },
            error: function() {
                showNotice('Server error. Please try again.', 'error');
            },
            complete: function() {
                $btn.removeClass('ross-loading').prop('disabled', false);
            }
        });
    }

    /**
     * Sync templates from files
     */
    function syncTemplates() {
        const $btn = $('#ross-sync-templates');
        $btn.addClass('ross-loading').prop('disabled', true);

        $.ajax({
            url: rossFooterAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ross_sync_footer_templates',
                nonce: rossFooterAdmin.sync_nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    showSyncModal(response.data.html);
                } else {
                    showNotice('No templates to sync.', 'info');
                }
            },
            error: function() {
                showNotice('Failed to load sync dialog.', 'error');
            },
            complete: function() {
                $btn.removeClass('ross-loading').prop('disabled', false);
            }
        });
    }

    /**
     * Show sync modal
     */
    function showSyncModal(html) {
        const $modal = $('#ross-sync-modal');
        $modal.html(html).addClass('visible').show();

        // Handle close button
        $modal.on('click', '#ross-sync-close', function() {
            $modal.removeClass('visible').fadeOut(200);
        });

        // Handle apply sync button
        $modal.on('click', '#ross-apply-sync', function(e) {
            e.preventDefault();
            
            const selectedTemplates = [];
            $modal.find('input[name="sync_template_ids[]"]:checked').each(function() {
                selectedTemplates.push($(this).val());
            });

            if (selectedTemplates.length === 0) {
                showNotice('Please select at least one template to sync.', 'warning');
                return;
            }

            $.ajax({
                url: rossFooterAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'ross_apply_template_sync',
                    templates: selectedTemplates,
                    nonce: rossFooterAdmin.sync_nonce
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('Templates synced successfully!', 'success');
                        $modal.removeClass('visible').fadeOut(200);
                        
                        // Reload page to show updated templates
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotice(response.data || 'Sync failed.', 'error');
                    }
                },
                error: function() {
                    showNotice('Server error during sync.', 'error');
                }
            });
        });

        // Close on overlay click
        $modal.on('click', function(e) {
            if ($(e.target).is('#ross-sync-modal')) {
                $modal.removeClass('visible').fadeOut(200);
            }
        });
    }

    /**
     * Initialize backup restore/delete actions
     */
    function initBackupActions() {
        // Restore backup
        $(document).on('click', '.ross-restore-backup', function(e) {
            e.preventDefault();
            
            const backupId = $(this).data('backup-id');
            
            showConfirm(
                'Restore this backup? Your current footer settings will be replaced.',
                function() {
                    restoreBackup(backupId);
                }
            );
        });

        // Delete backup
        $(document).on('click', '.ross-delete-backup', function(e) {
            e.preventDefault();
            
            const backupId = $(this).data('backup-id');
            
            showConfirm(
                'Delete this backup? This action cannot be undone.',
                function() {
                    deleteBackup(backupId);
                }
            );
        });
    }

    /**
     * Restore backup
     */
    function restoreBackup(backupId) {
        $.ajax({
            url: rossFooterAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ross_restore_footer_backup',
                backup_id: backupId,
                nonce: rossFooterAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('Backup restored successfully!', 'success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotice(response.data || 'Failed to restore backup.', 'error');
                }
            },
            error: function() {
                showNotice('Server error.', 'error');
            }
        });
    }

    /**
     * Delete backup
     */
    function deleteBackup(backupId) {
        $.ajax({
            url: rossFooterAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ross_delete_footer_backup',
                backup_id: backupId,
                nonce: rossFooterAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    showNotice('Backup deleted.', 'success');
                    refreshBackupsList();
                } else {
                    showNotice(response.data || 'Failed to delete backup.', 'error');
                }
            },
            error: function() {
                showNotice('Server error.', 'error');
            }
        });
    }

    /**
     * Refresh backups list
     */
    function refreshBackupsList() {
        $.ajax({
            url: rossFooterAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'ross_list_footer_backups',
                nonce: rossFooterAdmin.nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $('#ross-footer-backups').html('<h4>Recent Footer Backups</h4>' + response.data.html);
                }
            }
        });
    }

    /**
     * Initialize confirm modal
     */
    function initConfirmModal() {
        $(document).on('click', '#ross-confirm-cancel', function() {
            hideConfirm();
        });

        $(document).on('click', '.ross-confirm-overlay', function(e) {
            if ($(e.target).is('.ross-confirm-overlay')) {
                hideConfirm();
            }
        });

        // Close preview
        $(document).on('click', '.ross-template-preview-close', function() {
            $('#ross-template-preview').hide().removeClass('visible');
        });
    }

    /**
     * Show confirmation dialog
     */
    function showConfirm(message, onConfirm) {
        const $modal = $('#ross-confirm-modal');
        $modal.find('.ross-confirm-message').html(message);
        $modal.show();

        // Remove old handler and attach new one
        $('#ross-confirm-ok').off('click').on('click', function() {
            hideConfirm();
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    }

    /**
     * Hide confirmation dialog
     */
    function hideConfirm() {
        $('#ross-confirm-modal').hide();
    }

    /**
     * Show admin notice
     */
    function showNotice(message, type) {
        type = type || 'info';
        
        const $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        
        $('.wrap h1').after($notice);
        
        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);

        // Make dismissible work
        $notice.on('click', '.notice-dismiss', function() {
            $notice.fadeOut(200, function() {
                $(this).remove();
            });
        });
    }

    /**
     * Scroll to element smoothly
     */
    function scrollToElement($element) {
        $('html, body').animate({
            scrollTop: $element.offset().top - 100
        }, 400);
    }

})(jQuery);
