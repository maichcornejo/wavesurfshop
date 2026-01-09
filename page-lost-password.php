<?php
/**
 * Template Name: Recuperar contraseña Waves
 */

defined('ABSPATH') || exit;

get_header();
?>

<main class="waves-auth-page">

  <div class="waves-auth-card">

    <div class="waves-auth-image">

    </div>

    <div class="waves-auth-form">
      <h2>Recuperar contraseña</h2>

      <?php echo do_shortcode('[user_registration_lost_password]'); ?>
      <p class="waves-auth-note">
        🔒 Nunca compartimos tu correo electrónico.
      </p>

      <div class="waves-auth-link">
        <a href="<?php echo esc_url( site_url('/login') ); ?>">← Volver al login</a>
      </div>
    </div>

  </div>

</main>

<?php get_footer(); ?>
