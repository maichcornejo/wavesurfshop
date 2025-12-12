<?php
/**
 * Custom Product Archive Template (AJAX Ready)
 */

defined('ABSPATH') || exit;

get_header();

// HOOKS CRÍTICOS — WooCommerce detecta la página desde acá
do_action( 'woocommerce_before_main_content' );
do_action( 'woocommerce_before_shop_loop' ); 
?>

<div id="product-results-wrapper" class="container py-4">

    <div class="results-layout" style="display:flex; gap:30px;">


        <aside id="product-filters" style="width:260px;">
            <h3>Filtros</h3>

            <!-- Género -->
            <div class="filter-block">
                <h4>Género</h4>
                <label><input type="checkbox" class="filter-gender" value="hombre"> Hombre</label><br>
                <label><input type="checkbox" class="filter-gender" value="mujer"> Mujer</label><br>
                <label><input type="checkbox" class="filter-gender" value="niños"> Niños</label><br>
            </div>

            <div class="filter-block" style="margin-top:20px;">
                <h4>Precio máx</h4>
                <input type="range" id="filter-price" min="0" max="1000000" step="500" value="1000000">
                <span id="price-value">Hasta $1.000.000</span>
            </div>

            <div class="filter-block" style="margin-top:20px;">
                <h4>Marca</h4>
                <label><input type="checkbox" class="filter-brand" value="nike"> Nike</label><br>
                <label><input type="checkbox" class="filter-brand" value="adidas"> Adidas</label><br>
                <label><input type="checkbox" class="filter-brand" value="fila"> Fila</label><br>
                <label><input type="checkbox" class="filter-brand" value="dc"> DC Shoes</label>
            </div>

            <div class="filter-block" style="margin-top:20px;">
                <h4>Talle</h4>
                <?php for ($i = 35; $i <= 46; $i++) : ?>
                    <label><input type="checkbox" class="filter-size" value="<?php echo $i; ?>"> <?php echo $i; ?></label><br>
                <?php endfor; ?>
            </div>

        </aside>


        <div id="products-container" style="flex:1;">

            <header class="woocommerce-products-header">
                <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
            </header>

            <div id="products-list">
                <?php
                if (woocommerce_product_loop()) {

                    woocommerce_product_loop_start();

                    while (have_posts()) {
                        the_post();
                        wc_get_template_part('content', 'product');
                    }

                    woocommerce_product_loop_end();

                } else {
                    echo "<p>No se encontraron productos.</p>";
                }
                ?>
            </div>

        </div>

    </div>

</div>

<?php
// HOOKS DE CIERRE — WooCommerce necesita esto para ejecutar scripts y estilos
do_action( 'woocommerce_after_shop_loop' );
do_action( 'woocommerce_after_main_content' );
do_action( 'woocommerce_sidebar' );

get_footer();
