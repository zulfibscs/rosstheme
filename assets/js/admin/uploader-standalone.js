// Standalone uploader - works without dependencies
(function() {
    'use strict';
    
    function initUploader() {
        var buttons = document.querySelectorAll('.ross-upload-button');
        
        if (buttons.length === 0) {
            return;
        }
        
        // Add click handler to each button
        Array.prototype.forEach.call(buttons, function(button, index) {
            button.addEventListener('click', handleUploadClick);
        });
    }
    
    function handleUploadClick(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var button = e.currentTarget;
        var targetId = button.getAttribute('data-target');
        
        // Check if wp.media is available
        if (!window.wp || !window.wp.media) {
            alert('Error: Media library not available. Please refresh the page.');
            return false;
        }
        
        // Check if target field exists
        var targetInput = document.getElementById(targetId);
        if (!targetInput) {
            alert('Error: Target input field not found: ' + targetId);
            return false;
        }
        
        try {
            var frame = wp.media({
                title: 'Select Image',
                button: {
                    text: 'Select'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                
                if (!selection) {
                    return;
                }
                
                var attachment = selection.first();
                
                if (!attachment) {
                    return;
                }
                
                var data = attachment.toJSON();
                
                if (data.url) {
                    targetInput.value = data.url;
                    
                    // Trigger change event
                    var event = new Event('change', { bubbles: true });
                    targetInput.dispatchEvent(event);
                } else {
                    alert('Error: Could not get image URL');
                }
            });
            
            frame.open();
            
        } catch (err) {
            alert('Error: ' + err.message);
        }
        
        return false;
    }
    
    // Initialize when document is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUploader);
    } else {
        initUploader();
    }
    
    // Also initialize after a short delay to catch any late-loading buttons
    setTimeout(initUploader, 1000);
})();
