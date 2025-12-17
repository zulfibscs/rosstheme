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
    
    // Initialize conditional fields
    toggleTopBarFields();
    toggleCTAFields();
    
    // Bind events
    $('input[name="ross_theme_header_options[enable_topbar]"]').on('change', toggleTopBarFields);
    $('input[name="ross_theme_header_options[enable_cta_button]"]').on('change', toggleCTAFields);

    // Elementor-style responsive device switcher
    var currentDevice = 'desktop';

    // Device switcher button clicks
    $('.ross-device-switcher-btn').on('click', function() {
        var device = $(this).data('device');

        // Update active button
        $('.ross-device-switcher-btn').removeClass('active');
        $(this).addClass('active');

        // Update current device
        currentDevice = device;

        // Show/hide inputs based on device
        $('.ross-responsive-input').hide();
        $('.ross-device-' + device).show();

        // Update device indicators
        $('.ross-device-indicator').hide();
        $('.ross-device-' + device).show();
    });

    // Initialize device switcher (show desktop by default)
    $('.ross-device-desktop').show();
    $('.ross-device-indicator.ross-device-desktop').show();
    $('.ross-device-mobile').hide();
    $('.ross-device-indicator.ross-device-mobile').hide();
});