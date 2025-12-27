<?php
if (!defined('ABSPATH'))
    exit;

/*
|--------------------------------------------------------------------------
| 1) CARGAR CSS DEL TEMA PADRE LUCAS HOLA
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| 2) CARGAR TU CSS PERSONALIZADO (HOME, COMPONENTS, ETC)
|--------------------------------------------------------------------------
*/


function waves_enqueue_woocommerce_styles()
{

    // Solo cargar en páginas de WooCommerce: tienda, categorías, producto, búsqueda
    if (function_exists('is_woocommerce') && is_woocommerce() || is_product_taxonomy() || is_product()) {

        wp_enqueue_style(
            'waves-woocommerce-style',
            get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
            array('parent-style'),
            time()
        );
    }
}
add_action('wp_enqueue_scripts', 'waves_enqueue_woocommerce_styles', 99);


/*
|--------------------------------------------------------------------------
| 3) DESACTIVAR ESTILOS O SCRIPTS DEL PADRE — opcional
|--------------------------------------------------------------------------
*/
function child_remove_parent_assets()
{
    // wp_dequeue_style('animate-css');
    // wp_dequeue_script('wow-js');
}
add_action('wp_enqueue_scripts', 'child_remove_parent_assets', 20);



/* ============================================================
   CARGAR JS PARA FILTROS AJAX
============================================================ */
function waves_filters_scripts()
{

    wp_register_script(
        'waves-filter-products',
        get_stylesheet_directory_uri() . '/assets/js/filter-products.js',
        ['jquery'],
        time(),
        true
    );

    wp_localize_script('waves-filter-products', 'waves_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('waves_filter_nonce'),
    ]);

    wp_enqueue_script('waves-filter-products');
}
add_action('wp_enqueue_scripts', 'waves_filters_scripts');

add_action('wp_ajax_filter_products', 'waves_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'waves_filter_products');

function waves_filter_products()
{

    check_ajax_referer('waves_filter_nonce', 'nonce');

    $filters = $_POST['filters'] ?? [];
    $price = isset($_POST['price']) ? intval($_POST['price']) : 0;
    $page = max(1, intval($_POST['page'] ?? 1));

    /* =========================
       TAX QUERY (ATRIBUTOS)
    ========================= */

    $tax_query = [
        'relation' => 'AND'
    ];

    foreach ($filters as $taxonomy => $terms) {

        if (empty($terms))
            continue;

        $tax_query[] = [
            'taxonomy' => sanitize_text_field($taxonomy),
            'field' => 'slug',
            'terms' => array_map('sanitize_text_field', $terms),
            'operator' => 'IN'
        ];
    }

    /* =========================
       META QUERY (PRECIO)
    ========================= */

    $meta_query = [
        'relation' => 'AND'
    ];

    if ($price > 0) {
        $meta_query[] = [
            'relation' => 'OR',

            // Productos simples
            [
                'key' => '_price',
                'value' => $price,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ],

            // Productos variables
            [
                'key' => '_min_variation_price',
                'value' => $price,
                'compare' => '<=',
                'type' => 'NUMERIC'
            ]
        ];
    }

    /* =========================
       QUERY FINAL
    ========================= */

    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'paged' => $page,
        'tax_query' => $tax_query,
        'meta_query' => $meta_query,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {

        woocommerce_product_loop_start();

        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }

        woocommerce_product_loop_end();

    } else {
        echo '<p class="no-results">No se encontraron productos.</p>';
    }

    wp_die();
}


/* ============================================================
   CARGA ORDENADA DE CSS & JS DEL TEMA HIJO
============================================================ */
function waves_child_assets()
{

    /* ============================
       1) CSS PADRE
    ============================= */
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    /* ============================
       2) CSS GENERALES DEL HIJO
    ============================= */
    wp_enqueue_style(
        'child-home',
        get_stylesheet_directory_uri() . '/assets/css/home.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/home.css')
    );

    wp_enqueue_style(
        'child-woocommerce',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/woocommerce.css')
    );

    wp_enqueue_style(
        'child-footer',
        get_stylesheet_directory_uri() . '/assets/css/footer.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/footer.css')
    );

    wp_enqueue_style(
        'child-header',
        get_stylesheet_directory_uri() . '/assets/css/header.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/header.css')
    );


    wp_enqueue_style(
        'child-woocommerce-cards',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce-cards.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/woocommerce-cards.css')
    );

    wp_enqueue_style(
        'child-woocommerce-cart',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce-cart.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/woocommerce-cart.css')
    );

    wp_enqueue_style(
        'child-woocommerce-shop',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce-shop.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/woocommerce-shop.css')
    );

    wp_enqueue_style(
        'child-woocommerce-singleproduct',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce-singleproduct.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/woocommerce-singleproduct.css')
    );

    wp_enqueue_style(
        'child-filtros',
        get_stylesheet_directory_uri() . '/assets/css/filtros.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/filtros.css')
    );

    wp_enqueue_style(
        'child-favoritos',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce-favoritos.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/woocommerce-favoritos.css')
    );
   wp_enqueue_style(
        'waves-order-guide',
        get_stylesheet_directory_uri() . '/assets/css/guia-pedido.css',
        [],
        filemtime(get_stylesheet_directory() . '/assets/css/guia-pedido.css')
    );
    /* ============================
       3) JS DEL CARRUSEL DE MARCAS
    ============================= */
    wp_enqueue_script(
        'waves-brand',
        get_stylesheet_directory_uri() . '/assets/js/brand.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/brand.js'),
        true
    );

    /* ============================
       4) JS DE FILTRO AJAX
    ============================= */
    wp_register_script(
        'waves-filter-products',
        get_stylesheet_directory_uri() . '/assets/js/filter-products.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/assets/js/filter-products.js'),
        true
    );

    wp_localize_script('waves-filter-products', 'waves_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('waves_filter_nonce'),
    ));

    wp_enqueue_script('waves-filter-products');
}
add_action('wp_enqueue_scripts', 'waves_child_assets');

function waves_cart_autoupdate_script()
{
    if (is_cart()) {
        wp_enqueue_script(
            'cart-ajax-custom',
            get_stylesheet_directory_uri() . '/assets/js/cart-ajax.js',
            array('jquery', 'wc-cart'), // <--- MUY IMPORTANTE
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'waves_cart_autoupdate_script');


add_action('wp_enqueue_scripts', function () {
    if (is_cart()) {
        wp_enqueue_script(
            'cart-autoupdate',
            get_stylesheet_directory_uri() . '/assets/js/cart-autoupdate.js',
            array('jquery', 'wc-cart'),
            time(),
            true
        );
    }
});


// Desactivar el banner del theme padre
add_action('wp_enqueue_scripts', function () {
    wp_add_inline_style('shoes-store-elementor-style', '
        .header-image-box {
            display: none !important;
        }
    ');
}, 99);


/* aa*/
function waves_faq_styles()
{
    wp_enqueue_style(
        'faq-styles',
        get_stylesheet_directory_uri() . '/assets/css/faq.css',
        array(),
        '1.0',
        'all'
    );
}
add_action('wp_enqueue_scripts', 'waves_faq_styles');


function waves_enqueue_snap_scroll()
{
    if (is_front_page()) {
        wp_enqueue_style(
            'waves-snap-scroll',
            get_stylesheet_directory_uri() . '/assets/css/snap-scroll.css',
            array(),
            time()
        );
    }
}
add_action('wp_enqueue_scripts', 'waves_enqueue_snap_scroll');

function waves_story_scroll_script()
{

    if (is_front_page()) {
        wp_enqueue_script(
            'waves-story-scroll',
            get_stylesheet_directory_uri() . '/assets/js/story-scroll.js',
            array(),
            time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'waves_story_scroll_script');


function waves_product_card_scripts()
{

    if (function_exists('is_shop') && (is_shop() || is_front_page())) {
        wp_enqueue_script(
            'waves-product-card',
            get_stylesheet_directory_uri() . '/assets/js/product-card.js',
            [],
            time(),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'waves_product_card_scripts');

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script(
        'waves-product-card',
        get_stylesheet_directory_uri() . '/assets/js/product-card.js',
        [],
        time(),
        true
    );
});


add_action('wp_enqueue_scripts', function () {

    // Páginas donde hay PRODUCT CARDS con corazón
    wp_enqueue_script(
        'waves-favorites',
        get_stylesheet_directory_uri() . '/assets/js/single-product-variations.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/single-product-variations.js'),
        true
    );

});

add_action('wp_enqueue_scripts', function () {
    if (is_product()) {
        wp_enqueue_script('wc-add-to-cart-variation');
    }
});

/* ============================================================
   SEGUIMIENTO DE ENVÍOS – TRACKING
============================================================ */
function waves_enqueue_tracking_assets()
{

    // Solo en la página "seguimiento-envios"
    if (!is_page('seguimiento-envios')) {
        return;
    }

    $theme_uri = get_stylesheet_directory_uri();
    $theme_path = get_stylesheet_directory();

    // CSS
    wp_enqueue_style(
        'waves-tracking-css',
        $theme_uri . '/assets/css/tracking.css',
        array(),
        filemtime($theme_path . '/assets/css/tracking.css')
    );

    // JS (si todavía no lo creaste, podés comentarlo)
    wp_enqueue_script(
        'waves-tracking-js',
        $theme_uri . '/assets/js/tracking.js',
        array('jquery'),
        filemtime($theme_path . '/assets/js/tracking.js'),
        true
    );

    wp_localize_script(
        'waves-tracking-js',
        'wavesTracking',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('waves_tracking_nonce'),
        ]
    );

}
add_action('wp_enqueue_scripts', 'waves_enqueue_tracking_assets');

add_filter('woocommerce_account_menu_items', 'waves_remove_downloads_menu');
function waves_remove_downloads_menu($items)
{

    unset($items['downloads']);

    return $items;
}
add_filter('woocommerce_account_menu_items', 'waves_add_favorites_menu');
function waves_add_favorites_menu($items)
{

    $items['favorites'] = __('Mis favoritos');

    return $items;
}
add_action('init', 'waves_add_favorites_endpoint');
function waves_add_favorites_endpoint()
{
    add_rewrite_endpoint('favorites', EP_ROOT | EP_PAGES);
}
add_action('woocommerce_account_favorites_endpoint', 'waves_favorites_content');
function waves_favorites_content()
{
    echo '<h2>Mis favoritos</h2>';
    echo '<p>Acá vas a ver los productos guardados.</p>';
}
add_action('wp_enqueue_scripts', 'waves_enqueue_account_styles');
function waves_enqueue_account_styles()
{

    // Solo páginas de Mi Cuenta
    if (is_account_page()) {

        wp_enqueue_style(
            'waves-account-style',
            get_stylesheet_directory_uri() . '/assets/css/account.css',
            array(), // dependencias
            filemtime(get_stylesheet_directory() . '/assets/css/account.css')
        );
    }
}
add_action('wp_enqueue_scripts', function () {

    if (is_account_page()) {
        wp_enqueue_style(
            'waves-account-orders',
            get_stylesheet_directory_uri() . '/assets/css/account-orders.css',
            [],
            filemtime(get_stylesheet_directory() . '/assets/css/account-orders.css')
        );
    }

});


add_action('wp_ajax_toggle_favorite', 'toggle_favorite_product');
add_action('wp_ajax_nopriv_toggle_favorite', 'toggle_favorite_product');

function toggle_favorite_product()
{

    if (!is_user_logged_in()) {
        wp_send_json_error();
    }

    $user_id = get_current_user_id();
    $product_id = intval($_POST['product_id']);

    $favorites = get_user_meta($user_id, 'wc_favorites', true);
    $favorites = is_array($favorites) ? $favorites : [];

    if (in_array($product_id, $favorites)) {
        $favorites = array_diff($favorites, [$product_id]);
        $is_favorite = false;
    } else {
        $favorites[] = $product_id;
        $is_favorite = true;
    }

    update_user_meta($user_id, 'wc_favorites', $favorites);

    wp_send_json_success([
        'is_favorite' => $is_favorite
    ]);
}


add_action('template_redirect', function () {

    if (is_page('wishlist')) {
        wp_redirect(home_url('/favoritos'), 301);
        exit;
    }

});

/* ============================================================
   AJAX – SEGUIMIENTO DE ENVÍOS
============================================================ */

add_action('wp_ajax_waves_track_shipment', 'waves_track_shipment');
add_action('wp_ajax_nopriv_waves_track_shipment', 'waves_track_shipment');

function waves_track_shipment()
{

    check_ajax_referer('waves_tracking_nonce', 'nonce');

    $tracking_code = sanitize_text_field($_POST['code'] ?? '');

    if (empty($tracking_code)) {
        wp_send_json_error([
            'message' => 'Código inválido'
        ]);
    }

    /* ===================================================
       1) BUSCAR EN PEDIDOS DE WOOCOMMERCE
    =================================================== */

    $orders = wc_get_orders([
        'limit' => 1,
        'meta_key' => '_tracking_number',
        'meta_value' => $tracking_code,
    ]);

    if (!empty($orders)) {

        $order = $orders[0];

        wp_send_json_success([
            'type' => 'woocommerce',
            'order_id' => $order->get_id(),
            'status' => wc_get_order_status_name($order->get_status()),
            'date' => wc_format_datetime($order->get_date_created()),
            'total' => wc_price($order->get_total()),
            'tracking' => $tracking_code,
        ]);
    }

    /* ===================================================
       2) NO EXISTE → CORREO ARGENTINO
    =================================================== */

    wp_send_json_success([
        'type' => 'correo_argentino',
        'url' => 'https://www.correoargentino.com.ar/formularios/e-commerce?id=' . urlencode($tracking_code),
    ]);
}
add_action('wp_enqueue_scripts', function () {

    if (is_account_page()) {
        wp_enqueue_style(
            'waves-account-addresses',
            get_stylesheet_directory_uri() . '/assets/css/account-addresses.css',
            [],
            filemtime(get_stylesheet_directory() . '/assets/css/account-addresses.css')
        );
    }

});
/* ============================================================
   OCULTAR AVISO DE ZONA DE ENVÍO (FRONTEND) cartel verde
============================================================ */
add_filter('woocommerce_shipping_zone_method_added_notice', '__return_false');
add_filter('woocommerce_shipping_zone_method_updated_notice', '__return_false');
add_filter('woocommerce_shipping_zone_method_deleted_notice', '__return_false');
add_filter('woocommerce_add_notice', function ($message, $type) {

    if (strpos($message, 'Zona de coincidencia') !== false) {
        return;
    }

    wc_add_notice($message, $type);

}, 10, 2);

/* ============================================================
   CARGAR FUENTE GOOGLE FONTS: POPPINS
============================================================ */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'waves-google-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
        [],
        null
    );
});


function waves_get_product_stock_status(WC_Product $product)
{

    if (!$product->is_type('variable')) {
        return $product->get_stock_quantity();
    }

    $available = 0;

    foreach ($product->get_children() as $variation_id) {
        $variation = wc_get_product($variation_id);

        if (!$variation)
            continue;

        if ($variation->is_in_stock()) {
            $qty = (int) $variation->get_stock_quantity();

            // Si Woo no gestiona stock pero está "in stock"
            if ($qty === 0 || $qty === null) {
                return 10; // stock medio ficticio
            }

            $available += $qty;
        }
    }

    return $available;
}


add_action('wp_ajax_remove_favorite', 'remove_favorite');

function remove_favorite()
{

    if (!is_user_logged_in()) {
        wp_send_json_error();
    }

    $user_id = get_current_user_id();
    $product_id = intval($_POST['product_id']);

    $favorites = get_user_meta($user_id, 'wc_favorites', true);
    $favorites = is_array($favorites) ? $favorites : [];

    $favorites = array_diff($favorites, [$product_id]);

    update_user_meta($user_id, 'wc_favorites', array_values($favorites));

    wp_send_json_success();
}

/*correo argentino*/
add_action( 'wp_enqueue_scripts', function () {

    if ( is_cart() ) {

        wp_localize_script(
            'cart-autoupdate', // 🔴 ESTE es el handle correcto
            'wavesCart',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'waves_recalc_shipping' ),
            ]
        );

    }

});
add_action( 'wp_ajax_waves_force_recalc_shipping', 'waves_force_recalc_shipping' );
add_action( 'wp_ajax_nopriv_waves_force_recalc_shipping', 'waves_force_recalc_shipping' );

function waves_force_recalc_shipping() {
    check_ajax_referer( 'waves_recalc_shipping', 'nonce' );

    if ( ! function_exists('WC') || ! WC()->cart || ! WC()->session ) {
        wp_send_json_error( [ 'message' => 'WooCommerce no disponible' ] );
    }

    // ✅ limpiar caches de envío (Woo)
    WC()->session->set( 'shipping_for_package_0', null );
    WC()->session->set( 'chosen_shipping_methods', null );

    // En algunos setups también ayuda:
    WC()->customer->set_shipping_postcode('');
    // (si el plugin usa postcode para cotizar, lo va a recalcular al refrescar)

    // ✅ recalcular SOLO shipping (sin totals)
    WC()->cart->calculate_shipping();

    $packages = WC()->shipping()->get_packages();
    $rates_debug = [];

    if ( ! empty($packages) && isset($packages[0]['rates']) ) {
        foreach ( $packages[0]['rates'] as $rate_id => $rate ) {
            $rates_debug[] = [
                'id'    => $rate_id,
                'label' => $rate->get_label(),
                'cost'  => (float) $rate->get_cost(),
            ];
        }
    }

    wp_send_json_success([
        'rates' => $rates_debug,
    ]);
}
add_action('woocommerce_before_calculate_totals', function () {
    if ( WC()->session ) {
        foreach ( WC()->session->get_session_data() as $key => $value ) {
            if ( stripos($key, 'correo') !== false || stripos($key, 'argentin') !== false ) {
                WC()->session->__unset($key);
            }
        }
    }
}, 5);
add_filter('woocommerce_cart_shipping_packages', function ($packages) {

    if ( isset($packages[0]) ) {
        $packages[0]['rates'] = [];
    }

    return $packages;
}, 5);
add_action('woocommerce_cart_calculate_fees', function () {
    WC()->cart->fees_api()->remove_all_fees();
}, 1);


// Reemplazar login de WooCommerce por User Registration
add_action( 'woocommerce_before_customer_login_form', function () {

    if ( is_user_logged_in() ) {
        return;
    }

    echo '<div class="waves-custom-login">';

    // Login
    echo do_shortcode('[user_registration_login]');

    echo '<div class="waves-login-separator">o</div>';

    // Register
    echo do_shortcode('[user_registration_form id="133"]'); // CAMBIAR ID

    echo '</div>';

}, 1 );
