import '../../css/pages/mobile-navigation-cinematic.css';

const MOBILE_BREAKPOINT = 1180;
const CLOSE_DURATION_MS = 820;

function getAnimatedItems(navMenu) {
  var items = [];
  Array.prototype.slice.call(navMenu.children).forEach(function (child) {
    if (child.tagName === 'UL') {
      items = items.concat(Array.prototype.slice.call(child.children));
      return;
    }

    if (child.classList.contains('navbar__cta')) items.push(child);
  });

  return items;
}

export function initializeCinematicMobileNavigation(elements) {
  var hamburgerBtn = elements.hamburgerBtn;
  var navMenu = elements.navMenu;
  var navOverlay = elements.navOverlay;

  if (!hamburgerBtn || !navMenu || !navOverlay) {
    return { toggle: function () {} };
  }

  var animatedItems = getAnimatedItems(navMenu);
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var closeTimer = 0;
  var openFrame = 0;
  var previousOverflow = '';
  var state = 'closed';

  navMenu.setAttribute('data-cinematic-mobile-nav', '');
  navOverlay.setAttribute('data-cinematic-mobile-overlay', '');

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

  function clearPendingMotion() {
    window.clearTimeout(closeTimer);
    window.cancelAnimationFrame(openFrame);
    closeTimer = 0;
    openFrame = 0;
  }

  function finalizeClose(restoreFocus) {
    clearPendingMotion();
    navMenu.classList.remove('active', 'is-closing');
    navOverlay.classList.remove('active', 'is-closing');
    document.body.style.overflow = previousOverflow;
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
    navMenu.classList.remove('is-closing');
    navOverlay.classList.remove('is-closing');
    setHamburgerState(true);

    void navMenu.offsetWidth;
    openFrame = window.requestAnimationFrame(function () {
      navOverlay.classList.add('active');
      navMenu.classList.add('active');
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
    navMenu.classList.add('is-closing');
    navOverlay.classList.add('is-closing');
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

  navOverlay.addEventListener('click', function () {
    closeMenu({ restoreFocus: true });
  });

  navMenu.querySelectorAll('a[href]').forEach(function (link) {
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

  document.addEventListener('mobile-navigation:request-close', function () {
    closeMenu();
  });

  if (typeof MutationObserver === 'function') {
    new MutationObserver(function () {
      if (state !== 'open' || navMenu.classList.contains('active')) return;

      clearPendingMotion();
      navMenu.classList.remove('is-closing');
      navOverlay.classList.remove('active', 'is-closing');
      document.body.style.overflow = previousOverflow;
      setHamburgerState(false);
      state = 'closed';
    }).observe(navMenu, { attributes: true, attributeFilter: ['class'] });
  }

  return {
    toggle: toggleMenu,
    close: closeMenu,
    open: openMenu
  };
}