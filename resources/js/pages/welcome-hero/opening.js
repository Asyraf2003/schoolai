import { HERO_READY_EVENT } from './readiness.js';

export function initOpeningHero(root, slide) {
    var video = slide.querySelector('[data-hero-video]');
    var audioButtons = Array.from(document.querySelectorAll('[data-hero-audio]'));
    var audioEnabled = false;
    root.setAttribute('data-enhanced', 'true');
    if (!video) return;

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var lifecycle = new AbortController();
    var options = { signal: lifecycle.signal };
    var inViewport = true;
    var suspended = false;
    var openingFallback = root.dataset.heroPreparationFallback === 'true';
    var observer = null;
    video.loop = true;
    video.setAttribute('loop', '');
    root.dataset.heroMediaState = 'waiting-shell';

    function hydrateVideo() {
        if (video.getAttribute('data-hydrated') === 'true') return true;
        var hydratedSource = false;
        video.querySelectorAll('source[data-src]').forEach(function (source) {
            source.addEventListener('error', mediaError, options);
            source.src = source.getAttribute('data-src');
            source.removeAttribute('data-src');
            hydratedSource = true;
        });
        if (!hydratedSource) return false;
        video.preload = 'auto';
        video.setAttribute('data-hydrated', 'true');
        root.dataset.heroMediaState = 'preparing';
        video.load();
        return true;
    }

    function updateAudioButtons() {
        audioButtons.forEach(function (button) {
            button.setAttribute('aria-pressed', audioEnabled ? 'true' : 'false');
            var label = audioEnabled ? button.getAttribute('data-hero-audio-label-on')
                : button.getAttribute('data-hero-audio-label-off');
            if (label) button.setAttribute('aria-label', label);
        });
    }

    function syncVideo() {
        video.muted = !audioEnabled;
        if (video.dataset.homeMediaPreparing === 'true') return;
        if ((document.documentElement.dataset.homeScrollGate === 'locked' && !audioEnabled) || openingFallback || root.dataset.heroMediaState === 'media-error' || document.hidden || suspended || !inViewport || (reducedMotion.matches && !audioEnabled)
            || video.getAttribute('data-hydrated') !== 'true') {
            video.pause();
            return;
        }
        var playAttempt = video.play();
        if (playAttempt && typeof playAttempt.catch === 'function') {
            playAttempt.catch(function () {
                if (lifecycle.signal.aborted || document.hidden || suspended || !inViewport) return;
                if (root.dataset.heroMediaState !== 'media-error') {
                    root.dataset.heroMediaState = video.error ? 'media-error' : 'autoplay-blocked';
                    video.dispatchEvent(new Event('schoolai:hero-media-state'));
                }
                slide.classList.add('has-video-playback-fallback');
            });
        }
    }

    function warmVideo() {
        if (openingFallback || document.hidden || suspended || !inViewport) { video.pause(); return; }
        if (reducedMotion.matches && !audioEnabled) {
            root.dataset.heroMediaState = 'static-reduced';
            video.dispatchEvent(new Event('schoolai:hero-media-state'));
            video.pause();
            return;
        }
        hydrateVideo();
        syncVideo();
    }

    audioButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            audioEnabled = !audioEnabled;
            if (audioEnabled) openingFallback = false;
            updateAudioButtons();
            hydrateVideo();
            if (video.error || root.dataset.heroMediaState === 'media-error') {
                root.dataset.heroMediaState = 'preparing';
                video.load();
            }
            syncVideo();
        }, options);
    });
    video.addEventListener('loadeddata', function () {
        root.dataset.heroMediaState = 'frame-ready';
        if (!root.dataset.heroFirstFrame) {
            root.dataset.heroFirstFrame = 'true';
            performance.mark('schoolai:hero-first-frame');
        }
        slide.classList.remove('has-video-playback-fallback');
        if (document.documentElement.dataset.homeScrollGate === 'locked' && !audioEnabled && video.dataset.homeMediaPreparing !== 'true') video.pause();
    }, options);
    video.addEventListener('playing', function () { root.dataset.heroMediaState = 'playing'; }, options);
    function mediaError() {
        video.pause();
        root.dataset.heroMediaState = 'media-error';
        slide.classList.add('has-video-playback-fallback');
        video.dispatchEvent(new Event('schoolai:hero-media-state'));
    }
    video.addEventListener('error', mediaError, options);
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) video.pause();
        else if (root.dataset.heroReady === 'true') warmVideo();
    }, options);
    document.addEventListener('schoolai:hero-media-fallback', () => {
        openingFallback = true;
        video.pause();
        if (video.getAttribute('data-hydrated') === 'true') {
            video.querySelectorAll('source[src]').forEach(source => { source.setAttribute('data-src', source.src); source.removeAttribute('src'); });
            video.removeAttribute('data-hydrated');
            video.load();
        }
        root.dataset.heroMediaState = 'static-fallback';
    }, options);
    document.addEventListener('schoolai:first-journey-ready', warmVideo, options);
    reducedMotion.addEventListener('change', warmVideo, options);
    window.addEventListener('pagehide', function (event) {
        video.pause();
        suspended = true;
        if (!event.persisted) {
            lifecycle.abort();
            observer?.disconnect();
        }
    }, options);
    window.addEventListener('pageshow', function () {
        suspended = false;
        if (root.dataset.heroReady === 'true') warmVideo();
    }, options);
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(function (entries) {
            inViewport = entries.some(function (entry) { return entry.isIntersecting; });
            syncVideo();
        });
        observer.observe(root);
    }
    updateAudioButtons();
    if (root.dataset.heroReady === 'true') warmVideo();
    else window.addEventListener(HERO_READY_EVENT, warmVideo, { once: true, signal: lifecycle.signal });
}
