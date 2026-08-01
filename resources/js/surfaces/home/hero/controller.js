import { bindHeroEvents } from './events.js';
import { createHeroMediaController } from './media.js';
import { createHeroTransitionController } from './transition.js';

export function initializeHomeHero(root) {
  var slides = Array.from(root.querySelectorAll('[data-hero-slide]'));
  if (!slides.length || root.dataset.heroInitialized === 'true') return;

  var dots = Array.from(root.querySelectorAll('[data-hero-dot]'));
  var playbackButton = root.querySelector('[data-hero-playback]');
  var currentLabel = root.querySelector('[data-hero-current]');
  var liveRegion = root.querySelector('[data-hero-live]');
  var progressBar = root.querySelector('[data-hero-progress]');
  var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var duration = Number.parseInt(root.dataset.autoplayInterval, 10);
  var statusTemplate = root.dataset.slideLabel || 'Slide :current of :total';
  var state = {
    currentIndex: Math.max(0, slides.findIndex(function (slide) { return slide.classList.contains('is-active'); })),
    timer: 0,
    transitionTimer: 0,
    userPaused: false,
    relevant: true,
    presented: false,
    disposed: false
  };
  var disposeEvents = function () {};
  var transitions = createHeroTransitionController({ root, slides, reducedMotion });

  if (!Number.isFinite(duration) || duration < 4000) duration = 7000;

  function formatStatus(index) {
    return statusTemplate.replace(':current', String(index + 1)).replace(':total', String(slides.length));
  }

  function clearTimer() {
    window.clearTimeout(state.timer);
    state.timer = 0;
  }

  function clearTransition() {
    window.clearTimeout(state.transitionTimer);
    state.transitionTimer = 0;
    transitions.suspend();
    slides.forEach(function (slide) { slide.classList.remove('is-entering', 'is-leaving'); });
  }

  function canAutoAdvance() {
    return slides.length > 1 && !state.userPaused && !reducedMotion.matches &&
      !document.hidden && state.relevant && !state.disposed;
  }

  function resetProgress() {
    root.classList.remove('is-autoplaying');
    if (progressBar) {
      progressBar.style.animation = 'none';
      void progressBar.offsetWidth;
      progressBar.style.animation = '';
    }
    if (canAutoAdvance() && !media.activeVideo(state.currentIndex)) root.classList.add('is-autoplaying');
  }

  function updateControls() {
    dots.forEach(function (dot, index) {
      var active = index === state.currentIndex;
      dot.classList.toggle('is-active', active);
      if (active) dot.setAttribute('aria-current', 'true'); else dot.removeAttribute('aria-current');
    });

    if (currentLabel) currentLabel.textContent = String(state.currentIndex + 1).padStart(2, '0');
    if (!playbackButton) return;

    var paused = state.userPaused || reducedMotion.matches;
    playbackButton.classList.toggle('is-paused', paused);
    playbackButton.disabled = reducedMotion.matches;
    playbackButton.setAttribute('aria-pressed', paused ? 'true' : 'false');
    playbackButton.setAttribute(
      'aria-label',
      paused ? (playbackButton.dataset.playLabel || 'Play slideshow') :
        (playbackButton.dataset.pauseLabel || 'Pause slideshow')
    );
  }

  function scheduleNext() {
    clearTimer();
    resetProgress();
    if (!canAutoAdvance()) return;

    var video = media.activeVideo(state.currentIndex);
    if (video && !media.isFailed(state.currentIndex)) return;
    state.timer = window.setTimeout(function () {
      showSlide(state.currentIndex + 1, false, { origin: 'automatic' });
    }, duration);
  }

  function showSlide(requestedIndex, announce, request) {
    if (state.disposed) return;
    var nextIndex = (requestedIndex + slides.length) % slides.length;
    var previousIndex = state.currentIndex;
    var animate = state.presented && nextIndex !== previousIndex && !reducedMotion.matches;

    clearTimer();
    clearTransition();
    media.hydrate(slides[nextIndex], true);
    state.currentIndex = nextIndex;

    slides.forEach(function (slide, index) {
      var active = index === nextIndex;
      slide.classList.toggle('is-active', active);
      slide.setAttribute('aria-hidden', active ? 'false' : 'true');
      slide.inert = !active;
      if (animate && index === previousIndex) slide.classList.add('is-leaving');
      if (animate && active) slide.classList.add('is-entering');
    });

    state.presented = true;
    media.prefetchNext(nextIndex);
    media.sync(nextIndex, canAutoAdvance());
    var webglActive = animate && transitions.play(previousIndex, nextIndex, request);

    if (animate) {
      var cleanupDelay = webglActive ? transitions.settleDuration + 80 : 980;
      state.transitionTimer = window.setTimeout(clearTransition, cleanupDelay);
    }

    updateControls();
    if (announce && liveRegion) {
      liveRegion.textContent = formatStatus(nextIndex) + ': ' + (slides[nextIndex].dataset.slideTitle || '');
    }
    scheduleNext();
  }

  function syncEnvironment() {
    if (state.disposed) return;
    updateControls();
    media.sync(state.currentIndex, canAutoAdvance());
    if (document.hidden || !state.relevant || reducedMotion.matches) transitions.suspend();
    else transitions.resize();
    scheduleNext();
  }

  function suspend() {
    clearTimer();
    root.classList.remove('is-autoplaying');
    transitions.suspend();
    media.suspend();
  }

  function dispose() {
    if (state.disposed) return;
    state.disposed = true;
    clearTimer();
    clearTransition();
    disposeEvents();
    transitions.dispose();
    media.dispose();
  }

  var media = createHeroMediaController({
    slides,
    onVideoEnded: function (index) {
      if (index === state.currentIndex && canAutoAdvance()) {
        showSlide(index + 1, false, { origin: 'automatic' });
      }
    }
  });

  var actions = {
    dispose,
    goTo: function (index, request) { if (Number.isInteger(index)) showSlide(index, true, request); },
    next: function (request) { showSlide(state.currentIndex + 1, true, request); },
    previous: function (request) { showSlide(state.currentIndex - 1, true, request); },
    resume: syncEnvironment,
    setRelevant: function (relevant) { state.relevant = relevant; syncEnvironment(); },
    suspend,
    syncEnvironment,
    togglePaused: function () {
      if (!reducedMotion.matches) { state.userPaused = !state.userPaused; syncEnvironment(); }
    }
  };

  root.dataset.heroInitialized = 'true';
  root.dataset.enhanced = 'true';
  root.style.setProperty('--hero-autoplay-duration', duration + 'ms');
  media.prepare();
  disposeEvents = bindHeroEvents({ root, actions, reducedMotion });
  showSlide(state.currentIndex, false);
  transitions.warm();
}
