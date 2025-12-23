document.addEventListener('DOMContentLoaded', () => {

  /* ===== CAMBIO DE IMAGEN POR COLOR ===== */
  document.querySelectorAll('.product-card').forEach(card => {

    // Evitar duplicar eventos
    if (card.dataset.init === 'true') return;
    card.dataset.init = 'true';

    const img  = card.querySelector('.img-main');
    const dots = card.querySelectorAll('.color-dot');
    if (!img || !dots.length) return;

    const originalSrc = img.src;
    let selectedSrc  = null; // 🔥 imagen elegida por click

    dots.forEach(dot => {

      const imgSrc = dot.dataset.image;
      if (!imgSrc) return;

      // Preload
      const preload = new Image();
      preload.src = imgSrc;

      /* ===== HOVER (preview) ===== */
      dot.addEventListener('mouseenter', () => {
        img.src = imgSrc;
        img.removeAttribute('srcset');
      });

      /* ===== CLICK (selección fija) ===== */
      dot.addEventListener('click', () => {
        selectedSrc = imgSrc;

        img.src = imgSrc;
        img.removeAttribute('srcset');

        dots.forEach(d => d.classList.remove('active'));
        dot.classList.add('active');
      });

    });

    /* ===== SALIDA DEL CARD ===== */
    card.addEventListener('mouseleave', () => {
      if (selectedSrc) {
        img.src = selectedSrc; // 🔥 queda el color elegido
      } else {
        img.src = originalSrc; // vuelve al producto
      }

      img.removeAttribute('srcset');
    });

  });

  /* ===== CARRUSEL 4 PRODUCTOS ===== */
  const track = document.querySelector('.wc-track');
  const next = document.querySelector('.wc-arrow.next');
  const prev = document.querySelector('.wc-arrow.prev');

  if (!track || !next || !prev) return;

  const card = track.querySelector('.product-card');
  if (!card) return;

  const gap = 30;
  const move = (card.offsetWidth + gap) * 4;

  next.addEventListener('click', () => {
    track.scrollBy({ left: move, behavior: 'smooth' });
  });

  prev.addEventListener('click', () => {
    track.scrollBy({ left: -move, behavior: 'smooth' });
  });

});
