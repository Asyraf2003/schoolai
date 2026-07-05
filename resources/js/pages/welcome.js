
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
  /* NAV_CONTACT_ACTIVE_WHEN_FOOTER_VISIBLE_FINAL */
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
    item.addEventListener('click', function (event) {
      if (event.target && event.target.closest && event.target.closest('a')) {
        return;
      }

      openLightbox(item);
    });
  });

  if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
  if (lightboxBackdrop) lightboxBackdrop.addEventListener('click', closeLightbox);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && lightbox && !lightbox.hidden) closeLightbox();
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


/* NILAI_SEKOLAH_INTERACTIVE_FINAL */
document.addEventListener('DOMContentLoaded', function () {
  var valueCards = Array.prototype.slice.call(document.querySelectorAll('[data-school-value-card]'));

  if (!valueCards.length) return;

  function activateValueCard(activeCard) {
    valueCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  valueCards.forEach(function (card) {
    card.addEventListener('click', function () {
      activateValueCard(card);
    });

    card.addEventListener('focus', function () {
      activateValueCard(card);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateValueCard(card);
      }
    });
  });
});


/* PROGRAM_UNGGULAN_INTERACTIVE_FINAL */
document.addEventListener('DOMContentLoaded', function () {
  var programCards = Array.prototype.slice.call(document.querySelectorAll('[data-featured-program-card]'));

  if (!programCards.length) return;

  function activateProgramCard(activeCard) {
    programCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  programCards.forEach(function (card) {
    card.addEventListener('click', function () {
      activateProgramCard(card);
    });

    card.addEventListener('focus', function () {
      activateProgramCard(card);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateProgramCard(card);
      }
    });
  });
});


/* GALLERY_STICKY_STORY_FINAL */
document.addEventListener('DOMContentLoaded', function () {
  var storyRoot = document.querySelector('[data-gallery-story]');
  if (!storyRoot) return;

  var cards = Array.prototype.slice.call(storyRoot.querySelectorAll('[data-gallery-story-item]'));
  var panels = Array.prototype.slice.call(storyRoot.querySelectorAll('[data-gallery-visual-panel]'));
  if (!cards.length || !panels.length) return;

  var activeIndex = 0;

  function activateGalleryStory(index) {
    if (index === activeIndex) return;

    activeIndex = index;

    cards.forEach(function (card, cardIndex) {
      card.classList.toggle('is-active', cardIndex === index);
    });

    panels.forEach(function (panel, panelIndex) {
      panel.classList.toggle('is-active', panelIndex === index);
    });
  }

  cards.forEach(function (card, index) {
    card.addEventListener('focus', function () {
      activateGalleryStory(index);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateGalleryStory(index);
      }
    });
  });

  if ('IntersectionObserver' in window) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;

        var index = cards.indexOf(entry.target);
        if (index >= 0) {
          activateGalleryStory(index);
        }
      });
    }, {
      threshold: 0.52,
      rootMargin: '-24% 0px -30% 0px'
    });

    cards.forEach(function (card) {
      observer.observe(card);
    });
  } else {
    cards.forEach(function (card, index) {
      card.addEventListener('click', function () {
        activateGalleryStory(index);
      });
    });
  }
});

/* PUBLIC_PAGES_INTERACTION_FINAL */
document.addEventListener('DOMContentLoaded', function () {
  var articleFilters = Array.prototype.slice.call(document.querySelectorAll('[data-public-filter]'));
  var articleCards = Array.prototype.slice.call(document.querySelectorAll('[data-public-article]'));
  var articleSearch = document.querySelector('[data-public-search]');
  var activeArticleFilter = 'all';

  function activateButton(buttons, activeButton) {
    buttons.forEach(function (button) {
      button.classList.toggle('is-active', button === activeButton);
      button.setAttribute('aria-pressed', button === activeButton ? 'true' : 'false');
    });
  }

  function updateArticles() {
    var query = articleSearch ? articleSearch.value.trim().toLowerCase() : '';

    articleCards.forEach(function (card) {
      var category = card.getAttribute('data-category') || '';
      var text = card.textContent.toLowerCase();
      var categoryMatch = activeArticleFilter === 'all' || category === activeArticleFilter;
      var queryMatch = !query || text.indexOf(query) !== -1;

      card.classList.toggle('is-hidden', !(categoryMatch && queryMatch));
    });
  }

  if (articleCards.length) {
    articleFilters.forEach(function (button) {
      button.setAttribute('aria-pressed', button.classList.contains('is-active') ? 'true' : 'false');
      button.addEventListener('click', function () {
        activeArticleFilter = button.getAttribute('data-public-filter') || 'all';
        activateButton(articleFilters, button);
        updateArticles();
      });
    });

    if (articleSearch) {
      articleSearch.addEventListener('input', updateArticles);
    }

    updateArticles();
  }

  var galleryFilters = Array.prototype.slice.call(document.querySelectorAll('[data-public-gallery-filter]'));
  var galleryCards = Array.prototype.slice.call(document.querySelectorAll('[data-public-gallery-card]'));
  var activeGalleryFilter = 'all';

  function updateGallery() {
    galleryCards.forEach(function (card) {
      var category = card.getAttribute('data-category') || '';
      var type = card.getAttribute('data-type') || '';
      var match = activeGalleryFilter === 'all' || category === activeGalleryFilter || type === activeGalleryFilter;

      card.classList.toggle('is-hidden', !match);
    });
  }

  if (galleryCards.length) {
    galleryFilters.forEach(function (button) {
      button.setAttribute('aria-pressed', button.classList.contains('is-active') ? 'true' : 'false');
      button.addEventListener('click', function () {
        activeGalleryFilter = button.getAttribute('data-public-gallery-filter') || 'all';
        activateButton(galleryFilters, button);
        updateGallery();
      });
    });

    updateGallery();
  }

  var publicLightbox = document.querySelector('[data-public-lightbox]');
  var lightboxVisualPublic = document.querySelector('[data-public-lightbox-visual]');
  var lightboxTitlePublic = document.querySelector('[data-public-lightbox-title]');
  var lightboxCaptionPublic = document.querySelector('[data-public-lightbox-caption]');
  var lightboxClosePublic = Array.prototype.slice.call(document.querySelectorAll('[data-public-lightbox-close]'));
  var publicLastFocused = null;

  function closePublicLightbox() {
    if (!publicLightbox) return;

    publicLightbox.hidden = true;
    document.body.style.overflow = '';

    if (publicLastFocused && publicLastFocused.focus) {
      publicLastFocused.focus();
    }
  }

  function openPublicLightbox(card) {
    if (!publicLightbox || !lightboxVisualPublic || !lightboxTitlePublic || !lightboxCaptionPublic) return;

    publicLastFocused = document.activeElement;
    lightboxVisualPublic.textContent = card.getAttribute('data-emoji') || '';
    lightboxVisualPublic.style.background = 'linear-gradient(135deg, ' + getComputedStyle(card).getPropertyValue('--gallery-g1') + ', ' + getComputedStyle(card).getPropertyValue('--gallery-g2') + ')';
    lightboxTitlePublic.textContent = card.getAttribute('data-title') || '';
    lightboxCaptionPublic.textContent = card.getAttribute('data-caption') || '';
    publicLightbox.hidden = false;
    document.body.style.overflow = 'hidden';

    var closeButton = publicLightbox.querySelector('.public-lightbox__close');
    if (closeButton) closeButton.focus();
  }

  if (publicLightbox && galleryCards.length) {
    galleryCards.forEach(function (card) {
      card.addEventListener('click', function () {
        openPublicLightbox(card);
      });
    });

    lightboxClosePublic.forEach(function (button) {
      button.addEventListener('click', closePublicLightbox);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && publicLightbox && !publicLightbox.hidden) {
        closePublicLightbox();
      }
    });
  }
});
/* /PUBLIC_PAGES_INTERACTION_FINAL */
