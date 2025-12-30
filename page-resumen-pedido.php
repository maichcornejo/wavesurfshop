<?php
/**
 * Template Name: Resumen del pedido
 */
defined('ABSPATH') || exit;

get_header();

if (WC()->cart->is_empty()) {
  wp_safe_redirect(wc_get_cart_url());
  exit;
}
?>

<div class="container resumen-pedido">

  <h1>Resumen de tu pedido</h1>

  <!-- ======================
       PRODUCTOS + CUPONES
  ======================= -->
  <div class="resumen-box">

    <h3>Productos</h3>

    <?php foreach (WC()->cart->get_cart() as $item):
      $product = $item['data'];
      ?>
      <div class="resumen-item">
        <span><?php echo esc_html($product->get_name()); ?> × <?php echo $item['quantity']; ?></span>
        <strong><?php echo wc_price($item['line_subtotal']); ?></strong>
      </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->get_coupons()): ?>
      <div class="resumen-cupones">
        <?php foreach (WC()->cart->get_coupons() as $coupon): ?>
          <div class="resumen-cupon">
            Cupón <strong><?php echo esc_html($coupon->get_code()); ?></strong>
            − <?php echo wc_price(WC()->cart->get_coupon_discount_amount($coupon->get_code())); ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="resumen-subtotal">
      Subtotal
      <strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
    </div>

  </div>

  <!-- ======================
       ENVÍO (NATIVO)
  ======================= -->
  <div class="resumen-box">

    <h3>Envío</h3>

    <?php
    // fuerza que el formulario esté visible
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_true');

    woocommerce_shipping_calculator();
    ?>


  </div>

  <div class="resumen-box resumen-metodos-envio">
    <h3>Elegí el método de envío</h3>
    <?php
    // 🔥 Forzar cálculo de envío si hay dirección
    if (WC()->cart && WC()->customer) {
      WC()->cart->calculate_shipping();
      WC()->cart->calculate_totals();
    }
    ?>

    <?php if (WC()->cart->needs_shipping() && WC()->cart->get_shipping_packages()): ?>
      <?php wc_cart_totals_shipping_html(); ?>
    <?php else: ?>
      <p>Ingresá tu dirección para ver las opciones de envío.</p>
    <?php endif; ?>
  </div>


  <!-- ======================
       TOTAL
  ======================= -->
  <div class="resumen-box resumen-total">

    <?php if (WC()->cart->get_shipping_total() > 0): ?>

      <span>Total</span>
      <strong><?php echo WC()->cart->get_total(); ?></strong>

    <?php else: ?>

      <span>Total</span>
      <strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
      <small style="display:block;font-size:13px;opacity:.85">
        Calculá el envío para ver el total final
      </small>

    <?php endif; ?>

  </div>


  <!-- ======================
       MÉTODO DE PAGO
  ======================= -->
  <div class="resumen-box resumen-pago">

    <h3>¿Cómo querés pagar?</h3>

    <button class="btn-pago" data-method="checkout">
      Pagar ahora
    </button>

    <button class="btn-pago" data-method="transferencia">
      Transferencia bancaria
    </button>

  </div>

</div>

<?php get_footer(); ?>