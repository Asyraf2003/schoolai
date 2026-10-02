import { safePlay } from './video-utilities.js';

export function initVisionVideoPreviews() {
    const previews = [...document.querySelectorAll('[data-vision-video-preview]')];
    const visible = new Set();
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal };
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let paused = false;
    let suspended = false;
    function visibleLayer(preview) {
        const root = preview.closest('[data-vision-story]');
        if (!root.classList.contains('is-enhanced')) return true;
        const index = preview.closest('[data-vision-visual]').dataset.visionVisual;
        return root.dataset.visionMediaRange?.split(',').includes(index);
    }
    function sync() {
        previews.forEach(preview => {
            if (!preview.dataset.visionVideoState) return;
            const canPlay = visible.has(preview) && visibleLayer(preview) && !paused && !suspended
                && !document.hidden && !reduced.matches && preview.dataset.visionVideoState === 'frame-ready'
                && document.documentElement.dataset.homeScrollGate === 'unlocked';
            if (canPlay && preview.paused) safePlay(preview);
            else if (!canPlay) preview.pause();
        });
    }
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        entries.forEach(({ target, isIntersecting }) => isIntersecting ? visible.add(target) : visible.delete(target));
        sync();
    }, { threshold: .01 }) : null;
    previews.forEach(preview => observer ? observer.observe(preview) : visible.add(preview));
    document.addEventListener('schoolai:vision-media-range', sync, options);
    document.addEventListener('schoolai:first-journey-ready', sync, options);
    document.addEventListener('visibilitychange', sync, options);
    reduced.addEventListener('change', sync, options);
    window.addEventListener('pagehide', event => {
        suspended = true;
        sync();
        if (!event.persisted) { lifecycle.abort(); observer?.disconnect(); }
    }, options);
    window.addEventListener('pageshow', () => { suspended = false; sync(); }, options);
    return { pause() { paused = true; sync(); }, resume() { paused = false; sync(); } };
}
