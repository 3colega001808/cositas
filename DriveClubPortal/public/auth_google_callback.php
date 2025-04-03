<?php
/**
 * Google Authentication Callback
 * 
 * This page handles the Google OAuth callback after authentication.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/google_auth.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Get redirect URL from session (default to account page)
$redirectUrl = $_SESSION['auth_redirect'] ?? '/public/mi-cuenta.php';
unset($_SESSION['auth_redirect']); // Clear from session

// Check if auth code is present
if (!isset($_GET['code'])) {
    setFlashMessage('error', 'Error en la autenticación con Google. Por favor, inténtalo de nuevo.');
    redirect('/public/login.php');
}

// Process Google callback
$authCode = $_GET['code'];
$userData = processGoogleCallback($authCode);

if (!$userData) {
    setFlashMessage('error', 'Error en la autenticación con Google. Por favor, inténtalo de nuevo.');
    redirect('/public/login.php');
}

// Check if user exists by Google ID
$existingUser = getUserByGoogleId($userData['google_id']);

// Process login or registration
if ($existingUser) {
    // User exists, log them in
    $_SESSION['user_id'] = $existingUser['id'];
    
    // Update last login time
    updateData(
        'usuarios',
        ['ultimo_login' => date('Y-m-d H:i:s')],
        'id = ?',
        [$existingUser['id']]
    );
    
    setFlashMessage('success', '¡Bienvenido de nuevo, ' . $existingUser['nombre'] . '!');
} else {
    // Create or update user
    $userId = createOrUpdateGoogleUser($userData);
    
    if (!$userId) {
        setFlashMessage('error', 'Error al procesar el registro con Google. Por favor, inténtalo de nuevo.');
        redirect('/public/login.php');
    }
    
    // Log in the new user
    $_SESSION['user_id'] = $userId;
    
    // Get user data
    $user = getUserById($userId);
    
    // Send welcome email for new users
    if (!isset($user['ultimo_login']) || empty($user['ultimo_login'])) {
        sendWelcomeEmail($userData['email'], $userData['name']);
    }
    
    setFlashMessage('success', '¡Bienvenido a DriveClub, ' . $userData['name'] . '!');
}

// Redirect to destination
redirect($redirectUrl);
