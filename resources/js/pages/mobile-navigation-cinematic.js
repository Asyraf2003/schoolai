import '../../css/pages/mobile-navigation-cinematic.css';

const MOBILE_BREAKPOINT = 1180;
const CLOSE_DURATION_MS = 920;

function getAnimatedItems(navLayer) {
  return Array.prototype.slice.call(
    navLayer.querySelectorAll('[data-mobile-navigation-item]')
  );
}

function resetNestedMenus(navLayer) {
  navLayer.querySelectorAll('[data-nav-mega].is-open').forEach(function (menu) {
    var toggle = menu.querySelector('[data-nav-mega-toggle]');
    var panel = menu.querySelector('[data-nav-mega-panel]');

    menu.classList.remove('is-open');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
    if (panel) {
      panel.setAttribute('aria-hidden', 'true');
      panel.inert = true;
    }
  });
}

export function initializeCinematicMobileNavigation(elements) {
  var hamburgerBtn = elements.hamburgerBtn;
  var navLayer = elements.navLayer;
  var header = document.getElementById('navbar');

  if (!hamburgerBtn || !navLayer) {
    return { toggle: function () {} };
  }

  var animatedItems = getAnimatedItems(navLayer);
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var closeTimer = 0;
  var openFrame = 0;
  var previousOverflow = '';
  var state = 'closed';

  animatedItems.forEach(function (item, index) {
    item.style.setProperty('--mobile-menu-order', String(index));
    item.style.setProperty(
      '--mobile-menu-close-order',
      String(animatedItems.length - index - 1)
    );
  });

  function setHamburgerState(isOpen) {
    hamburgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    hamburgerBtn.setAttribute(
      'aria-label',
      hamburgerBtn.getAttribute(
        isOpen ? 'data-mobile-close-label' : 'data-mobile-open-label'
      ) || (isOpen ? 'Close menu' : 'Open menu')
    );
  }

  function setLayerAccessibility(isOpen) {
    navLayer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
    navLayer.inert = !isOpen;
    navLayer.hidden = !isOpen;
  }

  function clearPendingMotion() {
    window.clearTimeout(closeTimer);
    window.cancelAnimationFrame(openFrame);
    closeTimer = 0;
    openFrame = 0;
  }

  function finalizeClose(restoreFocus) {
    clearPendingMotion();
    navLayer.classList.remove('active', 'is-closing');
    resetNestedMenus(navLayer);
    setLayerAccessibility(false);
    document.body.style.overflow = previousOverflow;
    if (header) header.classList.remove('has-open-menu');
    setHamburgerState(false);
    state = 'closed';

    if (restoreFocus) hamburgerBtn.focus({ preventScroll: true });
  }

  function openMenu() {
    if (state === 'open' || state === 'opening') return;

    clearPendingMotion();
    state = 'opening';
    previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    setLayerAccessibility(true);
    navLayer.classList.remove('is-closing');
    if (header) header.classList.add('has-open-menu');
    setHamburgerState(true);

    void navLayer.offsetWidth;
    openFrame = window.requestAnimationFrame(function () {
      navLayer.classList.add('active');
      state = 'open';
      openFrame = 0;
    });
  }

  function closeMenu(options) {
    var settings = options || {};
    if (state === 'closed') return;

    clearPendingMotion();
    setHamburgerState(false);

    if (settings.immediate || reducedMotion.matches) {
      finalizeClose(Boolean(settings.restoreFocus));
      return;
    }

    state = 'closing';
    navLayer.classList.add('is-closing');
    closeTimer = window.setTimeout(function () {
      finalizeClose(Boolean(settings.restoreFocus));
    }, CLOSE_DURATION_MS);
  }

  function toggleMenu() {
    if (state === 'open' || state === 'opening') {
      closeMenu({ restoreFocus: true });
      return;
    }

    openMenu();
  }

  navLayer.querySelectorAll('[data-mobile-navigation-close]').forEach(function (control) {
    control.addEventListener('click', function () {
      closeMenu({ restoreFocus: true });
    });
  });

  navLayer.querySelectorAll('a[href]').forEach(function (link) {
    link.addEventListener('click', function () {
      closeMenu();
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeMenu({ restoreFocus: true });
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > MOBILE_BREAKPOINT) closeMenu({ immediate: true });
  });

  document.addEventListener('mobile-navigation:request-close', function (event) {
    closeMenu({
      immediate: Boolean(event.detail && event.detail.immediate),
      restoreFocus: Boolean(event.detail && event.detail.restoreFocus)
    });
  });

  setLayerAccessibility(false);
  setHamburgerState(false);

  return {
    toggle: toggleMenu,
    close: closeMenu,
    open: openMenu
  };
}
