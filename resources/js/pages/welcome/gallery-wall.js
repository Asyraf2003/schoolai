import { playGalleryRouteArrival } from '../../components/gallery-route-transition.js';
import { applyGalleryMediaLayout } from './gallery-media-layout.js';

document.addEventListener('DOMContentLoaded', function () {
  playGalleryRouteArrival();

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
