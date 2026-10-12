export function wheelPixels(event, viewportHeight) {
    const factor = event.deltaMode === 1 ? 16 : event.deltaMode === 2 ? viewportHeight : 1;
    return event.deltaY * factor;
}

function canScrollInside(target, direction) {
    for (let node = target?.nodeType === 1 ? target : target?.parentElement;
        node && node !== document.body && node !== document.documentElement;
        node = node.parentElement) {
        const style = getComputedStyle(node);
        if (!/auto|scroll/.test(style.overflowY) || node.scrollHeight <= node.clientHeight + 1) continue;
        if (direction > 0 && node.scrollTop + node.clientHeight < node.scrollHeight - 1) return true;
        if (direction < 0 && node.scrollTop > 1) return true;
    }
    return false;
}

export function mountScrollMomentum() {
    const noop = { suspend() {}, resume() {}, dispose() {} };
    if (!window.requestAnimationFrame || !window.matchMedia) return noop;
    const lifecycle = new AbortController();
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let frame = 0;
    let lastTime = 0;
    let target = window.scrollY;
    let lastWritten = window.scrollY;
    let suspended = document.hidden;
    let painting = false;

    const maxScroll = () => Math.max(0,
        (document.scrollingElement ?? document.documentElement).scrollHeight - window.innerHeight);
    const clamp = value => Math.max(0, Math.min(value, maxScroll()));
    function stop() {
        if (frame) window.cancelAnimationFrame(frame);
        frame = 0;
        lastTime = 0;
        target = window.scrollY;
        lastWritten = window.scrollY;
    }
    function tick(time) {
        frame = 0;
        if (suspended || reduced.matches || document.hidden) { stop(); return; }
        if (Math.abs(window.scrollY - lastWritten) > 2) { stop(); return; }
        target = clamp(target);
        const difference = target - window.scrollY;
        const dt = Math.min(48, Math.max(1, time - (lastTime || time - 16)));
        lastTime = time;
        const next = Math.abs(difference) <= 0.6
            ? target : window.scrollY + difference * (1 - Math.exp(-dt / 85));
        painting = true;
        window.scrollTo(0, next);
        lastWritten = window.scrollY;
        painting = false;
        if (Math.abs(target - window.scrollY) > 0.6) frame = window.requestAnimationFrame(tick);
        else lastTime = 0;
    }
    function wheel(event) {
        if (suspended || reduced.matches || !event.cancelable || event.defaultPrevented ||
            event.ctrlKey || event.metaKey || event.altKey || event.shiftKey ||
            Math.abs(event.deltaX) >= Math.abs(event.deltaY) ||
            document.documentElement.hasAttribute('data-landing-menu-open') ||
            document.querySelector('dialog[open], [aria-modal="true"]:not([hidden])') ||
            event.target?.closest?.('input, textarea, select, [contenteditable="true"], [data-native-wheel]')) return;
        const delta = wheelPixels(event, window.innerHeight);
        if (!Number.isFinite(delta) || Math.abs(delta) < 0.1 || canScrollInside(event.target, delta)) return;
        const current = window.scrollY;
        if (!frame) { target = current; lastWritten = current; }
        else if (Math.sign(delta) !== Math.sign(target - current)) target = current;
        const nextTarget = clamp(target + delta);
        if (Math.abs(nextTarget - current) < 0.6) { stop(); return; }
        event.preventDefault();
        target = nextTarget;
        if (!frame) frame = window.requestAnimationFrame(tick);
    }
    window.addEventListener('wheel', wheel, { passive: false, signal: lifecycle.signal });
    window.addEventListener('scroll', () => {
        if (frame && !painting && Math.abs(window.scrollY - lastWritten) > 2) stop();
    }, { passive: true, signal: lifecycle.signal });
    reduced.addEventListener('change', stop, { signal: lifecycle.signal });
    return {
        suspend() { suspended = true; stop(); },
        resume() { suspended = document.hidden; stop(); },
        dispose() { stop(); lifecycle.abort(); },
    };
}
