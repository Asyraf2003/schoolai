import { collectProgramDom, detailParts, hideDetails, showDetail } from './geometry.js';
import { mountGsap } from './gsap-controller.js';
import { mountProgramHeading } from './heading.js';
import { createProgramDialogIntegration } from './integration.js';
import { loadGsap } from './motion.js';

function mountReduced(dom, integration) {
  let activeIndex = -1;
  const open = (event) => {
    activeIndex = Number(event.currentTarget.dataset.programIndex);
    integration.lock(event.currentTarget);
    dom.layer.hidden = false;
    const detail = showDetail(dom, activeIndex);
    dom.root.classList.add('is-detail-open');
    detailParts(detail).back?.focus({ preventScroll: true });
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
  dom.backs.forEach((back) => back.addEventListener('click', close));
  document.addEventListener('keydown', keydown);
  return () => {
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', open));
    dom.backs.forEach((back) => back.removeEventListener('click', close));
    document.removeEventListener('keydown', keydown);
    if (activeIndex >= 0) integration.unlock();
  };
}

export function mountProgramJourney(root) {
  const dom = collectProgramDom(root);
  if (!dom.triggers.length || !dom.layer || !dom.backs.length || !dom.type) return () => {};
  root.classList.add('is-enhanced');
  const cleanHeading = mountProgramHeading(root);
  const integration = createProgramDialogIntegration(dom);

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const cleanReduced = mountReduced(dom, integration);
    return () => {
      cleanReduced();
      cleanHeading();
      root.classList.remove('is-enhanced', 'is-program-heading-revealed', 'is-detail-open');
    };
  }

  let cleanup = () => {};
  let disposed = false;
  let pendingTrigger = null;

  const pendingOpen = (event) => {
    event.preventDefault();
    pendingTrigger = event.currentTarget;
  };

  const clearPending = () => {
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', pendingOpen));
  };

  const replayPending = () => {
    if (!pendingTrigger || disposed) return;
    const trigger = pendingTrigger;
    pendingTrigger = null;
    queueMicrotask(() => {
      if (!disposed) trigger.click();
    });
  };

  dom.triggers.forEach((trigger) => trigger.addEventListener('click', pendingOpen));

  loadGsap().then((gsap) => {
    if (disposed) return;
    clearPending();
    root.classList.add('has-gsap');
    cleanup = mountGsap(dom, integration, gsap);
    replayPending();
  }).catch(() => {
    if (disposed) return;
    clearPending();
    root.classList.add('gsap-failed');
    cleanup = mountReduced(dom, integration);
    replayPending();
  });

  return () => {
    disposed = true;
    clearPending();
    cleanup();
    cleanHeading();
    root.classList.remove(
      'is-enhanced', 'has-gsap', 'gsap-failed', 'is-transitioning',
      'is-detail-open', 'is-program-heading-revealed',
    );
  };
}
