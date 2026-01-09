jQuery(function ($) {

  function collectFilters() {
    let filters = {};
    $('.filter-term:checked').each(function () {
      const taxonomy = $(this).data('taxonomy');
      const value = $(this).val();
      filters[taxonomy] = filters[taxonomy] || [];
      filters[taxonomy].push(value);
    });
    return filters;
  }

  function applyFilters(page = 1) {
    const filters = collectFilters();
    const price = $('#price-filter').val() || null;

    $.ajax({
      url: waves_ajax.ajax_url,
      method: 'POST',
      data: {
        action: 'filter_products',
        nonce: waves_ajax.nonce,
        filters: filters,
        price: price,
        page: page
      },
      beforeSend() {
        $('#products-list')
          .addClass('loading')
          .html('<div class="spinner"></div>');
      },
      success(response) {
        $('#products-list')
          .removeClass('loading')
          .html(response);

        if (window.initProductCard) window.initProductCard();
        document.querySelector('.shop-results')?.scrollIntoView({behavior:'smooth', block:'start'});
      }
    });
  }

  /* ==========
     Sidebar UX
  ========== */

  // helper: setea abierto/cerrado un bloque
  function setAccordionState($block, open){
    const $body = $block.find('.filter-body').first();
    $body.prop('hidden', !open);

    $block.find('.filter-toggle')
      .attr('aria-expanded', String(open))
      .find('.chev')
      .text(open ? '▾' : '▸');
  }

  // Colapsar/expandir
  $(document).on('click', '.filter-toggle', function () {
    const $block = $(this).closest('.filter-block');
    const $body  = $block.find('.filter-body').first();
    const isOpen = !$body.prop('hidden');

    setAccordionState($block, !isOpen);
  });

  // “Ver más” (limita chips)
  function applyLimit($options) {
    const limit = parseInt($options.data('limit') || 999, 10);
    const $items = $options.children('.filter-checkbox');
    $items.each(function(i){
      if (i >= limit) $(this).attr('hidden', true);
      else $(this).removeAttr('hidden');
    });
  }

  $('.filter-options').each(function(){ applyLimit($(this)); });

  $(document).on('click', '.filter-more', function(){
    const $block = $(this).closest('.filter-block');
    const $options = $block.find('.filter-options');
    const isExpanded = $(this).data('expanded') === 1;

    if (!isExpanded) {
      $options.children('.filter-checkbox').removeAttr('hidden');
      $(this).text('Ver menos').data('expanded', 1);
    } else {
      applyLimit($options);
      $(this).text('Ver más').data('expanded', 0);
    }
  });

  // Buscador por bloque
  $(document).on('input', '.filter-search', function(){
    const q = ($(this).val() || '').toLowerCase().trim();
    const $block = $(this).closest('.filter-block');
    $block.find('.filter-checkbox').each(function(){
      const name = $(this).data('term-name') || '';
      $(this).toggle(name.includes(q));
    });
  });

  // Contador de seleccionados por bloque
  function refreshSelectedCounts(){
    $('.filter-block[data-taxonomy]').each(function(){
      const selected = $(this).find('.filter-term:checked').length;
      $(this).find('.selected-count').text(selected);
    });
  }

  /* ==========
     Eventos filtros
  ========== */

  $(document).on('change', '.filter-term', function () {
    refreshSelectedCounts();
    applyFilters(1);
  });

  $('#price-filter').on('input change', function () {
    $('#price-output').text('Hasta $' + $(this).val());
    applyFilters(1);
  });

  $(document).on('click', '#filters-reset', function(){
    $('.filter-term').prop('checked', false);
    $('#price-filter').val($('#price-filter').attr('min') || 0);
    $('#price-output').text('');
    refreshSelectedCounts();
    applyFilters(1);
  });

  /* ==========
     Paginación AJAX
  ========== */

  function parsePageFromHref(href){
    try{
      const url = new URL(href, window.location.origin);
      const p = url.searchParams.get('paged') || url.searchParams.get('product-page');
      if (p) return parseInt(p, 10);
    }catch(e){}
    const m = href.match(/\/page\/(\d+)/);
    if (m) return parseInt(m[1], 10);
    return 1;
  }

  $(document).on('click', '.woocommerce-pagination a, .page-numbers a', function(e){
    e.preventDefault();
    applyFilters(parsePageFromHref(this.href));
  });

  /* ==========
     URL → FILTROS
  ========== */

  function applyFiltersFromURL() {
    const params = new URLSearchParams(window.location.search);

    params.forEach((value, key) => {
      value.split(',').forEach(val => {
        $(`.filter-term[data-taxonomy="${key}"][value="${val}"]`).prop('checked', true);
      });
    });

    refreshSelectedCounts();

    if (params.toString()) applyFilters(1);
  }

  applyFiltersFromURL();
  refreshSelectedCounts();

  /* ==========
     Estado inicial: acordiones cerrados
  ========== */

  // 1) Cerrar todos por defecto
  $('.filter-block').each(function(){
    setAccordionState($(this), false);
  });

  // 2) Opcional: dejar abierto SOLO Color
  $('.filter-block[data-taxonomy="pa_color"]').each(function(){
    setAccordionState($(this), true);
  });

  // 3) Si hay checks activos en un bloque, lo abrimos
  $('.filter-block[data-taxonomy]').each(function(){
    const $b = $(this);
    if ($b.find('.filter-term:checked').length > 0) {
      setAccordionState($b, true);
    }
  });

});
