import { detailParts, hideDetails, showDetail } from './geometry.js';
import { mountItemHover, TypeTransition } from './motion.js';

export function mountGsap(dom, integration, gsap) {
  const rtl = document.documentElement.dir === 'rtl';
  const typeTransition = new TypeTransition(gsap, dom.type, dom.typeLines, rtl);
  const cleanHover = mountItemHover(gsap, dom.cards);
  let currentItem = -1;
  let isAnimating = false;
  let opening = false;
  let activeTimeline = null;

  const openItem = (event) => {
    if (isAnimating) return;
    isAnimating = true;
    opening = true;
    currentItem = Number(event.currentTarget.dataset.programIndex);
    const detail = dom.details[currentItem];
    const parts = detailParts(detail);
    const typeIn = typeTransition.in();
    integration.lock(event.currentTarget);
    dom.root.classList.add('is-transitioning');

    activeTimeline = gsap.timeline({ onComplete: () => {
      activeTimeline = null;
      opening = false;
      isAnimating = false;
      dom.root.classList.remove('is-transitioning');
      parts.back?.focus({ preventScroll: true });
    } });

    activeTimeline.addLabel('start', 0)
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
      .set(parts.imageWrap, { y: '100%' }, 'articleOpening')
      .set(parts.image, { y: '-100%' }, 'articleOpening')
      .to(parts.copy, {
        duration: 1, ease: 'expo', opacity: 1, y: '0%', stagger: 0.08,
      }, 'articleOpening')
      .to([parts.imageWrap, parts.image], {
        duration: 1, ease: 'expo', y: '0%',
      }, 'articleOpening');
  };

  const closeItem = () => {
    if (currentItem < 0) return;

    if (isAnimating && opening) {
      activeTimeline?.kill();
      activeTimeline = null;
      opening = false;
      isAnimating = false;
      dom.root.classList.remove('is-transitioning');
    }

    if (isAnimating) return;
    isAnimating = true;
    const parts = detailParts(dom.details[currentItem]);
    const typeOut = typeTransition.out();
    dom.root.classList.add('is-transitioning');

    activeTimeline = gsap.timeline({ onComplete: () => {
      activeTimeline = null;
      isAnimating = false;
      currentItem = -1;
      dom.root.classList.remove('is-transitioning');
      integration.unlock();
    } });

    activeTimeline.addLabel('start', 0)
      .addLabel('typeTransition', 0.5)
      .addLabel('showItems', typeOut.totalDuration() * 0.7 + 0.5)
      .to(parts.copy, {
        duration: 1, ease: 'power4.in', opacity: 0, y: '50%', stagger: -0.08,
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
      }, 'showItems');
  };

  const keydown = (event) => {
    if (currentItem < 0) return;
    if (event.key === 'Escape') { event.preventDefault(); closeItem(); }
    else if (event.key === 'Tab') integration.trapTab(event);
  };

  dom.triggers.forEach((trigger) => trigger.addEventListener('click', openItem));
  dom.backs.forEach((back) => back.addEventListener('click', closeItem));
  document.addEventListener('keydown', keydown);
  const destroy = () => {
    activeTimeline?.kill();
    cleanHover();
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', openItem));
    dom.backs.forEach((back) => back.removeEventListener('click', closeItem));
    document.removeEventListener('keydown', keydown);
    if (currentItem >= 0) integration.unlock();
  };
  destroy.suspend = () => activeTimeline?.pause();
  destroy.resume = () => activeTimeline?.resume();
  return destroy;
}
