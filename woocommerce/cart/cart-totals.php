<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="cart_totals <?php echo WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''; ?>">

    <h2><?php esc_html_e( 'Total del carrito', 'woocommerce' ); ?></h2>

    <table cellspacing="0" class="shop_table shop_table_responsive">

        <tr class="cart-subtotal">
            <th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
            <td><?php wc_cart_totals_subtotal_html(); ?></td>
        </tr>

        <?php wc_cart_totals_shipping_html(); ?>

        <?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
            <tr class="fee">
                <th><?php echo esc_html( $fee->name ); ?></th>
                <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
            </tr>
        <?php endforeach; ?>

        <tr class="order-total">
            <th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
            <td><?php wc_cart_totals_order_total_html(); ?></td>
        </tr>

    </table>

    <div class="wc-proceed-to-checkout">
        <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
    </div>

</div>
