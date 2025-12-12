jQuery(function($){

    // Evita que el formulario recargue la página
    $(document).on('submit', 'form.woocommerce-cart-form', function(e){
        e.preventDefault();
    });

    function ajaxUpdateCart() {
        let form = $('form.woocommerce-cart-form');
        console.log("📡 Enviando AJAX update_cart…");

        $.ajax({
            url: wc_cart_params.wc_ajax_url.replace('%%endpoint%%', 'update_cart'),
            type: 'POST',
            data: form.serialize(),
            success: function() {
                console.log("✅ update_cart OK, recargando HTML...");

                $.ajax({
                    url: wc_cart_params.wc_ajax_url.replace('%%endpoint%%', 'get_cart'),
                    type: 'GET',
                    success: function(response) {

                        if (response && response.fragments) {

                            // 🔥 reemplazar todo fragmento devuelto por WooCommerce
                            $.each(response.fragments, function(selector, html) {
                                $(selector).replaceWith(html);
                            });

                            // volver a vincular los botones
                            $(document.body).trigger('updated_wc_div');
                        }
                    }
                });
            }
        });

    }


    function getQtyInput(btn) {
        return btn.closest('.qty-wrapper').find('input');
    }

    function ensureButtons() {
        $('.qty-wrapper').each(function() {
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

        $(document).off('click', '.qty-plus').on('click', '.qty-plus', function() {
            let input = getQtyInput($(this));
            let val = parseInt(input.val()) || 0;
            let max = parseInt(input.attr('max')) || 9999;

            input.val(Math.min(val + 1, max)).trigger('change');
        });

        $(document).off('click', '.qty-minus').on('click', '.qty-minus', function() {
            console.log("Click en +");
            let input = getQtyInput($(this));
            let val = parseInt(input.val()) || 0;
            let min = parseInt(input.attr('min')) || 0;

            input.val(Math.max(val - 1, min)).trigger('change');
        });

        $(document).off('change', '.qty-wrapper input').on('change', '.qty-wrapper input', function() {
            console.log("Cambio detectado → actualizando carrito");
            triggerCartUpdate();
        });
    }

    ensureButtons();
    bindEvents();

    $(document.body).on('updated_cart_totals updated_wc_div', function(){
        ensureButtons();
        bindEvents();
    });

});
