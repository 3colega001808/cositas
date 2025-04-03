<?php
/**
 * Password Reset Page
 * 
 * This page handles password reset requests and password updates.
 */

// Include necessary files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/session.php';

// Set page variables
$pageTitle = "Restablecer Contraseña";
$pageDescription = "Restablece tu contraseña para acceder a tu cuenta en DriveClub";

// Initialize variables
$step = 'request'; // Default to request step
$message = '';
$token = '';

// Check if token is present in URL
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    $user = validateResetToken($token);
    
    if ($user) {
        $step = 'reset';
    } else {
        $message = displayError('El enlace de restablecimiento no es válido o ha expirado');
        $step = 'request';
    }
}

// Process password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_reset'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $message = displayError('Por favor, introduce tu dirección de email');
    } else {
        list($success, $msg) = requestPasswordReset($email);
        
        if ($success) {
            $message = displaySuccess($msg);
        } else {
            $message = displayError($msg);
        }
    }
}

// Process password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (empty($password) || empty($confirmPassword)) {
        $message = displayError('Por favor, completa todos los campos');
        $step = 'reset';
    } elseif ($password !== $confirmPassword) {
        $message = displayError('Las contraseñas no coinciden');
        $step = 'reset';
    } else {
        list($success, $msg) = resetPassword($token, $password);
        
        if ($success) {
            $message = displaySuccess($msg . ' <a href="/public/login.php">Iniciar sesión</a>');
            $step = 'complete';
        } else {
            $message = displayError($msg);
            $step = 'reset';
        }
    }
}

// Additional CSS
$additionalCss = '
<style>
.reset-container {
  max-width: 500px;
  margin: 0 auto;
  padding: 2rem;
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
</style>
';

// Additional scripts
$additionalScripts = '
<script src="/assets/js/login.js"></script>
';

// Include header
include_once __DIR__ . '/../includes/header.php';
?>

<!-- Reset Password Page -->
<section class="page-banner">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <h1>Restablecer Contraseña</h1>
        <p class="lead">Recupera el acceso a tu cuenta DriveClub</p>
      </div>
    </div>
  </div>
</section>

<!-- Reset Password Content -->
<section class="reset-password-section py-5">
  <div class="container">
    <div class="content-card reset-container">
      <?php echo $message; ?>
      
      <?php if ($step === 'request'): ?>
        <div class="reset-request-form">
          <h2 class="mb-4">Solicitar restablecimiento de contraseña</h2>
          <p class="mb-4">Introduce tu dirección de email y te enviaremos un enlace para restablecer tu contraseña.</p>
          
          <form method="post" action="/public/reset_password.php">
            <div class="mb-4">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="tu@email.com" required>
            </div>
            <div class="d-grid">
              <button type="submit" name="request_reset" class="btn btn-primary">Enviar Enlace de Restablecimiento</button>
            </div>
          </form>
          
          <div class="mt-4 text-center">
            <p>¿Ya tienes una cuenta? <a href="/public/login.php">Iniciar sesión</a></p>
          </div>
        </div>
      
      <?php elseif ($step === 'reset'): ?>
        <div class="reset-form">
          <h2 class="mb-4">Crear nueva contraseña</h2>
          <p class="mb-4">Introduce tu nueva contraseña para tu cuenta.</p>
          
          <form method="post" action="/public/reset_password.php">
            <div class="mb-3">
              <label for="password" class="form-label">Nueva Contraseña</label>
              <div class="password-input">
                <input type="password" class="form-control" id="password" name="password" oninput="checkPasswordStrength(this.value)" required>
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password', this)">
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
              <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
              <div class="password-input">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="d-grid">
              <button type="submit" name="reset_password" class="btn btn-primary">Restablecer Contraseña</button>
            </div>
          </form>
        </div>
      
      <?php elseif ($step === 'complete'): ?>
        <div class="reset-complete text-center">
          <div class="mb-4">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
          </div>
          <h2 class="mb-3">¡Contraseña restablecida correctamente!</h2>
          <p class="mb-4">Tu contraseña ha sido actualizada. Ahora puedes iniciar sesión con tu nueva contraseña.</p>
          <a href="/public/login.php" class="btn btn-primary">Iniciar Sesión</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
// Include footer
include_once __DIR__ . '/../includes/footer.php';
?>
