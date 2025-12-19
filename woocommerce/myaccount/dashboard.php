<?php
defined('ABSPATH') || exit;

$user = wp_get_current_user();
?>

<div class="dashboard-welcome">
    <h2>¡Hola <?php echo esc_html( $user->display_name ); ?>! 👋</h2>
    <p>Desde tu cuenta podés ver tus pedidos, administrar tus datos y más.</p>
</div>

<div class="dashboard-actions">
    <a href="<?php echo wc_get_endpoint_url('orders'); ?>" class="dash-btn">Mis pedidos</a>
    <a href="<?php echo wc_get_endpoint_url('edit-account'); ?>" class="dash-btn">Editar cuenta</a>
    <a href="<?php echo wc_get_endpoint_url('edit-address'); ?>" class="dash-btn">Direcciones</a>
    <a href="<?php echo wc_get_endpoint_url('wishlist'); ?>" class="dash-btn">Mis favoritos</a>
</div>
