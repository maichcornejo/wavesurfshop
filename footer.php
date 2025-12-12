<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<footer class="main-footer-cc">

    <!-- TOP SECTION -->
    <div class="footer-top-cc">
        <div class="container-cc">
            <div class="row-cc">

                <!-- LOGO + QR -->
                <div class="col-cc-3 logo-qr-section">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/footer/logo.png"
                         alt="Waves Surf Shop Logo"
                         class="footer-logo">

                    <div class="qr-code">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/footer/qr-data-fiscal.png"
                             alt="Data Fiscal QR"
                             class="data-fiscal-img">
                    </div>
                </div>

                <!-- EMPRESA -->
                <div class="col-cc-3">
                    <h3 class="footer-heading-cc">EMPRESA</h3>

                    <p class="empresa-text">
                        Waves Surf Shop es una cadena de locales multimarca que inició sus actividades en ...
                        <a href="#" class="read-more">[más]</a>
                    </p>

                    <h3 class="footer-heading-cc">SEGUINOS</h3>
                    <ul class="social-links-cc">
                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fab fa-youtube"></i></a></li>
                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>

                <!-- CONTENIDOS -->
                <div class="col-cc-3">
                    <h3 class="footer-heading-cc">CONTENIDOS</h3>

                    <ul class="footer-links-cc">
                        <li><a href="#">E-shop</a></li>
                        <li><a href="#">Locales</a></li>
                        <li><a href="#">Contacto</a></li>
                    </ul>

                    <div class="arrepentimiento-btn-container">
                        <button class="btn-arrepentimiento">BOTÓN DE ARREPENTIMIENTO</button>
                    </div>

                    <div class="security-logos">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/footer/logo-cace.png" alt="Logo CACE">
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/footer/logo-ssl.png" alt="Logo SSL">
                    </div>
                </div>

                <!-- AYUDA / STAFF -->
                <div class="col-cc-3 help-staff-section">
                    <h3 class="footer-heading-cc">LEGALES</h3>

                    <ul class="footer-links-cc">
                        <li><a href="#">Cómo realizar tu pedido</a></li>
                        <li><a href="#">Términos y condiciones</a></li>
                        <li><a href="#">Políticas de devolución</a></li>
                        <li><a href="#">Preguntas frecuentes</a></li>
                    </ul>

                </div>

            </div>
        </div>
    </div>

    <!-- BOTTOM SECTION -->
    <div class="footer-bottom-cc">
        <div class="container-cc d-flex justify-content-between">
            <p class="copyright-cc">
                © <?php echo date('Y'); ?> Waves Surf Shop. Todos los derechos reservados. Web creada y administrada por nosotros :p
            </p>
            <p class="design-by">Design by: ControlZ studios</p>
        </div>
    </div>

</footer>


</body>
</html>
