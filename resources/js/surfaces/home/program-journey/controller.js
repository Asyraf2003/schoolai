import { createProgramGeometry } from './geometry.js';
import { createCopyMotion, createVisualScrollEngine, moveWithFlip } from './motion.js';

const clamp = (value, min = 0, max = 1) => Math.max(min, Math.min(max, value));

export function mountProgramJourney(root) {
  if (!root) return null;
  const frames = Array.from(root.querySelectorAll('[data-program-frame]'));
  const railItems = Array.from(root.querySelectorAll('[data-program-rail-item]'));
  const hud = root.querySelector('[data-program-hud]');
  const framesTrack = root.querySelector('[data-program-frames]');
  const curtain = root.querySelector('[data-program-curtain]');
  const rail = root.querySelector('[data-program-rail]');
  const exit = root.querySelector('[data-program-exit]');
  const exitLines = Array.from(root.querySelectorAll('[data-program-exit-lines] span'));
  const label = root.querySelector('[data-program-active-label]');
  const count = root.querySelector('[data-program-active-count]');
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

  if (!frames.length || !hud || !framesTrack || !curtain || !exit || !title
    || !description || !titleHome || !descriptionHome || !titleSlot
    || !descriptionSlot) return null;

  const introTitle = title.textContent.trim();
  const introDescription = description.textContent.trim();
  const defaultLabel = label?.textContent.trim() || '';
  const defaultLinkLabel = linkLabel?.textContent.trim() || '';
  const defaultLinkHref = link?.getAttribute('href') || '';
  const copyMotion = createCopyMotion(reducedMotion);
  const geometry = createProgramGeometry(root, description, frames.length);
  let activeIndex = -1;
  let mode = 'intro';
  let handedOff = false;
  let destroyed = false;

  root.classList.add('is-enhanced');

  function text(node, value) {
    if (node) node.textContent = value || '';
  }

  function handoffIn() {
    if (handedOff) return;
    handedOff = true;
    geometry.rememberCopyAnchor();
    titleHome.style.minHeight = `${title.offsetHeight}px`;
    descriptionHome.style.minHeight = `${description.offsetHeight}px`;
    descriptionSlot.appendChild(description);
    root.classList.add('has-handoff');
    moveWithFlip(title, titleSlot, reducedMotion);
  }

  function handoffOut() {
    if (!handedOff) return;
    handedOff = false;
    copyMotion.cancel();
    text(title, introTitle);
    text(description, introDescription);
    moveWithFlip(title, titleHome, reducedMotion);
    descriptionHome.appendChild(description);
    titleHome.style.removeProperty('min-height');
    descriptionHome.style.removeProperty('min-height');
    root.classList.remove('has-handoff', 'is-media-visible');
    mode = 'intro';
    activeIndex = -1;
  }

  function swapCopy(update) {
    copyMotion.swap({
      moving: [label, count, link],
      stable: [title, description],
      update,
    });
  }

  function setIntro() {
    if (mode === 'intro') return;
    mode = 'intro';
    activeIndex = -1;
    frames.forEach((frame, index) => frame.classList.toggle('is-active', index === 0));
    railItems.forEach((item, index) => item.setAttribute('aria-current', index === 0 ? 'true' : 'false'));
    swapCopy(() => {
      text(label, defaultLabel);
      text(count, '');
      text(title, introTitle);
      text(description, introDescription);
      text(linkLabel, defaultLinkLabel);
      if (link) link.href = defaultLinkHref;
    });
  }

  function setActive(index) {
    if (index === activeIndex && mode === 'program') return;
    const program = frames[index]?.dataset;
    if (!program) return;
    activeIndex = index;
    mode = 'program';
    frames.forEach((frame, i) => frame.classList.toggle('is-active', i === index));
    railItems.forEach((item, i) => item.setAttribute('aria-current', i === index ? 'true' : 'false'));
    root.style.setProperty('--program-accent', program.programAccent || '#0ea5e9');
    swapCopy(() => {
      text(label, program.programLabel);
      text(count, `${String(index + 1).padStart(2, '0')} / ${String(frames.length).padStart(2, '0')}`);
      text(title, program.programTitle);
      text(description, program.programDescription);
      if (link) link.href = program.programLink || defaultLinkHref;
      text(linkLabel, defaultLinkLabel);
    });
  }

  function render({ current }) {
    if (destroyed) return;
    const viewport = window.innerHeight;
    const rootRect = root.getBoundingClientRect();
    const rootTop = rootRect.top;
    if (rootTop <= viewport * .82 && rootRect.bottom > 0) handoffIn();
    if (rootTop > viewport * .9) handoffOut();

    const entryProgress = geometry.entryProgress(current);
    const mediaCurrent = geometry.mediaPosition(current);
    const exitProgress = geometry.exitProgress(current);
    curtain.style.transform = `translate3d(0, ${(-entryProgress * 100).toFixed(3)}%, 0)`;
    framesTrack.style.transform = `translate3d(0, ${(-mediaCurrent).toFixed(2)}px, 0)`;
    exit.style.opacity = String(exitProgress);
    hud.style.opacity = String(handedOff ? 1 - clamp(exitProgress * 1.12) : 0);
    hud.style.filter = `blur(${(exitProgress * 14).toFixed(2)}px)`;
    if (rail) rail.style.opacity = String(handedOff ? 1 - exitProgress : 0);
    root.classList.toggle('is-media-visible', entryProgress >= .55);
    exitLines.forEach((line, index) => {
      const progress = clamp((exitProgress - index * .035) / .72);
      line.style.transform = `scaleX(${progress.toFixed(3)})`;
    });

    if (!handedOff || entryProgress < .55) setIntro();
    else setActive(geometry.activeIndex(current));
  }

  const visualScroll = createVisualScrollEngine({
    reducedMotion,
    readTarget: geometry.readTarget,
    onUpdate: render,
  });

  function onScroll() {
    visualScroll.observe();
  }

  function onResize() {
    geometry.measure();
    visualScroll.sync(geometry.readTarget());
  }

  function onPageShow(event) {
    if (!event.persisted) return;
    geometry.measure();
    visualScroll.sync(geometry.readTarget());
  }

  function destroy(event) {
    if (event?.persisted || destroyed) return;
    destroyed = true;
    copyMotion.cancel();
    visualScroll.destroy();
    handoffOut();
    framesTrack.style.removeProperty('transform');
    curtain.style.removeProperty('transform');
    exit.style.removeProperty('opacity');
    root.classList.remove('is-enhanced');
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onResize);
    window.removeEventListener('pageshow', onPageShow);
    window.removeEventListener('pagehide', destroy);
  }

  geometry.measure();
  visualScroll.sync(geometry.readTarget());
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('pageshow', onPageShow);
  window.addEventListener('pagehide', destroy);
  return { destroy };
}
