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

        if (!allowVideo) return;

        var video = slide.querySelector('[data-hero-video]');
        if (!video) return;

        syncLoop(video);

        if (slide.classList.contains('is-active')) {
            video.preload = 'auto';
        }

        if (video.getAttribute('data-hydrated') === 'true') return;

        var hydratedSource = false;
        video.querySelectorAll('source[data-src]').forEach(function (source) {
            source.src = source.getAttribute('data-src');
            source.removeAttribute('data-src');
            hydratedSource = true;
        });

        if (hydratedSource) {
            video.setAttribute('data-hydrated', 'true');
            video.load();
        }
    }

    function canAutoplay() {
        return slides.length > 1 && !state.userPaused && !document.hidden;
    }

    function syncVideos() {
        slides.forEach(function (slide, index) {
            var video = slide.querySelector('[data-hero-video]');
            if (!video) return;

            syncLoop(video);

            if (index !== state.currentIndex) {
                video.pause();
                try { video.currentTime = 0; } catch (error) { /* Metadata may not exist yet. */ }
                return;
            }

            hydrateSlide(slide, true);

            if (state.userPaused || document.hidden) {
                video.pause();
                return;
            }

            var playAttempt = video.play();
            if (playAttempt && typeof playAttempt.catch === 'function') {
                playAttempt.catch(function () {
                    slide.classList.add('has-video-playback-fallback');
                });
            }
        });
    }

    return { canAutoplay, currentVideo, formatStatus, hydrateSlide, syncVideos };
}
