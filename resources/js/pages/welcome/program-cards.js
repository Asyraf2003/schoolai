import '../../../css/pages/welcome/program-showcase-desktop.css';

document.addEventListener('DOMContentLoaded', function () {
  var showcase = document.querySelector('[data-program-showcase]');
  if (!showcase) return;

  var cards = Array.prototype.slice.call(showcase.querySelectorAll('[data-featured-program-card]'));
  var stage = showcase.querySelector('[data-program-stage]');
  var visual = showcase.querySelector('[data-program-stage-visual]');
  var content = showcase.querySelector('[data-program-stage-content]');
  var stageIndex = showcase.querySelector('[data-program-stage-index]');
  var stageCode = showcase.querySelector('[data-program-stage-code]');
  var stageLabel = showcase.querySelector('[data-program-stage-label]');
  var stageTitle = showcase.querySelector('[data-program-stage-title]');
  var stageSummary = showcase.querySelector('[data-program-stage-summary]');
  var stageDescription = showcase.querySelector('[data-program-stage-description]');
  var stageImage = showcase.querySelector('[data-program-stage-image]');
  var stageSecondary = showcase.querySelector('[data-program-stage-secondary]');
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var desktop = window.matchMedia('(min-width: 1181px)');
  var observer = null;
  var pointerFrame = null;
  var scrollFrame = null;
  var activeCard = null;
  var animations = [];

  if (!cards.length || !stage) return;

  function replaceText(node, value) {
    if (node) node.textContent = value || '';
  }

  function animateNodes() {
    if (reduceMotion || !Element.prototype.animate) return;
    animations.forEach(function (animation) { animation.cancel(); });
    animations = [];

    if (content) {
      animations.push(content.animate(
        [{ opacity: 0.25, transform: 'translateY(22px)' }, { opacity: 1, transform: 'translateY(0)' }],
        { duration: 460, easing: 'cubic-bezier(.2,.8,.2,1)' }
      ));
    }
    if (visual) {
      animations.push(visual.animate(
        [{ opacity: 0.58, transform: 'scale(.985)' }, { opacity: 1, transform: 'scale(1)' }],
        { duration: 620, easing: 'cubic-bezier(.2,.8,.2,1)' }
      ));
    }
  }

  function replaceImage(node, source, alt) {
    if (!node || !source || node.getAttribute('src') === source) return;
    var preload = new Image();
    preload.onload = function () {
      node.src = source;
      if (typeof alt === 'string') node.alt = alt;
    };
    preload.src = source;
  }

  function activate(card, shouldFocus) {
    if (!card || activeCard === card) {
      if (shouldFocus && card) card.focus();
      return;
    }
    activeCard = card;
    cards.forEach(function (item) {
      var selected = item === card;
      item.classList.toggle('is-active', selected);
      item.setAttribute('aria-pressed', selected ? 'true' : 'false');
    });

    var index = Number(card.dataset.programIndex || 1);
    stage.style.setProperty('--program-accent', card.dataset.programAccent || '#0ea5e9');
    showcase.style.setProperty('--program-accent', card.dataset.programAccent || '#0ea5e9');
    stage.style.setProperty('--program-progress', ((index / cards.length) * 100).toFixed(2) + '%');
    replaceText(stageIndex, String(index).padStart(2, '0'));
    replaceText(stageCode, card.dataset.programCode);
    replaceText(stageLabel, card.dataset.programLabel);
    replaceText(stageTitle, card.dataset.programTitle);
    replaceText(stageSummary, card.dataset.programSummary);
    replaceText(stageDescription, card.dataset.programDescription);
    replaceImage(stageImage, card.dataset.programMedia, card.dataset.programTitle || '');
    replaceImage(stageSecondary, card.dataset.programSecondary, '');
    animateNodes();
    if (shouldFocus) card.focus();
  }

  function bindObserver() {
    if (observer) observer.disconnect();
    if (!desktop.matches || !('IntersectionObserver' in window)) return;
    observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) activate(entry.target, false);
      });
    }, { rootMargin: '-34% 0px -44% 0px', threshold: 0.08 });
    cards.forEach(function (card) { observer.observe(card); });
  }

  cards.forEach(function (card, index) {
    card.addEventListener('click', function () { activate(card, false); });
    card.addEventListener('focus', function () { activate(card, false); });
    card.addEventListener('mouseenter', function () {
      if (desktop.matches && window.matchMedia('(hover: hover) and (pointer: fine)').matches) activate(card, false);
    });
    card.addEventListener('keydown', function (event) {
      var next = null;
      if (event.key === 'ArrowDown' || event.key === 'ArrowRight') next = (index + 1) % cards.length;
      if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') next = (index - 1 + cards.length) % cards.length;
      if (event.key === 'Home') next = 0;
      if (event.key === 'End') next = cards.length - 1;
      if (next === null) return;
      event.preventDefault();
      activate(cards[next], true);
    });
  });

  stage.addEventListener('pointermove', function (event) {
    if (reduceMotion || !desktop.matches || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    if (pointerFrame) cancelAnimationFrame(pointerFrame);
    pointerFrame = requestAnimationFrame(function () {
      var bounds = stage.getBoundingClientRect();
      var x = ((event.clientX - bounds.left) / bounds.width - 0.5) * 18;
      var y = ((event.clientY - bounds.top) / bounds.height - 0.5) * 18;
      stage.style.setProperty('--stage-x', x.toFixed(2) + 'px');
      stage.style.setProperty('--stage-y', y.toFixed(2) + 'px');
      stage.style.setProperty('--stage-image-x', (-x * 0.24).toFixed(2) + 'px');
      stage.style.setProperty('--stage-image-y', (-y * 0.24).toFixed(2) + 'px');
    });
  });
  stage.addEventListener('pointerleave', function () {
    stage.style.setProperty('--stage-x', '0px');
    stage.style.setProperty('--stage-y', '0px');
    stage.style.setProperty('--stage-image-x', '0px');
    stage.style.setProperty('--stage-image-y', '0px');
  });

  function updateSectionProgress() {
    scrollFrame = null;
    var bounds = showcase.getBoundingClientRect();
    var travel = Math.max(1, bounds.height - window.innerHeight);
    var progress = Math.min(1, Math.max(0, -bounds.top / travel));
    showcase.style.setProperty('--section-progress', progress.toFixed(3));
    showcase.style.setProperty('--section-drift', (progress * 25).toFixed(2) + '%');
  }
  window.addEventListener('scroll', function () {
    if (!scrollFrame) scrollFrame = requestAnimationFrame(updateSectionProgress);
  }, { passive: true });
  desktop.addEventListener('change', bindObserver);
  bindObserver();
  activeCard = null;
  activate(cards[0], false);
  updateSectionProgress();

  window.addEventListener('pagehide', function () {
    if (observer) observer.disconnect();
    if (pointerFrame) cancelAnimationFrame(pointerFrame);
    if (scrollFrame) cancelAnimationFrame(scrollFrame);
    animations.forEach(function (animation) { animation.cancel(); });
  }, { once: true });
});
