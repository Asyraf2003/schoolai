import { mountValuesHeading } from './values-heading.js';

export function mountValues(world) {
    if (!world) return { suspend() {}, resume() {}, dispose() {} };
    const root = world.querySelector('[data-values]');
    const heading = mountValuesHeading(root);
    const program = world.querySelector('[data-program]');
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let observer;
    let active = false;
    let suspended = false;
    let frame = 0;
    const cancel = () => { cancelAnimationFrame(frame); frame = 0; };
    const paint = () => {
        frame = 0;
        if (suspended || document.hidden || motion.matches || !world.hasAttribute('data-values-enhanced')) return;
        const height = document.documentElement.clientHeight;
        const bottom = program.getBoundingClientRect().bottom;
        const progress = Math.min(1, Math.max(0, (height * 1.2 - bottom) / (height * .52)));
        const eased = progress * progress * (3 - 2 * progress);
        world.style.setProperty('--program-values-morph-pct', `${(eased * 100).toFixed(2)}%`);
        world.style.setProperty('--program-values-type-opacity', (.16 - eased * .05).toFixed(4));
    };
    const schedule = () => {
        if (active && !frame && !suspended && !document.hidden) frame = requestAnimationFrame(paint);
    };
    const clear = () => {
        cancel(); observer?.disconnect(); active = false;
        world.removeAttribute('data-values-enhanced');
        world.style.removeProperty('--program-values-morph-pct');
        world.style.removeProperty('--program-values-type-opacity');
    };
    const configure = () => {
        clear();
        if (motion.matches || !('IntersectionObserver' in window)) return;
        world.setAttribute('data-values-enhanced', '');
        paint();
        observer = new IntersectionObserver(entries => {
            active = entries.some(entry => entry.isIntersecting);
            if (active) schedule(); else cancel();
        }, { rootMargin: '8% 0px' });
        observer.observe(world);
    };
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', paint, { signal });
    document.addEventListener('scrollend', () => paint(), { signal });
    document.addEventListener('visibilitychange', () => { if (document.hidden) cancel(); else paint(); }, { signal });
    program.addEventListener('program:restored', paint, { signal });
    motion.addEventListener('change', configure, { signal });
    configure();
    return {
        suspend() { suspended = true; cancel(); heading.suspend(); },
        resume() { suspended = false; paint(); heading.resume(); },
        dispose() { clear(); heading.dispose(); lifecycle.abort(); },
    };
}
