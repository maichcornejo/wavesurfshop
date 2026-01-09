jQuery(function ($) {

  $('#tracking-form').on('submit', function (e) {
    e.preventDefault();

    const code = $('#tracking-code').val().trim();
    const $result = $('#tracking-result');

    if (!code) return;

    $result.html('<p>Consultando estado del envío...</p>');

    $.post(wavesTracking.ajax_url, {
      action: 'waves_track_shipment',
      nonce: wavesTracking.nonce,
      code: code
    }, function (res) {

      if (!res.success) {
        $result.html('<p>Error al consultar el envío.</p>');
        return;
      }

      /* =============================
         PEDIDO WOO
      ============================= */
      if (res.data.type === 'woocommerce') {

        $result.html(`
          <div class="tracking-box">
            <h3>Pedido #${res.data.order_id}</h3>
            <p><strong>Estado:</strong> ${res.data.status}</p>
            <p><strong>Fecha:</strong> ${res.data.date}</p>
            <p><strong>Total:</strong> ${res.data.total}</p>
            <p><strong>Código:</strong> ${res.data.tracking}</p>
          </div>
        `);
      }

      /* =============================
         CORREO ARGENTINO
      ============================= */
      if (res.data.type === 'correo_argentino') {

        $result.html(`
          <div class="tracking-box">
            <p>No encontramos el pedido en la tienda.</p>
            <a
              href="${res.data.url}"
              target="_blank"
              class="tracking-link"
            >
              Ver seguimiento en Correo Argentino →
            </a>
          </div>
        `);
      }

    });

  });

});
