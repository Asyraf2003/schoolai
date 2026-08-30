import { playGalleryRouteArrival } from '../../components/gallery-route-transition.js';
import { applyGalleryMediaLayout } from './gallery-media-layout.js';

function syncGalleryOverlayLock() {
  var lightboxOpen = Boolean(document.querySelector('[data-gallery-wall-lightbox]:not([hidden])'));
  document.body.classList.toggle('gallery-overlay-open', lightboxOpen);
}

function initPerspectiveGallery() {
  var perspective = document.querySelector('[data-gallery-perspective]');
  if (!perspective) return;

  var page = perspective.querySelector('[data-gallery-perspective-stage]');
  var wrapper = perspective.querySelector('[data-gallery-perspective-wrapper]');
  var trigger = perspective.querySelector('[data-gallery-menu-trigger]');
  var activeLabel = perspective.querySelector('[data-gallery-active-category]');
  var navButtons = Array.prototype.slice.call(perspective.querySelectorAll('[data-gallery-category-target]'));
  var panels = Array.prototype.slice.call(perspective.querySelectorAll('[data-gallery-category-panel]'));
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var activeObserver = null;
  var docScroll = 0;
  var closing = false;
  var closeTimer = null;

  if (!page || !wrapper || !trigger) return;

  function revealPanel(panel) {
    if (!panel) return;

    if (activeObserver) {
      activeObserver.disconnect();
      activeObserver = null;
    }

    var items = Array.prototype.slice.call(panel.querySelectorAll('[data-gallery-load-item]'));
    items.forEach(function (item) {
      item.classList.remove('is-visible');
    });

    if (!items.length) return;

    if (reducedMotion || !('IntersectionObserver' in window)) {
      items.forEach(function (item) {
        item.classList.add('is-visible');
      });
      return;
    }

    requestAnimationFrame(function () {
      activeObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        });
      }, {
        threshold: 0.12,
        rootMargin: '0px 0px -8% 0px',
      });

      items.forEach(function (item) {
        activeObserver.observe(item);
      });
    });
  }

  function activatePanel(targetId) {
    var nextPanel = panels.find(function (panel) {
      return panel.id === targetId;
    });

    if (!nextPanel) return null;

    panels.forEach(function (panel) {
      var active = panel === nextPanel;
      panel.hidden = !active;
      panel.classList.toggle('is-active', active);
    });

    navButtons.forEach(function (button) {
      var active = button.getAttribute('data-gallery-category-target') === targetId;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');

      if (active && activeLabel) {
        activeLabel.textContent = button.textContent.trim();
      }
    });

    revealPanel(nextPanel);
    return nextPanel;
  }

  function currentScrollY() {
    return window.pageYOffset || document.documentElement.scrollTop || 0;
  }

  function openMenu() {
    if (perspective.classList.contains('modalview') || closing) return;

    docScroll = currentScrollY();
    wrapper.style.top = (docScroll * -1) + 'px';

    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;

    perspective.classList.add('modalview');
    trigger.setAttribute('aria-expanded', 'true');

    window.setTimeout(function () {
      perspective.classList.add('animate');
    }, 25);

    var currentButton = navButtons.find(function (button) {
      return button.classList.contains('is-active');
    });

    window.setTimeout(function () {
      if (currentButton) currentButton.focus({ preventScroll: true });
    }, reducedMotion ? 0 : 180);
  }

  function closeMenu(restoreFocus, restoreScrollY) {
    if (!perspective.classList.contains('modalview') || closing) return;

    closing = true;
    var targetScroll = typeof restoreScrollY === 'number' ? restoreScrollY : docScroll;
    page.classList.add('transform');

    var finished = false;
    var finishClose = function () {
      if (finished) return;
      finished = true;

      window.clearTimeout(closeTimer);
      page.removeEventListener('transitionend', onTransitionEnd);
      perspective.classList.remove('modalview');
      page.classList.remove('transform');
      wrapper.style.top = '0px';
      trigger.setAttribute('aria-expanded', 'false');

      document.body.scrollTop = targetScroll;
      document.documentElement.scrollTop = targetScroll;
      window.scrollTo(0, targetScroll);

      closing = false;

      if (restoreFocus !== false) {
        trigger.focus({ preventScroll: true });
      }
    };

    var onTransitionEnd = function (event) {
      if (event.target !== page || event.propertyName.indexOf('transform') === -1) return;
      finishClose();
    };

    page.addEventListener('transitionend', onTransitionEnd);
    perspective.classList.remove('animate');
    closeTimer = window.setTimeout(finishClose, reducedMotion ? 0 : 460);
  }

  trigger.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopPropagation();

    if (perspective.classList.contains('modalview')) {
      closeMenu(true, docScroll);
    } else {
      openMenu();
    }
  });

  navButtons.forEach(function (button) {
    button.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();

      var wasActive = button.classList.contains('is-active');
      var targetId = button.getAttribute('data-gallery-category-target');
      var nextPanel = activatePanel(targetId);
      if (!nextPanel) return;

      closeMenu(false, wasActive ? docScroll : 0);
    });
  });

  page.addEventListener('click', function (event) {
    if (!perspective.classList.contains('animate')) return;
    if (event.target.closest('[data-gallery-menu-trigger]')) return;

    event.preventDefault();
    event.stopPropagation();
    closeMenu(true, docScroll);
  }, true);

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && perspective.classList.contains('modalview')) {
      event.preventDefault();
      closeMenu(true, docScroll);
    }
  });

  var initialPanel = panels.find(function (panel) {
    return !panel.hidden;
  });

  revealPanel(initialPanel || panels[0]);
}

function initGalleryLightbox() {
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
    var isDirectVideo = card.getAttribute('data-gallery-is-direct-video') === '1';
    var emoji = card.getAttribute('data-gallery-emoji') || '📸';
    var g1 = getComputedStyle(card).getPropertyValue('--gallery-g1') || '#DCF1F7';
    var g2 = getComputedStyle(card).getPropertyValue('--gallery-g2') || '#FFC93C';

    lastFocused = document.activeElement;
    clearMedia();
    lightbox.classList.toggle('is-video', isVideo);
    applyGalleryMediaLayout(mediaBox, mediaUrl, isVideo);

    mediaBox.style.setProperty('--gallery-g1', g1);
    mediaBox.style.setProperty('--gallery-g2', g2);

    if (mediaUrl && isVideo && isDirectVideo) {
      var video = document.createElement('video');
      video.src = mediaUrl;
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.preload = 'metadata';
      video.setAttribute('playsinline', '');
      video.setAttribute('webkit-playsinline', '');
      mediaBox.appendChild(video);
    } else if (mediaUrl && isVideo) {
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
    syncGalleryOverlayLock();

    var closeButton = lightbox.querySelector('.gallery-wall-lightbox__close');
    if (closeButton) closeButton.focus();
  }

  function closeLightbox() {
    lightbox.hidden = true;
    clearMedia();
    syncGalleryOverlayLock();

    if (lastFocused && lastFocused.focus) {
      lastFocused.focus({ preventScroll: true });
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
}

document.addEventListener('DOMContentLoaded', function () {
  playGalleryRouteArrival();
  initPerspectiveGallery();
  initGalleryLightbox();
});
