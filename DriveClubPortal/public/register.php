<?php
/**
 * Register Redirect Page
 * 
 * This file redirects to the login page with the register parameter.
 */

// Redirect to login page with register parameter
header('Location: /public/login.php?registro=true');
exit;
