import { applyGalleryMediaLayout } from './gallery-media-layout.js';

export function createGalleryStoryLightbox(storyRoot, cards) {
  var lastFocused = null;
  var mediaLightbox = null;
  var mediaStage = null;
  var lightboxLabel = storyRoot.getAttribute('data-lightbox-label') || 'Homepage gallery media';
  var closeLabel = storyRoot.getAttribute('data-close-label') || 'Close';
  var videoTitleFallback = storyRoot.getAttribute('data-video-title') || 'Gallery video';

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

  return {
    closeStoryMedia: closeStoryMedia,
    isOpen: function () { return mediaLightbox && !mediaLightbox.hidden; },
    openStoryMedia: openStoryMedia,
    openStoryMediaByIndex: openStoryMediaByIndex
  };
}
