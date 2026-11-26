import {
    createPacingState, queuePacingInput, takePacingStep, wheelPixels,
} from './scroll-pacing-core.js';
import { createPacingPolicy } from './scroll-pacing-policy.js';
import { createNativeScrollCheck } from './scroll-pacing-native.js';

export function mountScrollPacing(root, ports = {}) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const capability = matchMedia('(min-width: 1024px) and (hover: hover) and (pointer: fine)');
    const controller = new AbortController();
    const { signal } = controller;
    const state = createPacingState();
    const policy = createPacingPolicy(root, signal, ports.headingAnimations);
    const native = createNativeScrollCheck(signal);
    let frame = 0;
    let suspended = document.hidden;
    let disposed = false;
    let remainder = 0;
    let expectedScroll = null;

    function reset() {
        if (frame) cancelAnimationFrame(frame);
        frame = 0;
        state.pending = 0;
        state.lastFrame = null; state.velocity = 0; state.deliveredInput = null; state.coasting = false;
        remainder = 0; expectedScroll = null;
    }
    function resetInput() {
        native.invalidate();
        reset();
    }
    function tick() {
        const now = performance.now();
        frame = 0;
        if (suspended || document.hidden || reduced.matches || !capability.matches || disposed) { reset(); return; }
        const before = scrollY;
        if (expectedScroll !== null && Math.abs(before - expectedScroll) > 2) { reset(); return; }
        const gallery = window.ScrollTrigger?.getById('gallery-v2');
        const step = takePacingStep(state, now, policy.ranges(gallery, before), before);
        if (!step) {
            if (state.pending) frame = requestAnimationFrame(tick);
            else reset();
            return;
        }
        // Footer checkpoints constrain the ONE existing wheel owner.
        // Do not deliver residual kinetic debt across a gate.
        const desired = step + remainder;
        const limited = ports.footerGate?.limit(desired, before) ?? desired;
        if (!limited || Math.sign(limited) !== Math.sign(desired)) { reset(); return; }
        const clipped = Math.abs(limited) + .01 < Math.abs(desired);
        scrollBy(0, limited);
        const delivered = scrollY;
        remainder = clipped ? 0 : desired - (delivered - before);
        expectedScroll = delivered;
        const stoppedByGate = ports.footerGate?.advanced(delivered) ?? false;
        // ScrollTrigger otherwise observes this native write on a later tick.
        // Render the existing scrubbed timeline at the delivered position.
        if (gallery?.enabled && delivered >= gallery.start && delivered <= gallery.end) gallery.update();
        if (stoppedByGate) { reset(); return; }
        const max = (document.scrollingElement ?? document.documentElement).scrollHeight - innerHeight;
        if ((step < 0 && scrollY <= 0) || (step > 0 && scrollY >= max - 1)) { reset(); return; }
        if (state.pending) frame = requestAnimationFrame(tick);
    }
    function wheel(event) {
        const now = performance.now();
        if (suspended || document.hidden || reduced.matches || !capability.matches || disposed
            || !event.cancelable || event.defaultPrevented || event.ctrlKey || event.metaKey || event.shiftKey
            || Math.abs(event.deltaX) > Math.abs(event.deltaY) || native.ownsScroll(event, now)
            || document.querySelector('dialog[open], [aria-modal="true"], main[inert]')) { reset(); return; }
        const delta = wheelPixels(event, innerHeight);
        if (!delta) return;
        if (ports.footerGate?.wheel(delta, scrollY, performance.now())) {
            event.preventDefault();
            reset();
            return;
        }
        const scroller = document.scrollingElement ?? document.documentElement;
        if ((delta < 0 && scrollY <= 0)
            || (delta > 0 && scrollY + innerHeight >= scroller.scrollHeight - 1)) {
            reset(); return;
        }
        if (expectedScroll !== null && Math.abs(scrollY - expectedScroll) > 2) reset();
        if (!frame) { state.lastFrame = null; expectedScroll = null; remainder = 0; }
        event.preventDefault();
        if (queuePacingInput(state, delta, performance.now())) {
            cancelAnimationFrame(frame); frame = 0; remainder = 0; expectedScroll = null;
        }
        // Deliver accepted input before a slow first paint can expire it.
        // Later input coalesces in the already scheduled frame.
        if (!frame) tick();
    }
    function reflow() { policy.invalidate(); resetInput(); }
    function change() { policy.invalidate(); if (reduced.matches || !capability.matches) resetInput(); }
    function suspend() { suspended = true; reflow(); }
    function resume() { suspended = document.hidden; reflow(); }
    window.addEventListener('wheel', wheel, { passive: false, signal });
    window.addEventListener('keydown', resetInput, { capture: true, signal });
    window.addEventListener('pointerdown', resetInput, { passive: true, signal });
    window.addEventListener('hashchange', resetInput, { signal });
    window.addEventListener('resize', reflow, { signal });
    window.addEventListener('touchstart', resetInput, { passive: true, signal });
    reduced.addEventListener('change', change, { signal });
    capability.addEventListener('change', change, { signal });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) suspend();
        else resume();
    }, { signal });
    return {
        suspend, resume,
        dispose() { if (disposed) return; disposed = true; suspend(); controller.abort(); },
    };
}
