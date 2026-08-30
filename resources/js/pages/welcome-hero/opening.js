export function initOpeningHero(root, slide) {
    var video = slide.querySelector('[data-hero-video]');
    var audioButtons = Array.from(document.querySelectorAll('[data-hero-audio]'));
    var audioEnabled = false;

    root.setAttribute('data-enhanced', 'true');
    if (!video) return;

    video.loop = true;
    video.setAttribute('loop', '');

    function hydrateVideo() {
        if (video.getAttribute('data-hydrated') === 'true') return true;

        var hydratedSource = false;
        video.querySelectorAll('source[data-src]').forEach(function (source) {
            source.src = source.getAttribute('data-src');
            source.removeAttribute('data-src');
            hydratedSource = true;
        });

        if (!hydratedSource) return false;
        video.setAttribute('data-hydrated', 'true');
        video.load();
        return true;
    }

    function updateAudioButtons() {
        audioButtons.forEach(function (button) {
            button.setAttribute('aria-pressed', audioEnabled ? 'true' : 'false');
            var label = audioEnabled
                ? button.getAttribute('data-hero-audio-label-on')
                : button.getAttribute('data-hero-audio-label-off');
            if (label) button.setAttribute('aria-label', label);
        });
    }

    function syncVideo() {
        video.muted = !audioEnabled;

        if (document.hidden || video.getAttribute('data-hydrated') !== 'true') {
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

    function startDeferredVideo() {
        hydrateVideo();
        syncVideo();
    }

    audioButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            audioEnabled = !audioEnabled;
            updateAudioButtons();
            hydrateVideo();
            syncVideo();
        });
    });
    video.addEventListener('loadeddata', function () {
        slide.classList.remove('has-video-playback-fallback');
    });
    document.addEventListener('visibilitychange', syncVideo);
    window.addEventListener('pagehide', function () {
        document.removeEventListener('visibilitychange', syncVideo);
        video.pause();
    }, { once: true });

    updateAudioButtons();

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(startDeferredVideo, { timeout: 900 });
    } else {
        window.setTimeout(startDeferredVideo, 120);
    }
}
