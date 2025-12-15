<?php
defined('ABSPATH') || exit;

get_header();
do_action('woocommerce_before_main_content');
?>

<div class="container single-product-waves">

<?php while ( have_posts() ) : the_post(); ?>
<?php global $product; ?>

<div class="product-main">

  <!-- ================= GALERÍA ================= -->
  <div class="product-gallery woocommerce-product-gallery">
    <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
  </div>

  <!-- ================= INFO ================= -->
  <div class="product-info">
    <div class="summary entry-summary">

      <h1><?php the_title(); ?></h1>

      <?php woocommerce_template_single_price(); ?>

      <?php if ( $product->is_type( 'variable' ) ) :

        $available_variations = $product->get_available_variations();
        $attributes           = $product->get_variation_attributes();
      ?>

      <form class="variations_form cart"
        method="post"
        enctype="multipart/form-data"
        data-product_id="<?php echo absint( $product->get_id() ); ?>"
        data-product_variations='<?php echo wp_json_encode( $available_variations ); ?>'>

        <table class="variations">
          <tbody>

          <?php foreach ( $attributes as $attribute_name => $options ) :

            $is_color   = ( $attribute_name === 'pa_color' );
            $field_name = 'attribute_' . $attribute_name;
          ?>
            <tr>
              <th class="label">
                <?php echo wc_attribute_label( $attribute_name ); ?>
              </th>

              <td class="value">

                <?php
                wc_dropdown_variation_attribute_options( [
                  'options'   => $options,
                  'attribute' => $attribute_name,
                  'product'   => $product,
                ] );
                ?>

                <?php if ( $is_color ) : ?>
                <div class="waves-color-swatches">

                  <?php foreach ( $options as $option ) :

                    $img = '';
                    foreach ( $available_variations as $variation ) {
                      if (
                        isset( $variation['attributes'][ $field_name ] ) &&
                        $variation['attributes'][ $field_name ] === $option
                      ) {
                        $img = $variation['image']['src'] ?? '';
                        break;
                      }
                    }
                  ?>
                    <label class="color-swatch" title="<?php echo esc_attr( $option ); ?>">
                      <input type="radio"
                        name="<?php echo esc_attr( $field_name ); ?>"
                        value="<?php echo esc_attr( $option ); ?>">

                      <span class="swatch-image">
                        <?php if ( $img ) : ?>
                          <img src="<?php echo esc_url( $img ); ?>" alt="">
                        <?php endif; ?>
                      </span>
                    </label>
                  <?php endforeach; ?>

                </div>
                <?php endif; ?>

              </td>
            </tr>
          <?php endforeach; ?>

          </tbody>
        </table>

        <!-- ===== INFO EXTRA ===== -->
		<div class="waves-stock">
			<small class="waves-stock-text">Seleccioná un talle</small>
			<div class="waves-stock-bar">
				<span></span>
			</div>
		</div>



        <div class="waves-size-guide">📏 Ver guía de talles</div>
        <div class="waves-notify">🔔 Avisarme cuando haya stock</div>

        <!-- ===== WC ===== -->
        <div class="single_variation_wrap">
          <div class="woocommerce-variation single_variation"></div>
        </div>

        <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
        <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
        <input type="hidden" name="variation_id" class="variation_id" value="0">

        <button type="submit" class="single_add_to_cart_button button">
          AGREGAR AL CARRITO
        </button>

      </form>

      <?php endif; ?>

    </div>

  </div>
  <div class="waves-accordion">

  <!-- DESCRIPCIÓN -->
  <div class="waves-accordion-item is-open">
    <button class="waves-accordion-header">
      <span>Descripción</span>
      <i class="accordion-icon">–</i>
    </button>
    <div class="waves-accordion-content">
      <?php the_content(); ?>
    </div>
  </div>

  <!-- ENVÍOS -->
  <div class="waves-accordion-item">
    <button class="waves-accordion-header">
      <span>Envíos y devoluciones</span>
      <i class="accordion-icon">+</i>
    </button>
    <div class="waves-accordion-content">
      <p>📦 Envíos a todo el país.</p>
      <p>🚚 En CABA y GBA podés recibir tu pedido en 24/48 hs.</p>
      <p>🔁 Cambios sin cargo dentro de los 30 días.</p>
    </div>
  </div>

  <!-- PAGOS -->
  <div class="waves-accordion-item">
    <button class="waves-accordion-header">
      <span>Formas de pago</span>
      <i class="accordion-icon">+</i>
    </button>
    <div class="waves-accordion-content">
      <p>💳 Tarjetas de crédito y débito.</p>
      <p>💰 Hasta 6 cuotas sin interés con débito.</p>
      <p>🔒 Pagos 100% seguros.</p>
    </div>
  </div>

</div>


</div>

<?php endwhile; ?>

</div>

<?php
do_action('woocommerce_after_main_content');
get_footer();
