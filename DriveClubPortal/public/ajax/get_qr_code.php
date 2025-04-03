<?php
/**
 * Get QR Code AJAX Handler
 * 
 * This file handles the AJAX request to get a QR code for a reservation.
 */

// Set content type to JSON
header('Content-Type: application/json');

// Include necessary files
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/reservations.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para acceder a esta función'
    ]);
    exit;
}

// Get user ID from session
$userId = getCurrentUserId();

// Check if reservation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de reserva no proporcionado'
    ]);
    exit;
}

$reservationId = (int)$_GET['id'];

// Get QR code for reservation
list($success, $qrCodePath, $message) = getReservationQRCode($reservationId, $userId);

if (!$success) {
    echo json_encode([
        'success' => false,
        'message' => $message
    ]);
    exit;
}

// Generate QR code if not exists (or regenerate for this request)
require_once __DIR__ . '/../../includes/qr_generator.php';

// Get reservation details
$reservation = getReservationById($reservationId);

// Generate QR data
$qrData = json_encode([
    'id' => $reservation['id'],
    'vehicle' => $reservation['vehiculo_nombre'] . ' ' . $reservation['marca_nombre'],
    'start_date' => $reservation['fecha_inicio'],
    'end_date' => $reservation['fecha_fin'],
    'user' => $reservation['usuario_nombre'] . ' ' . $reservation['usuario_apellidos']
]);

// Generate QR code data URI
$qrDataUri = generateQRCodeDataURI($qrData);

// Return QR code URL and data URI
echo json_encode([
    'success' => true,
    'qr_code_url' => $qrCodePath,
    'qr_code_data_uri' => $qrDataUri,
    'reservation_id' => $reservationId
]);
