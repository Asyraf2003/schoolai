import { collectProgramDom, detailParts, hideDetails, showDetail } from './geometry.js';
import { createProgramDialogIntegration } from './integration.js';
import { cardsIn, cardsOut, detailIn, detailOut, typeIn, typeOut, wait } from './motion.js';

export function mountProgramJourney(root) {
  const dom = collectProgramDom(root);
  if (!dom.triggers.length || !dom.layer || !dom.back) return () => {};

  const reducedQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  const integration = createProgramDialogIntegration(dom);
  const rtl = document.documentElement.dir === 'rtl';
  let activeIndex = -1;
  let activeParts = null;
  let busy = false;

  root.classList.add('is-enhanced');

  const open = async (index, trigger) => {
    if (busy || index < 0 || index >= dom.details.length) return;
    busy = true;
    root.classList.add('is-transitioning');
    integration.lock(trigger);

    const reduced = reducedQuery.matches;
    const typePromise = typeIn(dom.type, dom.typeLines, reduced, rtl);
    const cardsPromise = cardsOut(dom.cards, reduced);
    if (!reduced) await wait(920);

    activeIndex = index;
    dom.layer.hidden = false;
    const detail = showDetail(dom, activeIndex);
    activeParts = detailParts(detail);
    root.classList.add('is-detail-open');

    await detailIn(activeParts, reduced);
    await Promise.all([typePromise, cardsPromise]);
    root.classList.remove('is-transitioning');
    dom.back.focus({ preventScroll: true });
    busy = false;
  };

  const close = async () => {
    if (busy || activeIndex < 0) return;
    busy = true;
    root.classList.add('is-transitioning');
    const reduced = reducedQuery.matches;
    await detailOut(activeParts, reduced);

    const typePromise = typeOut(dom.type, dom.typeLines, reduced, rtl);
    if (!reduced) await wait(560);
    dom.layer.hidden = true;
    hideDetails(dom);
    root.classList.remove('is-detail-open');

    await Promise.all([typePromise, cardsIn(dom.cards, reduced)]);
    activeIndex = -1;
    activeParts = null;
    root.classList.remove('is-transitioning');
    integration.unlock();
    busy = false;
  };

  const onTrigger = (event) => {
    open(Number(event.currentTarget.dataset.programIndex), event.currentTarget);
  };

  const onKeydown = (event) => {
    if (activeIndex < 0) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      close();
    } else if (event.key === 'Tab') {
      integration.trapTab(event);
    }
  };

  dom.triggers.forEach((trigger) => trigger.addEventListener('click', onTrigger));
  dom.back.addEventListener('click', close);
  document.addEventListener('keydown', onKeydown);

  return () => {
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', onTrigger));
    dom.back.removeEventListener('click', close);
    document.removeEventListener('keydown', onKeydown);
    if (activeIndex >= 0) integration.unlock();
    root.classList.remove('is-enhanced', 'is-transitioning', 'is-detail-open');
  };
}
