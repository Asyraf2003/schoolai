export function createMediaController(root) {
    var video = root.querySelector('[data-about-reel-video]');
    var source = video ? video.querySelector('source[data-src]') : null;
    var enabled = false;
    var near = false;
    var hydrated = false;
    var destroyed = false;

    function pause() {
        if (video && !video.paused) video.pause();
    }

    function hydrate() {
        if (!video || !source || hydrated) return;

        source.src = source.dataset.src || '';
        if (!source.src) return;

        hydrated = true;
        video.load();
    }

    function synchronize() {
        if (!video || destroyed || !enabled || !near) {
            pause();
            return;
        }

        hydrate();
        var playback = video.play();

        if (playback && typeof playback.catch === 'function') {
            playback.catch(function ignoreBlockedPlayback() {});
        }
    }

    function setEnabled(nextEnabled) {
        enabled = Boolean(nextEnabled);
        synchronize();
    }

    function setNear(nextNear) {
        near = Boolean(nextNear);
        synchronize();
    }

    function destroy() {
        destroyed = true;
        enabled = false;
        near = false;
        pause();
    }

    return { setEnabled: setEnabled, setNear: setNear, destroy: destroy };
}
