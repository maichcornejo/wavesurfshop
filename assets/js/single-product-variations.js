jQuery(function ($) {

  const $form = $('.variations_form');

  /* ===== CLICK EN SWATCH ===== */
  $('.waves-color-swatches').on('click', '.color-swatch', function () {

    const $swatch = $(this);
    const $radio  = $swatch.find('input[type="radio"]');

    if (!$radio.length) return;

    const value = $radio.val();
    const name  = $radio.attr('name'); // attribute_pa_color

    // UI
    $('.color-swatch').removeClass('active');
    $swatch.addClass('active');
    $radio.prop('checked', true);

    // sincroniza el select REAL de Woo
    const $select = $form.find(`select[name="${name}"]`);
    if ($select.length) {
      $select.val(value).trigger('change');
    }

  });

  /* ===== CAMBIO DE IMAGEN ===== */
  $form.on('found_variation', function (e, variation) {

    if (!variation.image || !variation.image.src) return;

    const newSrc = variation.image.src;

    const $img = $('.product-gallery:visible img.wp-post-image').first();

    if (!$img.length) return;

    // guarda original
    if (!$img.data('original-src')) {
      $img.data('original-src', $img.attr('src'));
    }

    $img
      .attr('src', newSrc)
      .removeAttr('srcset')
      .removeAttr('sizes');

  });

  /* ===== RESET ===== */
  $form.on('reset_data', function () {

    const $img = $('.product-gallery img.wp-post-image').first();
    const original = $img.data('original-src');

    if (original) {
      $img
        .attr('src', original)
        .removeAttr('srcset')
        .removeAttr('sizes');
    }

    $('.color-swatch').removeClass('active');
    $('.waves-color-swatches input[type="radio"]').prop('checked', false);

  });

});

jQuery(function ($) {
  $('.variations_form').on('found_variation', function () {
    const $img = $('.product-gallery img.wp-post-image').first();
    $img.css({ opacity: 0, transform: 'scale(0.96)' });
    setTimeout(() => {
      $img.css({ opacity: 1, transform: 'scale(1)' });
    }, 120);
  });
});


function updateStockBar(stock) {

  const stockEl = document.querySelector('.waves-stock');
  if (!stockEl) return;

  const bar  = stockEl.querySelector('.waves-stock-bar span');
  const text = stockEl.querySelector('.waves-stock-text');

  const MAX_STOCK = 50;

  let percentage = Math.min((stock / MAX_STOCK) * 100, 100);
  percentage = Math.max(percentage, 0);

  bar.style.width = `${percentage}%`;

  if (stock <= 0) {
    bar.style.backgroundColor = '#dc2626';
    text.textContent = 'Sin stock';
  } else if (stock <= 10) {
    bar.style.backgroundColor = '#dc2626';
    text.textContent = '¡Últimas unidades!';
  } else if (stock <= 25) {
    bar.style.backgroundColor = '#f97316';
    text.textContent = 'Quedan pocas unidades';
  } else if (stock < 50) {
    bar.style.backgroundColor = '#eab308';
    text.textContent = 'Stock disponible';
  } else {
    bar.style.backgroundColor = '#22c55e';
    text.textContent = 'Stock alto';
  }
}

jQuery(function ($) {

  const $variationBox = $('.woocommerce-variation.single_variation');

  const observer = new MutationObserver(() => {

    const text = $variationBox.text();

    // Buscar número en "80 disponibles"
    const match = text.match(/(\d+)/);

    if (match) {
      const stock = parseInt(match[1], 10);
      updateStockBar(stock);
    } else if (text.toLowerCase().includes('agotado')) {
      updateStockBar(0);
    }

  });

  observer.observe($variationBox[0], {
    childList: true,
    subtree: true,
    characterData: true
  });

});

document.querySelectorAll('.waves-accordion-header').forEach(header => {
  header.addEventListener('click', () => {

    const item = header.parentElement;
    const icon = header.querySelector('.accordion-icon');

    const isOpen = item.classList.contains('is-open');

    // cerrar todos
    document.querySelectorAll('.waves-accordion-item').forEach(i => {
      i.classList.remove('is-open');
      const ic = i.querySelector('.accordion-icon');
      if (ic) ic.textContent = '+';
    });

    // abrir el actual si no estaba abierto
    if (!isOpen) {
      item.classList.add('is-open');
      icon.textContent = '–';
    }

  });
});

jQuery(function ($) {
  const $modal = $('#wavesNotifyModal');
  const $btnOpen = $('#wavesNotifyBtn');
  const $btnClose = $('#wavesNotifyClose');
  const $formNotify = $('.waves-notify-form');

  /* ===== 1. ABRIR MODAL ===== */
  $btnOpen.on('click', function (e) {
    e.preventDefault();
    $modal.addClass('active');
    // Bloquea el scroll del fondo (opcional pero recomendado)
    $('body').css('overflow', 'hidden');
  });

  /* ===== 2. CERRAR MODAL ===== */
  function closeModal() {
    $modal.removeClass('active');
    $('body').css('overflow', '');
  }

  $btnClose.on('click', closeModal);

  // Cerrar al hacer clic fuera del cuadro blanco
  $modal.on('click', function (e) {
    if ($(e.target).is($modal)) {
      closeModal();
    }
  });

  /* ===== 3. LÓGICA DE STOCK (WOOCOMMERCE) ===== */
  // Ocultamos el botón por defecto si el producto tiene stock al cargar
  $('.variations_form').on('found_variation', function (e, variation) {
    if (!variation.is_in_stock) {
      $btnOpen.show(); // Aparece si está agotado
    } else {
      $btnOpen.hide(); // Se oculta si hay stock
    }
  });

  $('.variations_form').on('reset_data', function () {
    $btnOpen.hide();
  });

  /* ===== 4. ENVÍO DEL FORMULARIO ===== */
  $formNotify.on('submit', function (e) {
    e.preventDefault();
    
    const talle = $(this).find('select').val();
    const whatsapp = $(this).find('input[type="tel"]').val();

    // Aquí podrías conectar con una API o mostrar un mensaje de éxito
    const originalBtnText = $(this).find('button').text();
    $(this).find('button').text('¡ANOTADO! ✅').prop('disabled', true);

    setTimeout(() => {
      alert(`¡Listo! Te avisaremos al ${whatsapp} cuando el talle ${talle} esté disponible.`);
      closeModal();
      
      // Resetear botón para la próxima vez
      $(this).find('button').text(originalBtnText).prop('disabled', false);
      $formNotify[0].reset();
    }, 1000);
  });
});
