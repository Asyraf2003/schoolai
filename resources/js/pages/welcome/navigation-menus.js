export function initializeNavigationMenus() {
  /* ---------- 1. TAHUN BERJALAN DI FOOTER ---------- */
  var yearEl = document.getElementById('currentYear');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }

  /* ---------- 2. MOBILE HAMBURGER MENU ---------- */
  var hamburgerBtn = document.getElementById('hamburgerBtn');
  var navMenu = document.getElementById('navMenu');
  var navOverlay = document.getElementById('navOverlay');

  function openMenu() {
    navMenu.classList.add('active');
    navOverlay.classList.add('active');
    hamburgerBtn.setAttribute('aria-expanded', 'true');
    hamburgerBtn.setAttribute('aria-label', closeMenuLabel);
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    navMenu.classList.remove('active');
    navOverlay.classList.remove('active');
    hamburgerBtn.setAttribute('aria-expanded', 'false');
    hamburgerBtn.setAttribute('aria-label', openMenuLabel);
    document.body.style.overflow = '';
  }

  if (hamburgerBtn && navMenu && navOverlay) {
    var openMenuLabel = hamburgerBtn.getAttribute('data-mobile-open-label') || hamburgerBtn.getAttribute('aria-label') || 'Open menu';
    var closeMenuLabel = hamburgerBtn.getAttribute('data-mobile-close-label') || 'Close menu';
    hamburgerBtn.addEventListener('click', function () {
      var isOpen = navMenu.classList.contains('active');
      if (isOpen) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    navOverlay.addEventListener('click', closeMenu);

    // Tutup menu mobile setiap kali link menu diklik
    var navLinks = navMenu.querySelectorAll('a');
    navLinks.forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });

    // Tutup menu dengan tombol Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeMenu();
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
