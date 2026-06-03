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

    // ── Desktop accordion setup ──────────────────────────
    setupDesktop: function (wrapper, slider, cards, defaultActive, widths) {
      var widthFechado = widths.fechado;
      var widthAberto  = widths.aberto;

      // Initial state
      cards.forEach(function (card, i) {
        if (i === defaultActive) {
          gsap.set(card, { width: widthAberto });
          gsap.set(card.querySelector(".upsites-accordion-slide__content"), {
            display: "flex", justifyContent: "flex-end",
            alignItems: "flex-start", opacity: 1, y: 0,
          });
          gsap.set(card.querySelector(".upsites-accordion-slide__arrow"),   { opacity: 1, scale: 1 });
          var logo = card.querySelector(".upsites-accordion-slide__logo");
          if (logo) gsap.set(logo, { justifyContent: "flex-start", alignItems: "flex-end" });
          card.classList.add("is-active");
        } else {
          gsap.set(card, { width: widthFechado });
          gsap.set(card.querySelector(".upsites-accordion-slide__content"), { opacity: 0, display: "none" });
          gsap.set(card.querySelector(".upsites-accordion-slide__arrow"),   { opacity: 0, scale: 0.7 });
          var logo = card.querySelector(".upsites-accordion-slide__logo");
          if (logo) gsap.set(logo, { justifyContent: "center", alignItems: "center" });
        }
      });

      // Hover events
      cards.forEach(function (card) {
        card.addEventListener("mouseenter", function () {
          if (card.classList.contains("is-active")) return;

          var prevCard = slider.querySelector(".upsites-accordion-slide.is-active");
          if (prevCard) {
            prevCard.classList.remove("is-active");
            gsap.to(prevCard, { width: widthFechado, duration: 0.6, ease: "power2.out" });
            gsap.to(prevCard.querySelector(".upsites-accordion-slide__content"), {
              opacity: 0, duration: 0.15,
              onComplete: function () {
                gsap.set(prevCard.querySelector(".upsites-accordion-slide__content"), { display: "none" });
              },
            });
            gsap.to(prevCard.querySelector(".upsites-accordion-slide__arrow"), { opacity: 0, scale: 0.7, duration: 0.25 });
            var prevLogo = prevCard.querySelector(".upsites-accordion-slide__logo");
            if (prevLogo) gsap.to(prevLogo, { justifyContent: "center", alignItems: "center", duration: 0.4, ease: "power2.out" });
          }

          card.classList.add("is-active");
          gsap.to(card, { width: widthAberto, duration: 0.6, ease: "power2.out" });

          var logo = card.querySelector(".upsites-accordion-slide__logo");
          if (logo) gsap.to(logo, { justifyContent: "flex-start", alignItems: "flex-end", duration: 0.4, ease: "power2.out" });

          var content = card.querySelector(".upsites-accordion-slide__content");
          gsap.set(content, { display: "flex" });
          gsap.to(content, { opacity: 1, duration: 0.4, delay: 0.25, ease: "power2.out" });

          gsap.to(card.querySelector(".upsites-accordion-slide__arrow"), { opacity: 1, scale: 1, duration: 0.4, delay: 0.25, ease: "power2.out" });
        });
      });
    },

    // ── Mobile tab bar setup ─────────────────────────────
    setupMobile: function (wrapper, cards, tabs, defaultActive) {
      function activate(index) {
        cards.forEach(function (card, i) {
          card.classList.toggle("is-active", i === index);
          // Apply mobile bg position
          AccordionSlider.applyResponsive(card, true);
        });
        tabs.forEach(function (tab, i) {
          tab.classList.toggle("is-active", i === index);
        });
      }

      activate(defaultActive);

      tabs.forEach(function (tab) {
        tab.addEventListener("click", function () {
          var index = parseInt(tab.dataset.index, 10);
          activate(index);
        });
      });
    },

    setup: function ($wrapper) {
      var defaultActive = parseInt($wrapper.data("default-active"), 10) || 0;
      var wrapper       = $wrapper[0];
      var slider        = wrapper.querySelector(".upsites-accordion-slider");
      var cards         = Array.from(slider.querySelectorAll(".upsites-accordion-slide"));
      var tabs          = Array.from(wrapper.querySelectorAll(".upsites-accordion-tab"));

      // CSS variables for overlays
      var overlayStart = $wrapper.data("overlay-start");
      var overlayEnd   = $wrapper.data("overlay-end");
      if (overlayStart) wrapper.style.setProperty("--upsites-overlay-start", overlayStart);
      if (overlayEnd)   wrapper.style.setProperty("--upsites-overlay-end",   overlayEnd);

      cards.forEach(function (card) {
        var activeColor = card.dataset.overlayActive;
        if (activeColor) card.style.setProperty("--upsites-overlay-active", activeColor);
      });

      var isMobile = window.matchMedia("(max-width: 1024px)");

      var gap    = parseInt(window.getComputedStyle(slider).gap) || 20;
      var widths = AccordionSlider.computeWidths(slider, cards.length, gap);

      if (isMobile.matches) {
        AccordionSlider.setupMobile(wrapper, cards, tabs, defaultActive);
      } else {
        AccordionSlider.setupDesktop(wrapper, slider, cards, defaultActive, widths);
      }

      // Switch mode on breakpoint change
      isMobile.addEventListener("change", function (e) {
        // Reset all inline GSAP styles
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

      // Resize: recalculate desktop widths only
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
      function ($scope) {
        AccordionSlider.init($scope);
      }
    );
  });

  $(document).ready(function () {
    if (typeof elementorFrontend === "undefined") {
      AccordionSlider.init($("body"));
    }
  });
})(jQuery);
