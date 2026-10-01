const preparations = new WeakMap();

export function prepareVisionPreview(preview, { signal, staticOnly = false } = {}) {
    if (preparations.has(preview)) {
        const existing = preparations.get(preview);
        if (existing.staticOnly && !staticOnly) preparations.delete(preview);
        else return existing.work;
    }
    const work = new Promise(resolve => {
        let settled = false;
        const listeners = new AbortController();
        const options = { signal: listeners.signal };
        function finish(state) {
            if (settled) return;
            settled = true;
            listeners.abort();
            signal?.removeEventListener('abort', abort);
            preview.pause();
            preview.dataset.visionVideoState = state;
            resolve(state);
        }
        function abort() {
            preview.pause();
            preview.removeAttribute('src');
            preview.load();
            finish('semantic-fallback');
        }
        signal?.addEventListener('abort', abort, { once: true });
        if (signal?.aborted) { finish('semantic-fallback'); return; }
        const poster = preview.closest('[data-vision-visual]')?.querySelector('[data-vision-preview-poster]');
        if (poster) poster.loading = 'eager';
        Promise.resolve(poster?.decode?.()).then(() => {
            if (settled) return;
            if (staticOnly || document.hidden || !preview.dataset.visionVideoSrc) {
                finish('poster-ready');
                return;
            }
            preview.addEventListener('loadeddata', () => {
                preview.classList.add('is-ready');
                finish('frame-ready');
            }, options);
            preview.addEventListener('error', () => finish('poster-ready'), options);
            preview.dataset.visionVideoHydrated = 'true';
            preview.dataset.visionVideoState = 'preparing';
            preview.muted = true;
            preview.defaultMuted = true;
            preview.loop = true;
            preview.playsInline = true;
            preview.preload = 'metadata';
            preview.src = preview.dataset.visionVideoSrc;
            preview.load();
            const attempt = preview.play();
            attempt?.catch(() => finish('poster-ready'));
        }).catch(() => finish('semantic-fallback'));
    });
    preparations.set(preview, { work, staticOnly });
    return work;
}
