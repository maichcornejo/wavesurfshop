<?php
/**
 * Template Name: Favoritos
 */

defined('ABSPATH') || exit;

get_header();

?>

<section class="favorites-hero">
  <div class="container">
    <h1>Mis favoritos</h1>
    <p>Guardá tus productos preferidos y volvé cuando quieras ❤️</p>
  </div>
</section>

<?php if ( ! is_user_logged_in() ) : ?>

  <section class="favorites-locked">
    <div class="container">
      <div class="favorites-card">
        <h2>Iniciá sesión</h2>
        <p>Necesitás estar logueado para ver tus favoritos.</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn-favorites">
          Ir a mi cuenta
        </a>
      </div>
    </div>
  </section>

<?php
get_footer();
return;
endif;

$user_id   = get_current_user_id();
$favorites = get_user_meta($user_id, 'wc_favorites', true);
$favorites = is_array($favorites) ? $favorites : [];
?>

<section class="favorites-page">
  <div class="container">

    <?php if ( empty($favorites) ) : ?>

      <div class="favorites-empty">
        <span class="heart">♡</span>
        <h3>No tenés favoritos todavía</h3>
        <p>Explorá la tienda y tocá el corazón en los productos que te gusten.</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn-favorites">
          Ir a la tienda
        </a>
      </div>

    <?php else : ?>

      <div class="favorites-list">

        <?php
        $args = [
          'post_type' => 'product',
          'post__in'  => $favorites,
          'orderby'   => 'post__in',
        ];

        $loop = new WP_Query($args);

        while ( $loop->have_posts() ) :
          $loop->the_post();
          global $product;

          $brands = wc_get_product_terms(
            $product->get_id(),
            'product_brand',
            ['fields' => 'names']
          );
          ?>

          <article class="favorite-row">

            <!-- IMAGEN -->
            <a href="<?php the_permalink(); ?>" class="favorite-image">
              <?php echo $product->get_image('large'); ?>
            </a>

            <!-- INFO -->
            <div class="favorite-info">

              <?php if ( ! empty($brands) ) : ?>
                <span class="favorite-brand">
                  <?php echo esc_html($brands[0]); ?>
                </span>
              <?php endif; ?>

              <h2 class="favorite-title">
                <a href="<?php the_permalink(); ?>">
                  <?php the_title(); ?>
                </a>
              </h2>

              <div class="favorite-price">

                <?php if ( $product->is_type('variable') ) : ?>

                  <?php
                  // Para variables: Woo ya arma el rango correctamente
                  echo $product->get_price_html();
                  ?>

                <?php elseif ( $product->is_on_sale() ) : ?>

                  <?php
                  $regular = (float) $product->get_regular_price();
                  $sale    = (float) $product->get_sale_price();

                  if ( $regular > 0 && $sale > 0 ) :
                    $percent = round( ( ( $regular - $sale ) / $regular ) * 100 );
                  ?>

                    <span class="price-regular">
                      <?php echo wc_price( $regular ); ?>
                    </span>

                    <span class="price-sale">
                      <?php echo wc_price( $sale ); ?>
                    </span>

                    <span class="price-discount">
                      <?php echo esc_html( $percent ); ?>% OFF
                    </span>

                  <?php else : ?>

                    <?php echo $product->get_price_html(); ?>

                  <?php endif; ?>

                <?php else : ?>

                  <?php echo $product->get_price_html(); ?>

                <?php endif; ?>

              </div>


              <?php
                $virtual_stock = waves_get_product_stock_status( $product );
              ?>

              <div
                class="waves-stock"
                data-stock="<?php echo esc_attr( $virtual_stock ); ?>"
              >
                <div class="waves-stock-bar">
                  <span></span>
                </div>
                <div class="waves-stock-text"></div>
              </div>


              <div class="favorite-actions">
                <a href="<?php the_permalink(); ?>" class="btn-view">
                  Ver producto
                </a>
                <button
                  class="btn-remove-favorite"
                  data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
                >
                  Quitar de favoritos
                </button>

              </div>

            </div>

          </article>

        <?php endwhile; wp_reset_postdata(); ?>

      </div>

    <?php endif; ?>


  </div>
</section>

<?php get_footer(); ?>
