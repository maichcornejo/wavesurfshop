<div class="wc-section wide" data-wc-carousel>
  <div class="container">

    <div class="wc-header">
        <div class="wc-title-wrap">
            <h2 class="section-title">Nuevos Ingresos</h2>
        </div>

        <div class="wc-arrows">
            <button type="button" class="wc-arrow prev" aria-label="Anterior">←</button>
            <button type="button" class="wc-arrow next" aria-label="Siguiente">→</button>
        </div>
    </div>


    <div class="wc-carousel">
      <ul class="products wc-track">
        <?php
          $loop = new WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 12,
            'orderby'        => 'date',
            'order'          => 'DESC',
          ]);

          while ($loop->have_posts()) :
            $loop->the_post();
            wc_get_template_part('content', 'product');
          endwhile;

          wp_reset_postdata();
        ?>
      </ul>
    </div>

  </div>
</div>
