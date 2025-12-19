jQuery(function ($) {

    function collectFilters() {
        let filters = {};

        $('.filter-term:checked').each(function () {
            const taxonomy = $(this).data('taxonomy');
            const value    = $(this).val();

            if (!filters[taxonomy]) {
                filters[taxonomy] = [];
            }

            filters[taxonomy].push(value);
        });

        return filters;
    }

    function applyFilters(page = 1) {

        const filters = collectFilters();
        const price   = $('#price-filter').val() || null;

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

                // 🔥 Re-inicializar product cards
                if (window.initProductCard) {
                    window.initProductCard();
                }
            }
        });
    }

    /* =====================
       EVENTOS
    ===================== */

    $(document).on('change', '.filter-term', function () {
        applyFilters();
    });

    $('#price-filter').on('input change', function () {
        $('#price-output').text('Hasta $' + $(this).val());
        applyFilters();
    });

    /* =====================
       URL → FILTROS
       ?pa_marca=nike,adidas&pa_talle=40,41
    ===================== */

    function applyFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);

        params.forEach((value, key) => {
            const values = value.split(',');

            values.forEach(val => {
                $(`.filter-term[data-taxonomy="${key}"][value="${val}"]`)
                    .prop('checked', true);
            });
        });

        if (params.toString()) {
            applyFilters();
        }
    }

    applyFiltersFromURL();

});
