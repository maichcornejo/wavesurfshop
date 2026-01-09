jQuery(function ($) {

  function enhanceWooMessages() {
    $('.woocommerce-message, .woocommerce-error, .woocommerce-info')
      .not('.waves-enhanced')
      .each(function () {
        $(this)
          .addClass('waves-enhanced')
          .append('<button class="waves-close-notice">×</button>');
      });
  }

  enhanceWooMessages();

  $(document.body).on('updated_cart_totals wc_fragments_loaded', enhanceWooMessages);

  $(document).on('click', '.waves-close-notice', function () {
    $(this).closest('.woocommerce-message, .woocommerce-error, .woocommerce-info')
      .fadeOut(200, function () {
        $(this).remove();
      });
  });

});
