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

      <h1><?php the_title(); ?></h1>

      <div class="price-2">
          <?php woocommerce_template_single_price(); ?>
      </div>
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
                <?php if ( strpos( $attribute_name, 'talle' ) !== false ) : ?>
                <div class="waves-size-label">
                  <strong>Talle</strong>
                  <span>Seleccioná tu talle</span>
                </div>
                <div
                  class="waves-size-grid"
                  data-attribute="attribute_pa_talle-calzado">

                  <?php foreach ( $options as $option ) : ?>
                    <button
                      type="button"
                      class="size-box"
                      data-value="<?php echo esc_attr( $option ); ?>">
                      <?php echo esc_html( $option ); ?>
                    </button>
                  <?php endforeach; ?>

                </div>
              <?php endif; ?>


                <?php if ( $is_color ) : ?>
                  <div class="waves-size-label">
                    <strong>Color</strong>
                    <span>Elegí un color</span>
                  </div>
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
        
        <div class="single_variation_wrap">
          <div class="woocommerce-variation single_variation"></div>
        </div>
        
        <small class="waves-stock-text">Selecciona una opción</small> 
        <div class="waves-stock-bar">
          <span></span>
        </div>
      </div>



      <div class="waves-size-guide">📏 Ver guía de talles</div>
        <button type="button" class="waves-notify" id="wavesNotifyBtn">
          🔔 Avisarme cuando haya stock
        </button>

        <!-- ===== WC ===== -->

        <input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
        <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
        <input type="hidden" name="variation_id" class="variation_id" value="0">

        <div>
          <button type="submit" class="single_add_to_cart_button">
            AGREGAR AL CARRITO
          </button>
        </div>

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
      <p>🔒 Pagos 100% seguros.a</p>
    </div>
  </div>

</div>


</div>

<?php endwhile; ?>

</div>

<!-- ================= MODAL AVISO STOCK ================= -->

<div class="waves-notify-modal" id="wavesNotifyModal">

  <div class="waves-notify-box">

    <button class="waves-notify-close" id="wavesNotifyClose">✕</button>

    <h3>Te avisamos cuando vuelva a estar disponible</h3>

    <div class="waves-notify-product">
      <strong class="notify-product-name"></strong>
      <span class="notify-product-meta"></span>
    </div>

    <p class="waves-notify-sub">
      Recibí un mensaje de WhatsApp apenas ingrese tu talle.
    </p>


    <form class="waves-notify-form">

      <label>Talle</label>
      <select required>
        <option value="">Seleccionar talle</option>
        <option>35</option>
        <option>36</option>
        <option>37</option>
        <option>38</option>
        <option>39</option>
        <option>40</option>
        <option>41</option>
        <option>42</option>
        <option>43</option>
        <option>44</option>
        <option>45</option>
      </select>

      <label>WhatsApp</label>
      <input type="tel" placeholder="Ej: 2804123456" required>

      <button type="submit">
        NOTIFICARME CUANDO INGRESE
      </button>

      <small>
        Promesa sin spam. Solo para este producto.
      </small>

    </form>

  </div>
</div>

<!-- ================= FIN MODAL ================= -->

<?php
do_action('woocommerce_after_main_content');
get_footer();