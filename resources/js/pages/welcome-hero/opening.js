export function initOpeningHero(root, slide) {
    var video = slide.querySelector('[data-hero-video]');
    var audioButton = root.querySelector('[data-hero-audio]');
    var audioEnabled = false;

    root.setAttribute('data-enhanced', 'true');
    if (!video) return;

    video.loop = true;
    video.setAttribute('loop', '');

    function syncVideo() {
        video.muted = !audioEnabled;

        if (document.hidden) {
            video.pause();
            return;
        }

        var playAttempt = video.play();
        if (playAttempt && typeof playAttempt.catch === 'function') {
            playAttempt.catch(function () {
                slide.classList.add('has-video-playback-fallback');
            });
        }
    }

    audioButton?.addEventListener('click', function () {
        audioEnabled = !audioEnabled;
        audioButton.setAttribute('aria-pressed', audioEnabled ? 'true' : 'false');
        syncVideo();
    });
    video.addEventListener('loadeddata', function () {
        slide.classList.remove('has-video-playback-fallback');
    });
    document.addEventListener('visibilitychange', syncVideo);
    window.addEventListener('pagehide', function () {
        document.removeEventListener('visibilitychange', syncVideo);
        video.pause();
    }, { once: true });

    syncVideo();
}
