import { initMegaMenus, onMediaQueryChange } from './welcome-hero/mega-menu.js';
import { armHeroReadySignal } from './welcome-hero/readiness.js';
import { createSliderMediaActions } from './welcome-hero/slider-media.js';
import { createSliderPlaybackActions } from './welcome-hero/slider-playback.js';

function initHeroSlider(root) {
    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
    var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hero-dot]'));
    var previousButton = root.querySelector('[data-hero-previous]');
    var nextButton = root.querySelector('[data-hero-next]');
    var playbackButton = root.querySelector('[data-hero-playback]');
    var audioButton = root.querySelector('[data-hero-audio]');
    var currentLabel = root.querySelector('[data-hero-current]');
    var liveRegion = root.querySelector('[data-hero-live]');
    var progressBar = root.querySelector('[data-hero-progress]');
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var statusTemplate = root.getAttribute('data-slide-label') || 'Slide :current of :total';
    var duration = parseInt(root.getAttribute('data-autoplay-interval'), 10);
    var transitionDuration = 1060;
    var state = {
        currentIndex: Math.max(0, slides.findIndex(function (slide) {
            return slide.classList.contains('is-active');
        })),
        timer: null,
        transitionTimer: null,
        userPaused: false,
        audioEnabled: false,
        pointerStart: null,
        hasPresentedInitialSlide: false
    };

    if (!slides.length) return;
    if (!Number.isFinite(duration) || duration < 4000) duration = 7000;

    slides.forEach(function (slide) {
        if (slide.tagName.toLowerCase() === 'article' && slide.getAttribute('role') === 'group') {
            slide.setAttribute('role', 'region');
        }
    });

    root.style.setProperty('--hero-autoplay-duration', duration + 'ms');
    root.setAttribute('data-enhanced', 'true');

    var mediaActions = createSliderMediaActions({ slides, state, statusTemplate });
    var canAutoplay = mediaActions.canAutoplay;
    var syncVideos = mediaActions.syncVideos;
    var playbackActions = createSliderPlaybackActions({
        root, slides, dots, playbackButton, currentLabel, liveRegion, progressBar,
        reducedMotion, duration, transitionDuration, state, canAutoplay,
        currentVideo: mediaActions.currentVideo,
        formatStatus: mediaActions.formatStatus,
        hydrateSlide: mediaActions.hydrateSlide,
        syncVideos
    });
    var clearTimer = playbackActions.clearTimer;
    var clearTransition = playbackActions.clearTransition;
    var scheduleNext = playbackActions.scheduleNext;
    var showSlide = playbackActions.showSlide;
    var updatePlaybackButton = playbackActions.updatePlaybackButton;

    function updateAudioButton() {
        if (!audioButton) return;
        audioButton.setAttribute('aria-pressed', state.audioEnabled ? 'true' : 'false');
    }

    slides.forEach(function (slide, index) {
        var video = slide.querySelector('[data-hero-video]');
        if (!video) return;

        if (slides.length === 1) {
            video.loop = true;
            video.setAttribute('loop', '');
        } else {
            video.loop = false;
            video.removeAttribute('loop');
        }

        video.addEventListener('ended', function () {
            if (index !== state.currentIndex || !canAutoplay()) return;
            showSlide(state.currentIndex + 1, false);
        });

        video.addEventListener('loadeddata', function () {
            slide.classList.remove('has-video-playback-fallback');
        });
    });

    if (previousButton) {
        previousButton.addEventListener('click', function () {
            showSlide(state.currentIndex - 1, true);
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            showSlide(state.currentIndex + 1, true);
        });
    }

    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () {
            showSlide(index, true);
        });
    });

    if (audioButton) {
        audioButton.addEventListener('click', function () {
            state.audioEnabled = !state.audioEnabled;
            syncVideos();
            updateAudioButton();
        });
    }

    if (playbackButton) {
        playbackButton.addEventListener('click', function () {
            state.userPaused = !state.userPaused;
            updatePlaybackButton();
            syncVideos();
            scheduleNext();
        });
    }

    root.addEventListener('keydown', function (event) {
        if (event.altKey || event.ctrlKey || event.metaKey) return;

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            showSlide(state.currentIndex - 1, true);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            showSlide(state.currentIndex + 1, true);
        }
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
        showSlide(state.currentIndex + (deltaX < 0 ? 1 : -1), true);
    }, { passive: true });

    root.addEventListener('pointercancel', function () {
        state.pointerStart = null;
    }, { passive: true });

    function handleVisibilityChange() {
        syncVideos();
        scheduleNext();
    }

    document.addEventListener('visibilitychange', handleVisibilityChange);

    var removeMotionListener = onMediaQueryChange(reducedMotion, function () {
        updatePlaybackButton();
        syncVideos();
        scheduleNext();
    });

    window.addEventListener('pagehide', function () {
        clearTimer();
        clearTransition();
        removeMotionListener();
        slides.forEach(function (slide) {
            var video = slide.querySelector('[data-hero-video]');
            if (video) video.pause();
        });
    }, { once: true });

    updateAudioButton();
    updatePlaybackButton();
    armHeroReadySignal(root, slides[state.currentIndex]);
    showSlide(state.currentIndex, false);
}

function bootHomepageHero() {
    initMegaMenus();

    document.querySelectorAll('[data-hero-slider]').forEach(function (root) {
        initHeroSlider(root);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootHomepageHero, { once: true });
} else {
    bootHomepageHero();
}
