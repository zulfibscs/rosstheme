/**
 * Homepage Template Switcher - Admin JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';
    
    const switcher = {
        currentTemplate: null,
        previewingTemplate: null,
        
        init: function() {
            this.bindEvents();
            this.getCurrentTemplate();
        },
        
        bindEvents: function() {
            // Preview template
            $(document).on('click', '.ross-preview-template', this.previewTemplate.bind(this));
            
            // Apply template
            $(document).on('click', '.ross-apply-template', this.applyTemplate.bind(this));
            
            // Apply from preview modal
            $(document).on('click', '.ross-apply-from-preview', this.applyFromPreview.bind(this));
            
            // Close modal
            $(document).on('click', '.ross-modal-close, .ross-modal-overlay', this.closeModal.bind(this));
            
            // Escape key closes modal
            $(document).on('keyup', function(e) {
                if (e.key === 'Escape') {
                    switcher.closeModal();
                }
            });
        },
        
        getCurrentTemplate: function() {
            $.ajax({
                url: rossTemplateSwitcher.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ross_get_current_template',
                    nonce: rossTemplateSwitcher.nonce
                },
                success: function(response) {
                    if (response.success) {
                        switcher.currentTemplate = response.data.template;
                    }
                }
            });
        },
        
        previewTemplate: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const template = $btn.data('template');
            const previewUrl = $btn.data('preview-url');
            
            // Store for apply from preview
            this.previewingTemplate = template;
            
            // Show modal
            this.showModal(previewUrl);
        },
        
        showModal: function(url) {
            const $modal = $('#ross-template-preview-modal');
            const $iframe = $('#ross-preview-iframe');
            
            // Set iframe source
            $iframe.attr('src', url);
            
            // Show modal with fade
            $modal.fadeIn(300);
            $('body').addClass('ross-modal-open');
        },
        
        closeModal: function(e) {
            if (e) {
                e.preventDefault();
            }
            
            const $modal = $('#ross-template-preview-modal');
            const $iframe = $('#ross-preview-iframe');
            
            // Hide modal
            $modal.fadeOut(300, function() {
                $iframe.attr('src', '');
            });
            
            $('body').removeClass('ross-modal-open');
            this.previewingTemplate = null;
        },
        
        applyTemplate: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const template = $btn.data('template');
            const templateName = $btn.data('name');
            
            // Confirm
            if (!confirm('Apply "' + templateName + '" template to your homepage?\n\nYour current page template will be changed.')) {
                return;
            }
            
            this.executeApply(template, $btn);
        },
        
        applyFromPreview: function(e) {
            e.preventDefault();
            
            if (!this.previewingTemplate) {
                return;
            }
            
            const template = this.previewingTemplate;
            const $btn = $(e.currentTarget);
            
            // Confirm
            if (!confirm('Apply this template to your homepage?')) {
                return;
            }
            
            this.executeApply(template, $btn);
        },
        
        executeApply: function(template, $btn) {
            // Show loading
            $btn.prop('disabled', true);
            const originalText = $btn.html();
            $btn.html('<span class="dashicons dashicons-update"></span> Applying...');
            
            // Find card
            const $card = $('.template-card[data-template="' + template + '"]');
            $card.addClass('loading');
            
            $.ajax({
                url: rossTemplateSwitcher.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ross_apply_homepage_template',
                    nonce: rossTemplateSwitcher.nonce,
                    template: template
                },
                success: function(response) {
                    if (response.success) {
                        // Success notification
                        switcher.showNotice('success', response.data.message + ' <a href="' + response.data.home_url + '" target="_blank">View Homepage →</a>');
                        
                        // Update UI
                        switcher.updateActiveTemplate(template);
                        
                        // Close modal if open
                        switcher.closeModal();
                        
                        // Scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 500);
                    } else {
                        switcher.showNotice('error', response.data || 'Failed to apply template');
                    }
                },
                error: function() {
                    switcher.showNotice('error', 'Network error. Please try again.');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                    $card.removeClass('loading');
                }
            });
        },
        
        updateActiveTemplate: function(newTemplate) {
            // Remove active state from all cards
            $('.template-card').removeClass('active');
            $('.active-indicator').remove();
            $('.active-button').replaceWith(function() {
                const $this = $(this);
                const template = $this.closest('.template-card').data('template');
                return '<button class="button button-secondary ross-preview-template" data-template="' + template + '">' +
                       '<span class="dashicons dashicons-visibility"></span> Preview</button>' +
                       '<button class="button button-primary ross-apply-template" data-template="' + template + '">' +
                       '<span class="dashicons dashicons-download"></span> Apply</button>';
            });
            
            // Add active state to new template
            const $newCard = $('.template-card[data-template="' + newTemplate + '"]');
            $newCard.addClass('active');
            
            // Replace buttons with active button
            $newCard.find('.template-actions').html(
                '<button class="button button-primary active-button" disabled>' +
                '<span class="dashicons dashicons-yes"></span> Active Template</button>'
            );
            
            // Add active indicator
            $newCard.append(
                '<div class="active-indicator">' +
                '<span class="dashicons dashicons-star-filled"></span> Currently Active</div>'
            );
            
            // Update current template badge
            $('.current-template-badge strong').text(
                $newCard.find('.template-name').text()
            );
            
            // Update stored current template
            this.currentTemplate = newTemplate;
        },
        
        showNotice: function(type, message) {
            const $notice = $('<div class="notice notice-' + type + ' is-dismissible">' +
                            '<p>' + message + '</p>' +
                            '<button type="button" class="notice-dismiss">' +
                            '<span class="screen-reader-text">Dismiss</span></button></div>');
            
            $('.wrap h1').after($notice);
            
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Manual dismiss
            $notice.on('click', '.notice-dismiss', function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            });
        }
    };
    
    // Initialize
    switcher.init();
});
