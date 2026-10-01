import { detailParts, hideDetails, showDetail } from './geometry.js';

const mounted = new WeakMap();

export function mountReduced(dom, integration) {
  mounted.get(dom.root)?.();
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
  const cleanup = () => {
    dom.triggers.forEach((trigger) => trigger.removeEventListener('click', open));
    dom.backs.forEach((back) => back.removeEventListener('click', close));
    document.removeEventListener('keydown', keydown);
    if (activeIndex >= 0) integration.unlock();
    mounted.delete(dom.root);
  };
  mounted.set(dom.root, cleanup);
  return cleanup;
}

