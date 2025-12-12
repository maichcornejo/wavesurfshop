<div class="wc-section container">
    <h2 class="section-title">Nuevos Ingresos</h2>

    <?php
    $args = [
        'post_type' => 'product',
        'posts_per_page' => 4,
        'orderby' => 'date',
        'order' => 'DESC'
    ];

    $loop = new WP_Query($args);

    if ($loop->have_posts()) :
        echo '<div class="wc-grid">';
        while ($loop->have_posts()) : $loop->the_post();
            wc_get_template_part('content', 'product');
        endwhile;
        echo '</div>';
    endif;

    wp_reset_postdata();
    ?>
</div>
