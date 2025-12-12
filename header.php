<?php
/**
 * Custom Header for Waves Surf Shop (Child Theme) 
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 🔵 TOP BAR (opcional) -->
<div class="waves-topbar">
    <div class="container">
        <p class="topbar-text">Envíos a todo el país · Surf & Streetwear</p>
    </div>
</div>

<header id="site-header" class="waves-header">
    <div class="container header-inner">

        <!-- LOGO -->
        <div class="header-left">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logos/logo-sinfondo.png" 
                    alt="Waves Surf Shop" class="site-logo">
            </a>
        </div>

        <!-- MENÚ -->
        <nav class="header-menu">
            <?php
                wp_nav_menu([
                    'theme_location' => 'main-menu',
                    'container' => false,
                    'menu_class' => 'nav'
                ]);
            ?>
        </nav>

        <!-- ACCIONES (search + cuenta + carrito) -->
        <div class="header-actions">

            <!-- BUSCADOR -->
            <div class="header-search">
                <?php if ( class_exists('woocommerce') ) {
                    get_product_search_form();
                } ?>
            </div>

            <!-- MI CUENTA -->
            <div class="header-account">
                <a href="<?php echo wc_get_page_permalink('myaccount'); ?>">
                    <i class="fas fa-user"></i>
                </a>
            </div>

            <!-- CARRITO -->
            <div class="header-cart">
                <a href="<?php echo wc_get_cart_url(); ?>">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count">
                        <?php echo WC()->cart->get_cart_contents_count(); ?>
                    </span>
                </a>
            </div>

        </div>
    </div>
</header>

<div id="content" class="site-content">
