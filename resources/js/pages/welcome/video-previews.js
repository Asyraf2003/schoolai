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
    let ahead = null;
    const queued = new Set();

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
        const index = previews.indexOf(preview);
        const rect = preview.closest('[data-vision-story]').getBoundingClientRect();
        if (rect.top + index * Math.max(0, rect.height - innerHeight) / previews.length >= innerHeight * .8) return;
        prepareVisionPreview(preview).then(() => {
            if (activePreviews.has(preview) && !paused && !suspended && !document.hidden && !reduced.matches
                && visibleLayer(preview) && preview.dataset.visionVideoState === 'frame-ready') safePlay(preview);
        });
    }

    function prepareNext() {
        if (paused || suspended || document.hidden || reduced.matches
            || document.documentElement.dataset.homeScrollGate !== 'unlocked') return;
        previews.forEach((preview, index) => {
            const root = preview.closest('[data-vision-story]');
            const rect = root.getBoundingClientRect();
            const top = root.classList.contains('is-enhanced')
                ? rect.top + index * Math.max(0, rect.height - innerHeight) / previews.length
                : preview.closest('[data-vision-visual]').getBoundingClientRect().top;
            if (top >= innerHeight * .8 || top < -innerHeight || queued.has(preview)) return;
            queued.add(preview);
            prepareVisionPreview(preview, { signal: lifecycle.signal }).then(() => {
                if (activePreviews.has(preview)) play(preview);
            });
        });
    }

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(entries => {
            entries.forEach(({ target, isIntersecting }) => {
                if (isIntersecting) { activePreviews.add(target); play(target); }
                else { activePreviews.delete(target); target.pause(); }
            });
        }, { rootMargin: '0px 0px', threshold: 0.01 });
        previews.forEach(preview => observer.observe(preview));
        ahead = new IntersectionObserver(entries => {
            if (entries.some(entry => entry.isIntersecting)) prepareNext();
        }, { rootMargin: '100% 0px', threshold: 0 });
        ahead.observe(previews[0].closest('[data-vision-story]'));
    } else {
        prepareNext();
    }
    window.addEventListener('scroll', prepareNext, { passive: true, ...options });
    document.addEventListener('schoolai:vision-media-range', () => {
        prepareNext();
        activePreviews.forEach(preview => { if (visibleLayer(preview)) play(preview); else preview.pause(); });
    }, options);
    document.addEventListener('schoolai:first-journey-ready', () => { activePreviews.forEach(play); prepareNext(); }, options);
    function resume() {
        paused = false;
        activePreviews.forEach(play);
        prepareNext();
    }
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) previews.forEach(preview => preview.pause());
        else { activePreviews.forEach(play); prepareNext(); }
    }, options);
    reduced.addEventListener('change', () => {
        previews.forEach(preview => preview.pause());
        if (!reduced.matches) resume();
    }, options);
    window.addEventListener('pagehide', event => {
        suspended = true;
        previews.forEach(preview => preview.pause());
        if (!event.persisted) { lifecycle.abort(); observer?.disconnect(); ahead?.disconnect(); }
    }, options);
    window.addEventListener('pageshow', () => { suspended = false; resume(); }, options);
    return {
        pause() { paused = true; previews.forEach(preview => preview.pause()); },
        resume,
    };
}
