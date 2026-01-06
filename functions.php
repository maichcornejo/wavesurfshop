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
add_action('wp_enqueue_scripts', function () {

  if (is_cart()) {

    wp_localize_script(
      'cart-autoupdate', // 🔴 ESTE es el handle correcto
      'wavesCart',
      [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('waves_recalc_shipping'),
      ]
    );

  }

});


add_action('woocommerce_before_customer_login_form', function () {

  if (is_user_logged_in()) {
    return;
  }

  echo '<div class="waves-auth-wrapper">';

  // LOGIN
  echo do_shortcode('[user_registration_login]');

  echo '</div>';

}, 1);

add_action('wp_enqueue_scripts', function () {

  if (
    is_page_template('page-login.php') ||
    is_page_template('page-register.php') ||
    is_page_template('page-lost-password.php')
  ) {
    wp_enqueue_style(
      'waves-auth',
      get_stylesheet_directory_uri() . '/assets/css/auth.css',
      [],
      filemtime(get_stylesheet_directory() . '/assets/css/auth.css')
    );
  }

});

add_action('woocommerce_cart_actions', function () {

  if (WC()->cart && !WC()->cart->is_empty()) {
    echo '<a href="' . esc_url(site_url('/resumen-pedido')) . '" 
             class="button alt waves-btn-continuar">
            Continuar
          </a>';
  }

});
add_action('wp_enqueue_scripts', function () {

  if (!is_page('resumen-pedido')) {
    return;
  }

  wp_enqueue_style(
    'waves-resumen-pedido',
    get_stylesheet_directory_uri() . '/assets/css/resumen-pedido.css',
    [],
    filemtime(get_stylesheet_directory() . '/assets/css/resumen-pedido.css')
  );

},999);


/**
 * Redirigir Mi Cuenta a /login si el usuario no está logueado
 */
add_action('template_redirect', function () {

  if ((is_cart() || is_account_page()) && !is_user_logged_in()) {
    wp_redirect(home_url('/login'));
    exit;
  }

});


add_action('wp_enqueue_scripts', function () {

  if (!is_page('resumen-pedido')) {
    return;
  }

  wp_enqueue_script(
    'waves-resumen-pago',
    get_stylesheet_directory_uri() . '/assets/js/resumen-pago.js',
    ['jquery'],
    filemtime(get_stylesheet_directory() . '/assets/js/resumen-pago.js'),
    true
  );

});

add_action('wp_enqueue_scripts', function () {

  // Cargalo solo en tu template / página
  // Opción A: por slug (cambiá 'resumen-del-pedido' por el slug real)
  if (!is_page('resumen-pedido'))
    return;

  wp_enqueue_script(
    'waves-resumen-envio',
    get_stylesheet_directory_uri() . '/assets/js/resumen-envio.js',
    ['jquery'],
    filemtime(get_stylesheet_directory() . '/assets/js/resumen-envio.js'),
    true
  );

  // Si necesitás pasar variables (recomendado)
  wp_localize_script('waves-resumen-envio', 'waves_wc', [
    'ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
    'nonce' => wp_create_nonce('update-shipping-method'),
  ]);

}, 20);


add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

  ob_start(); ?>

  <div class="resumen-box resumen-total">
    <div class="resumen-total-inner">
      <span>Total</span>
      <strong><?php echo WC()->cart->get_total(); ?></strong>
    </div>

    <div class="resumen-total-loader">
      <span class="spinner"></span>
      <small>Calculando total…</small>
    </div>
  </div>

  <?php
  $fragments['.resumen-total'] = ob_get_clean();
  return $fragments;
});


/* ==========================================================
   MiCorreo API – Waves (Shipping by API)
   Pegar en functions.php (theme hijo)
========================================================== */

// ====== CONFIG (completá estos datos) ======
if (!defined('WAVES_MICORREO_BASE_URL')) {
  // TEST: https://apitest.correoargentino.com.ar/micorreo/v1
  // PROD: https://api.correoargentino.com.ar/micorreo/v1
  define('WAVES_MICORREO_BASE_URL', 'https://apitest.correoargentino.com.ar/micorreo/v1');
}

if (!defined('WAVES_MICORREO_USER'))
  define('WAVES_MICORREO_USER', 'TU_USER');
if (!defined('WAVES_MICORREO_PASS'))
  define('WAVES_MICORREO_PASS', 'TU_PASSWORD');

// CustomerId que te da MiCorreo (lo ves en la doc: se usa en /rates)
if (!defined('WAVES_MICORREO_CUSTOMER_ID'))
  define('WAVES_MICORREO_CUSTOMER_ID', '0000000000');

// Código postal origen (tu depósito / remitente)
if (!defined('WAVES_MICORREO_ORIGIN_CP'))
  define('WAVES_MICORREO_ORIGIN_CP', '0000');

// Defaults si el producto no tiene peso/dimensiones en Woo
if (!defined('WAVES_MICORREO_DEFAULT_WEIGHT_G'))
  define('WAVES_MICORREO_DEFAULT_WEIGHT_G', 1000); // 1kg
if (!defined('WAVES_MICORREO_DEFAULT_DIM_CM'))
  define('WAVES_MICORREO_DEFAULT_DIM_CM', 10);     // 10cm


/* ==========================================================
   HELPERS – Token + Request
========================================================== */

function waves_micorreo_get_token()
{
  $cached = get_transient('waves_micorreo_token');
  if (!empty($cached))
    return $cached;

  $url = rtrim(WAVES_MICORREO_BASE_URL, '/') . '/token';

  $args = [
    'method' => 'POST',
    'timeout' => 20,
    'headers' => [
      'Authorization' => 'Basic ' . base64_encode(WAVES_MICORREO_USER . ':' . WAVES_MICORREO_PASS),
      'Accept' => 'application/json',
    ],
  ];

  $res = wp_remote_request($url, $args);
  if (is_wp_error($res))
    return $res;

  $code = wp_remote_retrieve_response_code($res);
  $body = wp_remote_retrieve_body($res);

  if ($code < 200 || $code >= 300) {
    return new WP_Error('micorreo_token_error', 'Error token MiCorreo: ' . $code . ' ' . $body);
  }

  $json = json_decode($body, true);
  if (empty($json['token'])) {
    return new WP_Error('micorreo_token_invalid', 'Token inválido MiCorreo: ' . $body);
  }

  // expires: "YYYY-MM-DD HH:MM:SS" (según doc)
  $ttl = 50 * 60; // fallback 50min
  if (!empty($json['expires'])) {
    $expires_ts = strtotime($json['expires']);
    if ($expires_ts) {
      $ttl_calc = $expires_ts - time() - 60; // margen 60s
      if ($ttl_calc > 60)
        $ttl = $ttl_calc;
    }
  }

  set_transient('waves_micorreo_token', $json['token'], $ttl);

  return $json['token'];
}

function waves_micorreo_request($path, $method = 'GET', $data = null)
{
  $token = waves_micorreo_get_token();
  if (is_wp_error($token))
    return $token;

  $url = rtrim(WAVES_MICORREO_BASE_URL, '/') . '/' . ltrim($path, '/');

  $args = [
    'method' => $method,
    'timeout' => 25,
    'headers' => [
      'Authorization' => 'Bearer ' . $token,
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ],
  ];

  if ($data !== null) {
    $args['body'] = wp_json_encode($data);
  }

  $res = wp_remote_request($url, $args);
  if (is_wp_error($res))
    return $res;

  $code = wp_remote_retrieve_response_code($res);
  $body = wp_remote_retrieve_body($res);

  $json = json_decode($body, true);

  if ($code < 200 || $code >= 300) {
    return new WP_Error('micorreo_http_error', 'MiCorreo HTTP ' . $code, [
      'body' => $body,
      'json' => $json,
    ]);
  }

  return $json ?: [];
}


/* ==========================================================
   CART DIMENSIONS (weight + dims) ACA EMPIEZA CORREO ARGENTINO
========================================================== */

function waves_micorreo_cart_dimensions()
{
  $weight_g = 0;

  $max_h = 0;
  $max_w = 0;
  $max_l = 0;

  if (!WC()->cart) {
    return [
      'weight' => WAVES_MICORREO_DEFAULT_WEIGHT_G,
      'height' => WAVES_MICORREO_DEFAULT_DIM_CM,
      'width' => WAVES_MICORREO_DEFAULT_DIM_CM,
      'length' => WAVES_MICORREO_DEFAULT_DIM_CM,
    ];
  }

  foreach (WC()->cart->get_cart() as $item) {
    $product = $item['data'] ?? null;
    $qty = (int) ($item['quantity'] ?? 1);
    if (!$product)
      continue;

    // Woo weight unit suele ser kg. Convertimos a gramos.
    $w = (float) $product->get_weight();
    if ($w <= 0) {
      $weight_g += WAVES_MICORREO_DEFAULT_WEIGHT_G * $qty;
    } else {
      $weight_g += (int) round($w * 1000) * $qty;
    }

    $h = (float) $product->get_height();
    $wi = (float) $product->get_width();
    $l = (float) $product->get_length();

    $max_h = max($max_h, $h);
    $max_w = max($max_w, $wi);
    $max_l = max($max_l, $l);
  }

  // Defaults si faltan dimensiones
  if ($weight_g <= 0)
    $weight_g = WAVES_MICORREO_DEFAULT_WEIGHT_G;
  if ($max_h <= 0)
    $max_h = WAVES_MICORREO_DEFAULT_DIM_CM;
  if ($max_w <= 0)
    $max_w = WAVES_MICORREO_DEFAULT_DIM_CM;
  if ($max_l <= 0)
    $max_l = WAVES_MICORREO_DEFAULT_DIM_CM;

  return [
    'weight' => (int) $weight_g,
    'height' => (int) round($max_h),
    'width' => (int) round($max_w),
    'length' => (int) round($max_l),
  ];
}


/* ==========================================================
   WC-AJAX: 1) Guardar dirección de envío
========================================================== */

add_action('wc_ajax_waves_update_shipping_address', 'waves_update_shipping_address');
add_action('wc_ajax_nopriv_waves_update_shipping_address', 'waves_update_shipping_address');

function waves_update_shipping_address()
{
  if (!WC()->customer) {
    wp_send_json_error(['message' => 'No hay customer.']);
  }

  $country = sanitize_text_field($_POST['calc_shipping_country'] ?? 'AR');
  $state = sanitize_text_field($_POST['calc_shipping_state'] ?? '');
  $city = sanitize_text_field($_POST['calc_shipping_city'] ?? '');
  $postcode = sanitize_text_field($_POST['calc_shipping_postcode'] ?? '');
  $address_1 = sanitize_text_field($_POST['calc_shipping_address_1'] ?? '');
  $address_2 = sanitize_text_field($_POST['calc_shipping_address_2'] ?? '');

  // Validación mínima
  if (!$postcode || !$state || !$city) {
    wp_send_json_error(['message' => 'Faltan datos (provincia/ciudad/cp).']);
  }

  WC()->customer->set_shipping_address_1($address_1);
  WC()->customer->set_shipping_address_2($address_2);

  WC()->customer->set_shipping_country($country);
  WC()->customer->set_shipping_state($state);
  WC()->customer->set_shipping_city($city);
  WC()->customer->set_shipping_postcode($postcode);
  WC()->customer->save();

  // Al cambiar dirección, limpiamos selección anterior
  WC()->session->set('waves_micorreo_selected_rate', null);
  WC()->session->set('waves_micorreo_rates_cache', null);

  // Recalcular totales (todavía sin shipping seleccionado)
  if (WC()->cart) {
    WC()->cart->calculate_totals();
  }

  wp_send_json_success(['message' => 'Dirección guardada']);
}


/* ==========================================================
   WC-AJAX: 2) Pedir rates a MiCorreo (/rates)
========================================================== */

add_action('wc_ajax_waves_micorreo_rates', 'waves_micorreo_rates');
add_action('wc_ajax_nopriv_waves_micorreo_rates', 'waves_micorreo_rates');

function waves_micorreo_rates()
{
  if (!WC()->customer)
    wp_send_json_error(['message' => 'No hay customer.']);

  $dest_cp = WC()->customer->get_shipping_postcode();
  if (!$dest_cp)
    wp_send_json_error(['message' => 'No hay CP destino.']);

  $dims = waves_micorreo_cart_dimensions();

  $rates_out = [];
  $cache_map = [];

  // Vamos a pedir D y S por separado
  foreach (['D', 'S'] as $deliveredType) {

    $payload = [
      'customerId' => WAVES_MICORREO_CUSTOMER_ID,
      'postalCodeOrigin' => (string) WAVES_MICORREO_ORIGIN_CP,
      'postalCodeDestination' => (string) $dest_cp,
      'deliveredType' => $deliveredType, // D domicilio / S sucursal
      'dimensions' => $dims,
    ];

    $res = waves_micorreo_request('/rates', 'POST', $payload);

    if (is_wp_error($res)) {
      // si falla uno, seguimos con el otro
      continue;
    }

    $rates = $res['rates'] ?? [];
    if (!is_array($rates))
      continue;

    foreach ($rates as $r) {
      $ptype = sanitize_key($r['productType'] ?? 'xx');
      $pname = sanitize_text_field($r['productName'] ?? 'Envío');

      $price = isset($r['price']) ? (float) $r['price'] : 0.0;
      $min = isset($r['deliveryTimeMin']) ? (int) $r['deliveryTimeMin'] : null;
      $max = isset($r['deliveryTimeMax']) ? (int) $r['deliveryTimeMax'] : null;

      // id único (tiene que ser estable)
      $rate_id = 'waves_micorreo_' . $deliveredType . '_' . $ptype;

      // label lindo para UI
      $where = ($deliveredType === 'D') ? 'A domicilio' : 'Retiro en sucursal';
      $label = $where . ' — ' . $pname;

      $meta = '';
      if ($min !== null && $max !== null) {
        $meta = '<small style="opacity:.85">Entrega estimada: ' . esc_html($min) . '–' . esc_html($max) . ' días</small>';
      }

      $rates_out[] = [
        'id' => $rate_id,
        'label' => $label,
        'price_html' => wc_price($price),
        'meta_html' => $meta,
      ];

      $cache_map[$rate_id] = [
        'id' => $rate_id,
        'label' => $label,
        'cost' => $price,
        'deliveredType' => $deliveredType,
        'productType' => $ptype,
        'productName' => $pname,
        'deliveryMin' => $min,
        'deliveryMax' => $max,
      ];
    }
  }

  // Guardamos cache en sesión para poder seleccionar luego
  WC()->session->set('waves_micorreo_rates_cache', $cache_map);

  if (empty($rates_out)) {
    wp_send_json_error(['message' => 'Sin cotizaciones disponibles.']);
  }

  wp_send_json_success([
    'rates' => $rates_out
  ]);
}


/* ==========================================================
   WC-AJAX: 3) Seleccionar rate (guardar en sesión)
========================================================== */

add_action('wc_ajax_waves_micorreo_select_rate', 'waves_micorreo_select_rate');
add_action('wc_ajax_nopriv_waves_micorreo_select_rate', 'waves_micorreo_select_rate');

function waves_micorreo_select_rate()
{
  $nonce = $_POST['security'] ?? '';
  if (!wp_verify_nonce($nonce, 'update-shipping-method')) {
    wp_send_json_error(['message' => 'Nonce inválido.']);
  }

  $rate_id = sanitize_text_field($_POST['rate_id'] ?? '');
  if (!$rate_id)
    wp_send_json_error(['message' => 'Falta rate_id.']);

  $cache = WC()->session->get('waves_micorreo_rates_cache');
  if (!is_array($cache) || empty($cache[$rate_id])) {
    wp_send_json_error(['message' => 'Rate no encontrado en cache.']);
  }

  WC()->session->set('waves_micorreo_selected_rate', $rate_id);

  // Woo espera chosen_shipping_methods como array
  WC()->session->set('chosen_shipping_methods', [$rate_id]);

  // Recalcular totales para que el total cambie
  if (WC()->cart) {
    WC()->cart->calculate_shipping();
    WC()->cart->calculate_totals();
  }

  wp_send_json_success(['message' => 'Rate seleccionado']);
}


/* ==========================================================
   Integración Woo: inyectar el rate seleccionado en package_rates
   => Esto hace que WC()->cart->get_shipping_total() refleje el costo real
========================================================== */

add_filter('woocommerce_package_rates', function ($rates, $package) {

  if (is_admin() && !defined('DOING_AJAX'))
    return $rates;
  if (!WC()->session)
    return $rates;

  $selected = WC()->session->get('waves_micorreo_selected_rate');
  $cache = WC()->session->get('waves_micorreo_rates_cache');

  if (!$selected || !is_array($cache) || empty($cache[$selected])) {
    return $rates; // sin selección, no tocamos
  }

  $data = $cache[$selected];
  $cost = (float) ($data['cost'] ?? 0);

  $rate = new WC_Shipping_Rate(
    $selected,
    $data['label'] ?? 'Envío',
    $cost,
    [],             // taxes (Woo los calcula si aplica)
    'waves_micorreo' // method_id
  );

  // Devolvemos SOLO el seleccionado, así no aparecen duplicados
  return [$selected => $rate];

}, 50, 2);


/* ==========================================================
   Limpieza automática si cambia el carrito (opcional pero recomendado)
========================================================== */
add_action('woocommerce_cart_updated', function () {
  if (!WC()->session)
    return;
  WC()->session->set('waves_micorreo_selected_rate', null);
  WC()->session->set('waves_micorreo_rates_cache', null);
});

/* ==========================================================
   Aplicar / quitar cupón desde resumen-pedido*/
add_action('template_redirect', function () {

  if ( ! is_page('resumen-pedido') ) return;

  if ( empty($_POST['waves_coupon_action']) ) return;

  if ( ! WC()->cart ) return;

  $action = sanitize_text_field($_POST['waves_coupon_action']);
  $code   = wc_format_coupon_code( wp_unslash($_POST['coupon_code'] ?? '') );

  // Evitar re-POST al refrescar
  $redirect = remove_query_arg(['coupon_msg','coupon_type']);

  if ($action === 'apply') {

    if ( empty($code) ) {
      wp_safe_redirect( add_query_arg([
        'coupon_type' => 'error',
        'coupon_msg'  => 'Ingresá un código.',
      ], $redirect ) );
      exit;
    }

    $ok = WC()->cart->apply_coupon($code);
    WC()->cart->calculate_totals();

    if ($ok) {
      wp_safe_redirect( add_query_arg([
        'coupon_type' => 'success',
        'coupon_msg'  => 'Cupón aplicado.',
      ], $redirect ) );
      exit;
    }

    wp_safe_redirect( add_query_arg([
      'coupon_type' => 'error',
      'coupon_msg'  => 'Cupón inválido o no aplicable.',
    ], $redirect ) );
    exit;
  }

  if ($action === 'remove') {
    if ( ! empty($code) ) {
      WC()->cart->remove_coupon($code);
      WC()->cart->calculate_totals();
    }

    wp_safe_redirect( add_query_arg([
      'coupon_type' => 'success',
      'coupon_msg'  => 'Cupón quitado.',
    ], $redirect ) );
    exit;
  }

});

add_action('wp_ajax_waves_set_payment_method', 'waves_set_payment_method');
add_action('wp_ajax_nopriv_waves_set_payment_method', 'waves_set_payment_method');

function waves_set_payment_method() {
  if ( ! function_exists('WC') || ! WC()->session ) {
    wp_send_json_error(['message' => 'WooCommerce session no disponible.']);
  }

  $method = isset($_POST['method']) ? sanitize_text_field($_POST['method']) : '';
  if ( empty($method) ) {
    wp_send_json_error(['message' => 'Método inválido.']);
  }

  // Validar contra gateways disponibles
  WC()->payment_gateways();
  $available = WC()->payment_gateways->get_available_payment_gateways();

  if ( empty($available[$method]) ) {
    wp_send_json_error(['message' => 'Ese método no está disponible.']);
  }

  WC()->session->set('chosen_payment_method', $method);

  wp_send_json_success([
    'checkout_url' => wc_get_checkout_url(),
    'chosen'       => $method
  ]);
}

add_action('wp_enqueue_scripts', function () {

  // Ajustá esta condición a tu página/template si querés cargarlo solo ahí
  // if ( ! is_cart() ) return;

  $css = get_stylesheet_directory() . '/assets/css/waves-payments.css';
  $js  = get_stylesheet_directory() . '/assets/js/waves-payments.js';

  wp_enqueue_style(
    'waves-payments',
    get_stylesheet_directory_uri() . '/assets/css/waves-payments.css',
    [],
    file_exists($css) ? filemtime($css) : time()
  );

  wp_enqueue_script(
    'waves-payments',
    get_stylesheet_directory_uri() . '/assets/js/waves-payments.js',
    ['jquery'],
    file_exists($js) ? filemtime($js) : time(),
    true
  );

  wp_localize_script('waves-payments', 'WAVES_PAY', [
    'ajax_url' => admin_url('admin-ajax.php'),
  ]);
});


