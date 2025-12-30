jQuery(function ($) {

  // Evitar anchors #
  $(document).on('click', 'a[href="#"]', function (e) {
    e.preventDefault();
  });

  function refreshPage() {
    setTimeout(function () {
      location.href = location.pathname;
    }, 400);
  }

  // SOLO refrescar cuando cambia método o sucursal
  $(document).on(
    'change',
    'input[name^="shipping_method"], .resumen-metodos-envio select',
    function () {
      refreshPage();
    }
  );

});

jQuery(function ($) {

  $(document).on('submit', '.woocommerce-shipping-calculator', function (e) {
    e.preventDefault();

    const $form = $(this);
    const formData = $form.serializeArray();

    console.group('📦 SUBMIT SHIPPING CALCULATOR');

    // 1️⃣ Datos de dirección enviados
    console.log('📍 Dirección enviada:');
    formData.forEach(field => {
      if (
        field.name.includes('country') ||
        field.name.includes('state') ||
        field.name.includes('city') ||
        field.name.includes('postcode')
      ) {
        console.log(`   ${field.name}:`, field.value);
      }
    });

    // 2️⃣ Método seleccionado ANTES
    const selectedBefore = $('input[name^="shipping_method"]:checked').val();
    console.log('🚚 Envío seleccionado ANTES:', selectedBefore || 'ninguno');

    $.ajax({
      url: wc_cart_params.wc_ajax_url.replace('%%endpoint%%', 'update_shipping_method'),
      type: 'POST',
      data: $form.serialize(),
      success: function (res) {

        console.log('✅ update_shipping_method OK');
        console.log('📨 Response:', res);

        // Forzar recálculo
        $(document.body).trigger('update_checkout');

        refreshFragments();
      },
      error: function (xhr) {
        console.error('❌ Error update_shipping_method', xhr.responseText);
        console.groupEnd();
      }
    });
  });

  function refreshFragments() {

    console.group('🔄 REFRESH FRAGMENTS');

    $.ajax({
      url: wc_cart_params.ajax_url,
      type: 'POST',
      data: { action: 'woocommerce_get_refreshed_fragments' },
      success: function (response) {

        console.log('📦 Fragments response:', response);

        if (response?.fragments) {

          // 3️⃣ Métodos disponibles DESPUÉS
          const methods = $('input[name^="shipping_method"]')
            .map(function () {
              return {
                id: $(this).val(),
                label: $(this).closest('li').text().trim()
              };
            })
            .get();

          console.log('🚚 Métodos disponibles DESPUÉS:', methods);

          const selectedAfter = $('input[name^="shipping_method"]:checked').val();
          console.log('🚚 Envío seleccionado DESPUÉS:', selectedAfter || 'ninguno');

          $.each(response.fragments, function (key, value) {
            $(key).replaceWith(value);
          });

        } else {
          console.warn('⚠️ No llegaron fragments');
        }

        console.groupEnd();
      },
      error: function (xhr) {
        console.error('❌ Error refresh fragments', xhr.responseText);
        console.groupEnd();
      }
    });
  }

});
