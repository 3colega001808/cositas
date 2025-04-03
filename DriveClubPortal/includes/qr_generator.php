<?php
/**
 * QR Code Generator
 * 
 * This file contains functions for generating QR codes.
 */

/**
 * Generate QR code using endroid/qr-code library
 * 
 * @param string $data Data to encode in QR code
 * @param int $id ID for the file name
 * @param int $size QR code size in pixels
 * @return string|false Path to QR code or false on failure
 */
function generateQRCode($data, $id, $size = 300) {
    // We'll use a JavaScript library for generating QR codes in the browser
    // but this function will create a placeholder for storing the QR code path in the database
    
    // Create directory if it doesn't exist
    $dir = __DIR__ . '/../public/uploads/qrcodes';
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $path = '/uploads/qrcodes/reservation_' . $id . '_' . time() . '.png';
    $fullPath = __DIR__ . '/../public' . $path;
    
    // Create a blank image as a placeholder
    // In a real implementation, we would use a QR code library to generate the actual QR code
    $img = imagecreatetruecolor($size, $size);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    
    // Fill background with white
    imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $white);
    
    // Draw a border
    imagerectangle($img, 0, 0, $size - 1, $size - 1, $black);
    
    // Add a message
    $text = "QR Code Placeholder";
    $font = 3;
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = ($size - $textWidth) / 2;
    $y = ($size - $textHeight) / 2;
    
    imagestring($img, $font, $x, $y, $text, $black);
    
    // Save the placeholder
    imagepng($img, $fullPath);
    imagedestroy($img);
    
    return $path;
}

/**
 * Generate QR code in data URI format (for JavaScript)
 * 
 * @param string $data Data to encode in QR code
 * @param int $size QR code size in pixels
 * @return string Data URI
 */
function generateQRCodeDataURI($data, $size = 300) {
    // This is a placeholder function for server-side QR code generation
    // The actual QR code generation will be done using a JavaScript library
    
    return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFeAJ5/IL5RAAAAABJRU5ErkJggg==';
}
