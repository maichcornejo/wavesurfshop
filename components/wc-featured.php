<div class="wc-section container">
    <h2 class="section-title">Productos Destacados</h2>

    <?php
    $args = [
        'post_type' => 'product',
        'posts_per_page' => 4,
        'tax_query' => [
            [
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
            ],
        ],
    ];

    $loop = new WP_Query($args);

    if ($loop->have_posts()) :
        echo '<div class="wc-grid">';
        while ($loop->have_posts()) : $loop->have_posts();
            wc_get_template_part('content', 'product');
        endwhile;
        echo '</div>';
    endif;

    wp_reset_postdata();
    ?>
</div>
