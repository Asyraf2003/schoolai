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
    let nearby = !('IntersectionObserver' in window);
    let next = 1;
    let preparingNext = false;

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

    async function prepareNext() {
        if (document.documentElement.dataset.homeExperienceState === 'static-fallback'
            || paused || !nearby || preparingNext || suspended || document.hidden || reduced.matches
            || document.documentElement.dataset.homeScrollGate !== 'unlocked') return;
        preparingNext = true;
        // Await the first journey rather than racing three decoders at startup.
        await prepareVisionPreview(previews[0], { staticOnly: reduced.matches });
        while (!paused && nearby && next < previews.length && !lifecycle.signal.aborted && !suspended && !document.hidden && !reduced.matches) {
            await prepareVisionPreview(previews[next++], { signal: lifecycle.signal });
            await new Promise(resolve => window.setTimeout(resolve, 0));
        }
        preparingNext = false;
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
            nearby = entries.some(entry => entry.isIntersecting);
            if (nearby) prepareNext();
        }, { rootMargin: '100% 0px', threshold: 0 });
        ahead.observe(previews[0].closest('[data-vision-story]'));
    } else {
        prepareNext();
    }
    document.addEventListener('schoolai:vision-media-range', () => {
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
