const preparations = new WeakMap();

function hasPlaybackBuffer(preview) {
    if (preview.readyState < 3 || !preview.videoWidth) return false;
    for (let index = 0; index < preview.buffered.length; index++) {
        if (preview.buffered.start(index) <= preview.currentTime
            && preview.buffered.end(index) - preview.currentTime >= Math.min(1, preview.duration || 1)) return true;
    }
    return false;
}

export function prepareVisionPreview(preview, { signal, staticOnly = false } = {}) {
    if (preparations.has(preview)) {
        const existing = preparations.get(preview);
        if (existing.staticOnly !== staticOnly || existing.signal?.aborted) {
            existing.cancel();
            preparations.delete(preview);
        }
        else return existing.work;
    }
    let cancel = () => {};
    const work = new Promise(resolve => {
        let settled = false;
        let started = false;
        let playbackStarted = false;
        const listeners = new AbortController();
        const options = { signal: listeners.signal };
        function finish(state) {
            if (settled) return;
            settled = true;
            listeners.abort();
            signal?.removeEventListener('abort', abort);
            preview.pause();
            preview.preload = 'metadata';
            preview.dataset.visionVideoState = state;
            resolve(state);
        }
        function abort() {
            if (settled) return;
            preview.pause();
            preview.classList.remove('is-ready');
            preview.removeAttribute('src');
            preview.load();
            finish('semantic-fallback');
        }
        cancel = abort;
        function check() {
            if (!settled && preview.readyState >= 2) preview.classList.add('is-ready');
            if (!settled && playbackStarted && hasPlaybackBuffer(preview)) {
                preview.classList.add('is-ready');
                finish('frame-ready');
            }
        }
        function fallback() {
            if (settled) return;
            preview.classList.remove('is-ready');
            preview.removeAttribute('src');
            preview.load();
            finish('poster-ready');
        }
        function play() {
            Promise.resolve(preview.play()).then(() => {
                if (settled) return;
                playbackStarted = true;
                preview.pause();
                check();
            }, error => {
                if (error?.name === 'AbortError' && document.hidden) return;
                fallback();
            });
        }
        function start() {
            if (settled || started || document.hidden) return;
            started = true;
            for (const event of ['loadeddata', 'canplay', 'canplaythrough', 'progress', 'playing', 'timeupdate']) {
                preview.addEventListener(event, check, options);
            }
            preview.addEventListener('error', fallback, options);
            preview.dataset.visionVideoHydrated = 'true';
            preview.muted = true;
            preview.defaultMuted = true;
            preview.loop = true;
            preview.playsInline = true;
            preview.preload = 'auto';
            preview.src = preview.dataset.visionVideoSrc;
            preview.load();
            play();
        }
        signal?.addEventListener('abort', abort, { once: true });
        if (signal?.aborted) { finish('semantic-fallback'); return; }
        preview.dataset.visionVideoState = 'preparing';
        const poster = preview.closest('[data-vision-visual]')?.querySelector('[data-vision-preview-poster]');
        if (poster) poster.loading = 'eager';
        Promise.resolve(poster?.decode?.()).then(() => {
            if (settled) return;
            if (staticOnly || !preview.dataset.visionVideoSrc) { finish('poster-ready'); return; }
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) preview.pause();
                else if (started) play();
                else start();
            }, options);
            start();
        }).catch(() => finish('semantic-fallback'));
    });
    preparations.set(preview, { work, staticOnly, signal, cancel });
    return work;
}
