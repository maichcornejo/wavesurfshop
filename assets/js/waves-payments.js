jQuery(function ($) {
  const $box = $('.resumen-pago');
  if (!$box.length) return;

  const $continue = $box.find('.waves-pay-continue');
  const $hint = $box.find('.waves-pay-hint');

  function getSelected() {
    return $box.find('input[name="waves_payment_method"]:checked').val() || '';
  }

  function updateUI() {
    const selected = getSelected();
    $continue.prop('disabled', !selected);

    $box.find('.waves-pay-card').removeClass('is-selected');
    if (selected) {
      $box.find(`input[value="${selected}"]`).closest('.waves-pay-card').addClass('is-selected');
      $hint.text('Listo. Vas a ir al checkout con este método preseleccionado.');
    } else {
      $hint.text('Elegí un método para continuar.');
    }
  }

  $box.on('change', 'input[name="waves_payment_method"]', updateUI);

  $continue.on('click', function () {
    const method = getSelected();
    if (!method) return;

    $continue.prop('disabled', true).addClass('is-loading').text('Preparando checkout…');

    $.post(WAVES_PAY.ajax_url, {
      action: 'waves_set_payment_method',
      method: method
    })
    .done(function (res) {
      if (res && res.success && res.data && res.data.checkout_url) {
        window.location.href = res.data.checkout_url;
        return;
      }
      const msg = (res && res.data && res.data.message) ? res.data.message : 'No se pudo guardar el método.';
      alert(msg);
    })
    .fail(function () {
      alert('Error de red guardando el método.');
    })
    .always(function () {
      $continue.prop('disabled', false).removeClass('is-loading').text('Continuar al checkout');
    });
  });

  updateUI();
});
