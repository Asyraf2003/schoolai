import { safePlay } from './video-utilities.js';
import { prepareVisionPreview } from './video-readiness.js';

export function initVisionVideoPreviews() {
    const previews = Array.from(document.querySelectorAll('[data-vision-video-preview]'));
    const activePreviews = new Set();
    if (!previews.length) return { pause: () => {}, resume: () => {} };
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal };
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let paused = false;
    let suspended = false;
    let observer = null;

    function visibleLayer(preview) {
        const root = preview.closest('[data-vision-story]');
        if (!root.classList.contains('is-enhanced')) return true;
        const index = preview.closest('[data-vision-visual]').dataset.visionVisual;
        return root.dataset.visionMediaRange?.split(',').includes(index);
    }
    function play(preview) {
        if (document.documentElement.dataset.homeExperienceState === 'static-fallback'
            || !visibleLayer(preview) || paused || suspended || reduced.matches || document.hidden
            || document.documentElement.dataset.homeScrollGate !== 'unlocked') return;
        prepareVisionPreview(preview).then(() => {
            if (activePreviews.has(preview) && !paused && !suspended && !document.hidden && !reduced.matches
                && visibleLayer(preview) && preview.dataset.visionVideoState === 'frame-ready') safePlay(preview);
        });
    }

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(entries => {
            entries.forEach(({ target, isIntersecting }) => {
                if (isIntersecting) { activePreviews.add(target); play(target); }
                else { activePreviews.delete(target); if (target.dataset.visionVideoState !== 'preparing') target.pause(); }
            });
        }, { rootMargin: '0px 0px', threshold: 0.01 });
        previews.forEach(preview => observer.observe(preview));
    } else { previews.forEach(preview => activePreviews.add(preview)); }
    document.addEventListener('schoolai:vision-media-range', () => {
        activePreviews.forEach(preview => { if (visibleLayer(preview)) play(preview); else if (preview.dataset.visionVideoState !== 'preparing') preview.pause(); });
    }, options);
    document.addEventListener('schoolai:first-journey-ready', () => { activePreviews.forEach(play); }, options);
    function resume() {
        paused = false;
        activePreviews.forEach(play);
    }
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) previews.forEach(preview => preview.pause());
        else { activePreviews.forEach(play); }
    }, options);
    reduced.addEventListener('change', () => {
        previews.forEach(preview => preview.pause());
        if (!reduced.matches) resume();
    }, options);
    window.addEventListener('pagehide', event => {
        suspended = true;
        previews.forEach(preview => preview.pause());
        if (!event.persisted) { lifecycle.abort(); observer?.disconnect(); }
    }, options);
    window.addEventListener('pageshow', () => { suspended = false; resume(); }, options);
    return {
        pause() { paused = true; previews.forEach(preview => preview.pause()); },
        resume,
    };
}
