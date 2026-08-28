const CARD_START_RATIO = 0.88;
const CARD_STEP_RATIO = 0.065;
const REVEAL_DISTANCE_RATIO = 0.16;
const SETTLE_MS = 105;
const EPSILON = 0.0005;

const clamp = (value, min = 0, max = 1) => (
  Math.min(max, Math.max(min, value))
);

const smooth = (value) => {
  const progress = clamp(value);
  return progress * progress * (3 - 2 * progress);
};

function readLiftOffset(viewportHeight, viewportWidth) {
  if (viewportWidth < 640) return clamp(viewportHeight * 0.045, 26, 42);
  if (viewportWidth < 1024) return clamp(viewportHeight * 0.052, 30, 50);
  return clamp(viewportHeight * 0.06, 34, 60);
}

function readStaggerIndex(index, viewportWidth) {
  if (viewportWidth >= 640) return index % 4;
  return index % 2;
}

function readTarget(card, index, viewportHeight, viewportWidth) {
  const cardTop = card.getBoundingClientRect().top;
  const staggerIndex = readStaggerIndex(index, viewportWidth);
  const triggerLine = viewportHeight * (
    CARD_START_RATIO - CARD_STEP_RATIO * staggerIndex
  );
  const revealDistance = Math.max(
    1,
    viewportHeight * REVEAL_DISTANCE_RATIO,
  );

  return smooth((triggerLine - cardTop) / revealDistance);
}

export function mountProgramFormation(root) {
  const cards = [...root.querySelectorAll('[data-program-card]')];
  const triggers = [...root.querySelectorAll('[data-program-open]')];
  if (!cards.length || cards.length !== triggers.length) return () => {};

  root.classList.add('is-program-formation');

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduced) {
    return () => root.classList.remove('is-program-formation');
  }

  const targets = cards.map(() => 0);
  const current = cards.map(() => 0);
  let liftOffset = 40;
  let frame = 0;
  let lastTime = 0;
  let destroyed = false;

  function readTargets() {
    const viewportHeight = window.innerHeight || 1;
    const viewportWidth = window.innerWidth || 1;
    liftOffset = readLiftOffset(viewportHeight, viewportWidth);

    cards.forEach((card, index) => {
      targets[index] = readTarget(card, index, viewportHeight, viewportWidth);
    });
  }

  function paint(index) {
    const reveal = current[index];
    const trigger = triggers[index];
    const y = (1 - reveal) * liftOffset;

    trigger.style.opacity = reveal.toFixed(4);
    trigger.style.transform = `translate3d(0, ${y.toFixed(2)}px, 0)`;
    trigger.style.pointerEvents = reveal > 0.08 ? 'auto' : 'none';
    trigger.style.willChange = 'transform, opacity';
  }

  function requestRender() {
    if (!frame && !destroyed && !document.hidden) {
      frame = window.requestAnimationFrame(render);
    }
  }

  function render(time) {
    frame = 0;
    if (destroyed || document.hidden) return;

    const delta = lastTime ? Math.min(64, Math.max(0, time - lastTime)) : 16.67;
    lastTime = time;
    const alpha = 1 - Math.exp(-delta / SETTLE_MS);
    let unsettled = false;

    current.forEach((value, index) => {
      const target = targets[index];
      const next = value + (target - value) * alpha;

      if (Math.abs(target - next) <= EPSILON) {
        current[index] = target;
      } else {
        current[index] = next;
        unsettled = true;
      }

      paint(index);
    });

    if (unsettled) requestRender();
    else lastTime = 0;
  }

  function sample() {
    readTargets();
    requestRender();
  }

  function onVisibility() {
    if (document.hidden) {
      if (frame) window.cancelAnimationFrame(frame);
      frame = 0;
      lastTime = 0;
      return;
    }
    sample();
  }

  readTargets();
  targets.forEach((target, index) => {
    current[index] = target;
    paint(index);
  });

  window.addEventListener('scroll', sample, { passive: true });
  window.addEventListener('resize', sample);
  document.addEventListener('visibilitychange', onVisibility);

  return () => {
    destroyed = true;
    if (frame) window.cancelAnimationFrame(frame);
    window.removeEventListener('scroll', sample);
    window.removeEventListener('resize', sample);
    document.removeEventListener('visibilitychange', onVisibility);

    triggers.forEach((trigger) => {
      trigger.style.removeProperty('opacity');
      trigger.style.removeProperty('transform');
      trigger.style.removeProperty('pointer-events');
      trigger.style.removeProperty('will-change');
    });

    root.classList.remove('is-program-formation');
  };
}
