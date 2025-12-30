<?php
/**
 * Template Name: Login Waves
 */

defined('ABSPATH') || exit;

get_header();
?>

<main class="waves-auth-page">

  <div class="waves-auth-card">

    <div class="waves-auth-image"></div>

    <div class="waves-auth-form">
      <h2>Ingresar</h2>

      <?php echo do_shortcode('[user_registration_login]'); ?>
      <!-- DIVISOR -->
      <div class="waves-divider">
        <span>o</span>
      </div>
      
      <!-- GOOGLE REGISTER -->
      <div class="waves-social-login">
        <?php echo do_shortcode('[miniorange_social_login]'); ?>
      </div>
    </div>

  </div>

</main>

<?php get_footer(); ?>
