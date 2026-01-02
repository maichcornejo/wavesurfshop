jQuery(function ($) {

  /* ============================
     HELPERS
  ============================ */

  function getQtyInput(btn) {
    return btn.closest('.qty-wrapper').find('input.qty');
  }

  function triggerWooUpdate() {
    const $btn = $('button[name="update_cart"]');
    if ($btn.length) {
      $btn.prop('disabled', false);
      $btn.trigger('click');
    }
  }

  /* ============================
     BOTONES + / -
  ============================ */

  function ensureButtons() {
    $('.qty-wrapper').each(function () {
      const w = $(this);

      if (w.find('.qty-minus').length === 0) {
        w.prepend('<button type="button" class="qty-btn qty-minus">−</button>');
      }

      if (w.find('.qty-plus').length === 0) {
        w.append('<button type="button" class="qty-btn qty-plus">+</button>');
      }
    });
  }

  function bindQtyEvents() {

    $(document)
      .off('click', '.qty-plus')
      .on('click', '.qty-plus', function () {
        const input = getQtyInput($(this));
        let val = parseInt(input.val(), 10) || 0;
        let max = parseInt(input.attr('max'), 10) || 9999;

        input.val(Math.min(val + 1, max)).trigger('change');
      });

    $(document)
      .off('click', '.qty-minus')
      .on('click', '.qty-minus', function () {
        const input = getQtyInput($(this));
        let val = parseInt(input.val(), 10) || 0;
        let min = parseInt(input.attr('min'), 10) || 0;

        input.val(Math.max(val - 1, min)).trigger('change');
      });

    $(document)
      .off('change', '.qty-wrapper input.qty')
      .on('change', '.qty-wrapper input.qty', function () {
        triggerWooUpdate();
      });
  }

  /* ============================
     INIT
  ============================ */

  ensureButtons();
  bindQtyEvents();

  // Cuando Woo refresca el carrito
  $(document.body).on('updated_wc_div updated_cart_totals', function () {
    ensureButtons();
    bindQtyEvents();
  });

});
