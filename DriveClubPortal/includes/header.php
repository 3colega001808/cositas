<?php
/**
 * Header template
 * 
 * This file contains the header part of the website.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userData = null;

// Get user data if logged in
if ($isLoggedIn) {
    require_once __DIR__ . '/../includes/users.php';
    $userData = getUserById($_SESSION['user_id']);
}

// Helper function to check current page
function isCurrentPage($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    return $currentPage === $pageName ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>DriveClub - Alquiler de Coches por Suscripción</title>
    <meta name="description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : 'DriveClub: Tu plataforma de alquiler de coches por suscripción'; ?>" />
    <meta name="author" content="DriveClub" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/src/index.css">
    <?php if (isset($additionalCss)): ?>
        <?php echo $additionalCss; ?>
    <?php endif; ?>
</head>
<body>
    <div id="root">
        <!-- Header -->
        <header class="header">
            <div class="container">
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand" href="/public/index.php">
                        <h2 class="logo-text">
                            <span class="drive">Drive</span><span class="club">Club</span>
                        </h2>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="bi bi-list fs-1"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('index.php'); ?>" href="/public/index.php">Inicio</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('vehiculos.php'); ?>" href="/public/vehiculos.php">Vehículos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('planes.php'); ?>" href="/public/planes.php">Planes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo isCurrentPage('mi-cuenta.php'); ?>" href="/public/mi-cuenta.php">Mi Cuenta</a>
                            </li>
                        </ul>
                        <div class="header-actions">
                            <?php if ($isLoggedIn): ?>
                                <div class="dropdown">
                                    <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo htmlspecialchars($userData['nombre']); ?>
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                        <li><a class="dropdown-item" href="/public/mi-cuenta.php">Mi Cuenta</a></li>
                                        <li><a class="dropdown-item" href="/public/mi-cuenta.php?tab=subscription">Mi Suscripción</a></li>
                                        <li><a class="dropdown-item" href="/public/mi-cuenta.php?tab=reservations">Mis Reservas</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="/public/logout.php">Cerrar Sesión</a></li>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <a href="/public/login.php" class="btn btn-outline-secondary">Iniciar Sesión</a>
                                <a href="/public/login.php?registro=true" class="btn btn-primary">Registrarse</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
