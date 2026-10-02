const preparation = new WeakMap();
const preparationOptions = new WeakMap();
let frameCanvas = null;

function snapshotDecodedFrame(video) {
    frameCanvas ||= typeof OffscreenCanvas === 'function'
        ? new OffscreenCanvas(1, 1) : document.createElement('canvas');
    frameCanvas.width = video.videoWidth;
    frameCanvas.height = video.videoHeight;
    const context = frameCanvas.getContext('2d');
    if (!context) return false;
    context.drawImage(video, 0, 0);
    if (!frameCanvas.transferToImageBitmap) return frameCanvas.width > 0 && frameCanvas.height > 0;
    const bitmap = frameCanvas.transferToImageBitmap();
    try { return bitmap.width > 0 && bitmap.height > 0; }
    finally { bitmap.close(); }
}

export function releasePreviewFrames() {
    if (frameCanvas) { frameCanvas.width = 1; frameCanvas.height = 1; frameCanvas = null; }
}

export function watchPreviewReadiness(check) {
    let frame = null, stopped = false;
    const tick = () => {
        if (stopped) return;
        check();
        if (!stopped) frame = window.requestAnimationFrame(tick);
    };
    if (typeof window !== 'undefined' && window.requestAnimationFrame) frame = window.requestAnimationFrame(tick);
    return () => { stopped = true; if (frame !== null) window.cancelAnimationFrame?.(frame); };
}

function withSignal(work, signal) {
    if (!signal) return work;
    return new Promise((resolve, reject) => {
        const abort = () => reject(new DOMException('Preparation cancelled', 'AbortError'));
        signal.addEventListener('abort', abort, { once: true });
        if (signal.aborted) abort();
        Promise.resolve(work).then(resolve, reject).finally(() => signal.removeEventListener('abort', abort));
    });
}

export function previewHasFutureFrame(video) {
    if (video.readyState < 3 || !video.videoWidth) return false;
    for (let index = 0; index < video.buffered.length; index++) {
        if (video.buffered.start(index) <= video.currentTime
            && video.buffered.end(index) - video.currentTime >= Math.min(1, video.duration || 1)) return true;
    }
    return false;
}

export async function rewarmPreview(video, signal) {
    if (await preparation.get(video) !== 'frame-ready') return 'poster-ready';
    preparation.delete(video);
    return preparePreview(video, { ...preparationOptions.get(video), source: video.currentSrc, signal,
        loop: video.loop, muted: video.muted, pauseOnReady: false });
}

export function preparePreview(video, { source, poster, signal, staticOnly = false, loop = true, muted = true, pauseOnReady = true } = {}) {
    if (preparation.has(video)) return preparation.get(video);
    preparationOptions.set(video, { source, poster, staticOnly, loop, muted });
    const work = (async () => {
        if (poster) {
            video.poster = poster.src;
            await withSignal(poster.decode(), signal);
            if (!poster.naturalWidth) throw new Error('Required video poster unavailable');
        }
        if (staticOnly && poster) { video.classList.add('is-ready'); return 'poster-ready'; }
        if (signal?.aborted) throw new DOMException('Preparation cancelled', 'AbortError');
        if (!source) {
            if (poster) return 'poster-ready';
            throw new Error('Required video source unavailable');
        }
        return new Promise((resolve, reject) => {
            const listeners = new AbortController();
            const options = { signal: listeners.signal };
            let settled = false;
            let presented = typeof video.requestVideoFrameCallback !== 'function';
            let frameRequest = null;
            let copyPaint = null;
            let copied = false;
            let playbackStarted = false;
            let stopWatching = () => {};
            function finish(state, error) {
                if (settled) return;
                settled = true;
                stopWatching();
                listeners.abort();
                if (frameRequest !== null) video.cancelVideoFrameCallback?.(frameRequest);
                if (copyPaint !== null) window.cancelAnimationFrame(copyPaint);
                signal?.removeEventListener('abort', abort);
                if (error || state !== 'frame-ready' || pauseOnReady) video.pause();
                video.preload = 'metadata';
                if (error) reject(error);
                else {
                    video.classList.add('is-ready');
                    resolve(state);
                }
            }
            const abort = () => finish(null, new DOMException('Preparation cancelled', 'AbortError'));
            const check = () => {
                if (settled) return;
                if (video.readyState >= 2) video.classList.add('is-ready');
                const future = previewHasFutureFrame(video);
                // Engines may suppress offscreen compositor callbacks. Copy an
                // actual decoded frame before the section can enter the viewport.
                if (!presented && !copied && future && video.getBoundingClientRect) {
                    const rect = video.getBoundingClientRect();
                    const offscreen = rect.top >= window.innerHeight || rect.bottom <= 0
                        || getComputedStyle(video).visibility === 'hidden';
                    if (offscreen) {
                        try { copied = snapshotDecodedFrame(video); }
                        catch (error) { finish(null, error); return; }
                        if (copied) copyPaint = window.requestAnimationFrame(() => {
                            copyPaint = window.requestAnimationFrame(() => { copyPaint = null; presented = true; check(); });
                        });
                    }
                }
                if (presented && future && playbackStarted) finish('frame-ready');
            };
            const fallback = () => {
                if (settled) return;
                if (!poster) { finish(null, new Error('Required video has no usable fallback')); return; }
                video.querySelectorAll?.('source[src]').forEach(sourceElement => {
                    sourceElement.dataset.src = sourceElement.src;
                    sourceElement.removeAttribute('src');
                });
                video.removeAttribute('src');
                video.load();
                finish('poster-ready');
            };
            signal?.addEventListener('abort', abort, { once: true });
            for (const name of ['loadeddata', 'canplay', 'canplaythrough', 'progress', 'suspend', 'playing']) video.addEventListener(name, check, options);
            video.addEventListener('error', fallback, options);
            video.muted = muted;
            video.defaultMuted = true;
            video.loop = loop;
            video.playsInline = true;
            video.preload = 'auto';
            if (!presented) frameRequest = video.requestVideoFrameCallback(() => { presented = true; check(); });
            stopWatching = watchPreviewReadiness(check);
            if (video.currentSrc !== source || video.readyState < 1) {
                video.src = source;
                video.load();
            }
            // Decode only enough to display and start playback; completion is never awaited.
            const started = () => {
                if (settled) return;
                playbackStarted = true;
                if (video.dataset) video.dataset.homePlaybackWarmed = 'true';
                check();
            };
            const attempt = video.play();
            if (attempt?.then) attempt.then(started, fallback);
            else started();
            check();
        });
    })();
    preparation.set(video, work);
    return work;
}
