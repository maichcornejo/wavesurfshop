document.addEventListener("DOMContentLoaded", () => {
  const carousels = document.querySelectorAll('[data-wc-carousel]');

  carousels.forEach((section) => {
    const track = section.querySelector(".wc-track");
    const prev = section.querySelector(".wc-arrow.prev");
    const next = section.querySelector(".wc-arrow.next");
    if (!track || !prev || !next) return;

    const getStep = () => {
      const firstCard =
        track.querySelector(".product-card") ||
        track.querySelector("li.product") ||
        track.children[0];

      if (!firstCard) return 320;

      const cardW = firstCard.getBoundingClientRect().width;
      const gap = parseFloat(getComputedStyle(track).gap || "0") || 0;
      return cardW + gap;
    };

    const updateArrows = () => {
      const maxScroll = track.scrollWidth - track.clientWidth;
      const x = track.scrollLeft;

      prev.disabled = x <= 2;
      next.disabled = x >= maxScroll - 2;
    };

    const scrollByDir = (dir) => {
      track.scrollBy({ left: dir * getStep(), behavior: "smooth" });
    };

    prev.addEventListener("click", () => scrollByDir(-1));
    next.addEventListener("click", () => scrollByDir(1));

    let raf = null;
    track.addEventListener("scroll", () => {
      if (raf) cancelAnimationFrame(raf);
      raf = requestAnimationFrame(updateArrows);
    });

    window.addEventListener("resize", updateArrows);
    updateArrows();
  });
});
