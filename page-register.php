<?php
/**
 * Template Name: Registro Waves
 */

defined('ABSPATH') || exit;

get_header();
?>

<main class="waves-auth-page">

  <div class="waves-auth-card">

    <div class="waves-auth-image"></div>
    
    <div class="waves-auth-form">
      
      <h2>Crear cuenta</h2>
      <p>Registrate en segundos para empezar a comprar.</p>
      
      
      <!-- FORMULARIO USER REGISTRATION -->
      <?php echo do_shortcode('[user_registration_form id="133"]'); ?>

      <!-- DIVISOR -->
      <div class="waves-divider">
        <span>o</span>
      </div>
      
      <!-- GOOGLE REGISTER -->
      <div class="waves-social-login">
        <?php echo do_shortcode('[miniorange_social_login]'); ?>
      </div>
      
      <div class="waves-auth-link">
        ¿Ya tenés cuenta?
        <a href="<?php echo esc_url( site_url('/login') ); ?>">Ingresar</a>
      </div>

    </div>

  </div>

</main>

<?php get_footer(); ?>
