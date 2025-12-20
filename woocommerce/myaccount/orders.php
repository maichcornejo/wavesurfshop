<?php
defined('ABSPATH') || exit;

$customer_orders = wc_get_orders([
    'customer' => get_current_user_id(),
    'paginate' => true,
]);

?>

<div class="waves-orders">

    <h2 class="waves-orders-title">Mis pedidos</h2>
    <div class="waves-orders-back">
        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="waves-back-btn">
            Volver al menú
        </a>
    </div>


    <?php if ($customer_orders->total > 0): ?>

        <?php foreach ($customer_orders->orders as $order): ?>
            <?php
            $order_id = $order->get_id();
            $status = wc_get_order_status_name($order->get_status());
            $date = wc_format_datetime($order->get_date_created());
            $total = $order->get_formatted_order_total();
            $items_count = $order->get_item_count();
            ?>

            <article class="waves-order-card status-<?php echo esc_attr($order->get_status()); ?>">

                <div class="order-main">
                    <span class="order-number">Pedido #<?php echo esc_html($order_id); ?></span>
                    <span class="order-date"><?php echo esc_html($date); ?></span>
                </div>

                <div class="order-meta">
                    <span class="order-status"><?php echo esc_html($status); ?></span>
<<<<<<< HEAD
                    <span class="order-total">
                        <?php echo wp_kses_post($total); ?>
                        <small><?php echo esc_html($items_count); ?> productos</small>
                    </span>
=======
                    <small>
                        <?php
                        echo esc_html($items_count) . ' ' . ($items_count === 1 ? 'producto' : 'productos');
                        ?>
                    </small>

>>>>>>> refs/remotes/origin/maia
                </div>

                <div class="order-actions">
                    <a class="waves-btn" href="<?php echo esc_url($order->get_view_order_url()); ?>">
                        Ver pedido
                    </a>
                </div>

            </article>

        <?php endforeach; ?>

    </div>

    <?php do_action('woocommerce_account_orders_pagination'); ?>

<?php else: ?>

    <div class="waves-orders-empty">
        <p>No realizaste pedidos todavía.</p>
        <a class="waves-btn" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
            Ir a la tienda
        </a>
    </div>

<?php endif; ?>

</div>