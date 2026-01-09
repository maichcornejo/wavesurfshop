console.log("🔥 brand.js cargado");

function initBrandCarousel() {
  document.querySelectorAll('.premium-brand-carousel[data-brand-carousel="1"]').forEach((carousel) => {
    if (carousel.dataset.inited === "1") return; // evita doble init
    carousel.dataset.inited = "1";

    const track = carousel.querySelector(".brand-track");
    const prevBtn = carousel.querySelector(".brand-prev");
    const nextBtn = carousel.querySelector(".brand-next");

    if (!track || !prevBtn || !nextBtn) return;

    let items = Array.from(track.children).filter(Boolean);
    if (items.length < 2) return; // no hace falta carrusel

    // Gap real desde CSS (soporta gap/column-gap)
    function getGapPx() {
      const cs = getComputedStyle(track);
      const gap = parseFloat(cs.columnGap || cs.gap || "0");
      return isNaN(gap) ? 0 : gap;
    }

    // Asegura que las imágenes hayan cargado para medir bien
    const firstImg = items[0].querySelector("img");
    const ready = firstImg && !firstImg.complete
      ? new Promise((res) => { firstImg.addEventListener("load", res, { once: true }); firstImg.addEventListener("error", res, { once: true }); })
      : Promise.resolve();

    ready.then(() => {
      // 🔁 Clonado para loop
      items.forEach(item => track.appendChild(item.cloneNode(true)));

      let position = 0;
      let isMoving = false;

      function computeMoveBy() {
        const itemWidth = items[0].getBoundingClientRect().width;
        return itemWidth + getGapPx();
      }

      function move(dir = 1) {
        if (isMoving) return;
        isMoving = true;

        const moveBy = computeMoveBy();
        position += moveBy * dir;

        track.style.transition = "transform 0.6s ease";
        track.style.transform = `translateX(-${position}px)`;

        setTimeout(() => {
          const half = track.children.length / 2;
          const limit = half * moveBy;

          if (position >= limit) {
            track.style.transition = "none";
            position = 0;
            track.style.transform = "translateX(0)";
          }

          if (position < 0) {
            track.style.transition = "none";
            position = (half - 1) * moveBy;
            track.style.transform = `translateX(-${position}px)`;
          }

          isMoving = false;
        }, 650);
      }

      nextBtn.addEventListener("click", () => move(1));
      prevBtn.addEventListener("click", () => move(-1));

      // autoplay
      const timer = setInterval(() => move(1), 3000);

      // si el carrusel se elimina del DOM, limpiamos el interval
      const obs = new MutationObserver(() => {
        if (!document.body.contains(carousel)) {
          clearInterval(timer);
          obs.disconnect();
        }
      });
      obs.observe(document.body, { childList: true, subtree: true });

      // recalcular suave en resize
      window.addEventListener("resize", () => {
        track.style.transition = "none";
        track.style.transform = `translateX(-${position}px)`;
      });
    });
  });
}

if (document.readyState === "complete") {
  initBrandCarousel();
} else {
  window.addEventListener("load", initBrandCarousel);
}
