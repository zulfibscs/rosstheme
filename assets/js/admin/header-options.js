jQuery(document).ready(function($) {
    // Initialize color pickers
    $('.color-picker').wpColorPicker();
    
    // Media uploader for logos - with fallback and better error handling
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
    
    // Conditional logic for top bar
    function toggleTopBarFields() {
        var isChecked = $('input[name="ross_theme_header_options[enable_topbar]"]').is(':checked');
        if (isChecked) {
            $('#topbar_left_content').closest('tr').show();
            $('#topbar_bg_color').closest('tr').show();
            $('#topbar_text_color').closest('tr').show();
        } else {
            $('#topbar_left_content').closest('tr').hide();
            $('#topbar_bg_color').closest('tr').hide();
            $('#topbar_text_color').closest('tr').hide();
        }
    }
    
    // Conditional logic for CTA button
    function toggleCTAFields() {
        var isChecked = $('input[name="ross_theme_header_options[enable_cta_button]"]').is(':checked');
        if (isChecked) {
            $('#cta_button_text').closest('tr').show();
            $('#cta_button_color').closest('tr').show();
        } else {
            $('#cta_button_text').closest('tr').hide();
            $('#cta_button_color').closest('tr').hide();
        }
    }
    
    // Conditional logic for header centering based on width
    function toggleHeaderCenterField() {
        var headerWidth = $('select[name="ross_theme_header_options[header_width]"]').val();
        var centerRow = $('input[name="ross_theme_header_options[header_center]"]').closest('tr');
        var centerInput = $('input[name="ross_theme_header_options[header_center]"]');
        var centerLabel = centerRow.find('label[for="header_center"]');
        var centerDesc = centerRow.find('.description');
        
        if (headerWidth === 'full') {
            // Enable center option
            centerInput.prop('disabled', false);
            centerLabel.css('color', '');
            centerDesc.css('color', '');
            centerRow.show();
        } else {
            // Disable center option
            centerInput.prop('disabled', true).prop('checked', false);
            centerLabel.css('color', '#999');
            centerDesc.css('color', '#666');
            centerRow.show(); // Keep row visible but disabled
        }
    }
    
    // Initialize conditional fields
    toggleTopBarFields();
    toggleCTAFields();
    toggleHeaderCenterField();
    
    // Bind events
    $('input[name="ross_theme_header_options[enable_topbar]"]').on('change', toggleTopBarFields);
    $('input[name="ross_theme_header_options[enable_cta_button]"]').on('change', toggleCTAFields);
    $('select[name="ross_theme_header_options[header_width]"]').on('change', toggleHeaderCenterField);
});