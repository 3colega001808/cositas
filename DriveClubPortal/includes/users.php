<?php
/**
 * User management functions
 * 
 * This file contains functions for managing users.
 */

// Include dependencies
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Get user by ID
 * 
 * @param int $userId User ID
 * @return array|null User data or null if not found
 */
function getUserById($userId) {
    return fetchOne(
        "SELECT u.*, r.nombre as rol_nombre
         FROM usuarios u
         JOIN roles r ON u.rol_id = r.id
         WHERE u.id = ?",
        [$userId],
        'i'
    );
}

/**
 * Get user by email
 * 
 * @param string $email User email
 * @return array|null User data or null if not found
 */
function getUserByEmail($email) {
    return fetchOne(
        "SELECT u.*, r.nombre as rol_nombre
         FROM usuarios u
         JOIN roles r ON u.rol_id = r.id
         WHERE u.email = ?",
        [$email],
        's'
    );
}

/**
 * Get user subscription
 * 
 * @param int $userId User ID
 * @return array|null Subscription data or null if not found
 */
function getUserSubscription($userId) {
    return fetchOne(
        "SELECT su.*, ps.nombre as plan_nombre, ps.precio_mensual,
         ps.descripcion, ps.limite_vehiculos, ps.limite_duracion,
         ps.kilometraje_mensual, ps.caracteristicas_especiales
         FROM suscripciones_usuario su
         JOIN planes_suscripcion ps ON su.plan_id = ps.id
         WHERE su.usuario_id = ?
         AND su.activo = 1
         AND su.fecha_inicio <= CURDATE()
         AND (su.fecha_fin IS NULL OR su.fecha_fin >= CURDATE())
         ORDER BY su.fecha_inicio DESC
         LIMIT 1",
        [$userId],
        'i'
    );
}

/**
 * Get user subscription history
 * 
 * @param int $userId User ID
 * @return array Subscription history
 */
function getUserSubscriptionHistory($userId) {
    return executeQuery(
        "SELECT su.*, ps.nombre as plan_nombre, ps.precio_mensual
         FROM suscripciones_usuario su
         JOIN planes_suscripcion ps ON su.plan_id = ps.id
         WHERE su.usuario_id = ?
         ORDER BY su.fecha_inicio DESC",
        [$userId],
        'i'
    );
}

/**
 * Update user profile
 * 
 * @param int $userId User ID
 * @param array $userData User data to update
 * @return array [status, message]
 */
function updateUserProfile($userId, $userData) {
    // Validate required fields
    $requiredFields = ['nombre', 'apellidos', 'email'];
    foreach ($requiredFields as $field) {
        if (!isset($userData[$field]) || empty($userData[$field])) {
            return [false, "El campo $field es requerido"];
        }
    }
    
    // Validate email
    if (!isValidEmail($userData['email'])) {
        return [false, "El email no es válido"];
    }
    
    // Check if email is already used by another user
    $existingUser = fetchOne(
        "SELECT id FROM usuarios WHERE email = ? AND id != ?",
        [$userData['email'], $userId],
        'si'
    );
    
    if ($existingUser) {
        return [false, "Este email ya está en uso por otro usuario"];
    }
    
    // Prepare data for update
    $updateData = [
        'nombre' => $userData['nombre'],
        'apellidos' => $userData['apellidos'],
        'email' => $userData['email'],
        'telefono' => $userData['telefono'] ?? null,
        'direccion' => $userData['direccion'] ?? null,
        'ciudad' => $userData['ciudad'] ?? null,
        'codigo_postal' => $userData['codigo_postal'] ?? null,
        'pais' => $userData['pais'] ?? null,
        'fecha_actualizacion' => date('Y-m-d H:i:s')
    ];
    
    // Update avatar if provided
    if (isset($userData['avatar']) && !empty($userData['avatar'])) {
        $updateData['avatar'] = $userData['avatar'];
    }
    
    // Update license number if provided
    if (isset($userData['numero_licencia']) && !empty($userData['numero_licencia'])) {
        $updateData['numero_licencia'] = $userData['numero_licencia'];
    }
    
    // Update user
    $updated = updateData(
        'usuarios',
        $updateData,
        'id = ?',
        [$userId]
    );
    
    if (!$updated) {
        return [false, "Error al actualizar el perfil"];
    }
    
    return [true, "Perfil actualizado correctamente"];
}

/**
 * Create user subscription
 * 
 * @param int $userId User ID
 * @param int $planId Plan ID
 * @param string $paymentMethod Payment method
 * @return array [status, message, subscription_id]
 */
function createUserSubscription($userId, $planId, $paymentMethod = 'tarjeta') {
    // Check if plan exists
    $plan = fetchOne(
        "SELECT id, precio_mensual FROM planes_suscripcion WHERE id = ? AND activo = 1",
        [$planId],
        'i'
    );
    
    if (!$plan) {
        return [false, "El plan seleccionado no existe", null];
    }
    
    // Check if user already has an active subscription
    $activeSubscription = getUserSubscription($userId);
    
    if ($activeSubscription) {
        // End current subscription
        updateData(
            'suscripciones_usuario',
            [
                'fecha_fin' => date('Y-m-d'),
                'activo' => 0,
                'fecha_actualizacion' => date('Y-m-d H:i:s')
            ],
            'id = ?',
            [$activeSubscription['id']]
        );
    }
    
    // Create new subscription
    $insertData = [
        'usuario_id' => $userId,
        'plan_id' => $planId,
        'fecha_inicio' => date('Y-m-d'),
        'fecha_fin' => null,
        'estado_pago' => 'pagado',
        'renovacion_automatica' => 1,
        'activo' => 1,
        'metodo_pago' => $paymentMethod,
        'fecha_ultimo_pago' => date('Y-m-d H:i:s'),
        'fecha_proximo_pago' => date('Y-m-d', strtotime('+1 month')),
        'fecha_creacion' => date('Y-m-d H:i:s'),
        'fecha_actualizacion' => date('Y-m-d H:i:s')
    ];
    
    $subscriptionId = insertData('suscripciones_usuario', $insertData);
    
    if (!$subscriptionId) {
        return [false, "Error al crear la suscripción", null];
    }
    
    return [true, "Suscripción creada correctamente", $subscriptionId];
}

/**
 * Cancel user subscription
 * 
 * @param int $userId User ID
 * @param int $subscriptionId Subscription ID
 * @return array [status, message]
 */
function cancelUserSubscription($userId, $subscriptionId) {
    // Check if subscription exists and belongs to user
    $subscription = fetchOne(
        "SELECT id, fecha_fin FROM suscripciones_usuario 
         WHERE id = ? AND usuario_id = ? AND activo = 1",
        [$subscriptionId, $userId],
        'ii'
    );
    
    if (!$subscription) {
        return [false, "Suscripción no encontrada"];
    }
    
    // Update subscription
    $updated = updateData(
        'suscripciones_usuario',
        [
            'renovacion_automatica' => 0,
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ],
        'id = ?',
        [$subscriptionId]
    );
    
    if (!$updated) {
        return [false, "Error al cancelar la suscripción"];
    }
    
    return [true, "Tu suscripción se cancelará automáticamente al final del periodo actual"];
}

/**
 * Get all subscription plans
 * 
 * @return array Subscription plans
 */
function getAllSubscriptionPlans() {
    return executeQuery(
        "SELECT * FROM planes_suscripcion WHERE activo = 1 ORDER BY precio_mensual ASC",
        [],
        ''
    );
}

/**
 * Get subscription plan by ID
 * 
 * @param int $planId Plan ID
 * @return array|null Plan data or null if not found
 */
function getSubscriptionPlanById($planId) {
    return fetchOne(
        "SELECT * FROM planes_suscripcion WHERE id = ? AND activo = 1",
        [$planId],
        'i'
    );
}

/**
 * Get user reviews
 * 
 * @param int $userId User ID
 * @return array Reviews
 */
function getUserReviews($userId) {
    return executeQuery(
        "SELECT r.*, v.nombre as vehiculo_nombre, m.nombre as marca_nombre,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
         FROM reseñas r
         JOIN vehiculos v ON r.vehiculo_id = v.id
         JOIN marcas m ON v.marca_id = m.id
         WHERE r.usuario_id = ?
         ORDER BY r.fecha_creacion DESC",
        [$userId],
        'i'
    );
}

/**
 * Check if user has completed their profile
 * 
 * @param int $userId User ID
 * @return bool True if profile is complete, false otherwise
 */
function isProfileComplete($userId) {
    $user = getUserById($userId);
    
    if (!$user) {
        return false;
    }
    
    $requiredFields = ['nombre', 'apellidos', 'email', 'telefono', 'direccion', 'ciudad', 'codigo_postal', 'pais', 'numero_licencia'];
    
    foreach ($requiredFields as $field) {
        if (empty($user[$field])) {
            return false;
        }
    }
    
    return true;
}

/**
 * Get user profile completion percentage
 * 
 * @param int $userId User ID
 * @return int Completion percentage
 */
function getProfileCompletionPercentage($userId) {
    $user = getUserById($userId);
    
    if (!$user) {
        return 0;
    }
    
    $requiredFields = ['nombre', 'apellidos', 'email', 'telefono', 'direccion', 'ciudad', 'codigo_postal', 'pais', 'numero_licencia'];
    $completedFields = 0;
    
    foreach ($requiredFields as $field) {
        if (!empty($user[$field])) {
            $completedFields++;
        }
    }
    
    return round(($completedFields / count($requiredFields)) * 100);
}

/**
 * Get user roles
 * 
 * @return array Roles
 */
function getAllRoles() {
    return executeQuery(
        "SELECT * FROM roles ORDER BY id ASC",
        [],
        ''
    );
}
