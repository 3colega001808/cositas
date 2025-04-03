<?php
/**
 * Homepage
 * 
 * Main landing page for the DriveClub website.
 */

// Set page title and description
$pageTitle = "Inicio";
$pageDescription = "DriveClub: Tu plataforma de alquiler de coches por suscripción. Conduce sin límites, sin compromisos a largo plazo.";

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/vehicles.php';
require_once __DIR__ . '/../includes/session.php';

// Track user session
trackUserSession();

// Get featured vehicles
$featuredVehicles = getFeaturedVehicles(4);

// Get subscription plans
require_once __DIR__ . '/../includes/users.php';
$subscriptionPlans = getAllSubscriptionPlans();

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="hero-content">
          <h1 class="hero-title">Conduce sin límites</h1>
          <p class="hero-subtitle">Tu suscripción a la libertad sobre ruedas</p>
          <p class="hero-text">Elige entre nuestra exclusiva colección de vehículos y cámbialos según tus necesidades. Sin entrada inicial, sin permanencia, todo incluido.</p>
          <div class="hero-buttons">
            <a href="/public/vehiculos.php" class="btn btn-primary btn-lg">Ver vehículos</a>
            <a href="/public/planes.php" class="btn btn-outline-primary btn-lg">Nuestros planes</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works py-5">
  <div class="container">
    <h2 class="section-title text-center mb-5">¿Cómo funciona?</h2>
    
    <div class="row g-4">
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-icon">
            <i class="bi bi-1-circle"></i>
          </div>
          <h3>Elige tu plan</h3>
          <p>Selecciona entre nuestros planes Básico, Premium o Elite según tus necesidades y presupuesto.</p>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-icon">
            <i class="bi bi-2-circle"></i>
          </div>
          <h3>Reserva tu vehículo</h3>
          <p>Navega por nuestra flota y elige el vehículo que mejor se adapte a tus necesidades del momento.</p>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="step-card">
          <div class="step-icon">
            <i class="bi bi-3-circle"></i>
          </div>
          <h3>Disfruta de la experiencia</h3>
          <p>Recoge tu vehículo y disfruta de la libertad. Cámbialo cuando quieras según tu plan.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured Vehicles Section -->
<section class="featured-vehicles py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center mb-5">Vehículos destacados</h2>
    
    <div class="row g-4">
      <?php if (empty($featuredVehicles)): ?>
        <div class="col-12 text-center">
          <p>No hay vehículos destacados disponibles en este momento.</p>
        </div>
      <?php else: ?>
        <?php foreach ($featuredVehicles as $vehicle): ?>
          <div class="col-md-6 col-lg-3">
            <div class="vehicle-card">
              <div class="vehicle-image">
                <?php if (!empty($vehicle['imagen'])): ?>
                  <img src="<?php echo htmlspecialchars($vehicle['imagen']); ?>" alt="<?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?>" class="img-fluid">
                <?php else: ?>
                  <div class="no-image">
                    <i class="bi bi-car-front"></i>
                  </div>
                <?php endif; ?>
                <span class="plan-badge"><?php echo htmlspecialchars($vehicle['plan_nombre']); ?></span>
              </div>
              <div class="vehicle-details">
                <h3 class="vehicle-title"><?php echo htmlspecialchars($vehicle['marca_nombre'] . ' ' . $vehicle['nombre']); ?></h3>
                <p class="vehicle-type"><?php echo htmlspecialchars($vehicle['tipo_nombre']); ?></p>
                <div class="vehicle-features">
                  <?php if (!empty($vehicle['potencia'])): ?>
                    <span class="feature"><i class="bi bi-lightning"></i> <?php echo htmlspecialchars($vehicle['potencia']); ?></span>
                  <?php endif; ?>
                  <?php if (!empty($vehicle['aceleracion'])): ?>
                    <span class="feature"><i class="bi bi-speedometer"></i> <?php echo htmlspecialchars($vehicle['aceleracion']); ?></span>
                  <?php endif; ?>
                </div>
                <div class="vehicle-footer">
                  <div class="price"><?php echo formatPrice($vehicle['precio_mensual']); ?>/mes</div>
                  <a href="/public/reserva.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-primary">Reservar</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    
    <div class="text-center mt-5">
      <a href="/public/vehiculos.php" class="btn btn-outline-primary">Ver todos los vehículos</a>
    </div>
  </div>
</section>

<!-- Subscription Plans Section -->
<section class="subscription-plans py-5">
  <div class="container">
    <h2 class="section-title text-center mb-5">Nuestros planes de suscripción</h2>
    
    <div class="row g-4 justify-content-center">
      <?php foreach ($subscriptionPlans as $index => $plan): ?>
        <div class="col-md-6 col-lg-4">
          <div class="plan-card <?php echo ($index === 1) ? 'featured' : ''; ?>">
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
                    <li><i class="bi bi-check-circle"></i> Cambio de vehículo mensual</li>
                    <li><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($plan['kilometraje_mensual'] ?? '1500'); ?> km mensuales</li>
                    <li><i class="bi bi-check-circle"></i> Mantenimiento básico incluido</li>
                    <li><i class="bi bi-check-circle"></i> Seguro a terceros</li>
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
                  <li><i class="bi bi-check-circle"></i> Cambio de vehículo mensual</li>
                  <li><i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($plan['kilometraje_mensual'] ?? '1500'); ?> km mensuales</li>
                  <li><i class="bi bi-check-circle"></i> Mantenimiento básico incluido</li>
                  <li><i class="bi bi-check-circle"></i> Seguro a terceros</li>
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
              <a href="/public/planes.php#plan-<?php echo $plan['id']; ?>" class="btn <?php echo ($index === 1) ? 'btn-primary' : 'btn-outline-primary'; ?> w-100">Elegir Plan</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    
    <div class="text-center mt-5">
      <a href="/public/planes.php" class="btn btn-outline-primary">Ver detalles de los planes</a>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials py-5 bg-light">
  <div class="container">
    <h2 class="section-title text-center mb-5">Lo que dicen nuestros clientes</h2>
    
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            <?php
              // Get random testimonials
              $testimonials = [
                [
                  'name' => 'Laura Martínez',
                  'position' => 'Directora de Marketing',
                  'image' => 'https://i.pravatar.cc/150?img=5',
                  'text' => 'DriveClub ha cambiado mi forma de moverme por la ciudad. La flexibilidad de poder cambiar de vehículo según mis necesidades es invaluable. Un mes puedo usar un compacto para el día a día y al siguiente un SUV para escapadas de fin de semana.'
                ],
                [
                  'name' => 'Javier García',
                  'position' => 'Ingeniero de Software',
                  'image' => 'https://i.pravatar.cc/150?img=8',
                  'text' => 'La relación calidad-precio es inmejorable. He hecho cálculos y estoy ahorrando más de 3.000€ al año comparado con tener mi propio coche, sin contar el estrés que me ahorro al no preocuparme por el mantenimiento, seguro o depreciación.'
                ],
                [
                  'name' => 'Elena Rodríguez',
                  'position' => 'Consultora',
                  'image' => 'https://i.pravatar.cc/150?img=9',
                  'text' => 'El servicio al cliente es excepcional. Tuve un problema con una reserva y lo solucionaron en minutos. Además, la aplicación es muy intuitiva y hace que todo el proceso sea extremadamente sencillo. Totalmente recomendable.'
                ]
              ];
              
              foreach ($testimonials as $index => $testimonial):
            ?>
              <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                <div class="testimonial-card text-center">
                  <div class="testimonial-image">
                    <img src="<?php echo $testimonial['image']; ?>" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" class="rounded-circle">
                  </div>
                  <div class="testimonial-content">
                    <p class="testimonial-text">"<?php echo htmlspecialchars($testimonial['text']); ?>"</p>
                    <h4 class="testimonial-name"><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                    <p class="testimonial-position"><?php echo htmlspecialchars($testimonial['position']); ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
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
        <h2 class="cta-title">¿Listo para empezar a disfrutar?</h2>
        <p class="cta-text">Únete a DriveClub hoy mismo y experimenta una nueva forma de conducir.</p>
        <div class="cta-buttons">
          <a href="/public/login.php?registro=true" class="btn btn-primary btn-lg">Regístrate ahora</a>
          <a href="/public/planes.php" class="btn btn-outline-primary btn-lg">Ver planes</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
