const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

export function createProgramGeometry(root, description, frameCount) {
  let copyAnchor = null;
  let metrics = {
    start: 0,
    step: window.innerHeight,
    entryTravel: window.innerHeight,
    mediaTravel: 0,
    exitTravel: window.innerHeight,
    travel: 0,
  };

  function applyCopyAnchor() {
    if (!copyAnchor) return;
    root.style.setProperty('--program-copy-top', `${copyAnchor.top * window.innerHeight}px`);
    root.style.setProperty('--program-copy-left', `${copyAnchor.left * window.innerWidth}px`);
    root.style.setProperty('--program-copy-width', `${copyAnchor.width * window.innerWidth}px`);
  }

  function measure() {
    const step = Math.max(1, window.innerHeight);
    const start = window.scrollY + root.getBoundingClientRect().top;
    const entryTravel = step;
    const mediaTravel = Math.max(0, (frameCount - 1) * step);
    const exitTravel = step;
    const travel = entryTravel + mediaTravel + exitTravel;
    metrics = { start, step, entryTravel, mediaTravel, exitTravel, travel };
    root.style.setProperty('--program-step', `${step}px`);
    root.style.setProperty('--program-track-height', `${frameCount * step}px`);
    root.style.setProperty('--program-scroll-distance', `${travel}px`);
    applyCopyAnchor();
  }

  function rememberCopyAnchor() {
    const rect = description.getBoundingClientRect();
    copyAnchor = {
      top: rect.top / window.innerHeight,
      left: rect.left / window.innerWidth,
      width: rect.width / window.innerWidth,
    };
    applyCopyAnchor();
  }

  function readTarget() {
    return clamp(window.scrollY - metrics.start, 0, metrics.travel);
  }

  function entryProgress(current) {
    return clamp(current / metrics.entryTravel, 0, 1);
  }

  function mediaPosition(current) {
    return clamp(current - metrics.entryTravel, 0, metrics.mediaTravel);
  }

  function activeIndex(current) {
    return clamp(Math.round(mediaPosition(current) / metrics.step), 0, frameCount - 1);
  }

  function exitProgress(current) {
    const exitStart = metrics.entryTravel + metrics.mediaTravel;
    return clamp((current - exitStart) / metrics.exitTravel, 0, 1);
  }

  return {
    measure,
    rememberCopyAnchor,
    readTarget,
    entryProgress,
    mediaPosition,
    activeIndex,
    exitProgress,
  };
}
