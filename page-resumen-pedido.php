<?php
/**
 * Template Name: Resumen del pedido
 */
defined('ABSPATH') || exit;

get_header();

if (WC()->cart->is_empty()) {
  wp_safe_redirect(wc_get_cart_url());
  exit;
}

$customer = WC()->customer;

// Defaults desde la sesión / customer
$country = $customer ? ($customer->get_shipping_country() ?: 'AR') : 'AR';
$state = $customer ? $customer->get_shipping_state() : '';
$city = $customer ? $customer->get_shipping_city() : '';
$postcode = $customer ? $customer->get_shipping_postcode() : '';

// Provincias AR (Woo)
$states = WC()->countries->get_states($country);
?>

<div class="container resumen-pedido">

  <h1>Resumen de tu pedido</h1>

  <!-- ======================
       PRODUCTOS + CUPONES
  ======================= -->
  <div class="resumen-box">

    <h3>Productos</h3>

    <?php foreach (WC()->cart->get_cart() as $item):
      $product = $item['data'];
      ?>
      <div class="resumen-item">
        <span><?php echo esc_html($product->get_name()); ?> × <?php echo (int) $item['quantity']; ?></span>
        <strong><?php echo wc_price($item['line_subtotal']); ?></strong>
      </div>
    <?php endforeach; ?>

    <?php if (WC()->cart->get_coupons()): ?>
      <div class="resumen-cupones">
        <?php foreach (WC()->cart->get_coupons() as $coupon): ?>
          <div class="resumen-cupon">
            Cupón <strong><?php echo esc_html($coupon->get_code()); ?></strong>
            − <?php echo wc_price(WC()->cart->get_coupon_discount_amount($coupon->get_code())); ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="resumen-subtotal">
      Subtotal
      <strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
    </div>
    <!-- ======================
     CUPÓN
====================== -->
    <div class="waves-coupon-box">

      <div class="waves-coupon-head">
        <span>¿Tenés un código de promoción?</span>
      </div>

      <?php
      // Mensajes (si redirigimos con query args)
      if (isset($_GET['coupon_msg'])) {
        $type = sanitize_text_field($_GET['coupon_type'] ?? 'success');
        $msg = sanitize_text_field($_GET['coupon_msg']);
        echo '<div class="waves-coupon-alert is-' . esc_attr($type) . '">' . esc_html($msg) . '</div>';
      }
      ?>

      <?php if (WC()->cart->get_applied_coupons()): ?>
        <div class="waves-coupon-applied">
          <span>Aplicados:</span>

          <div class="waves-coupon-tags">
            <?php foreach (WC()->cart->get_applied_coupons() as $code): ?>
              <form method="post" class="waves-coupon-tag">
                <input type="hidden" name="waves_coupon_action" value="remove">
                <input type="hidden" name="coupon_code" value="<?php echo esc_attr($code); ?>">
                <button type="submit" class="waves-coupon-remove" title="Quitar">
                  <?php echo esc_html($code); ?> <b>×</b>
                </button>
              </form>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <form method="post" class="waves-coupon-form">
        <input type="hidden" name="waves_coupon_action" value="apply">

        <input type="text" name="coupon_code" class="waves-coupon-input" placeholder="Ingresá tu código"
          autocomplete="off">

        <button type="submit" class="waves-coupon-btn">
          Aplicar
        </button>
      </form>

    </div>

  </div>

  <!-- ======================
       ENVÍO
  ======================= -->
<div class="resumen-box resumen-envio">

  <h3>Entrega</h3>

  <div class="waves-shipping-picker">
    <button type="button" class="waves-shipcard" data-type="retiro">
      <span class="waves-ship-ico">🏬</span>
      <span class="waves-ship-txt">
        <strong>Retiro</strong>
        <small>(por una sucursal)</small>
      </span>
      <span class="waves-ship-badge waves-ok">Disponible</span>
    </button>

    <button type="button" class="waves-shipcard is-active" data-type="domicilio">
      <span class="waves-ship-ico">🚚</span>
      <span class="waves-ship-txt">
        <strong>Envío</strong>
        <small>(a domicilio)</small>
      </span>
      <span class="waves-ship-badge">Opción seleccionada</span>
    </button>
  </div>

  <!-- FORM DINÁMICO (según tipo) -->
  <div class="waves-shipform" id="waves-shipform">

    <div class="waves-shipform-alert" aria-live="polite"></div>

    <!-- ===== RETIRO ===== -->
    <div class="waves-shipform-panel" data-type="retiro">
      <h4>Datos para retiro</h4>

      <div class="waves-grid-2">
        <div class="waves-field">
          <label>Nombre</label>
          <input type="text" data-field="first_name" required>
        </div>
        <div class="waves-field">
          <label>Apellido</label>
          <input type="text" data-field="last_name" required>
        </div>
      </div>

      <div class="waves-grid-2">
        <div class="waves-field">
          <label>DNI</label>
          <input type="text" data-field="dni" required>
        </div>
        <div class="waves-field">
          <label>Teléfono</label>
          <input type="tel" data-field="phone" required>
        </div>
      </div>

      <div class="waves-field">
        <label>Provincia</label>
        <select data-field="state" required>
          <option value="">Seleccioná una provincia</option>
          <?php foreach (WC()->countries->get_states('AR') as $code => $label): ?>
            <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($label); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="waves-grid-2">
        <div class="waves-field">
          <label>Ciudad</label>
          <input type="text" data-field="city" required>
        </div>
        <div class="waves-field">
          <label>Código postal</label>
          <input type="text" data-field="postcode" required>
        </div>
      </div>

      <button type="button" class="waves-ship-apply" data-apply="retiro">
        Guardar y cotizar
      </button>
    </div>

    <!-- ===== DOMICILIO ===== -->
    <div class="waves-shipform-panel" data-type="domicilio">
      <h4>Datos para envío a domicilio</h4>

      <div class="waves-grid-2">
        <div class="waves-field">
          <label>Nombre</label>
          <input type="text" data-field="first_name" required>
        </div>
        <div class="waves-field">
          <label>Apellido</label>
          <input type="text" data-field="last_name" required>
        </div>
      </div>

      <div class="waves-grid-2">
        <div class="waves-field">
          <label>DNI</label>
          <input type="text" data-field="dni" required>
        </div>
        <div class="waves-field">
          <label>Teléfono</label>
          <input type="tel" data-field="phone" required>
        </div>
      </div>

      <div class="waves-field">
        <label>Provincia</label>
        <select data-field="state" required>
          <option value="">Seleccioná una provincia</option>
          <?php foreach (WC()->countries->get_states('AR') as $code => $label): ?>
            <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($label); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="waves-grid-2">
        <div class="waves-field">
          <label>Ciudad</label>
          <input type="text" data-field="city" required>
        </div>
        <div class="waves-field">
          <label>Código postal</label>
          <input type="text" data-field="postcode" required>
        </div>
      </div>

      <div class="waves-field">
        <label>Dirección (calle y número)</label>
        <input type="text" data-field="address_1" required>
      </div>

      <div class="waves-field">
        <label>Piso / Depto (opcional)</label>
        <input type="text" data-field="address_2">
      </div>

      <button type="button" class="waves-ship-apply" data-apply="domicilio">
        Guardar y cotizar
      </button>
    </div>

  </div>

  <!-- Mensaje + rates -->
  <div class="resumen-metodos-envio">
    <div class="waves-methods-message">
      <small style="opacity:.85">Todavía no calculaste el envío.</small>
    </div>
    <div class="waves-rates"></div>
  </div>

  <!-- FORM OCULTO (lo usa el JS para guardar dirección) -->
  <form class="woocommerce-shipping-calculator waves-hidden-form" method="post" style="display:none;">
    <input type="hidden" name="calc_shipping_country" value="AR">
    <input type="hidden" name="calc_shipping_state" value="">
    <input type="hidden" name="calc_shipping_city" value="">
    <input type="hidden" name="calc_shipping_postcode" value="">
    <input type="hidden" name="calc_shipping_address_1" value="">
    <input type="hidden" name="calc_shipping_address_2" value="">
    <input type="hidden" name="waves_ship_type" value="domicilio">
    <button type="submit">submit</button>
  </form>

</div>

  <!-- ======================
       TOTAL
  ======================= -->
  <div class="resumen-box resumen-total">

    <?php if (WC()->cart->get_shipping_total() > 0): ?>
      <div class="resumen-total-inner">
        <span>Total</span>
        <strong><?php echo WC()->cart->get_total(); ?></strong>
      </div>

      <div class="resumen-total-loader">
        <span class="spinner"></span>
        <small>Calculando total…</small>
      </div>

    <?php else: ?>

      <span>Total</span>
      <strong><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
      <small style="display:block;font-size:13px;opacity:.85">
        Calculá el envío para ver el total final
      </small>

    <?php endif; ?>

  </div>

<!-- ======================
     MÉTODO DE PAGO (dinámico)
======================= -->
  <div class="resumen-box resumen-pago">

    <h3>¿Cómo querés pagar?</h3>

    <?php
    if ( function_exists('WC') && WC()->cart ) {

      // Asegura que Woo inicialice gateways
      WC()->payment_gateways();

      $gateways = WC()->payment_gateways->get_available_payment_gateways();
      $chosen   = WC()->session ? WC()->session->get('chosen_payment_method') : '';

      if ( ! empty($gateways) ) : ?>
        <div class="waves-pay-grid" role="radiogroup" aria-label="Métodos de pago">
          <?php foreach ( $gateways as $gateway_id => $gateway ) :

            // title / icon / description (según lo que provea el plugin)
            $title = $gateway->get_title();
            $desc  = method_exists($gateway, 'get_description') ? $gateway->get_description() : '';

            // Nombre “de marca / pasarela” (admin). Si no existe, usa el ID.
            $provider = !empty($gateway->method_title) ? $gateway->method_title : strtoupper($gateway_id);
            // Badge / tagline "con onda" por gateway (fallback)
            $badge   = '';
            $tagline = '';
            // 1) Título corto (lo que se ve grande)
            $display_title = $title;

            // 2) Subtítulo (lo que se ve abajo)
            $display_sub = '';

            // Defaults: si no hay tagline, usar descripción
            if (!empty($tagline)) {
              $display_sub = $tagline;
            } elseif (!empty($desc)) {
              $display_sub = wp_strip_all_tags($desc);
            }

            // Ajustes por gateway (para que NO repita)
            switch ($gateway_id) {
              case 'gocuotas':
                $display_title = 'GoCuotas'; // corto y claro
                // si el plugin ya trae un título largo, lo usamos como sub si no hay tagline
                if (empty($display_sub)) {
                  $display_sub = 'Pagá en cuotas con débito sin interés';
                }
                break;

              case 'bacs':
                $display_title = 'Transferencia bancaria';
                if (empty($display_sub)) {
                  $display_sub = 'Acreditación manual (puede demorar).';
                }
                break;
            }


            switch ($gateway_id) {
              case 'gocuotas':
                $badge   = 'Recomendado';
                $tagline = 'Pagá en cuotas con débito (sin interés)';
                break;

              case 'bacs':
                $badge   = 'Sin recargo';
                $tagline = 'Transferencia bancaria (acreditación manual)';
                break;
            }

            // Icono del gateway (si el plugin lo provee)
            $icon = method_exists($gateway, 'get_icon') ? $gateway->get_icon() : '';

            // Fallback: iconos tuyos si el plugin no devuelve nada
            $fallback_icons = [
              'gocuotas' => get_stylesheet_directory_uri() . '/assets/images/logos/go.png',
              'bacs'     => get_stylesheet_directory_uri() . '/assets/images/logos/transferencia.svg',
            ];

            if ( empty(trim((string)$icon)) && isset($fallback_icons[$gateway_id]) ) {
              $icon = '<img src="' . esc_url($fallback_icons[$gateway_id]) . '" alt="' . esc_attr($provider) . '">';
            }


            $is_checked = ($chosen && $chosen === $gateway_id) ? 'checked' : '';
            ?>
                      <label class="waves-pay-card <?php echo $is_checked ? 'is-selected' : ''; ?>">
              <input
                class="waves-pay-radio"
                type="radio"
                name="waves_payment_method"
                value="<?php echo esc_attr($gateway_id); ?>"
                <?php echo $is_checked; ?>
              />

              <div class="waves-pay-row">
                <div class="waves-pay-logo">
                  <?php if (!empty($icon)) : ?>
                    <?php echo wp_kses_post($icon); ?>
                  <?php else : ?>
                    <span class="waves-pay-logo-fallback">💳</span>
                  <?php endif; ?>
                </div>

                <div class="waves-pay-main">
                  <div class="waves-pay-name">
                    <?php echo esc_html($display_title); ?>
                  </div>

                  <?php if (!empty($display_sub)) : ?>
                    <div class="waves-pay-sub">
                      <?php echo esc_html($display_sub); ?>
                    </div>
                  <?php endif; ?>

                </div>

                <div class="waves-pay-right">
                  <?php if (!empty($badge)) : ?>
                    <span class="waves-pay-badge"><?php echo esc_html($badge); ?></span>
                  <?php endif; ?>

                  <span class="waves-pay-check" aria-hidden="true"></span>
                </div>
              </div>
            </label>

          <?php endforeach; ?>
        </div>

        <button type="button" class="waves-pay-continue" disabled>
          Continuar al checkout
        </button>

        <p class="waves-pay-hint">
          Elegí un método para continuar.
        </p>

      <?php else : ?>
        <p class="waves-pay-empty">No hay métodos de pago disponibles para este carrito.</p>
      <?php endif;

    } else {
      echo '<p class="waves-pay-empty">WooCommerce no está listo en esta pantalla.</p>';
    }
    ?>

  </div>


</div>

<?php
// ⚠️ Si ya estás usando wp_localize_script('waves-resumen-envio', 'waves_wc', ...)
// BORRÁ este bloque de abajo para no duplicar.
// Si NO estás usando wp_localize_script, dejalo.
?>
<script>
  window.waves_wc = {
    ajax_url: "<?php echo esc_url(WC_AJAX::get_endpoint('%%endpoint%%')); ?>",
    nonce: "<?php echo wp_create_nonce('update-shipping-method'); ?>"
  };
</script>

<?php get_footer(); ?>