import '../../../css/pages/welcome-hero-carousel.css';
import { onMediaQueryChange } from './mega-menu.js';
import { HERO_READY_EVENT } from './readiness.js';
import { createSliderMediaActions } from './slider-media.js';
import { createSliderPlaybackActions } from './slider-playback.js';

export function initHeroCarousel(root, slides) {
    var previousButton = root.querySelector('[data-hero-previous]');
    var nextButton = root.querySelector('[data-hero-next]');
    var audioButtons = Array.from(document.querySelectorAll('[data-hero-audio]'));
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var duration = parseInt(root.getAttribute('data-autoplay-interval'), 10);
    var lifecycle = new AbortController();
    var options = { signal: lifecycle.signal };
    var observer = null;
    var state = {
        reducedMotion,
        suspended: false,
        inViewport: true,
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
        root,
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
        }, options);
        video.addEventListener('loadeddata', function () {
            slide.classList.remove('has-video-playback-fallback');
            if (index === state.currentIndex) {
                root.dataset.heroMediaState = 'frame-ready';
                if (!root.dataset.heroFirstFrame) {
                    root.dataset.heroFirstFrame = 'true';
                    performance.mark('schoolai:hero-first-frame');
                }
            }
        }, options);
        video.addEventListener('playing', () => {
            if (index === state.currentIndex) root.dataset.heroMediaState = 'playing';
        }, options);
        video.addEventListener('error', () => {
            if (index === state.currentIndex) root.dataset.heroMediaState = 'media-error';
        }, options);
    });

    previousButton?.addEventListener('click', function () {
        playbackActions.showSlide(state.currentIndex - 1, true);
    }, options);
    nextButton?.addEventListener('click', function () {
        playbackActions.showSlide(state.currentIndex + 1, true);
    }, options);
    audioButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            state.audioEnabled = !state.audioEnabled;
            startDeferredVideo();
            mediaActions.syncVideos();
            updateAudioButtons();
        }, options);
    });

    root.addEventListener('keydown', function (event) {
        if (event.altKey || event.ctrlKey || event.metaKey) return;
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        event.preventDefault();
        playbackActions.showSlide(
            state.currentIndex + (event.key === 'ArrowRight' ? 1 : -1), true,
        );
    }, options);
    root.addEventListener('pointerdown', function (event) {
        if (event.pointerType === 'mouse' || event.target.closest('a, button, form, [role="link"]')) return;
        state.pointerStart = { x: event.clientX, y: event.clientY, id: event.pointerId };
    }, { passive: true, signal: lifecycle.signal });
    root.addEventListener('pointerup', function (event) {
        if (!state.pointerStart || state.pointerStart.id !== event.pointerId) return;
        var deltaX = event.clientX - state.pointerStart.x;
        var deltaY = event.clientY - state.pointerStart.y;
        state.pointerStart = null;
        if (Math.abs(deltaX) < 48 || Math.abs(deltaX) <= Math.abs(deltaY)) return;
        playbackActions.showSlide(state.currentIndex + (deltaX < 0 ? 1 : -1), true);
    }, { passive: true, signal: lifecycle.signal });
    root.addEventListener('pointercancel', function () {
        state.pointerStart = null;
    }, { passive: true, signal: lifecycle.signal });

    function handleVisibilityChange() {
        mediaActions.syncVideos();
        playbackActions.scheduleNext();
    }

    document.addEventListener('visibilitychange', handleVisibilityChange, options);
    var removeMotionListener = onMediaQueryChange(reducedMotion, function () {
        mediaActions.syncVideos();
        playbackActions.scheduleNext();
    });
    window.addEventListener('pagehide', function (event) {
        state.suspended = true;
        playbackActions.clearTimer();
        playbackActions.clearTransition();
        slides.forEach(slide => slide.querySelector('[data-hero-video]')?.pause());
        if (!event.persisted) {
            lifecycle.abort();
            removeMotionListener();
            observer?.disconnect();
        }
    }, options);
    window.addEventListener('pageshow', function () {
        state.suspended = false;
        handleVisibilityChange();
    }, options);
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(function (entries) {
            state.inViewport = entries.some(entry => entry.isIntersecting);
            handleVisibilityChange();
        });
        observer.observe(root);
    }

    updateAudioButtons();
    playbackActions.showSlide(state.currentIndex, false);

    if (root.dataset.heroReady === 'true') startDeferredVideo();
    else window.addEventListener(HERO_READY_EVENT, startDeferredVideo, { once: true, signal: lifecycle.signal });
}
