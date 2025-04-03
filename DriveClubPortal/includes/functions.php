<?php
/**
 * General functions
 * 
 * This file contains general utility functions for the DriveClub application.
 */

/**
 * Generate a random token
 * 
 * @param int $length Token length (default: 32)
 * @return string Random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Format price with Euro symbol
 * 
 * @param float $price Price to format
 * @return string Formatted price
 */
function formatPrice($price) {
    return '€' . number_format($price, 2, ',', '.');
}

/**
 * Format date to Spanish format
 * 
 * @param string $date Date in Y-m-d format
 * @return string Formatted date (d/m/Y)
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

/**
 * Get URL parameters as array
 * 
 * @return array URL parameters
 */
function getUrlParams() {
    $params = [];
    $queryString = $_SERVER['QUERY_STRING'] ?? '';
    
    if (!empty($queryString)) {
        parse_str($queryString, $params);
    }
    
    return $params;
}

/**
 * Build URL with parameters
 * 
 * @param string $base Base URL
 * @param array $params URL parameters
 * @return string URL with parameters
 */
function buildUrl($base, $params = []) {
    if (empty($params)) {
        return $base;
    }
    
    $query = http_build_query($params);
    $separator = (strpos($base, '?') !== false) ? '&' : '?';
    
    return $base . $separator . $query;
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @param int $statusCode HTTP status code (default: 302)
 * @return void
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

/**
 * Display error message
 * 
 * @param string $message Error message
 * @return string HTML for error message
 */
function displayError($message) {
    return '
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

/**
 * Display success message
 * 
 * @param string $message Success message
 * @return string HTML for success message
 */
function displaySuccess($message) {
    return '
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        ' . htmlspecialchars($message) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

/**
 * Check if a string starts with a specific substring
 * 
 * @param string $haystack The string to search in
 * @param string $needle The substring to search for
 * @return bool True if starts with the substring, false otherwise
 */
function startsWith($haystack, $needle) {
    return substr($haystack, 0, strlen($needle)) === $needle;
}

/**
 * Check if a string ends with a specific substring
 * 
 * @param string $haystack The string to search in
 * @param string $needle The substring to search for
 * @return bool True if ends with the substring, false otherwise
 */
function endsWith($haystack, $needle) {
    $length = strlen($needle);
    if ($length === 0) {
        return true;
    }
    return substr($haystack, -$length) === $needle;
}

/**
 * Validate email format
 * 
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * 
 * @param string $password Password to validate
 * @return array [isValid, message]
 */
function validatePassword($password) {
    $minLength = 8;
    
    if (strlen($password) < $minLength) {
        return [false, "La contraseña debe tener al menos $minLength caracteres"];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return [false, "La contraseña debe incluir al menos una letra mayúscula"];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return [false, "La contraseña debe incluir al menos un número"];
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return [false, "La contraseña debe incluir al menos un carácter especial"];
    }
    
    return [true, "Contraseña válida"];
}

/**
 * Sanitize input to prevent XSS
 * 
 * @param string $input Input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Clean data for database insertion
 * 
 * @param string $input Input to clean
 * @return string Cleaned input
 */
function cleanInput($input) {
    $input = trim($input);
    
    // If the input is suspected to contain HTML, use a more strict approach
    if (strpos($input, '<') !== false || strpos($input, '>') !== false) {
        $input = filter_var($input, FILTER_SANITIZE_STRING);
    }
    
    return $input;
}

/**
 * Save image from base64 data
 * 
 * @param string $base64Data Base64 encoded image data
 * @param string $outputDirectory Directory to save image
 * @param string $fileName Optional file name
 * @return string|false Path to saved image or false on failure
 */
function saveBase64Image($base64Data, $outputDirectory, $fileName = '') {
    // Extract the base64 data part
    if (strpos($base64Data, ';base64,') !== false) {
        list(, $base64Data) = explode(';base64,', $base64Data);
        $base64Data = str_replace(' ', '+', $base64Data);
    }
    
    // Decode base64 data
    $decodedData = base64_decode($base64Data);
    
    if ($decodedData === false) {
        return false;
    }
    
    // Ensure directory exists
    if (!is_dir($outputDirectory)) {
        mkdir($outputDirectory, 0755, true);
    }
    
    // Generate file name if not provided
    if (empty($fileName)) {
        $fileName = uniqid() . '.png';
    }
    
    $filePath = $outputDirectory . '/' . $fileName;
    
    // Save file
    if (file_put_contents($filePath, $decodedData)) {
        return $filePath;
    }
    
    return false;
}

/**
 * Convert a date from one format to another
 * 
 * @param string $date Date to convert
 * @param string $fromFormat Current format
 * @param string $toFormat Target format
 * @return string Converted date
 */
function convertDateFormat($date, $fromFormat, $toFormat) {
    $dateTime = DateTime::createFromFormat($fromFormat, $date);
    
    if ($dateTime === false) {
        return $date;
    }
    
    return $dateTime->format($toFormat);
}

/**
 * Calculate days between two dates
 * 
 * @param string $startDate Start date (Y-m-d format)
 * @param string $endDate End date (Y-m-d format)
 * @return int Number of days
 */
function calculateDateDifference($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = $start->diff($end);
    
    return $interval->days;
}

/**
 * Get current page URL
 * 
 * @return string Current page URL
 */
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Check if a request is AJAX
 * 
 * @return bool True if AJAX request, false otherwise
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
