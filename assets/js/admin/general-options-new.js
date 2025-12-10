jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.color-picker').wpColorPicker();
    
    // Media uploader for logos and favicon - with fallback and better error handling
    $(document).on('click', '.ross-upload-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var button = $(this);
        var target = button.data('target');
        
        // Validation checks
        if (!target) {
            alert('Error: No target field specified for this upload button. Please contact support.');
            return false;
        }
        
        if (typeof wp === 'undefined') {
            alert('Error: WordPress object is not loaded. Please refresh the page.');
            return false;
        }
        
        if (typeof wp.media === 'undefined') {
            alert('Error: Media library is not available. Please ensure you are logged in and have permission to upload files.');
            return false;
        }
        
        // Create media uploader instance
        var custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Image',
            button: {
                text: 'Select Image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // Handle selection
        custom_uploader.on('select', function() {
            try {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                
                if (attachment && attachment.url) {
                    $('#' + target).val(attachment.url);
                    $('#' + target).trigger('change');
                } else {
                    alert('Error: Could not retrieve image URL. Please try again.');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        });
        
        // Open media library
        try {
            custom_uploader.open();
        } catch (err) {
            alert('Error: Could not open media library: ' + err.message);
        }
        
        return false;
    });
    
    // Conditional logic for preloader
    function togglePreloaderFields() {
        var isChecked = $('input[name="ross_theme_general_options[enable_preloader]"]').is(':checked');
    }
    
    // Initialize conditional fields
    togglePreloaderFields();
    
    // Bind events
    $('input[name="ross_theme_general_options[enable_preloader]"]').on('change', togglePreloaderFields);
});
