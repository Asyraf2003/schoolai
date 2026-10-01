import { collectProgramDom, detailParts } from './geometry.js';
import { mountGsap } from './gsap-controller.js';
import { mountProgramHeading } from './heading.js';
import { createProgramDialogIntegration } from './integration.js';
import { loadGsap } from './motion.js';

import { mountReduced } from './reduced-controller.js';

function withReady(cleanup, ready = Promise.resolve()) {
  cleanup.ready = ready;
  return cleanup;
}

export function mountProgramJourney(root, { signal } = {}) {
  const dom = collectProgramDom(root);
  if (!dom.triggers.length || !dom.layer || !dom.backs.length || !dom.type) {
    return withReady(() => {});
  }

  root.classList.add('is-enhanced');
  const cleanHeading = mountProgramHeading(root);
  const integration = createProgramDialogIntegration(dom);

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  const lifecycle = new AbortController();
  let cleanup = () => {};
  let disposed = false;
  let started = false;
  let pendingTrigger = null;
  let resolveReady;
  const ready = new Promise(resolve => { resolveReady = resolve; });
  const settled = state => {
    root.dataset.programReady = state;
    resolveReady({ state });
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

  const startEnhanced = () => {
    if (started || disposed) return;
    started = true;

    loadGsap({ signal: AbortSignal.any([lifecycle.signal, ...(signal ? [signal] : [])]) }).then((gsap) => {
      if (disposed || signal?.aborted) return;
      if (reduced.matches) { installStatic(); return; }
      clearPending();
      root.classList.add('has-gsap');
      cleanup = mountGsap(dom, integration, gsap);
      settled('gsap');
      replayPending();
    }).catch(() => {
      if (disposed) return;
      root.classList.add('gsap-failed');
      installStatic();
    });
  };

  function installStatic() {
    clearPending();
    cleanup();
    dom.layer.hidden = true;
    root.classList.remove('has-gsap', 'is-transitioning', 'is-detail-open');
    cleanup = mountReduced(dom, integration);
    settled('static-fallback');
    replayPending();
  }

  function pendingOpen(event) {
    event.preventDefault();
    pendingTrigger = event.currentTarget;
    startEnhanced();
  }

  dom.triggers.forEach((trigger) => trigger.addEventListener('click', pendingOpen));

  signal?.addEventListener('abort', installStatic, { once: true, signal: lifecycle.signal });
  if (reduced.matches || signal?.aborted) installStatic();
  else startEnhanced();

  const destroy = () => {
    if (disposed) return;
    disposed = true;
    lifecycle.abort();
    resolveReady({ state: 'disposed' });
    clearPending();
    cleanup();
    cleanHeading();
    root.classList.remove(
      'is-enhanced', 'has-gsap', 'gsap-failed', 'is-transitioning',
      'is-detail-open', 'is-program-heading-revealed',
    );
  };

  const suspend = () => cleanup.suspend?.();
  const resume = () => { if (!document.hidden) cleanup.resume?.(); };
  window.addEventListener('pagehide', event => event.persisted ? suspend() : destroy(), { signal: lifecycle.signal });
  window.addEventListener('pageshow', resume, { signal: lifecycle.signal });
  document.addEventListener('visibilitychange', () => document.hidden ? suspend() : resume(), { signal: lifecycle.signal });
  reduced.addEventListener('change', () => {
    if (!reduced.matches) {
      cleanup(); dom.layer.hidden = true;
      root.classList.remove('is-detail-open', 'is-transitioning');
      started = false; startEnhanced(); return;
    }
    installStatic();
    const details = dom.details.flatMap(detail => {
      const parts = detailParts(detail);
      return [...parts.copy, parts.imageWrap, parts.image];
    });
    window.gsap?.set([...dom.cards, dom.header, dom.type, ...dom.typeLines, ...details].filter(Boolean), { clearProps: 'transform,opacity,pointerEvents' });
  }, { signal: lifecycle.signal });
  return withReady(destroy, ready);
}
