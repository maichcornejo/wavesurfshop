<?php
if ( ! defined( 'ABSPATH' ) ) exit;

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


function waves_enqueue_woocommerce_styles() {

    // Solo cargar en páginas de WooCommerce: tienda, categorías, producto, búsqueda
    if ( function_exists('is_woocommerce') && is_woocommerce() || is_product_taxonomy() || is_product() ) {

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
function child_remove_parent_assets() {
    // wp_dequeue_style('animate-css');
    // wp_dequeue_script('wow-js');
}
add_action('wp_enqueue_scripts', 'child_remove_parent_assets', 20);



/* ============================================================
   CARGAR JS PARA FILTROS AJAX
============================================================ */
function waves_filters_scripts() {
    // Registrar script
    wp_register_script(
        'waves-filter-products',
        get_stylesheet_directory_uri() . '/assets/js/filter-products.js',
        array('jquery'),
        time(),
        true
    );

    // Localizar (pasar datos PHP → JS)
    wp_localize_script('waves-filter-products', 'waves_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('waves_filter_nonce'),
    ));

    // Cargar script
    wp_enqueue_script('waves-filter-products');
}
add_action('wp_enqueue_scripts', 'waves_filters_scripts');


/* ============================================================
   HANDLER AJAX: FILTRAR PRODUCTOS
============================================================ */
add_action('wp_ajax_filter_products', 'waves_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'waves_filter_products');

function waves_filter_products() {

    check_ajax_referer('waves_filter_nonce', 'nonce');

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
    );

    /* -------------------------
        FILTRO: GÉNERO
    --------------------------*/
    if (!empty($_POST['genders'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'pa_genero',
            'field'    => 'slug',
            'terms'    => $_POST['genders'],
        );
    }

    /* -------------------------
        FILTRO: MARCA
    --------------------------*/
    if (!empty($_POST['brands'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'pa_marca',
            'field'    => 'slug',
            'terms'    => $_POST['brands'],
        );
    }

    /* -------------------------
        FILTRO: TALLE
    --------------------------*/
    if (!empty($_POST['sizes'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'pa_talle',
            'field'    => 'slug',
            'terms'    => $_POST['sizes'],
        );
    }

    /* -------------------------
        FILTRO: PRECIO
    --------------------------*/
    if (!empty($_POST['price'])) {
        $args['meta_query'][] = array(
            'key'     => '_price',
            'value'   => intval($_POST['price']),
            'compare' => '<=',
            'type'    => 'NUMERIC',
        );
    }

    /* -------------------------
        CONSULTA
    --------------------------*/
    $query = new WP_Query($args);

    if ($query->have_posts()) :

        ob_start();
        woocommerce_product_loop_start();

        while ($query->have_posts()) : $query->the_post();
            wc_get_template_part('content', 'product');
        endwhile;

        woocommerce_product_loop_end();

        echo ob_get_clean();

    else :
        echo "<p>No se encontraron productos.</p>";
    endif;

    wp_die();
}

/* ============================================================
   CARGA ORDENADA DE CSS & JS DEL TEMA HIJO
============================================================ */
function waves_child_assets() {

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
        'nonce'    => wp_create_nonce('waves_filter_nonce'),
    ));

    wp_enqueue_script('waves-filter-products');
}
add_action('wp_enqueue_scripts', 'waves_child_assets');

function waves_cart_autoupdate_script() {
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


add_action('wp_enqueue_scripts', function() {
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
add_action('wp_enqueue_scripts', function() {
    wp_add_inline_style('shoes-store-elementor-style', '
        .header-image-box {
            display: none !important;
        }
    ');
}, 99);


/* aa*/
function waves_faq_styles() {
    wp_enqueue_style(
        'faq-styles',
        get_stylesheet_directory_uri() . '/assets/css/faq.css',
        array(),
        '1.0',
        'all'
    );
}
add_action( 'wp_enqueue_scripts', 'waves_faq_styles' );


function waves_enqueue_snap_scroll() {
    if ( is_front_page() ) {
        wp_enqueue_style(
            'waves-snap-scroll',
            get_stylesheet_directory_uri() . '/assets/css/snap-scroll.css',
            array(),
            time()
        );
    }
}
add_action('wp_enqueue_scripts', 'waves_enqueue_snap_scroll');

function waves_story_scroll_script() {

    if ( is_front_page() ) {
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


function waves_product_card_scripts() {

    if ( function_exists('is_shop') && ( is_shop() || is_front_page() ) ) {
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


add_action( 'wp_enqueue_scripts', function () {
  if ( is_product() ) {
    wp_enqueue_script(
      'waves-variations',
      get_stylesheet_directory_uri() . '/assets/js/single-product-variations.js',
      [ 'jquery', 'wc-add-to-cart-variation' ],
      null,
      true
    );
  }
});


add_action( 'wp_enqueue_scripts', function () {
    if ( is_product() ) {
        wp_enqueue_script( 'wc-add-to-cart-variation' );
    }
});


add_filter( 'woocommerce_account_menu_items', 'waves_remove_downloads_menu' );
function waves_remove_downloads_menu( $items ) {

    unset( $items['downloads'] );

    return $items;
}
add_filter( 'woocommerce_account_menu_items', 'waves_add_favorites_menu' );
function waves_add_favorites_menu( $items ) {

    $items['favorites'] = __('Mis favoritos');

    return $items;
}
add_action( 'init', 'waves_add_favorites_endpoint' );
function waves_add_favorites_endpoint() {
    add_rewrite_endpoint( 'favorites', EP_ROOT | EP_PAGES );
}
add_action( 'woocommerce_account_favorites_endpoint', 'waves_favorites_content' );
function waves_favorites_content() {
    echo '<h2>Mis favoritos</h2>';
    echo '<p>Acá vas a ver los productos guardados.</p>';
}
add_action( 'wp_enqueue_scripts', 'waves_enqueue_account_styles' );
function waves_enqueue_account_styles() {

    // Solo páginas de Mi Cuenta
    if ( is_account_page() ) {

        wp_enqueue_style(
            'waves-account-style',
            get_stylesheet_directory_uri() . '/assets/css/account.css',
            array(), // dependencias
            filemtime( get_stylesheet_directory() . '/assets/css/account.css' )
        );
    }
}
add_action( 'wp_enqueue_scripts', function () {

    if ( is_account_page() ) {
        wp_enqueue_style(
            'waves-account-orders',
            get_stylesheet_directory_uri() . '/assets/css/account-orders.css',
            [],
            filemtime( get_stylesheet_directory() . '/assets/css/account-orders.css' )
        );
    }

});


add_action('wp_ajax_toggle_favorite', 'toggle_favorite_product');
add_action('wp_ajax_nopriv_toggle_favorite', 'toggle_favorite_product');

function toggle_favorite_product() {

  if ( ! is_user_logged_in() ) {
    wp_send_json_error();
  }

  $user_id    = get_current_user_id();
  $product_id = intval($_POST['product_id']);

  $favorites = get_user_meta($user_id, 'wc_favorites', true);
  $favorites = is_array($favorites) ? $favorites : [];

  if ( in_array($product_id, $favorites) ) {
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

  if ( is_page('wishlist') ) {
    wp_redirect( home_url('/favoritos'), 301 );
    exit;
  }

});
