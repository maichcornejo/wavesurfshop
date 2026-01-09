(function ($) {
  function hideAnnoyingZoneNotice() {
    $('.woocommerce-notices-wrapper .woocommerce-info').each(function () {
      const t = $(this).text().trim().toLowerCase();
      if (t.includes('zona de coincidencia')) $(this).remove();
    });
  }

  function applyBillingSyncUI() {
    const $cb = $('#waves_billing_from_shipping');
    if (!$cb.length) return;

    const enabled = $cb.is(':checked');
    $('body').toggleClass('waves-billing-same', enabled);
  }

  function keepShipToDifferentUnchecked() {
  const $ship = $('#ship-to-different-address-checkbox');
  if (!$ship.length) return;

  if (window.__wavesShipDiff === undefined) {
    window.__wavesShipDiff = $ship.is(':checked');
  }

  $ship.off('change.waves').on('change.waves', function () {
    window.__wavesShipDiff = $(this).is(':checked');
  });

  // ✅ no dispares change acá (evita loop)
  if (window.__wavesShipDiff === false) {
    $ship.prop('checked', false);
  }
}


  $(document).ready(function () {
    hideAnnoyingZoneNotice();
    applyBillingSyncUI();
    keepShipToDifferentUnchecked();

    $(document.body).on('updated_checkout', function () {
      hideAnnoyingZoneNotice();
      applyBillingSyncUI();
      keepShipToDifferentUnchecked();
    });

    $(document).on('change', '#waves_billing_from_shipping', function () {
      applyBillingSyncUI();
      // fuerza recalcular checkout sin romper
      $(document.body).trigger('update_checkout');
    });
  });
})(jQuery);


(function ($) {

  const KEY = 'waves_chosen_shipping_0';

  function readWooTotal() {
    // Total nativo (el que Woo mantiene actualizado)
    const $total = $('#order_review .order-total .woocommerce-Price-amount');
    return $total.length ? $total.first().text().trim() : null;
  }

  function updateTotalCard() {
    const total = readWooTotal();
    if (!total) return;

    const $card = $('.waves-total-amount');
    if (!$card.length) return;

    $card.text(total);
  }

  function storeChosenShipping() {
    const $checked = $('input[name="shipping_method[0]"]:checked');
    if ($checked.length) {
      localStorage.setItem(KEY, $checked.val());
    }
  }

  function restoreChosenShipping() {
    const chosen = localStorage.getItem(KEY);
    if (!chosen) return;

    const $radio = $('input[name="shipping_method[0]"][value="' + chosen.replace(/"/g,'\\"') + '"]');
    if (!$radio.length) return;

    // Si Woo lo desmarcó, lo volvemos a marcar
    if (!$radio.is(':checked')) {
      $radio.prop('checked', true);

      // Importante: trigger change para que Woo lo tome
      $radio.trigger('change');
    }
  }

  function bindShippingEvents() {
    // Guardar cada vez que el usuario elige uno
    $(document).off('change.wavesShip', 'input[name="shipping_method[0]"]');
    $(document).on('change.wavesShip', 'input[name="shipping_method[0]"]', function () {
      storeChosenShipping();
      // Forzar refresh para que Woo recalcule total/pago
      $(document.body).trigger('update_checkout');
    });
  }

  $(document).ready(function () {
    bindShippingEvents();
    updateTotalCard();
    restoreChosenShipping();

    // Cada vez que Woo refresca el checkout
    $(document.body).on('updated_checkout', function () {
      bindShippingEvents();       // porque los radios se re-renderizan
      restoreChosenShipping();    // vuelve a marcar si Woo lo pisó
      updateTotalCard();          // actualiza tu card total
    });

    // También cuando Woo inicia el refresh
    $(document.body).on('update_checkout', function () {
      updateTotalCard();
    });
  });

})(jQuery);
