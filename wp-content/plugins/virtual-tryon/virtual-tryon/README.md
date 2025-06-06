# Virtual Try-On WordPress Plugin

A WordPress plugin that integrates with the Replicate API to provide virtual clothing try-on functionality.

## Description

This plugin allows your website visitors to virtually try on clothing items using AI. It uses the idm-vton model from Replicate to generate realistic try-on images.

## Features

- Upload a photo of yourself
- Upload an image of a garment
- Describe the garment
- Select the category (upper body, lower body, dresses)
- Generate a realistic try-on image

## Installation

1. Download the plugin zip file
2. Go to your WordPress admin panel > Plugins > Add New
3. Click "Upload Plugin" and select the zip file
4. Activate the plugin

## Configuration

1. Go to WordPress admin panel > Virtual Try-On
2. Enter your Replicate API token (get one from https://replicate.com/account/api-tokens)
3. Save settings

## Usage

1. Add the shortcode `[virtual_tryon]` to any page or post where you want the try-on interface to appear
2. Users can upload their photo and a garment image
3. The plugin will process the images and display the result

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Active Replicate API account and token

## Troubleshooting

- If images fail to upload, check your server's file permissions and PHP upload limits
- If API calls fail, verify your Replicate API token is correct
- For large images, you may need to increase your server's max execution time

## Privacy Notice

This plugin uploads user-provided images to the Replicate API for processing. Make sure to update your privacy policy to inform users about this data processing.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- Uses the idm-vton model by cuuupid on Replicate
- Model URL: https://replicate.com/cuuupid/idm-vton 