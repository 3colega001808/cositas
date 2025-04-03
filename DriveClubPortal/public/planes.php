<?php
/**
 * Subscription Plans Page
 * 
 * This page displays the available subscription plans.
 */

// Set page title and description
$pageTitle = "Planes de Suscripción";
$pageDescription = "Descubre nuestros planes de suscripción para alquiler de vehículos";

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/users.php';
require_once __DIR__ . '/../includes/session.php';

// Track user session
trackUserSession();

// Get all subscription plans
$subscriptionPlans = getAllSubscriptionPlans();

// Process plan selection if user is logged in
$planSelectionMessage = '';
if (isset($_GET['select']) && isLoggedIn()) {
    $planId = (int)$_GET['select'];
    
    // Verify plan exists
    $selectedPlan = null;
    foreach ($subscriptionPlans as $plan) {
        if ($plan['id'] == $planId) {
            $selectedPlan = $plan;
            break;
        }
    }
    
    if ($selectedPlan) {
        // Redirect to subscription confirmation
        header('Location: /public/confirmar_suscripcion.php?plan=' . $planId);
        exit;
    }
}

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Plans Banner -->
<section class="plans-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h1>Planes de Suscripción</h1>
        <p class="lead">Elige el plan que mejor se adapte a tus necesidades y estilo de vida.</p>
      </div>
    </div>
  </div>
</section>

<!-- Plans Section -->
<section class="plans-section py-5">
  <div class="container">
    <h2 class="section-title text-center mb-5">Nuestros Planes de Suscripción</h2>
    
    <?php if (!empty($planSelectionMessage)): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo $planSelectionMessage; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <div class="row g-4 justify-content-center">
      <?php if (empty($subscriptionPlans)): ?>
        <div class="col-12 text-center">
          <p>No hay planes de suscripción disponibles en este momento.</p>
        </div>
      <?php else: ?>
        <?php foreach ($subscriptionPlans as $index => $plan): ?>
          <div class="col-md-6 col-lg-4" id="plan-<?php echo $plan['id']; ?>">
            <div class="plan-card <?php echo ($index === 1) ? 'featured' : ''; ?>" style="opacity: 1; transform: translateY(0px); transition: opacity 0.5s, transform 0.5s;">
              <?php if ($index === 1): ?>
                <div class="plan-badge">Más Popular</div>
              <?php endif; ?>
              <div class="plan-header">
                <h3><?php echo htmlspecialchars($plan['nombre']); ?></h3>
                <p class="price"><?php echo formatPrice($plan['precio_mensual']); ?><span>/mes</span></p>
              </div>
              <div class="plan-body">
                <?php if (!empty($plan['caracteristicas_especiales'])): ?>
                  <?php 
                    $features = json_decode($plan['caracteristicas_especiales'], true);
                    if (is_array($features) && !empty($features)):
                  ?>
                    <ul class="plan-features">
                      <?php foreach ($features as $feature): ?>
                        <li>
                          <i class="bi <?php echo (isset($feature['included']) && $feature['included']) ? 'bi-check-circle' : 'bi-x-lg text-danger'; ?>"></i>
                          <?php echo htmlspecialchars($feature['name']); ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <ul class="plan-features">
                      <li><i class="bi bi-check-circle"></i> Acceso a vehículos <?php echo $index === 0 ? 'compactos' : ($index === 1 ? 'compactos, Sedán, SUVs' : 'todos, incluyendo deportivos'); ?></li>
                      <li><i class="bi bi-check-circle"></i> Cambio de vehículo <?php echo $index === 0 ? 'mensual' : ($index === 1 ? 'cada 2 semanas' : 'ilimitado'); ?></li>
                      <li><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($plan['kilometraje_mensual'] ?? ($index === 0 ? '1500' : ($index === 1 ? '2500' : 'Ilimitado'))); ?> km mensuales</li>
                      <li><i class="bi bi-check-circle"></i> Mantenimiento básico incluido</li>
                      <li><i class="bi bi-check-circle"></i> Seguro <?php echo $index === 0 ? 'a terceros' : ($index === 1 ? 'a todo riesgo' : 'a todo riesgo con ampliación'); ?></li>
                      <?php if ($index >= 1): ?>
                        <li><i class="bi bi-check-circle"></i> Asistencia en carretera</li>
                      <?php else: ?>
                        <li><i class="bi bi-x-lg text-danger"></i> Asistencia en carretera</li>
                      <?php endif; ?>
                      <?php if ($index >= 2): ?>
                        <li><i class="bi bi-check-circle"></i> Entrega y recogida</li>
                        <li><i class="bi bi-check-circle"></i> Acceso a eventos exclusivos</li>
                      <?php else: ?>
                        <li><i class="bi bi-x-lg text-danger"></i> Entrega y recogida</li>
                        <li><i class="bi bi-x-lg text-danger"></i> Acceso a eventos exclusivos</li>
                      <?php endif; ?>
                    </ul>
                  <?php endif; ?>
                <?php else: ?>
                  <ul class="plan-features">
                    <li><i class="bi bi-check-circle"></i> Acceso a vehículos <?php echo $index === 0 ? 'compactos' : ($index === 1 ? 'compactos, Sedán, SUVs' : 'todos, incluyendo deportivos'); ?></li>
                    <li><i class="bi bi-check-circle"></i> Cambio de vehículo <?php echo $index === 0 ? 'mensual' : ($index === 1 ? 'cada 2 semanas' : 'ilimitado'); ?></li>
                    <li><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($plan['kilometraje_mensual'] ?? ($index === 0 ? '1500' : ($index === 1 ? '2500' : 'Ilimitado'))); ?> km mensuales</li>
                    <li><i class="bi bi-check-circle"></i> Mantenimiento básico incluido</li>
                    <li><i class="bi bi-check-circle"></i> Seguro <?php echo $index === 0 ? 'a terceros' : ($index === 1 ? 'a todo riesgo' : 'a todo riesgo con ampliación'); ?></li>
                    <?php if ($index >= 1): ?>
                      <li><i class="bi bi-check-circle"></i> Asistencia en carretera</li>
                    <?php else: ?>
                      <li><i class="bi bi-x-lg text-danger"></i> Asistencia en carretera</li>
                    <?php endif; ?>
                    <?php if ($index >= 2): ?>
                      <li><i class="bi bi-check-circle"></i> Entrega y recogida</li>
                      <li><i class="bi bi-check-circle"></i> Acceso a eventos exclusivos</li>
                    <?php else: ?>
                      <li><i class="bi bi-x-lg text-danger"></i> Entrega y recogida</li>
                      <li><i class="bi bi-x-lg text-danger"></i> Acceso a eventos exclusivos</li>
                    <?php endif; ?>
                  </ul>
                <?php endif; ?>
                
                <?php if (isLoggedIn()): ?>
                  <a href="/public/confirmar_suscripcion.php?plan=<?php echo $plan['id']; ?>" class="btn <?php echo ($index === 1) ? 'btn-primary' : 'btn-outline-primary'; ?> w-100">Elegir Plan</a>
                <?php else: ?>
                  <a href="/public/login.php?redirect=/public/planes.php" class="btn <?php echo ($index === 1) ? 'btn-primary' : 'btn-outline-primary'; ?> w-100">Iniciar Sesión para Elegir</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Comparison Features Section -->
<section class="features-comparison py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center mb-5">Características Detalladas</h2>
    
    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="feature-card">
          <div class="icon-container">
            <i class="bi bi-car-front"></i>
          </div>
          <h3>Vehículos Disponibles</h3>
          <p><strong>Básico:</strong> Compactos</p>
          <p><strong>Premium:</strong> Compactos, Sedán, SUVs</p>
          <p><strong>Elite:</strong> Todos, incluyendo deportivos</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="feature-card">
          <div class="icon-container">
            <i class="bi bi-arrow-repeat"></i>
          </div>
          <h3>Cambio de Vehículo</h3>
          <p><strong>Básico:</strong> Mensual</p>
          <p><strong>Premium:</strong> Cada 2 semanas</p>
          <p><strong>Elite:</strong> Ilimitado</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="feature-card">
          <div class="icon-container">
            <i class="bi bi-speedometer2"></i>
          </div>
          <h3>Kilometraje</h3>
          <p><strong>Básico:</strong> 1.500 km/mes</p>
          <p><strong>Premium:</strong> 2.500 km/mes</p>
          <p><strong>Elite:</strong> Ilimitado</p>
        </div>
      </div>
      
      <div class="col-md-6 col-lg-3">
        <div class="feature-card">
          <div class="icon-container">
            <i class="bi bi-shield-check"></i>
          </div>
          <h3>Seguro</h3>
          <p><strong>Básico:</strong> A terceros</p>
          <p><strong>Premium:</strong> A todo riesgo</p>
          <p><strong>Elite:</strong> A todo riesgo con ampliación</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Join Section -->
<section class="why-join-section py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <h2 class="section-title mb-4">¿Por qué unirte a DriveClub?</h2>
        <div class="why-join-feature mb-4">
          <div class="icon-container">
            <i class="bi bi-cash-coin"></i>
          </div>
          <div>
            <h3>Sin inversión inicial</h3>
            <p>Olvídate de la entrada inicial, depreciación y gastos ocultos asociados a la compra de un vehículo.</p>
          </div>
        </div>
        <div class="why-join-feature mb-4">
          <div class="icon-container">
            <i class="bi bi-gear"></i>
          </div>
          <div>
            <h3>Sin preocupaciones de mantenimiento</h3>
            <p>Nos encargamos de todas las revisiones y mantenimiento para que disfrutes de tu vehículo sin complicaciones.</p>
          </div>
        </div>
        <div class="why-join-feature mb-4">
          <div class="icon-container">
            <i class="bi bi-arrow-repeat"></i>
          </div>
          <div>
            <h3>Variedad y flexibilidad</h3>
            <p>Cambia de vehículo según tus necesidades: un SUV para el fin de semana, un deportivo para impresionar o un eléctrico para el día a día.</p>
          </div>
        </div>
        <div class="why-join-feature">
          <div class="icon-container">
            <i class="bi bi-shield-check"></i>
          </div>
          <div>
            <h3>Tranquilidad total</h3>
            <p>Vehículos nuevos, revisados y con todas las garantías. Además, nuestros planes incluyen seguros adaptados a cada necesidad.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mt-4 mt-lg-0">
        <div class="why-join-image">
          <img src="https://images.unsplash.com/photo-1532581140115-3e355d1ed1de?q=80&w=1770&auto=format&fit=crop" alt="Experiencia DriveClub" class="img-fluid rounded">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ Section -->
<section class="faq-section py-5">
  <div class="container">
    <h2 class="section-title text-center mb-5">Preguntas Frecuentes</h2>
    <div class="accordion" id="faqAccordion">
      <div class="accordion-item">
        <h3 class="accordion-header">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
            ¿Cómo funciona la suscripción a DriveClub?
          </button>
        </h3>
        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <p>El funcionamiento es muy sencillo:</p>
            <ol>
              <li>Elige el plan de suscripción que mejor se adapte a tus necesidades.</li>
              <li>Regístrate y completa tu perfil.</li>
              <li>Selecciona el vehículo que deseas conducir.</li>
              <li>Recoge tu vehículo en nuestra oficina o solicita entrega a domicilio (disponible según plan).</li>
              <li>¡Disfruta de la libertad de conducir sin ataduras!</li>
            </ol>
            <p>Puedes cambiar de vehículo según la frecuencia permitida por tu plan, o actualizar tu plan en cualquier momento.</p>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
            ¿Qué incluye el precio mensual?
          </button>
        </h3>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <p>El precio mensual incluye:</p>
            <ul>
              <li>Uso del vehículo seleccionado</li>
              <li>Seguro (cobertura según el plan elegido)</li>
              <li>Mantenimiento regular del vehículo</li>
              <li>Asistencia en carretera (en planes Premium y Elite)</li>
              <li>Impuestos de circulación</li>
              <li>Cambio de vehículo según frecuencia del plan elegido</li>
            </ul>
            <p>El combustible, peajes y multas no están incluidos y corren por cuenta del cliente.</p>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
            ¿Puedo cambiar de plan o cancelar mi suscripción?
          </button>
        </h3>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <p>Sí, puedes cambiar de plan en cualquier momento. Si decides cambiar a un plan superior, el cambio será efectivo inmediatamente. Si cambias a un plan inferior, se aplicará al finalizar tu ciclo de facturación actual.</p>
            <p>Puedes cancelar tu suscripción con un preaviso de 30 días. No existen penalizaciones por cancelación, siempre que el vehículo sea devuelto en las condiciones acordadas.</p>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
            ¿Qué sucede si excedo el kilometraje incluido?
          </button>
        </h3>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <p>Cada plan incluye un número específico de kilómetros al mes:</p>
            <ul>
              <li>Plan Básico: 1.500 km/mes</li>
              <li>Plan Premium: 2.500 km/mes</li>
              <li>Plan Elite: Kilometraje ilimitado</li>
            </ul>
            <p>Si excedes el kilometraje incluido en tu plan, se aplicará un cargo adicional de 0,15€ por kilómetro extra. Los kilómetros no utilizados no se acumulan para el siguiente mes.</p>
          </div>
        </div>
      </div>
      <div class="accordion-item">
        <h3 class="accordion-header">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
            ¿Qué requisitos necesito para suscribirme?
          </button>
        </h3>
        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
          <div class="accordion-body">
            <p>Para suscribirte a DriveClub necesitas:</p>
            <ul>
              <li>Ser mayor de 25 años</li>
              <li>Carnet de conducir con al menos 2 años de antigüedad</li>
              <li>Tarjeta de crédito o débito para los pagos mensuales</li>
              <li>Completar nuestro formulario de registro con tus datos personales</li>
            </ul>
            <p>Dependiendo del tipo de vehículo, pueden aplicar requisitos adicionales, especialmente para vehículos de alta gama o deportivos.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8 mx-auto text-center">
        <h2 class="cta-title">¿Listo para unirte a DriveClub?</h2>
        <p class="cta-text">Comienza hoy mismo a disfrutar de la libertad de conducir sin compromisos a largo plazo.</p>
        <?php if (isLoggedIn()): ?>
          <a href="/public/vehiculos.php" class="btn btn-primary btn-lg">Explora nuestros vehículos</a>
        <?php else: ?>
          <a href="/public/login.php?registro=true" class="btn btn-primary btn-lg">Regístrate ahora</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
