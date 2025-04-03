<?php
/**
 * Logout Page
 * 
 * This page handles user logout by destroying the session.
 */

// Include necessary files
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Logout user
logoutUser();

// Redirect to home page
redirect('/public/index.php');
