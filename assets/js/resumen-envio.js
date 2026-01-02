jQuery(function ($) {

  /* ===============================
     HELPERS – LOG + LOADING
  =============================== */

  function logShippingState(context = '') {

    const method   = $('input[name^="shipping_method"]:checked').val() || '—';
    const state    = $('[name="calc_shipping_state"]').val() || '—';
    const city     = $('[name="calc_shipping_city"]').val() || '—';
    const postcode = $('[name="calc_shipping_postcode"]').val() || '—';

    console.group(`📦 ESTADO ENVÍO ${context}`);
    console.log('🚚 Método:', method);
    console.log('🗺️ Provincia:', state);
    console.log('🏙️ Ciudad:', city);
    console.log('📮 CP:', postcode);
    console.groupEnd();
  }

  function startTotalLoading() {
    $('.resumen-total').addClass('is-loading');
  }

  function stopTotalLoading() {
    $('.resumen-total').removeClass('is-loading');
  }

  /* ===============================
     SHIPPING ADDRESS
  =============================== */

  $(document).on('submit', '.woocommerce-shipping-calculator', function (e) {
    e.preventDefault();

    const $form = $(this);

    console.group('📦 UPDATE SHIPPING ADDRESS');
    logShippingState('(antes de guardar dirección)');
    startTotalLoading();

    $.ajax({
      url: waves_wc.ajax_url.replace('%%endpoint%%', 'waves_update_shipping_address'),
      type: 'POST',
      data: $form.serialize(),
      success: function () {
        console.log('✅ Dirección guardada');
        logShippingState('(dirección guardada)');
        updateFragments();
      },
      error: function (xhr) {
        console.error('❌ Error dirección', xhr.responseText);
        stopTotalLoading();
      }
    });

    console.groupEnd();
  });

  /* ===============================
     SHIPPING METHOD
  =============================== */

  $(document).on('change', 'input[name^="shipping_method"]', function () {

    const method = $(this).val();

    console.group('🚚 UPDATE SHIPPING METHOD');
    console.log('Seleccionado:', method);
    logShippingState('(cambio de método)');
    startTotalLoading();

    $.ajax({
      url: waves_wc.ajax_url.replace('%%endpoint%%', 'update_shipping_method'),
      type: 'POST',
      data: {
        security: waves_wc.nonce,
        shipping_method: [method]
      },
      success: function () {
        console.log('✅ Método guardado');
        updateFragments();
      },
      error: function (xhr) {
        console.error('❌ Error método', xhr.responseText);
        stopTotalLoading();
      }
    });

    console.groupEnd();
  });

  /* ===============================
     FRAGMENTS (TOTAL + SHIPPING)
  =============================== */

  function updateFragments() {

    $.ajax({
      url: waves_wc.ajax_url.replace('%%endpoint%%', 'get_refreshed_fragments'),
      type: 'POST',
      success: function (response) {

        if (!response?.fragments) {
          stopTotalLoading();
          return;
        }

        console.log('🔄 Fragments actualizados');
        logRatesFromDOM();

        $.each(response.fragments, function (key, value) {
          $(key).replaceWith(value);
        });

        const total =
          $('.resumen-total strong').text() ||
          $('.order-total .woocommerce-Price-amount').text();

        if (total) {
          console.log('💰 TOTAL ACTUALIZADO:', total);
        }

        logShippingState('(post fragments)');
        stopTotalLoading();
      },
      error: function () {
        stopTotalLoading();
      }
    });
  }

function logRatesFromDOM() {

  console.group('🚚 SHIPPING RATES (DOM)');

  $('input[name^="shipping_method"]').each(function () {
    const label = $(this).closest('li').text().trim();
    const id = $(this).val();
    console.log(`${label} | ID: ${id}`);
  });

  console.groupEnd();
}


});
