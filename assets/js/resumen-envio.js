jQuery(function ($) {
  "use strict";

  /* ==========================================================
    CONFIG + HELPERS
  ========================================================== */

  const CFG = {
    debug: true,
    endpoints: {
      saveAddress: "waves_update_shipping_address",
      getRates: "waves_micorreo_rates",
      selectRate: "waves_micorreo_select_rate",
      fragments: "get_refreshed_fragments",
    },
    selectors: {
      totalBox: ".resumen-total",
      methodsBox: ".resumen-metodos-envio",
      calcForm: ".woocommerce-shipping-calculator",
      rateContainer: ".waves-rates",
    }
  };

  const hasCfg = typeof window.waves_wc !== "undefined" && window.waves_wc.ajax_url;
  if (!hasCfg) {
    console.warn("⚠️ waves_wc no está definido (ajax_url). Revisá wp_localize_script.");
    return;
  }

  function ajaxUrl(endpoint) {
    return window.waves_wc.ajax_url.replace("%%endpoint%%", endpoint);
  }

  function log(...args) {
    if (CFG.debug) console.log(...args);
  }
  function group(name) {
    if (CFG.debug) console.group(name);
  }
  function groupEnd() {
    if (CFG.debug) console.groupEnd();
  }

  function startTotalLoading() {
    $(CFG.selectors.totalBox).addClass("is-loading");
  }
  function stopTotalLoading() {
    $(CFG.selectors.totalBox).removeClass("is-loading");
  }

  function showMethodsMessage(html) {
    const $box = $(CFG.selectors.methodsBox);
    let $wrap = $box.find(".waves-methods-message");
    if (!$wrap.length) {
      $wrap = $('<div class="waves-methods-message"></div>');
      $box.append($wrap);
    }
    $wrap.html(html);
  }

  function ensureRatesContainer() {
    const $box = $(CFG.selectors.methodsBox);
    let $rates = $box.find(CFG.selectors.rateContainer);
    if (!$rates.length) {
      $rates = $('<div class="waves-rates"></div>');
      $box.append($rates);
    }
    return $rates;
  }

  function serializeAddress($form) {
    // Woo shipping calculator fields (se mantienen)
    return $form.serialize();
  }

  /* ==========================================================
    RENDER: RATES
    Esperado desde PHP:
    {
      success: true,
      data: {
        rates: [
          { id, label, price_html, meta_html? }
        ]
      }
    }
  ========================================================== */

  function renderRates(rates) {
    const $rates = ensureRatesContainer();

    if (!Array.isArray(rates) || !rates.length) {
      $rates.html(`<p style="opacity:.85">No hay opciones de envío para esa dirección.</p>`);
      return;
    }

    const html = `
      <ul class="waves-rates-list">
        ${rates.map(r => `
          <li class="waves-rate">
            <label>
              <input type="radio" name="waves_rate" value="${escapeHtml(r.id)}">
              <span class="waves-rate-label">
                <span class="waves-rate-title">${r.label || "Envío"}</span>
                <span class="waves-rate-price">${r.price_html || ""}</span>
              </span>
              ${r.meta_html ? `<div class="waves-rate-meta">${r.meta_html}</div>` : ""}
            </label>
          </li>
        `).join("")}
      </ul>
    `;

    $rates.html(html);
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  /* ==========================================================
    FRAGMENTS: Total + Shipping (Woo)
  ========================================================== */

  function updateFragments() {
    return $.ajax({
      url: ajaxUrl(CFG.endpoints.fragments),
      type: "POST",
    }).done(function (response) {
      if (!response || !response.fragments) return;

      $.each(response.fragments, function (key, value) {
        $(key).replaceWith(value);
      });

      const total =
        $(CFG.selectors.totalBox).find("strong").first().text() ||
        $(".order-total .woocommerce-Price-amount").text();

      if (total) log("💰 TOTAL ACTUALIZADO:", total);
    });
  }

  /* ==========================================================
    FLOW:
    1) Submit dirección (shipping calculator)
    2) Backend guarda dirección
    3) Pedimos rates a MiCorreo
    4) Render radios
  ========================================================== */

  function fetchRates() {
    showMethodsMessage(`<small style="opacity:.85">Buscando opciones de envío…</small>`);
    return $.ajax({
      url: ajaxUrl(CFG.endpoints.getRates),
      type: "POST",
      dataType: "json"
    }).done(function (res) {
      if (!res || res.success !== true) {
        renderRates([]);
        showMethodsMessage(`<small style="color:#ff6b6b">No se pudieron obtener las opciones de envío.</small>`);
        return;
      }

      showMethodsMessage(""); // limpia mensaje
      renderRates(res.data?.rates || []);
      log("🚚 Rates recibidos:", res.data?.rates || []);
    }).fail(function (xhr) {
      renderRates([]);
      showMethodsMessage(`<small style="color:#ff6b6b">Error consultando envío: ${escapeHtml(xhr.responseText || "")}</small>`);
    });
  }

  $(document).on("submit", CFG.selectors.calcForm, function (e) {
    e.preventDefault();

    const $form = $(this);

    group("📦 GUARDAR DIRECCIÓN + TRAER RATES");
    startTotalLoading();

    $.ajax({
      url: ajaxUrl(CFG.endpoints.saveAddress),
      type: "POST",
      data: serializeAddress($form),
    })
      .done(function () {
        log("✅ Dirección guardada");
        return fetchRates();
      })
      .always(function () {
        stopTotalLoading();
        groupEnd();
      });
  });

  /* ==========================================================
    Selección de rate (radio)
    1) guardar rate elegido en sesión
    2) refrescar fragments (total)
  ========================================================== */

  let selectTimeout = null;

  $(document).on("change", 'input[name="waves_rate"]', function () {
    const rateId = $(this).val();
    if (!rateId) return;

    clearTimeout(selectTimeout);
    selectTimeout = setTimeout(function () {

      group("🚚 SELECCIONAR RATE");
      log("Rate elegido:", rateId);
      startTotalLoading();

      $.ajax({
        url: ajaxUrl(CFG.endpoints.selectRate),
        type: "POST",
        dataType: "json",
        data: {
          security: window.waves_wc.nonce || "",
          rate_id: rateId
        }
      })
        .done(function (res) {
          if (!res || res.success !== true) {
            console.error("❌ No se pudo guardar el rate");
            return;
          }
          log("✅ Rate guardado en sesión");
          return updateFragments();
        })
        .always(function () {
          stopTotalLoading();
          groupEnd();
        });

    }, 150);
  });

  /* ==========================================================
    Si ya hay dirección cargada al entrar, podés auto-traer rates.
    (Opcional) Descomentá si querés:
  ========================================================== */

  // fetchRates();

});
  /* ==========================================================
    MODALS (Retiro / Domicilio)
  ========================================================== */

  function openModal(id){
    const $m = $('#' + id);
    $m.addClass('is-open').attr('aria-hidden','false');
  }
  function closeModal($m){
    $m.removeClass('is-open').attr('aria-hidden','true');
  }

  $(document).on('click', '[data-open]', function(){
    openModal($(this).data('open'));
  });

  $(document).on('click', '.waves-modal [data-close]', function(){
    closeModal($(this).closest('.waves-modal'));
  });

  // click fuera del panel
  $(document).on('click', '.waves-modal-backdrop', function(){
    closeModal($(this).closest('.waves-modal'));
  });

  function setHiddenShippingFields(data){
    const $form = $('.woocommerce-shipping-calculator.waves-hidden-form');

    $form.find('[name="calc_shipping_country"]').val('AR');
    $form.find('[name="calc_shipping_state"]').val(data.state || '');
    $form.find('[name="calc_shipping_city"]').val(data.city || '');
    $form.find('[name="calc_shipping_postcode"]').val(data.postcode || '');

    // extras
    $form.find('[name="calc_shipping_address_1"]').val(data.address_1 || '');
    $form.find('[name="calc_shipping_address_2"]').val(data.address_2 || '');

    $form.trigger('submit');
  }

  function readModalFields($modal){
    return {
      state: $modal.find('[data-field="state"]').val(),
      city: $modal.find('[data-field="city"]').val(),
      postcode: $modal.find('[data-field="postcode"]').val(),
      address_1: $modal.find('[data-field="address_1"]').val(),
      address_2: $modal.find('[data-field="address_2"]').val()
    };
  }

  function markActiveCard(which){
    $('.waves-shipcard').removeClass('is-active');
    if (which === 'retiro') $('.waves-shipcard[data-open="modal-retiro"]').addClass('is-active');
    if (which === 'domicilio') $('.waves-shipcard[data-open="modal-domicilio"]').addClass('is-active');
  }

  $(document).on('click', '[data-confirm="retiro"]', function(){
    const $m = $('#modal-retiro');
    const data = readModalFields($m);

    if (!data.state || !data.city || !data.postcode){
      alert('Completá provincia, ciudad y código postal.');
      return;
    }

    data.address_1 = '';
    data.address_2 = '';

    markActiveCard('retiro');
    closeModal($m);
    setHiddenShippingFields(data);
  });

  $(document).on('click', '[data-confirm="domicilio"]', function(){
    const $m = $('#modal-domicilio');
    const data = readModalFields($m);

    if (!data.state || !data.city || !data.postcode || !data.address_1){
      alert('Completá provincia, ciudad, código postal y dirección.');
      return;
    }

    markActiveCard('domicilio');
    closeModal($m);
    setHiddenShippingFields(data);
  });

  jQuery(function($){

  const $panels = $('.waves-shipform-panel');
  const $alert  = $('.waves-shipform-alert');
  const $hidden = $('.waves-hidden-form');

  function showAlert(msg, type='error'){
    $alert
      .removeClass('is-error is-ok')
      .addClass('is-show')
      .addClass(type === 'ok' ? 'is-ok' : 'is-error')
      .text(msg);
  }
  function clearAlert(){ $alert.removeClass('is-show is-error is-ok').text(''); }

  function setActiveType(type){
    $('.waves-shipcard').removeClass('is-active');
    $('.waves-shipcard[data-type="'+type+'"]').addClass('is-active');

    $panels.removeClass('is-open');
    $panels.filter('[data-type="'+type+'"]').addClass('is-open');

    $hidden.find('[name="waves_ship_type"]').val(type);
    clearAlert();
  }

  // init
  setActiveType($('.waves-shipcard.is-active').data('type') || 'domicilio');

  // click en Retiro / Domicilio
  $(document).on('click', '.waves-shipcard', function(){
    setActiveType($(this).data('type'));
  });

  function collectPanelData($panel){
    const data = {};
    $panel.find('[data-field]').each(function(){
      data[$(this).data('field')] = ($(this).val() || '').trim();
    });
    return data;
  }

  function validate(type, data){
    const required = ['first_name','last_name','dni','phone','state','city','postcode'];
    if(type === 'domicilio') required.push('address_1');
    return required.filter(k => !data[k]);
  }

  function syncHidden(type, data){
    $hidden.find('[name="calc_shipping_country"]').val('AR');
    $hidden.find('[name="calc_shipping_state"]').val(data.state || '');
    $hidden.find('[name="calc_shipping_city"]').val(data.city || '');
    $hidden.find('[name="calc_shipping_postcode"]').val(data.postcode || '');

    if(type === 'domicilio'){
      $hidden.find('[name="calc_shipping_address_1"]').val(data.address_1 || '');
      $hidden.find('[name="calc_shipping_address_2"]').val(data.address_2 || '');
    }else{
      $hidden.find('[name="calc_shipping_address_1"]').val('');
      $hidden.find('[name="calc_shipping_address_2"]').val('');
    }
  }

  // Guardar y cotizar
  $(document).on('click', '.waves-ship-apply', function(){
    clearAlert();

    const type = $(this).data('apply');
    const $panel = $('.waves-shipform-panel[data-type="'+type+'"]');
    const data = collectPanelData($panel);

    const missing = validate(type, data);
    if(missing.length){
      showAlert('Completá los campos obligatorios para cotizar el envío.', 'error');
      return;
    }

    syncHidden(type, data);

    // Cotizar: submit del form oculto (Woo guarda en sesión y recalcula)
    showAlert('Datos guardados. Cotizando envío…', 'ok');
    $hidden[0].submit();
  });

});
