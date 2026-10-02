import { rewarmPreview, releasePreviewFrames } from './preview-readiness.js';

export async function finalizeHomepageMedia() {
    const previews = [...document.querySelectorAll('[data-hero-video],[data-vision-video-preview],[data-gallery-video-preview]')];
    const lifecycle = new AbortController();
    const exit = event => { if (!event.persisted) lifecycle.abort(); };
    window.addEventListener('pagehide', exit);
    previews.forEach(video => { video.dataset.homeMediaPreparing = 'true'; });
    try {
        await Promise.all(previews.map(async video => {
            const state = await rewarmPreview(video, lifecycle.signal);
            for (const owner of ['hero','vision','gallery']) {
                if (video.hasAttribute(`data-${owner}-video${owner === 'hero' ? '' : '-preview'}`)) {
                    video.dataset[`${owner}VideoState`] = state;
                    if (owner !== 'hero') video.dataset[`${owner}VideoHydrated`] = state === 'frame-ready' ? 'true' : 'false';
                }
            }
            if (video.matches('[data-hero-video]')) {
                video.dataset.hydrated = state === 'frame-ready' ? 'true' : 'false';
                const root = video.closest('[data-hero-slider]');
                if (state === 'poster-ready' && root.dataset.heroMode === 'opening') {
                    root.dataset.heroPreparationFallback = 'true';
                    document.dispatchEvent(new CustomEvent('schoolai:hero-media-fallback'));
                }
            }
        }));
    } finally {
        lifecycle.abort();
        window.removeEventListener('pagehide', exit);
        previews.forEach(video => { video.pause(); delete video.dataset.homeMediaPreparing; });
        releasePreviewFrames();
    }
}
