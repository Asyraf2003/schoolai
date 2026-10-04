// HTMLMediaElement adapter. Only this boundary calls load/play/pause.
export function createHeroMedia(slides, { onEnded, onAudioBlocked, onFailure }) {
    const removers = [];
    const failed = new Set();
    let version = 0;
    let disposed = false;
    const videos = slides.map(slide => slide.querySelector('video'));
    const listen = (target, event, callback) => {
        target.addEventListener(event, callback);
        removers.push(() => target.removeEventListener(event, callback));
    };
    videos.forEach((video, index) => {
        if (!video) return;
        const fail = () => {
            if (!video.dataset.hydrated) return;
            failed.add(index);
            video.pause();
            slides[index].dataset.playing = 'false';
            onFailure(index);
        };
        listen(video, 'playing', () => { slides[index].dataset.playing = 'true'; });
        listen(video, 'error', fail);
        video.querySelectorAll('source').forEach(source => listen(source, 'error', fail));
        listen(video, 'ended', () => onEnded(index));
    });
    function sync(state) {
        const token = ++version;
        videos.forEach((video, index) => {
            if (!video) return;
            const active = state.index === index;
            video.muted = !state.audio || !active;
            video.loop = slides.length === 1;
            if (!active || !state.canPlay || failed.has(index)) {
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
        retry(index) { failed.delete(index); },
        dispose() { disposed = true; version++; videos.forEach(video => video?.pause()); removers.forEach(remove => remove()); },
    };
}
