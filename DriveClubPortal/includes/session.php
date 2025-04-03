<?php
/**
 * Session management functions
 * 
 * This file contains functions for session management.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set a session value
 * 
 * @param string $key Session key
 * @param mixed $value Session value
 * @return void
 */
function setSessionValue($key, $value) {
    $_SESSION[$key] = $value;
}

/**
 * Get a session value
 * 
 * @param string $key Session key
 * @param mixed $default Default value if key doesn't exist
 * @return mixed Session value or default
 */
function getSessionValue($key, $default = null) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

/**
 * Remove a session value
 * 
 * @param string $key Session key
 * @return void
 */
function removeSessionValue($key) {
    if (isset($_SESSION[$key])) {
        unset($_SESSION[$key]);
    }
}

/**
 * Check if a session value exists
 * 
 * @param string $key Session key
 * @return bool True if exists, false otherwise
 */
function hasSessionValue($key) {
    return isset($_SESSION[$key]);
}

/**
 * Set a flash message (displayed once)
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 * @return void
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear all flash messages
 * 
 * @return array Flash messages
 */
function getFlashMessages() {
    $messages = isset($_SESSION['flash_messages']) ? $_SESSION['flash_messages'] : [];
    unset($_SESSION['flash_messages']);
    
    return $messages;
}

/**
 * Display flash messages
 * 
 * @return string HTML for flash messages
 */
function displayFlashMessages() {
    $messages = getFlashMessages();
    $output = '';
    
    foreach ($messages as $message) {
        $alertClass = 'alert-info';
        
        switch ($message['type']) {
            case 'success':
                $alertClass = 'alert-success';
                break;
            case 'error':
                $alertClass = 'alert-danger';
                break;
            case 'warning':
                $alertClass = 'alert-warning';
                break;
        }
        
        $output .= '
        <div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($message['message']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
    
    return $output;
}

/**
 * Get CSRF token - creates one if it doesn't exist
 * 
 * @return string CSRF token
 */
function getCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string $token Token to validate
 * @return bool True if valid, false otherwise
 */
function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set a secure cookie
 * 
 * @param string $name Cookie name
 * @param string $value Cookie value
 * @param int $expiry Expiry time in seconds from now
 * @param bool $httpOnly HTTP only flag
 * @return bool True on success
 */
function setSecureCookie($name, $value, $expiry = 0, $httpOnly = true) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $path = '/';
    
    return setcookie(
        $name,
        $value,
        $expiry > 0 ? time() + $expiry : 0,
        $path,
        '',  // domain
        $secure,
        $httpOnly
    );
}

/**
 * Get cookie value
 * 
 * @param string $name Cookie name
 * @param mixed $default Default value if cookie doesn't exist
 * @return mixed Cookie value or default
 */
function getCookieValue($name, $default = null) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

/**
 * Delete a cookie
 * 
 * @param string $name Cookie name
 * @return bool True on success
 */
function deleteCookie($name) {
    return setcookie($name, '', time() - 3600, '/');
}

/**
 * Check if the user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Track user session for analytics
 * 
 * @return void
 */
function trackUserSession() {
    // Set or update session start time
    if (!isset($_SESSION['session_start_time'])) {
        $_SESSION['session_start_time'] = time();
    }
    
    // Set or update last activity time
    $_SESSION['last_activity_time'] = time();
    
    // Track pages visited
    if (!isset($_SESSION['pages_visited'])) {
        $_SESSION['pages_visited'] = [];
    }
    
    $currentPage = $_SERVER['REQUEST_URI'];
    
    if (!in_array($currentPage, $_SESSION['pages_visited'])) {
        $_SESSION['pages_visited'][] = $currentPage;
    }
    
    // Track session duration
    $_SESSION['session_duration'] = time() - $_SESSION['session_start_time'];
}
