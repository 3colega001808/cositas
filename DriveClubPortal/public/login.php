<?php
/**
 * Login Page
 * 
 * Handles user login and registration
 */

// Start session
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /public/mi-cuenta.php');
    exit;
}

// Include dependencies
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/google_auth.php';

// Set page variables
$pageTitle = "Iniciar Sesión";
$pageDescription = "Inicia sesión o regístrate en DriveClub";

// Process login form
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $loginError = "Por favor, introduce tu email y contraseña";
    } else {
        list($success, $message, $userId) = loginUser($email, $password);
        
        if ($success) {
            // Set session variables
            $_SESSION['user_id'] = $userId;
            
            // Set a cookie for "remember me" functionality if requested
            if (isset($_POST['remember_me']) && $_POST['remember_me'] === '1') {
                $token = generateToken();
                setSecureCookie('remember_token', $token, 30 * 24 * 60 * 60); // 30 days
                
                // Store token in database
                $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                updateData(
                    'usuarios',
                    ['remember_token' => $hashedToken],
                    'id = ?',
                    [$userId]
                );
            }
            
            // Redirect to account page
            header('Location: /public/mi-cuenta.php');
            exit;
        } else {
            $loginError = $message;
        }
    }
}

// Process registration form
$registerError = '';
$registerSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $userData = [
        'nombre' => trim($_POST['nombre']),
        'apellidos' => trim($_POST['apellidos']),
        'email' => trim($_POST['email']),
        'telefono' => trim($_POST['telefono']),
        'password' => $_POST['password']
    ];
    
    // Validate required fields
    foreach (['nombre', 'apellidos', 'email', 'password'] as $field) {
        if (empty($userData[$field])) {
            $registerError = "Todos los campos marcados con * son obligatorios";
            break;
        }
    }
    
    // Proceed if no error
    if (empty($registerError)) {
        list($success, $message, $userId) = registerUser($userData);
        
        if ($success) {
            $registerSuccess = $message;
            
            // Send welcome email
            require_once __DIR__ . '/../config/email.php';
            sendWelcomeEmail($userData['email'], $userData['nombre']);
            
            // Set session variables to log in the user automatically
            $_SESSION['user_id'] = $userId;
            
            // Redirect to account page
            header('Location: /public/mi-cuenta.php');
            exit;
        } else {
            $registerError = $message;
        }
    }
}

// Set additional CSS
$additionalCss = '
<style>
  /* Estilos adicionales específicos para la página de login */
  .login-container {
    min-height: 100vh;
    width: 100%;
    display: flex;
  }
  
  .login-form-container {
    padding: 3rem;
    width: 100%;
    max-width: 550px;
    margin: 0 auto;
    height: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  
  .login-image {
    width: 100%;
    height: 100%;
    min-height: 100vh;
    background-size: cover;
    background-position: center;
    position: relative;
  }
  
  .login-image::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    z-index: 1;
  }
  
  .login-image .overlay-content {
    position: absolute;
    bottom: 100px;
    left: 50px;
    right: 50px;
    color: white;
    z-index: 2;
  }
  
  .login-image .overlay-content h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }
  
  .login-header {
    margin-bottom: 2.5rem;
  }
  
  .login-logo {
    height: 50px;
    max-width: 200px;
  }
  
  .login-tabs {
    display: flex;
    border-bottom: 1px solid var(--gray-300);
    margin-bottom: 1.5rem;
  }
  
  .tab-btn {
    flex: 1;
    background: none;
    border: none;
    padding: 15px;
    font-weight: 600;
    color: var(--gray-600);
    cursor: pointer;
    position: relative;
  }
  
  .tab-btn.active {
    color: var(--primary-color);
  }
  
  .tab-btn.active::after {
    content: "";
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: var(--primary-color);
  }
  
  .divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 20px 0;
  }
  
  .divider::before,
  .divider::after {
    content: "";
    flex: 1;
    border-bottom: 1px solid var(--gray-300);
  }
  
  .divider span {
    padding: 0 10px;
    color: var(--gray-600);
    font-size: 14px;
  }
  
  .password-input {
    position: relative;
  }
  
  .password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--gray-600);
    cursor: pointer;
    padding: 0;
  }
  
  .password-strength {
    width: 100%;
  }
  
  .strength-bar {
    height: 5px;
    background-color: var(--gray-200);
    border-radius: 3px;
    margin-bottom: 5px;
  }
  
  .strength-indicator {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s, background-color 0.3s;
  }
  
  @media (max-width: 991.98px) {
    .login-form-container {
      padding: 2rem;
    }
    
    .login-image {
      display: none;
    }
  }
</style>
';

// Set additional scripts
$additionalScripts = '
<script src="/assets/js/login.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
';

// Get Google auth URL
$googleAuthUrl = getGoogleAuthUrl();

// Include custom header (without the standard navbar)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $pageTitle; ?> | DriveClub - Alquiler de Coches por Suscripción</title>
    <meta name="description" content="<?php echo $pageDescription; ?>" />
    <meta name="author" content="DriveClub" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/src/index.css">
    <?php echo $additionalCss; ?>
</head>
<body>
    <div id="root">
      <!-- Login Content -->
      <div class="login-container">
        <div class="row g-0 h-100 w-100">
          <!-- Left Panel - Form -->
          <div class="col-lg-5">
            <div class="login-form-container">
              <div class="login-header mb-5">
                <a class="navbar-brand" href="/public/index.php">
                  <h2 class="logo-text">
                    <span class="drive">Drive</span><span class="club">Club</span>
                  </h2>
                </a>
              </div>
              
              <div class="login-tabs mb-4">
                <button class="tab-btn active" id="loginTabBtn">Iniciar Sesión</button>
                <button class="tab-btn" id="registerTabBtn">Registrarse</button>
              </div>
              
              <!-- Login Form -->
              <div class="form-container" id="loginForm">
                <h1 class="login-title mb-4">Bienvenido de nuevo</h1>
                
                <?php if (!empty($loginError)): ?>
                  <div class="alert alert-danger"><?php echo $loginError; ?></div>
                <?php endif; ?>
                
                <div class="social-login mb-4">
                  <a href="<?php echo $googleAuthUrl; ?>" class="btn btn-outline-secondary w-100 mb-3">
                    <i class="bi bi-google me-2"></i>Continuar con Google
                  </a>
                </div>
                
                <div class="divider">
                  <span>O</span>
                </div>
                
                <form method="post" action="/public/login.php">
                  <div class="mb-3">
                    <label for="loginEmail" class="form-label">Email</label>
                    <input type="email" class="form-control" id="loginEmail" name="email" placeholder="tu@email.com" required>
                  </div>
                  <div class="mb-4">
                    <label for="loginPassword" class="form-label">Contraseña</label>
                    <div class="password-input">
                      <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Tu contraseña" required>
                      <button type="button" class="password-toggle" onclick="togglePasswordVisibility('loginPassword', this)">
                        <i class="bi bi-eye"></i>
                      </button>
                    </div>
                  </div>
                  <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="rememberMe" name="remember_me" value="1">
                      <label class="form-check-label" for="rememberMe">Recordarme</label>
                    </div>
                    <a href="/public/reset_password.php" class="forgot-password mt-2 mt-sm-0">¿Olvidaste tu contraseña?</a>
                  </div>
                  <input type="hidden" name="login" value="1">
                  <button type="submit" class="btn btn-primary w-100 mb-4">Iniciar Sesión</button>
                  <p class="text-center mb-0">¿No tienes cuenta? <a href="#" id="switchToRegister">Regístrate aquí</a></p>
                </form>
              </div>
              
              <!-- Register Form -->
              <div class="form-container" id="registerForm" style="display: none;">
                <h1 class="login-title mb-4">Crear una cuenta</h1>
                
                <?php if (!empty($registerError)): ?>
                  <div class="alert alert-danger"><?php echo $registerError; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($registerSuccess)): ?>
                  <div class="alert alert-success"><?php echo $registerSuccess; ?></div>
                <?php endif; ?>
                
                <div class="social-login mb-4">
                  <a href="<?php echo $googleAuthUrl; ?>" class="btn btn-outline-secondary w-100 mb-3">
                    <i class="bi bi-google me-2"></i>Continuar con Google
                  </a>
                </div>
                
                <div class="divider">
                  <span>O</span>
                </div>
                
                <form method="post" action="/public/login.php">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="registerFirstName" class="form-label">Nombre *</label>
                      <input type="text" class="form-control" id="registerFirstName" name="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="registerLastName" class="form-label">Apellidos *</label>
                      <input type="text" class="form-control" id="registerLastName" name="apellidos" placeholder="Tus apellidos" required>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="registerEmail" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="registerEmail" name="email" placeholder="tu@email.com" required>
                  </div>
                  <div class="mb-3">
                    <label for="registerPhone" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="registerPhone" name="telefono" placeholder="+34 612 345 678">
                  </div>
                  <div class="mb-4">
                    <label for="registerPassword" class="form-label">Contraseña *</label>
                    <div class="password-input">
                      <input type="password" class="form-control" id="registerPassword" name="password" placeholder="Crea una contraseña" oninput="checkPasswordStrength(this.value)" required>
                      <button type="button" class="password-toggle" onclick="togglePasswordVisibility('registerPassword', this)">
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
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="termsAgree" name="terms_agree" required>
                      <label class="form-check-label" for="termsAgree">
                        Acepto los <a href="#">Términos y Condiciones</a> y la <a href="#">Política de Privacidad</a>
                      </label>
                    </div>
                  </div>
                  <input type="hidden" name="register" value="1">
                  <button type="submit" class="btn btn-primary w-100 mb-4">Crear Cuenta</button>
                  <p class="text-center mb-0">¿Ya tienes cuenta? <a href="#" id="switchToLogin">Inicia sesión aquí</a></p>
                </form>
              </div>
              
              <div class="login-footer mt-5">
                <p class="text-center mb-0">© <?php echo date('Y'); ?> DriveClub. Todos los derechos reservados.</p>
              </div>
            </div>
          </div>
          
          <!-- Right Panel - Image -->
          <div class="col-lg-7 d-none d-lg-block">
            <div class="login-image" style="background-image: url('https://images.unsplash.com/photo-1511919884226-fd3cad34687c?q=80&w=1770&auto=format&fit=crop');">
              <div class="overlay-content">
                <h2>Disfruta de la libertad al volante</h2>
                <p class="lead">Únete a DriveClub y experimenta la conducción sin compromisos.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $additionalScripts; ?>
</body>
</html>
