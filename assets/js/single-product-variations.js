jQuery(function ($) {

  const $form = $('.variations_form');
  if (!$form.length) return;

  // Variaciones de Woo (CLAVE para imagen por color)
  const variations = $form.data('product_variations') || [];

  /* =====================================================
     CLICK EN COLOR (SWATCH)
     - sincroniza select
     - cambia imagen aunque NO haya talle
  ===================================================== */
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

    /* ===== CAMBIO DE IMAGEN SOLO POR COLOR ===== */
    const variationByColor = variations.find(v =>
      v.attributes[name] === value && v.image && v.image.src
    );

    if (variationByColor) {
      const $img = $('.product-gallery img.wp-post-image').first();
      if ($img.length) {

        if (!$img.data('original-src')) {
          $img.data('original-src', $img.attr('src'));
        }

        $img
          .attr('src', variationByColor.image.src)
          .removeAttr('srcset')
          .removeAttr('sizes');
      }
    }
  });

  /* =====================================================
     VARIACIÓN COMPLETA (COLOR + TALLE)
     Woo dispara found_variation
  ===================================================== */
  $form.on('found_variation', function (e, variation) {

    if (!variation || !variation.image || !variation.image.src) return;

    const $img = $('.product-gallery img.wp-post-image').first();
    if (!$img.length) return;

    if (!$img.data('original-src')) {
      $img.data('original-src', $img.attr('src'));
    }

    $img
      .attr('src', variation.image.src)
      .removeAttr('srcset')
      .removeAttr('sizes');

    // animación
    $img.css({ opacity: 0, transform: 'scale(0.96)' });
    setTimeout(() => {
      $img.css({ opacity: 1, transform: 'scale(1)' });
    }, 120);
  });

  /* =====================================================
     RESET
  ===================================================== */
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

/* =====================================================
   STOCK BAR (NO SE TOCA)
===================================================== */
function updateStockBar(stock) {
  const stockEl = document.querySelector('.waves-stock');
  if (!stockEl) return;

  const bar = stockEl.querySelector('.waves-stock-bar span');
  const text = stockEl.querySelector('.waves-stock-text');

  if (!bar) return;

  const MAX_STOCK = 30;
  let percentage = Math.max(0, Math.min((stock / MAX_STOCK) * 100, 100));

  bar.style.width = `${percentage}%`;

  let color = '';
  let label = '';

  if (stock <= 0) {
    color = '#dc2626';
    label = 'Sin stock';
  } else if (stock <= 5) {
    color = '#dc2626';
    label = '¡Últimas unidades!';
  } else if (stock <= 15) {
    color = '#f97316';
    label = 'Stock bajo';
  } else if (stock <= 25) {
    color = '#eab308';
    label = 'Stock medio';
  } else {
    color = '#22c55e';
    label = 'Stock disponible';
  }

  // USAMOS setProperty para saltarnos cualquier restricción del CSS
  bar.style.setProperty('background-color', color, 'important');
  // También aplicamos el color al texto para que el shadow (currentColor) funcione si decides dejarlo
  bar.style.setProperty('color', color); 
  
  if (text) {
      text.textContent = label;
      // Esto hace que el texto cambie al mismo color que la barra
      text.style.color = color; 
    }
}


jQuery(function ($) {

  const $variationBox = $('.woocommerce-variation.single_variation');
  if (!$variationBox.length) return;

  const observer = new MutationObserver(() => {

    const text = $variationBox.text();
    // Busca un número que NO tenga un símbolo de moneda inmediatamente antes
    // o que esté seguido de palabras relacionadas a stock
    const stockMatch = text.match(/(?:disponibles|stock|unidades):\s*(\d+)|(\d+)\s*(?:disponibles|unidades)/i);

    if (stockMatch) {
      // Captura el grupo que no sea null
      const stockValue = parseInt(stockMatch[1] || stockMatch[2], 10);
      updateStockBar(stockValue);
    }
    else if (text.toLowerCase().includes('agotado')) {
      updateStockBar(0);
    }
  });

  observer.observe($variationBox[0], {
    childList: true,
    subtree: true,
    characterData: true
  });
});

/* =====================================================
   ACCORDION
===================================================== */
document.querySelectorAll('.waves-accordion-header').forEach(header => {
  header.addEventListener('click', () => {

    const item = header.parentElement;
    const icon = header.querySelector('.accordion-icon');
    const isOpen = item.classList.contains('is-open');

    document.querySelectorAll('.waves-accordion-item').forEach(i => {
      i.classList.remove('is-open');
      const ic = i.querySelector('.accordion-icon');
      if (ic) ic.textContent = '+';
    });

    if (!isOpen) {
      item.classList.add('is-open');
      icon.textContent = '–';
    }
  });
});


jQuery(function ($) {

  const $form = $('.variations_form');
  if (!$form.length) return;

  const variations = $form.data('product_variations') || [];

  /* =====================================================
     CLICK EN TALLE (CUADRADITOS)
  ===================================================== */
  $('.waves-size-grid').on('click', '.size-box:not(.disabled)', function () {

    const $box = $(this);
    const value = $box.data('value');
    const attrName = $box.closest('.waves-size-grid').data('attribute');

    // UI
    $box.siblings().removeClass('active');
    $box.addClass('active');

    // sincroniza select real de Woo
    const $select = $form.find(`select[name="${attrName}"]`);
    if ($select.length) {
      $select.val(value).trigger('change');
    }
  });

  /* =====================================================
     DESHABILITAR TALLES SIN STOCK (DINÁMICO)
  ===================================================== */
  function updateSizeAvailability(selectedColor = null) {

    // Detectar dinámicamente la key REAL del talle
    const sizeAttribute = (variations[0] && Object.keys(variations[0].attributes || {}))
      .find(k => k.includes('talle'));

    console.info('[sizes] sizeAttribute REAL detectado:', sizeAttribute);
    console.info('[sizes] color seleccionado:', selectedColor);

    if (!sizeAttribute) {
      console.warn('[sizes] No se pudo detectar el atributo de talle');
      return;
    }

    // Obtener talles con stock para el color
    const sizesInStock = Array.from(new Set(
      variations
        .filter(v => {
          const attrs = v.attributes || {};
          const vColor = attrs['attribute_pa_color'];
          const vSize  = attrs[sizeAttribute];

          const matchesColor = !selectedColor || vColor == selectedColor;
          return matchesColor && vSize && v.is_in_stock;
        })
        .map(v => String(v.attributes[sizeAttribute]).trim())
    ));

    console.info('[sizes] talles EN STOCK para este color:', sizesInStock);

    // Aplicar disabled / enabled
    $('.waves-size-grid .size-box').each(function () {
      const size = String($(this).data('value')).trim();
      const hasStock = sizesInStock.includes(size);
      $(this).toggleClass('disabled', !hasStock);
    });
  }


  /* =====================================================
     CUANDO CAMBIA EL COLOR → REVISAR TALLES
  ===================================================== */
$('.waves-color-swatches').on('click', '.color-swatch', function () {
  const color = $(this).find('input').val();
  updateSizeAvailability(color);
  setTimeout(autoSelectFirstAvailableSize, 50);
});

  /* =====================================================
     RESET
  ===================================================== */
  $form.on('reset_data', function () {
    $('.size-box').removeClass('active disabled');
  });

});


function autoSelectFirstAvailableSize() {
  const $first = $('.waves-size-grid .size-box:not(.disabled)').first();
  if ($first.length) $first.trigger('click');
}


jQuery(function ($) {

  const $form  = $('.variations_form');
  const $price = $('.price-2');

  if (!$form.length || !$price.length) return;

  // Guardamos precio base (rango)
  const basePriceHtml = $price.html();

  /* =====================================
     VARIACIÓN SELECCIONADA → PRECIO VARIACIÓN
  ===================================== */
  $form.on('show_variation', function (e, variation) {

    if (!variation || !variation.price_html) return;

    $price.fadeOut(120, function () {
      $price.html(variation.price_html).fadeIn(180);
    });
  });

  /* =====================================
     VARIACIÓN NO VÁLIDA / RESET
  ===================================== */
  $form.on('hide_variation', function () {
    $price.fadeOut(120, function () {
      $price.html(basePriceHtml).fadeIn(180);
    });
  });

});

jQuery(function ($) {

  const $form = $('.variations_form');
  if (!$form.length) return;

  function forceVariationCheck() {
    // Fuerza a Woo a recalcular la variación
    $form.trigger('check_variations');

    // Debug (podés borrar luego)
    const variationId = $form.find('input.variation_id').val();
    console.info('[variation] variation_id:', variationId);
  }

  // Cuando se cambia CUALQUIER select de Woo
  $form.on('change', 'select', function () {
    forceVariationCheck();
  });

});


jQuery(function ($) {

  const $modal = $('#wavesNotifyModal');
  const $openBtn = $('#wavesNotifyBtn');
  const $closeBtn = $('#wavesNotifyClose');

  const $productName = $('.notify-product-name');
  const $productMeta = $('.notify-product-meta');

  if (!$modal.length || !$openBtn.length) return;

  function getSelectedValue(selector) {
    const $el = $(selector);
    return $el.length ? $el.val() || null : null;
  }

  function openModal() {

    // Nombre del producto
    const productName = $('h1').first().text().trim();

    // Color (opcional)
    const color =
      getSelectedValue('select[name^="attribute_pa_color"]') ||
      $('.color-swatch.active').attr('title') ||
      null;

    // Talle (si ya eligió)
    const size =
      getSelectedValue('select[name*="talle"]') ||
      $('.size-box.active').data('value') ||
      null;

    $productName.text(productName);

    if (color && size) {
      $productMeta.text(`Color: ${color} · Talle: ${size}`);
    } else if (size) {
      $productMeta.text(`Talle: ${size}`);
    } else if (color) {
      $productMeta.text(`Color: ${color}`);
    } else {
      $productMeta.text('');
    }

    // Autoseleccionar talle en el modal
    if (size) {
      $modal.find('select').val(size);
    }

    $modal.addClass('active');
    $('body').addClass('modal-open');
  }

  function closeModal() {
    $modal.removeClass('active');
    $('body').removeClass('modal-open');
  }

  /* ===============================
     EVENTS
  =============================== */

  $openBtn.on('click', function (e) {
    e.preventDefault();
    openModal();
  });

  $closeBtn.on('click', closeModal);

  $modal.on('click', function (e) {
    if ($(e.target).is($modal)) closeModal();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $modal.hasClass('active')) {
      closeModal();
    }
  });

});
