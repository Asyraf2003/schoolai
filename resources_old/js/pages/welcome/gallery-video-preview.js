import { prepareGalleryMedia } from './gallery-media-preparation.js';

export function mountGalleryVideoPreviews(root) {
    const media = [...root.querySelectorAll('[data-gallery-story-visual]')];
    const previews = media.filter(item => item instanceof HTMLVideoElement);
    const nearby = new Set();
    const visible = new Set();
    const prepared = new WeakMap();
    const lifecycle = new AbortController();
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let aheadObserver = null;
    let visibleObserver = null;
    let preparing = false;
    let suspended = false;

    const inactive = () => suspended || lifecycle.signal.aborted || document.hidden || reduced.matches;
    function syncPlayback() {
        previews.forEach(preview => {
            if (!inactive() && visible.has(preview) && preview.dataset.galleryVideoState === 'frame-ready') {
                preview.play()?.catch(() => {});
            } else preview.pause();
        });
    }
    async function prepareNext() {
        if (preparing || suspended || document.hidden || lifecycle.signal.aborted) return;
        preparing = true;
        for (const item of media) {
            if (!nearby.has(item) || prepared.has(item)) continue;
            const work = prepareGalleryMedia(item, lifecycle.signal);
            prepared.set(item, work);
            const state = await work;
            if (lifecycle.signal.aborted) break;
            item.dataset.galleryMediaReady = state;
            if (state === 'poster-ready') prepared.delete(item);
            syncPlayback();
            if (suspended || document.hidden) break;
        }
        preparing = false;
    }
    const resume = () => { suspended = false; syncPlayback(); prepareNext(); };
    const suspend = () => { suspended = true; previews.forEach(preview => preview.pause()); };
    const destroy = () => {
        suspend();
        lifecycle.abort();
        aheadObserver?.disconnect();
        visibleObserver?.disconnect();
    };
    if ('IntersectionObserver' in window) {
        aheadObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => entry.isIntersecting ? nearby.add(entry.target) : nearby.delete(entry.target));
            prepareNext();
        }, { rootMargin: '100% 0px', threshold: 0 });
        visibleObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => entry.isIntersecting ? visible.add(entry.target) : visible.delete(entry.target));
            syncPlayback();
        }, { rootMargin: '0px', threshold: 0.01 });
        media.forEach(item => aheadObserver.observe(item));
        previews.forEach(preview => visibleObserver.observe(preview));
    }
    document.addEventListener('visibilitychange', () => document.hidden ? suspend() : resume(), { signal: lifecycle.signal });
    window.addEventListener('pagehide', event => event.persisted ? suspend() : destroy(), { signal: lifecycle.signal });
    window.addEventListener('pageshow', resume, { signal: lifecycle.signal });
    reduced.addEventListener('change', () => { syncPlayback(); prepareNext(); }, { signal: lifecycle.signal });
    return destroy;
}
