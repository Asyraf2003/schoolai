const clamp = (value, min = 0, max = 1) => (
  Math.min(max, Math.max(min, value))
);

const smooth = (value) => {
  const progress = clamp(value);
  return progress * progress * (3 - 2 * progress);
};

const MORPH_START_BOTTOM_RATIO = 1.2;
const MORPH_END_BOTTOM_RATIO = 0.52;

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

export function mountProgramValuesWorld(root) {
  const program = root.querySelector('[data-program-kinetic]');
  const values = root.querySelector('[data-values-story]');
  if (!program || !values) return () => {};

  let frame = 0;
  let destroyed = false;

  function render() {
    frame = 0;
    if (destroyed || document.hidden) return;

    const viewportHeight = window.innerHeight || 1;
    const programBottom = program.getBoundingClientRect().bottom;
    const start = viewportHeight * MORPH_START_BOTTOM_RATIO;
    const end = viewportHeight * MORPH_END_BOTTOM_RATIO;
    const progress = clamp(
      (start - programBottom) / Math.max(1, start - end),
    );

    paintWorld(root, progress);
  }

  function requestRender() {
    if (!frame && !destroyed) {
      frame = window.requestAnimationFrame(render);
    }
  }

  function onVisibility() {
    if (document.hidden) {
      if (frame) window.cancelAnimationFrame(frame);
      frame = 0;
      return;
    }
    requestRender();
  }

  window.addEventListener('scroll', requestRender, { passive: true });
  window.addEventListener('resize', requestRender);
  document.addEventListener('visibilitychange', onVisibility);
  requestRender();

  return () => {
    destroyed = true;
    if (frame) window.cancelAnimationFrame(frame);
    window.removeEventListener('scroll', requestRender);
    window.removeEventListener('resize', requestRender);
    document.removeEventListener('visibilitychange', onVisibility);
    root.style.removeProperty('--program-values-morph');
    root.style.removeProperty('--program-values-morph-pct');
    root.style.removeProperty('--program-values-type-opacity');
  };
}

const programValuesWorld = document.querySelector('[data-program-values-world]');
if (programValuesWorld) mountProgramValuesWorld(programValuesWorld);
