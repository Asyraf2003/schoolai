import '../../../css/pages/welcome-hero-carousel.css';
import { onMediaQueryChange } from './mega-menu.js';
import { createSliderMediaActions } from './slider-media.js';
import { createSliderPlaybackActions } from './slider-playback.js';

export function initHeroCarousel(root, slides) {
    var previousButton = root.querySelector('[data-hero-previous]');
    var nextButton = root.querySelector('[data-hero-next]');
    var audioButtons = Array.from(document.querySelectorAll('[data-hero-audio]'));
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var duration = parseInt(root.getAttribute('data-autoplay-interval'), 10);
    var state = {
        currentIndex: Math.max(0, slides.findIndex(function (slide) {
            return slide.classList.contains('is-active');
        })),
        timer: null,
        transitionTimer: null,
        userPaused: false,
        audioEnabled: false,
        videoHydrationReady: false,
        pointerStart: null,
        hasPresentedInitialSlide: false
    };

    if (!Number.isFinite(duration) || duration < 4000) duration = 7000;
    root.style.setProperty('--hero-autoplay-duration', duration + 'ms');
    root.setAttribute('data-enhanced', 'true');

    var mediaActions = createSliderMediaActions({
        slides,
        state,
        statusTemplate: root.getAttribute('data-slide-label') || 'Slide :current of :total'
    });
    var playbackActions = createSliderPlaybackActions({
        root,
        slides,
        dots: [],
        playbackButton: null,
        currentLabel: null,
        liveRegion: root.querySelector('[data-hero-live]'),
        progressBar: null,
        reducedMotion,
        duration,
        transitionDuration: 1060,
        state,
        canAutoplay: mediaActions.canAutoplay,
        currentVideo: mediaActions.currentVideo,
        formatStatus: mediaActions.formatStatus,
        hydrateSlide: mediaActions.hydrateSlide,
        syncVideos: mediaActions.syncVideos
    });

    function updateAudioButtons() {
        audioButtons.forEach(function (button) {
            button.setAttribute('aria-pressed', state.audioEnabled ? 'true' : 'false');
            var label = state.audioEnabled
                ? button.getAttribute('data-hero-audio-label-on')
                : button.getAttribute('data-hero-audio-label-off');
            if (label) button.setAttribute('aria-label', label);
        });
    }

    function startDeferredVideo() {
        if (state.videoHydrationReady) return;
        state.videoHydrationReady = true;
        mediaActions.syncVideos();
    }

    slides.forEach(function (slide, index) {
        var video = slide.querySelector('[data-hero-video]');
        if (!video) return;

        video.loop = false;
        video.removeAttribute('loop');
        video.addEventListener('ended', function () {
            if (index !== state.currentIndex || !mediaActions.canAutoplay()) return;
            playbackActions.showSlide(state.currentIndex + 1, false);
        });
        video.addEventListener('loadeddata', function () {
            slide.classList.remove('has-video-playback-fallback');
        });
    });

    previousButton?.addEventListener('click', function () {
        playbackActions.showSlide(state.currentIndex - 1, true);
    });
    nextButton?.addEventListener('click', function () {
        playbackActions.showSlide(state.currentIndex + 1, true);
    });
    audioButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            state.audioEnabled = !state.audioEnabled;
            startDeferredVideo();
            mediaActions.syncVideos();
            updateAudioButtons();
        });
    });

    root.addEventListener('keydown', function (event) {
        if (event.altKey || event.ctrlKey || event.metaKey) return;
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        event.preventDefault();
        playbackActions.showSlide(
            state.currentIndex + (event.key === 'ArrowRight' ? 1 : -1), true,
        );
    });
    root.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse' || event.target.closest('a, button, form, [role="link"]')) return;
        state.pointerStart = { x: event.clientX, y: event.clientY, id: event.pointerId };
    }, { passive: true });
    root.addEventListener('pointerup', function (event) {
        if (!state.pointerStart || state.pointerStart.id !== event.pointerId) return;
        var deltaX = event.clientX - state.pointerStart.x;
        var deltaY = event.clientY - state.pointerStart.y;
        state.pointerStart = null;
        if (Math.abs(deltaX) < 48 || Math.abs(deltaX) <= Math.abs(deltaY)) return;
        playbackActions.showSlide(state.currentIndex + (deltaX < 0 ? 1 : -1), true);
    }, { passive: true });
    root.addEventListener('pointercancel', function () {
        state.pointerStart = null;
    }, { passive: true });

    function handleVisibilityChange() {
        mediaActions.syncVideos();
        playbackActions.scheduleNext();
    }

    document.addEventListener('visibilitychange', handleVisibilityChange);
    var removeMotionListener = onMediaQueryChange(reducedMotion, function () {
        mediaActions.syncVideos();
        playbackActions.scheduleNext();
    });
    window.addEventListener('pagehide', function () {
        playbackActions.clearTimer();
        playbackActions.clearTransition();
        removeMotionListener();
        document.removeEventListener('visibilitychange', handleVisibilityChange);
        slides.forEach(function (slide) {
            slide.querySelector('[data-hero-video]')?.pause();
        });
    }, { once: true });

    updateAudioButtons();
    playbackActions.showSlide(state.currentIndex, false);

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(startDeferredVideo, { timeout: 900 });
    } else {
        window.setTimeout(startDeferredVideo, 120);
    }
}
