import { preparePreview } from './preview-readiness.js';

export async function prepareHeroCarouselMedia(signal) {
    const root = document.querySelector('[data-hero-slider]');
    if (!root) return;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    for (const video of root.querySelectorAll('[data-hero-video]')) {
        const source = video.currentSrc || video.querySelector('source')?.getAttribute('data-src')
            || video.querySelector('source')?.src;
        const poster = video.poster ? new Image() : null;
        if (poster) poster.src = video.poster;
        video.dataset.homeMediaPreparing = 'true';
        let state;
        try {
            state = await preparePreview(video, {
                source, poster, signal, staticOnly: reduced || root.dataset.heroPreparationFallback === 'true', loop: video.loop,
                muted: document.querySelector('[data-hero-audio][aria-pressed="true"]') ? video.muted : true,
            });
        } finally { delete video.dataset.homeMediaPreparing; }
        video.dataset.hydrated = state === 'frame-ready' ? 'true' : 'false';
        video.dataset.heroVideoState = state;
        video.closest('[data-hero-slide]').classList.toggle('has-video-playback-fallback', state === 'poster-ready');
        if (state === 'poster-ready' && root.dataset.heroMode === 'opening') {
            root.dataset.heroPreparationFallback = 'true';
            document.dispatchEvent(new CustomEvent('schoolai:hero-media-fallback'));
        }
    }
}
