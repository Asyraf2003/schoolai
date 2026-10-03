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
