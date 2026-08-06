import { createProgramGeometry } from './geometry.js';
import {
  createCopyMotion,
  createRailMotion,
  createVisualScrollEngine,
  moveWithFlip,
} from './motion.js';

const clamp = (value, min = 0, max = 1) => Math.max(min, Math.min(max, value));

export function mountProgramJourney(root) {
  if (!root) return null;
  const frames = Array.from(root.querySelectorAll('[data-program-frame]'));
  const railItems = Array.from(root.querySelectorAll('[data-program-rail-item]'));
  const hud = root.querySelector('[data-program-hud]');
  const framesTrack = root.querySelector('[data-program-frames]');
  const rail = root.querySelector('.program-journey__rail');
  const railWindow = root.querySelector('[data-program-rail-window]');
  const railTrack = root.querySelector('[data-program-rail]');
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

  if (!frames.length || !hud || !framesTrack || !exit || !title || !description
    || !titleHome || !descriptionHome || !titleSlot || !descriptionSlot) return null;

  const introTitle = title.textContent.trim();
  const introDescription = description.textContent.trim();
  const defaultLabel = label?.textContent.trim() || '';
  const defaultLinkLabel = linkLabel?.textContent.trim() || '';
  const defaultLinkHref = link?.getAttribute('href') || '';
  const copyMotion = createCopyMotion(reducedMotion);
  const railMotion = createRailMotion(railTrack, railWindow, railItems, reducedMotion);
  let activeIndex = -1;
  let mode = 'intro';
  let handedOff = false;
  let destroyed = false;
  let renderFrame = 0;
  let exitProgress = 0;

  root.classList.add('is-enhanced');

  function text(node, value) {
    if (node) node.textContent = value || '';
  }

  const geometry = createProgramGeometry(root, framesTrack, description, frames.length);

  function handoffIn() {
    if (handedOff) return;
    handedOff = true;
    geometry.rememberCopyAnchor();
    titleHome.style.minHeight = `${title.offsetHeight}px`;
    descriptionHome.style.minHeight = `${description.offsetHeight}px`;
    root.classList.add('has-handoff');
    moveWithFlip(title, titleSlot, reducedMotion);
    moveWithFlip(description, descriptionSlot, reducedMotion);
  }

  function handoffOut() {
    if (!handedOff) return;
    handedOff = false;
    copyMotion.cancel();
    text(title, introTitle);
    text(description, introDescription);
    moveWithFlip(title, titleHome, reducedMotion);
    moveWithFlip(description, descriptionHome, reducedMotion);
    titleHome.style.removeProperty('min-height');
    descriptionHome.style.removeProperty('min-height');
    root.classList.remove('has-handoff');
    mode = 'intro';
    activeIndex = -1;
  }

  function setIntro() {
    if (mode === 'intro') return;
    mode = 'intro';
    copyMotion.swap([label, count, title, description, link], () => {
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
    copyMotion.swap([label, count, title, description, link], () => {
      text(label, program.programLabel);
      text(count, `${String(index + 1).padStart(2, '0')} / ${String(frames.length).padStart(2, '0')}`);
      text(title, program.programTitle);
      text(description, program.programDescription);
      if (link) link.href = program.programLink || defaultLinkHref;
      text(linkLabel, defaultLinkLabel);
    });
    railMotion.move(index);
  }

  function renderChrome() {
    renderFrame = 0;
    if (destroyed) return;
    const viewport = window.innerHeight;
    const rootRect = root.getBoundingClientRect();
    if (rootRect.top <= viewport * .82 && rootRect.bottom > 0) handoffIn();
    if (rootRect.top > viewport * .9) handoffOut();

    const exitRect = exit.getBoundingClientRect();
    exitProgress = clamp((viewport - exitRect.top) / viewport);
    hud.style.opacity = String(handedOff ? 1 - clamp(exitProgress * 1.12) : 0);
    hud.style.filter = `blur(${(exitProgress * 18).toFixed(2)}px)`;
    exitLines.forEach((line, index) => {
      const progress = clamp((exitProgress - index * .035) / .72);
      line.style.transform = `scaleX(${progress.toFixed(3)})`;
    });
    if (rail) {
      rail.style.opacity = String(clamp((viewport * .58 - rootRect.top) / (viewport * .36)) * (1 - exitProgress));
    }
  }

  function scheduleChrome() {
    if (!renderFrame) renderFrame = requestAnimationFrame(renderChrome);
  }

  const visualScroll = createVisualScrollEngine({
    reducedMotion,
    readTarget: geometry.readTarget,
    canSnap: () => handedOff && exitProgress < .08 && geometry.travel > 0,
    getAnchors: geometry.anchors,
    snapTo: (local) => window.scrollTo({ top: geometry.documentTarget(local), behavior: 'smooth' }),
    stopSnap: () => window.scrollTo({ top: window.scrollY, behavior: 'auto' }),
    onUpdate: ({ target, current }) => {
      framesTrack.style.transform = `translate3d(0, ${(target - current).toFixed(2)}px, 0)`;
      const rootTop = root.getBoundingClientRect().top;
      if (!handedOff || rootTop > window.innerHeight * .12) setIntro();
      else setActive(geometry.activeIndex(current));
      scheduleChrome();
    },
  });

  function onScroll() {
    scheduleChrome();
    visualScroll.observe();
  }

  function onResize() {
    geometry.measure();
    visualScroll.sync(geometry.readTarget());
    scheduleChrome();
  }

  railItems.forEach((item, index) => {
    item.addEventListener('click', () => {
      visualScroll.snapNow(geometry.anchors()[index]);
    });
  });

  function destroy() {
    if (destroyed) return;
    destroyed = true;
    copyMotion.cancel();
    visualScroll.destroy();
    railMotion.cancel();
    if (renderFrame) cancelAnimationFrame(renderFrame);
    handoffOut();
    framesTrack.style.removeProperty('transform');
    root.classList.remove('is-enhanced');
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('resize', onResize);
    window.removeEventListener('pagehide', destroy);
  }

  geometry.measure();
  visualScroll.sync(geometry.readTarget());
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('pagehide', destroy, { once: true });
  renderChrome();
  return { destroy };
}
