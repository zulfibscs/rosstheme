// Test version - minimal logo uploader
(function() {
    'use strict';
    
    // Wait for DOM and jQuery to be ready
    if (typeof jQuery !== 'undefined') {
        jQuery(document).ready(function($) {
            // Find all upload buttons
            var uploadButtons = document.querySelectorAll('.ross-upload-button');
            
            // Attach click handler to each button
            uploadButtons.forEach(function(button, index) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var targetId = this.getAttribute('data-target');
                    
                    if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                        alert('Media library not available. Please refresh the page.');
                        return false;
                    }
                    
                    try {
                        var frame = wp.media({
                            title: 'Select Image',
                            button: {
                                text: 'Select'
                            },
                            multiple: false
                        });
                        
                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();
                            
                            if (attachment.url) {
                                var input = document.getElementById(targetId);
                                if (input) {
                                    input.value = attachment.url;
                                }
                            }
                        });
                        
                        frame.open();
                    } catch(err) {
                        alert('Error: ' + err.message);
                    }
                    
                    return false;
                });
            });
        });
    }
})();
