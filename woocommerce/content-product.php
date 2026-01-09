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

        <?php
        $user_id    = get_current_user_id();
        $product_id = get_the_ID();
        $favorites  = get_user_meta( $user_id, 'wc_favorites', true );
        $favorites  = is_array($favorites) ? $favorites : [];

        $is_fav = in_array( $product_id, $favorites );
        ?>

        <button
            class="fav-heart <?php echo $is_fav ? 'active' : ''; ?>"
            data-product-id="<?php echo esc_attr( $product_id ); ?>"
            aria-label="Agregar a favoritos"
        >
            ♥
            <span class="fav-tooltip">
            <?php echo $is_fav ? 'Quitar de favoritos' : 'Agregar a favoritos'; ?>
            </span>
        </button>

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

                // $color suele venir como slug: "blanco-negro" o "blanco/negro"
                $parts = preg_split('/[\/\-]+/', strtolower($color));
                $left  = $parts[0] ?? $color;
                $right = $parts[1] ?? $left; // si no hay segundo, queda monocolor
            ?>
                <span
                    class="color-dot"
                    data-left="<?php echo esc_attr($left); ?>"
                    data-right="<?php echo esc_attr($right); ?>"
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
