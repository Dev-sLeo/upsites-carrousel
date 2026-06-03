(function ($) {
  "use strict";

  var AccordionSlider = {

    init: function ($scope) {
      $scope.find(".upsites-accordion-wrapper").each(function () {
        AccordionSlider.setup($(this));
      });
    },

    applyResponsive: function (card, mobile) {
      var logo = card.querySelector(".upsites-accordion-slide__logo");
      if (logo) {
        var logoImg  = logo.querySelector("img");
        var widthKey = mobile ? "logoWidthMobile" : "logoWidth";
        var width    = logo.dataset[widthKey];
        if (width && logoImg) logoImg.style.maxWidth = width + "px";
      }
      var bg    = card.querySelector(".upsites-accordion-slide__bg");
      var bgPos = mobile ? card.dataset.bgPosMobile : null;
      if (bg && bgPos) bg.style.backgroundPosition = bgPos;
    },

    computeWidths: function (slider, n, gap) {
      var available = slider.offsetWidth - gap * (n - 1);
      var ratio     = 740 / 150;
      var inactive  = available / (ratio + n - 1);
      var active    = ratio * inactive;
      return {
        aberto:  Math.round(active)   + "px",
        fechado: Math.round(inactive) + "px",
      };
    },

    // ── Desktop accordion ────────────────────────────────
    setupDesktop: function (wrapper, slider, cards, defaultActive, widths) {
      var widthFechado = widths.fechado;
      var widthAberto  = widths.aberto;

      // Initial state
      cards.forEach(function (card, i) {
        var bg   = card.querySelector(".upsites-accordion-slide__bg");
        var logo = card.querySelector(".upsites-accordion-slide__logo");

        if (i === defaultActive) {
          gsap.set(card, { width: widthAberto });
          gsap.set(card.querySelector(".upsites-accordion-slide__content"), {
            display: "flex", justifyContent: "flex-end",
            alignItems: "flex-start", opacity: 1, y: 0,
          });
          gsap.set(card.querySelector(".upsites-accordion-slide__arrow"), { opacity: 1, scale: 1 });
          if (bg)   gsap.set(bg,   { scale: 1 });
          if (logo) gsap.set(logo, { justifyContent: "flex-start", alignItems: "flex-end", opacity: 1 });
          card.classList.add("is-active");
        } else {
          gsap.set(card, { width: widthFechado });
          gsap.set(card.querySelector(".upsites-accordion-slide__content"), { opacity: 0, display: "none" });
          gsap.set(card.querySelector(".upsites-accordion-slide__arrow"),   { opacity: 0, scale: 0.7 });
          if (bg)   gsap.set(bg,   { scale: 1 });
          if (logo) gsap.set(logo, { justifyContent: "center", alignItems: "center", opacity: 1 });
        }

        // Zoom on hover
        if (bg) {
          card.addEventListener("mouseenter", function () {
            gsap.to(bg, { scale: 1.2, duration: 0.5, ease: "power2.out" });
          });
          card.addEventListener("mouseleave", function () {
            gsap.to(bg, { scale: 1, duration: 0.5, ease: "power2.out" });
          });
        }
      });

      // Hover: expand / collapse
      cards.forEach(function (card) {
        card.addEventListener("mouseenter", function () {
          if (card.classList.contains("is-active")) return;

          var prevCard = slider.querySelector(".upsites-accordion-slide.is-active");
          if (prevCard) {
            prevCard.classList.remove("is-active");
            gsap.to(prevCard, { width: widthFechado, duration: 0.6, ease: "power2.out" });

            // Content: fade out
            gsap.to(prevCard.querySelector(".upsites-accordion-slide__content"), {
              opacity: 0, duration: 0.15,
              onComplete: function () {
                gsap.set(prevCard.querySelector(".upsites-accordion-slide__content"), { display: "none" });
              },
            });

            // Arrow: kill + hide instantly
            var prevArrow = prevCard.querySelector(".upsites-accordion-slide__arrow");
            gsap.killTweensOf(prevArrow);
            gsap.set(prevArrow, { opacity: 0, scale: 0.7 });

            // Logo: kill any running tween, snap back to center instantly
            var prevLogo = prevCard.querySelector(".upsites-accordion-slide__logo");
            if (prevLogo) {
              gsap.killTweensOf(prevLogo);
              gsap.set(prevLogo, { justifyContent: "center", alignItems: "center", opacity: 1 });
            }
          }

          card.classList.add("is-active");
          gsap.to(card, { width: widthAberto, duration: 0.6, ease: "power2.out" });

          // Logo: snap to bottom-left position, then fade in
          var logo = card.querySelector(".upsites-accordion-slide__logo");
          if (logo) {
            gsap.killTweensOf(logo);
            gsap.set(logo, { justifyContent: "flex-start", alignItems: "flex-end", opacity: 0 });
            gsap.to(logo, { opacity: 1, duration: 0.35, delay: 0.2, ease: "power2.out" });
          }

          // Content: fade-up
          var content = card.querySelector(".upsites-accordion-slide__content");
          gsap.set(content, { display: "flex", opacity: 0, y: 16 });
          gsap.to(content,  { opacity: 1, y: 0, duration: 0.4, delay: 0.2, ease: "power2.out" });

          // Arrow
          var arrow = card.querySelector(".upsites-accordion-slide__arrow");
          gsap.killTweensOf(arrow);
          gsap.to(arrow, {
            opacity: 1, scale: 1, duration: 0.4, delay: 0.25, ease: "power2.out",
            onStart: function () {
              if (!card.classList.contains("is-active")) gsap.set(arrow, { opacity: 0, scale: 0.7 });
            },
          });
        });
      });
    },

    // ── Mobile tab bar ───────────────────────────────────
    setupMobile: function (wrapper, cards, tabs, defaultActive) {
      var currentIndex = defaultActive;

      function activate(nextIndex) {
        if (nextIndex === currentIndex) return;

        var prevCard    = cards[currentIndex];
        var nextCard    = cards[nextIndex];
        var prevContent = prevCard.querySelector(".upsites-accordion-slide__content");
        var prevArrow   = prevCard.querySelector(".upsites-accordion-slide__arrow");

        // Fade-down exit
        gsap.to([prevContent, prevArrow], {
          opacity: 0, y: 20, duration: 0.22, ease: "power2.in",
          onComplete: function () {
            prevCard.classList.remove("is-active");
            tabs[currentIndex].classList.remove("is-active");

            currentIndex = nextIndex;

            nextCard.classList.add("is-active");
            tabs[nextIndex].classList.add("is-active");
            AccordionSlider.applyResponsive(nextCard, true);

            var nextContent = nextCard.querySelector(".upsites-accordion-slide__content");
            var nextArrow   = nextCard.querySelector(".upsites-accordion-slide__arrow");

            // Fade-up enter
            gsap.fromTo(
              [nextContent, nextArrow],
              { opacity: 0, y: -20 },
              { opacity: 1, y: 0, duration: 0.32, ease: "power2.out" }
            );
          },
        });
      }

      // Set initial active state
      cards.forEach(function (card, i) {
        card.classList.toggle("is-active", i === currentIndex);
        AccordionSlider.applyResponsive(card, true);
      });
      tabs.forEach(function (tab, i) {
        tab.classList.toggle("is-active", i === currentIndex);
      });

      // Set initial content visible (no animation on load)
      var initContent = cards[currentIndex].querySelector(".upsites-accordion-slide__content");
      var initArrow   = cards[currentIndex].querySelector(".upsites-accordion-slide__arrow");
      gsap.set([initContent, initArrow], { opacity: 1, y: 0 });

      tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
          activate(parseInt(tab.dataset.index, 10));
        });
      });
    },

    setup: function ($wrapper) {
      var defaultActive = parseInt($wrapper.data("default-active"), 10) || 0;
      var wrapper       = $wrapper[0];
      var slider        = wrapper.querySelector(".upsites-accordion-slider");
      var cards         = Array.from(slider.querySelectorAll(".upsites-accordion-slide"));
      var tabs          = Array.from(wrapper.querySelectorAll(".upsites-accordion-tab"));

      // CSS overlay variables
      var overlayStart = $wrapper.data("overlay-start");
      var overlayEnd   = $wrapper.data("overlay-end");
      if (overlayStart) wrapper.style.setProperty("--upsites-overlay-start", overlayStart);
      if (overlayEnd)   wrapper.style.setProperty("--upsites-overlay-end",   overlayEnd);

      cards.forEach(function (card) {
        var activeColor = card.dataset.overlayActive;
        if (activeColor) card.style.setProperty("--upsites-overlay-active", activeColor);
      });

      var isMobile = window.matchMedia("(max-width: 1024px)");
      var gap      = parseInt(window.getComputedStyle(slider).gap) || 20;
      var widths   = AccordionSlider.computeWidths(slider, cards.length, gap);

      if (isMobile.matches) {
        AccordionSlider.setupMobile(wrapper, cards, tabs, defaultActive);
      } else {
        AccordionSlider.setupDesktop(wrapper, slider, cards, defaultActive, widths);
      }

      // Breakpoint switch
      isMobile.addEventListener("change", function (e) {
        cards.forEach(function (card) {
          gsap.set(card, { clearProps: "all" });
          gsap.set(card.querySelector(".upsites-accordion-slide__content"), { clearProps: "all" });
          gsap.set(card.querySelector(".upsites-accordion-slide__arrow"),   { clearProps: "all" });
          var logo = card.querySelector(".upsites-accordion-slide__logo");
          if (logo) gsap.set(logo, { clearProps: "all" });
          card.classList.remove("is-active");
        });
        tabs.forEach(function (tab) { tab.classList.remove("is-active"); });

        var newWidths = AccordionSlider.computeWidths(slider, cards.length, gap);
        if (e.matches) {
          AccordionSlider.setupMobile(wrapper, cards, tabs, defaultActive);
        } else {
          AccordionSlider.setupDesktop(wrapper, slider, cards, defaultActive, newWidths);
        }
      });

      // Resize: recalculate desktop widths
      var resizeTimer;
      window.addEventListener("resize", function () {
        if (isMobile.matches) return;
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
          var w = AccordionSlider.computeWidths(slider, cards.length, gap);
          cards.forEach(function (card) {
            gsap.set(card, { width: card.classList.contains("is-active") ? w.aberto : w.fechado });
          });
          widths = w;
        }, 100);
      });
    },
  };

  $(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/upsites-accordion-slider.default",
      function ($scope) { AccordionSlider.init($scope); }
    );
  });

  $(document).ready(function () {
    if (typeof elementorFrontend === "undefined") {
      AccordionSlider.init($("body"));
    }
  });
})(jQuery);
