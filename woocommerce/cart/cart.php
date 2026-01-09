<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 10.1.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>
<div class="waves-cart-page">
  <div class="waves-cart-shell">

    <header class="waves-cart-top">
      <div class="waves-cart-heading">
        <span class="waves-cart-icon">🛒</span>
        <h1 class="waves-cart-title">Tu Carrito</h1>

        <span class="waves-cart-badge">
          <?php echo intval( WC()->cart->get_cart_contents_count() ); ?> Productos
        </span>
      </div>
    </header>

    <div class="waves-cart-card">

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove"><span class="screen-reader-text"><?php esc_html_e( 'Remove item', 'woocommerce' ); ?></span></th>
				<th class="product-thumbnail"><span class="screen-reader-text"><?php esc_html_e( 'Thumbnail image', 'woocommerce' ); ?></span></th>
				<th scope="col" class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th scope="col" class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
				<th scope="col" class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
				<th scope="col" class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
				/**
				 * Filter the product name.
				 *
				 * @since 2.1.0
				 * @param string $product_name Name of the product in the cart.
				 * @param array $cart_item The product in the cart.
				 * @param string $cart_item_key Key for the product in the cart.
				 */
				$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
					?>
					<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a role="button" href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										/* translators: %s is the product name */
										esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
						</td>
						<td class="product-thumbnail">
						<?php
						/**
						 * Filter the product thumbnail displayed in the WooCommerce cart.
						 *
						 * This filter allows developers to customize the HTML output of the product
						 * thumbnail. It passes the product image along with cart item data
						 * for potential modifications before being displayed in the cart.
						 *
						 * @param string $thumbnail     The HTML for the product image.
						 * @param array  $cart_item     The cart item data.
						 * @param string $cart_item_key Unique key for the cart item.
						 *
						 * @since 2.1.0
						 */
						$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

						if ( ! $product_permalink ) {
							echo $thumbnail; // PHPCS: XSS ok.
						} else {
							printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
						}
						?>
						</td>

						<td scope="row" role="rowheader" class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
						<?php
						if ( ! $product_permalink ) {
							echo wp_kses_post( $product_name . '&nbsp;' );
						} else {
							/**
							 * This filter is documented above.
							 *
							 * @since 2.1.0
							 */
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}

						do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

						// Meta data.
						echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

						// Mini descripción (Talle / Color) desde la variación del carrito



					

					

						if ($talle_name || $color_name) : ?>
						<?php endif; ?>


						<?php
						// Backorder notification.
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post(
								apply_filters(
									'woocommerce_cart_item_backorder_notification',
									'<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>',
									$product_id
								)
							);
						}

							// Backorder notification.
							if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
							}
							?>
							</td>

						<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
						<?php
						if ( $_product->is_sold_individually() ) {
							$min_quantity = 1;
							$max_quantity = 1;
						} else {
							$min_quantity = 0;
							$max_quantity = $_product->get_max_purchase_quantity();
						}

						$product_quantity = woocommerce_quantity_input(
							array(
								'input_name'   => "cart[{$cart_item_key}][qty]",
								'input_value'  => $cart_item['quantity'],
								'max_value'    => $max_quantity,
								'min_value'    => $min_quantity,
								'product_name' => $product_name,
							),
							$_product,
							false
						);

						echo '<div class="qty-wrapper" data-key="'.$cart_item_key.'">
								<button type="button" class="qty-btn qty-minus">−</button>
								' . preg_replace('/<div class="quantity[^>]*>|<\/div>/', '', $product_quantity) . '
								<button type="button" class="qty-btn qty-plus">+</button>
							</div>';

						?>
						</td>

						<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_contents' ); ?>

			<tr class="waves-cart-summary-row">
			<td colspan="1">
				<div class="waves-cart-summary-wrap">
				<div class="waves-cart-summary-card">

					<div class="waves-cart-summary-title">
					<span class="dot"></span>
					Resumen
					</div>

					<div class="waves-cart-summary-grid">
					<div class="item">
						<div class="label">Total productos</div>
						<div class="value"><?php wc_cart_totals_subtotal_html(); ?></div>
					</div>

					</div>

					<div class="waves-cart-summary-hint">
					El total puede variar según envío / cupones.
					</div>

				</div>
				</div>
			</td>
			</tr>


			<tr>
				<td colspan="6" class="actions">
					<div class="waves-cart-actions">

						<?php if ( wc_coupons_enabled() ) { ?>
						<div class="waves-coupon">
							<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>

							<input type="text"
								name="coupon_code"
								class="input-text"
								id="coupon_code"
								value=""
								placeholder="Código de cupón" />

							<button type="submit"
									class="button waves-btn waves-btn-primary"
									name="apply_coupon"
									value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
							Aplicar cupón
							</button>

							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
						<?php } ?>

						<div class="waves-cart-actions-right">
							<button type="submit"
									class="button waves-btn waves-btn-soft"
									name="update_cart"
									value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
							Actualizar carrito
							</button>

						<?php do_action( 'woocommerce_cart_actions' ); ?>

		
						</div>

					</div>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>

			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

    </div><!-- .waves-cart-card -->
  </div><!-- .waves-cart-shell -->
</div><!-- .waves-cart-page -->


<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>



<?php do_action( 'woocommerce_after_cart' ); ?>
