<?php
/**
 * Email Configuration
 * 
 * This file contains the email configuration for the DriveClub application.
 * It includes settings for PHPMailer and templates for various email types.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Email configuration
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'driveclub@example.com');
define('MAIL_PASSWORD', ''); // In production, use environment variables
define('MAIL_FROM_ADDRESS', 'driveclub@example.com');
define('MAIL_FROM_NAME', 'DriveClub');
define('MAIL_ENCRYPTION', 'tls');

/**
 * Initialize PHPMailer
 * 
 * @return PHPMailer Returns an initialized PHPMailer instance
 */
function initMailer() {
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = MAIL_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = MAIL_USERNAME;
    $mail->Password = MAIL_PASSWORD;
    $mail->SMTPSecure = MAIL_ENCRYPTION;
    $mail->Port = MAIL_PORT;
    $mail->CharSet = 'UTF-8';
    
    // Default sender
    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    
    return $mail;
}

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string $altBody Plain text alternative (optional)
 * @param array $attachments Array of attachments (optional)
 * @return bool True on success, false on failure
 */
function sendEmail($to, $subject, $body, $altBody = '', $attachments = []) {
    try {
        $mail = initMailer();
        
        // Recipients
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        if (!empty($altBody)) {
            $mail->AltBody = $altBody;
        } else {
            $mail->AltBody = strip_tags($body);
        }
        
        // Attachments
        foreach ($attachments as $attachment) {
            if (isset($attachment['path']) && isset($attachment['name'])) {
                $mail->addAttachment($attachment['path'], $attachment['name']);
            } elseif (isset($attachment['path'])) {
                $mail->addAttachment($attachment['path']);
            }
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send welcome email to a new user
 * 
 * @param string $to Recipient email
 * @param string $name User's name
 * @return bool True on success, false on failure
 */
function sendWelcomeEmail($to, $name) {
    $subject = "¡Bienvenido a DriveClub!";
    
    $body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background-color: #0A3D62; padding: 20px; text-align: center; color: white;">
            <h1>¡Bienvenido a DriveClub!</h1>
        </div>
        <div style="padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <p>Hola <strong>' . htmlspecialchars($name) . '</strong>,</p>
            <p>¡Gracias por unirte a DriveClub! Estamos muy contentos de tenerte como miembro de nuestra comunidad.</p>
            <p>Con tu cuenta, ahora puedes:</p>
            <ul>
                <li>Explorar nuestra flota de vehículos premium</li>
                <li>Realizar reservas en segundos</li>
                <li>Gestionar tus suscripciones</li>
                <li>Acceder a ofertas exclusivas para miembros</li>
            </ul>
            <p>Si tienes alguna pregunta, no dudes en contactarnos respondiendo a este correo o llamando a nuestro servicio de atención al cliente.</p>
            <div style="text-align: center; margin-top: 30px;">
                <a href="http://localhost/public/index.php" style="background-color: #0A3D62; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Visitar mi cuenta</a>
            </div>
        </div>
        <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
            <p>© 2023 DriveClub. Todos los derechos reservados.</p>
        </div>
    </div>
    ';
    
    return sendEmail($to, $subject, $body);
}

/**
 * Send password reset email
 * 
 * @param string $to Recipient email
 * @param string $name User's name
 * @param string $resetToken Reset token
 * @return bool True on success, false on failure
 */
function sendPasswordResetEmail($to, $name, $resetToken) {
    $subject = "Restablecer tu contraseña de DriveClub";
    
    $resetLink = 'http://localhost/public/reset_password.php?token=' . urlencode($resetToken);
    
    $body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background-color: #0A3D62; padding: 20px; text-align: center; color: white;">
            <h1>Restablecer Contraseña</h1>
        </div>
        <div style="padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <p>Hola <strong>' . htmlspecialchars($name) . '</strong>,</p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta DriveClub. Haz clic en el botón a continuación para crear una nueva contraseña:</p>
            <div style="text-align: center; margin: 30px 0;">
                <a href="' . $resetLink . '" style="background-color: #0A3D62; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Restablecer Contraseña</a>
            </div>
            <p>Si no solicitaste este cambio, puedes ignorar este correo y tu contraseña permanecerá sin cambios.</p>
            <p>Este enlace caducará en 24 horas por motivos de seguridad.</p>
        </div>
        <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
            <p>© 2023 DriveClub. Todos los derechos reservados.</p>
        </div>
    </div>
    ';
    
    return sendEmail($to, $subject, $body);
}

/**
 * Send reservation confirmation email with QR code
 * 
 * @param string $to Recipient email
 * @param string $name User's name
 * @param array $reservationData Reservation details
 * @param string $qrCodePath Path to the QR code image
 * @return bool True on success, false on failure
 */
function sendReservationConfirmationEmail($to, $name, $reservationData, $qrCodePath) {
    $subject = "Confirmación de tu reserva en DriveClub - #" . $reservationData['id'];
    
    $vehicleName = htmlspecialchars($reservationData['vehicle_name']);
    $startDate = htmlspecialchars($reservationData['fecha_inicio']);
    $endDate = htmlspecialchars($reservationData['fecha_fin']);
    
    $body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background-color: #0A3D62; padding: 20px; text-align: center; color: white;">
            <h1>Reserva Confirmada</h1>
        </div>
        <div style="padding: 20px; border: 1px solid #ddd; background-color: #f9f9f9;">
            <p>Hola <strong>' . htmlspecialchars($name) . '</strong>,</p>
            <p>¡Tu reserva ha sido confirmada! Aquí están los detalles:</p>
            
            <div style="background-color: #fff; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="margin-top: 0; color: #0A3D62;">Detalles de la Reserva</h3>
                <p><strong>Vehículo:</strong> ' . $vehicleName . '</p>
                <p><strong>Fechas:</strong> ' . $startDate . ' al ' . $endDate . '</p>
                <p><strong>Número de Reserva:</strong> #' . $reservationData['id'] . '</p>
                <p>Utiliza el código QR adjunto para recoger tu vehículo.</p>
            </div>
            
            <p>Si necesitas realizar algún cambio o tienes alguna pregunta, contacta con nosotros lo antes posible.</p>
            <p>¡Disfruta de tu experiencia DriveClub!</p>
        </div>
        <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
            <p>© 2023 DriveClub. Todos los derechos reservados.</p>
        </div>
    </div>
    ';
    
    $attachments = [
        [
            'path' => $qrCodePath,
            'name' => 'reserva_qr_' . $reservationData['id'] . '.png'
        ]
    ];
    
    return sendEmail($to, $subject, $body, '', $attachments);
}
