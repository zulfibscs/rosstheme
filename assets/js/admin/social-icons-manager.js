/**
 * Social Icons Manager V2 - Admin UI Controller
 */
(function($) {
    'use strict';
    
    // Popular Font Awesome icons for social media
    const popularIcons = [
        'fab fa-discord',
        'fab fa-behance',
        'fab fa-dribbble',
        'fab fa-medium',
        'fab fa-reddit',
        'fab fa-snapchat',
        'fab fa-spotify',
        'fab fa-twitch',
        'fab fa-vimeo',
        'fab fa-youtube',
        'fab fa-github',
        'fab fa-gitlab',
        'fab fa-stack-overflow',
        'fab fa-slack',
        'fab fa-skype',
        'fab fa-whatsapp',
        'fab fa-telegram',
        'fab fa-wechat',
        'fab fa-weibo',
        'fab fa-tumblr',
        'fab fa-soundcloud',
        'fab fa-patreon',
        'fab fa-kickstarter',
        'fab fa-product-hunt',
        'fab fa-bandcamp',
        'fas fa-rss',
        'fas fa-envelope',
        'fas fa-phone',
        'fas fa-link',
        'fas fa-globe'
    ];
    
    $(document).ready(function() {
        initializePlatformToggles();
        initializeIconPicker();
        initializeCustomPlatformColor();
    });
    
    /**
     * Platform Toggle Switches
     */
    function initializePlatformToggles() {
        $('.platform-toggle').on('change', function() {
            const $card = $(this).closest('.social-platform-card');
            const isEnabled = $(this).is(':checked');
            
            if (isEnabled) {
                $card.removeClass('is-disabled').addClass('is-enabled');
                $card.find('input[type="url"], input[type="text"], button').prop('disabled', false);
            } else {
                $card.removeClass('is-enabled').addClass('is-disabled');
                $card.find('input[type="url"], input[type="text"], button').not('.platform-toggle').prop('disabled', true);
            }
        });
    }
    
    /**
     * Icon Picker Modal
     */
    function initializeIconPicker() {
        const $modal = $('#ross-icon-picker-modal');
        const $grid = $('#icon-picker-grid');
        const $search = $('#icon-search');
        const $iconInput = $('#custom-social-icon-input');
        const $iconPreview = $('#custom-platform-icon-preview');
        
        // Populate icon grid
        populateIconGrid();
        
        // Open modal
        $('#open-icon-picker').on('click', function(e) {
            e.preventDefault();
            $modal.fadeIn(200);
            $search.val('').trigger('input');
        });
        
        // Close modal
        $('.ross-modal-close, .ross-modal').on('click', function(e) {
            if (e.target === this) {
                $modal.fadeOut(200);
            }
        });
        
        // Icon search
        $search.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $('.icon-option').each(function() {
                const iconClass = $(this).data('icon');
                if (iconClass.toLowerCase().includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        // Icon selection
        $grid.on('click', '.icon-option', function() {
            const iconClass = $(this).data('icon');
            
            // Update selection
            $('.icon-option').removeClass('selected');
            $(this).addClass('selected');
            
            // Update hidden input
            $iconInput.val(iconClass);
            
            // Update preview
            $iconPreview.attr('class', iconClass);
            $('#open-icon-picker i').attr('class', iconClass);
            
            // Close modal
            setTimeout(() => {
                $modal.fadeOut(200);
            }, 300);
        });
        
        function populateIconGrid() {
            const currentIcon = $iconInput.val();
            
            popularIcons.forEach(iconClass => {
                const isSelected = iconClass === currentIcon ? 'selected' : '';
                const $icon = $('<div>', {
                    class: `icon-option ${isSelected}`,
                    'data-icon': iconClass,
                    html: `<i class="${iconClass}"></i>`
                });
                $grid.append($icon);
            });
        }
    }
    
    /**
     * Custom Platform Color Picker
     */
    function initializeCustomPlatformColor() {
        // Initialize color picker for custom platform
        if ($.fn.wpColorPicker) {
            $('input[name="ross_theme_footer_options[custom_social_color]"]').wpColorPicker({
                change: function(event, ui) {
                    updateCustomIconColor(ui.color.toString());
                },
                clear: function() {
                    updateCustomIconColor('#666666');
                }
            });
        }
        
        function updateCustomIconColor(color) {
            $('.platform-icon--custom').css('background-color', color);
        }
    }
    
})(jQuery);
