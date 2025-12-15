document.addEventListener('DOMContentLoaded', () => {

    /* ===== CAMBIO DE IMAGEN POR COLOR ===== */
    document.querySelectorAll('.product-card').forEach(card => {

        const img = card.querySelector('.img-main');
        const dots = card.querySelectorAll('.color-dot');
        if (!img || !dots.length) return;

        const original = img.src;

        dots.forEach(dot => {
            const preload = new Image();
            preload.src = dot.dataset.image;

            const change = () => {
                img.src = dot.dataset.image;
                img.removeAttribute('srcset');
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

});
