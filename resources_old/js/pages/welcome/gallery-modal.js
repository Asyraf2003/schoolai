export function initGalleryModal() {
  var modal = document.querySelector('[data-gallery-modal]');
  if (!modal) return;

  var mediaBox = modal.querySelector('[data-gallery-modal-media]');
  var titleBox = modal.querySelector('[data-gallery-modal-title]');
  var closeButtons = Array.prototype.slice.call(modal.querySelectorAll('[data-gallery-modal-close]'));
  var openers = Array.prototype.slice.call(document.querySelectorAll('[data-gallery-modal-open]'));
  var lastFocused = null;

  if (!mediaBox || !titleBox || !openers.length) return;

  function clearMedia() {
    mediaBox.replaceChildren();
  }

  function closeModal() {
    if (modal.hidden) return;

    modal.hidden = true;
    document.body.classList.remove('gallery-modal-open');
    clearMedia();

    if (lastFocused && typeof lastFocused.focus === 'function') {
      lastFocused.focus({ preventScroll: true });
    }
  }

  function openModal(opener) {
    var title = opener.getAttribute('data-gallery-title') || '';
    var mediaUrl = opener.getAttribute('data-gallery-media-url') || '';
    var thumbnailUrl = opener.getAttribute('data-gallery-thumbnail-url') || '';
    var isVideo = opener.getAttribute('data-gallery-is-video') === '1';
    var isDirectVideo = opener.getAttribute('data-gallery-is-direct-video') === '1';

    lastFocused = document.activeElement;
    titleBox.textContent = title;
    clearMedia();

    if (isVideo && isDirectVideo && mediaUrl) {
      var video = document.createElement('video');
      video.src = mediaUrl;
      video.controls = true;
      video.autoplay = true;
      video.playsInline = true;
      video.preload = 'metadata';
      mediaBox.appendChild(video);
    } else if (isVideo && mediaUrl) {
      var iframe = document.createElement('iframe');
      iframe.src = mediaUrl;
      iframe.title = title || 'Gallery video';
      iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      iframe.allowFullscreen = true;
      iframe.referrerPolicy = 'strict-origin-when-cross-origin';
      mediaBox.appendChild(iframe);
    } else if (mediaUrl || thumbnailUrl) {
      var image = document.createElement('img');
      image.src = mediaUrl || thumbnailUrl;
      image.alt = title;
      mediaBox.appendChild(image);
    }

    modal.hidden = false;
    document.body.classList.add('gallery-modal-open');

    var closeButton = modal.querySelector('.gallery-grid-modal__close');
    if (closeButton) closeButton.focus({ preventScroll: true });
  }

  openers.forEach(function (opener) {
    opener.addEventListener('click', function () {
      openModal(opener);
    });
  });

  closeButtons.forEach(function (button) {
    button.addEventListener('click', closeModal);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && !modal.hidden) {
      event.preventDefault();
      closeModal();
    }
  });
}
