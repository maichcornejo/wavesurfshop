jQuery(function($){

    // Abrir menú móvil
    $('#waves-burger').on('click', function(){
        $('#waves-mobile-menu').addClass('open');
        $('body').addClass('no-scroll');
    });

    // Cerrar menú móvil
    $('#waves-mobile-close').on('click', function(){
        $('#waves-mobile-menu').removeClass('open');
        $('body').removeClass('no-scroll');
    });

    // Sticky header
    let lastScroll = 0;
    $(window).on('scroll', function(){
        let current = $(this).scrollTop();
        if(current > lastScroll){
            $('.waves-header-main').addClass('hidden');
        } else {
            $('.waves-header-main').removeClass('hidden');
        }
        lastScroll = current;
    });

});
