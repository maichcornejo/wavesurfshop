jQuery(function ($) {

    // Evita que el formulario recargue la página
    $(document).on('submit', 'form.woocommerce-cart-form', function (e) {
        e.preventDefault();
    });

    


    function getQtyInput(btn) {
        return btn.closest('.qty-wrapper').find('input');
    }

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

    function triggerCartUpdate() {
        ajaxUpdateCart();
    }

    function bindEvents() {

        $(document).off('click', '.qty-plus').on('click', '.qty-plus', function () {
            let input = getQtyInput($(this));
            let val = parseInt(input.val()) || 0;
            let max = parseInt(input.attr('max')) || 9999;

            input.val(Math.min(val + 1, max)).trigger('change');
        });

        $(document).off('click', '.qty-minus').on('click', '.qty-minus', function () {
            console.log("Click en +");
            let input = getQtyInput($(this));
            let val = parseInt(input.val()) || 0;
            let min = parseInt(input.attr('min')) || 0;

            input.val(Math.max(val - 1, min)).trigger('change');
        });

        $(document).off('change', '.qty-wrapper input').on('change', '.qty-wrapper input', function () {
            console.log("Cambio detectado → actualizando carrito");
            triggerCartUpdate();
        });
    }

    ensureButtons();
    bindEvents();

    $(document.body).on('updated_cart_totals updated_wc_div', function () {
        ensureButtons();
        bindEvents();
    });

});
// 1️⃣ helpers / wrappers
function triggerCartUpdate() {
    if (typeof ajaxUpdateCart === 'function') {
        ajaxUpdateCart();
    }
}

// 2️⃣ lógica principal
jQuery(function ($) {

    let recalcTimeout = null;

    $(document).on('change', '.waves-shipping select, .shipping select', function () {

        clearTimeout(recalcTimeout);

        recalcTimeout = setTimeout(function () {

            console.log('📦 Cambio de sucursal detectado → recalculando envío');

            $.ajax({
                url: wavesCart.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'waves_force_recalc_shipping',
                    nonce: wavesCart.nonce
                },
                success: function (res) {
                    if (res && res.success) {
                        console.log('✅ Backend OK', res.data.rates);

                        const $btn = $('button[name="update_cart"]');
                        if ($btn.length) {
                            $btn.prop('disabled', false);
                            $btn.trigger('click');
                        }
                    } else {
                        console.error('❌ Backend respondió error', res);
                    }
                }


            });

        }, 600);
    });

    /**
     * Forzar fin de "Cargando sucursal..." del plugin de Correo Argentino
     */
    function clearCorreoArgentinoLoading() {

        // texto plano
        $('.shipping').find(':contains("Cargando sucursal")').each(function () {
            $(this).text('');
        });

        // loaders comunes
        $('.shipping-loading, .correoargentino-loading').hide();

        // select2 bloqueados
        $('.shipping select').prop('disabled', false);
    }
});


jQuery(function ($) {

    $(document.body).on('wc_fragment_refresh', function () {
        console.log('🧩 wc_fragment_refresh disparado');
    });

    $(document.body).on('updated_cart_totals', function () {
        console.log('🔁 updated_cart_totals disparado');
    });

    $(document.body).on('updated_wc_div', function () {
        console.log('🔄 updated_wc_div disparado');
    });

});

