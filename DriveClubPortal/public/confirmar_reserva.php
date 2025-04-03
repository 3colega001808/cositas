<?php
/**
 * Reservation Confirmation Page
 * 
 * This page shows the reservation confirmation details.
 * It's typically shown after a reservation has been successfully created.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/reservations.php';
require_once __DIR__ . '/../includes/vehicles.php';

// Require authentication
requireAuth();

// Get user ID from session
$userId = $_SESSION['user_id'];

// Check if reservation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('/public/mi-cuenta.php?tab=reservations');
}

$reservationId = (int)$_GET['id'];

// Get reservation details
$reservation = getReservationById($reservationId);

// Verify that the reservation exists and belongs to the current user
if (!$reservation || $reservation['usuario_id'] != $userId) {
    setFlashMessage('error', 'Reserva no encontrada o no autorizada');
    redirect('/public/mi-cuenta.php?tab=reservations');
}

// Get vehicle details
$vehicle = getVehicleById($reservation['vehiculo_id']);

// Set page title and description
$pageTitle = "Confirmación de Reserva";
$pageDescription = "Detalles de tu reserva confirmada";

// Additional scripts
$additionalScripts = '
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Generate QR Code
    const qrContainer = document.getElementById("qrCodeContainer");
    const qrData = JSON.stringify({
        id: ' . $reservation['id'] . ',
        vehicle: "' . htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']) . '",
        start_date: "' . $reservation['fecha_inicio'] . '",
        end_date: "' . $reservation['fecha_fin'] . '",
        user: "' . htmlspecialchars($reservation['usuario_nombre'] . ' ' . $reservation['usuario_apellidos']) . '"
    });
    
    if (qrContainer && window.QRCode) {
        try {
            new QRCode(qrContainer, {
                text: qrData,
                width: 200,
                height: 200,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        } catch (error) {
            console.error("Error generating QR code:", error);
        }
    }
    
    // Enable download QR code
    const downloadBtn = document.getElementById("downloadQrCode");
    if (downloadBtn) {
        downloadBtn.addEventListener("click", function() {
            const qrImage = qrContainer.querySelector("img");
            if (qrImage) {
                downloadBtn.href = qrImage.src;
            }
        });
    }
    
    // Print reservation
    const printBtn = document.getElementById("printReservation");
    if (printBtn) {
        printBtn.addEventListener("click", function() {
            window.print();
        });
    }
});
</script>
';

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Confirmation Banner -->
<section class="confirmation-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h1>Reserva Confirmada</h1>
        <p class="lead">Tu reserva ha sido confirmada correctamente.</p>
      </div>
    </div>
  </div>
</section>

<!-- Confirmation Content -->
<section class="confirmation-section py-5">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="content-card">
          <div class="confirmation-header text-center mb-4">
            <div class="confirmation-icon">
              <i class="bi bi-check-circle-fill text-success"></i>
            </div>
            <h2 class="mb-0">¡Reserva Confirmada!</h2>
            <p class="text-muted">Reserva #<?php echo $reservation['id']; ?></p>
          </div>
          
          <div class="reservation-details mb-4">
            <div class="row mb-3">
              <div class="col-md-6">
                <h4 class="detail-title">Vehículo</h4>
                <div class="d-flex align-items-center">
                  <?php if (!empty($reservation['imagen'])): ?>
                    <img src="<?php echo htmlspecialchars($reservation['imagen']); ?>" alt="<?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?>" class="vehicle-thumbnail me-3">
                  <?php else: ?>
                    <div class="vehicle-thumbnail-placeholder me-3">
                      <i class="bi bi-car-front"></i>
                    </div>
                  <?php endif; ?>
                  <div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?></h5>
                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($vehicle['tipo_nombre']); ?></p>
                  </div>
                </div>
              </div>
              <div class="col-md-6 mt-3 mt-md-0">
                <h4 class="detail-title">Fechas</h4>
                <div>
                  <p class="mb-1"><strong>Inicio:</strong> <?php echo formatDate($reservation['fecha_inicio']); ?></p>
                  <p class="mb-0"><strong>Fin:</strong> <?php echo formatDate($reservation['fecha_fin']); ?></p>
                </div>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <h4 class="detail-title">Ubicación de Recogida</h4>
                <p class="mb-0"><?php echo htmlspecialchars($reservation['ubicacion_recogida'] ?? 'Oficina central'); ?></p>
              </div>
              <div class="col-md-6 mt-3 mt-md-0">
                <h4 class="detail-title">Ubicación de Devolución</h4>
                <p class="mb-0"><?php echo htmlspecialchars($reservation['ubicacion_devolucion'] ?? 'Oficina central'); ?></p>
              </div>
            </div>
            
            <?php if (!empty($reservation['comentarios'])): ?>
              <div class="row mb-3">
                <div class="col-12">
                  <h4 class="detail-title">Comentarios</h4>
                  <p class="mb-0"><?php echo nl2br(htmlspecialchars($reservation['comentarios'])); ?></p>
                </div>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="qr-code-section mb-4">
            <h4 class="text-center mb-3">Código QR de tu Reserva</h4>
            <div class="d-flex justify-content-center">
              <div class="qr-code-container" id="qrCodeContainer"></div>
            </div>
            <p class="text-center text-muted mt-2">Muestra este código al recoger y devolver el vehículo</p>
          </div>
          
          <div class="confirmation-actions d-flex flex-wrap justify-content-center gap-2">
            <a href="/public/mi-cuenta.php?tab=reservations" class="btn btn-primary">Ver Mis Reservas</a>
            <a href="#" id="downloadQrCode" class="btn btn-outline-primary" download="reserva_<?php echo $reservation['id']; ?>.png">Descargar QR</a>
            <button id="printReservation" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Imprimir</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Additional Information -->
<section class="additional-info py-5 bg-light">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="content-card">
          <h3 class="mb-4">Información Importante</h3>
          
          <div class="important-info-item mb-4">
            <h4><i class="bi bi-clock me-2"></i>Horarios de Recogida y Devolución</h4>
            <p>Puedes recoger tu vehículo entre las 9:00 y las 19:00 de lunes a viernes, y entre las 10:00 y las 14:00 los sábados. La devolución debe realizarse dentro del mismo horario.</p>
          </div>
          
          <div class="important-info-item mb-4">
            <h4><i class="bi bi-card-checklist me-2"></i>Documentación Necesaria</h4>
            <p>Recuerda traer tu carnet de conducir, DNI o pasaporte, y la tarjeta de crédito utilizada para la reserva. Sin esta documentación, no podremos entregarte el vehículo.</p>
          </div>
          
          <div class="important-info-item mb-4">
            <h4><i class="bi bi-info-circle me-2"></i>Política de Combustible</h4>
            <p>El vehículo se entrega con el depósito lleno y debe devolverse en las mismas condiciones. En caso contrario, se cobrará el combustible faltante más un cargo por servicio de repostaje.</p>
          </div>
          
          <div class="important-info-item">
            <h4><i class="bi bi-telephone me-2"></i>Contacto</h4>
            <p>Si necesitas modificar o cancelar tu reserva, o tienes cualquier pregunta, no dudes en contactarnos:</p>
            <ul class="mb-0">
              <li>Teléfono: +34 912 345 678</li>
              <li>Email: reservas@driveclub.com</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
