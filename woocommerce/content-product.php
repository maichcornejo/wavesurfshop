<?php
defined('ABSPATH') || exit;
global $product;

// Imagen principal
$main_image_id = $product->get_image_id();

// Colores + imagen por variación
$color_map = [];

if ($product->is_type('variable')) {
    foreach ($product->get_available_variations() as $variation) {
        if (
            isset($variation['attributes']['attribute_pa_color']) &&
            !empty($variation['image_id'])
        ) {
            $color = $variation['attributes']['attribute_pa_color'];

            $image_url = wp_get_attachment_image_url(
                $variation['image_id'],
                'woocommerce_single'
            );

            if ($image_url) {
                $color_map[$color] = $image_url;
            }
        }
    }
}

?>

<article <?php wc_product_class('product-card', $product); ?>>

    <a href="<?php the_permalink(); ?>" class="product-image">
        <?php echo wp_get_attachment_image(
            $main_image_id,
            'woocommerce_single',
            false,
            ['class' => 'img-main']
        ); ?>
    </a>

    <div class="product-info">

        <h3 class="product-title"><?php the_title(); ?></h3>

        <span class="product-price">
            <?php echo $product->get_price_html(); ?>
        </span>

        <?php if (!empty($color_map)) : ?>
            <div class="product-colors">
                <?php
                $i = 0;
                foreach ($color_map as $color => $image) :
                    if ($i >= 4) break;
                ?>
                    <span
                        class="color-dot"
                        data-color="<?php echo esc_attr($color); ?>"
                        data-image="<?php echo esc_url($image); ?>">
                    </span>
                <?php
                    $i++;
                endforeach;

                if (count($color_map) > 4) :
                    echo '<span class="color-more">+' . (count($color_map) - 4) . '</span>';
                endif;
                ?>
            </div>
        <?php endif; ?>

    </div>
</article>
