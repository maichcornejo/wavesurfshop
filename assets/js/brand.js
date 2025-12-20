console.log("🔥 brand.js cargado");

function initBrandCarousel() {
    console.log("✅ initBrandCarousel ejecutado");

    const carousel = document.querySelector(".premium-brand-carousel");
    if (!carousel) {
        console.warn("⛔ carrusel no encontrado");
        return;
    }

    const track = carousel.querySelector(".brand-track");
    const prevBtn = carousel.querySelector(".brand-prev");
    const nextBtn = carousel.querySelector(".brand-next");

    if (!track || !prevBtn || !nextBtn) {
        console.warn("⛔ elementos internos faltantes");
        return;
    }

    let items = Array.from(track.children);
    console.log("items antes:", items.length);

    const itemWidth = items[0].offsetWidth;
    const gap = 80;
    const moveBy = itemWidth + gap;

    // 🔁 CLONADO PARA LOOP
    items.forEach(item => track.appendChild(item.cloneNode(true)));

    console.log("items después:", track.children.length);

    let position = 0;
    let isMoving = false;

    function move(dir = 1) {
        if (isMoving) return;
        isMoving = true;

        position += moveBy * dir;
        track.style.transition = "transform 0.6s ease";
        track.style.transform = `translateX(-${position}px)`;

        setTimeout(() => {
            if (position >= (track.children.length / 2) * moveBy) {
                track.style.transition = "none";
                position = 0;
                track.style.transform = "translateX(0)";
            }

            if (position < 0) {
                track.style.transition = "none";
                position = (track.children.length / 2 - 1) * moveBy;
                track.style.transform = `translateX(-${position}px)`;
            }

            isMoving = false;
        }, 650);
    }

    nextBtn.addEventListener("click", () => move(1));
    prevBtn.addEventListener("click", () => move(-1));

    setInterval(() => move(1), 3000);
}

// ✅ Ejecutar SIEMPRE, aunque load ya haya ocurrido
if (document.readyState === "complete") {
    initBrandCarousel();
} else {
    window.addEventListener("load", initBrandCarousel);
}
