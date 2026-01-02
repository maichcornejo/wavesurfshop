
<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_account_navigation'); ?>

<div class="myaccount-container">

    <main class="myaccount-content">
        <?php
            /**
             * Mostrar la sección seleccionada (pedidos, editar cuenta, direcciones, etc.)
             */
            do_action('woocommerce_account_content');
        ?>
    </main>

</div>

<?php do_action('woocommerce_after_account_navigation'); ?>
