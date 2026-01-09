jQuery(function ($) {

  const $form = $('.variations_form');
  if (!$form.length) return;

  const variations = $form.data('product_variations') || [];

  // ✅ DEBUG helper
  function logVariationStock(variation, context = '') {
    console.group(`📦 STOCK DEBUG (${context})`);

    if (!variation) {
      console.warn('No variation object');
      console.groupEnd();
      return;
    }

    console.info('variation_id:', variation.variation_id);
    console.info('is_in_stock:', variation.is_in_stock);

    // Woo suele traer esto (a veces viene null/undefined según config)
    console.info('stock_quantity:', variation.stock_quantity);
    console.info('backorders_allowed:', variation.backorders_allowed);
    console.info('max_qty:', variation.max_qty);
    console.info('min_qty:', variation.min_qty);

    // Texto/HTML que Woo muestra en la caja de variación
    console.info('availability_html:', variation.availability_html);
    console.info('variation_description:', variation.variation_description);

    // Qué atributos quedaron elegidos
    console.info('attributes:', variation.attributes);

    // Si existe tu barra
    const stockEl = document.querySelector('.waves-stock');
    console.info('.waves-stock exists?', !!stockEl);

    console.groupEnd();
  }

  /* =====================================================
     VARIACIÓN COMPLETA (COLOR + TALLE)
     Woo dispara found_variation
  ===================================================== */
  $form.on('found_variation', function (e, variation) {

    // ✅ LOG del stock
    logVariationStock(variation, 'found_variation');

    // (tu código de imagen sigue igual)
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

    $img.css({ opacity: 0, transform: 'scale(0.96)' });
    setTimeout(() => {
      $img.css({ opacity: 1, transform: 'scale(1)' });
    }, 120);
  });

  // ✅ opcional: también cuando Woo muestra variación (algunos themes usan este evento)
  $form.on('show_variation', function (e, variation) {
    logVariationStock(variation, 'show_variation');
  });

  // ✅ cuando se oculta / queda inválida
  $form.on('hide_variation', function () {
    console.info('📦 STOCK DEBUG (hide_variation) -> variación inválida / incompleta');
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

    // Toggle SOLO este item
    item.classList.toggle('is-open', !isOpen);

    // Icono
    if (icon) {
      icon.textContent = isOpen ? '+' : '–';
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

  // 🔥 sincronizar el select REAL de Woo
  const $colorSelect = $form.find('select[name="attribute_pa_color"]');
  if ($colorSelect.length) {
    $colorSelect.val(color).trigger('change');
  }

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
  const $first = jQuery('.waves-size-grid .size-box:not(.disabled)').first();
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

jQuery(function ($) {

  $(document).on('click', '.fav-heart', function (e) {

    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const productId = $btn.data('product-id');
    const $tooltip = $btn.find('.fav-tooltip');

    if (!productId) return;

    $.ajax({
      url: wc_add_to_cart_params.ajax_url,
      type: 'POST',
      data: {
        action: 'toggle_favorite',
        product_id: productId
      },
      success: function (res) {
        
        if (!res.success) return;

        $btn
          .toggleClass('active', res.data.is_favorite)
          .addClass('pop');

        setTimeout(() => {
          $btn.removeClass('pop');
        }, 450);

        $tooltip.text(
          res.data.is_favorite
            ? 'Quitar de favoritos'
            : 'Agregar a favoritos'
        );
        $(`.favorite-row[data-product-id="${productId}"]`)
        .slideUp(250, function () {
          $(this).remove();
        });
      }
    });

  });

});


document.querySelectorAll('.waves-stock').forEach(el => {
  const stock = parseInt(el.dataset.stock, 10);

  if (isNaN(stock)) return;

  // scope local
  const bar = el.querySelector('.waves-stock-bar span');
  const text = el.querySelector('.waves-stock-text');

  const MAX_STOCK = 30;
  let percentage = Math.max(0, Math.min((stock / MAX_STOCK) * 100, 100));
  bar.style.width = `${percentage}%`;

  let color = '';
  let label = '';

  if (stock <= 0) {
    color = '#dc2626';
    label = 'Sin stock';
  } else if (stock <= 3) {
    color = '#dc2626';
    label = 'Última disponible';
  } else if (stock <= 5) {
    color = '#dc2626';
    label = '¡Últimas unidades!';
  } else if (stock <= 15) {
    color = '#f97316';
    label = 'Quedan pocas unidades';
  } else {
    color = '#22c55e';
    label = 'Disponible';
  }

  bar.style.backgroundColor = color;
  text.textContent = label;
  text.style.color = color;
});

jQuery(function ($) {

  $(document).on('click', '.btn-remove-favorite', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const productId = $btn.data('product-id');

    if (!productId) return;

    $.ajax({
      url: wc_add_to_cart_params.ajax_url,
      type: 'POST',
      data: {
        action: 'remove_favorite',
        product_id: productId
      },
      success: function (res) {
        console.log('REMOVE', res);

        if (!res || !res.success) return;

        // Animación: remover de la lista
        $btn
          .closest('.favorite-row')
          .slideUp(250, function () {
            $(this).remove();
          });

      }
    });

  });

});


document.addEventListener('DOMContentLoaded', function () {

  const openBtn  = document.getElementById('openSizeGuide');
  const modal    = document.getElementById('sizeGuideModal');
  const closeBtn = modal.querySelector('.waves-size-close');
  const backdrop = modal.querySelector('.waves-size-modal-backdrop');

  openBtn.addEventListener('click', () => {
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
  });

  function closeModal() {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }

  closeBtn.addEventListener('click', closeModal);
  backdrop.addEventListener('click', closeModal);

});
