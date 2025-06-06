<?php
/**
 * Plugin Name: Virtual Try-On
 * Description: Clothing virtual try-on using Replicate API
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Main plugin class
class Virtual_TryOn {
    
    public function __construct() {
        // Register activation hook
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register shortcode
        add_shortcode('virtual_tryon', array($this, 'tryon_shortcode'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_virtual_tryon_process', array($this, 'process_tryon_ajax'));
        add_action('wp_ajax_nopriv_virtual_tryon_process', array($this, 'process_tryon_ajax'));
    }
    
    // Activation function
    public function activate() {
        // Initialize default settings
        add_option('virtual_tryon_api_token', '');
    }
    
    // Add admin menu
    public function add_admin_menu() {
        add_menu_page(
            'Virtual Try-On Settings',
            'Virtual Try-On',
            'manage_options',
            'virtual-tryon-settings',
            array($this, 'settings_page'),
            'dashicons-admin-customizer',
            100
        );
    }
    
    // Settings page
    public function settings_page() {
        // Save settings if form is submitted
        if (isset($_POST['save_settings']) && check_admin_referer('virtual_tryon_settings')) {
            update_option('virtual_tryon_api_token', sanitize_text_field($_POST['api_token']));
            echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
        }
        
        $api_token = get_option('virtual_tryon_api_token', '');
        ?>
        <div class="wrap">
            <h1>Virtual Try-On Settings</h1>
            <form method="post" action="">
                <?php wp_nonce_field('virtual_tryon_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="api_token">Replicate API Token</label></th>
                        <td>
                            <input type="password" name="api_token" id="api_token" value="<?php echo esc_attr($api_token); ?>" class="regular-text">
                            <p class="description">Enter your Replicate API token. <a href="https://replicate.com/account/api-tokens" target="_blank">Get your token here</a>.</p>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="save_settings" class="button-primary" value="Save Settings">
                </p>
            </form>
        </div>
        <?php
    }
    
    // Shortcode function
    public function tryon_shortcode($atts) {
        // Start output buffering
        ob_start();
        ?>
        <div class="virtual-tryon-container">
            <div class="virtual-tryon-form">
                <h3>Virtual Try-On</h3>
                
                <div class="form-group">
                    <label for="human-image">Your Photo</label>
                    <input type="file" id="human-image" accept="image/*">
                    <p class="description">Upload a photo of yourself</p>
                </div>
                
                <div class="form-group">
                    <label for="garment-image">Garment Image</label>
                    <input type="file" id="garment-image" accept="image/*">
                    <p class="description">Upload an image of the garment</p>
                </div>
                
                <div class="form-group">
                    <label for="garment-description">Garment Description</label>
                    <input type="text" id="garment-description" placeholder="e.g., cute pink top">
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category">
                        <option value="upper_body">Upper Body</option>
                        <option value="lower_body">Lower Body</option>
                        <option value="dresses">Dresses</option>
                    </select>
                </div>
                
                <button id="process-tryon" class="button button-primary">Generate Try-On</button>
                <div id="tryon-message"></div>
            </div>
            
            <div class="virtual-tryon-results">
                <div class="loading-indicator" style="display: none;">Processing...</div>
                <div class="result-images">
                    <div class="original-image">
                        <h4>Original Image</h4>
                        <img id="original-preview" src="" alt="">
                    </div>
                    <div class="result-image">
                        <h4>Try-On Result</h4>
                        <img id="result-preview" src="" alt="">
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    // Enqueue scripts and styles
    public function enqueue_scripts() {
        // Only enqueue on pages where the shortcode is used
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'virtual_tryon')) {
            // Register and enqueue CSS
            wp_register_style(
                'virtual-tryon-style', 
                plugin_dir_url(__FILE__) . 'assets/css/frontend.css',
                array(),
                '1.0.0'
            );
            wp_enqueue_style('virtual-tryon-style');
            
            // Register and enqueue JS
            wp_register_script(
                'virtual-tryon-script',
                plugin_dir_url(__FILE__) . 'assets/js/frontend.js',
                array('jquery'),
                '1.0.0',
                true
            );
            
            // Pass variables to JavaScript
            wp_localize_script('virtual-tryon-script', 'virtualTryOn', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('virtual_tryon_nonce')
            ));
            
            wp_enqueue_script('virtual-tryon-script');
        }
    }
    
    // AJAX handler for processing try-on requests
    public function process_tryon_ajax() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'virtual_tryon_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
        }
        
        // Get API token
        $api_token = get_option('virtual_tryon_api_token', '');
        if (empty($api_token)) {
            wp_send_json_error(array('message' => 'API token not configured. Please set it in the plugin settings.'));
        }
        
        // Get parameters
        $human_img = isset($_POST['human_img']) ? sanitize_text_field($_POST['human_img']) : '';
        $garment_img = isset($_POST['garment_img']) ? sanitize_text_field($_POST['garment_img']) : '';
        $garment_desc = isset($_POST['garment_desc']) ? sanitize_text_field($_POST['garment_desc']) : 'clothing item';
        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : 'upper_body';
        
        if (empty($human_img) || empty($garment_img)) {
            wp_send_json_error(array('message' => 'Missing required images.'));
        }
        
        // Convert data URLs to actual URLs by uploading to media library
        $human_img_url = $this->upload_base64_image($human_img, 'human');
        $garment_img_url = $this->upload_base64_image($garment_img, 'garment');
        
        if (!$human_img_url || !$garment_img_url) {
            wp_send_json_error(array('message' => 'Failed to upload images.'));
        }
        
        // Prepare request data for Replicate API
        $data = array(
            'version' => 'c871bb9b046607b688449ecbae55fd8c6d945e0a1948644bf2361b3d821d3ff4',
            'input' => array(
                'crop' => false,
                'seed' => 42,
                'steps' => 30,
                'category' => $category,
                'fence_dc' => false,
                'garment_des' => $garment_desc,
                'human_img' => $human_img_url,
                'garment_img' => $garment_img_url,
                'mask_only' => false
            )
        );
        
        // Make API request to Replicate
        $response = wp_remote_post('https://api.replicate.com/v1/predictions', array(
            'headers' => array(
                'Authorization' => 'Token ' . $api_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data),
            'timeout' => 60
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }
        
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);
        
        // Check if we need to poll for results
        if (isset($result['id']) && isset($result['status'])) {
            if ($result['status'] === 'starting' || $result['status'] === 'processing') {
                // In a real implementation, you might want to return the ID and have the client poll
                // For simplicity, we'll poll here a few times
                $prediction_id = $result['id'];
                $output_url = $this->poll_prediction_result($prediction_id, $api_token);
                
                if ($output_url) {
                    wp_send_json_success(array('output_url' => $output_url));
                } else {
                    wp_send_json_error(array('message' => 'Processing timed out. Please try again.'));
                }
            } elseif ($result['status'] === 'succeeded' && isset($result['output'])) {
                wp_send_json_success(array('output_url' => $result['output']));
            } else {
                wp_send_json_error(array('message' => 'API returned an unexpected status: ' . $result['status']));
            }
        } else {
            wp_send_json_error(array('message' => 'API returned an unexpected response.', 'response' => $result));
        }
    }
    
    // Helper function to poll for prediction results
    private function poll_prediction_result($prediction_id, $api_token) {
        $max_attempts = 10;
        $attempt = 0;
        
        while ($attempt < $max_attempts) {
            $response = wp_remote_get('https://api.replicate.com/v1/predictions/' . $prediction_id, array(
                'headers' => array(
                    'Authorization' => 'Token ' . $api_token,
                    'Content-Type' => 'application/json'
                ),
                'timeout' => 10
            ));
            
            if (!is_wp_error($response)) {
                $body = wp_remote_retrieve_body($response);
                $result = json_decode($body, true);
                
                if (isset($result['status']) && $result['status'] === 'succeeded' && isset($result['output'])) {
                    return $result['output'];
                } elseif (isset($result['status']) && $result['status'] === 'failed') {
                    return false;
                }
            }
            
            $attempt++;
            sleep(3); // Wait 3 seconds before polling again
        }
        
        return false; // Timed out
    }
    
    // Helper function to upload base64 image to media library
    private function upload_base64_image($base64_img, $prefix = 'img') {
        // Remove the data URL prefix
        $img_parts = explode(',', $base64_img);
        $img_data = isset($img_parts[1]) ? base64_decode($img_parts[1]) : base64_decode($img_parts[0]);
        
        // Get file type from the data URL
        $img_type = '';
        if (isset($img_parts[0]) && preg_match('/data:image\/(.*?);base64/', $img_parts[0], $matches)) {
            $img_type = $matches[1];
        } else {
            $img_type = 'jpeg'; // Default to JPEG
        }
        
        // Generate a unique filename
        $filename = $prefix . '-' . uniqid() . '.' . $img_type;
        
        // Get WordPress upload directory
        $upload_dir = wp_upload_dir();
        $upload_path = $upload_dir['path'] . '/' . $filename;
        $upload_url = $upload_dir['url'] . '/' . $filename;
        
        // Save the file
        $result = file_put_contents($upload_path, $img_data);
        
        if (!$result) {
            return false;
        }
        
        // Optional: Add the image to the media library
        $attachment = array(
            'post_mime_type' => 'image/' . $img_type,
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attach_id = wp_insert_attachment($attachment, $upload_path);
        
        if ($attach_id) {
            // Include image.php to use wp_generate_attachment_metadata()
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            
            // Generate metadata and update attachment
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload_path);
            wp_update_attachment_metadata($attach_id, $attach_data);
        }
        
        return $upload_url;
    }
}

// Initialize the plugin
$virtual_tryon = new Virtual_TryOn(); 