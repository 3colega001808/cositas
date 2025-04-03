<?php
/**
 * Footer template
 * 
 * This file contains the footer part of the website.
 */
?>
        <!-- Footer -->
        <footer class="footer mt-auto py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h2 class="logo-text mb-4">
                            <span class="drive">Drive</span><span class="club">Club</span>
                        </h2>
                        <p class="text-muted">Tu destino para alquiler de vehículos premium por suscripción. Conducción sin límites, sin compromisos a largo plazo.</p>
                        <div class="social-links mt-4">
                            <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                        <h5 class="footer-heading">Navegación</h5>
                        <ul class="footer-links">
                            <li><a href="/public/index.php">Inicio</a></li>
                            <li><a href="/public/vehiculos.php">Vehículos</a></li>
                            <li><a href="/public/planes.php">Planes</a></li>
                            <li><a href="/public/mi-cuenta.php">Mi Cuenta</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                        <h5 class="footer-heading">Planes</h5>
                        <ul class="footer-links">
                            <li><a href="/public/planes.php">Plan Básico</a></li>
                            <li><a href="/public/planes.php">Plan Premium</a></li>
                            <li><a href="/public/planes.php">Plan Elite</a></li>
                            <li><a href="/public/planes.php">Comparativa</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <h5 class="footer-heading">Contacto</h5>
                        <ul class="footer-contact">
                            <li><i class="bi bi-geo-alt"></i> Calle Principal 123, Madrid, España</li>
                            <li><i class="bi bi-envelope"></i> info@driveclub.com</li>
                            <li><i class="bi bi-telephone"></i> +34 91 123 45 67</li>
                        </ul>
                        <div class="newsletter mt-4">
                            <h6>Suscríbete a nuestra newsletter</h6>
                            <form class="newsletter-form mt-2">
                                <div class="input-group">
                                    <input type="email" class="form-control" placeholder="Tu email" aria-label="Tu email">
                                    <button class="btn btn-primary" type="submit">Enviar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="footer-bottom mt-5 pt-4 text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> DriveClub. Todos los derechos reservados.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($additionalScripts)): ?>
        <?php echo $additionalScripts; ?>
    <?php endif; ?>
</body>
</html>
