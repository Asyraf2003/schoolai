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
          window.setTimeout(updateActiveNavLink, 90);
        }
      });
    }
  });

  function updateActiveNavLink() {
    var navbarEl = document.getElementById('navbar');
    if (!navbarEl || !sections.length) return;

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

    if (current) {
      setOnlyActiveNavLink(current.link);
      return;
    }

    restoreDefaultActiveNavLinks();
  }

  /* ---------- 4. NAVBAR BERUBAH SAAT SCROLL ---------- */
  var navbar = document.getElementById('navbar');
  function handleNavbarScroll() {
    if (!navbar) return;

    if (window.scrollY > 40) {
      navbar.classList.add('is-scrolled');
    } else {
      navbar.classList.remove('is-scrolled');
    }
  }


  // Gabungkan semua listener scroll supaya efisien (tidak berulang-ulang)
  window.addEventListener('scroll', function () {
    handleNavbarScroll();
    updateActiveNavLink();
  });
  // Jalankan sekali di awal untuk set kondisi awal
  handleNavbarScroll();
  updateActiveNavLink();

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
