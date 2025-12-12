jQuery(function($){

    function applyFilters(page = 1) {

        const genders = $('.filter-gender:checked').map((_, el) => el.value).get();
        const brands  = $('.filter-brand:checked').map((_, el) => el.value).get();
        const sizes   = $('.filter-size:checked').map((_, el) => el.value).get();
        const price   = $('#filter-price').val();

        $.ajax({
            url: waves_ajax.ajax_url,
            method: 'POST',
            data: {
                action: 'filter_products',
                nonce: waves_ajax.nonce,
                genders: genders,
                brands: brands,
                sizes: sizes,
                price: price,
                page: page
            },
            beforeSend: function() {
                $('#products-list').addClass('loading');
                $('#products-list').html('<div class="spinner"></div>');
            },
            success: function(response){
                $('#products-list').removeClass('loading');
                $('#products-list').html(response);
            }
        });
    }

    // EVENTOS
    $('.filter-gender, .filter-brand, .filter-size').on('change', function(){
        applyFilters();
    });

    $('#filter-price').on('input', function(){
        $('#price-value').text("Hasta $" + $(this).val());
        applyFilters();
    });

});

// Leer parámetros GET de la URL
function getUrlParam(param) {
    const url = new URL(window.location.href);
    return url.searchParams.get(param);
}

// Al cargar la página, aplicar filtros desde URL
jQuery(function($){

    // Aplicar género
    const genderParam = getUrlParam("gender");
    if (genderParam) {
        $('.filter-gender[value="'+genderParam+'"]').prop('checked', true);
    }

    // Aplicar marca
    const brandParam = getUrlParam("brand");
    if (brandParam) {
        $('.filter-brand[value="'+brandParam+'"]').prop('checked', true);
    }

    // Aplicar talle
    const sizeParam = getUrlParam("size");
    if (sizeParam) {
        $('.filter-size[value="'+sizeParam+'"]').prop('checked', true);
    }

    // Aplicar precio máximo desde URL (opcional)
    const priceParam = getUrlParam("max_price");
    if (priceParam) {
        $('#filter-price').val(priceParam);
        $('#price-value').text("Hasta $" + priceParam);
    }

    // Ejecutar AJAX automáticamente si hay parámetros
    if (genderParam || brandParam || sizeParam || priceParam) {
        applyFilters();
    }

});
