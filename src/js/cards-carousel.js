import "../scss/cards-carousel.scss";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

const STEP_PX = 32; // px between stacked card tops

function setup(container) {
  const wrappers = Array.from(
    container.querySelectorAll(".upsites-cc-card-wrapper")
  );
  const total = wrappers.length;
  if (total <= 1) return;

  // ── Layout: position wrappers absolutely so they overlap ──────
  const cardH      = wrappers[0].offsetHeight;
  const containerH = cardH + STEP_PX * (total - 1);

  gsap.set(container, { height: containerH, position: "relative" });
  wrappers.forEach((wrapper, i) => {
    gsap.set(wrapper, {
      position:        "absolute",
      top:             STEP_PX * i,
      left:            0,
      right:           0,
      zIndex:          i + 1,
      transformOrigin: "top center", // scale compresses down, top stays fixed
    });
  });

  // ── Start Y for each card entering from below ─────────────────
  const getStartY = (i) => {
    const vh      = window.innerHeight || 800;
    const base    = vh * 1.1;    // 110% of viewport
    const spacing = vh * 0.35;  // distance between cards
    return Math.round(base + (i - 1) * spacing);
  };

  // ── Render: drive all positions from a single progress value ──
  // pSteps goes 0 → (total - 1)
  const render = (pSteps) => {
    wrappers.forEach((wrapper, i) => {

      // ─ Y translation (card entering from below) ─
      if (i === 0) {
        gsap.set(wrapper, { y: 0 });
      } else {
        const local = pSteps - (i - 1); // 0..1 while card i enters
        const t     = Math.max(0, Math.min(1, local));
        gsap.set(wrapper, { y: getStartY(i) * (1 - t) });
      }

      // ─ Scale + Opacity ─
      // scaleStep divides the range 0.9 → 1.0 equally across all cards
      // so every adjacent pair has the same scale difference
      const scaleStep = (total - 1) > 0 ? 0.1 / (total - 1) : 0.1;

      if (i < total - 1) {
        // Buried cards: scale down and fade as the next card enters
        const tBury        = Math.max(0, Math.min(1, pSteps - i));
        const finalScale   = 1.0 - (total - 1 - i) * scaleStep;
        const finalOpacity = (i + 1) / total;
        const tOpacity     = Math.max(0, Math.min(1, (tBury - 0.75) / 0.25));
        gsap.set(wrapper, {
          scale:   1 - tBury * (1 - finalScale),
          opacity: 1 - tOpacity * (1 - finalOpacity),
        });
      } else {
        // Last card: scale in from one step below 1.0 (matches the card beneath it)
        const tEnter     = i === 0 ? 1 : Math.max(0, Math.min(1, pSteps - (i - 1)));
        const startScale = 1.0 - scaleStep;
        gsap.set(wrapper, {
          scale:   startScale + tEnter * (1 - startScale),
          opacity: 1,
        });
      }
    });
  };

  // Initial state: all cards below except the first
  render(0);

  const perStep = () => Math.round(window.innerHeight * 0.9);

  const getTopOffset = () => {
    const w = window.innerWidth;
    if (w <= 767)  return parseInt(container.dataset.topOffsetMobile, 10) || 0;
    if (w <= 1024) return parseInt(container.dataset.topOffsetTablet, 10) || 0;
    return parseInt(container.dataset.topOffset, 10) || 0;
  };

  const st = ScrollTrigger.create({
    trigger:             container,
    pin:                 container,
    start:               () => `top top+=${getTopOffset()}`,
    end:                 () => `+=${perStep() * (total - 1)}`,
    scrub:               0.25,
    anticipatePin:       1,
    invalidateOnRefresh: true,
    onUpdate(self) {
      render(self.progress * (total - 1));
    },
  });

  const syncNow = () => render(st.progress * (total - 1));

  ScrollTrigger.addEventListener("refresh", syncNow);

  requestAnimationFrame(() => {
    ScrollTrigger.refresh();
    syncNow();
  });
}

function init() {
  document
    .querySelectorAll(".upsites-cards-carousel:not([data-carousel-init])")
    .forEach((container) => {
      container.dataset.carouselInit = "1";
      setup(container);
    });
}

// ── Elementor integration (no jQuery needed) ──────────────────────
if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) {
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/upsites-cards-carousel.default",
    ($scope) => {
      const container = $scope[0].querySelector(".upsites-cards-carousel");
      if (container && !container.dataset.carouselInit) {
        container.dataset.carouselInit = "1";
        setup(container);
      }
    }
  );
}

// Fallback: plain site or Elementor already rendered
document.addEventListener("DOMContentLoaded", init);
window.addEventListener("load", () => {
  init();
  ScrollTrigger.refresh();
});
