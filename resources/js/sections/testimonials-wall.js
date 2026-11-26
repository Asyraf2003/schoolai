export function mountTestimonialsWall(root, signal) {
    const scene = root.querySelector('[data-testimonials-scene]');
    if (!scene || !window.IntersectionObserver || !CSS.supports('position', 'sticky')) {
        return { suspend() {}, resume() {} };
    }

    const desktop = matchMedia('(min-width: 1024px) and (min-height: 650px) and (hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)');
    const inner = scene.firstElementChild;
    const rows = [...scene.querySelectorAll('[data-testimonials-row]')];
    const direction = getComputedStyle(scene).direction === 'rtl' ? -1 : 1;
    let near = false;
    let active = false;
    let suspended = document.hidden;
    let frame = 0;

    function paint() {
        frame = 0;
        if (!active || !near || suspended) return;

        const stickyTop = parseFloat(getComputedStyle(inner).insetBlockStart);
        const travel = scene.offsetHeight - inner.offsetHeight;
        const progress = Math.max(0, Math.min(1, (stickyTop - scene.getBoundingClientRect().top) / Math.max(1, travel)));
        const ranges = rows.map(row => row.scrollWidth - row.clientWidth);

        rows.forEach((row, index) => {
            if (row.matches(':focus-within')) return;
            const offset = ranges[index] * (index % 2 === 0 ? progress : 1 - progress);
            row.scrollLeft = direction * offset;
        });
    }

    function schedule() {
        if (active && near && !suspended && !frame) frame = requestAnimationFrame(paint);
    }

    function configure() {
        if (signal.aborted) return;
        active = desktop.matches && rows.length === 2 && rows.every(row => row.childElementCount > 2);
        scene.toggleAttribute('data-testimonials-scroll', active);
        if (active) {
            const stickyTop = parseFloat(getComputedStyle(inner).insetBlockStart);
            if (stickyTop + inner.offsetHeight + 12 > innerHeight) {
                active = false;
                scene.removeAttribute('data-testimonials-scroll');
            }
        }
        rows.forEach(row => {
            if (active) row.tabIndex = 0;
            else row.removeAttribute('tabindex');
        });
        schedule();
    }

    function suspend() {
        suspended = true;
        cancelAnimationFrame(frame);
        frame = 0;
    }

    function resume() {
        suspended = document.hidden;
        configure();
    }

    const observer = new IntersectionObserver(([entry]) => {
        near = entry.isIntersecting;
        schedule();
    }, { rootMargin: '120px 0px' });
    observer.observe(scene);
    signal.addEventListener('abort', () => { observer.disconnect(); suspend(); }, { once: true });
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', configure, { signal });
    desktop.addEventListener('change', configure, { signal });
    document.addEventListener('visibilitychange', () => { if (document.hidden) suspend(); else resume(); }, { signal });
    document.fonts?.ready.then(configure);
    configure();

    return { suspend, resume };
}
