/**
 * Homepage Templates Admin JS
 * Handles template selection, preview, and application
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Filter templates by category
        $('.ross-filter-btn').on('click', function() {
            const category = $(this).data('category');
            
            // Update active button
            $('.ross-filter-btn').removeClass('active');
            $(this).addClass('active');
            
            // Filter cards
            if (category === 'all') {
                $('.ross-template-card').fadeIn(300);
            } else {
                $('.ross-template-card').hide();
                $('.ross-template-card[data-category="' + category + '"]').fadeIn(300);
            }
        });
        
        // Apply template
        $('.ross-apply-template').on('click', function() {
            const $button = $(this);
            const templateId = $button.data('template');
            
            if ($button.prop('disabled')) {
                return;
            }
            
            // Confirm action
            if (!confirm('Apply this homepage template? Your current homepage will be replaced.')) {
                return;
            }
            
            // Disable button and show loading
            $button.prop('disabled', true)
                   .text(rossHomepageTemplates.strings.applying);
            
            // AJAX request
            $.ajax({
                url: rossHomepageTemplates.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ross_apply_homepage_template',
                    nonce: rossHomepageTemplates.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        showNotice('success', response.data.message);
                        
                        // Reload page to update UI
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotice('error', response.data.message || rossHomepageTemplates.strings.error);
                        $button.prop('disabled', false)
                               .text('Apply Template');
                    }
                },
                error: function() {
                    showNotice('error', rossHomepageTemplates.strings.error);
                    $button.prop('disabled', false)
                           .text('Apply Template');
                }
            });
        });
        
        // Reset template
        $('.ross-reset-template').on('click', function() {
            const $button = $(this);
            const templateId = $button.data('template');
            
            if (!confirm(rossHomepageTemplates.strings.confirmReset)) {
                return;
            }
            
            $button.prop('disabled', true)
                   .text('Resetting...');
            
            $.ajax({
                url: rossHomepageTemplates.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'ross_reset_homepage_template',
                    nonce: rossHomepageTemplates.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        showNotice('success', response.data.message);
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showNotice('error', response.data.message || 'Error resetting template');
                        $button.prop('disabled', false)
                               .text('Reset to Default');
                    }
                },
                error: function() {
                    showNotice('error', 'Error resetting template');
                    $button.prop('disabled', false)
                           .text('Reset to Default');
                }
            });
        });
        
        /**
         * Show admin notice
         */
        function showNotice(type, message) {
            const noticeClass = type === 'success' ? 'notice-success' : 'notice-error';
            const $notice = $('<div class="notice ' + noticeClass + ' is-dismissible"><p>' + message + '</p></div>');
            
            $('.ross-homepage-templates-wrap h1').after($notice);
            
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        }
        
    });
    
})(jQuery);
