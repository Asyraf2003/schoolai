function updatePlaybackButton(button, isPaused) {
    if (!button) return;

    button.classList.toggle('is-paused', isPaused);
    button.setAttribute('aria-pressed', isPaused ? 'true' : 'false');
    button.setAttribute(
        'aria-label',
        isPaused
            ? (button.getAttribute('data-play-label') || 'Play video')
            : (button.getAttribute('data-pause-label') || 'Pause video')
    );
}

export function initOpeningHero(root, slide) {
    var video = slide.querySelector('[data-hero-video]');
    var audioButton = root.querySelector('[data-hero-audio]');
    var playbackButton = root.querySelector('[data-hero-playback]');
    var userPaused = false;
    var audioEnabled = false;

    root.setAttribute('data-enhanced', 'true');
    if (!video) return;

    video.loop = true;
    video.setAttribute('loop', '');

    function syncVideo() {
        video.muted = !audioEnabled;

        if (userPaused || document.hidden) {
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
    playbackButton?.addEventListener('click', function () {
        userPaused = !userPaused;
        updatePlaybackButton(playbackButton, userPaused);
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

    updatePlaybackButton(playbackButton, false);
    syncVideo();
}
