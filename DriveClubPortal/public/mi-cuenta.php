<?php
/**
 * User Account Page
 * 
 * This page displays user account information, reservations, and subscription details.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/users.php';
require_once __DIR__ . '/../includes/reservations.php';

// Require authentication
requireAuth();

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get user data
$userData = getUserById($userId);
if (!$userData) {
    // User not found, redirect to login
    header('Location: /public/logout.php');
    exit;
}

// Get user subscription
$userSubscription = getUserSubscription($userId);

// Get user reservations
$activeReservations = getUserActiveReservations($userId);
$pastReservations = getUserPastReservations($userId);

// Get reservation counts
$reservationCounts = countUserReservationsByState($userId);

// Set the active tab
$activeTab = 'reservations';
if (isset($_GET['tab']) && in_array($_GET['tab'], ['reservations', 'subscription', 'profile'])) {
    $activeTab = $_GET['tab'];
}

// Process profile update
$profileUpdateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $profileData = [
        'nombre' => trim($_POST['nombre']),
        'apellidos' => trim($_POST['apellidos']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono']),
        'direccion' => trim($_POST['direccion']),
        'ciudad' => trim($_POST['ciudad']),
        'codigo_postal' => trim($_POST['codigo_postal']),
        'pais' => trim($_POST['pais']),
        'numero_licencia' => trim($_POST['numero_licencia'])
    ];
    
    // Update profile
    list($success, $message) = updateUserProfile($userId, $profileData);
    
    if ($success) {
        $profileUpdateMessage = displaySuccess($message);
        // Refresh user data
        $userData = getUserById($userId);
    } else {
        $profileUpdateMessage = displayError($message);
    }
}

// Process password update
$passwordUpdateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate passwords
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $passwordUpdateMessage = displayError("Todos los campos son obligatorios");
    } elseif ($newPassword !== $confirmPassword) {
        $passwordUpdateMessage = displayError("Las contraseñas nuevas no coinciden");
    } else {
        // Update password
        list($success, $message) = changePassword($userId, $currentPassword, $newPassword);
        
        if ($success) {
            $passwordUpdateMessage = displaySuccess($message);
        } else {
            $passwordUpdateMessage = displayError($message);
        }
    }
}

// Process reservation cancellation
$reservationMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservationId = (int)$_POST['reservation_id'];
    
    // Cancel reservation
    list($success, $message) = cancelReservation($reservationId, $userId);
    
    if ($success) {
        $reservationMessage = displaySuccess($message);
        // Refresh reservations
        $activeReservations = getUserActiveReservations($userId);
        $pastReservations = getUserPastReservations($userId);
    } else {
        $reservationMessage = displayError($message);
    }
}

// Process review submission
$reviewMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewData = [
        'usuario_id' => $userId,
        'vehiculo_id' => (int)$_POST['vehiculo_id'],
        'reserva_id' => (int)$_POST['reserva_id'],
        'puntuacion' => (int)$_POST['puntuacion'],
        'comentario' => trim($_POST['comentario'])
    ];
    
    // Submit review
    list($success, $message) = createReview($reviewData);
    
    if ($success) {
        $reviewMessage = displaySuccess($message);
    } else {
        $reviewMessage = displayError($message);
    }
}

// Process subscription cancellation
$subscriptionMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_subscription'])) {
    $subscriptionId = (int)$_POST['subscription_id'];
    
    // Cancel subscription
    list($success, $message) = cancelUserSubscription($userId, $subscriptionId);
    
    if ($success) {
        $subscriptionMessage = displaySuccess($message);
        // Refresh subscription
        $userSubscription = getUserSubscription($userId);
    } else {
        $subscriptionMessage = displayError($message);
    }
}

// Set page title
$pageTitle = "Mi Cuenta";
$pageDescription = "Gestiona tus reservas, suscripción y datos personales.";

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Account Banner -->
<section class="account-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h1>Mi Cuenta</h1>
        <p class="lead">Gestiona tus reservas, suscripción y datos personales.</p>
      </div>
    </div>
  </div>
</section>

<!-- Account Content -->
<section class="account-section py-5">
  <div class="container">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-lg-3 mb-4 mb-lg-0">
        <div class="account-sidebar">
          <div class="user-info mb-4">
            <div class="user-avatar">
              <?php if (!empty($userData['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($userData['avatar']); ?>" alt="<?php echo htmlspecialchars($userData['nombre']); ?>" class="img-fluid rounded-circle">
              <?php else: ?>
                <img src="https://i.pravatar.cc/150?img=12" alt="User Profile" class="img-fluid rounded-circle">
              <?php endif; ?>
            </div>
            <div class="user-details">
              <h4><?php echo htmlspecialchars($userData['nombre'] . ' ' . $userData['apellidos']); ?></h4>
              <?php if ($userSubscription): ?>
                <p class="plan-badge"><?php echo htmlspecialchars($userSubscription['plan_nombre']); ?></p>
              <?php else: ?>
                <p class="plan-badge bg-secondary">Sin suscripción activa</p>
              <?php endif; ?>
            </div>
          </div>
          <ul class="nav flex-column account-nav" id="accountTabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link <?php echo $activeTab === 'reservations' ? 'active' : ''; ?>" id="reservations-tab" data-bs-toggle="tab" href="#reservations" role="tab" aria-controls="reservations" aria-selected="<?php echo $activeTab === 'reservations' ? 'true' : 'false'; ?>">
                <i class="bi bi-calendar-check me-2"></i>Mis Reservas
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo $activeTab === 'subscription' ? 'active' : ''; ?>" id="subscription-tab" data-bs-toggle="tab" href="#subscription" role="tab" aria-controls="subscription" aria-selected="<?php echo $activeTab === 'subscription' ? 'true' : 'false'; ?>">
                <i class="bi bi-card-list me-2"></i>Mi Suscripción
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?php echo $activeTab === 'profile' ? 'active' : ''; ?>" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="<?php echo $activeTab === 'profile' ? 'true' : 'false'; ?>">
                <i class="bi bi-person me-2"></i>Mi Perfil
              </a>
            </li>
            <li class="nav-item mt-5">
              <a class="nav-link text-danger" href="/public/logout.php">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Content Area -->
      <div class="col-lg-9">
        <div class="tab-content account-content" id="accountTabsContent">
          <!-- Reservations Tab -->
          <div class="tab-pane fade <?php echo $activeTab === 'reservations' ? 'show active' : ''; ?>" id="reservations" role="tabpanel" aria-labelledby="reservations-tab">
            <div class="content-card">
              <h2 class="section-title mb-4">Mis Reservas</h2>
              
              <?php echo $reservationMessage; ?>
              
              <div class="reservation-stats mb-4">
                <div class="row g-3">
                  <div class="col-6 col-md-3">
                    <div class="stat-card">
                      <div class="stat-value"><?php echo $reservationCounts['Activa'] ?? 0; ?></div>
                      <div class="stat-label">Activas</div>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="stat-card">
                      <div class="stat-value"><?php echo $reservationCounts['Pendiente'] ?? 0; ?></div>
                      <div class="stat-label">Pendientes</div>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="stat-card">
                      <div class="stat-value"><?php echo $reservationCounts['Completada'] ?? 0; ?></div>
                      <div class="stat-label">Completadas</div>
                    </div>
                  </div>
                  <div class="col-6 col-md-3">
                    <div class="stat-card">
                      <div class="stat-value"><?php echo $reservationCounts['Cancelada'] ?? 0; ?></div>
                      <div class="stat-label">Canceladas</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <ul class="nav nav-tabs mb-4 border-bottom border-2" id="reservationTabs" role="tablist">
                <li class="nav-item w-50 text-center" role="presentation">
                  <button class="nav-link active w-100" id="current-tab" data-bs-toggle="tab" data-bs-target="#current-reservations" type="button" role="tab" aria-controls="current-reservations" aria-selected="true">Actuales</button>
                </li>
                <li class="nav-item w-50 text-center" role="presentation">
                  <button class="nav-link w-100" id="past-tab" data-bs-toggle="tab" data-bs-target="#past-reservations" type="button" role="tab" aria-controls="past-reservations" aria-selected="false">Historial</button>
                </li>
              </ul>
              
              <div class="tab-content" id="reservationTabsContent">
                <div class="tab-pane fade show active" id="current-reservations" role="tabpanel" aria-labelledby="current-tab">
                  <?php if (empty($activeReservations)): ?>
                    <div class="alert alert-info">
                      No tienes reservas activas en este momento.
                      <a href="/public/vehiculos.php" class="alert-link">Explora nuestros vehículos</a>
                    </div>
                  <?php else: ?>
                    <?php foreach ($activeReservations as $reservation): ?>
                      <div class="reservation-card">
                        <div class="row align-items-center">
                          <div class="col-md-3">
                            <?php if (!empty($reservation['imagen'])): ?>
                              <img src="<?php echo htmlspecialchars($reservation['imagen']); ?>" alt="<?php echo htmlspecialchars($reservation['vehiculo_nombre']); ?>" class="img-fluid rounded">
                            <?php else: ?>
                              <div class="no-image">
                                <i class="bi bi-car-front"></i>
                              </div>
                            <?php endif; ?>
                          </div>
                          <div class="col-md-9">
                            <div class="reservation-details">
                              <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                                <div>
                                  <h4 class="mb-2"><?php echo htmlspecialchars($reservation['marca_nombre'] . ' ' . $reservation['vehiculo_nombre']); ?></h4>
                                  <span class="vehicle-type"><?php echo htmlspecialchars($reservation['tipo_nombre']); ?></span>
                                </div>
                                <div class="qr-code mt-2 mt-md-0">
                                  <a href="#" class="view-qr-code" data-reservation="<?php echo $reservation['id']; ?>">
                                    <i class="bi bi-qr-code"></i>
                                    <span>Ver QR</span>
                                  </a>
                                </div>
                              </div>
                              <div class="reservation-dates mb-3">
                                <span class="date-badge"><i class="bi bi-calendar-event me-1"></i> <?php echo formatDate($reservation['fecha_inicio']); ?> - <?php echo formatDate($reservation['fecha_fin']); ?></span>
                              </div>
                              <div class="reservation-status mb-3">
                                <span class="status-badge <?php echo strtolower($reservation['estado_nombre']); ?>"><?php echo htmlspecialchars($reservation['estado_nombre']); ?></span>
                              </div>
                              <div class="reservation-actions">
                                <form method="post" action="/public/mi-cuenta.php?tab=reservations" onsubmit="return confirm('¿Estás seguro de querer cancelar esta reserva?');">
                                  <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                  <button type="submit" name="cancel_reservation" class="btn btn-sm btn-outline-danger">Cancelar Reserva</button>
                                </form>
                                <?php if ($reservation['estado_nombre'] === 'Pendiente'): ?>
                                  <a href="/public/reserva.php?id=<?php echo $reservation['vehiculo_id']; ?>&edit=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-outline-secondary">Modificar Fechas</a>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
                
                <div class="tab-pane fade" id="past-reservations" role="tabpanel" aria-labelledby="past-tab">
                  <?php if (empty($pastReservations)): ?>
                    <div class="alert alert-info">
                      No tienes reservas completadas o canceladas en tu historial.
                    </div>
                  <?php else: ?>
                    <?php foreach ($pastReservations as $reservation): ?>
                      <div class="reservation-card">
                        <div class="row align-items-center">
                          <div class="col-md-3">
                            <?php if (!empty($reservation['imagen'])): ?>
                              <img src="<?php echo htmlspecialchars($reservation['imagen']); ?>" alt="<?php echo htmlspecialchars($reservation['vehiculo_nombre']); ?>" class="img-fluid rounded">
                            <?php else: ?>
                              <div class="no-image">
                                <i class="bi bi-car-front"></i>
                              </div>
                            <?php endif; ?>
                          </div>
                          <div class="col-md-9">
                            <div class="reservation-details">
                              <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                  <h4 class="mb-2"><?php echo htmlspecialchars($reservation['marca_nombre'] . ' ' . $reservation['vehiculo_nombre']); ?></h4>
                                  <span class="vehicle-type"><?php echo htmlspecialchars($reservation['tipo_nombre']); ?></span>
                                </div>
                              </div>
                              <div class="reservation-dates mb-3">
                                <span class="date-badge"><i class="bi bi-calendar-event me-1"></i> <?php echo formatDate($reservation['fecha_inicio']); ?> - <?php echo formatDate($reservation['fecha_fin']); ?></span>
                              </div>
                              <div class="reservation-status mb-3">
                                <span class="status-badge <?php echo strtolower($reservation['estado_nombre']); ?>"><?php echo htmlspecialchars($reservation['estado_nombre']); ?></span>
                              </div>
                              <div class="reservation-actions">
                                <a href="/public/reserva.php?id=<?php echo $reservation['vehiculo_id']; ?>" class="btn btn-sm btn-outline-primary">Reservar de Nuevo</a>
                                <?php if ($reservation['estado_nombre'] === 'Completada' && canMakeReview($reservation['id'], $userId)): ?>
                                  <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $reservation['id']; ?>">Dejar Opinión</button>
                                  
                                  <!-- Review Modal -->
                                  <div class="modal fade" id="reviewModal<?php echo $reservation['id']; ?>" tabindex="-1" aria-labelledby="reviewModalLabel<?php echo $reservation['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="reviewModalLabel<?php echo $reservation['id']; ?>">Dejar una opinión sobre <?php echo htmlspecialchars($reservation['marca_nombre'] . ' ' . $reservation['vehiculo_nombre']); ?></h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="post" action="/public/mi-cuenta.php?tab=reservations">
                                          <div class="modal-body">
                                            <div class="mb-3">
                                              <label for="rating<?php echo $reservation['id']; ?>" class="form-label">Puntuación</label>
                                              <select class="form-select" id="rating<?php echo $reservation['id']; ?>" name="puntuacion" required>
                                                <option value="">Selecciona una puntuación</option>
                                                <option value="5">5 estrellas - Excelente</option>
                                                <option value="4">4 estrellas - Muy bueno</option>
                                                <option value="3">3 estrellas - Bueno</option>
                                                <option value="2">2 estrellas - Regular</option>
                                                <option value="1">1 estrella - Malo</option>
                                              </select>
                                            </div>
                                            <div class="mb-3">
                                              <label for="comment<?php echo $reservation['id']; ?>" class="form-label">Comentario</label>
                                              <textarea class="form-control" id="comment<?php echo $reservation['id']; ?>" name="comentario" rows="3" placeholder="Comparte tu experiencia con este vehículo" required></textarea>
                                            </div>
                                            <input type="hidden" name="reserva_id" value="<?php echo $reservation['id']; ?>">
                                            <input type="hidden" name="vehiculo_id" value="<?php echo $reservation['vehiculo_id']; ?>">
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" name="submit_review" class="btn btn-primary">Enviar Opinión</button>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                  </div>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </div>
              
              <?php if (empty($activeReservations) && $userSubscription): ?>
                <div class="mt-4 text-center">
                  <a href="/public/vehiculos.php" class="btn btn-primary">Realizar una nueva reserva</a>
                </div>
              <?php elseif (!$userSubscription): ?>
                <div class="mt-4 text-center">
                  <div class="alert alert-warning">
                    Para realizar reservas, necesitas una suscripción activa.
                    <a href="/public/planes.php" class="alert-link">Ver planes de suscripción</a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Subscription Tab -->
          <div class="tab-pane fade <?php echo $activeTab === 'subscription' ? 'show active' : ''; ?>" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
            <div class="content-card">
              <h2 class="section-title mb-4">Mi Suscripción</h2>
              
              <?php echo $subscriptionMessage; ?>
              
              <?php if ($userSubscription): ?>
                <!-- Current Subscription -->
                <div class="current-plan mb-5">
                  <div class="row align-items-center">
                    <div class="col-md-4">
                      <div class="plan-icon-container">
                        <i class="bi bi-award"></i>
                        <h3><?php echo htmlspecialchars($userSubscription['plan_nombre']); ?></h3>
                      </div>
                    </div>
                    <div class="col-md-8">
                      <div class="plan-details">
                        <div class="mb-3">
                          <span class="text-muted">Estado:</span>
                          <span class="status-badge active ms-2">Activo</span>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted">Fecha de inicio:</span>
                          <span class="ms-2"><?php echo formatDate($userSubscription['fecha_inicio']); ?></span>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted">Renovación automática:</span>
                          <span class="ms-2"><?php echo $userSubscription['renovacion_automatica'] ? 'Activada' : 'Desactivada'; ?></span>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted">Próximo pago:</span>
                          <span class="ms-2"><?php echo formatDate($userSubscription['fecha_proximo_pago']); ?></span>
                        </div>
                        <div class="mb-3">
                          <span class="text-muted">Precio mensual:</span>
                          <span class="ms-2"><?php echo formatPrice($userSubscription['precio_mensual']); ?></span>
                        </div>
                        <div class="plan-actions">
                          <a href="/public/planes.php" class="btn btn-sm btn-outline-primary">Cambiar de Plan</a>
                          <form method="post" action="/public/mi-cuenta.php?tab=subscription" class="d-inline-block" onsubmit="return confirm('¿Estás seguro de querer cancelar la renovación automática? Tu plan seguirá activo hasta el final del periodo actual.');">
                            <input type="hidden" name="subscription_id" value="<?php echo $userSubscription['id']; ?>">
                            <?php if ($userSubscription['renovacion_automatica']): ?>
                              <button type="submit" name="cancel_subscription" class="btn btn-sm btn-outline-danger">Cancelar Renovación Automática</button>
                            <?php endif; ?>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Subscription Details -->
                <div class="subscription-details mb-5">
                  <h4 class="mb-3">Detalles del Plan</h4>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <div class="detail-card">
                        <div class="detail-icon">
                          <i class="bi bi-speedometer2"></i>
                        </div>
                        <div class="detail-content">
                          <h5>Kilometraje Mensual</h5>
                          <p><?php echo htmlspecialchars($userSubscription['kilometraje_mensual'] ?? 'Ilimitado'); ?> km</p>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <div class="detail-card">
                        <div class="detail-icon">
                          <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="detail-content">
                          <h5>Cambios de Vehículo</h5>
                          <p><?php echo $userSubscription['limite_vehiculos'] ? htmlspecialchars($userSubscription['limite_vehiculos']) . ' al mes' : 'Ilimitados'; ?></p>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <div class="detail-card">
                        <div class="detail-icon">
                          <i class="bi bi-calendar2-week"></i>
                        </div>
                        <div class="detail-content">
                          <h5>Duración de Reservas</h5>
                          <p>Hasta <?php echo htmlspecialchars($userSubscription['limite_duracion'] ?? '30'); ?> días</p>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <div class="detail-card">
                        <div class="detail-icon">
                          <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="detail-content">
                          <h5>Tipo de Seguro</h5>
                          <p><?php echo $userSubscription['plan_nombre'] === 'Básico' ? 'A terceros' : ($userSubscription['plan_nombre'] === 'Premium' ? 'A todo riesgo' : 'A todo riesgo con ampliación'); ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Payment History -->
                <div class="payment-history">
                  <h4 class="mb-3">Historial de Pagos</h4>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Descripción</th>
                          <th>Importe</th>
                          <th>Estado</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td><?php echo formatDate($userSubscription['fecha_ultimo_pago']); ?></td>
                          <td>Plan <?php echo htmlspecialchars($userSubscription['plan_nombre']); ?> - Mensualidad</td>
                          <td><?php echo formatPrice($userSubscription['precio_mensual']); ?></td>
                          <td><span class="badge bg-success">Pagado</span></td>
                        </tr>
                        <tr>
                          <td><?php echo formatDate(date('Y-m-d', strtotime('-1 month', strtotime($userSubscription['fecha_ultimo_pago'])))); ?></td>
                          <td>Plan <?php echo htmlspecialchars($userSubscription['plan_nombre']); ?> - Mensualidad</td>
                          <td><?php echo formatPrice($userSubscription['precio_mensual']); ?></td>
                          <td><span class="badge bg-success">Pagado</span></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php else: ?>
                <!-- No Subscription -->
                <div class="no-subscription text-center py-4">
                  <div class="mb-4">
                    <i class="bi bi-exclamation-circle display-1"></i>
                  </div>
                  <h3 class="mb-3">No tienes una suscripción activa</h3>
                  <p class="mb-4">Para disfrutar de nuestros servicios, elige un plan que se adapte a tus necesidades.</p>
                  <a href="/public/planes.php" class="btn btn-primary">Ver Planes de Suscripción</a>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <!-- Profile Tab -->
          <div class="tab-pane fade <?php echo $activeTab === 'profile' ? 'show active' : ''; ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            <div class="content-card">
              <h2 class="section-title mb-4">Mi Perfil</h2>
              
              <?php if (!isProfileComplete($userId)): ?>
                <div class="alert alert-warning mb-4">
                  <i class="bi bi-exclamation-triangle me-2"></i>
                  Tu perfil está incompleto. Por favor, completa todos los campos para poder realizar reservas.
                  <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo getProfileCompletionPercentage($userId); ?>%;" aria-valuenow="<?php echo getProfileCompletionPercentage($userId); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              <?php endif; ?>
              
              <?php echo $profileUpdateMessage; ?>
              
              <div class="profile-tabs mb-4">
                <ul class="nav nav-tabs" id="profileDetailTabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="personal-info-tab" data-bs-toggle="tab" data-bs-target="#personal-info" type="button" role="tab" aria-controls="personal-info" aria-selected="true">Información Personal</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">Seguridad</button>
                  </li>
                </ul>
              </div>
              
              <div class="tab-content" id="profileTabContent">
                <!-- Personal Information Tab -->
                <div class="tab-pane fade show active" id="personal-info" role="tabpanel" aria-labelledby="personal-info-tab">
                  <form method="post" action="/public/mi-cuenta.php?tab=profile">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($userData['nombre']); ?>" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="apellidos" class="form-label">Apellidos *</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($userData['apellidos']); ?>" required>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono *</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($userData['telefono'] ?? ''); ?>" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="direccion" class="form-label">Dirección *</label>
                      <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($userData['direccion'] ?? ''); ?>" required>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="ciudad" class="form-label">Ciudad *</label>
                        <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($userData['ciudad'] ?? ''); ?>" required>
                      </div>
                      <div class="col-md-3 mb-3">
                        <label for="codigo_postal" class="form-label">Código Postal *</label>
                        <input type="text" class="form-control" id="codigo_postal" name="codigo_postal" value="<?php echo htmlspecialchars($userData['codigo_postal'] ?? ''); ?>" required>
                      </div>
                      <div class="col-md-3 mb-3">
                        <label for="pais" class="form-label">País *</label>
                        <input type="text" class="form-control" id="pais" name="pais" value="<?php echo htmlspecialchars($userData['pais'] ?? ''); ?>" required>
                      </div>
                    </div>
                    <div class="mb-4">
                      <label for="numero_licencia" class="form-label">Número de Licencia de Conducir *</label>
                      <input type="text" class="form-control" id="numero_licencia" name="numero_licencia" value="<?php echo htmlspecialchars($userData['numero_licencia'] ?? ''); ?>" required>
                    </div>
                    <input type="hidden" name="update_profile" value="1">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                  </form>
                </div>
                
                <!-- Security Tab -->
                <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                  <?php echo $passwordUpdateMessage; ?>
                  
                  <form method="post" action="/public/mi-cuenta.php?tab=profile" id="passwordForm">
                    <div class="mb-3">
                      <label for="current_password" class="form-label">Contraseña Actual *</label>
                      <div class="password-input">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('current_password', this)">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </div>
                    <div class="mb-3">
                      <label for="new_password" class="form-label">Nueva Contraseña *</label>
                      <div class="password-input">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('new_password', this)">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                      <div class="password-strength mt-2">
                        <div class="strength-bar">
                          <div class="strength-indicator" id="strengthIndicator" style="width: 0%"></div>
                        </div>
                        <small class="strength-text" id="strengthText">La contraseña debe tener al menos 8 caracteres</small>
                      </div>
                    </div>
                    <div class="mb-4">
                      <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña *</label>
                      <div class="password-input">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </div>
                    <input type="hidden" name="update_password" value="1">
                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                  </form>
                  
                  <hr class="my-4">
                  
                  <div class="connected-accounts">
                    <h4 class="mb-3">Cuentas Conectadas</h4>
                    <div class="account-item">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <i class="bi bi-google me-3 fs-4"></i>
                          <div>
                            <h5 class="mb-0">Google</h5>
                            <p class="text-muted mb-0"><?php echo !empty($userData['google_id']) ? htmlspecialchars($userData['email']) : 'No conectado'; ?></p>
                          </div>
                        </div>
                        <?php if (!empty($userData['google_id'])): ?>
                          <span class="badge bg-success">Conectado</span>
                        <?php else: ?>
                          <a href="/public/auth_google.php" class="btn btn-sm btn-outline-secondary">Conectar</a>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrCodeModalLabel">Código QR de Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <h4 id="qrReservationId" class="mb-3"></h4>
        <div class="qr-code-container mb-3">
          <img id="qrImage" src="" alt="QR Code" class="img-fluid">
        </div>
        <p class="mb-0 text-muted">Muestra este código al recoger y devolver el vehículo</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <a href="#" id="downloadQrCode" class="btn btn-primary" download>Descargar QR</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Password strength check
  const newPassword = document.getElementById('new_password');
  const strengthIndicator = document.getElementById('strengthIndicator');
  const strengthText = document.getElementById('strengthText');
  
  if (newPassword && strengthIndicator && strengthText) {
    newPassword.addEventListener('input', function() {
      checkPasswordStrength(this.value);
    });
  }
  
  // Download QR code
  const downloadQrBtn = document.getElementById('downloadQrCode');
  const qrImage = document.getElementById('qrImage');
  
  if (downloadQrBtn && qrImage) {
    downloadQrBtn.addEventListener('click', function() {
      downloadQrBtn.href = qrImage.src;
    });
  }
  
  // Function to toggle password visibility
  window.togglePasswordVisibility = function(inputId, button) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
      input.type = 'text';
      button.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
      input.type = 'password';
      button.innerHTML = '<i class="bi bi-eye"></i>';
    }
  };
  
  // Function to check password strength
  window.checkPasswordStrength = function(password) {
    const strengthIndicator = document.getElementById('strengthIndicator');
    const strengthText = document.getElementById('strengthText');
    
    if (!strengthIndicator || !strengthText) return;
    
    let strength = 0;
    let tips = [];
    
    // Basic length check
    if (password.length >= 8) {
      strength += 25;
    } else {
      tips.push("La contraseña debe tener al menos 8 caracteres");
    }
    
    // Check for uppercase letters
    if (password.match(/[A-Z]/)) {
      strength += 25;
    } else {
      tips.push("Incluye al menos una letra mayúscula");
    }
    
    // Check for numbers
    if (password.match(/[0-9]/)) {
      strength += 25;
    } else {
      tips.push("Incluye al menos un número");
    }
    
    // Check for special characters
    if (password.match(/[^A-Za-z0-9]/)) {
      strength += 25;
    } else {
      tips.push("Incluye al menos un carácter especial");
    }
    
    // Update the strength indicator
    strengthIndicator.style.width = strength + '%';
    
    // Change color based on strength
    if (strength < 50) {
      strengthIndicator.style.backgroundColor = '#e63946';
    } else if (strength < 75) {
      strengthIndicator.style.backgroundColor = '#ffd166';
    } else {
      strengthIndicator.style.backgroundColor = '#2ecc71';
    }
    
    // Update the strength text
    if (tips.length > 0) {
      strengthText.textContent = tips[0];
    } else {
      strengthText.textContent = "Contraseña segura";
    }
  };
  
  // Password form validation
  const passwordForm = document.getElementById('passwordForm');
  if (passwordForm) {
    passwordForm.addEventListener('submit', function(e) {
      const newPass = document.getElementById('new_password').value;
      const confirmPass = document.getElementById('confirm_password').value;
      
      if (newPass !== confirmPass) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
      }
      
      // Check password strength
      if (newPass.length < 8 || !newPass.match(/[A-Z]/) || !newPass.match(/[0-9]/) || !newPass.match(/[^A-Za-z0-9]/)) {
        if (!confirm('La contraseña no cumple con todos los requisitos de seguridad. ¿Deseas continuar de todos modos?')) {
          e.preventDefault();
          return false;
        }
      }
      
      return true;
    });
  }
});
</script>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
