<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="waves-cart-totals <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">

  <header class="waves-cart-header">
    <h2>Total del carrito</h2>
  </header>

  <div class="waves-cart-body">

    <div class="waves-row">
      <span>Subtotal</span>
      <strong><?php wc_cart_totals_subtotal_html(); ?></strong>
    </div>

    <div class="waves-shipping">
      <?php wc_cart_totals_shipping_html(); ?>
    </div>

    <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
      <div class="waves-row">
        <span><?php echo esc_html( $fee->name ); ?></span>
        <strong><?php wc_cart_totals_fee_html( $fee ); ?></strong>
      </div>
    <?php endforeach; ?>

    <div class="waves-row total">
      <span>Total</span>
      <strong><?php wc_cart_totals_order_total_html(); ?></strong>
    </div>

  </div>

  <div class="waves-cart-cta">
    <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
  </div>

</div>

