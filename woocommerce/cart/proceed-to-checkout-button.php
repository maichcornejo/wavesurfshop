<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" 
   class="waves-checkout-btn wc-forward">
    Finalizar compra
</a>
