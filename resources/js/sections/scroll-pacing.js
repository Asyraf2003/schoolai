import {
    createPacingState, queuePacingInput, takePacingStep, wheelPixels,
} from './scroll-pacing-core.js';

export function mountScrollPacing(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const controller = new AbortController();
    const { signal } = controller;
    const state = createPacingState();
    let frame = 0;
    let suspended = document.hidden;
    let disposed = false;

    function reset() {
        if (frame) cancelAnimationFrame(frame);
        frame = 0;
        state.pending = 0;
        state.lastFrame = null;
    }
    function targetOwnsScroll(target) {
        let node = target instanceof Element ? target : target?.parentElement;
        while (node && node !== document.body) {
            if (node.matches('textarea, select, iframe, dialog, [role="dialog"], [contenteditable], [data-scroll-native]')) return true;
            const overflow = getComputedStyle(node).overflowY;
            if (/^(auto|scroll)$/.test(overflow) && node.scrollHeight > node.clientHeight + 1) return true;
            node = node.parentElement;
        }
        return false;
    }
    function tick(now) {
        frame = 0;
        if (suspended || document.hidden || reduced.matches || disposed) { reset(); return; }
        const step = takePacingStep(state, now);
        if (!step) return;
        const before = scrollY;
        scrollBy({ top: step, behavior: 'instant' });
        if (Math.abs(scrollY - before) < .01) { reset(); return; }
        if (state.pending) frame = requestAnimationFrame(tick);
    }
    function wheel(event) {
        if (suspended || document.hidden || reduced.matches || disposed
            || !event.cancelable || event.defaultPrevented || event.ctrlKey || event.metaKey
            || Math.abs(event.deltaX) > Math.abs(event.deltaY) || targetOwnsScroll(event.target)
            || document.querySelector('dialog[open], [aria-modal="true"], main[inert]')) return;
        const delta = wheelPixels(event, innerHeight);
        if (!delta) return;
        const scroller = document.scrollingElement ?? document.documentElement;
        if ((delta < 0 && scrollY <= 0)
            || (delta > 0 && scrollY + innerHeight >= scroller.scrollHeight - 1)) {
            reset(); return;
        }
        event.preventDefault();
        queuePacingInput(state, delta, performance.now());
        if (!frame) frame = requestAnimationFrame(tick);
    }
    function change() { if (reduced.matches) reset(); }
    function suspend() { suspended = true; reset(); }
    function resume() { suspended = document.hidden; reset(); }
    window.addEventListener('wheel', wheel, { passive: false, signal });
    window.addEventListener('keydown', reset, { capture: true, signal });
    window.addEventListener('pointerdown', reset, { passive: true, signal });
    window.addEventListener('hashchange', reset, { signal });
    reduced.addEventListener('change', change, { signal });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) suspend();
        else resume();
    }, { signal });
    return {
        suspend, resume,
        dispose() { if (disposed) return; disposed = true; suspend(); controller.abort(); },
    };
}
