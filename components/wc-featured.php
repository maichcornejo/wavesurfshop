<div class="wc-section featured">
  <div class="container">
    <h2 class="section-title">Productos Destacados</h2>

    <?php
    $loop = new WP_Query([
      'post_type'      => 'product',
      'post_status'    => 'publish',
      'posts_per_page' => 20,
      'tax_query'      => [
        [
          'taxonomy' => 'product_visibility',
          'field'    => 'slug',          // <-- CLAVE
          'terms'    => ['featured'],     // <-- CLAVE
          'operator' => 'IN',
        ],
      ],
      'orderby' => 'date',
      'order'   => 'DESC',
    ]);

    // Debug rápido (si querés ver si trae algo)
    // echo '<!-- found: ' . esc_html($loop->found_posts) . ' -->';

    if ($loop->have_posts()) :
      echo '<ul class="products wc-grid">';
      while ($loop->have_posts()) : $loop->the_post();
        wc_get_template_part('content', 'product');
      endwhile;
      echo '</ul>';
    else :
      echo '<p>No hay productos destacados todavía.</p>';
    endif;

    wp_reset_postdata();
    ?>
  </div>
</div>
