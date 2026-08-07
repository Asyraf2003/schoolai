import { createProgramGeometry } from './geometry.js';
import { createCopyMotion, createVisualScrollEngine } from './motion.js';

function setBaseBox(node, box) {
  if (!node || !box) return;
  node.style.left = `${box.x.toFixed(2)}px`;
  node.style.top = `${box.y.toFixed(2)}px`;
  node.style.width = `${box.w.toFixed(2)}px`;
  node.style.height = `${box.h.toFixed(2)}px`;
}

function transformTo(node, base, target) {
  if (!node || !base || !target || !base.w || !base.h) return;
  const scaleX = target.w / base.w;
  const scaleY = target.h / base.h;
  const translateX = target.x - base.x;
  const translateY = target.y - base.y;
  node.style.transform = `matrix(${scaleX.toFixed(5)}, 0, 0, ${scaleY.toFixed(5)}, ${translateX.toFixed(2)}, ${translateY.toFixed(2)})`;
}

export function mountProgramJourney(root) {
  if (!root) return null;

  const handoff = root.querySelector('[data-program-handoff]');
  const mission = root.querySelector('[data-program-handoff-mission]');
  const main = root.querySelector('[data-program-handoff-main]');
  const thumbOne = root.querySelector('[data-program-handoff-thumb-one]');
  const thumbTwo = root.querySelector('[data-program-handoff-thumb-two]');
  const showcase = root.querySelector('[data-program-showcase]');
  const titleBox = root.querySelector('[data-program-title-box]');
  const copyBox = root.querySelector('[data-program-copy-box]');
  const description = root.querySelector('[data-program-hud-description]');
  const link = root.querySelector('[data-program-active-link]');
  const backgrounds = root.querySelector('[data-program-backgrounds]');
  const frames = Array.from(root.querySelectorAll('[data-program-frame]'));
  const rail = root.querySelector('[data-program-rail]');
  const railItems = Array.from(root.querySelectorAll('[data-program-rail-item]'));
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!handoff || !mission || !main || !thumbOne || !thumbTwo || !showcase
    || !titleBox || !copyBox || !description || !backgrounds || !frames.length) return null;

  const introDescription = description.textContent.trim();
  const defaultLink = link?.getAttribute('href') || '';
  const geometry = createProgramGeometry(root);
  const copyMotion = createCopyMotion(reducedMotion);
  let baseBoxes = null;
  let activeIndex = -2;
  let destroyed = false;

  root.classList.add('is-enhanced');

  function setActive(index) {
    if (index === activeIndex) return;
    activeIndex = index;

    railItems.forEach((item, itemIndex) => {
      item.setAttribute('aria-current', itemIndex === index ? 'true' : 'false');
    });

    if (index < 0) {
      copyMotion.swap({
        stable: [description],
        update: () => {
          description.textContent = introDescription;
          if (link) link.href = defaultLink;
        },
      });
      return;
    }

    const data = frames[index]?.dataset;
    if (!data) return;
    root.style.setProperty('--program-accent', data.programAccent || '#0ea5e9');
    copyMotion.swap({
      stable: [description],
      update: () => {
        description.textContent = data.programDescriptionValue || introDescription;
        if (link) link.href = data.programLinkValue || defaultLink;
      },
    });
  }

  function render({ current }) {
    if (destroyed || !baseBoxes) return;
    const state = geometry.state(current, frames.length);

    transformTo(mission, baseBoxes.mission, state.boxes.mission);
    transformTo(main, baseBoxes.main, state.boxes.main);
    transformTo(thumbOne, baseBoxes.thumbOne, state.boxes.thumbOne);
    transformTo(thumbTwo, baseBoxes.thumbTwo, state.boxes.thumbTwo);
    transformTo(titleBox, baseBoxes.title, state.boxes.title);
    transformTo(copyBox, baseBoxes.copy, state.boxes.copy);

    mission.style.opacity = state.missionOpacity.toFixed(3);
    thumbOne.style.opacity = state.thumbOneOpacity.toFixed(3);
    handoff.style.opacity = state.handoffOpacity.toFixed(3);
    showcase.style.opacity = state.showcaseOpacity.toFixed(3);
    showcase.style.color = state.textColor;
    backgrounds.style.opacity = state.backgroundsOpacity.toFixed(3);
    if (rail) rail.style.opacity = state.railOpacity.toFixed(3);

    frames.forEach((frame, index) => {
      frame.style.opacity = String(state.frameOpacities[index] || 0);
      frame.classList.toggle('is-active', index === state.activeIndex);
    });

    root.classList.toggle('is-program-loop', state.activeIndex >= 0);
    setActive(state.activeIndex);
  }

  const visualScroll = createVisualScrollEngine({
    reducedMotion,
    readTarget: geometry.readTarget,
    onUpdate: render,
  });

  function refreshGeometry() {
    geometry.measure();
    const start = geometry.state(0, frames.length).boxes;
    const showcaseFinal = geometry.state(.60, frames.length).boxes;
    baseBoxes = {
      mission: start.mission,
      main: start.main,
      thumbOne: start.thumbOne,
      thumbTwo: start.thumbTwo,
      title: showcaseFinal.title,
      copy: showcaseFinal.copy,
    };

    setBaseBox(mission, baseBoxes.mission);
    setBaseBox(main, baseBoxes.main);
    setBaseBox(thumbOne, baseBoxes.thumbOne);
    setBaseBox(thumbTwo, baseBoxes.thumbTwo);
    setBaseBox(titleBox, baseBoxes.title);
    setBaseBox(copyBox, baseBoxes.copy);
    visualScroll.sync(geometry.readTarget());
  }

  function onResize() {
    refreshGeometry();
  }

  function onPageShow(event) {
    if (event.persisted) refreshGeometry();
  }

  function destroy(event) {
    if (event?.persisted || destroyed) return;
    destroyed = true;
    copyMotion.cancel();
    visualScroll.destroy();
    window.removeEventListener('scroll', visualScroll.observe);
    window.removeEventListener('resize', onResize);
    window.removeEventListener('pageshow', onPageShow);
    window.removeEventListener('pagehide', destroy);
    root.classList.remove('is-enhanced', 'is-program-loop');
    root.style.removeProperty('height');
  }

  refreshGeometry();
  window.addEventListener('scroll', visualScroll.observe, { passive: true });
  window.addEventListener('resize', onResize, { passive: true });
  window.addEventListener('pageshow', onPageShow, { passive: true });
  window.addEventListener('pagehide', destroy);

  return { destroy };
}
