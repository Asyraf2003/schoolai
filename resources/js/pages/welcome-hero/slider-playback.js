export function createSliderPlaybackActions(options) {
    var root = options.root;
    var slides = options.slides;
    var dots = options.dots;
    var playbackButton = options.playbackButton;
    var currentLabel = options.currentLabel;
    var liveRegion = options.liveRegion;
    var progressBar = options.progressBar;
    var reducedMotion = options.reducedMotion;
    var duration = options.duration;
    var transitionDuration = options.transitionDuration;
    var state = options.state;
    var canAutoplay = options.canAutoplay;
    var currentVideo = options.currentVideo;
    var formatStatus = options.formatStatus;
    var hydrateSlide = options.hydrateSlide;
    var syncVideos = options.syncVideos;

    function resetProgress() {
        root.classList.remove('is-autoplaying');

        if (progressBar) {
            progressBar.style.animation = 'none';
            void progressBar.offsetWidth;
            progressBar.style.animation = '';
        }

        if (canAutoplay() && !currentVideo()) {
            root.classList.add('is-autoplaying');
        }
    }

    function clearTimer() {
        if (state.timer !== null) {
            window.clearTimeout(state.timer);
            state.timer = null;
        }
    }

    function clearTransition() {
        if (state.transitionTimer !== null) {
            window.clearTimeout(state.transitionTimer);
            state.transitionTimer = null;
        }

        slides.forEach(function (slide) {
            slide.classList.remove('is-entering', 'is-leaving');
        });
    }

    function scheduleNext() {
        clearTimer();
        resetProgress();

        if (!canAutoplay()) return;

        // Video slides own their duration. They advance only after the media ends.
        if (currentVideo()) return;

        state.timer = window.setTimeout(function () {
            showSlide(state.currentIndex + 1, false);
        }, duration);
    }

    function updatePlaybackButton() {
        if (!playbackButton) return;

        var isPaused = state.userPaused || reducedMotion.matches;
        playbackButton.classList.toggle('is-paused', isPaused);
        playbackButton.setAttribute('aria-pressed', isPaused ? 'true' : 'false');
        playbackButton.setAttribute(
            'aria-label',
            isPaused
                ? (playbackButton.getAttribute('data-play-label') || 'Play slideshow')
                : (playbackButton.getAttribute('data-pause-label') || 'Pause slideshow')
        );
    }

    function showSlide(requestedIndex, announce) {
        var nextIndex = (requestedIndex + slides.length) % slides.length;
        var previousIndex = state.currentIndex;
        var shouldAnimate = state.hasPresentedInitialSlide &&
            nextIndex !== previousIndex &&
            !reducedMotion.matches;

        clearTransition();
        state.currentIndex = nextIndex;

        slides.forEach(function (slide, index) {
            var isActive = index === state.currentIndex;

            slide.classList.toggle('is-active', isActive);
            slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            slide.inert = !isActive;

            if (shouldAnimate && index === previousIndex) {
                slide.classList.add('is-leaving');
            }

            if (shouldAnimate && isActive) {
                slide.classList.add('is-entering');
            }
        });

        if (shouldAnimate) {
            state.transitionTimer = window.setTimeout(function () {
                slides.forEach(function (slide) {
                    slide.classList.remove('is-entering', 'is-leaving');
                });
                state.transitionTimer = null;
            }, transitionDuration);
        }

        state.hasPresentedInitialSlide = true;

        dots.forEach(function (dot, index) {
            var isActive = index === state.currentIndex;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
            dot.tabIndex = isActive ? 0 : -1;
        });

        if (currentLabel) {
            currentLabel.textContent = String(state.currentIndex + 1).padStart(2, '0');
        }

        hydrateSlide(slides[state.currentIndex], true);

        var nextSlide = slides[(state.currentIndex + 1) % slides.length];
        if (nextSlide && !nextSlide.querySelector('[data-hero-video]')) {
            hydrateSlide(nextSlide, false);
        }

        syncVideos();

        if (announce && liveRegion) {
            liveRegion.textContent = formatStatus(state.currentIndex) + ': ' +
                (slides[state.currentIndex].getAttribute('data-slide-title') || '');
        }

        scheduleNext();
    }

    return { clearTimer, clearTransition, scheduleNext, showSlide, updatePlaybackButton };
}
