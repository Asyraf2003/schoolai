import { testimonialIntroFrame, testimonialTrackOffset } from './testimonials-motion.js';

export function mountTestimonials(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const intro = root.querySelector('[data-testimonials-intro]');
    const rows = root.querySelector('[data-testimonials-rows]');
    const tracks = [...root.querySelectorAll('[data-testimonials-track]')];
    const capable = 'IntersectionObserver' in window && CSS.supports('position', 'sticky');
    let near = false;
    let suspended = false;
    let disposed = false;
    let frame = 0;
    const clear = () => {
        cancelAnimationFrame(frame); frame = 0;
        root.removeAttribute('data-testimonials-enabled');
        root.removeAttribute('data-testimonials-active');
        ['--testimonials-tone', '--testimonials-title-opacity', '--testimonials-title-y'].forEach(key => root.style.removeProperty(key));
        tracks.forEach(track => track.style.removeProperty('--testimonial-track-x'));
    };
    const render = () => {
        frame = 0;
        if (disposed || suspended || document.hidden || reduced.matches || !capable) return;
        const introBox = intro.getBoundingClientRect();
        const rowBox = rows.getBoundingClientRect();
        const metrics = tracks.map(track => ({ track, travel: Math.max(0, track.scrollWidth - track.parentElement.clientWidth) * .9 }));
        const progress = -introBox.top / Math.max(1, introBox.height - innerHeight);
        const pose = testimonialIntroFrame(progress);
        const wall = (innerHeight - rowBox.top) / Math.max(1, rowBox.height + innerHeight);
        const rtl = document.documentElement.dir === 'rtl';
        root.style.setProperty('--testimonials-tone', String(pose.tone));
        root.style.setProperty('--testimonials-title-opacity', pose.opacity.toFixed(4));
        root.style.setProperty('--testimonials-title-y', `${pose.y.toFixed(2)}px`);
        metrics.forEach(({ track, travel }, index) => {
            track.style.setProperty('--testimonial-track-x', `${testimonialTrackOffset(wall, travel, index, rtl).toFixed(2)}px`);
        });
    };
    const schedule = () => {
        if (near && !frame && !suspended && !disposed && !document.hidden && !reduced.matches) frame = requestAnimationFrame(render);
    };
    const configure = () => {
        clear();
        if (!capable || reduced.matches || disposed) return;
        root.setAttribute('data-testimonials-enabled', '');
        render(); schedule();
    };
    const observer = capable ? new IntersectionObserver(entries => {
        near = entries.some(entry => entry.isIntersecting);
        root.toggleAttribute('data-testimonials-active', near && !suspended && !reduced.matches && !document.hidden);
        if (near) schedule(); else { cancelAnimationFrame(frame); frame = 0; }
    }, { rootMargin: '50% 0px' }) : null;
    observer?.observe(root);
    const resize = typeof ResizeObserver === 'function' ? new ResizeObserver(schedule) : null;
    resize?.observe(intro); resize?.observe(rows);
    root.addEventListener('focusin', event => {
        event.target.closest('[data-testimonials-row]')?.setAttribute('data-testimonials-manual', '');
    }, { signal });
    root.addEventListener('pointerdown', event => {
        event.target.closest('[data-testimonials-row]')?.setAttribute('data-testimonials-manual', '');
    }, { signal });
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', schedule, { signal });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) { cancelAnimationFrame(frame); frame = 0; root.removeAttribute('data-testimonials-active'); }
        else schedule();
    }, { signal });
    reduced.addEventListener('change', configure, { signal });
    document.fonts?.ready.then(schedule);
    configure();
    return {
        suspend() { suspended = true; cancelAnimationFrame(frame); frame = 0; root.removeAttribute('data-testimonials-active'); },
        resume() { suspended = false; render(); schedule(); },
        dispose() { if (disposed) return; disposed = true; clear(); observer?.disconnect(); resize?.disconnect(); lifecycle.abort(); },
    };
}
