<?php
/**
 * Vehicles Page
 * 
 * This page displays the available vehicles for rental.
 */

// Set page title and description
$pageTitle = "Vehículos";
$pageDescription = "Explora nuestra flota de vehículos disponibles para alquilar";

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/vehicles.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/users.php';

// Track user session
trackUserSession();

// Get user ID if logged in
$userId = null;
$userSubscription = null;
if (isLoggedIn()) {
    $userId = getCurrentUserId();
    $userSubscription = getUserSubscription($userId);
}

// Process filters
$filters = [];

if (isset($_GET['marca']) && !empty($_GET['marca'])) {
    $filters['marca_id'] = (int)$_GET['marca'];
}

if (isset($_GET['tipo']) && !empty($_GET['tipo'])) {
    $filters['tipo_id'] = (int)$_GET['tipo'];
}

if (isset($_GET['plan']) && !empty($_GET['plan'])) {
    $filters['plan_minimo_id'] = (int)$_GET['plan'];
}

if (isset($_GET['disponible'])) {
    $filters['disponible'] = (int)$_GET['disponible'];
}

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $filters['search'] = trim($_GET['q']);
}

// Get vehicles based on filters
$vehicles = getAllVehicles($filters);

// Get all brands and types for filtering
$brands = getAllBrands();
$vehicleTypes = getAllVehicleTypes();
$subscriptionPlans = getAllSubscriptionPlans();

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Vehicles Banner -->
<section class="vehicles-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h1>Nuestros Vehículos</h1>
        <p class="lead">Explora nuestra flota de vehículos premium disponibles para alquiler.</p>
      </div>
    </div>
  </div>
</section>

<!-- Vehicles Content -->
<section class="vehicles-section py-5">
  <div class="container">
    <div class="row">
      <!-- Filters Sidebar -->
      <div class="col-lg-3 mb-4">
        <div class="filters-card">
          <h3 class="filters-title">Filtros</h3>
          <form id="vehicleFilterForm" method="get" action="/public/vehiculos.php">
            <!-- Search -->
            <div class="mb-3">
              <label for="searchTerm" class="form-label">Buscar</label>
              <div class="input-group">
                <input type="text" class="form-control" id="searchTerm" name="q" placeholder="Buscar vehículos..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
              </div>
            </div>
            
            <!-- Brands -->
            <div class="mb-3">
              <label for="filterMarca" class="form-label">Marca</label>
              <select class="form-select" id="filterMarca" name="marca">
                <option value="">Todas las marcas</option>
                <?php foreach ($brands as $brand): ?>
                  <option value="<?php echo $brand['id']; ?>" <?php echo (isset($_GET['marca']) && $_GET['marca'] == $brand['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($brand['nombre']); ?> (<?php echo $brand['vehicle_count']; ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <!-- Types -->
            <div class="mb-3">
              <label for="filterTipo" class="form-label">Tipo</label>
              <select class="form-select" id="filterTipo" name="tipo">
                <option value="">Todos los tipos</option>
                <?php foreach ($vehicleTypes as $type): ?>
                  <option value="<?php echo $type['id']; ?>" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == $type['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($type['nombre']); ?> (<?php echo $type['vehicle_count']; ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <!-- Subscription Plan -->
            <div class="mb-3">
              <label for="filterPlan" class="form-label">Plan mínimo</label>
              <select class="form-select" id="filterPlan" name="plan">
                <option value="">Todos los planes</option>
                <?php foreach ($subscriptionPlans as $plan): ?>
                  <option value="<?php echo $plan['id']; ?>" <?php echo (isset($_GET['plan']) && $_GET['plan'] == $plan['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($plan['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <!-- Availability -->
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="filterDisponible" name="disponible" value="1" <?php echo (isset($_GET['disponible']) && $_GET['disponible'] == 1) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="filterDisponible">Solo vehículos disponibles</label>
            </div>
            
            <!-- Filter Buttons -->
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
              <button type="button" id="resetFilters" class="btn btn-outline-secondary">Limpiar Filtros</button>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Vehicles Grid -->
      <div class="col-lg-9">
        <div class="vehicles-header mb-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h2 class="section-title mb-0">Vehículos Disponibles</h2>
            <div class="results-count">
              <?php echo count($vehicles); ?> vehículos encontrados
            </div>
          </div>
        </div>
        
        <div class="row g-4">
          <?php if (empty($vehicles)): ?>
            <div class="col-12">
              <div class="alert alert-info">
                No se encontraron vehículos con los criterios seleccionados. <a href="/public/vehiculos.php" class="alert-link">Ver todos los vehículos</a>
              </div>
            </div>
          <?php else: ?>
            <?php foreach ($vehicles as $vehicle): ?>
              <div class="col-md-6 col-xl-4">
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
                    <?php if ($vehicle['disponible'] == 0): ?>
                      <span class="availability-badge bg-danger">No disponible</span>
                    <?php endif; ?>
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
                      <?php if (!empty($vehicle['transmision'])): ?>
                        <span class="feature"><i class="bi bi-gear"></i> <?php echo htmlspecialchars($vehicle['transmision']); ?></span>
                      <?php endif; ?>
                    </div>
                    <div class="vehicle-footer">
                      <div class="price"><?php echo formatPrice($vehicle['precio_mensual']); ?>/mes</div>
                      <?php if ($vehicle['disponible'] == 1): ?>
                        <?php if (isLoggedIn()): ?>
                          <?php if ($userSubscription): ?>
                            <?php if ($userSubscription['plan_id'] >= $vehicle['plan_minimo_id']): ?>
                              <a href="/public/reserva.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-sm btn-primary">Reservar</a>
                            <?php else: ?>
                              <a href="/public/planes.php" class="btn btn-sm btn-outline-secondary">Mejorar Plan</a>
                            <?php endif; ?>
                          <?php else: ?>
                            <a href="/public/planes.php" class="btn btn-sm btn-outline-secondary">Suscribirse</a>
                          <?php endif; ?>
                        <?php else: ?>
                          <a href="/public/login.php?redirect=/public/vehiculos.php" class="btn btn-sm btn-outline-secondary">Iniciar Sesión</a>
                        <?php endif; ?>
                      <?php else: ?>
                        <span class="btn btn-sm btn-outline-danger disabled">No disponible</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<?php if (!isLoggedIn()): ?>
<section class="cta-section py-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8 mx-auto text-center">
        <h2 class="cta-title">¿Listo para empezar a conducir?</h2>
        <p class="cta-text">Regístrate ahora y elige el vehículo perfecto para ti.</p>
        <div class="cta-buttons">
          <a href="/public/login.php?registro=true" class="btn btn-primary">Crear Cuenta</a>
          <a href="/public/planes.php" class="btn btn-outline-primary">Ver Planes</a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
