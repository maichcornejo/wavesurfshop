<?php
/**
 * Template Name: Favoritos
 */

defined('ABSPATH') || exit;

get_header();

if ( ! is_user_logged_in() ) : ?>

  <div class="container favorites-page">
    <h1>Mis favoritos</h1>
    <p>Iniciá sesión para ver tus productos favoritos ❤️</p>
  </div>

<?php
get_footer();
return;
endif;

$user_id   = get_current_user_id();
$favorites = get_user_meta($user_id, 'wc_favorites', true);
$favorites = is_array($favorites) ? $favorites : [];

?>

<div class="container favorites-page">

  <h1>Mis favoritos</h1>

  <?php if ( empty($favorites) ) : ?>

    <div class="favorites-empty">
      Todavía no agregaste productos a favoritos.
    </div>

  <?php else : ?>

    <div class="favorites-grid">

      <?php
      $args = [
        'post_type' => 'product',
        'post__in'  => $favorites,
        'orderby'   => 'post__in',
      ];

      $loop = new WP_Query($args);

      while ( $loop->have_posts() ) :
        $loop->the_post();

        wc_get_template_part('content', 'product');

      endwhile;

      wp_reset_postdata();
      ?>

    </div>

  <?php endif; ?>

</div>

<?php get_footer(); ?>
