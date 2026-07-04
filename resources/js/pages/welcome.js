
/* =========================================================
   SEKOLAH CERIA NUSANTARA — SCRIPT.JS
   Daftar isi:
   1. Tahun berjalan di footer
   2. Mobile hamburger menu
   3. Smooth scroll menu + nav aktif saat scroll
   4. Navbar berubah saat discroll
   5. Animasi angka statistik (counter)
   6. Filter ekstrakurikuler
   7. Lightbox galeri
   8. Tombol scroll-to-top
   9. Animasi reveal saat elemen masuk viewport
   10. Tilt halus pada ilustrasi hero (opsional)
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {

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
    hamburgerBtn.setAttribute('aria-label', 'Tutup menu');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    navMenu.classList.remove('active');
    navOverlay.classList.remove('active');
    hamburgerBtn.setAttribute('aria-expanded', 'false');
    hamburgerBtn.setAttribute('aria-label', 'Buka menu');
    document.body.style.overflow = '';
  }

  if (hamburgerBtn && navMenu && navOverlay) {
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

  /* ---------- 3. SMOOTH SCROLL + NAV AKTIF ---------- */
  var allNavLinks = document.querySelectorAll('.nav-link');
  var sections = [];

  allNavLinks.forEach(function (link) {
    var targetId = link.getAttribute('href');
    if (targetId && targetId.startsWith('#')) {
      var targetSection = document.querySelector(targetId);
      if (targetSection) sections.push({ id: targetId, el: targetSection, link: link });

      link.addEventListener('click', function (e) {
        e.preventDefault();
        var target = document.querySelector(targetId);
        if (target) {
          var navHeight = document.getElementById('navbar').offsetHeight;
          var targetPos = target.getBoundingClientRect().top + window.pageYOffset - navHeight + 1;
          window.scrollTo({ top: targetPos, behavior: 'smooth' });
        }
      });
    }
  });

  function updateActiveNavLink() {
    var scrollPos = window.scrollY + document.getElementById('navbar').offsetHeight + 40;
    var current = sections[0];
    sections.forEach(function (section) {
      if (section.el.offsetTop <= scrollPos) current = section;
    });
    allNavLinks.forEach(function (link) { link.classList.remove('active'); });
    if (current) current.link.classList.add('active');
  }

  /* ---------- 4. NAVBAR BERUBAH SAAT SCROLL ---------- */
  var navbar = document.getElementById('navbar');
  function handleNavbarScroll() {
    if (window.scrollY > 40) {
      navbar.classList.add('is-scrolled');
    } else {
      navbar.classList.remove('is-scrolled');
    }
  }

  /* ---------- 8. TOMBOL SCROLL TO TOP (dipanggil dalam scroll listener) ---------- */
  var scrollTopBtn = document.getElementById('scrollTopBtn');
  function handleScrollTopBtn() {
    if (window.scrollY > 500) {
      scrollTopBtn.hidden = false;
    } else {
      scrollTopBtn.hidden = true;
    }
  }

  if (scrollTopBtn) {
    scrollTopBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Gabungkan semua listener scroll supaya efisien (tidak berulang-ulang)
  window.addEventListener('scroll', function () {
    handleNavbarScroll();
    handleScrollTopBtn();
    updateActiveNavLink();
  });
  // Jalankan sekali di awal untuk set kondisi awal
  handleNavbarScroll();
  handleScrollTopBtn();
  updateActiveNavLink();

  /* ---------- 5. ANIMASI ANGKA STATISTIK ---------- */
  var statNumbers = document.querySelectorAll('.stat-item__number');

  function animateCount(el) {
    if (!el.hasAttribute('data-count')) return;
    var target = parseInt(el.getAttribute('data-count'), 10) || 0;
    var suffix = el.getAttribute('data-suffix') || '';
    var duration = 1400; // ms
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      // easeOutQuad supaya animasi terasa halus di akhir
      var eased = 1 - (1 - progress) * (1 - progress);
      var current = Math.floor(eased * target);
      el.textContent = current + suffix;
      if (progress < 1) {
        window.requestAnimationFrame(step);
      } else {
        el.textContent = target + suffix;
      }
    }
    window.requestAnimationFrame(step);
  }

  if ('IntersectionObserver' in window && statNumbers.length) {
    var statObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    statNumbers.forEach(function (el) { statObserver.observe(el); });
  } else {
    // Fallback jika IntersectionObserver tidak tersedia
    statNumbers.forEach(function (el) {
      if (!el.hasAttribute('data-count')) return;
      var target = parseInt(el.getAttribute('data-count'), 10) || 0;
      el.textContent = target + (el.getAttribute('data-suffix') || '');
    });
  }

  /* ---------- 6. FILTER EKSTRAKURIKULER ---------- */
  var filterBtns = document.querySelectorAll('.filter-btn');
  var ekskulCards = document.querySelectorAll('.ekskul-card');
  var ekskulEmpty = document.getElementById('ekskulEmpty');

  filterBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var filter = btn.getAttribute('data-filter');

      filterBtns.forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');

      var visibleCount = 0;
      ekskulCards.forEach(function (card) {
        var category = card.getAttribute('data-category');
        var shouldShow = (filter === 'semua' || filter === category);
        card.classList.toggle('is-hidden', !shouldShow);
        if (shouldShow) visibleCount++;
      });

      if (ekskulEmpty) ekskulEmpty.hidden = visibleCount !== 0;
    });
  });

  /* ---------- 7. LIGHTBOX GALERI ---------- */
  var galeriItems = document.querySelectorAll('.galeri-item');
  var lightbox = document.getElementById('lightbox');
  var lightboxVisual = document.getElementById('lightboxVisual');
  var lightboxCaption = document.getElementById('lightboxCaption');
  var lightboxClose = document.getElementById('lightboxClose');
  var lightboxBackdrop = document.getElementById('lightboxBackdrop');
  var lastFocusedElement = null;

  function openLightbox(item) {
    var emoji = item.querySelector('.galeri-item__emoji');
    var caption = item.getAttribute('data-caption') || '';
    var g1 = getComputedStyle(item).getPropertyValue('--g1');
    var g2 = getComputedStyle(item).getPropertyValue('--g2');

    lightboxVisual.style.background = 'linear-gradient(135deg,' + g1 + ',' + g2 + ')';
    lightboxVisual.textContent = emoji ? emoji.textContent : '';
    lightboxCaption.textContent = caption;

    lastFocusedElement = document.activeElement;
    lightbox.hidden = false;
    document.body.style.overflow = 'hidden';
    lightboxClose.focus();
  }

  function closeLightbox() {
    lightbox.hidden = true;
    document.body.style.overflow = '';
    if (lastFocusedElement) lastFocusedElement.focus();
  }

  galeriItems.forEach(function (item) {
    item.addEventListener('click', function () { openLightbox(item); });
  });

  if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
  if (lightboxBackdrop) lightboxBackdrop.addEventListener('click', closeLightbox);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !lightbox.hidden) closeLightbox();
  });

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

  /* ---------- 10. TILT AGRESIF PADA FOTO HERO ---------- */
  var tiltEl = document.getElementById('tiltIllustration');
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (tiltEl && !prefersReducedMotion && window.matchMedia('(hover: hover)').matches) {
    function resetHeroTilt() {
      tiltEl.style.setProperty('--hero-rotate-x', '0deg');
      tiltEl.style.setProperty('--hero-rotate-y', '0deg');
      tiltEl.style.setProperty('--hero-shift-x', '0px');
      tiltEl.style.setProperty('--hero-shift-y', '0px');
      tiltEl.style.setProperty('--hero-proximity', '0');
      tiltEl.style.transform = 'rotateX(0deg) rotateY(0deg)';
    }

    document.addEventListener('mousemove', function (e) {
      var rect = tiltEl.getBoundingClientRect();
      var centerX = rect.left + rect.width / 2;
      var centerY = rect.top + rect.height / 2;
      var dx = e.clientX - centerX;
      var dy = e.clientY - centerY;
      var distance = Math.sqrt(dx * dx + dy * dy);
      var maxDistance = Math.max(rect.width, rect.height) * 1.45;
      var proximity = Math.max(0, 1 - distance / maxDistance);

      if (proximity <= 0) {
        resetHeroTilt();
        return;
      }

      var normalizedX = dx / (rect.width / 2);
      var normalizedY = dy / (rect.height / 2);
      var rotateX = (normalizedY * -18 * proximity).toFixed(2);
      var rotateY = (normalizedX * 18 * proximity).toFixed(2);
      var shiftX = (normalizedX * 58 * proximity).toFixed(2);
      var shiftY = (normalizedY * 58 * proximity).toFixed(2);

      tiltEl.style.setProperty('--hero-rotate-x', rotateX + 'deg');
      tiltEl.style.setProperty('--hero-rotate-y', rotateY + 'deg');
      tiltEl.style.setProperty('--hero-shift-x', shiftX + 'px');
      tiltEl.style.setProperty('--hero-shift-y', shiftY + 'px');
      tiltEl.style.setProperty('--hero-proximity', proximity.toFixed(2));
      tiltEl.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
    });

    window.addEventListener('blur', resetHeroTilt);
  }

});


/* HERO_RESPONSIVE_TILT_LOCK_FINAL */
(function () {
  var tiltEl = document.getElementById('tiltIllustration');
  if (!tiltEl || !window.matchMedia) return;

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var desktopMotion = window.matchMedia('(min-width: 768px) and (hover: hover)');

  function resetHeroTilt() {
    tiltEl.style.setProperty('--hero-rotate-x', '0deg');
    tiltEl.style.setProperty('--hero-rotate-y', '0deg');
    tiltEl.style.setProperty('--hero-shift-x', '0px');
    tiltEl.style.setProperty('--hero-shift-y', '0px');

    if (!desktopMotion.matches || reduceMotion.matches) {
      tiltEl.style.transform = 'none';
    } else {
      tiltEl.style.transform = 'rotateX(0deg) rotateY(0deg)';
    }
  }

  function applyHeroTilt(event) {
    if (!desktopMotion.matches || reduceMotion.matches) {
      resetHeroTilt();
      return;
    }

    var rect = tiltEl.getBoundingClientRect();
    var centerX = rect.left + rect.width / 2;
    var centerY = rect.top + rect.height / 2;
    var dx = event.clientX - centerX;
    var dy = event.clientY - centerY;
    var distance = Math.sqrt(dx * dx + dy * dy);
    var maxDistance = Math.max(rect.width, rect.height) * 1.45;
    var proximity = Math.max(0, 1 - distance / maxDistance);

    if (proximity <= 0) {
      resetHeroTilt();
      return;
    }

    var normalizedX = dx / (rect.width / 2);
    var normalizedY = dy / (rect.height / 2);
    var rotateX = (normalizedY * -18 * proximity).toFixed(2);
    var rotateY = (normalizedX * 18 * proximity).toFixed(2);
    var shiftX = (normalizedX * 58 * proximity).toFixed(2);
    var shiftY = (normalizedY * 58 * proximity).toFixed(2);

    tiltEl.style.setProperty('--hero-rotate-x', rotateX + 'deg');
    tiltEl.style.setProperty('--hero-rotate-y', rotateY + 'deg');
    tiltEl.style.setProperty('--hero-shift-x', shiftX + 'px');
    tiltEl.style.setProperty('--hero-shift-y', shiftY + 'px');
    tiltEl.style.transform = 'rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg)';
  }

  document.addEventListener('pointermove', applyHeroTilt, { passive: true });
  window.addEventListener('resize', resetHeroTilt);
  window.addEventListener('orientationchange', resetHeroTilt);

  if (desktopMotion.addEventListener) {
    desktopMotion.addEventListener('change', resetHeroTilt);
    reduceMotion.addEventListener('change', resetHeroTilt);
  }

  resetHeroTilt();
})();


/* VISI_MISI_INTERACTIVE_FINAL */
document.addEventListener('DOMContentLoaded', function () {
  var missionCards = Array.prototype.slice.call(document.querySelectorAll('[data-mission-card]'));

  if (!missionCards.length) return;

  function activateMissionCard(activeCard) {
    missionCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  missionCards.forEach(function (card) {
    card.addEventListener('click', function () {
      activateMissionCard(card);
    });

    card.addEventListener('focus', function () {
      activateMissionCard(card);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateMissionCard(card);
      }
    });
  });
});
