console.log("STORY SCROLL FIXED ✅");

const section = document.querySelector(".story-section");
const track = document.querySelector(".story-track");
const scenes = document.querySelectorAll(".story-scene");

if (section && track && scenes.length) {

  window.addEventListener("scroll", () => {

    const rect = section.getBoundingClientRect();
    const scrollable = rect.height - window.innerHeight;
    const scrolled = Math.min(Math.max(-rect.top, 0), scrollable);
    const progress = scrollable > 0 ? scrolled / scrollable : 0;

    /* =========================
       TRACK — mueve escenas
       ========================= */

    const maxTranslate = (scenes.length - 1) * 110;
    track.style.transform = `translateY(-${progress * maxTranslate}vh)`;

    /* =========================
       ESCENAS
       ========================= */

    scenes.forEach((scene, index) => {

      const sceneDuration = 0.6;
      const sceneGap = 0.05;

      const start = index * (sceneDuration - sceneGap);
      const end = start + sceneDuration;

      const p = Math.min(
        Math.max((progress - start) / (end - start), 0),
        1
      );

      const img = scene.querySelector("img");
      const media = scene.querySelector(".story-media");
      const mainText = scene.querySelector(".story-text-main");
      const secondary = scene.querySelector(".story-text-secondary");

      /* =========================
         VALORES BASE
         ========================= */

      let mainOpacity = 1;
      let mainY = 0;

      let secondaryOpacity = 0;
      let secondaryY = 40;
      let secondaryScale = 0.95;

      let edgeShadow = 0;

      /* =========================
         FASE 1 — INTRO
         ========================= */

      if (p < 0.35) {
        img.classList.remove("effect");
        edgeShadow = 0;
      }

      /* =========================
         FASE 2 — HERO (zoom cool)
         ========================= */

      else if (p < 0.85) {

        let t = (p - 0.35) / 0.5;
        t = Math.min(Math.max(t, 0), 1);

        const ease = t * t * (3 - 2 * t);

        img.classList.add("effect");

        mainOpacity = 1 - ease;
        mainY = -24 * ease;

        secondaryOpacity = ease;
        secondaryY = (1 - ease) * 40;
        secondaryScale = 0.95 + ease * 0.05;

        edgeShadow = ease;

        scene.classList.toggle("is-secondary", p > 0.5);
      }

      /* =========================
         FASE 3 — SALIDA
         ========================= */

      else {

        img.classList.remove("effect");

        const t = (p - 0.85) / 0.15;

        mainOpacity = 0;

        secondaryOpacity = 1 - t;
        secondaryY = 0;
        secondaryScale = 1;

        edgeShadow = 1 - t;
      }

      /* =========================
         APLICAR ESTADOS
         ========================= */

      media.style.setProperty("--edge-shadow", edgeShadow);

      mainText.style.setProperty("--main-opacity", mainOpacity);
      mainText.style.setProperty("--main-y", `${mainY}px`);

      secondary.style.setProperty("--secondary-opacity", secondaryOpacity);
      secondary.style.setProperty("--secondary-y", `${secondaryY}px`);
      secondary.style.setProperty("--secondary-scale", secondaryScale);
    });
  });
}
