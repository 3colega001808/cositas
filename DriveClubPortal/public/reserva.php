<?php
/**
 * Vehicle Reservation Page
 * 
 * This page allows users to reserve a vehicle.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/vehicles.php';
require_once __DIR__ . '/../includes/reservations.php';
require_once __DIR__ . '/../includes/users.php';

// Require authentication
requireAuth();

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get user subscription
$userSubscription = getUserSubscription($userId);

// Redirect if no active subscription
if (!$userSubscription) {
    setFlashMessage('error', 'Necesitas una suscripción activa para realizar reservas');
    redirect('/public/planes.php');
}

// Get vehicle ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('error', 'Vehículo no especificado');
    redirect('/public/vehiculos.php');
}

$vehicleId = (int)$_GET['id'];

// Get vehicle data
$vehicle = getVehicleById($vehicleId);

// Redirect if vehicle not found or not active
if (!$vehicle || $vehicle['activo'] != 1) {
    setFlashMessage('error', 'Vehículo no encontrado o no disponible');
    redirect('/public/vehiculos.php');
}

// Check if subscription plan allows this vehicle
if ($userSubscription['plan_id'] < $vehicle['plan_minimo_id']) {
    setFlashMessage('error', 'Tu plan actual no permite reservar este vehículo. Por favor, actualiza tu plan.');
    redirect('/public/planes.php');
}

// Get vehicle images
$vehicleImages = getVehicleImages($vehicleId);

// Default dates (today + 1 day and today + 8 days)
$defaultStartDate = date('Y-m-d', strtotime('+1 day'));
$defaultEndDate = date('Y-m-d', strtotime('+8 days'));

// Check if editing an existing reservation
$editingReservation = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $reservationId = (int)$_GET['edit'];
    $reservation = getReservationById($reservationId);
    
    if ($reservation && $reservation['usuario_id'] == $userId && $reservation['vehiculo_id'] == $vehicleId) {
        $editingReservation = $reservation;
        $defaultStartDate = $reservation['fecha_inicio'];
        $defaultEndDate = $reservation['fecha_fin'];
    }
}

// Process reservation form
$reservationMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reservation'])) {
    $startDate = $_POST['fecha_inicio'];
    $endDate = $_POST['fecha_fin'];
    $comments = trim($_POST['comentarios'] ?? '');
    
    // Validate dates
    if (empty($startDate) || empty($endDate)) {
        $reservationMessage = displayError('Por favor, selecciona las fechas de inicio y fin');
    } elseif (strtotime($startDate) < strtotime(date('Y-m-d'))) {
        $reservationMessage = displayError('La fecha de inicio no puede ser en el pasado');
    } elseif (strtotime($endDate) <= strtotime($startDate)) {
        $reservationMessage = displayError('La fecha de fin debe ser posterior a la fecha de inicio');
    } else {
        // Calculate duration
        $duration = calculateDateDifference($startDate, $endDate);
        
        // Check if duration exceeds plan limit
        if ($userSubscription['limite_duracion'] && $duration > $userSubscription['limite_duracion']) {
            $reservationMessage = displayError('La duración de la reserva excede el límite de tu plan (' . $userSubscription['limite_duracion'] . ' días)');
        } else {
            // Check if vehicle is available for these dates
            $isAvailable = checkVehicleAvailability($vehicleId, $startDate, $endDate);
            
            if (!$isAvailable) {
                $reservationMessage = displayError('El vehículo no está disponible para las fechas seleccionadas');
            } else {
                // Prepare reservation data
                $reservationData = [
                    'usuario_id' => $userId,
                    'vehiculo_id' => $vehicleId,
                    'fecha_inicio' => $startDate,
                    'fecha_fin' => $endDate,
                    'comentarios' => $comments,
                    'ubicacion_recogida' => 'Oficina central',
                    'ubicacion_devolucion' => 'Oficina central'
                ];
                
                // Create reservation
                list($success, $message, $reservationId) = createReservation($reservationData);
                
                if ($success) {
                    setFlashMessage('success', 'Reserva creada correctamente. Recibirás un email con los detalles y el código QR.');
                    redirect('/public/mi-cuenta.php?tab=reservations');
                } else {
                    $reservationMessage = displayError($message);
                }
            }
        }
    }
}

// Set page title and description
$pageTitle = "Reservar " . $vehicle['marca_nombre'] . " " . $vehicle['nombre'];
$pageDescription = "Reserva el " . $vehicle['marca_nombre'] . " " . $vehicle['nombre'] . " con DriveClub";

// Additional scripts
$additionalScripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialize datepicker
    const startDateInput = document.getElementById("fecha_inicio");
    const endDateInput = document.getElementById("fecha_fin");
    
    if (startDateInput && endDateInput) {
        // Set min dates
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split("T")[0];
        
        startDateInput.min = tomorrowStr;
        
        // Update end date min when start date changes
        startDateInput.addEventListener("change", function() {
            const selectedDate = new Date(this.value);
            selectedDate.setDate(selectedDate.getDate() + 1);
            const minEndDate = selectedDate.toISOString().split("T")[0];
            
            endDateInput.min = minEndDate;
            
            // If end date is before new min, update it
            if (endDateInput.value && new Date(endDateInput.value) < selectedDate) {
                endDateInput.value = minEndDate;
            }
            
            updateReservationSummary();
        });
        
        endDateInput.addEventListener("change", function() {
            updateReservationSummary();
        });
        
        // Initial update
        updateReservationSummary();
    }
    
    // Update reservation summary
    function updateReservationSummary() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (isNaN(startDate) || isNaN(endDate)) return;
        
        const durationElement = document.getElementById("reservation-duration");
        const startDateElement = document.getElementById("summary-start-date");
        const endDateElement = document.getElementById("summary-end-date");
        
        if (durationElement && startDateElement && endDateElement) {
            // Calculate duration in days
            const duration = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24));
            
            // Format dates
            const options = { year: "numeric", month: "long", day: "numeric" };
            const formattedStartDate = startDate.toLocaleDateString("es-ES", options);
            const formattedEndDate = endDate.toLocaleDateString("es-ES", options);
            
            // Update elements
            durationElement.textContent = duration + (duration === 1 ? " día" : " días");
            startDateElement.textContent = formattedStartDate;
            endDateElement.textContent = formattedEndDate;
        }
    }
});
</script>
';

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Vehicle Detail Banner -->
<section class="vehicle-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/public/index.php">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/public/vehiculos.php">Vehículos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?></li>
          </ol>
        </nav>
        <h1><?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($vehicle['tipo_nombre']); ?></p>
      </div>
    </div>
  </div>
</section>

<!-- Reservation Content -->
<section class="reservation-section py-5">
  <div class="container">
    <div class="row">
      <!-- Vehicle Info -->
      <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="content-card mb-4">
          <div class="vehicle-gallery">
            <?php if (!empty($vehicleImages)): ?>
              <div id="vehicleCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <?php foreach ($vehicleImages as $index => $image): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                      <img src="<?php echo htmlspecialchars($image['url_imagen']); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?>">
                    </div>
                  <?php endforeach; ?>
                </div>
                <?php if (count($vehicleImages) > 1): ?>
                  <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                  </button>
                <?php endif; ?>
              </div>
            <?php else: ?>
              <div class="no-image-large">
                <i class="bi bi-car-front"></i>
                <p>No hay imágenes disponibles</p>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="vehicle-specs mt-4">
            <h3 class="specs-title">Características</h3>
            <div class="row g-3">
              <?php if (!empty($vehicle['potencia'])): ?>
                <div class="col-6 col-md-4">
                  <div class="spec-item">
                    <div class="spec-icon"><i class="bi bi-lightning"></i></div>
                    <div class="spec-info">
                      <h4>Potencia</h4>
                      <p><?php echo htmlspecialchars($vehicle['potencia']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($vehicle['aceleracion'])): ?>
                <div class="col-6 col-md-4">
                  <div class="spec-item">
                    <div class="spec-icon"><i class="bi bi-speedometer"></i></div>
                    <div class="spec-info">
                      <h4>Aceleración</h4>
                      <p><?php echo htmlspecialchars($vehicle['aceleracion']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($vehicle['velocidad_maxima'])): ?>
                <div class="col-6 col-md-4">
                  <div class="spec-item">
                    <div class="spec-icon"><i class="bi bi-speedometer2"></i></div>
                    <div class="spec-info">
                      <h4>Vel. Máxima</h4>
                      <p><?php echo htmlspecialchars($vehicle['velocidad_maxima']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($vehicle['transmision'])): ?>
                <div class="col-6 col-md-4">
                  <div class="spec-item">
                    <div class="spec-icon"><i class="bi bi-gear"></i></div>
                    <div class="spec-info">
                      <h4>Transmisión</h4>
                      <p><?php echo htmlspecialchars($vehicle['transmision']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($vehicle['traccion'])): ?>
                <div class="col-6 col-md-4">
                  <div class="spec-item">
                    <div class="spec-icon"><i class="bi bi-truck"></i></div>
                    <div class="spec-info">
                      <h4>Tracción</h4>
                      <p><?php echo htmlspecialchars($vehicle['traccion']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php if (!empty($vehicle['consumo_combustible'])): ?>
                <div class="col-6 col-md-4">
                  <div class="spec-item">
                    <div class="spec-icon"><i class="bi bi-fuel-pump"></i></div>
                    <div class="spec-info">
                      <h4>Consumo</h4>
                      <p><?php echo htmlspecialchars($vehicle['consumo_combustible']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <?php if (!empty($vehicle['descripcion'])): ?>
            <div class="vehicle-description mt-4">
              <h3 class="description-title">Descripción</h3>
              <p><?php echo nl2br(htmlspecialchars($vehicle['descripcion'])); ?></p>
            </div>
          <?php endif; ?>
        </div>
        
        <div class="plan-info content-card">
          <h3 class="plan-title">Tu Plan: <?php echo htmlspecialchars($userSubscription['plan_nombre']); ?></h3>
          <p class="plan-description">Este vehículo está disponible con tu plan actual.</p>
          <ul class="plan-features">
            <li><i class="bi bi-check-circle-fill"></i> Seguro <?php echo $userSubscription['plan_nombre'] === 'Básico' ? 'a terceros' : ($userSubscription['plan_nombre'] === 'Premium' ? 'a todo riesgo' : 'a todo riesgo con ampliación'); ?></li>
            <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($userSubscription['kilometraje_mensual'] ?? 'Ilimitado'); ?> km mensuales incluidos</li>
            <li><i class="bi bi-check-circle-fill"></i> Asistencia en carretera <?php echo $userSubscription['plan_nombre'] === 'Básico' ? 'no incluida' : 'incluida'; ?></li>
            <?php if ($userSubscription['plan_nombre'] === 'Elite'): ?>
              <li><i class="bi bi-check-circle-fill"></i> Entrega y recogida a domicilio</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
      
      <!-- Reservation Form -->
      <div class="col-lg-6">
        <div class="content-card">
          <h2 class="section-title mb-4"><?php echo $editingReservation ? 'Modificar Reserva' : 'Reservar Vehículo'; ?></h2>
          
          <?php echo $reservationMessage; ?>
          
          <form method="post" action="/public/reserva.php?id=<?php echo $vehicleId; ?><?php echo $editingReservation ? '&edit=' . $editingReservation['id'] : ''; ?>" id="reservationForm">
            <div class="row mb-4">
              <div class="col-md-6 mb-3 mb-md-0">
                <label for="fecha_inicio" class="form-label">Fecha de inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo $defaultStartDate; ?>" required>
              </div>
              <div class="col-md-6">
                <label for="fecha_fin" class="form-label">Fecha de fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo $defaultEndDate; ?>" required>
              </div>
            </div>
            
            <div class="mb-4">
              <label for="comentarios" class="form-label">Comentarios (opcional)</label>
              <textarea class="form-control" id="comentarios" name="comentarios" rows="3" placeholder="Indica aquí cualquier comentario o petición especial"><?php echo $editingReservation ? htmlspecialchars($editingReservation['comentarios']) : ''; ?></textarea>
            </div>
            
            <div class="reservation-summary mb-4">
              <h3 class="summary-title">Resumen de la Reserva</h3>
              <div class="summary-content">
                <div class="row mb-2">
                  <div class="col-5 text-muted">Vehículo:</div>
                  <div class="col-7"><?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?></div>
                </div>
                <div class="row mb-2">
                  <div class="col-5 text-muted">Plan:</div>
                  <div class="col-7"><?php echo htmlspecialchars($userSubscription['plan_nombre']); ?></div>
                </div>
                <div class="row mb-2">
                  <div class="col-5 text-muted">Fecha de inicio:</div>
                  <div class="col-7" id="summary-start-date">-</div>
                </div>
                <div class="row mb-2">
                  <div class="col-5 text-muted">Fecha de fin:</div>
                  <div class="col-7" id="summary-end-date">-</div>
                </div>
                <div class="row mb-2">
                  <div class="col-5 text-muted">Duración:</div>
                  <div class="col-7" id="reservation-duration">-</div>
                </div>
                <div class="row">
                  <div class="col-5 text-muted">Recogida/Entrega:</div>
                  <div class="col-7">Oficina central</div>
                </div>
              </div>
            </div>
            
            <div class="reservation-terms mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms" name="accept_terms" required>
                <label class="form-check-label" for="acceptTerms">
                  Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a> de alquiler
                </label>
              </div>
            </div>
            
            <div class="d-grid">
              <button type="submit" name="submit_reservation" class="btn btn-primary btn-lg"><?php echo $editingReservation ? 'Actualizar Reserva' : 'Confirmar Reserva'; ?></button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones de Alquiler</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h5>1. Condiciones generales</h5>
        <p>El cliente debe tener un mínimo de 25 años de edad y poseer un permiso de conducir válido con al menos 2 años de antigüedad. El vehículo solo podrá ser conducido por el cliente o por conductores adicionales expresamente autorizados por DriveClub.</p>
        
        <h5>2. Recogida y devolución</h5>
        <p>El cliente debe presentar su carnet de conducir original y una tarjeta de crédito a su nombre en el momento de la recogida. El vehículo debe ser devuelto en la fecha acordada y en las mismas condiciones en que fue entregado.</p>
        
        <h5>3. Kilometraje</h5>
        <p>Cada plan incluye un número determinado de kilómetros. El exceso de kilometraje se cobrará a razón de 0,15€ por kilómetro adicional.</p>
        
        <h5>4. Combustible</h5>
        <p>El vehículo se entrega con el depósito lleno y debe devolverse en las mismas condiciones. En caso contrario, se cobrará el combustible faltante más un cargo por servicio de repostaje.</p>
        
        <h5>5. Cancelaciones</h5>
        <p>Las cancelaciones realizadas con más de 48 horas de antelación no tendrán cargo. Para cancelaciones realizadas entre 24 y 48 horas antes, se cobrará el 50% del importe. Para cancelaciones con menos de 24 horas o no presentación, se cobrará el 100% del importe.</p>
        
        <h5>6. Responsabilidad</h5>
        <p>El cliente es responsable de cualquier multa o sanción que reciba durante el período de alquiler. DriveClub se reserva el derecho de cobrar dichos importes a la tarjeta de crédito del cliente.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
