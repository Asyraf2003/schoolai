import { createProgramGeometry } from './geometry.js';
import { createProgramIntegration } from './integration.js';
import { createCopyMotion, createVisualScrollEngine } from './motion.js';

export function mountProgramJourney(root) {
  if (!root) return null;

  const frames = Array.from(root.querySelectorAll('[data-program-frame]'));
  const railItems = Array.from(root.querySelectorAll('[data-program-rail-item]'));
  const hud = root.querySelector('[data-program-hud]');
  const framesTrack = root.querySelector('[data-program-frames]');
  const introFrame = root.querySelector('[data-program-intro-frame]');
  const titleGuide = root.querySelector('[data-program-title-guide]');
  const descriptionGuide = root.querySelector('[data-program-description-guide]');
  const rail = root.querySelector('[data-program-rail]');
  const exit = root.querySelector('[data-program-exit]');
  const exitLines = Array.from(root.querySelectorAll('[data-program-exit-lines] span'));
  const title = root.querySelector('[data-program-title]');
  const description = root.querySelector('[data-program-description]');
  const link = root.querySelector('[data-program-active-link]');
  const linkLabel = root.querySelector('[data-program-active-link-label]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!frames.length || !hud || !framesTrack || !introFrame || !titleGuide
    || !descriptionGuide || !exit || !title || !description) return null;

  const introTitle = title.textContent.trim();
  const introDescription = description.textContent.trim();
  const defaultLinkLabel = linkLabel?.textContent.trim() || '';
  const defaultLinkHref = link?.getAttribute('href') || '';
  const copyMotion = createCopyMotion(reducedMotion);
  const geometry = createProgramGeometry(root, hud, frames.length);
  let activeIndex = -1;
  let travelDirection = 1;
  let destroyed = false;

  const integration = createProgramIntegration(root, reducedMotion);

  integration.sync();
  geometry.rememberOrigin(introFrame, titleGuide, descriptionGuide);
  root.classList.add('is-enhanced');
  geometry.measure();
  geometry.measureTitleMotion(title);

  function text(node, value) {
    if (node) node.textContent = value || '';
  }

  function swapCopy(update) {
    copyMotion.swap({ stable: [title, description], update });
  }

  function setIntro() {
    if (activeIndex === -1) return;
    activeIndex = -1;
    frames.forEach((frame) => frame.classList.remove('is-active'));
    railItems.forEach((item) => item.setAttribute('aria-current', 'false'));
    swapCopy(() => {
      text(title, introTitle);
      text(description, introDescription);
      text(linkLabel, defaultLinkLabel);
      if (link) link.href = defaultLinkHref;
    });
  }

  function setActive(index) {
    if (activeIndex === index) return;
    const program = frames[index]?.dataset;
    if (!program) return;
    activeIndex = index;
    frames.forEach((frame, itemIndex) => {
      frame.classList.toggle('is-active', itemIndex === index);
    });
    railItems.forEach((item, itemIndex) => {
      item.setAttribute('aria-current', itemIndex === index ? 'true' : 'false');
    });
    root.style.setProperty('--program-accent', program.programAccent || '#0ea5e9');
    swapCopy(() => {
      text(title, program.programTitle);
      text(description, program.programDescription);
      if (link) link.href = program.programLink || defaultLinkHref;
      text(linkLabel, defaultLinkLabel);
    });
  }

  function render({ current, velocity }) {
    if (destroyed) return;
    if (velocity > .001) travelDirection = 1;
    else if (velocity < -.001) travelDirection = -1;
    const trackCurrent = geometry.trackPosition(current);
    const exitProgress = geometry.exitProgress(current);
    const programIndex = geometry.programIndex(current, travelDirection);
    const programVisible = programIndex >= 0;

    framesTrack.style.transform = `translate3d(0, ${(-trackCurrent).toFixed(2)}px, 0)`;
    title.style.transform = geometry.titleTransform(current);
    exit.style.opacity = String(exitProgress);
    hud.style.opacity = String(1 - exitProgress);
    if (rail) rail.style.opacity = String(programVisible ? 1 - exitProgress : 0);
    root.classList.toggle('is-program-visible', programVisible);

    if (programVisible) setActive(programIndex);
    else setIntro();

    exitLines.forEach((line, index) => {
      const progress = Math.max(0, Math.min(1, (exitProgress - index * .035) / .72));
      line.style.transform = `scaleX(${progress.toFixed(3)})`;
    });
  }

  const visualScroll = createVisualScrollEngine({
    reducedMotion,
    readTarget: geometry.readTarget,
    onUpdate: render,
  });

  function onRailClick(event) {
    const item = event.currentTarget;
    const index = Number(item.dataset.programIndex);
    if (!Number.isInteger(index) || index < 0 || index >= frames.length) return;
    event.preventDefault();
    window.scrollTo({ top: geometry.documentPosition(index), behavior: 'instant' });
    visualScroll.sync(geometry.localPosition(index));
    const href = item.getAttribute('href');
    if (href && window.location.hash !== href) history.pushState(null, '', href);
  }

  function refreshGeometry() {
    title.style.removeProperty('transform');
    geometry.rememberOrigin(introFrame, titleGuide, descriptionGuide);
    geometry.measure();
    geometry.measureTitleMotion(title);
    visualScroll.sync(geometry.readTarget());
  }

  function onResize() {
    integration.sync();
    refreshGeometry();
    window.dispatchEvent(new CustomEvent('program:layout'));
  }

  function onVisionLayout() {
    refreshGeometry();
  }

  function onPageShow(event) {
    if (event.persisted) onResize();
  }

  function destroy(event) {
    if (event?.persisted || destroyed) return;
    destroyed = true;
    copyMotion.cancel();
    visualScroll.destroy();
    title.style.removeProperty('transform');
    framesTrack.style.removeProperty('transform');
    exit.style.removeProperty('opacity');
    hud.style.removeProperty('opacity');
    rail?.style.removeProperty('opacity');
    root.classList.remove('is-enhanced', 'is-program-visible');
    integration.destroy();
    railItems.forEach((item) => item.removeEventListener('click', onRailClick));
    window.removeEventListener('scroll', visualScroll.observe);
    window.removeEventListener('resize', onResize);
    window.removeEventListener('vision:layout', onVisionLayout);
    window.removeEventListener('pageshow', onPageShow);
    window.removeEventListener('pagehide', destroy);
  }

  railItems.forEach((item) => item.addEventListener('click', onRailClick));
  visualScroll.sync(geometry.readTarget());
  window.dispatchEvent(new CustomEvent('program:layout'));
  window.addEventListener('scroll', visualScroll.observe, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('vision:layout', onVisionLayout);
  window.addEventListener('pageshow', onPageShow);
  window.addEventListener('pagehide', destroy);
  return { destroy };
}
