jQuery(document).ready(function($) {
    // Variables to store image data
    let humanImageData = null;
    let garmentImageData = null;
    let pollingInterval = null;
    
    // Handle human image upload
    $('#human-image').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(event) {
            humanImageData = event.target.result;
            $('#original-preview').attr('src', humanImageData);
        };
        reader.readAsDataURL(file);
    });
    
    // Handle garment image upload
    $('#garment-image').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(event) {
            garmentImageData = event.target.result;
        };
        reader.readAsDataURL(file);
    });
    
    // Process try-on button click
    $('#process-tryon').on('click', function() {
        // Clear any existing polling
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
        
        // Validate inputs
        if (!humanImageData) {
            showMessage('Please upload your photo.', 'error');
            return;
        }
        
        if (!garmentImageData) {
            showMessage('Please upload a garment image.', 'error');
            return;
        }
        
        const garmentDesc = $('#garment-description').val() || 'clothing item';
        const category = $('#category').val();
        
        // Show initial loading state
        updateLoadingMessage('Starting the process...');
        $('#result-preview').attr('src', '');
        $('#tryon-message').removeClass('error success').empty();
        
        // Disable the generate button
        $('#process-tryon').prop('disabled', true);
        
        // Make the AJAX call to our WordPress backend
        $.ajax({
            url: virtualTryOn.ajaxUrl,
            type: 'POST',
            data: {
                action: 'virtual_tryon_process',
                nonce: virtualTryOn.nonce,
                human_img: humanImageData,
                garment_img: garmentImageData,
                garment_desc: garmentDesc,
                category: category
            },
            success: function(response) {
                if (!response.success) {
                    handleError(response.data.message || 'Unknown error occurred');
                    return;
                }

                if (response.data.output_url) {
                    // If we got a direct result
                    handleSuccess(response.data.output_url);
                } else if (response.data.prediction_id) {
                    // Start polling for results
                    let attempts = 0;
                    pollingInterval = setInterval(function() {
                        attempts++;
                        
                        // Update progress message based on attempts
                        if (attempts === 1) {
                            updateLoadingMessage('Initializing the model...');
                        } else if (attempts <= 5) {
                            updateLoadingMessage('Starting up the prediction...');
                        } else {
                            updateLoadingMessage('Processing your images... (Attempt ' + attempts + ' of 30)');
                        }
                        
                        $.ajax({
                            url: virtualTryOn.ajaxUrl,
                            type: 'POST',
                            data: {
                                action: 'virtual_tryon_check_status',
                                nonce: virtualTryOn.nonce,
                                prediction_id: response.data.prediction_id
                            },
                            success: function(pollResponse) {
                                if (!pollResponse.success) {
                                    handleError(pollResponse.data.message || 'Failed to check prediction status');
                                    return;
                                }

                                const status = pollResponse.data.status;
                                switch(status) {
                                    case 'starting':
                                        updateLoadingMessage('Starting up the model...');
                                        break;
                                    case 'processing':
                                        updateLoadingMessage('Processing your images... This typically takes about 30 seconds.');
                                        break;
                                    case 'succeeded':
                                        clearInterval(pollingInterval);
                                        handleSuccess(pollResponse.data.output_url);
                                        break;
                                    case 'failed':
                                        handleError(pollResponse.data.error || 'Processing failed');
                                        break;
                                    case 'canceled':
                                        handleError('The process was canceled');
                                        break;
                                    default:
                                        handleError('Unknown status: ' + status);
                                }
                            },
                            error: function(xhr, status, error) {
                                handleError('Error checking prediction status: ' + error);
                            }
                        });
                        
                        // Stop polling after 30 attempts (60 seconds)
                        if (attempts >= 30) {
                            handleError('Process timed out. Please try again.');
                        }
                    }, 2000); // Poll every 2 seconds
                } else {
                    handleError('Invalid response from server');
                }
            },
            error: function(xhr, status, error) {
                handleError('Error: ' + error);
            },
            timeout: 120000 // Set timeout to 2 minutes to account for processing time
        });
    });
    
    // Helper function to show messages
    function showMessage(message, type) {
        $('#tryon-message')
            .removeClass('error success')
            .addClass(type)
            .html(message);
    }

    // Helper function to update loading message
    function updateLoadingMessage(message) {
        $('.loading-indicator')
            .html(message)
            .show();
    }

    // Helper function to handle errors
    function handleError(message) {
        clearInterval(pollingInterval);
        $('.loading-indicator').hide();
        $('#process-tryon').prop('disabled', false);
        showMessage(message, 'error');
    }

    // Helper function to handle success
    function handleSuccess(outputUrl) {
        $('.loading-indicator').hide();
        $('#process-tryon').prop('disabled', false);
        $('#result-preview').attr('src', outputUrl);
        showMessage('Try-on completed successfully!', 'success');
    }
}); 