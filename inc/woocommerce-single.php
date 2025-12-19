<?php
defined('ABSPATH') || exit;

/**
 * SINGLE PRODUCT – WAVES
 */

/* =========================
   LAYOUT
========================= */

// Sin sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Reordenar summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 35 );

/* =========================
   CUOTAS
========================= */

add_action( 'woocommerce_single_product_summary', 'waves_installments_block', 15 );
function waves_installments_block() {
	echo '
	<div class="waves-installments">
		<strong>3 cuotas sin interés</strong>
		<span class="installment-price">$39.633</span>
		<div class="waves-installments-badge">6 cuotas sin interés con débito</div>
	</div>';
}

/* =========================
   ENVÍOS
========================= */

add_action( 'woocommerce_single_product_summary', 'waves_shipping_block', 25 );
function waves_shipping_block() {
	echo '
	<div class="waves-shipping">
		<p><strong>🚚 Te llega hoy</strong> <small>Exclusivo CABA y GBA</small></p>
		<p>📦 Llega entre <strong>jueves y miércoles</strong> · A todo el país</p>
	</div>';
}

/* =========================
   STOCK VISUAL
========================= */

add_action( 'woocommerce_single_product_summary', 'waves_stock_bar', 28 );
function waves_stock_bar() {
	global $product;

	if ( ! $product->managing_stock() ) return;

	$qty = $product->get_stock_quantity();
	if ( $qty <= 0 ) return;

	echo '
	<div class="waves-stock">
		<span>¡Quedan <strong>' . $qty . '</strong> en stock!</span>
		<div class="stock-bar">
			<div class="stock-fill" style="width:' . min(100, $qty * 20) . '%"></div>
		</div>
	</div>';
}
