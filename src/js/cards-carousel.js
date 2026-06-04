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

  const mm = gsap.matchMedia();

  mm.add("(min-width: 768px)", () => {
    // ── Layout: position wrappers absolutely so they overlap ──────
    const cardH     = wrappers[0].offsetHeight;
    const containerH = cardH + STEP_PX * (total - 1);

    gsap.set(container, { height: containerH, position: "relative" });
    wrappers.forEach((wrapper, i) => {
      gsap.set(wrapper, {
        position: "absolute",
        top:      STEP_PX * i,
        left:     0,
        right:    0,
        zIndex:   i + 1,
      });
    });

    // ── Start Y for each card entering from below ─────────────────
    const getStartY = (i) => {
      const vh      = window.innerHeight || 800;
      const base    = vh * 1.1;      // 110 % of viewport
      const spacing = vh * 0.35;     // distance between cards
      return Math.round(base + (i - 1) * spacing);
    };

    // ── Render: drive all positions from a single progress value ──
    // pSteps goes 0 → (total - 1)
    const render = (pSteps) => {
      wrappers.forEach((wrapper, i) => {

        // ─ Y translation ─
        if (i === 0) {
          gsap.set(wrapper, { y: 0 });
        } else {
          const local = pSteps - (i - 1); // 0..1 while card i enters
          const t     = Math.max(0, Math.min(1, local));
          gsap.set(wrapper, { y: getStartY(i) * (1 - t) });
        }

        // ─ Opacity: buried cards fade proportionally ─
        if (i < total - 1) {
          const tBury       = Math.max(0, Math.min(1, pSteps - i));
          const finalOpacity = (i + 1) / total;
          gsap.set(wrapper, { opacity: 1 - tBury * (1 - finalOpacity) });
        } else {
          gsap.set(wrapper, { opacity: 1 });
        }
      });
    };

    // Initial state: all cards below except the first
    render(0);

    const perStep = () => Math.round(window.innerHeight * 0.9);

    const st = ScrollTrigger.create({
      trigger:             container,
      pin:                 container,
      start:               "top top",
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

    // Cleanup when matchMedia condition changes
    return () => {
      ScrollTrigger.removeEventListener("refresh", syncNow);
      st.kill();
      wrappers.forEach((w) => gsap.set(w, { clearProps: "all" }));
      gsap.set(container, { clearProps: "all" });
    };
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
