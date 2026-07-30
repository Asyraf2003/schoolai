export function initializeNavigationMenus() {
  /* ---------- 1. TAHUN BERJALAN DI FOOTER ---------- */
  var yearEl = document.getElementById('currentYear');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  /* ---------- 2. LAZY MOBILE HAMBURGER MENU ---------- */
  var hamburgerBtn = document.getElementById('hamburgerBtn');
  var navLayer = document.getElementById('navMenu');
  var header = document.getElementById('navbar');

  if (hamburgerBtn && navLayer) {
    var controllerPromise = null;

    function createFallbackController() {
      var fallbackMenu = navLayer.querySelector('.mobile-navigation-layer__menu');

      function setOpen(shouldOpen) {
        navLayer.hidden = !shouldOpen;
        navLayer.inert = !shouldOpen;
        navLayer.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
        navLayer.classList.toggle('active', shouldOpen);
        hamburgerBtn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        hamburgerBtn.setAttribute(
          'aria-label',
          hamburgerBtn.getAttribute(
            shouldOpen ? 'data-mobile-close-label' : 'data-mobile-open-label'
          ) || (shouldOpen ? 'Close menu' : 'Open menu')
        );
        if (header) header.classList.toggle('has-open-menu', shouldOpen);
        document.body.style.overflow = shouldOpen ? 'hidden' : '';
        navLayer.style.cssText = shouldOpen
          ? 'position:fixed;inset:0;z-index:400;display:block;overflow:hidden;background:#fff;color:#121212;'
          : '';
        if (fallbackMenu) {
          fallbackMenu.style.cssText = shouldOpen
            ? 'position:relative;z-index:1;height:100%;overflow:auto;padding:110px 24px 40px;box-sizing:border-box;'
            : '';
        }
      }

      return {
        toggle: function () {
          setOpen(!navLayer.classList.contains('active'));
        },
        close: function () {
          setOpen(false);
        }
      };
    }

    function loadMobileNavigation() {
      if (!window.matchMedia('(max-width: 1180px)').matches) {
        return Promise.resolve(null);
      }

      if (!controllerPromise) {
        controllerPromise = import('../mobile-navigation-cinematic.js')
          .then(function (module) {
            return module.initializeCinematicMobileNavigation({
              hamburgerBtn: hamburgerBtn,
              navLayer: navLayer
            });
          })
          .catch(createFallbackController);
      }

      return controllerPromise;
    }

    function warmMobileNavigation() {
      loadMobileNavigation();
    }

    hamburgerBtn.addEventListener('pointerenter', warmMobileNavigation, { once: true });
    hamburgerBtn.addEventListener('focus', warmMobileNavigation, { once: true });
    hamburgerBtn.addEventListener('touchstart', warmMobileNavigation, {
      once: true,
      passive: true
    });
    hamburgerBtn.addEventListener('click', function (event) {
      event.preventDefault();
      hamburgerBtn.setAttribute('aria-busy', 'true');
      loadMobileNavigation().then(function (controller) {
        hamburgerBtn.removeAttribute('aria-busy');
        if (controller) controller.toggle();
      });
    });
  }

  /* ---------- 3. LANGUAGE SWITCHER ---------- */
  var languageMenus = Array.prototype.slice.call(
    document.querySelectorAll('.nav-language')
  );

  function setLanguageMenuState(languageMenu, isOpen) {
    var languageButton = languageMenu.querySelector('.nav-language__button');

    languageMenu.classList.toggle('is-open', isOpen);

    if (languageButton) {
      languageButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }
  }

  function closeLanguageMenus(exceptMenu) {
    languageMenus.forEach(function (languageMenu) {
      if (languageMenu !== exceptMenu) {
        setLanguageMenuState(languageMenu, false);
      }
    });
  }

  languageMenus.forEach(function (languageMenu, index) {
    var languageButton = languageMenu.querySelector('.nav-language__button');
    var languagePanel = languageMenu.querySelector('.nav-language__panel');

    if (!languageButton || !languagePanel) return;

    if (!languagePanel.id) {
      languagePanel.id = 'navLanguagePanel-' + index;
    }

    languageButton.setAttribute('aria-controls', languagePanel.id);
    setLanguageMenuState(languageMenu, false);

    languageButton.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();

      var shouldOpen = !languageMenu.classList.contains('is-open');

      closeLanguageMenus(languageMenu);
      setLanguageMenuState(languageMenu, shouldOpen);
    });

    languageButton.addEventListener('keydown', function (event) {
      if (event.key !== 'ArrowDown') return;

      event.preventDefault();
      closeLanguageMenus(languageMenu);
      setLanguageMenuState(languageMenu, true);

      var firstOption = languagePanel.querySelector(
        '.nav-language__option:not([disabled])'
      );

      if (firstOption) firstOption.focus();
    });

    languagePanel.addEventListener('keydown', function (event) {
      var options = Array.prototype.slice.call(
        languagePanel.querySelectorAll('.nav-language__option:not([disabled])')
      );
      var currentIndex = options.indexOf(document.activeElement);

      if (event.key === 'Escape') {
        event.preventDefault();
        event.stopPropagation();
        setLanguageMenuState(languageMenu, false);
        languageButton.focus();
        return;
      }

      if (!options.length || (event.key !== 'ArrowDown' && event.key !== 'ArrowUp')) {
        return;
      }

      event.preventDefault();

      var direction = event.key === 'ArrowDown' ? 1 : -1;
      var nextIndex = currentIndex < 0
        ? 0
        : (currentIndex + direction + options.length) % options.length;

      options[nextIndex].focus();
    });
  });

  document.addEventListener('click', function (event) {
    languageMenus.forEach(function (languageMenu) {
      if (!languageMenu.contains(event.target)) {
        setLanguageMenuState(languageMenu, false);
      }
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeLanguageMenus();
    }
  });
}
