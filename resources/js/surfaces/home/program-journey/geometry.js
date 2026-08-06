const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

export function createProgramGeometry(root, frameCount) {
  let origin = null;
  let titleMotion = { x: 0, y: 0, scale: 1 };
  let metrics = {
    start: 0,
    step: window.innerHeight,
    trackTravel: 0,
    exitTravel: window.innerHeight,
    travel: 0,
  };

  function viewportRect(rect) {
    return {
      top: rect.top / window.innerHeight,
      left: rect.left / window.innerWidth,
      width: rect.width / window.innerWidth,
      height: rect.height / window.innerHeight,
    };
  }

  function resolvedRect(rect) {
    return {
      top: rect.top * window.innerHeight,
      left: rect.left * window.innerWidth,
      width: rect.width * window.innerWidth,
      height: rect.height * window.innerHeight,
    };
  }

  function applyDescriptionOrigin() {
    if (!origin) return;
    const description = resolvedRect(origin.description);
    root.style.setProperty('--program-copy-top', `${description.top}px`);
    root.style.setProperty('--program-copy-left', `${description.left}px`);
    root.style.setProperty('--program-copy-width', `${description.width}px`);
  }

  function measure() {
    const step = Math.max(1, window.innerHeight);
    const start = window.scrollY + root.getBoundingClientRect().top;
    const trackTravel = Math.max(0, frameCount * step);
    const exitTravel = step;
    const travel = trackTravel + exitTravel;
    metrics = { start, step, trackTravel, exitTravel, travel };
    root.style.setProperty('--program-step', `${step}px`);
    root.style.setProperty('--program-track-height', `${(frameCount + 1) * step}px`);
    root.style.setProperty('--program-scroll-distance', `${travel}px`);
    applyDescriptionOrigin();
  }

  function rememberOrigin(titleRect, descriptionRect) {
    origin = {
      title: viewportRect(titleRect),
      description: viewportRect(descriptionRect),
    };
    applyDescriptionOrigin();
  }

  function hasOrigin() {
    return origin !== null;
  }

  function measureTitleMotion(title) {
    if (!origin || !title) return;
    const source = resolvedRect(origin.title);
    const target = title.getBoundingClientRect();
    if (!target.width) return;
    titleMotion = {
      x: source.left - target.left,
      y: source.top - target.top,
      scale: source.width / target.width,
    };
  }

  function readTarget() {
    return clamp(window.scrollY - metrics.start, 0, metrics.travel);
  }

  function trackPosition(current) {
    return clamp(current, 0, metrics.trackTravel);
  }

  function titleProgress(current) {
    return clamp(current / metrics.step, 0, 1);
  }

  function titleTransform(current) {
    const inverse = 1 - titleProgress(current);
    const x = titleMotion.x * inverse;
    const y = titleMotion.y * inverse;
    const scale = 1 + (titleMotion.scale - 1) * inverse;
    return `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) scale(${scale.toFixed(4)})`;
  }

  function programIndex(current) {
    if (current < metrics.step * .98) return -1;
    const index = Math.round((current - metrics.step) / metrics.step);
    return clamp(index, 0, frameCount - 1);
  }

  function exitProgress(current) {
    return clamp(
      (current - metrics.trackTravel) / metrics.exitTravel,
      0,
      1,
    );
  }

  return {
    measure,
    rememberOrigin,
    hasOrigin,
    measureTitleMotion,
    readTarget,
    trackPosition,
    titleTransform,
    programIndex,
    exitProgress,
  };
}
