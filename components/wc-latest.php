<div class="wc-section wide">

    <div class="wc-header">
        <h2 class="section-title">Nuevos Ingresos</h2>

        <div class="wc-arrows">
            <button class="wc-arrow prev">←</button>
            <button class="wc-arrow next">→</button>
        </div>
    </div>

    <div class="wc-carousel">
        <div class="wc-track">

            <?php
            $args = [
                'post_type'      => 'product',
                'posts_per_page' => 12, // más productos para deslizar
                'orderby'        => 'date',
                'order'          => 'DESC'
            ];

            $loop = new WP_Query($args);

            while ($loop->have_posts()) :
                $loop->the_post();
                wc_get_template_part('content', 'product');
            endwhile;

            wp_reset_postdata();
            ?>

        </div>
    </div>

</div>
