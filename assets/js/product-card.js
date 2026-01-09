(function () {

  /* ===============================
     CAMBIO DE IMAGEN POR COLOR
  =============================== */
  function initProductCard(root = document) {
    root.querySelectorAll('.product-card').forEach(card => {

      // Evitar duplicar eventos
      if (card.dataset.init === 'true') return;
      card.dataset.init = 'true';

      const img  = card.querySelector('.img-main');
      const dots = card.querySelectorAll('.color-dot');
      if (!img || !dots.length) return;

      const originalSrc = img.getAttribute('src');
      let selectedSrc = null;

      dots.forEach(dot => {
        const imgSrc = dot.dataset.image;
        if (!imgSrc) return;

        // Preload
        const preload = new Image();
        preload.src = imgSrc;

        // HOVER (preview)
        dot.addEventListener('mouseenter', () => {
          img.src = imgSrc;
          img.removeAttribute('srcset');
        });

        // CLICK (selección fija)
        dot.addEventListener('click', () => {
          selectedSrc = imgSrc;

          img.src = imgSrc;
          img.removeAttribute('srcset');

          dots.forEach(d => d.classList.remove('active'));
          dot.classList.add('active');
        });
      });

      // Salida del card
      card.addEventListener('mouseleave', () => {
        img.src = selectedSrc || originalSrc;
        img.removeAttribute('srcset');
      });
    });
  }

  /* ===============================
     CARRUSEL 4 PRODUCTOS (1 sola vez)
  =============================== */
  function initCarouselOnce() {
    const track = document.querySelector('.wc-track');
    const next  = document.querySelector('.wc-arrow.next');
    const prev  = document.querySelector('.wc-arrow.prev');

    if (!track || !next || !prev) return;
    if (track.dataset.carouselInit === 'true') return;
    track.dataset.carouselInit = 'true';

    const firstCard = track.querySelector('.product-card');
    if (!firstCard) return;

    const gap = 30;
    const move = (firstCard.offsetWidth + gap) * 4;

    next.addEventListener('click', () => {
      track.scrollBy({ left: move, behavior: 'smooth' });
    });

    prev.addEventListener('click', () => {
      track.scrollBy({ left: -move, behavior: 'smooth' });
    });
  }

  // ✅ Exponer para que el AJAX lo pueda llamar
  window.initProductCard = initProductCard;

  document.addEventListener('DOMContentLoaded', () => {
    initProductCard(document);
    initCarouselOnce();
  });

})();
