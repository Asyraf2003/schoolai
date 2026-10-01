export function createSliderMediaActions(options) {
    var slides = options.slides;
    var state = options.state;
    var statusTemplate = options.statusTemplate;
    var singleSlide = slides.length === 1;

    function formatStatus(index) {
        return statusTemplate
            .replace(':current', String(index + 1))
            .replace(':total', String(slides.length));
    }

    function currentVideo() {
        var slide = slides[state.currentIndex];
        return slide ? slide.querySelector('[data-hero-video]') : null;
    }

    function syncLoop(video) {
        video.loop = singleSlide;
        if (singleSlide) video.setAttribute('loop', '');
        else video.removeAttribute('loop');
    }

    function hydrateSlide(slide, allowVideo) {
        if (!slide) return;

        slide.querySelectorAll('img[data-src]').forEach(function (image) {
            image.src = image.getAttribute('data-src');
            image.removeAttribute('data-src');
        });

        if (!allowVideo || state.videoHydrationReady !== true
            || ((state.reducedMotion?.matches || document.hidden || state.suspended) && !state.audioEnabled)) return;

        var video = slide.querySelector('[data-hero-video]');
        if (!video) return;

        syncLoop(video);

        if (video.getAttribute('data-hydrated') === 'true') return;

        var hydratedSource = false;
        video.querySelectorAll('source[data-src]').forEach(function (source) {
            source.addEventListener('error', () => {
                video.pause();
                if (slide === slides[state.currentIndex]) options.root.dataset.heroMediaState = 'media-error';
                slide.classList.add('has-video-playback-fallback');
            }, { once: true, signal: state.lifecycleSignal });
            source.src = source.getAttribute('data-src');
            source.removeAttribute('data-src');
            hydratedSource = true;
        });

        if (hydratedSource) {
            video.preload = 'metadata';
            video.setAttribute('data-hydrated', 'true');
            video.load();
        }
    }

    function canAutoplay() {
        return slides.length > 1 && !state.userPaused && !document.hidden
            && !state.suspended && state.inViewport !== false && !state.reducedMotion?.matches;
    }

    function syncVideos() {
        slides.forEach(function (slide, index) {
            var video = slide.querySelector('[data-hero-video]');
            if (!video) return;

            syncLoop(video);

            if (index !== state.currentIndex) {
                video.muted = true;
                video.pause();
                try { video.currentTime = 0; } catch (error) { /* Metadata may not exist yet. */ }
                return;
            }

            if (state.videoHydrationReady !== true) {
                video.pause();
                return;
            }

            if (state.reducedMotion?.matches && !state.audioEnabled) {
                options.root.dataset.heroMediaState = 'static-reduced';
                video.pause();
                return;
            }
            if (state.suspended || state.inViewport === false || document.hidden) {
                video.pause();
                return;
            }
            hydrateSlide(slide, true);
            video.muted = !state.audioEnabled;

            if (state.userPaused || document.hidden) {
                video.pause();
                return;
            }

            var playAttempt = video.play();
            if (playAttempt && typeof playAttempt.catch === 'function') {
                playAttempt.catch(function () {
                    if (index !== state.currentIndex || state.suspended || document.hidden || state.inViewport === false) return;
                    if (options.root.dataset.heroMediaState !== 'media-error') {
                        options.root.dataset.heroMediaState = video.error ? 'media-error' : 'autoplay-blocked';
                    }
                    slide.classList.add('has-video-playback-fallback');
                });
            }
        });
    }

    return { canAutoplay, currentVideo, formatStatus, hydrateSlide, syncVideos };
}
