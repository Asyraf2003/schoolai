import { mountTestimonialsLoop } from './testimonials-loop.js';
import { mountTestimonialsRotation } from './testimonials-rotation.js';

export function mountTestimonialsWall(root, signal) {
    const board = root.querySelector('[data-testimonials-wall]');
    if (!board || !window.IntersectionObserver) return { suspend() {}, resume() {} };

    const rows = [...board.querySelectorAll('[data-testimonials-row]')];
    const phone = matchMedia('(max-width: 767px), (max-width: 1023px) and (pointer: coarse)');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const direction = getComputedStyle(board).direction === 'rtl' ? -1 : 1;
    let visible = false;
    let suspended = document.hidden;
    let mode, controller, events;

    function destroy() {
        controller?.destroy();
        events?.abort();
        controller = events = undefined;
        board.removeAttribute('data-testimonials-mode');
    }

    function configure() {
        if (signal.aborted) return;
        const total = board.querySelectorAll('[data-testimonial-card]').length;
        const suitable = rows.length >= 2 && total >= 8;
        const next = reduced.matches || !suitable ? undefined : phone.matches ? 'rotation' : 'loop';
        if (next === mode) {
            controller?.resize();
            return;
        }

        destroy();
        mode = next;
        if (!next || (next === 'rotation' && !board.animate)) return;
        events = new AbortController();
        board.dataset.testimonialsMode = next;
        controller = next === 'rotation'
            ? mountTestimonialsRotation(board, rows, direction, events.signal)
            : mountTestimonialsLoop(rows, direction, events.signal);
        if (visible && !suspended) controller.start();
    }

    const observer = new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;
        if (visible && !suspended) controller?.start();
        else controller?.stop();
    });
    observer.observe(board);

    function suspend() { suspended = true; controller?.stop(); }
    function resume() { suspended = document.hidden; if (!suspended && visible) controller?.start(); }

    signal.addEventListener('abort', () => { observer.disconnect(); destroy(); }, { once: true });
    window.addEventListener('resize', configure, { signal });
    phone.addEventListener('change', configure, { signal });
    reduced.addEventListener('change', configure, { signal });
    document.addEventListener('visibilitychange', () => { if (document.hidden) suspend(); else resume(); }, { signal });
    document.fonts?.ready.then(configure);
    configure();

    return { suspend, resume };
}
