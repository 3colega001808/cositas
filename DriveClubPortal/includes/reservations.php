<?php
/**
 * Reservation functions
 * 
 * This file contains functions for managing reservations.
 */

// Include dependencies
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/qr_generator.php';
require_once __DIR__ . '/vehicles.php';
require_once __DIR__ . '/users.php';
require_once __DIR__ . '/../config/email.php';

/**
 * Create a new reservation
 * 
 * @param array $reservationData Reservation data
 * @return array [status, message, reservation_id]
 */
function createReservation($reservationData) {
    // Validate required fields
    $requiredFields = ['usuario_id', 'vehiculo_id', 'fecha_inicio', 'fecha_fin'];
    foreach ($requiredFields as $field) {
        if (!isset($reservationData[$field]) || empty($reservationData[$field])) {
            return [false, "El campo $field es requerido", null];
        }
    }
    
    // Check if dates are valid
    $startDate = $reservationData['fecha_inicio'];
    $endDate = $reservationData['fecha_fin'];
    
    if (strtotime($startDate) < strtotime(date('Y-m-d'))) {
        return [false, "La fecha de inicio no puede ser en el pasado", null];
    }
    
    if (strtotime($endDate) <= strtotime($startDate)) {
        return [false, "La fecha de fin debe ser posterior a la fecha de inicio", null];
    }
    
    // Check if vehicle is available for these dates
    $isAvailable = checkVehicleAvailability(
        $reservationData['vehiculo_id'], 
        $startDate, 
        $endDate
    );
    
    if (!$isAvailable) {
        return [false, "El vehículo no está disponible para las fechas seleccionadas", null];
    }
    
    // Check if user has a valid subscription
    $hasValidSubscription = checkUserSubscription($reservationData['usuario_id']);
    
    if (!$hasValidSubscription) {
        return [false, "No tienes una suscripción válida", null];
    }
    
    // Check if user's subscription allows this vehicle
    $canReserveVehicle = checkSubscriptionVehicleAccess(
        $reservationData['usuario_id'],
        $reservationData['vehiculo_id']
    );
    
    if (!$canReserveVehicle) {
        return [false, "Tu plan de suscripción actual no permite reservar este vehículo", null];
    }
    
    // Start a transaction
    $conn = beginTransaction();
    
    if (!$conn) {
        return [false, "Error al iniciar la transacción", null];
    }
    
    try {
        // Set reservation state to pending by default
        $pendingStateId = getPendingStateId();
        
        if (!$pendingStateId) {
            throw new Exception("Error al obtener el estado de reserva pendiente");
        }
        
        // Generate a unique code for QR
        $reservationCode = generateReservationCode();
        
        // Prepare data for insertion
        $insertData = [
            'usuario_id' => $reservationData['usuario_id'],
            'vehiculo_id' => $reservationData['vehiculo_id'],
            'fecha_inicio' => $startDate,
            'fecha_fin' => $endDate,
            'estado_id' => $pendingStateId,
            'comentarios' => $reservationData['comentarios'] ?? null,
            'ubicacion_recogida' => $reservationData['ubicacion_recogida'] ?? null,
            'ubicacion_devolucion' => $reservationData['ubicacion_devolucion'] ?? null,
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'fecha_actualizacion' => date('Y-m-d H:i:s')
        ];
        
        // Insert reservation
        $stmt = $conn->prepare(
            "INSERT INTO reservas (
                usuario_id, vehiculo_id, fecha_inicio, fecha_fin, estado_id,
                comentarios, ubicacion_recogida, ubicacion_devolucion,
                fecha_creacion, fecha_actualizacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        $stmt->bind_param(
            "iissiissss",
            $insertData['usuario_id'],
            $insertData['vehiculo_id'],
            $insertData['fecha_inicio'],
            $insertData['fecha_fin'],
            $insertData['estado_id'],
            $insertData['comentarios'],
            $insertData['ubicacion_recogida'],
            $insertData['ubicacion_devolucion'],
            $insertData['fecha_creacion'],
            $insertData['fecha_actualizacion']
        );
        
        $stmt->execute();
        $reservationId = $conn->insert_id;
        $stmt->close();
        
        if (!$reservationId) {
            throw new Exception("Error al crear la reserva");
        }
        
        // Generate QR code
        $qrData = json_encode([
            'reservation_id' => $reservationId,
            'code' => $reservationCode,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'vehicle_id' => $reservationData['vehiculo_id'],
            'user_id' => $reservationData['usuario_id'],
        ]);
        
        $qrCodePath = generateQRCode($qrData, $reservationId);
        
        if (!$qrCodePath) {
            throw new Exception("Error al generar el código QR");
        }
        
        // Update reservation with QR code path
        $stmt = $conn->prepare("UPDATE reservas SET codigo_qr = ? WHERE id = ?");
        $stmt->bind_param("si", $qrCodePath, $reservationId);
        $stmt->execute();
        $stmt->close();
        
        // Update vehicle availability
        $stmt = $conn->prepare("UPDATE vehiculos SET disponible = 0 WHERE id = ?");
        $stmt->bind_param("i", $reservationData['vehiculo_id']);
        $stmt->execute();
        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        // Send confirmation email with QR code
        $user = getUserById($reservationData['usuario_id']);
        $vehicle = getVehicleById($reservationData['vehiculo_id']);
        
        $emailData = [
            'id' => $reservationId,
            'vehicle_name' => $vehicle['nombre'] . ' ' . $vehicle['marca_nombre'],
            'fecha_inicio' => formatDate($startDate),
            'fecha_fin' => formatDate($endDate)
        ];
        
        sendReservationConfirmationEmail(
            $user['email'],
            $user['nombre'],
            $emailData,
            $qrCodePath
        );
        
        return [true, "Reserva creada correctamente", $reservationId];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error creating reservation: " . $e->getMessage());
        return [false, "Error al crear la reserva: " . $e->getMessage(), null];
    } finally {
        // Close connection
        $conn->close();
    }
}

/**
 * Generate a unique reservation code
 * 
 * @return string Reservation code
 */
function generateReservationCode() {
    return strtoupper(substr(uniqid(), -6) . bin2hex(random_bytes(2)));
}

/**
 * Get pending reservation state ID
 * 
 * @return int|null Pending state ID or null if not found
 */
function getPendingStateId() {
    $state = fetchOne(
        "SELECT id FROM estados_reserva WHERE nombre = 'Pendiente'",
        [],
        ''
    );
    
    if (!$state) {
        // Create pending state if it doesn't exist
        $insertId = insertData('estados_reserva', [
            'nombre' => 'Pendiente',
            'descripcion' => 'Reserva pendiente de confirmación'
        ]);
        
        return $insertId;
    }
    
    return $state['id'];
}

/**
 * Check if a vehicle is available for a specific date range
 * 
 * @param int $vehicleId Vehicle ID
 * @param string $startDate Start date (Y-m-d format)
 * @param string $endDate End date (Y-m-d format)
 * @return bool True if available, false otherwise
 */
function checkVehicleAvailability($vehicleId, $startDate, $endDate) {
    // Check if vehicle exists and is active
    $vehicle = fetchOne(
        "SELECT id, disponible FROM vehiculos WHERE id = ? AND activo = 1",
        [$vehicleId],
        'i'
    );
    
    if (!$vehicle || $vehicle['disponible'] != 1) {
        return false;
    }
    
    // Check if there are overlapping reservations
    $overlappingReservations = executeQuery(
        "SELECT COUNT(*) as count FROM reservas r
         JOIN estados_reserva e ON r.estado_id = e.id
         WHERE r.vehiculo_id = ?
         AND e.nombre NOT IN ('Cancelada', 'Completada')
         AND (
             (r.fecha_inicio BETWEEN ? AND ?) OR
             (r.fecha_fin BETWEEN ? AND ?) OR
             (r.fecha_inicio <= ? AND r.fecha_fin >= ?)
         )",
        [$vehicleId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate],
        'issssss'
    );
    
    if (!$overlappingReservations) {
        return false;
    }
    
    return $overlappingReservations[0]['count'] == 0;
}

/**
 * Check if a user has a valid subscription
 * 
 * @param int $userId User ID
 * @return bool True if has valid subscription, false otherwise
 */
function checkUserSubscription($userId) {
    $subscription = fetchOne(
        "SELECT id FROM suscripciones_usuario
         WHERE usuario_id = ?
         AND activo = 1
         AND fecha_inicio <= CURDATE()
         AND (fecha_fin IS NULL OR fecha_fin >= CURDATE())",
        [$userId],
        'i'
    );
    
    return !empty($subscription);
}

/**
 * Check if user's subscription allows access to a specific vehicle
 * 
 * @param int $userId User ID
 * @param int $vehicleId Vehicle ID
 * @return bool True if allowed, false otherwise
 */
function checkSubscriptionVehicleAccess($userId, $vehicleId) {
    $result = fetchOne(
        "SELECT 1 FROM suscripciones_usuario su
         JOIN planes_suscripcion ps ON su.plan_id = ps.id
         JOIN vehiculos v ON v.plan_minimo_id <= ps.id
         WHERE su.usuario_id = ?
         AND v.id = ?
         AND su.activo = 1
         AND su.fecha_inicio <= CURDATE()
         AND (su.fecha_fin IS NULL OR su.fecha_fin >= CURDATE())",
        [$userId, $vehicleId],
        'ii'
    );
    
    return !empty($result);
}

/**
 * Get user's active reservations
 * 
 * @param int $userId User ID
 * @return array Reservations
 */
function getUserActiveReservations($userId) {
    return executeQuery(
        "SELECT r.*, e.nombre as estado_nombre, 
         v.nombre as vehiculo_nombre, v.matricula,
         m.nombre as marca_nombre, t.nombre as tipo_nombre,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
         FROM reservas r
         JOIN estados_reserva e ON r.estado_id = e.id
         JOIN vehiculos v ON r.vehiculo_id = v.id
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         WHERE r.usuario_id = ?
         AND e.nombre NOT IN ('Cancelada', 'Completada')
         ORDER BY r.fecha_inicio ASC",
        [$userId],
        'i'
    );
}

/**
 * Get user's past reservations
 * 
 * @param int $userId User ID
 * @return array Reservations
 */
function getUserPastReservations($userId) {
    return executeQuery(
        "SELECT r.*, e.nombre as estado_nombre, 
         v.nombre as vehiculo_nombre, v.matricula,
         m.nombre as marca_nombre, t.nombre as tipo_nombre,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
         FROM reservas r
         JOIN estados_reserva e ON r.estado_id = e.id
         JOIN vehiculos v ON r.vehiculo_id = v.id
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         WHERE r.usuario_id = ?
         AND (e.nombre IN ('Cancelada', 'Completada') OR r.fecha_fin < CURDATE())
         ORDER BY r.fecha_fin DESC",
        [$userId],
        'i'
    );
}

/**
 * Get reservation by ID
 * 
 * @param int $reservationId Reservation ID
 * @return array|null Reservation data or null if not found
 */
function getReservationById($reservationId) {
    return fetchOne(
        "SELECT r.*, e.nombre as estado_nombre, 
         v.nombre as vehiculo_nombre, v.matricula,
         m.nombre as marca_nombre, t.nombre as tipo_nombre,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen,
         u.nombre as usuario_nombre, u.apellidos as usuario_apellidos, u.email as usuario_email
         FROM reservas r
         JOIN estados_reserva e ON r.estado_id = e.id
         JOIN vehiculos v ON r.vehiculo_id = v.id
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         JOIN usuarios u ON r.usuario_id = u.id
         WHERE r.id = ?",
        [$reservationId],
        'i'
    );
}

/**
 * Cancel reservation
 * 
 * @param int $reservationId Reservation ID
 * @param int $userId User ID (for security check)
 * @return array [status, message]
 */
function cancelReservation($reservationId, $userId) {
    // Get reservation
    $reservation = fetchOne(
        "SELECT r.*, e.nombre as estado_nombre
         FROM reservas r
         JOIN estados_reserva e ON r.estado_id = e.id
         WHERE r.id = ? AND r.usuario_id = ?",
        [$reservationId, $userId],
        'ii'
    );
    
    if (!$reservation) {
        return [false, "Reserva no encontrada"];
    }
    
    // Check if reservation can be cancelled
    if (in_array($reservation['estado_nombre'], ['Cancelada', 'Completada'])) {
        return [false, "Esta reserva no puede ser cancelada"];
    }
    
    // Get cancelled state ID
    $cancelledState = fetchOne(
        "SELECT id FROM estados_reserva WHERE nombre = 'Cancelada'",
        [],
        ''
    );
    
    if (!$cancelledState) {
        // Create cancelled state if it doesn't exist
        $insertId = insertData('estados_reserva', [
            'nombre' => 'Cancelada',
            'descripcion' => 'Reserva cancelada por el usuario'
        ]);
        
        if (!$insertId) {
            return [false, "Error al obtener el estado de cancelación"];
        }
        
        $cancelledStateId = $insertId;
    } else {
        $cancelledStateId = $cancelledState['id'];
    }
    
    // Start a transaction
    $conn = beginTransaction();
    
    if (!$conn) {
        return [false, "Error al iniciar la transacción"];
    }
    
    try {
        // Update reservation status
        $stmt = $conn->prepare("UPDATE reservas SET estado_id = ?, fecha_actualizacion = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $cancelledStateId, $reservationId);
        $stmt->execute();
        $stmt->close();
        
        // Set vehicle as available again if the reservation is current or future
        if (strtotime($reservation['fecha_fin']) >= strtotime(date('Y-m-d'))) {
            $stmt = $conn->prepare("UPDATE vehiculos SET disponible = 1 WHERE id = ?");
            $stmt->bind_param("i", $reservation['vehiculo_id']);
            $stmt->execute();
            $stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        return [true, "Reserva cancelada correctamente"];
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error cancelling reservation: " . $e->getMessage());
        return [false, "Error al cancelar la reserva"];
    } finally {
        // Close connection
        $conn->close();
    }
}

/**
 * Get QR code for a reservation
 * 
 * @param int $reservationId Reservation ID
 * @param int $userId User ID (for security check)
 * @return array [status, qr_code_path, message]
 */
function getReservationQRCode($reservationId, $userId) {
    $reservation = fetchOne(
        "SELECT codigo_qr FROM reservas WHERE id = ? AND usuario_id = ?",
        [$reservationId, $userId],
        'ii'
    );
    
    if (!$reservation) {
        return [false, null, "Reserva no encontrada"];
    }
    
    if (empty($reservation['codigo_qr'])) {
        return [false, null, "Código QR no disponible para esta reserva"];
    }
    
    return [true, $reservation['codigo_qr'], ""];
}

/**
 * Count user's reservations by state
 * 
 * @param int $userId User ID
 * @return array Counts by state
 */
function countUserReservationsByState($userId) {
    $counts = executeQuery(
        "SELECT e.nombre, COUNT(*) as count
         FROM reservas r
         JOIN estados_reserva e ON r.estado_id = e.id
         WHERE r.usuario_id = ?
         GROUP BY e.nombre",
        [$userId],
        'i'
    );
    
    $result = [
        'Pendiente' => 0,
        'Activa' => 0,
        'Completada' => 0,
        'Cancelada' => 0
    ];
    
    if ($counts) {
        foreach ($counts as $count) {
            $result[$count['nombre']] = $count['count'];
        }
    }
    
    return $result;
}

/**
 * Check if a user can make a review for a reservation
 * 
 * @param int $reservationId Reservation ID
 * @param int $userId User ID
 * @return bool True if can review, false otherwise
 */
function canMakeReview($reservationId, $userId) {
    // Check if reservation exists and is completed
    $reservation = fetchOne(
        "SELECT r.id FROM reservas r 
         JOIN estados_reserva e ON r.estado_id = e.id
         WHERE r.id = ? AND r.usuario_id = ? AND e.nombre = 'Completada'",
        [$reservationId, $userId],
        'ii'
    );
    
    if (!$reservation) {
        return false;
    }
    
    // Check if user already made a review
    $review = fetchOne(
        "SELECT id FROM reseñas WHERE reserva_id = ? AND usuario_id = ?",
        [$reservationId, $userId],
        'ii'
    );
    
    return empty($review);
}

/**
 * Create a review for a reservation
 * 
 * @param array $reviewData Review data
 * @return array [status, message]
 */
function createReview($reviewData) {
    // Validate required fields
    $requiredFields = ['usuario_id', 'vehiculo_id', 'reserva_id', 'puntuacion', 'comentario'];
    foreach ($requiredFields as $field) {
        if (!isset($reviewData[$field]) || ($field != 'comentario' && empty($reviewData[$field]))) {
            return [false, "El campo $field es requerido"];
        }
    }
    
    // Validate score
    if ($reviewData['puntuacion'] < 1 || $reviewData['puntuacion'] > 5) {
        return [false, "La puntuación debe estar entre 1 y 5"];
    }
    
    // Check if user can make a review
    if (!canMakeReview($reviewData['reserva_id'], $reviewData['usuario_id'])) {
        return [false, "No puedes hacer una reseña para esta reserva"];
    }
    
    // Insert review
    $insertId = insertData('reseñas', [
        'usuario_id' => $reviewData['usuario_id'],
        'vehiculo_id' => $reviewData['vehiculo_id'],
        'reserva_id' => $reviewData['reserva_id'],
        'puntuacion' => $reviewData['puntuacion'],
        'comentario' => $reviewData['comentario'],
        'aprobada' => 1,
        'fecha_creacion' => date('Y-m-d H:i:s'),
        'fecha_actualizacion' => date('Y-m-d H:i:s')
    ]);
    
    if (!$insertId) {
        return [false, "Error al crear la reseña"];
    }
    
    return [true, "Reseña creada correctamente"];
}
