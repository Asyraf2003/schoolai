import { nearestOccurrence } from './gallery-spatial-geometry.js';

export function mountSpatialInput(root, state, wake, signal) {
    const viewport = root.querySelector('[data-spatial-viewport]');
    let drag, suppress = false, pointerX, restoring = false;
    document.addEventListener('gallery:viewer', event => { restoring = true; pointerX = undefined; if (!event.detail) queueMicrotask(() => { restoring = false; }); }, { signal });
    const direction = document.documentElement.dir === 'rtl' ? -1 : 1;
    const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;
    const shift = pixels => { state.target += pixels * state.scale * direction; wake(); };
    viewport.addEventListener('pointerdown', event => {
        if (event.button !== 0 || !event.isPrimary) return;
        drag = { id: event.pointerId, x: event.clientX, y: event.clientY, last: event.clientX, time: event.timeStamp, active: false };
        state.velocity = 0; suppress = false;
    }, { signal });
    viewport.addEventListener('pointermove', event => {
        if (!drag) {
            if (event.pointerType === 'mouse' && !reduced() && pointerX !== undefined) shift(-(event.clientX - pointerX) * .12);
            pointerX = event.clientX;
            return;
        }
        if (event.pointerId !== drag.id) return;
        const dx = event.clientX - drag.x, dy = event.clientY - drag.y;
        if (!drag.active && Math.abs(dy) > Math.abs(dx) && Math.abs(dy) > 8) { drag = null; return; }
        if (!drag.active && Math.abs(dx) < 8) return;
        if (!drag.active) {
            drag.active = true; suppress = true;
            viewport.setPointerCapture(event.pointerId);
            root.dataset.spatialDragging = '';
        }
        const delta = drag.last - event.clientX;
        const dt = Math.max(.008, (event.timeStamp - drag.time) / 1000);
        state.velocity = state.velocity * .45 + delta * state.scale * direction / dt * .55;
        shift(delta); drag.last = event.clientX; drag.time = event.timeStamp;
        state.dragging = true;
    }, { signal });
    const release = event => {
        if (!drag || event.pointerId !== drag.id) return;
        if (event.type === 'pointercancel' || reduced() || event.timeStamp - drag.time > 100) state.velocity = 0;
        if (viewport.hasPointerCapture(drag.id)) viewport.releasePointerCapture(drag.id);
        drag = null; state.dragging = false; pointerX = undefined;
        delete root.dataset.spatialDragging; wake();
    };
    viewport.addEventListener('pointerup', release, { signal });
    viewport.addEventListener('pointercancel', release, { signal });
    viewport.addEventListener('pointerleave', () => { pointerX = undefined; }, { signal });
    viewport.addEventListener('dragstart', e => e.preventDefault(), { signal });
    viewport.addEventListener('click', event => {
        if (suppress) { event.preventDefault(); event.stopPropagation(); suppress = false; }
    }, { signal, capture: true });
    viewport.addEventListener('wheel', event => {
        if (Math.abs(event.deltaX) <= Math.abs(event.deltaY) || event.ctrlKey) return;
        const delta = event.deltaX * (event.deltaMode === 1 ? 16 : event.deltaMode === 2 ? viewport.clientWidth : 1);
        event.preventDefault(); state.velocity = 0; shift(delta);
    }, { signal, passive: false });
    viewport.addEventListener('focusin', event => {
        if (restoring || drag) return;
        const index = state.links.indexOf(event.target.closest('[data-spatial-panel]'));
        if (index < 0) return;
        state.target = nearestOccurrence(index, state.position, state.links.length); state.velocity = 0; wake();
    }, { signal });
}
