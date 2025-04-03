<?php
/**
 * Authentication functions
 * 
 * This file contains functions for user authentication.
 */

// Include database connection
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Register a new user
 * 
 * @param array $userData User data
 * @return array [status, message, user_id]
 */
function registerUser($userData) {
    // Validate email
    if (!isValidEmail($userData['email'])) {
        return [false, "El email no es válido", null];
    }
    
    // Check if email already exists
    $existingUser = fetchOne(
        "SELECT id FROM usuarios WHERE email = ?",
        [$userData['email']],
        's'
    );
    
    if ($existingUser) {
        return [false, "Este email ya está registrado", null];
    }
    
    // Validate password
    list($isValidPassword, $passwordMessage) = validatePassword($userData['password']);
    
    if (!$isValidPassword) {
        return [false, $passwordMessage, null];
    }
    
    // Hash password
    $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    // Prepare data for insertion
    $insertData = [
        'email' => $userData['email'],
        'password' => $passwordHash,
        'nombre' => $userData['nombre'],
        'apellidos' => $userData['apellidos'],
        'telefono' => $userData['telefono'] ?? null,
        'rol_id' => 2, // Regular user role
        'activo' => 1,
        'fecha_creacion' => date('Y-m-d H:i:s'),
        'fecha_actualizacion' => date('Y-m-d H:i:s')
    ];
    
    // Insert user
    $userId = insertData('usuarios', $insertData);
    
    if (!$userId) {
        return [false, "Error al registrar el usuario", null];
    }
    
    return [true, "Usuario registrado correctamente", $userId];
}

/**
 * Authenticate a user
 * 
 * @param string $email User email
 * @param string $password User password
 * @return array [status, message, user_id]
 */
function loginUser($email, $password) {
    // Validate email
    if (!isValidEmail($email)) {
        return [false, "Email o contraseña incorrectos", null];
    }
    
    // Get user by email
    $user = fetchOne(
        "SELECT id, password, nombre, apellidos, activo FROM usuarios WHERE email = ?",
        [$email],
        's'
    );
    
    if (!$user) {
        return [false, "Email o contraseña incorrectos", null];
    }
    
    // Check if user is active
    if ($user['activo'] != 1) {
        return [false, "Esta cuenta está desactivada", null];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return [false, "Email o contraseña incorrectos", null];
    }
    
    // Update last login time
    updateData(
        'usuarios',
        ['ultimo_login' => date('Y-m-d H:i:s')],
        'id = ?',
        [$user['id']]
    );
    
    return [true, "Inicio de sesión exitoso", $user['id']];
}

/**
 * Request password reset
 * 
 * @param string $email User email
 * @return array [status, message]
 */
function requestPasswordReset($email) {
    // Validate email
    if (!isValidEmail($email)) {
        return [false, "El email no es válido"];
    }
    
    // Get user by email
    $user = fetchOne(
        "SELECT id, nombre, apellidos FROM usuarios WHERE email = ? AND activo = 1",
        [$email],
        's'
    );
    
    if (!$user) {
        // Don't reveal that the email doesn't exist for security reasons
        return [true, "Si tu email está registrado, recibirás un enlace para restablecer tu contraseña"];
    }
    
    // Generate reset token
    $resetToken = generateToken();
    $resetTokenExpiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
    
    // Update user with reset token
    $updateData = [
        'token_reseteo' => $resetToken,
        'token_reseteo_expiracion' => $resetTokenExpiration
    ];
    
    $updated = updateData(
        'usuarios',
        $updateData,
        'id = ?',
        [$user['id']]
    );
    
    if (!$updated) {
        return [false, "Error al procesar la solicitud"];
    }
    
    // Send reset email
    require_once __DIR__ . '/../config/email.php';
    $emailSent = sendPasswordResetEmail($email, $user['nombre'], $resetToken);
    
    if (!$emailSent) {
        return [false, "Error al enviar el email de restablecimiento"];
    }
    
    return [true, "Si tu email está registrado, recibirás un enlace para restablecer tu contraseña"];
}

/**
 * Validate reset token
 * 
 * @param string $token Reset token
 * @return array|null User data if valid, null otherwise
 */
function validateResetToken($token) {
    if (empty($token)) {
        return null;
    }
    
    // Get user by token
    $user = fetchOne(
        "SELECT id, nombre, email FROM usuarios WHERE token_reseteo = ? AND token_reseteo_expiracion > NOW() AND activo = 1",
        [$token],
        's'
    );
    
    return $user;
}

/**
 * Reset password
 * 
 * @param string $token Reset token
 * @param string $newPassword New password
 * @return array [status, message]
 */
function resetPassword($token, $newPassword) {
    // Validate token
    $user = validateResetToken($token);
    
    if (!$user) {
        return [false, "Token inválido o expirado"];
    }
    
    // Validate password
    list($isValidPassword, $passwordMessage) = validatePassword($newPassword);
    
    if (!$isValidPassword) {
        return [false, $passwordMessage];
    }
    
    // Hash new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update user password and clear token
    $updateData = [
        'password' => $passwordHash,
        'token_reseteo' => null,
        'token_reseteo_expiracion' => null,
        'fecha_actualizacion' => date('Y-m-d H:i:s')
    ];
    
    $updated = updateData(
        'usuarios',
        $updateData,
        'id = ?',
        [$user['id']]
    );
    
    if (!$updated) {
        return [false, "Error al restablecer la contraseña"];
    }
    
    return [true, "Contraseña restablecida correctamente"];
}

/**
 * Change user password
 * 
 * @param int $userId User ID
 * @param string $currentPassword Current password
 * @param string $newPassword New password
 * @return array [status, message]
 */
function changePassword($userId, $currentPassword, $newPassword) {
    // Get user
    $user = fetchOne(
        "SELECT id, password FROM usuarios WHERE id = ? AND activo = 1",
        [$userId],
        'i'
    );
    
    if (!$user) {
        return [false, "Usuario no encontrado"];
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        return [false, "La contraseña actual es incorrecta"];
    }
    
    // Validate new password
    list($isValidPassword, $passwordMessage) = validatePassword($newPassword);
    
    if (!$isValidPassword) {
        return [false, $passwordMessage];
    }
    
    // Hash new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update user password
    $updateData = [
        'password' => $passwordHash,
        'fecha_actualizacion' => date('Y-m-d H:i:s')
    ];
    
    $updated = updateData(
        'usuarios',
        $updateData,
        'id = ?',
        [$userId]
    );
    
    if (!$updated) {
        return [false, "Error al cambiar la contraseña"];
    }
    
    return [true, "Contraseña cambiada correctamente"];
}

/**
 * Check if a user is authenticated
 * 
 * @return bool True if authenticated, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirect if not authenticated
 * 
 * @param string $redirectUrl URL to redirect to
 * @return void
 */
function requireAuth($redirectUrl = '/public/login.php') {
    if (!isAuthenticated()) {
        redirect($redirectUrl);
    }
}

/**
 * Log out the current user
 * 
 * @return void
 */
function logoutUser() {
    // Unset all session variables
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}
