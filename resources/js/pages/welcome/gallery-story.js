import { createGalleryStoryLightbox } from './gallery-story-lightbox.js';

document.addEventListener('DOMContentLoaded', function () {
  var storyRoot = document.querySelector('[data-gallery-story]');
  if (!storyRoot) return;

  var cards = Array.prototype.slice.call(storyRoot.querySelectorAll('[data-gallery-story-item]'));
  var panels = Array.prototype.slice.call(storyRoot.querySelectorAll('[data-gallery-visual-panel]'));
  if (!cards.length || !panels.length) return;

  var activeIndex = 0;
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

  var storyLightbox = createGalleryStoryLightbox(storyRoot, cards);
  var closeStoryMedia = storyLightbox.closeStoryMedia;
  var openStoryMedia = storyLightbox.openStoryMedia;
  var openStoryMediaByIndex = storyLightbox.openStoryMediaByIndex;

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
    if (event.key === 'Escape' && storyLightbox.isOpen()) {
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
