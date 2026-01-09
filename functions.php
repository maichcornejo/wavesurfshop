<?php
if (!defined('ABSPATH'))
  exit;

/* ==========================================================
   HELPERS
========================================================== */

function waves_asset_ver($rel_path, $fallback_time = true)
{
  $full = get_stylesheet_directory() . $rel_path;
  if (file_exists($full))
    return filemtime($full);
  return $fallback_time ? time() : null;
}

/* ==========================================================
   ASSETS (CSS/JS) – TODO ORDENADO Y SIN DUPLICADOS
========================================================== */

add_action('wp_enqueue_scripts', function () {

  /* =========================
     1) CSS PADRE
  ========================= */
  wp_enqueue_style(
    'parent-style',
    get_template_directory_uri() . '/style.css',
    [],
    null
  );

  /* =========================
     2) CSS GENERALES HIJO
  ========================= */
  wp_enqueue_style(
    'child-home',
    get_stylesheet_directory_uri() . '/assets/css/home.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/home.css')
  );

  wp_enqueue_style(
    'child-footer',
    get_stylesheet_directory_uri() . '/assets/css/footer.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/footer.css')
  );

  wp_enqueue_style(
    'child-header',
    get_stylesheet_directory_uri() . '/assets/css/header.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/header.css')
  );

  // Guía pedido (no depende del parent)
  wp_enqueue_style(
    'waves-order-guide',
    get_stylesheet_directory_uri() . '/assets/css/guia-pedido.css',
    [],
    waves_asset_ver('/assets/css/guia-pedido.css')
  );
  wp_enqueue_style(
    'child-woocommerce',
    get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/woocommerce.css')
  );

  wp_enqueue_style(
    'child-woocommerce-cards',
    get_stylesheet_directory_uri() . '/assets/css/woocommerce-cards.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/woocommerce-cards.css')
  );

  wp_enqueue_style(
    'child-favorito',
    get_stylesheet_directory_uri() . '/assets/css/favorito.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/favorito.css')
  );

  wp_enqueue_style(
    'child-favoritos',
    get_stylesheet_directory_uri() . '/assets/css/woocommerce-favoritos.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/woocommerce-favoritos.css')
  );
  wp_enqueue_style(
    'child-woocommerce-cart',
    get_stylesheet_directory_uri() . '/assets/css/woocommerce-cart.css',
    ['parent-style'],
    waves_asset_ver('/assets/css/woocommerce-cart.css')
  );
  /* =========================
     3) CSS WOOCOMMERCE (CONDICIONAL)
  ========================= */
  if ((function_exists('is_woocommerce') && is_woocommerce()) || is_product_taxonomy() || is_product()) {



    wp_enqueue_style(
      'child-woocommerce-shop',
      get_stylesheet_directory_uri() . '/assets/css/woocommerce-shop.css',
      ['parent-style'],
      waves_asset_ver('/assets/css/woocommerce-shop.css')
    );

    wp_enqueue_style(
      'child-woocommerce-singleproduct',
      get_stylesheet_directory_uri() . '/assets/css/woocommerce-singleproduct.css',
      ['parent-style'],
      waves_asset_ver('/assets/css/woocommerce-singleproduct.css')
    );

    wp_enqueue_style(
      'child-filtros',
      get_stylesheet_directory_uri() . '/assets/css/filtros.css',
      ['parent-style'],
      waves_asset_ver('/assets/css/filtros.css')
    );

  }

  /* =========================
     4) CSS EXTRA
  ========================= */
  wp_enqueue_style(
    'faq-styles',
    get_stylesheet_directory_uri() . '/assets/css/faq.css',
    [],
    waves_asset_ver('/assets/css/faq.css'),
    'all'
  );

   if (is_page('finalizar-comprar')) {

    wp_enqueue_style(
      'waves-checkout-css',
      get_stylesheet_directory_uri() . '/assets/css/woocoommerce-checkout.css',
      [],
      waves_asset_ver('/assets/css/woocoommerce-checkout.css')
    );
  }

  if (is_front_page()) {
    wp_enqueue_style(
      'waves-snap-scroll',
      get_stylesheet_directory_uri() . '/assets/css/snap-scroll.css',
      [],
      waves_asset_ver('/assets/css/snap-scroll.css')
    );
  }

  if (is_account_page()) {
    wp_enqueue_style(
      'waves-account-style',
      get_stylesheet_directory_uri() . '/assets/css/account.css',
      [],
      waves_asset_ver('/assets/css/account.css')
    );

    wp_enqueue_style(
      'waves-account-orders',
      get_stylesheet_directory_uri() . '/assets/css/account-orders.css',
      [],
      waves_asset_ver('/assets/css/account-orders.css')
    );

    wp_enqueue_style(
      'waves-account-addresses',
      get_stylesheet_directory_uri() . '/assets/css/account-addresses.css',
      [],
      waves_asset_ver('/assets/css/account-addresses.css')
    );
  }

  if (is_page('resumen-pedido')) {
    wp_enqueue_script('wc-cart');
    wp_enqueue_script('wc-cart-fragments');
    wp_enqueue_style(
      'waves-resumen-pedido',
      get_stylesheet_directory_uri() . '/assets/css/resumen-pedido.css',
      [],
      waves_asset_ver('/assets/css/resumen-pedido.css')
    );

  }

  if (is_page('seguimiento-envios')) {
    wp_enqueue_style(
      'waves-tracking-css',
      get_stylesheet_directory_uri() . '/assets/css/tracking.css',
      [],
      waves_asset_ver('/assets/css/tracking.css')
    );
  }

  // Auth pages
  if (
    is_page_template('page-login.php') ||
    is_page_template('page-register.php') ||
    is_page_template('page-lost-password.php')
  ) {
    wp_enqueue_style(
      'waves-auth',
      get_stylesheet_directory_uri() . '/assets/css/auth.css',
      [],
      waves_asset_ver('/assets/css/auth.css')
    );
  }
 
  /* =========================
     5) GOOGLE FONT
  ========================= */
  wp_enqueue_style(
    'waves-google-fonts',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
    [],
    null
  );

  /* =========================
     6) JS
  ========================= */

  // Carrusel marcas
  wp_enqueue_script(
    'waves-brand',
    get_stylesheet_directory_uri() . '/assets/js/brand.js',
    ['jquery'],
    waves_asset_ver('/assets/js/brand.js'),
    true
  );

  // Story scroll home
  if (is_front_page()) {
    wp_enqueue_script(
      'waves-story-scroll',
      get_stylesheet_directory_uri() . '/assets/js/story-scroll.js',
      [],
      waves_asset_ver('/assets/js/story-scroll.js'),
      true
    );
  }

  // Product cards (solo donde corresponde)  ✅ (antes estaba duplicado)
  if (function_exists('is_shop') && (is_shop() || is_front_page())) {
    wp_enqueue_script(
      'waves-product-card',
      get_stylesheet_directory_uri() . '/assets/js/product-card.js',
      [],
      waves_asset_ver('/assets/js/product-card.js'),
      true
    );
  }

  // Variations (favoritos / etc)  (mantengo el handle raro para no romper)
  wp_enqueue_script(
    'waves-favorites',
    get_stylesheet_directory_uri() . '/assets/js/single-product-variations.js',
    ['jquery'],
    waves_asset_ver('/assets/js/single-product-variations.js'),
    true
  );

  // Woo variation add to cart solo en producto
  if (is_product()) {
    wp_enqueue_script('wc-add-to-cart-variation');
  }

  // Tracking JS + localize
  if (is_page('seguimiento-envios')) {
    wp_enqueue_script(
      'waves-tracking-js',
      get_stylesheet_directory_uri() . '/assets/js/tracking.js',
      ['jquery'],
      waves_asset_ver('/assets/js/tracking.js'),
      true
    );

    wp_localize_script('waves-tracking-js', 'wavesTracking', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('waves_tracking_nonce'),
    ]);
  }

  // Filtros AJAX (siempre, como lo tenías)
  wp_enqueue_script(
    'waves-filter-products',
    get_stylesheet_directory_uri() . '/assets/js/filter-products.js',
    ['jquery'],
    waves_asset_ver('/assets/js/filter-products.js'),
    true
  );

  wp_localize_script('waves-filter-products', 'waves_ajax', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('waves_filter_nonce'),
  ]);

  // Carrito: scripts + localize (unificado)
  if (is_cart()) {
    wp_enqueue_script(
      'cart-ajax-custom',
      get_stylesheet_directory_uri() . '/assets/js/cart-ajax.js',
      ['jquery', 'wc-cart'],
      waves_asset_ver('/assets/js/cart-ajax.js'),
      true
    );

    wp_enqueue_script(
      'cart-autoupdate',
      get_stylesheet_directory_uri() . '/assets/js/cart-autoupdate.js',
      ['jquery', 'wc-cart'],
      waves_asset_ver('/assets/js/cart-autoupdate.js'),
      true
    );

    // Localize de Correo Argentino (antes estaba en otro hook separado)
    wp_localize_script('cart-autoupdate', 'wavesCart', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('waves_recalc_shipping'),
    ]);
  }

  // Resumen-pedido: scripts + localize
  if (is_page('resumen-pedido')) {

    wp_enqueue_script(
      'waves-resumen-pago',
      get_stylesheet_directory_uri() . '/assets/js/resumen-pago.js',
      ['jquery'],
      waves_asset_ver('/assets/js/resumen-pago.js'),
      true
    );
  }

  // Payments assets (lo dejé global porque lo tenías global)
  $css = get_stylesheet_directory() . '/assets/css/waves-payments.css';
  $js = get_stylesheet_directory() . '/assets/js/waves-payments.js';

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

  // WC Carousel
  wp_enqueue_script(
    'waves-wc-carousel',
    get_stylesheet_directory_uri() . '/assets/js/wc-carousel.js',
    [],
    waves_asset_ver('/assets/js/wc-carousel.js'),
    true
  );

}, 20);


/* ==========================================================
   DESACTIVAR ESTILOS/SCRIPTS DEL PADRE (OPCIONAL)
========================================================== */
add_action('wp_enqueue_scripts', function () {
  // wp_dequeue_style('animate-css');
  // wp_dequeue_script('wow-js');
}, 20);

/* ==========================================================
   OCULTAR BANNER DEL THEME PADRE (INLINE)
========================================================== */
add_action('wp_enqueue_scripts', function () {
  wp_add_inline_style('shoes-store-elementor-style', '
    .header-image-box{ display:none !important; }
  ');
}, 99);

/* ==========================================================
   AJAX FILTROS PRODUCTOS
========================================================== */
add_action('wp_ajax_filter_products', 'waves_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'waves_filter_products');

function waves_filter_products()
{

  check_ajax_referer('waves_filter_nonce', 'nonce');

  $filters = $_POST['filters'] ?? [];
  $price = isset($_POST['price']) ? intval($_POST['price']) : 0;
  $page = max(1, intval($_POST['page'] ?? 1));

  $tax_query = ['relation' => 'AND'];

  foreach ($filters as $taxonomy => $terms) {
    if (empty($terms))
      continue;

    $tax_query[] = [
      'taxonomy' => sanitize_text_field($taxonomy),
      'field' => 'slug',
      'terms' => array_map('sanitize_text_field', $terms),
      'operator' => 'IN',
    ];
  }

  $meta_query = ['relation' => 'AND'];

  if ($price > 0) {
    $meta_query[] = [
      'relation' => 'OR',
      [
        'key' => '_price',
        'value' => $price,
        'compare' => '<=',
        'type' => 'NUMERIC',
      ],
      [
        'key' => '_min_variation_price',
        'value' => $price,
        'compare' => '<=',
        'type' => 'NUMERIC',
      ],
    ];
  }

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

    $total_pages = (int) $query->max_num_pages;

    if ($total_pages > 1) {
      $big = 999999999;

      echo '<nav class="woocommerce-pagination">';
      echo paginate_links([
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '',
        'current' => $page,
        'total' => $total_pages,
        'prev_text' => '←',
        'next_text' => '→',
        'type' => 'list',
      ]);
      echo '</nav>';
    }

  } else {
    echo '<p class="no-results">No se encontraron productos.</p>';
  }

  wp_reset_postdata();
  wp_die();
}

/* ==========================================================
   MI CUENTA: MENÚ + ENDPOINT + REDIRECT
========================================================== */

add_filter('woocommerce_account_menu_items', function ($items) {
  unset($items['downloads']);
  return $items;
});

add_filter('woocommerce_account_menu_items', function ($items) {
  $items['favorites'] = __('Mis favoritos');
  return $items;
});

add_action('init', function () {
  add_rewrite_endpoint('favorites', EP_ROOT | EP_PAGES);
});

add_action('woocommerce_account_favorites_endpoint', function () {
  echo '<h2>Mis favoritos</h2>';
  echo '<p>Acá vas a ver los productos guardados.</p>';
});

add_action('template_redirect', function () {
  if (is_account_page() && !is_user_logged_in()) {
    wp_safe_redirect(home_url('/login'));
    exit;
  }
});

/* ==========================================================
   FAVORITOS: TOGGLE / REMOVE + REDIRECT LEGACY
========================================================== */

add_action('wp_ajax_toggle_favorite', 'toggle_favorite_product');
add_action('wp_ajax_nopriv_toggle_favorite', 'toggle_favorite_product');

function toggle_favorite_product()
{

  if (!is_user_logged_in())
    wp_send_json_error();

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

  wp_send_json_success(['is_favorite' => $is_favorite]);
}

add_action('wp_ajax_remove_favorite', 'remove_favorite');
function remove_favorite()
{

  if (!is_user_logged_in())
    wp_send_json_error();

  $user_id = get_current_user_id();
  $product_id = intval($_POST['product_id']);

  $favorites = get_user_meta($user_id, 'wc_favorites', true);
  $favorites = is_array($favorites) ? $favorites : [];

  $favorites = array_diff($favorites, [$product_id]);
  update_user_meta($user_id, 'wc_favorites', array_values($favorites));

  wp_send_json_success();
}

add_action('template_redirect', function () {
  if (is_page('wishlist')) {
    wp_redirect(home_url('/favoritos'), 301);
    exit;
  }
});

/* ==========================================================
   TRACKING (AJAX)
========================================================== */

add_action('wp_ajax_waves_track_shipment', 'waves_track_shipment');
add_action('wp_ajax_nopriv_waves_track_shipment', 'waves_track_shipment');

function waves_track_shipment()
{

  check_ajax_referer('waves_tracking_nonce', 'nonce');

  $tracking_code = sanitize_text_field($_POST['code'] ?? '');
  if (empty($tracking_code)) {
    wp_send_json_error(['message' => 'Código inválido']);
  }

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

  wp_send_json_success([
    'type' => 'correo_argentino',
    'url' => 'https://www.correoargentino.com.ar/formularios/e-commerce?id=' . urlencode($tracking_code),
  ]);
}

/* ==========================================================
   OCULTAR AVISO ZONA ENVÍO (cartel verde)
========================================================== */
add_filter('woocommerce_shipping_zone_method_added_notice', '__return_false');
add_filter('woocommerce_shipping_zone_method_updated_notice', '__return_false');
add_filter('woocommerce_shipping_zone_method_deleted_notice', '__return_false');

/* ==========================================================
   STOCK (helper)
========================================================== */
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

      if ($qty === 0 || $qty === null) {
        return 10; // stock medio ficticio
      }

      $available += $qty;
    }
  }

  return $available;
}

/* ==========================================================
   LOGIN FORM CUSTOM EN MI CUENTA (shortcode)
========================================================== */
add_action('woocommerce_before_customer_login_form', function () {
  if (is_user_logged_in())
    return;

  echo '<div class="waves-auth-wrapper">';
  echo do_shortcode('[user_registration_login]');
  echo '</div>';
}, 1);

/* ==========================================================
   BOTÓN "CONTINUAR" EN CARRITO
========================================================== */
add_action('woocommerce_cart_actions', function () {
  if (WC()->cart && !WC()->cart->is_empty()) {
    echo '<a href="' . esc_url(site_url('/finalizar-comprar')) . '" class="button alt waves-btn-continuar">Continuar</a>';
  }
});


/* ==========================================================
   CUPONES (resumen-pedido) – apply/remove + redirect
========================================================== */
add_action('template_redirect', function () {

  if (!is_page('resumen-pedido'))
    return;
  if (empty($_POST['waves_coupon_action']))
    return;
  if (!WC()->cart)
    return;

  $action = sanitize_text_field($_POST['waves_coupon_action']);
  $code = wc_format_coupon_code(wp_unslash($_POST['coupon_code'] ?? ''));

  $redirect = remove_query_arg(['coupon_msg', 'coupon_type']);

  if ($action === 'apply') {

    if (empty($code)) {
      wp_safe_redirect(add_query_arg([
        'coupon_type' => 'error',
        'coupon_msg' => 'Ingresá un código.',
      ], $redirect));
      exit;
    }

    $ok = WC()->cart->apply_coupon($code);
    WC()->cart->calculate_totals();

    if ($ok) {
      wp_safe_redirect(add_query_arg([
        'coupon_type' => 'success',
        'coupon_msg' => 'Cupón aplicado.',
      ], $redirect));
      exit;
    }

    wp_safe_redirect(add_query_arg([
      'coupon_type' => 'error',
      'coupon_msg' => 'Cupón inválido o no aplicable.',
    ], $redirect));
    exit;
  }

  if ($action === 'remove') {
    if (!empty($code)) {
      WC()->cart->remove_coupon($code);
      WC()->cart->calculate_totals();
    }

    wp_safe_redirect(add_query_arg([
      'coupon_type' => 'success',
      'coupon_msg' => 'Cupón quitado.',
    ], $redirect));
    exit;
  }
});

/* ==========================================================
   PAYMENT METHOD (AJAX)
========================================================== */

add_action('wp_ajax_waves_set_payment_method', 'waves_set_payment_method');
add_action('wp_ajax_nopriv_waves_set_payment_method', 'waves_set_payment_method');

function waves_set_payment_method()
{

  if (!function_exists('WC') || !WC()->session) {
    wp_send_json_error(['message' => 'WooCommerce session no disponible.']);
  }

  $method = isset($_POST['method']) ? sanitize_text_field($_POST['method']) : '';
  if (empty($method)) {
    wp_send_json_error(['message' => 'Método inválido.']);
  }

  WC()->payment_gateways();
  $available = WC()->payment_gateways->get_available_payment_gateways();

  if (empty($available[$method])) {
    wp_send_json_error(['message' => 'Ese método no está disponible.']);
  }

  WC()->session->set('chosen_payment_method', $method);

  wp_send_json_success([
    'checkout_url' => wc_get_checkout_url(),
    'chosen' => $method,
  ]);
}

/* ==========================================================
   FRAGMENTS (TOTAL + REVIEW ORDER)
========================================================== */
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {

  ob_start(); ?>
  <div class="resumen-box resumen-total">
    <span>Total</span>
    <strong><?php echo WC()->cart ? WC()->cart->get_total() : ''; ?></strong>
    <small style="display:block;font-size:13px;opacity:.85">
      El envío se calcula en el checkout.
    </small>
  </div>
  <?php
  $fragments['.resumen-total'] = ob_get_clean();

  return $fragments;
});
