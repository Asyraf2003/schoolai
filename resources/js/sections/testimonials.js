import { testimonialDepartureStart, testimonialIntroFrame, testimonialWordFrame, testimonialTrackOffset } from './testimonials-motion.js';

export function mountTestimonials(root) {
    if (!root) return { synchronize() {}, suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const gallery = root.previousElementSibling;
    const intro = root.querySelector('[data-testimonials-intro]');
    const rows = root.querySelector('[data-testimonials-rows]');
    const tracks = [...root.querySelectorAll('[data-testimonials-track]')];
    const words = [...root.querySelectorAll('[data-testimonials-word]')];
    const capable = ['IntersectionObserver', 'MutationObserver', 'DOMMatrix'].every(name => typeof window[name] === 'function') && CSS.supports('position', 'sticky');
    let near = false;
    let suspended = false;
    let disposed = false;
    let frame = 0;
    const clear = () => {
        cancelAnimationFrame(frame); frame = 0;
        root.removeAttribute('data-testimonials-enabled');
        root.removeAttribute('data-testimonials-active');
        delete root.dataset.testimonialsProgress;
        ['--testimonials-tone', '--testimonials-title-y', '--testimonials-rows-opacity', '--testimonials-field', '--testimonials-handoff', '--testimonials-row-overlap'].forEach(key => root.style.removeProperty(key));
        tracks.forEach(track => track.style.removeProperty('--testimonial-track-x'));
        words.forEach(word => { word.style.removeProperty('--testimonial-word-opacity'); word.style.removeProperty('--testimonial-word-blur'); });
    };
    const render = () => {
        frame = 0;
        if (disposed || suspended || document.hidden || reduced.matches || !capable) return;
        const introBox = intro.getBoundingClientRect();
        const rowBox = rows.getBoundingClientRect();
        const metrics = tracks.map(track => ({ track, travel: Math.max(0, track.scrollWidth - track.parentElement.clientWidth) * .9 }));
        const span = Math.max(1, introBox.height - innerHeight);
        const overlap = gallery?.hasAttribute('data-gallery-enabled') ? innerHeight + gallery.offsetHeight * .1 : 0;
        const shift = -overlap - (parseFloat(getComputedStyle(root).marginBlockStart) || 0);
        const shrink = Number(gallery?.dataset.galleryShrinkStart);
        const blurDuration = shrink > 0 ? .4 * (.52 - shrink) : .20;
        const remaining = 1 - testimonialDepartureStart(blurDuration);
        const rowShift = -span * remaining - (parseFloat(getComputedStyle(rows).marginBlockStart) || 0);
        const progress = -(introBox.top + shift) / span;
        const pose = testimonialIntroFrame(progress, blurDuration);
        const wall = (innerHeight - rowBox.top - shift - rowShift) / Math.max(1, rowBox.height + innerHeight);
        const rtl = document.documentElement.dir === 'rtl';
        root.dataset.testimonialsProgress = progress.toFixed(5);
        root.style.setProperty('--testimonials-handoff', `${-overlap}px`);
        root.style.setProperty('--testimonials-row-overlap', `${-span * remaining}px`);
        const light = (pose.tone - 8) / 247;
        const field = `rgb(${pose.tone} ${Math.round(9 + 246 * light)} ${Math.round(11 + 244 * light)})`;
        root.style.setProperty('--testimonials-field', progress < 0 ? 'transparent' : field);
        root.style.setProperty('--testimonials-tone', String(pose.tone));
        root.style.setProperty('--testimonials-title-y', `${(pose.y * span).toFixed(2)}px`);
        root.style.setProperty('--testimonials-rows-opacity', pose.rows.toFixed(4));
        words.forEach((word, index) => {
            const state = testimonialWordFrame(progress, index, words.length, blurDuration);
            word.style.setProperty('--testimonial-word-opacity', state.opacity.toFixed(4));
            word.style.setProperty('--testimonial-word-blur', `${state.blur.toFixed(2)}px`);
        });
        metrics.forEach(({ track, travel }, index) => {
            track.style.setProperty('--testimonial-track-x', `${testimonialTrackOffset(wall, travel, index, rtl).toFixed(2)}px`);
        });
    };
    const schedule = (force = false) => {
        if ((force === true || near) && !frame && !suspended && !disposed && !document.hidden && !reduced.matches) frame = requestAnimationFrame(render);
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
    const resize = typeof ResizeObserver === 'function' ? new ResizeObserver(() => schedule(true)) : null;
    resize?.observe(intro); resize?.observe(rows); if (gallery) resize?.observe(gallery);
    const changes = typeof MutationObserver === 'function' ? new MutationObserver(() => schedule(true)) : null;
    if (gallery) changes?.observe(gallery, { attributes: true, attributeFilter: ['data-gallery-enabled'] });
    const manual = event => {
        const row = event.target.closest('[data-testimonials-row]');
        if (!row || row.hasAttribute('data-testimonials-manual')) return;
        const offset = new DOMMatrix(getComputedStyle(row.firstElementChild).transform).m41;
        row.setAttribute('data-testimonials-manual', '');
        row.scrollLeft = -offset;
    };
    root.addEventListener('focusin', manual, { signal });
    root.addEventListener('pointerdown', manual, { signal });
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
        synchronize() { if (!near) return; cancelAnimationFrame(frame); render(); },
        suspend() { suspended = true; cancelAnimationFrame(frame); frame = 0; root.removeAttribute('data-testimonials-active'); },
        resume() { suspended = false; render(); schedule(); },
        dispose() { if (disposed) return; disposed = true; clear(); observer?.disconnect(); resize?.disconnect(); changes?.disconnect(); lifecycle.abort(); },
    };
}
