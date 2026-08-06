const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

export function createProgramGeometry(root, framesTrack, description, frameCount) {
  let copyAnchor = null;
  let metrics = { start: 0, step: window.innerHeight, travel: 0 };

  function measure() {
    const rootTop = window.scrollY + root.getBoundingClientRect().top;
    metrics = {
      step: window.innerHeight,
      start: rootTop + framesTrack.offsetTop,
      travel: Math.max(0, framesTrack.offsetHeight - window.innerHeight),
    };
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

  function applyCopyAnchor() {
    if (!copyAnchor) return;
    root.style.setProperty('--program-copy-top', `${copyAnchor.top * window.innerHeight}px`);
    root.style.setProperty('--program-copy-left', `${copyAnchor.left * window.innerWidth}px`);
    root.style.setProperty('--program-copy-width', `${copyAnchor.width * window.innerWidth}px`);
  }

  function readTarget() {
    return clamp(window.scrollY - metrics.start, 0, metrics.travel);
  }

  function anchors() {
    return Array.from({ length: frameCount }, (_, index) => (
      Math.min(metrics.travel, index * metrics.step)
    ));
  }

  function activeIndex(current) {
    return clamp(Math.round(current / metrics.step), 0, frameCount - 1);
  }

  function documentTarget(local) {
    return metrics.start + local;
  }

  return {
    measure,
    rememberCopyAnchor,
    readTarget,
    anchors,
    activeIndex,
    documentTarget,
    get travel() { return metrics.travel; },
  };
}
