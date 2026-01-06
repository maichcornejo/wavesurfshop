jQuery(function ($) {

  $('.btn-pago').on('click', function () {

    const method = $(this).data('method');

    // guardamos el método elegido
    sessionStorage.setItem('waves_payment_method', method);

    // vamos al checkout
    window.location.href = wc_checkout_params.checkout_url;

  });

});
