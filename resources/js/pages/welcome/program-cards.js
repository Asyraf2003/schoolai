import '../../../css/pages/welcome/program-showcase-desktop.css';

document.addEventListener('DOMContentLoaded', function () {
  var showcase = document.querySelector('[data-program-showcase]');
  if (!showcase) return;

  var programCards = Array.prototype.slice.call(
    showcase.querySelectorAll('[data-featured-program-card]')
  );
  var stage = showcase.querySelector('[data-program-stage]');
  var stageVisual = showcase.querySelector('[data-program-stage-visual]');
  var stageContent = showcase.querySelector('[data-program-stage-content]');
  var stageIndex = showcase.querySelector('[data-program-stage-index]');
  var stageCode = showcase.querySelector('[data-program-stage-code]');
  var stageLabel = showcase.querySelector('[data-program-stage-label]');
  var stageTitle = showcase.querySelector('[data-program-stage-title]');
  var stageSummary = showcase.querySelector('[data-program-stage-summary]');
  var stageDescription = showcase.querySelector('[data-program-stage-description]');
  var reduceMotion = window.matchMedia
    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var contentAnimation = null;
  var visualAnimation = null;
  var pointerFrame = null;

  if (!programCards.length || !stage) return;

  function replaceText(node, value) {
    if (node) node.textContent = value || '';
  }

  function animateStage() {
    if (reduceMotion || !stageContent || !stageVisual || !stageContent.animate) return;

    if (contentAnimation) contentAnimation.cancel();
    if (visualAnimation) visualAnimation.cancel();

    contentAnimation = stageContent.animate(
      [
        { opacity: 0.38, transform: 'translateY(16px)' },
        { opacity: 1, transform: 'translateY(0)' },
      ],
      { duration: 360, easing: 'cubic-bezier(.22,.8,.2,1)' }
    );

    visualAnimation = stageVisual.animate(
      [
        { opacity: 0.62, transform: 'scale(.96)' },
        { opacity: 1, transform: 'scale(1)' },
      ],
      { duration: 480, easing: 'cubic-bezier(.22,.8,.2,1)' }
    );
  }

  function activateProgramCard(activeCard, shouldFocus) {
    programCards.forEach(function (card) {
      var isActive = card === activeCard;
      card.classList.toggle('is-active', isActive);
      card.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    stage.style.setProperty('--program-accent', activeCard.dataset.programAccent || '#0ea5e9');
    replaceText(stageIndex, String(activeCard.dataset.programIndex || '1').padStart(2, '0'));
    replaceText(stageCode, activeCard.dataset.programCode);
    replaceText(stageLabel, activeCard.dataset.programLabel);
    replaceText(stageTitle, activeCard.dataset.programTitle);
    replaceText(stageSummary, activeCard.dataset.programSummary);
    replaceText(stageDescription, activeCard.dataset.programDescription);
    animateStage();

    if (shouldFocus) activeCard.focus();
  }

  programCards.forEach(function (card, index) {
    card.addEventListener('click', function () {
      activateProgramCard(card, false);
    });

    card.addEventListener('focus', function () {
      activateProgramCard(card, false);
    });

    card.addEventListener('mouseenter', function () {
      if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        activateProgramCard(card, false);
      }
    });

    card.addEventListener('keydown', function (event) {
      var nextIndex = null;

      if (event.key === 'ArrowDown' || event.key === 'ArrowRight') {
        nextIndex = (index + 1) % programCards.length;
      } else if (event.key === 'ArrowUp' || event.key === 'ArrowLeft') {
        nextIndex = (index - 1 + programCards.length) % programCards.length;
      } else if (event.key === 'Home') {
        nextIndex = 0;
      } else if (event.key === 'End') {
        nextIndex = programCards.length - 1;
      }

      if (nextIndex === null) return;
      event.preventDefault();
      activateProgramCard(programCards[nextIndex], true);
    });
  });

  stage.addEventListener('pointermove', function (event) {
    if (reduceMotion || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) return;
    if (pointerFrame) cancelAnimationFrame(pointerFrame);

    pointerFrame = requestAnimationFrame(function () {
      var bounds = stage.getBoundingClientRect();
      var x = ((event.clientX - bounds.left) / bounds.width - 0.5) * 18;
      var y = ((event.clientY - bounds.top) / bounds.height - 0.5) * 18;
      stage.style.setProperty('--stage-x', x.toFixed(2) + 'px');
      stage.style.setProperty('--stage-y', y.toFixed(2) + 'px');
    });
  });

  stage.addEventListener('pointerleave', function () {
    stage.style.setProperty('--stage-x', '0px');
    stage.style.setProperty('--stage-y', '0px');
  });

  activateProgramCard(programCards[0], false);
});
