// HTMLMediaElement adapter. Only this boundary calls load/play/pause.
export function createHeroMedia(slides, { onEnded, onAudioBlocked, onFailure }) {
    const removers = [];
    const failed = new Set();
    const deadlines = new Map();
    let version = 0;
    let disposed = false;
    let appliedPolicy = null;
    const videos = slides.map(slide => slide.querySelector('video'));
    const listen = (target, event, callback) => {
        target.addEventListener(event, callback);
        removers.push(() => target.removeEventListener(event, callback));
    };
    videos.forEach((video, index) => {
        if (!video) return;
        const fail = () => {
            if (!video.dataset.hydrated) return;
            clearTimeout(deadlines.get(index));
            deadlines.delete(index);
            failed.add(index);
            video.pause();
            slides[index].dataset.playing = 'false';
            onFailure(index);
        };
        listen(video, 'playing', () => {
            clearTimeout(deadlines.get(index)); deadlines.delete(index);
            slides[index].dataset.playing = 'true';
        });
        const waiting = () => {
            if (video.paused || video.readyState >= 3) return;
            slides[index].dataset.playing = 'false';
            if (!deadlines.has(index)) deadlines.set(index, setTimeout(() => {
                deadlines.delete(index);
                if (!video.paused && video.readyState < 3) fail();
            }, 8000));
        };
        listen(video, 'waiting', waiting);
        listen(video, 'stalled', waiting);
        listen(video, 'error', fail);
        video.querySelectorAll('source').forEach(source => listen(source, 'error', fail));
        listen(video, 'ended', () => onEnded(index));
    });
    function sync(state) {
        const policy = `${state.index}:${state.audio}:${state.canPlay}`;
        const needsResume = state.canPlay && videos[state.index]?.paused && !failed.has(state.index);
        if (disposed || (policy === appliedPolicy && !needsResume)) return;
        appliedPolicy = policy;
        const token = ++version;
        videos.forEach((video, index) => {
            if (!video) return;
            const active = state.index === index;
            const muted = !state.audio || !active;
            const loop = slides.length === 1;
            if (video.muted !== muted) video.muted = muted;
            if (video.loop !== loop) video.loop = loop;
            if (!active || !state.canPlay || failed.has(index)) {
                clearTimeout(deadlines.get(index)); deadlines.delete(index);
                video.pause();
                if (!active) {
                    slides[index].dataset.playing = 'false';
                    try { video.currentTime = 0; } catch { /* Media metadata may not exist. */ }
                }
                return;
            }
            if (!video.dataset.hydrated) {
                video.querySelectorAll('source[data-src]').forEach(source => { source.src = source.dataset.src; });
                video.dataset.hydrated = 'true';
                video.preload = 'metadata';
                video.load();
                deadlines.set(index, setTimeout(() => {
                    deadlines.delete(index);
                    if (video.readyState < 3) {
                        failed.add(index); video.pause();
                        slides[index].dataset.playing = 'false'; onFailure(index);
                    }
                }, 8000));
            }
            const attempt = video.play();
            attempt?.catch(() => {
                if (disposed || token !== version) return;
                slides[index].dataset.playing = 'false';
                failed.add(index);
                if (state.audio) onAudioBlocked();
                onFailure(index);
            });
        });
    }
    return {
        sync,
        isVideo: index => Boolean(videos[index]),
        failed: index => failed.has(index),
        retry(index) { failed.delete(index); appliedPolicy = null; },
        dispose() { disposed = true; version++; deadlines.forEach(clearTimeout); deadlines.clear(); videos.forEach(video => video?.pause()); removers.forEach(remove => remove()); },
    };
}
