<?php
/**
 * Template Name: Resumen del pedido (SIN ENVÍO)
 */
defined('ABSPATH') || exit;

get_header();

if (!function_exists('WC') || !WC()->cart || WC()->cart->is_empty()) {
  wp_safe_redirect(wc_get_cart_url());
  exit;
}
?>

<div class="container resumen-pedido">

  <h1>Resumen de tu pedido</h1>

  <!-- ======================
       PRODUCTOS + CUPONES
  ======================= -->
  <div class="resumen-box resumen-productos">

    <h3>Productos</h3>

    <?php foreach (WC()->cart->get_cart() as $item):
      $product = $item['data'] ?? null;
      if (!$product) continue;
      ?>
      <div class="resumen-item">
        <span><?php echo esc_html($product->get_name()); ?> × <?php echo (int) ($item['quantity'] ?? 1); ?></span>
        <strong><?php echo wc_price($item['line_subtotal'] ?? 0); ?></strong>
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
    ======================= -->
    <div class="waves-coupon-box">

      <div class="waves-coupon-head">
        <span>¿Tenés un código de promoción?</span>
      </div>

      <?php
      if (isset($_GET['coupon_msg'])) {
        $type = sanitize_text_field($_GET['coupon_type'] ?? 'success');
        $msg  = sanitize_text_field($_GET['coupon_msg']);
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
        <input type="text" name="coupon_code" class="waves-coupon-input" placeholder="Ingresá tu código" autocomplete="off">
        <button type="submit" class="waves-coupon-btn">Aplicar</button>
      </form>

    </div>

  </div>

  <!-- ======================
       TOTAL (SIN ENVÍO)
  ======================= -->
  <div class="resumen-box resumen-total">
    <span>Total</span>
    <strong><?php echo WC()->cart->get_total(); ?></strong>
    <small style="display:block;font-size:13px;opacity:.85">
      El envío se calcula en el checkout.
    </small>
  </div>

  <!-- ======================
       MÉTODO DE PAGO (dinámico)
  ======================= -->
  <div class="resumen-box resumen-pago">

    <h3>¿Cómo querés pagar?</h3>

    <?php
    WC()->payment_gateways(); // inicializa gateways
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $chosen   = WC()->session ? WC()->session->get('chosen_payment_method') : '';

    if (!empty($gateways)) : ?>
      <div class="waves-pay-grid" role="radiogroup" aria-label="Métodos de pago">
        <?php foreach ($gateways as $gateway_id => $gateway):

          $title = $gateway->get_title();
          $desc  = method_exists($gateway, 'get_description') ? $gateway->get_description() : '';
          $provider = !empty($gateway->method_title) ? $gateway->method_title : strtoupper($gateway_id);

          $badge = '';
          $tagline = '';
          $display_title = $title;
          $display_sub = '';

          $is_mp = (
            stripos($gateway_id, 'mercado') !== false ||
            stripos($gateway_id, 'mercadopago') !== false ||
            stripos($provider, 'Mercado Pago') !== false ||
            stripos($title, 'Mercado Pago') !== false
          );

          $mp_variant = '';
          if ($is_mp) {
            if (stripos($title, 'Efectivo') !== false) $mp_variant = 'Efectivo';
            elseif (stripos($title, 'Cuotas sin Tarjeta') !== false) $mp_variant = 'Cuotas sin tarjeta';
            elseif (stripos($title, 'Checkout Pro') !== false) $mp_variant = 'Checkout Pro';
            elseif (stripos($title, 'Checkout API') !== false) $mp_variant = 'Checkout API';
          }

          switch ($gateway_id) {
            case 'gocuotas':
              $display_title = 'GoCuotas';
              $badge   = 'Recomendado';
              $tagline = 'Pagá en cuotas con débito (sin interés)';
              break;

            case 'bacs':
              $display_title = 'Transferencia bancaria';
              $badge   = 'Sin recargo';
              $tagline = 'Transferencia bancaria (acreditación manual)';
              break;
          }

          if ($is_mp) {
            $display_title = 'Mercado Pago';
            $badge   = 'Oficial';
            $tagline = 'Pagá con tarjeta, débito o dinero en cuenta' . (!empty($mp_variant) ? ' • ' . $mp_variant : '');
          }

          if (!empty($tagline)) $display_sub = $tagline;
          elseif (!empty($desc)) $display_sub = wp_strip_all_tags($desc);

          $icon = method_exists($gateway, 'get_icon') ? $gateway->get_icon() : '';

          $fallback_icons = [
            'gocuotas'     => get_stylesheet_directory_uri() . '/assets/images/logos/go.png',
            'bacs'         => get_stylesheet_directory_uri() . '/assets/images/logos/transferencia.svg',
            'mercadopago'  => get_stylesheet_directory_uri() . '/assets/images/logos/mercadopago.png',
          ];

          if (empty(trim((string)$icon))) {
            if (isset($fallback_icons[$gateway_id])) {
              $icon = '<img src="' . esc_url($fallback_icons[$gateway_id]) . '" alt="' . esc_attr($provider) . '">';
            } elseif ($is_mp && isset($fallback_icons['mercadopago'])) {
              $icon = '<img src="' . esc_url($fallback_icons['mercadopago']) . '" alt="Mercado Pago">';
            }
          }

          $is_checked = ($chosen && $chosen === $gateway_id) ? 'checked' : '';
        ?>
          <label class="waves-pay-card <?php echo $is_checked ? 'is-selected' : ''; ?>">
            <input class="waves-pay-radio" type="radio" name="waves_payment_method"
              value="<?php echo esc_attr($gateway_id); ?>" <?php echo $is_checked; ?> />

            <div class="waves-pay-row">
              <div class="waves-pay-logo">
                <?php if (!empty($icon)) : ?>
                  <?php echo wp_kses_post($icon); ?>
                <?php else : ?>
                  <span class="waves-pay-logo-fallback">💳</span>
                <?php endif; ?>
              </div>

              <div class="waves-pay-main">
                <div class="waves-pay-name"><?php echo esc_html($display_title); ?></div>
                <?php if (!empty($display_sub)) : ?>
                  <div class="waves-pay-sub"><?php echo esc_html($display_sub); ?></div>
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

      <p class="waves-pay-hint">Elegí un método para continuar.</p>

    <?php else : ?>
      <p class="waves-pay-empty">No hay métodos de pago disponibles para este carrito.</p>
    <?php endif; ?>

  </div>

</div>

<?php get_footer(); ?>
