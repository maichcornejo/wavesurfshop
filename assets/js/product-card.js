<<<<<<< HEAD
/**
 * Inicializa:
 * - Cambio de imagen por color
 * - Previene doble binding
 */
window.initProductCard = function () {

    document.querySelectorAll('.product-card').forEach(card => {

        // Evitar duplicar eventos
        if (card.dataset.init === 'true') return;
        card.dataset.init = 'true';

        /* ===== CAMBIO DE IMAGEN POR COLOR ===== */
        const img  = card.querySelector('.img-main');
        const dots = card.querySelectorAll('.color-dot');

=======
document.addEventListener('DOMContentLoaded', () => {

    /* ===== CAMBIO DE IMAGEN POR COLOR ===== */
    document.querySelectorAll('.product-card').forEach(card => {

        const img = card.querySelector('.img-main');
        const dots = card.querySelectorAll('.color-dot');
>>>>>>> e4d28d2 (empezando 15/12)
        if (!img || !dots.length) return;

        const original = img.src;

        dots.forEach(dot => {
<<<<<<< HEAD

=======
>>>>>>> e4d28d2 (empezando 15/12)
            const preload = new Image();
            preload.src = dot.dataset.image;

            const change = () => {
                img.src = dot.dataset.image;
                img.removeAttribute('srcset');
<<<<<<< HEAD

=======
>>>>>>> e4d28d2 (empezando 15/12)
                dots.forEach(d => d.classList.remove('active'));
                dot.classList.add('active');
            };

            dot.addEventListener('mouseenter', change);
            dot.addEventListener('click', change);
        });

        card.addEventListener('mouseleave', () => {
            img.src = original;
            img.removeAttribute('srcset');
            dots.forEach(d => d.classList.remove('active'));
        });
<<<<<<< HEAD

    });
};

/* ===== INIT AL CARGAR ===== */
document.addEventListener('DOMContentLoaded', () => {
    window.initProductCard();
=======
    });

    /* ===== CARRUSEL 4 PRODUCTOS ===== */
    const track = document.querySelector('.wc-track');
    const next = document.querySelector('.wc-arrow.next');
    const prev = document.querySelector('.wc-arrow.prev');

    if (!track || !next || !prev) return;

    const card = track.querySelector('.product-card');
    const gap = 30;
    const move = (card.offsetWidth + gap) * 4;

    next.addEventListener('click', () => {
        track.scrollBy({ left: move, behavior: 'smooth' });
    });

    prev.addEventListener('click', () => {
        track.scrollBy({ left: -move, behavior: 'smooth' });
    });

>>>>>>> e4d28d2 (empezando 15/12)
});
