<?php
/**
 * Subscription Confirmation Page
 * 
 * This page allows users to confirm and complete their subscription purchase.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/users.php';

// Require authentication
requireAuth();

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get user data
$userData = getUserById($userId);

// Check if plan ID is provided
if (!isset($_GET['plan']) || empty($_GET['plan'])) {
    setFlashMessage('error', 'Plan no especificado');
    redirect('/public/planes.php');
}

$planId = (int)$_GET['plan'];

// Get plan data
$plan = getSubscriptionPlanById($planId);

if (!$plan) {
    setFlashMessage('error', 'Plan no encontrado');
    redirect('/public/planes.php');
}

// Process subscription form
$subscriptionMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_subscription'])) {
    $paymentMethod = $_POST['payment_method'];
    
    // Check if payment details are complete
    if (!isset($_POST['card_number']) || empty($_POST['card_number']) ||
        !isset($_POST['expiry_date']) || empty($_POST['expiry_date']) ||
        !isset($_POST['cvv']) || empty($_POST['cvv'])) {
        
        $subscriptionMessage = displayError('Por favor, completa todos los campos de pago');
    } else {
        // Create subscription
        list($success, $message, $subscriptionId) = createUserSubscription($userId, $planId, $paymentMethod);
        
        if ($success) {
            setFlashMessage('success', 'Tu suscripción ha sido activada correctamente');
            redirect('/public/mi-cuenta.php?tab=subscription');
        } else {
            $subscriptionMessage = displayError($message);
        }
    }
}

// Get user's current subscription
$currentSubscription = getUserSubscription($userId);

// Set page title and description
$pageTitle = "Confirmar Suscripción";
$pageDescription = "Confirma los detalles de tu suscripción a DriveClub";

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Subscription Banner -->
<section class="subscription-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h1>Confirmar Suscripción</h1>
        <p class="lead">Revisa y confirma los detalles de tu suscripción.</p>
      </div>
    </div>
  </div>
</section>

<!-- Subscription Content -->
<section class="subscription-section py-5">
  <div class="container">
    <div class="row">
      <!-- Subscription Summary -->
      <div class="col-lg-6">
        <div class="content-card">
          <h2 class="section-title mb-4">Resumen de tu Suscripción</h2>
          
          <div class="subscription-details mb-4">
            <div class="plan-header mb-4">
              <div class="d-flex align-items-center">
                <div class="plan-icon me-3">
                  <i class="bi bi-award"></i>
                </div>
                <div>
                  <h3 class="mb-0">Plan <?php echo htmlspecialchars($plan['nombre']); ?></h3>
                  <p class="text-muted mb-0">Suscripción mensual</p>
                </div>
              </div>
            </div>
            
            <div class="plan-features">
              <h4 class="mb-3">Incluye:</h4>
              <ul class="features-list">
                <?php if (!empty($plan['caracteristicas_especiales'])): ?>
                  <?php 
                    $features = json_decode($plan['caracteristicas_especiales'], true);
                    if (is_array($features) && !empty($features)):
                  ?>
                    <?php foreach ($features as $feature): ?>
                      <?php if (isset($feature['included']) && $feature['included']): ?>
                        <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($feature['name']); ?></li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <li><i class="bi bi-check-circle-fill"></i> Acceso a vehículos <?php echo $plan['nombre'] === 'Básico' ? 'compactos' : ($plan['nombre'] === 'Premium' ? 'compactos, Sedán, SUVs' : 'todos, incluyendo deportivos'); ?></li>
                    <li><i class="bi bi-check-circle-fill"></i> Cambio de vehículo <?php echo $plan['nombre'] === 'Básico' ? 'mensual' : ($plan['nombre'] === 'Premium' ? 'cada 2 semanas' : 'ilimitado'); ?></li>
                    <li><i class="bi bi-check-circle-fill"></i> <?php echo htmlspecialchars($plan['kilometraje_mensual'] ?? ($plan['nombre'] === 'Básico' ? '1500' : ($plan['nombre'] === 'Premium' ? '2500' : 'Ilimitado'))); ?> km mensuales</li>
                    <li><i class="bi bi-check-circle-fill"></i> Mantenimiento básico incluido</li>
                    <li><i class="bi bi-check-circle-fill"></i> Seguro <?php echo $plan['nombre'] === 'Básico' ? 'a terceros' : ($plan['nombre'] === 'Premium' ? 'a todo riesgo' : 'a todo riesgo con ampliación'); ?></li>
                    <?php if ($plan['nombre'] !== 'Básico'): ?>
                      <li><i class="bi bi-check-circle-fill"></i> Asistencia en carretera</li>
                    <?php endif; ?>
                    <?php if ($plan['nombre'] === 'Elite'): ?>
                      <li><i class="bi bi-check-circle-fill"></i> Entrega y recogida</li>
                      <li><i class="bi bi-check-circle-fill"></i> Acceso a eventos exclusivos</li>
                    <?php endif; ?>
                  <?php endif; ?>
                <?php endif; ?>
              </ul>
            </div>
            
            <div class="price-breakdown mt-4">
              <h4 class="mb-3">Desglose de precios:</h4>
              <div class="price-item d-flex justify-content-between">
                <span>Cuota mensual Plan <?php echo htmlspecialchars($plan['nombre']); ?></span>
                <span><?php echo formatPrice($plan['precio_mensual']); ?></span>
              </div>
              <div class="price-item d-flex justify-content-between">
                <span>Impuestos (21% IVA)</span>
                <span><?php echo formatPrice($plan['precio_mensual'] * 0.21); ?></span>
              </div>
              <div class="price-total d-flex justify-content-between mt-3">
                <span class="fw-bold">Total mensual</span>
                <span class="fw-bold"><?php echo formatPrice($plan['precio_mensual'] * 1.21); ?></span>
              </div>
            </div>
          </div>
          
          <?php if ($currentSubscription): ?>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <strong>Nota:</strong> Ya tienes una suscripción activa (Plan <?php echo htmlspecialchars($currentSubscription['plan_nombre']); ?>). 
              Al confirmar esta nueva suscripción, tu plan actual se actualizará automáticamente.
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <!-- Payment Form -->
      <div class="col-lg-6">
        <div class="content-card">
          <h2 class="section-title mb-4">Método de Pago</h2>
          
          <?php echo $subscriptionMessage; ?>
          
          <form method="post" action="/public/confirmar_suscripcion.php?plan=<?php echo $planId; ?>" id="paymentForm">
            <div class="payment-methods mb-4">
              <h4 class="mb-3">Selecciona un método de pago:</h4>
              <div class="row g-3">
                <div class="col-6">
                  <div class="form-check payment-option">
                    <input class="form-check-input" type="radio" name="payment_method" id="creditCard" value="tarjeta" checked>
                    <label class="form-check-label" for="creditCard">
                      <i class="bi bi-credit-card me-2"></i>Tarjeta de crédito
                    </label>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-check payment-option">
                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                    <label class="form-check-label" for="paypal">
                      <i class="bi bi-paypal me-2"></i>PayPal
                    </label>
                  </div>
                </div>
              </div>
            </div>
            
            <div id="creditCardForm">
              <div class="mb-3">
                <label for="cardHolder" class="form-label">Nombre del titular</label>
                <input type="text" class="form-control" id="cardHolder" name="card_holder" value="<?php echo htmlspecialchars($userData['nombre'] . ' ' . $userData['apellidos']); ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="cardNumber" class="form-label">Número de tarjeta</label>
                <input type="text" class="form-control" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" required>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="expiryDate" class="form-label">Fecha de caducidad</label>
                  <input type="text" class="form-control" id="expiryDate" name="expiry_date" placeholder="MM/AA" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="cvv" class="form-label">CVV</label>
                  <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
                </div>
              </div>
            </div>
            
            <div id="paypalForm" style="display: none;">
              <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Al hacer clic en "Confirmar Suscripción", serás redirigido a PayPal para completar el pago.
              </div>
            </div>
            
            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="autoRenew" name="auto_renew" value="1" checked>
                <label class="form-check-label" for="autoRenew">
                  Activar renovación automática mensual
                </label>
              </div>
            </div>
            
            <div class="mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="acceptTerms" name="accept_terms" required>
                <label class="form-check-label" for="acceptTerms">
                  Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a> de suscripción
                </label>
              </div>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" name="confirm_subscription" class="btn btn-primary btn-lg">Confirmar Suscripción</button>
              <a href="/public/planes.php" class="btn btn-outline-secondary">Cancelar</a>
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
        <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones de Suscripción</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h5>1. Suscripción y Renovación</h5>
        <p>La suscripción a DriveClub es un servicio de pago mensual que se renueva automáticamente al final de cada período de facturación, a menos que el cliente cancele su suscripción con al menos 30 días de antelación.</p>
        
        <h5>2. Requisitos</h5>
        <p>Para suscribirse a DriveClub, el cliente debe tener al menos 25 años, un permiso de conducir válido con al menos 2 años de antigüedad, y una tarjeta de crédito o débito válida para los pagos mensuales.</p>
        
        <h5>3. Cambio de Plan</h5>
        <p>El cliente puede cambiar su plan de suscripción en cualquier momento. Si cambia a un plan superior, el cambio será efectivo inmediatamente y se cobrará la diferencia proporcional. Si cambia a un plan inferior, el cambio será efectivo al final del ciclo de facturación actual.</p>
        
        <h5>4. Política de Cancelación</h5>
        <p>El cliente puede cancelar su suscripción en cualquier momento con un preaviso de 30 días. No se aplicarán penalizaciones por cancelación siempre que se respete el plazo de preaviso y se devuelvan todos los vehículos en las condiciones acordadas.</p>
        
        <h5>5. Uso del Servicio</h5>
        <p>La suscripción permite al cliente reservar vehículos según las condiciones de su plan. El cliente es responsable del vehículo durante el período de reserva y debe cumplir con todas las normas de tráfico y condiciones de uso del vehículo.</p>
        
        <h5>6. Límites y Excesos</h5>
        <p>Cada plan incluye un número específico de kilómetros mensuales, cambios de vehículo y otros beneficios. El exceso de kilometraje se cobrará a razón de 0,15€ por kilómetro adicional.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Payment Method Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const creditCardRadio = document.getElementById('creditCard');
  const paypalRadio = document.getElementById('paypal');
  const creditCardForm = document.getElementById('creditCardForm');
  const paypalForm = document.getElementById('paypalForm');
  
  if (creditCardRadio && paypalRadio) {
    creditCardRadio.addEventListener('change', function() {
      if (this.checked) {
        creditCardForm.style.display = 'block';
        paypalForm.style.display = 'none';
      }
    });
    
    paypalRadio.addEventListener('change', function() {
      if (this.checked) {
        creditCardForm.style.display = 'none';
        paypalForm.style.display = 'block';
      }
    });
  }
  
  // Simple credit card formatting
  const cardNumber = document.getElementById('cardNumber');
  if (cardNumber) {
    cardNumber.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      let formattedValue = '';
      for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
          formattedValue += ' ';
        }
        formattedValue += value[i];
      }
      e.target.value = formattedValue;
    });
  }
  
  // Simple expiry date formatting
  const expiryDate = document.getElementById('expiryDate');
  if (expiryDate) {
    expiryDate.addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 2) {
        value = value.substr(0, 2) + '/' + value.substr(2, 2);
      }
      e.target.value = value;
    });
  }
  
  // CVV validation
  const cvv = document.getElementById('cvv');
  if (cvv) {
    cvv.addEventListener('input', function(e) {
      e.target.value = e.target.value.replace(/\D/g, '').substr(0, 3);
    });
  }
});
</script>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
