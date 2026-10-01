export function prepareGalleryMedia(media, signal) {
    if (media instanceof HTMLImageElement) {
        media.loading = 'eager';
        return Promise.resolve(media.decode?.()).then(() => 'image-ready', () => 'semantic-fallback');
    }
    if (!(media instanceof HTMLVideoElement)) return Promise.resolve('static-ready');
    if (media.dataset.galleryVideoState) return Promise.resolve(media.dataset.galleryVideoState);
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || document.hidden) {
        return Promise.resolve('poster-ready');
    }
    return new Promise(resolve => {
        const listeners = new AbortController();
        let settled = false;
        const deadline = window.setTimeout(() => finish('semantic-fallback'), 5000);
        function finish(state) {
            if (settled) return;
            settled = true;
            listeners.abort();
            window.clearTimeout(deadline);
            signal.removeEventListener('abort', abort);
            media.pause();
            if (state === 'semantic-fallback') { media.removeAttribute('src'); media.load(); }
            media.dataset.galleryVideoState = state;
            resolve(state);
        }
        function abort() { finish('semantic-fallback'); }
        signal.addEventListener('abort', abort, { once: true });
        if (signal.aborted) { abort(); return; }
        media.addEventListener('loadeddata', () => {
            media.classList.add('is-ready');
            finish('frame-ready');
        }, { signal: listeners.signal });
        media.addEventListener('error', abort, { signal: listeners.signal });
        const source = media.dataset.galleryVideoSrc;
        if (!source) { abort(); return; }
        media.dataset.galleryVideoHydrated = 'true';
        media.muted = true;
        media.defaultMuted = true;
        media.loop = true;
        media.playsInline = true;
        media.preload = 'metadata';
        media.src = source;
        media.load();
        media.play()?.catch(abort);
    });
}
