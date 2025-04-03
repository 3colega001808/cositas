<?php
/**
 * Vehicle management functions
 * 
 * This file contains functions for managing vehicles.
 */

// Include dependencies
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

/**
 * Get all active vehicles
 * 
 * @param array $filters Optional filters
 * @return array Vehicles
 */
function getAllVehicles($filters = []) {
    $sql = "SELECT v.*, m.nombre as marca_nombre, t.nombre as tipo_nombre, 
           ps.nombre as plan_nombre, ps.precio_mensual,
           (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
           FROM vehiculos v
           JOIN marcas m ON v.marca_id = m.id
           JOIN tipos_vehiculo t ON v.tipo_id = t.id
           JOIN planes_suscripcion ps ON v.plan_minimo_id = ps.id
           WHERE v.activo = 1";
    
    $params = [];
    $types = '';
    
    // Add filters
    if (!empty($filters)) {
        if (isset($filters['marca_id']) && !empty($filters['marca_id'])) {
            $sql .= " AND v.marca_id = ?";
            $params[] = $filters['marca_id'];
            $types .= 'i';
        }
        
        if (isset($filters['tipo_id']) && !empty($filters['tipo_id'])) {
            $sql .= " AND v.tipo_id = ?";
            $params[] = $filters['tipo_id'];
            $types .= 'i';
        }
        
        if (isset($filters['plan_minimo_id']) && !empty($filters['plan_minimo_id'])) {
            $sql .= " AND v.plan_minimo_id <= ?";
            $params[] = $filters['plan_minimo_id'];
            $types .= 'i';
        }
        
        if (isset($filters['disponible']) && $filters['disponible'] !== null) {
            $sql .= " AND v.disponible = ?";
            $params[] = $filters['disponible'];
            $types .= 'i';
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $sql .= " AND (v.nombre LIKE ? OR m.nombre LIKE ? OR t.nombre LIKE ?)";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'sss';
        }
    }
    
    $sql .= " ORDER BY v.plan_minimo_id ASC, m.nombre ASC, v.nombre ASC";
    
    return executeQuery($sql, $params, $types);
}

/**
 * Get vehicle by ID
 * 
 * @param int $vehicleId Vehicle ID
 * @return array|null Vehicle data or null if not found
 */
function getVehicleById($vehicleId) {
    return fetchOne(
        "SELECT v.*, m.nombre as marca_nombre, t.nombre as tipo_nombre, 
         ps.nombre as plan_nombre, ps.precio_mensual
         FROM vehiculos v
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         JOIN planes_suscripcion ps ON v.plan_minimo_id = ps.id
         WHERE v.id = ? AND v.activo = 1",
        [$vehicleId],
        'i'
    );
}

/**
 * Get vehicle images
 * 
 * @param int $vehicleId Vehicle ID
 * @return array Images
 */
function getVehicleImages($vehicleId) {
    return executeQuery(
        "SELECT * FROM imagenes_vehiculo 
         WHERE vehiculo_id = ? 
         ORDER BY es_principal DESC, orden_visualizacion ASC",
        [$vehicleId],
        'i'
    );
}

/**
 * Get vehicle main image
 * 
 * @param int $vehicleId Vehicle ID
 * @return string|null Image URL or null if not found
 */
function getVehicleMainImage($vehicleId) {
    $image = fetchOne(
        "SELECT url_imagen FROM imagenes_vehiculo 
         WHERE vehiculo_id = ? AND es_principal = 1 
         LIMIT 1",
        [$vehicleId],
        'i'
    );
    
    return $image ? $image['url_imagen'] : null;
}

/**
 * Get all vehicle brands
 * 
 * @return array Brands
 */
function getAllBrands() {
    return executeQuery(
        "SELECT m.*, 
         (SELECT COUNT(*) FROM vehiculos WHERE marca_id = m.id AND activo = 1) as vehicle_count 
         FROM marcas m 
         ORDER BY m.nombre ASC",
        [],
        ''
    );
}

/**
 * Get all vehicle types
 * 
 * @return array Types
 */
function getAllVehicleTypes() {
    return executeQuery(
        "SELECT t.*, 
         (SELECT COUNT(*) FROM vehiculos WHERE tipo_id = t.id AND activo = 1) as vehicle_count 
         FROM tipos_vehiculo t 
         ORDER BY t.nombre ASC",
        [],
        ''
    );
}

/**
 * Get user's allowed vehicles based on subscription
 * 
 * @param int $userId User ID
 * @return array Vehicles
 */
function getUserAllowedVehicles($userId) {
    return executeQuery(
        "SELECT v.*, m.nombre as marca_nombre, t.nombre as tipo_nombre, 
         ps.nombre as plan_nombre, ps.precio_mensual,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
         FROM vehiculos v
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         JOIN planes_suscripcion ps ON v.plan_minimo_id = ps.id
         JOIN suscripciones_usuario su ON ps.id >= v.plan_minimo_id
         WHERE su.usuario_id = ?
         AND su.activo = 1
         AND su.fecha_inicio <= CURDATE()
         AND (su.fecha_fin IS NULL OR su.fecha_fin >= CURDATE())
         AND v.activo = 1
         AND v.disponible = 1
         ORDER BY v.plan_minimo_id ASC, m.nombre ASC, v.nombre ASC",
        [$userId],
        'i'
    );
}

/**
 * Search vehicles
 * 
 * @param string $term Search term
 * @return array Vehicles
 */
function searchVehicles($term) {
    $searchTerm = '%' . $term . '%';
    
    return executeQuery(
        "SELECT v.*, m.nombre as marca_nombre, t.nombre as tipo_nombre, 
         ps.nombre as plan_nombre, ps.precio_mensual,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
         FROM vehiculos v
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         JOIN planes_suscripcion ps ON v.plan_minimo_id = ps.id
         WHERE v.activo = 1
         AND (v.nombre LIKE ? OR m.nombre LIKE ? OR t.nombre LIKE ?)
         ORDER BY v.plan_minimo_id ASC, m.nombre ASC, v.nombre ASC",
        [$searchTerm, $searchTerm, $searchTerm],
        'sss'
    );
}

/**
 * Get vehicle reviews
 * 
 * @param int $vehicleId Vehicle ID
 * @return array Reviews
 */
function getVehicleReviews($vehicleId) {
    return executeQuery(
        "SELECT r.*, u.nombre as usuario_nombre, u.apellidos as usuario_apellidos, u.avatar
         FROM reseñas r
         JOIN usuarios u ON r.usuario_id = u.id
         WHERE r.vehiculo_id = ? AND r.aprobada = 1
         ORDER BY r.fecha_creacion DESC",
        [$vehicleId],
        'i'
    );
}

/**
 * Get average rating for a vehicle
 * 
 * @param int $vehicleId Vehicle ID
 * @return float Average rating
 */
function getVehicleAverageRating($vehicleId) {
    $result = fetchOne(
        "SELECT AVG(puntuacion) as avg_rating FROM reseñas 
         WHERE vehiculo_id = ? AND aprobada = 1",
        [$vehicleId],
        'i'
    );
    
    return $result ? round($result['avg_rating'], 1) : 0;
}

/**
 * Get similar vehicles
 * 
 * @param int $vehicleId Current vehicle ID
 * @param int $typeId Vehicle type ID
 * @param int $limit Number of vehicles to return
 * @return array Similar vehicles
 */
function getSimilarVehicles($vehicleId, $typeId, $limit = 3) {
    return executeQuery(
        "SELECT v.*, m.nombre as marca_nombre, t.nombre as tipo_nombre, 
         ps.nombre as plan_nombre, ps.precio_mensual,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen
         FROM vehiculos v
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         JOIN planes_suscripcion ps ON v.plan_minimo_id = ps.id
         WHERE v.id != ? AND v.tipo_id = ? AND v.activo = 1
         ORDER BY RAND()
         LIMIT ?",
        [$vehicleId, $typeId, $limit],
        'iii'
    );
}

/**
 * Get vehicle maintenance records
 * 
 * @param int $vehicleId Vehicle ID
 * @return array Maintenance records
 */
function getVehicleMaintenanceRecords($vehicleId) {
    return executeQuery(
        "SELECT * FROM registros_mantenimiento 
         WHERE vehiculo_id = ?
         ORDER BY fecha_mantenimiento DESC",
        [$vehicleId],
        'i'
    );
}

/**
 * Get featured vehicles
 * 
 * @param int $limit Number of vehicles to return
 * @return array Featured vehicles
 */
function getFeaturedVehicles($limit = 4) {
    return executeQuery(
        "SELECT v.*, m.nombre as marca_nombre, t.nombre as tipo_nombre, 
         ps.nombre as plan_nombre, ps.precio_mensual,
         (SELECT url_imagen FROM imagenes_vehiculo WHERE vehiculo_id = v.id AND es_principal = 1 LIMIT 1) as imagen,
         (SELECT AVG(puntuacion) FROM reseñas WHERE vehiculo_id = v.id AND aprobada = 1) as rating,
         (SELECT COUNT(*) FROM reseñas WHERE vehiculo_id = v.id AND aprobada = 1) as review_count
         FROM vehiculos v
         JOIN marcas m ON v.marca_id = m.id
         JOIN tipos_vehiculo t ON v.tipo_id = t.id
         JOIN planes_suscripcion ps ON v.plan_minimo_id = ps.id
         WHERE v.activo = 1
         ORDER BY rating DESC, review_count DESC, RAND()
         LIMIT ?",
        [$limit],
        'i'
    );
}
