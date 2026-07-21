
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

function galleryMediaLayoutClass(url, isVideo) {
  if (!isVideo) return 'is-image';

  try {
    var parsed = new URL(url, window.location.href);
    var host = parsed.hostname.toLowerCase().replace(/^www\./, '');

    if (host === 'facebook.com' || host === 'tiktok.com') {
      return 'is-portrait';
    }

    if (host === 'instagram.com') {
      return /^\/p\//.test(parsed.pathname) ? 'is-square' : 'is-portrait';
    }

    return 'is-landscape';
  } catch (error) {
    return 'is-landscape';
  }
}

function applyGalleryMediaLayout(element, url, isVideo) {
  if (!element) return;

  element.classList.remove('is-landscape', 'is-portrait', 'is-square', 'is-image');
  element.classList.add(galleryMediaLayoutClass(url, isVideo));
}

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


document.addEventListener('DOMContentLoaded', function () {
  var storyRoot = document.querySelector('[data-gallery-story]');
  if (!storyRoot) return;

  var cards = Array.prototype.slice.call(storyRoot.querySelectorAll('[data-gallery-story-item]'));
  var panels = Array.prototype.slice.call(storyRoot.querySelectorAll('[data-gallery-visual-panel]'));
  if (!cards.length || !panels.length) return;

  var activeIndex = 0;
  var lastFocused = null;
  var mediaLightbox = null;
  var mediaStage = null;
  var lightboxLabel = storyRoot.getAttribute('data-lightbox-label') || 'Homepage gallery media';
  var closeLabel = storyRoot.getAttribute('data-close-label') || 'Close';
  var videoTitleFallback = storyRoot.getAttribute('data-video-title') || 'Gallery video';

  function activateGalleryStory(index) {
    activeIndex = index;

    cards.forEach(function (card, cardIndex) {
      var isActive = cardIndex === index;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    panels.forEach(function (panel, panelIndex) {
      var isActive = panelIndex === index;
      panel.classList.toggle('is-active', isActive);
      panel.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  function ensureMediaLightbox() {
    if (mediaLightbox) return;

    mediaLightbox = document.createElement('div');
    mediaLightbox.className = 'homepage-gallery-lightbox';
    mediaLightbox.setAttribute('role', 'dialog');
    mediaLightbox.setAttribute('aria-modal', 'true');
    mediaLightbox.setAttribute('aria-label', lightboxLabel);
    mediaLightbox.hidden = true;

    var backdrop = document.createElement('button');
    backdrop.type = 'button';
    backdrop.className = 'homepage-gallery-lightbox__backdrop';
    backdrop.setAttribute('data-homepage-gallery-close', '');

    var panel = document.createElement('article');
    panel.className = 'homepage-gallery-lightbox__panel';

    var closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'homepage-gallery-lightbox__close';
    closeButton.setAttribute('data-homepage-gallery-close', '');

    mediaStage = document.createElement('div');
    mediaStage.className = 'homepage-gallery-lightbox__media';
    mediaStage.setAttribute('data-homepage-gallery-media', '');

    panel.append(closeButton, mediaStage);
    mediaLightbox.append(backdrop, panel);

    document.body.appendChild(mediaLightbox);

    Array.prototype.slice.call(mediaLightbox.querySelectorAll('[data-homepage-gallery-close]')).forEach(function (button) {
      button.setAttribute('aria-label', closeLabel);

      if (button.classList.contains('homepage-gallery-lightbox__close')) {
        button.textContent = closeLabel;
      }

      button.addEventListener('click', closeStoryMedia);
    });
  }

  function clearStoryMedia() {
    if (mediaStage) {
      mediaStage.replaceChildren();
      mediaStage.classList.remove('is-landscape', 'is-portrait', 'is-square', 'is-image');
    }
  }

  function normalizeStoryVideoUrl(url) {
    if (!url) return '';

    try {
      var parsed = new URL(url, window.location.href);
      var host = parsed.hostname.replace(/^www\./, '');

      if (host === 'youtu.be') {
        var videoId = parsed.pathname.replace(/^\//, '').split('/')[0];

        if (videoId) {
          parsed.protocol = 'https:';
          parsed.hostname = 'www.youtube.com';
          parsed.pathname = '/embed/' + videoId;
        }
      }

      host = parsed.hostname.replace(/^www\./, '');

      if (host === 'youtube.com' || host === 'm.youtube.com' || host === 'youtube-nocookie.com') {
        var watchId = parsed.searchParams.get('v');

        if (watchId && parsed.pathname === '/watch') {
          parsed.pathname = '/embed/' + watchId;
          parsed.searchParams.delete('v');
        }

        parsed.protocol = 'https:';
        parsed.searchParams.set('playsinline', '1');
        parsed.searchParams.set('rel', '0');
        parsed.searchParams.set('modestbranding', '1');
      }

      return parsed.toString();
    } catch (error) {
      return url;
    }
  }

  function openStoryMediaByIndex(index) {
    var card = cards[index];

    if (!card) return false;

    return openStoryMedia(card);
  }

  function openStoryMedia(card) {
    var mediaUrl = card.getAttribute('data-media-url') || '';
    if (!mediaUrl) return false;

    ensureMediaLightbox();

    var title = card.getAttribute('data-title') || '';
    var isVideo = card.getAttribute('data-is-video') === '1';
    var normalizedMediaUrl = isVideo ? normalizeStoryVideoUrl(mediaUrl) : mediaUrl;

    lastFocused = document.activeElement;
    clearStoryMedia();
    mediaLightbox.classList.toggle('is-video', isVideo);
    applyGalleryMediaLayout(mediaStage, normalizedMediaUrl, isVideo);

    if (isVideo) {
      var iframe = document.createElement('iframe');
      iframe.src = normalizedMediaUrl;
      iframe.title = title || videoTitleFallback;
      iframe.loading = 'eager';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; fullscreen; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      iframe.setAttribute('allowfullscreen', '');
      iframe.setAttribute('playsinline', '');
      iframe.setAttribute('webkit-playsinline', '');
      iframe.referrerPolicy = 'strict-origin-when-cross-origin';
      mediaStage.appendChild(iframe);
    } else {
      var image = document.createElement('img');
      image.src = mediaUrl;
      image.alt = title || '';
      image.loading = 'lazy';
      image.decoding = 'async';
      mediaStage.appendChild(image);
    }

    mediaLightbox.hidden = false;
    document.body.style.overflow = '';

    window.requestAnimationFrame(function () {
      document.body.style.overflow = 'hidden';
    });

    var closeButton = mediaLightbox.querySelector('.homepage-gallery-lightbox__close');
    if (closeButton) closeButton.focus();

    return true;
  }

  function closeStoryMedia() {
    if (!mediaLightbox) return;

    mediaLightbox.hidden = true;
    document.body.style.overflow = '';
    clearStoryMedia();

    if (lastFocused && lastFocused.focus) {
      lastFocused.focus();
    }
  }

  cards.forEach(function (card, index) {
    card.setAttribute('role', 'button');
    card.setAttribute('aria-pressed', index === activeIndex ? 'true' : 'false');

    card.addEventListener('click', function () {
      activateGalleryStory(index);
      openStoryMedia(card);
    });

    card.addEventListener('keydown', function (event) {
      if (event.key !== 'Enter' && event.key !== ' ') return;

      event.preventDefault();
      activateGalleryStory(index);
      openStoryMedia(card);
    });

    card.addEventListener('focus', function () {
      activateGalleryStory(index);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateGalleryStory(index);
      }
    });
  });

  panels.forEach(function (panel, index) {
    panel.setAttribute('role', 'button');
    panel.setAttribute('tabindex', '0');
    panel.setAttribute('aria-pressed', index === activeIndex ? 'true' : 'false');

    panel.addEventListener('click', function () {
      activateGalleryStory(index);
      openStoryMediaByIndex(index);
    });

    panel.addEventListener('keydown', function (event) {
      if (event.key !== 'Enter' && event.key !== ' ') return;

      event.preventDefault();
      activateGalleryStory(index);
      openStoryMediaByIndex(index);
    });
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && mediaLightbox && !mediaLightbox.hidden) {
      closeStoryMedia();
    }
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
  }
});



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




document.addEventListener('DOMContentLoaded', function () {
  var cards = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-wall-card]'));
  var lightbox = document.querySelector('[data-gallery-wall-lightbox]');
  var mediaBox = document.querySelector('[data-gallery-wall-lightbox-media]');
  var closeButtons = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-wall-lightbox-close]'));
  var lastFocused = null;

  if (!cards.length || !lightbox || !mediaBox) return;

  var wallVideoTitleFallback = lightbox.getAttribute('data-gallery-wall-video-title') || 'Gallery video';

  function clearMedia() {
    mediaBox.replaceChildren();
    mediaBox.classList.remove('is-landscape', 'is-portrait', 'is-square', 'is-image');
  }

  function openLightbox(card) {
    var title = card.getAttribute('data-gallery-title') || '';
    var mediaUrl = card.getAttribute('data-gallery-media-url') || '';
    var isVideo = card.getAttribute('data-gallery-is-video') === '1';
    var emoji = card.getAttribute('data-gallery-emoji') || '📸';
    var g1 = getComputedStyle(card).getPropertyValue('--gallery-g1') || '#DCF1F7';
    var g2 = getComputedStyle(card).getPropertyValue('--gallery-g2') || '#FFC93C';

    lastFocused = document.activeElement;
    clearMedia();
    lightbox.classList.toggle('is-video', isVideo);
    applyGalleryMediaLayout(mediaBox, mediaUrl, isVideo);

    mediaBox.style.setProperty('--gallery-g1', g1);
    mediaBox.style.setProperty('--gallery-g2', g2);

    if (mediaUrl && isVideo) {
      var iframe = document.createElement('iframe');
      iframe.src = mediaUrl;
      iframe.title = title || wallVideoTitleFallback;
      iframe.loading = 'lazy';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      iframe.setAttribute('allowfullscreen', '');
      iframe.setAttribute('playsinline', '');
      iframe.setAttribute('webkit-playsinline', '');
      iframe.referrerPolicy = 'strict-origin-when-cross-origin';
      mediaBox.appendChild(iframe);
    } else if (mediaUrl) {
      var image = document.createElement('img');
      image.src = mediaUrl;
      image.alt = title || '';
      image.loading = 'lazy';
      mediaBox.appendChild(image);
    } else {
      var fallback = document.createElement('span');
      fallback.textContent = emoji;
      fallback.setAttribute('aria-hidden', 'true');
      mediaBox.appendChild(fallback);
    }

    lightbox.hidden = false;
    document.body.style.overflow = 'hidden';

    var closeButton = lightbox.querySelector('.gallery-wall-lightbox__close');
    if (closeButton) closeButton.focus();
  }

  function closeLightbox() {
    lightbox.hidden = true;
    document.body.style.overflow = '';
    clearMedia();

    if (lastFocused && lastFocused.focus) {
      lastFocused.focus();
    }
  }

  cards.forEach(function (card) {
    card.addEventListener('click', function () {
      openLightbox(card);
    });

    card.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openLightbox(card);
      }
    });
  });

  closeButtons.forEach(function (button) {
    button.addEventListener('click', closeLightbox);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !lightbox.hidden) {
      closeLightbox();
    }
  });
});


document.addEventListener('DOMContentLoaded', function () {
  var lazyMedia = Array.prototype.slice.call(document.querySelectorAll('[data-lazy-media][data-lazy-src]'));

  if (!lazyMedia.length) return;

  function reveal(el) {
    el.classList.add('is-lazy-loaded');
  }

  function hydrate(el) {
    if (!el || el.getAttribute('data-lazy-hydrated') === '1') return;

    var src = el.getAttribute('data-lazy-src');
    if (!src) return;

    el.setAttribute('data-lazy-hydrated', '1');

    if (el.tagName === 'IMG') {
      el.setAttribute('loading', 'lazy');
      el.setAttribute('decoding', 'async');
      el.addEventListener('load', function () { reveal(el); }, { once: true });
      el.addEventListener('error', function () { reveal(el); }, { once: true });
      el.setAttribute('src', src);
      el.removeAttribute('data-lazy-src');
      return;
    }

    if (el.tagName === 'IFRAME') {
      el.addEventListener('load', function () { reveal(el); }, { once: true });
      el.setAttribute('src', src);
      el.removeAttribute('data-lazy-src');
      return;
    }

    el.setAttribute('src', src);
    el.removeAttribute('data-lazy-src');
    reveal(el);
  }

  if (!('IntersectionObserver' in window)) {
    lazyMedia.forEach(hydrate);
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;

      hydrate(entry.target);
      observer.unobserve(entry.target);
    });
  }, {
    root: null,
    rootMargin: '32px 0px',
    threshold: 0.01
  });

  lazyMedia.forEach(function (el) {
    observer.observe(el);
  });
});
