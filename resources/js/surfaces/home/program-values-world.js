import { subscribeHomepageFrame } from '../../pages/welcome/scroll-frame.js';

const clamp = (value, min = 0, max = 1) => (
  Math.min(max, Math.max(min, value))
);

const smooth = (value) => {
  const progress = clamp(value);
  return progress * progress * (3 - 2 * progress);
};

const MORPH_START_BOTTOM_RATIO = 1.2;
const MORPH_END_BOTTOM_RATIO = 0.68;

function paintWorld(root, progress) {
  const eased = smooth(progress);
  root.style.setProperty('--program-values-morph', eased.toFixed(4));
  root.style.setProperty(
    '--program-values-morph-pct',
    `${(eased * 100).toFixed(2)}%`,
  );
  root.style.setProperty(
    '--program-values-type-opacity',
    (0.16 - eased * 0.05).toFixed(4),
  );
}

export function mountProgramValuesWorld(root, { signal } = {}) {
  if (!root || signal?.aborted) return () => {};
  const program = root.querySelector('[data-program-kinetic]');
  const values = root.querySelector('[data-values-story]');
  if (!program || !values) return () => {};

  let destroyed = false;

  const shared = subscribeHomepageFrame(({ height }) => {
    const viewportHeight = height;
    const programBottom = program.getBoundingClientRect().bottom;
    const start = viewportHeight * MORPH_START_BOTTOM_RATIO;
    const end = viewportHeight * MORPH_END_BOTTOM_RATIO;
    return clamp((start - programBottom) / Math.max(1, start - end));
  }, progress => { if (!destroyed) paintWorld(root, progress); });
  const lifecycle = new AbortController();
  signal?.addEventListener('abort', destroy, { once: true, signal: lifecycle.signal });
  window.addEventListener('pagehide', event => { if (!event.persisted) destroy(); }, { signal: lifecycle.signal });

  function destroy() {
    destroyed = true;
    shared.remove();
    lifecycle.abort();
    root.style.removeProperty('--program-values-morph');
    root.style.removeProperty('--program-values-morph-pct');
    root.style.removeProperty('--program-values-type-opacity');
  }
  return destroy;
}

