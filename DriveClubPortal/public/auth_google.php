<?php
/**
 * Google Authentication Redirect
 * 
 * This page initiates the Google OAuth authentication process.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/google_auth.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Get redirect URL (if any)
$redirectUrl = isset($_GET['redirect']) ? $_GET['redirect'] : '/public/mi-cuenta.php';

// Store redirect URL in session
$_SESSION['auth_redirect'] = $redirectUrl;

// Get Google auth URL
$authUrl = getGoogleAuthUrl();

// Redirect to Google auth page
header('Location: ' . $authUrl);
exit;
