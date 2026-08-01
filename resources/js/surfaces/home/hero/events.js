export function bindHeroEvents(options) {
  var root = options.root;
  var actions = options.actions;
  var reducedMotion = options.reducedMotion;
  var controller = new AbortController();
  var signal = controller.signal;
  var pointerStart = null;
  var observer = null;

  function isRtl() {
    return document.documentElement.dir === 'rtl';
  }

  root.querySelector('[data-hero-previous]')?.addEventListener('click', actions.previous, { signal: signal });
  root.querySelector('[data-hero-next]')?.addEventListener('click', actions.next, { signal: signal });
  root.querySelector('[data-hero-playback]')?.addEventListener('click', actions.togglePaused, { signal: signal });

  root.querySelectorAll('[data-hero-dot]').forEach(function (dot) {
    dot.addEventListener('click', function () {
      actions.goTo(Number.parseInt(dot.dataset.slideIndex, 10));
    }, { signal: signal });
  });

  root.addEventListener('keydown', function (event) {
    if (event.altKey || event.ctrlKey || event.metaKey) return;
    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;

    event.preventDefault();
    var pointsForward = event.key === (isRtl() ? 'ArrowLeft' : 'ArrowRight');
    if (pointsForward) actions.next(); else actions.previous();
  }, { signal: signal });

  root.addEventListener('pointerdown', function (event) {
    if (event.pointerType === 'mouse' || event.target.closest('a, button, form')) return;
    pointerStart = { id: event.pointerId, x: event.clientX, y: event.clientY };
  }, { passive: true, signal: signal });

  root.addEventListener('pointerup', function (event) {
    if (!pointerStart || pointerStart.id !== event.pointerId) return;
    var deltaX = event.clientX - pointerStart.x;
    var deltaY = event.clientY - pointerStart.y;
    pointerStart = null;

    if (Math.abs(deltaX) < 48 || Math.abs(deltaX) <= Math.abs(deltaY)) return;
    var swipeForward = deltaX < 0;
    if (isRtl()) swipeForward = !swipeForward;
    if (swipeForward) actions.next(); else actions.previous();
  }, { passive: true, signal: signal });

  root.addEventListener('pointercancel', function () { pointerStart = null; }, { passive: true, signal: signal });
  document.addEventListener('visibilitychange', actions.syncEnvironment, { signal: signal });
  window.addEventListener('resize', actions.syncEnvironment, { passive: true, signal: signal });

  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (form instanceof HTMLFormElement && form.action.includes('/bahasa/')) actions.suspend();
  }, { capture: true, signal: signal });

  window.addEventListener('pagehide', function (event) {
    if (event.persisted) actions.suspend(); else actions.dispose();
  }, { signal: signal });

  window.addEventListener('pageshow', function (event) {
    if (event.persisted) actions.resume();
  }, { signal: signal });

  var motionListener = actions.syncEnvironment;
  if (typeof reducedMotion.addEventListener === 'function') {
    reducedMotion.addEventListener('change', motionListener);
  } else {
    reducedMotion.addListener(motionListener);
  }

  if ('IntersectionObserver' in window) {
    observer = new IntersectionObserver(function (entries) {
      actions.setRelevant(entries.some(function (entry) { return entry.isIntersecting; }));
    }, { threshold: 0.08 });
    observer.observe(root);
  }

  return function disposeEvents() {
    controller.abort();
    if (observer) observer.disconnect();
    if (typeof reducedMotion.removeEventListener === 'function') {
      reducedMotion.removeEventListener('change', motionListener);
    } else {
      reducedMotion.removeListener(motionListener);
    }
  };
}
