const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

export function createProgramGeometry(root, description, frameCount) {
  let copyAnchor = null;
  let metrics = {
    start: 0,
    step: window.innerHeight,
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
    const mediaTravel = Math.max(0, (frameCount - 1) * step);
    const exitTravel = step;
    metrics = { start, step, mediaTravel, exitTravel, travel: mediaTravel + exitTravel };
    root.style.setProperty('--program-step', `${step}px`);
    root.style.setProperty('--program-track-height', `${frameCount * step}px`);
    root.style.setProperty('--program-scroll-distance', `${metrics.travel}px`);
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

  function mediaPosition(current) {
    return clamp(current, 0, metrics.mediaTravel);
  }

  function activeIndex(current) {
    return clamp(Math.round(mediaPosition(current) / metrics.step), 0, frameCount - 1);
  }

  function exitProgress(current) {
    return clamp((current - metrics.mediaTravel) / metrics.exitTravel, 0, 1);
  }

  return {
    measure,
    rememberCopyAnchor,
    readTarget,
    mediaPosition,
    activeIndex,
    exitProgress,
  };
}
