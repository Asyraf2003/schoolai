import { collectProgramDom, detailParts, hideDetails, showDetail } from './geometry.js';
import { createProgramDialogIntegration } from './integration.js';
import { loadGsap, mountItemHover, TypeTransition } from './motion.js';

function mountReduced(dom, integration) {
  let activeIndex = -1;
  const open = (event) => {
    activeIndex = Number(event.currentTarget.dataset.programIndex);
    integration.lock(event.currentTarget);
    dom.layer.hidden = false;
    showDetail(dom, activeIndex);
    dom.root.classList.add('is-detail-open');
    dom.back.focus({ preventScroll: true });
  };
  const close = () => {
    if (activeIndex < 0) return;
    dom.layer.hidden = true;
    hideDetails(dom);
    dom.root.classList.remove('is-detail-open');
    activeIndex = -1;
    integration.unlock();
  };
  const keydown = (event) => {
    if (activeIndex < 0) return;
    if (event.key === 'Escape') { event.preventDefault(); close(); }
    else if (event.key === 'Tab') integration.trapTab(event);
  };

  dom.triggers.forEach((trigger) => trigger.addEventListener('click', open));
  dom.back.addEventListener('click', close);
  document.addEventListener('keydown', keydown);
  return () => {
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', open));
    dom.back.removeEventListener('click', close);
    document.removeEventListener('keydown', keydown);
    if (activeIndex >= 0) integration.unlock();
  };
}

function mountGsap(dom, integration, gsap) {
  const rtl = document.documentElement.dir === 'rtl';
  const typeTransition = new TypeTransition(gsap, dom.type, dom.typeLines, rtl);
  const cleanHover = mountItemHover(gsap, dom.cards);
  let currentItem = -1;
  let isAnimating = false;

  const openItem = (event) => {
    if (isAnimating) return;
    isAnimating = true;
    currentItem = Number(event.currentTarget.dataset.programIndex);
    const parts = detailParts(dom.details[currentItem]);
    const typeIn = typeTransition.in();
    integration.lock(event.currentTarget);
    dom.root.classList.add('is-transitioning');

    const timeline = gsap.timeline({ onComplete: () => {
      isAnimating = false;
      dom.root.classList.remove('is-transitioning');
      dom.back.focus({ preventScroll: true });
    } });

    timeline.addLabel('start', 0)
      .addLabel('typeTransition', 0.3)
      .addLabel('articleOpening', typeIn.totalDuration() * 0.75 + 0.3)
      .to(dom.cards, {
        duration: 0.8, ease: 'power2.inOut', opacity: 0,
        y: (position) => position % 2 ? '25%' : '-25%',
      }, 'start')
      .to(dom.header, { duration: 0.8, ease: 'power3', opacity: 0, pointerEvents: 'none' }, 'start')
      .add(typeIn.play(), 'typeTransition')
      .add(() => {
        dom.layer.hidden = false;
        showDetail(dom, currentItem);
        dom.root.classList.add('is-detail-open');
      }, 'articleOpening')
      .set(parts.copy, { opacity: 0, y: '50%' }, 'articleOpening')
      .set(parts.imageWrap, { y: '100%' }, 2)
      .set(parts.image, { y: '-100%' }, 2)
      .to(parts.copy, {
        duration: 1, ease: 'expo', opacity: 1, y: '0%', stagger: 0.04,
      }, 'articleOpening')
      .to([parts.imageWrap, parts.image], { duration: 1, ease: 'expo', y: '0%' }, 'articleOpening');
  };

  const closeItem = () => {
    if (isAnimating || currentItem < 0) return;
    isAnimating = true;
    const parts = detailParts(dom.details[currentItem]);
    const typeOut = typeTransition.out();
    dom.root.classList.add('is-transitioning');

    const timeline = gsap.timeline({ onComplete: () => {
      isAnimating = false;
      currentItem = -1;
      dom.root.classList.remove('is-transitioning');
      integration.unlock();
    } });

    timeline.addLabel('start', 0)
      .addLabel('typeTransition', 0.5)
      .addLabel('showItems', typeOut.totalDuration() * 0.7 + 0.5)
      .to(dom.back, { duration: 0.7, ease: 'power1', opacity: 0 }, 'start')
      .to(parts.copy, {
        duration: 1, ease: 'power4.in', opacity: 0, y: '50%', stagger: -0.04,
      }, 'start')
      .to(parts.imageWrap, { duration: 1, ease: 'power4.in', y: '100%' }, 'start')
      .to(parts.image, { duration: 1, ease: 'power4.in', y: '-100%' }, 'start')
      .add(() => {
        dom.layer.hidden = true;
        hideDetails(dom);
        dom.root.classList.remove('is-detail-open');
      })
      .add(typeOut.play(), 'typeTransition')
      .to(dom.header, {
        duration: 0.8, ease: 'power3', opacity: 1, pointerEvents: 'auto',
      }, 'showItems')
      .to(dom.cards, {
        duration: 1, ease: 'power3.inOut', opacity: 1, y: '0%',
      }, 'showItems')
      .set(dom.back, { opacity: 1 });
  };

  const keydown = (event) => {
    if (currentItem < 0) return;
    if (event.key === 'Escape') { event.preventDefault(); closeItem(); }
    else if (event.key === 'Tab') integration.trapTab(event);
  };

  dom.triggers.forEach((trigger) => trigger.addEventListener('click', openItem));
  dom.back.addEventListener('click', closeItem);
  document.addEventListener('keydown', keydown);
  return () => {
    cleanHover();
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', openItem));
    dom.back.removeEventListener('click', closeItem);
    document.removeEventListener('keydown', keydown);
    if (currentItem >= 0) integration.unlock();
  };
}

export function mountProgramJourney(root) {
  const dom = collectProgramDom(root);
  if (!dom.triggers.length || !dom.layer || !dom.back || !dom.type) return () => {};
  root.classList.add('is-enhanced');
  const integration = createProgramDialogIntegration(dom);
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return mountReduced(dom, integration);

  let cleanup = () => {};
  let disposed = false;
  loadGsap().then((gsap) => {
    if (disposed) return;
    root.classList.add('has-gsap');
    cleanup = mountGsap(dom, integration, gsap);
  }).catch(() => root.classList.add('gsap-failed'));

  return () => {
    disposed = true;
    cleanup();
    root.classList.remove('is-enhanced', 'has-gsap', 'gsap-failed', 'is-transitioning', 'is-detail-open');
  };
}
