import { createDesktopHeaderVisibility } from './navigation-header-visibility.js';

export function initializeNavigationState() {
  /* ---------- 4. SMOOTH SCROLL + NAV AKTIF ---------- */
  var allNavLinks = document.querySelectorAll('.nav-link');
  var defaultActiveNavLinks = Array.prototype.slice.call(document.querySelectorAll('.nav-link.active'));
  var sections = [];

  function getElementDocumentTop(el) {
    return el.getBoundingClientRect().top + window.pageYOffset;
  }

  function clearActiveNavLinks() {
    allNavLinks.forEach(function (link) {
      link.classList.remove('active');
    });
  }

  function restoreDefaultActiveNavLinks() {
    clearActiveNavLinks();

    defaultActiveNavLinks.forEach(function (link) {
      if (document.documentElement.contains(link)) {
        link.classList.add('active');
      }
    });
  }

  function setOnlyActiveNavLink(activeLink) {
    clearActiveNavLinks();

    if (activeLink) {
      activeLink.classList.add('active');
    }
  }

  allNavLinks.forEach(function (link) {
    var targetId = link.getAttribute('href');

    if (targetId && targetId.startsWith('#')) {
      var targetSection = document.querySelector(targetId);

      if (targetSection) {
        sections.push({ id: targetId, el: targetSection, link: link });
      }

      link.addEventListener('click', function (e) {
        e.preventDefault();

        var target = document.querySelector(targetId);
        if (target) {
          var navForOffset = document.getElementById('navbar');
          var navHeight = navForOffset ? navForOffset.offsetHeight : 0;
          var targetPos = target.getBoundingClientRect().top + window.pageYOffset - navHeight + 1;

          window.scrollTo({ top: targetPos, behavior: 'smooth' });
          window.setTimeout(requestNavigationUpdate, 90);
        }
      });
    }
  });

  function resolveActiveNavLink() {
    var navbarEl = document.getElementById('navbar');
    if (!navbarEl || !sections.length) return undefined;

    var navHeight = navbarEl.offsetHeight || 0;
    var topActivationLine = window.scrollY + navHeight + 48;
    var footerActivationLine = window.scrollY + window.innerHeight - 120;
    var pageBottomReached = (window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 4);
    var current = null;

    sections.forEach(function (section) {
      var sectionTop = getElementDocumentTop(section.el);
      var sectionHeight = Math.max(section.el.offsetHeight, section.el.scrollHeight, 1);
      var sectionBottom = sectionTop + sectionHeight;
      var isContactSection = section.id === '#kontak';

      if (isContactSection) {
        if (sectionTop <= footerActivationLine || pageBottomReached) {
          current = section;
        }
        return;
      }

      if (sectionTop <= topActivationLine && sectionBottom > topActivationLine) {
        current = section;
        return;
      }

      if (sectionTop <= topActivationLine) {
        current = section;
      }
    });

    return current ? current.link : null;
  }

  function applyActiveNavLink(activeLink) {
    if (typeof activeLink === 'undefined') return;

    if (activeLink) {
      setOnlyActiveNavLink(activeLink);
      return;
    }

    restoreDefaultActiveNavLinks();
  }

  /* ---------- 4. NAVBAR BERUBAH SAAT DISCROLL ---------- */
  var navbar = document.getElementById('navbar');
  var hero = document.getElementById('beranda');
  var navbarScrolled = navbar ? navbar.classList.contains('is-scrolled') : false;
  var navigationFrame = 0;
  var headerVisibility = createDesktopHeaderVisibility(
    navbar,
    hero,
    getElementDocumentTop
  );

  function handleNavbarScroll() {
    if (!navbar) return false;

    var threshold = navbarScrolled ? 24 : 48;
    var nextScrolled = window.scrollY > threshold;
    if (nextScrolled === navbarScrolled) return false;

    navbarScrolled = nextScrolled;
    navbar.classList.toggle('is-scrolled', nextScrolled);
    return true;
  }

  function runNavigationUpdate() {
    navigationFrame = 0;

    // Resolve layout-dependent state before mutating the navbar geometry.
    var activeLink = resolveActiveNavLink();
    headerVisibility.update();
    applyActiveNavLink(activeLink);

    // is-scrolled changes navbar min-height. Commit it last, then measure the
    // settled geometry on the next frame instead of forcing sync layout now.
    if (handleNavbarScroll()) {
      requestNavigationUpdate();
    }
  }

  function requestNavigationUpdate() {
    if (navigationFrame) return;
    navigationFrame = window.requestAnimationFrame(runNavigationUpdate);
  }

  window.addEventListener('scroll', requestNavigationUpdate, { passive: true });
  window.addEventListener('resize', requestNavigationUpdate, { passive: true });

  requestNavigationUpdate();

  /* ---------- 9. ANIMASI REVEAL SAAT SCROLL ---------- */
  var revealEls = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window && revealEls.length) {
    var revealObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    revealEls.forEach(function (el) { revealObserver.observe(el); });
  } else {
    revealEls.forEach(function (el) { el.classList.add('is-visible'); });
  }
}
