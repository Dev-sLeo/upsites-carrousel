import "../scss/mega-menu-nav.scss";

function closeAllTopItems(root, exceptItem) {
  root.querySelectorAll(".upsites-mega-nav__top-item.is-open").forEach((item) => {
    if (item === exceptItem) return;
    item.classList.remove("is-open");
    const link = item.querySelector(":scope > .upsites-mega-nav__top-link");
    if (link) link.setAttribute("aria-expanded", "false");
  });
}

const PANEL_GAP = 20; // px between the nav bar and the dropdown panel
const CLOSE_DELAY = 250; // ms grace period so crossing the gap doesn't close the panel

function positionPanel(root, item) {
  const panel = item.querySelector(":scope > .upsites-mega-nav__panel");
  const bar = root.querySelector(".upsites-mega-nav__desktop");
  if (!panel || !bar) return;
  panel.style.top = bar.getBoundingClientRect().bottom + PANEL_GAP + "px";
}

function isElementorEditMode() {
  return (
    typeof elementorFrontend !== "undefined" &&
    elementorFrontend.isEditMode &&
    elementorFrontend.isEditMode()
  );
}

function setupDesktop(root) {
  // Every level-0 item reacts to hover — items without a mega menu don't
  // open anything themselves, but hovering one must still close whichever
  // sibling panel is currently open (otherwise it stays open, floating over
  // the rest of the bar, while the pointer has clearly moved away from it).
  const allTopItems = root.querySelectorAll(".upsites-mega-nav__top-item");

  allTopItems.forEach((item) => {
    const trigger = item.querySelector(":scope > .upsites-mega-nav__top-link");
    if (!trigger) return;

    if (!item.classList.contains("has-submenu")) {
      item.addEventListener("mouseenter", () => {
        if (!isElementorEditMode()) closeAllTopItems(root);
      });
      return;
    }

    const panel = item.querySelector(":scope > .upsites-mega-nav__panel");

    let closeTimer = null;
    const cancelClose = () => {
      if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
      }
    };

    const open = () => {
      cancelClose();
      closeAllTopItems(root, item);
      positionPanel(root, item);
      item.classList.add("is-open");
      trigger.setAttribute("aria-expanded", "true");
    };
    const closeNow = () => {
      cancelClose();
      item.classList.remove("is-open");
      trigger.setAttribute("aria-expanded", "false");
    };
    // Gives the pointer a grace period to cross the gap between the nav bar
    // and the panel (or move from the panel back to the button) without the
    // menu closing underneath it.
    const scheduleClose = () => {
      cancelClose();
      closeTimer = setTimeout(closeNow, CLOSE_DELAY);
    };

    trigger.addEventListener("click", (e) => {
      e.preventDefault();
      item.classList.contains("is-open") ? closeNow() : open();
    });

    // Hover only on the live site — inside the Elementor editor it would
    // pop the panel open over the controls and block editing.
    item.addEventListener("mouseenter", () => {
      if (!isElementorEditMode()) open();
    });
    item.addEventListener("mouseleave", () => {
      if (!isElementorEditMode()) scheduleClose();
    });

    if (panel) {
      panel.addEventListener("mouseenter", () => {
        if (!isElementorEditMode()) cancelClose();
      });
      panel.addEventListener("mouseleave", () => {
        if (!isElementorEditMode()) scheduleClose();
      });
    }
  });

  document.addEventListener("click", (e) => {
    if (!root.contains(e.target)) closeAllTopItems(root);
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeAllTopItems(root);
  });

  window.addEventListener("resize", () => {
    const openItem = root.querySelector(".upsites-mega-nav__top-item.is-open");
    if (openItem) positionPanel(root, openItem);
  });

  // ── Tabs inside a panel (Figma: switches "while hovering", click as fallback) ──
  root.querySelectorAll(".upsites-mega-nav__panel").forEach((panel) => {
    const tabs = panel.querySelectorAll(".upsites-mega-nav__tab");
    const tabpanels = panel.querySelectorAll(".upsites-mega-nav__tabpanel");
    if (!tabs.length) return;

    const activateTab = (tab) => {
      const index = tab.dataset.tab;
      tabs.forEach((t) => {
        const active = t === tab;
        t.classList.toggle("is-active", active);
        t.setAttribute("aria-selected", active ? "true" : "false");
      });
      tabpanels.forEach((p) => {
        p.classList.toggle("is-active", p.dataset.tabpanel === index);
      });
    };

    tabs.forEach((tab) => {
      tab.addEventListener("click", () => activateTab(tab));
      tab.addEventListener("mouseenter", () => {
        if (!isElementorEditMode()) activateTab(tab);
      });
    });
  });
}

function setupMobile(root) {
  const mobile = root.querySelector(".upsites-mega-nav__mobile");
  const burger = root.querySelector(".upsites-mega-nav__burger");
  const closeBtn = root.querySelector(".upsites-mega-nav__close");
  if (!mobile) return;

  const level1 = mobile.querySelector('.upsites-mega-nav__screen[data-level="1"]');

  const openOffCanvas = () => {
    mobile.classList.add("is-open");
    mobile.setAttribute("aria-hidden", "false");
    document.body.style.overflow = "hidden";
    if (burger) burger.setAttribute("aria-expanded", "true");
  };

  const closeOffCanvas = () => {
    mobile.classList.remove("is-open");
    mobile.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    if (burger) burger.setAttribute("aria-expanded", "false");
    goToLevel1();
  };

  const goToLevel1 = () => {
    mobile.querySelectorAll(".upsites-mega-nav__screen").forEach((s) => s.classList.remove("is-active"));
    if (level1) level1.classList.add("is-active");
  };

  const goToGroup = (groupId) => {
    mobile.querySelectorAll(".upsites-mega-nav__screen").forEach((s) => s.classList.remove("is-active"));
    const target = mobile.querySelector(
      `.upsites-mega-nav__screen--level2[data-group="${CSS.escape(groupId)}"]`
    );
    if (target) target.classList.add("is-active");
  };

  if (burger) burger.addEventListener("click", openOffCanvas);
  if (closeBtn) closeBtn.addEventListener("click", closeOffCanvas);

  mobile.querySelectorAll("[data-open-group]").forEach((btn) => {
    btn.addEventListener("click", () => goToGroup(btn.dataset.openGroup));
  });

  mobile.querySelectorAll("[data-back]").forEach((btn) => {
    btn.addEventListener("click", goToLevel1);
  });

  // ── Column accordions inside level-2 screens ──
  mobile.querySelectorAll(".upsites-mega-nav__accordion-trigger").forEach((trigger) => {
    trigger.addEventListener("click", () => {
      const panel = document.getElementById(trigger.getAttribute("aria-controls"));
      const isOpen = trigger.getAttribute("aria-expanded") === "true";

      // Only one column open at a time within the same accordion group.
      trigger
        .closest(".upsites-mega-nav__mobile-accordion-group")
        .querySelectorAll(".upsites-mega-nav__accordion-trigger")
        .forEach((t) => {
          t.setAttribute("aria-expanded", "false");
          const p = document.getElementById(t.getAttribute("aria-controls"));
          if (p) p.classList.remove("is-open");
        });

      if (!isOpen) {
        trigger.setAttribute("aria-expanded", "true");
        if (panel) panel.classList.add("is-open");
      }
    });
  });
}

function setup(root) {
  if (root.dataset.megaNavInit) return;
  root.dataset.megaNavInit = "1";
  setupDesktop(root);
  setupMobile(root);
}

function init() {
  document.querySelectorAll(".upsites-mega-nav").forEach(setup);
}

// ── Elementor integration ──────────────────────────────────
if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) {
  elementorFrontend.hooks.addAction(
    "frontend/element_ready/upsites-mega-menu-nav.default",
    ($scope) => {
      const root = $scope[0].querySelector(".upsites-mega-nav");
      if (root) setup(root);
    }
  );
}

document.addEventListener("DOMContentLoaded", init);
