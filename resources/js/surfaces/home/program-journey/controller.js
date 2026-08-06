import { createProgramGeometry } from './geometry.js';
import { createCopyMotion, createVisualScrollEngine } from './motion.js';

export function mountProgramJourney(root) {
  if (!root) return null;

  const frames = Array.from(root.querySelectorAll('[data-program-frame]'));
  const railItems = Array.from(root.querySelectorAll('[data-program-rail-item]'));
  const hud = root.querySelector('[data-program-hud]');
  const framesTrack = root.querySelector('[data-program-frames]');
  const rail = root.querySelector('[data-program-rail]');
  const exit = root.querySelector('[data-program-exit]');
  const exitLines = Array.from(root.querySelectorAll('[data-program-exit-lines] span'));
  const titleSlot = root.querySelector('[data-program-title-slot]');
  const descriptionSlot = root.querySelector('[data-program-description-slot]');
  const link = root.querySelector('[data-program-active-link]');
  const linkLabel = root.querySelector('[data-program-active-link-label]');
  const origin = document.querySelector('[data-program-origin]');
  const titleHome = origin?.querySelector('[data-program-title-home]');
  const descriptionHome = origin?.querySelector('[data-program-description-home]');
  const title = origin?.querySelector('[data-program-origin-title]');
  const description = origin?.querySelector('[data-program-origin-description]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!frames.length || !hud || !framesTrack || !exit || !title || !description
    || !titleHome || !descriptionHome || !titleSlot || !descriptionSlot) return null;

  const introTitle = title.textContent.trim();
  const introDescription = description.textContent.trim();
  const defaultLinkLabel = linkLabel?.textContent.trim() || '';
  const defaultLinkHref = link?.getAttribute('href') || '';
  const copyMotion = createCopyMotion(reducedMotion);
  const geometry = createProgramGeometry(root, frames.length);
  let activeIndex = null;
  let handedOff = false;
  let destroyed = false;

  root.classList.add('is-enhanced');

  function text(node, value) {
    if (node) node.textContent = value || '';
  }

  function captureOrigin() {
    if (handedOff) return;
    const titleRect = title.getBoundingClientRect();
    const descriptionRect = description.getBoundingClientRect();
    const visible = titleRect.bottom > 0 && titleRect.top < window.innerHeight
      && descriptionRect.bottom > 0 && descriptionRect.top < window.innerHeight;
    if (visible) geometry.rememberOrigin(titleRect, descriptionRect);
  }

  function handoffIn() {
    if (handedOff) return;
    handedOff = true;
    if (!geometry.hasOrigin()) {
      geometry.rememberOrigin(
        title.getBoundingClientRect(),
        description.getBoundingClientRect(),
      );
    }
    titleHome.style.minHeight = `${title.offsetHeight}px`;
    descriptionHome.style.minHeight = `${description.offsetHeight}px`;
    descriptionSlot.appendChild(description);
    titleSlot.appendChild(title);
    root.classList.add('has-handoff');
    geometry.measureTitleMotion(title);
  }

  function handoffOut() {
    if (!handedOff) return;
    handedOff = false;
    copyMotion.cancel();
    text(title, introTitle);
    text(description, introDescription);
    text(linkLabel, defaultLinkLabel);
    if (link) link.href = defaultLinkHref;
    title.style.removeProperty('transform');
    titleHome.appendChild(title);
    descriptionHome.appendChild(description);
    titleHome.style.removeProperty('min-height');
    descriptionHome.style.removeProperty('min-height');
    root.classList.remove('has-handoff', 'is-program-visible');
    frames.forEach((frame) => frame.classList.remove('is-active'));
    railItems.forEach((item) => item.setAttribute('aria-current', 'false'));
    activeIndex = null;
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

  function render({ current }) {
    if (destroyed) return;
    const rootRect = root.getBoundingClientRect();
    captureOrigin();

    if (rootRect.top <= 0 && rootRect.bottom > 0) handoffIn();
    if (rootRect.top > 0 || rootRect.bottom <= 0) handoffOut();

    const trackCurrent = geometry.trackPosition(current);
    const exitProgress = geometry.exitProgress(current);
    const programIndex = geometry.programIndex(current);
    framesTrack.style.transform = `translate3d(0, ${(-trackCurrent).toFixed(2)}px, 0)`;
    exit.style.opacity = String(exitProgress);
    hud.style.opacity = String(handedOff ? 1 - exitProgress : 0);
    if (rail) rail.style.opacity = String(handedOff ? 1 - exitProgress : 0);

    if (handedOff) {
      title.style.transform = geometry.titleTransform(current);
      root.classList.toggle('is-program-visible', programIndex >= 0);
      if (programIndex < 0) setIntro();
      else setActive(programIndex);
    }

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

  function onResize() {
    geometry.measure();
    if (handedOff) geometry.measureTitleMotion(title);
    visualScroll.sync(geometry.readTarget());
  }

  function onPageShow(event) {
    if (!event.persisted) return;
    onResize();
  }

  function destroy(event) {
    if (event?.persisted || destroyed) return;
    destroyed = true;
    copyMotion.cancel();
    visualScroll.destroy();
    handoffOut();
    framesTrack.style.removeProperty('transform');
    exit.style.removeProperty('opacity');
    root.classList.remove('is-enhanced');
    window.removeEventListener('scroll', visualScroll.observe);
    window.removeEventListener('resize', onResize);
    window.removeEventListener('pageshow', onPageShow);
    window.removeEventListener('pagehide', destroy);
  }

  geometry.measure();
  visualScroll.sync(geometry.readTarget());
  window.addEventListener('scroll', visualScroll.observe, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('pageshow', onPageShow);
  window.addEventListener('pagehide', destroy);
  return { destroy };
}
