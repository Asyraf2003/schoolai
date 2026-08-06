const clamp = (value, min, max) => Math.max(min, Math.min(max, value));

export function createProgramGeometry(root, hud, frameCount) {
  let origin = null;
  let titleMotion = { x: 0, y: 0, scale: 1 };
  let metrics = {
    start: 0,
    step: window.innerHeight,
    trackTravel: 0,
    exitTravel: window.innerHeight,
    travel: 0,
  };

  function localRect(node, owner) {
    const rect = node.getBoundingClientRect();
    const ownerRect = owner.getBoundingClientRect();
    return {
      top: rect.top - ownerRect.top,
      left: rect.left - ownerRect.left,
      width: rect.width,
      height: rect.height,
    };
  }

  function applyDescriptionOrigin() {
    if (!origin) return;
    root.style.setProperty('--program-copy-top', `${origin.description.top}px`);
    root.style.setProperty('--program-copy-left', `${origin.description.left}px`);
    root.style.setProperty('--program-copy-width', `${origin.description.width}px`);
  }

  function rememberOrigin(introFrame, titleGuide, descriptionGuide) {
    origin = {
      title: localRect(titleGuide, introFrame),
      description: localRect(descriptionGuide, introFrame),
    };
    applyDescriptionOrigin();
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

  function measureTitleMotion(title) {
    if (!origin || !title) return;
    const target = localRect(title, hud);
    if (!target.width) return;
    titleMotion = {
      x: origin.title.left - target.left,
      y: origin.title.top - target.top,
      scale: origin.title.width / target.width,
    };
  }

  function readTarget() {
    return clamp(window.scrollY - metrics.start, 0, metrics.travel);
  }

  function trackPosition(current) {
    return clamp(current, 0, metrics.trackTravel);
  }

  function titleTransform(current) {
    const progress = clamp(current / metrics.step, 0, 1);
    const inverse = 1 - progress;
    const x = titleMotion.x * inverse;
    const y = titleMotion.y * inverse;
    const scale = 1 + (titleMotion.scale - 1) * inverse;
    return `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, 0) scale(${scale.toFixed(4)})`;
  }

  function programIndex(current) {
    if (current < metrics.step) return -1;
    const index = Math.round((current - metrics.step) / metrics.step);
    return clamp(index, 0, frameCount - 1);
  }

  function localPosition(index) {
    return clamp((index + 1) * metrics.step, 0, metrics.trackTravel);
  }

  function documentPosition(index) {
    return metrics.start + localPosition(index);
  }

  function exitProgress(current) {
    return clamp(
      (current - metrics.trackTravel) / metrics.exitTravel,
      0,
      1,
    );
  }

  return {
    rememberOrigin,
    measure,
    measureTitleMotion,
    readTarget,
    trackPosition,
    titleTransform,
    programIndex,
    localPosition,
    documentPosition,
    exitProgress,
  };
}
