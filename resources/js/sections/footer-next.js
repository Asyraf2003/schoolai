import { createFooterGate } from './footer-gate.js';

export function mountFooterNext(root) {
    const white = root?.querySelector('.site-footer-v2__white');
    const shell = root?.querySelector('[data-footer-next-shell]');
    const panel = shell?.querySelector('[data-footer-next]');
    const next = panel?.querySelector('[data-footer-next-main]');
    if (!white || !shell || !panel || !next) {
        return { wheel() { return false; }, limit(step) { return step; },
            advanced() { return false; }, suspend() {}, resume() {}, dispose() {} };
    }
    let suspended = false, disposed = false, navigated = false;
    const top = element => element.getBoundingClientRect().top + scrollY;
    const bounds = () => {
        // On tall/mobile footers, allow reading the whole white content first.
        const whiteEnd = top(white) + white.offsetHeight;
        const whiteStop = Math.max(top(white), whiteEnd - innerHeight);
        const blueStop = Math.max(whiteStop, top(shell) + shell.offsetHeight - innerHeight);
        return { white: whiteStop, blue: blueStop };
    };
    const navigate = () => {
        if (disposed || suspended || navigated) return;
        navigated = true;
        window.location.assign(next.href);
    };
    const gate = createFooterGate(bounds, navigate);
    const paint = () => {
        panel.toggleAttribute('data-footer-next-active', gate.phase === 'blue-held');
        const progress = gate.progress;
        panel.style.setProperty('--footer-progress', progress.toFixed(4));
        panel.dataset.footerNextProgress = String(Math.round(progress * 100));
        root.dataset.footerPhase = gate.phase;
    };
    const api = {
        wheel(delta, position, now) {
            if (suspended || disposed) return false;
            const blocked = gate.wheel(delta, position, now);
            paint();
            return blocked;
        },
        limit(step, position) {
            return suspended || disposed ? step : gate.limit(step, position);
        },
        advanced(position) {
            if (suspended || disposed) return false;
            const atStop = gate.advanced(position);
            paint();
            return atStop;
        },
        suspend() { suspended = true; panel.removeAttribute('data-footer-next-active'); },
        resume() { suspended = false; navigated = false; gate.reset(scrollY); paint(); },
        dispose() {
            disposed = true;
            panel.removeAttribute('data-footer-next-active');
            root.removeAttribute('data-footer-phase');
        },
    };
    gate.reset(scrollY);
    paint();
    return api;
}
