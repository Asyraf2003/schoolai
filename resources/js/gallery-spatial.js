import { createSpatialRenderer } from './gallery-spatial-renderer.js';
import { mountSpatialInput } from './gallery-spatial-input.js';

export function mountSpatialGallery(root) {
    const abort = new AbortController(), { signal } = abort;
    const links = [...root.querySelectorAll('[data-spatial-panel]')];
    const state = { links, centers: [], target: 0, position: 0, limit: 0, scale: 1, velocity: 0, dragging: false };
    const preference = matchMedia('(prefers-reduced-motion: reduce)');
    let frame = 0, last = 0, visible = true, suspended = false, disposed = false, graphics;
    const allowed = () => visible && !suspended && !document.hidden && !document.documentElement.hasAttribute('data-gallery-viewing');
    const wake = () => { if (!frame && !disposed && allowed()) frame = requestAnimationFrame(tick); };
    const stop = () => { cancelAnimationFrame(frame); frame = 0; last = 0; };
    const reset = () => {
        delete root.dataset.spatialReady;
        links.forEach(link => { link.removeAttribute('style'); link.querySelector('span').removeAttribute('style'); });
    };
    const dispose = () => {
        if (disposed) return;
        disposed = true; stop(); abort.abort(); observer.disconnect(); resize.disconnect(); modal.disconnect();
        graphics?.dispose(); reset();
    };
    function tick(time) {
        frame = 0;
        if (!allowed()) { last = 0; return; }
        const dt = Math.min(.05, last ? (time - last) / 1000 : 1 / 60); last = time;
        if (!state.dragging) {
            state.velocity *= Math.exp(-7 * dt);
            state.target = Math.max(0, Math.min(state.limit, state.target + state.velocity * dt));
        }
        state.position += (state.target - state.position) * (preference.matches ? 1 : 1 - Math.exp(-12 * dt));
        if (!graphics.draw()) { dispose(); return; }
        if (Math.abs(state.target - state.position) > .0001 || Math.abs(state.velocity) > .001) wake();
        else last = 0;
    }
    const observer = new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; if (visible) wake(); else stop(); });
    const resize = new ResizeObserver(() => { graphics?.resize(); wake(); });
    const modal = new MutationObserver(() => { if (allowed()) wake(); else { state.velocity = 0; stop(); } });
    try { graphics = createSpatialRenderer(root, state, wake); } catch { dispose(); return; }
    state.target = state.position = state.centers[Math.min(1, links.length - 1)] ?? 0;
    root.dataset.spatialReady = '';
    mountSpatialInput(root, state, wake, signal);
    graphics.canvas.addEventListener('webglcontextlost', event => { event.preventDefault(); dispose(); }, { signal });
    observer.observe(root); resize.observe(root);
    modal.observe(document.documentElement, { attributes: true, attributeFilter: ['data-gallery-viewing'] });
    document.addEventListener('visibilitychange', () => { if (allowed()) wake(); else stop(); }, { signal });
    document.addEventListener('public:navigate', () => { suspended = true; stop(); }, { signal });
    window.addEventListener('pagehide', event => { if (event.persisted) { suspended = true; stop(); } else dispose(); }, { signal });
    window.addEventListener('pageshow', () => { suspended = false; wake(); }, { signal });
    preference.addEventListener('change', () => { state.velocity = 0; wake(); }, { signal });
    wake();
}
