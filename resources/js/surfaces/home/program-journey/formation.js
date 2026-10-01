import { subscribeHomepageFrame } from '../../../pages/welcome/scroll-frame.js';

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
  if (viewportWidth >= 1024) return index % 4;
  if (viewportWidth >= 640) return 0;
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

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');

  const targets = cards.map(() => 0);
  const current = cards.map(() => 0);
  let liftOffset = 40;
  let sampledRevision = -1;
  let lastTime = 0;
  let motionEpoch = -1;
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

  function requestRender() { if (!destroyed) shared.request(); }

  function render(_, viewport) {
    const { time } = viewport;
    if (viewport.motionEpoch !== motionEpoch) { lastTime = 0; motionEpoch = viewport.motionEpoch; }
    if (destroyed || document.hidden) return;
    if (reduced.matches) { current.fill(1); clearTriggers(); lastTime = 0; return; }

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

    if (!unsettled) lastTime = 0;
    return unsettled;
  }

  const shared = subscribeHomepageFrame(viewport => {
    if (sampledRevision !== viewport.revision) {
      if (reduced.matches) targets.fill(1);
      else readTargets();
      sampledRevision = viewport.revision;
    }
  }, render);

  if (reduced.matches) targets.fill(1);
  else readTargets();
  targets.forEach((target, index) => {
    current[index] = target;
    if (!reduced.matches) paint(index);
  });

  const onPreference = () => { sampledRevision = -1; lastTime = 0; requestRender(); };
  reduced.addEventListener?.('change', onPreference);
  const observer = 'ResizeObserver' in window ? new ResizeObserver(() => {
    sampledRevision = -1; requestRender();
  }) : null;
  observer?.observe(root);

  function clearTriggers() {
    triggers.forEach(trigger => ['opacity', 'transform', 'pointer-events', 'will-change']
      .forEach(property => trigger.style.removeProperty(property)));
  }
  return () => {
    destroyed = true;
    shared.remove();
    observer?.disconnect();
    reduced.removeEventListener?.('change', onPreference);
    clearTriggers();
    root.classList.remove('is-program-formation');
  };
}
