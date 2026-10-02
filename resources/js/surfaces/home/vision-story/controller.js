import { createVisionGeometry } from './geometry.js';
import { prepareVisionAssets } from './preparation.js';
import { createVisionTimeline } from './timeline.js';

export function mountVisionStory({ signal } = {}) {
    const root = document.querySelector('[data-vision-story]');
    if (!root) return { ready: Promise.resolve({ state: 'absent' }), destroy() {} };
    const wide = window.matchMedia('(min-width: 1024px)');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal };
    const geometry = createVisionGeometry(root);
    let timeline = null;
    let observer = null;
    let resizeTimer = null;
    let frame = null;
    let near = false;
    let destroyed = false;
    let suspended = false;
    let target = 0;
    let current = 0;
    let lastFrameTime = performance.now();
    let assets = null;
    let assetMode = null;

    function render() { timeline?.setProgress(current); }
    function cancelFrame() {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = null;
    }
    function tick(now) {
        frame = null;
        if (destroyed || suspended || document.hidden || !near || !timeline) return;
        const elapsed = Math.min(64, Math.max(1, now - lastFrameTime));
        const alpha = 1 - Math.exp(-elapsed / 88);
        current += (target - current) * alpha;
        lastFrameTime = now;
        if (Math.abs(target - current) > .00015) frame = requestAnimationFrame(tick);
        else current = target;
        render();
    }
    function updateTarget() {
        if (!timeline || destroyed || suspended || document.hidden || !wide.matches) return;
        target = geometry.readProgress();
        if (near && frame === null && target !== current) {
            lastFrameTime = performance.now();
            frame = requestAnimationFrame(tick);
        }
    }
    function disableEnhanced() {
        cancelFrame();
        timeline?.destroy();
        timeline = null;
        root.classList.remove('is-enhanced');
    }
    async function prepare() {
        if (destroyed || signal?.aborted) return { state: 'static-fallback' };
        if (assetMode !== reduced.matches) { assets = null; assetMode = reduced.matches; }
        assets ||= prepareVisionAssets(root, lifecycle.signal, reduced.matches);
        const media = await assets;
        if (destroyed || signal?.aborted) return { state: 'static-fallback' };
        if (!wide.matches || reduced.matches || media === 'semantic-fallback') {
            disableEnhanced();
            root.dataset.visionState = 'static-ready';
        } else {
            root.classList.add('is-enhanced');
            geometry.measure();
            timeline ||= createVisionTimeline(root);
            target = geometry.readProgress();
            current = target;
            render();
            root.dataset.visionState = 'prepared';
        }
        root.classList.remove('is-preparing');
        return { state: root.dataset.visionState, media };
    }
    function fallback() {
        destroy();
        root.dataset.visionState = 'static-fallback';
        return { state: 'static-fallback' };
    }
    function syncMode() {
        if (!wide.matches || reduced.matches) disableEnhanced();
        prepare().catch(fallback);
    }
    function onResize() {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(syncMode, 140);
    }
    function destroy() {
        if (destroyed) return;
        destroyed = true;
        lifecycle.abort();
        window.clearTimeout(resizeTimer);
        disableEnhanced();
        observer?.disconnect();
        root.classList.remove('is-near', 'is-preparing');
        signal?.removeEventListener('abort', fallback);
    }
    window.addEventListener('scroll', updateTarget, { passive: true, ...options });
    window.addEventListener('resize', onResize, options);
    reduced.addEventListener('change', syncMode, options);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) cancelFrame();
        else updateTarget();
    }, options);
    window.addEventListener('pagehide', event => {
        suspended = true;
        cancelFrame();
        if (!event.persisted) destroy();
    }, options);
    window.addEventListener('pageshow', () => { suspended = false; syncMode(); }, options);
    signal?.addEventListener('abort', fallback, { once: true });
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(entries => {
            near = entries.some(entry => entry.isIntersecting);
            root.classList.toggle('is-near', near);
            if (near) updateTarget();
            else cancelFrame();
        }, { rootMargin: '0px 0px -5% 0px', threshold: 0 });
        observer.observe(root);
    } else { near = true; root.classList.add('is-near'); }
    const ready = prepare().catch(fallback);
    return { ready, destroy };
}
