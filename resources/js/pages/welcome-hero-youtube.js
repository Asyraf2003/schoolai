/* YouTube hero support plus media-aware slide timing.
 * Image slides keep the normal autoplay interval. Video slides wait until
 * playback actually ends before advancing to the next slide.
 */

var HERO_INTERNAL_TIMER_DISABLED_INTERVAL = 2147480000;

function isYouTubeEmbedUrl(value) {
    if (typeof value !== 'string' || value.trim() === '') return false;

    try {
        var url = new URL(value, window.location.origin);
        var host = url.hostname.toLowerCase();

        return (host === 'www.youtube.com' || host === 'www.youtube-nocookie.com') &&
            /^\/embed\/[A-Za-z0-9_-]{11}\/?$/.test(url.pathname);
    } catch (error) {
        return false;
    }
}

function normalizeYouTubePlaybackUrl(value) {
    if (!isYouTubeEmbedUrl(value)) return value;

    try {
        var url = new URL(value, window.location.origin);

        url.searchParams.set('autoplay', '1');
        url.searchParams.set('mute', '1');
        url.searchParams.set('controls', '0');
        url.searchParams.set('playsinline', '1');
        url.searchParams.set('rel', '0');
        url.searchParams.set('modestbranding', '1');
        url.searchParams.set('enablejsapi', '1');
        url.searchParams.set('loop', '0');
        url.searchParams.delete('playlist');

        return url.toString();
    } catch (error) {
        return value;
    }
}

function postYouTubeMessage(frame, payload) {
    if (!frame || !frame.contentWindow || frame.getAttribute('data-hydrated') !== 'true') return;

    frame.contentWindow.postMessage(JSON.stringify(payload), '*');
}

function postYouTubeCommand(frame, command, args) {
    postYouTubeMessage(frame, {
        event: 'command',
        func: command,
        args: Array.isArray(args) ? args : [],
    });
}

function subscribeToYouTubeState(frame) {
    if (!frame || frame.getAttribute('data-hydrated') !== 'true') return;

    if (!frame.id) {
        frame.id = 'hero-youtube-' + Math.random().toString(36).slice(2, 10);
    }

    postYouTubeMessage(frame, {
        event: 'listening',
        id: frame.id,
    });
    postYouTubeCommand(frame, 'addEventListener', ['onStateChange']);
}

function hydrateYouTubeFrame(frame) {
    if (!frame || frame.getAttribute('data-hydrated') === 'true') return;

    var source = frame.getAttribute('data-src');
    if (!source) return;

    frame.src = normalizeYouTubePlaybackUrl(source);
    frame.removeAttribute('data-src');
    frame.setAttribute('data-hydrated', 'true');
}

function heroPlaybackIsPaused(root) {
    if (!root) return false;

    var playbackButton = root.querySelector('[data-hero-playback]');

    return !!playbackButton && playbackButton.getAttribute('aria-pressed') === 'true';
}

function syncYouTubeSlide(slide) {
    var frame = slide.querySelector('[data-hero-youtube]');
    if (!frame) return;

    var isActive = slide.classList.contains('is-active');
    var root = slide.closest('[data-hero-slider]');

    if (isActive) {
        frame.removeAttribute('data-hero-ended-dispatched');
        hydrateYouTubeFrame(frame);
        subscribeToYouTubeState(frame);

        if (
            document.hidden ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
            heroPlaybackIsPaused(root)
        ) {
            postYouTubeCommand(frame, 'pauseVideo');
            return;
        }

        postYouTubeCommand(frame, 'playVideo');
        return;
    }

    postYouTubeCommand(frame, 'pauseVideo');
    postYouTubeCommand(frame, 'seekTo', [0, true]);
}

function replaceYouTubeVideoPlaceholder(video) {
    var source = video.querySelector('source[data-src], source[src]');
    if (!source) return null;

    var sourceUrl = source.getAttribute('data-src') || source.getAttribute('src') || '';
    if (!isYouTubeEmbedUrl(sourceUrl)) return null;

    var slide = video.closest('[data-hero-slide]');
    var wrapper = document.createElement('div');
    var frame = document.createElement('iframe');
    var poster = video.getAttribute('poster');

    wrapper.className = 'hero-cinema__youtube-frame';

    if (poster) {
        wrapper.style.setProperty('--hero-youtube-poster', 'url("' + poster.replace(/"/g, '\\"') + '")');
        wrapper.classList.add('has-poster');
    }

    frame.setAttribute('data-hero-youtube', '');
    frame.setAttribute('data-src', normalizeYouTubePlaybackUrl(sourceUrl));
    frame.setAttribute('title', slide ? (slide.getAttribute('data-slide-title') || 'Hero video') : 'Hero video');
    frame.setAttribute('tabindex', '-1');
    frame.setAttribute('aria-hidden', 'true');
    frame.setAttribute('loading', 'lazy');
    frame.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
    frame.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture');

    frame.addEventListener('load', function () {
        subscribeToYouTubeState(frame);

        window.setTimeout(function () {
            subscribeToYouTubeState(frame);
        }, 300);

        window.setTimeout(function () {
            subscribeToYouTubeState(frame);
        }, 1000);

        if (slide && slide.classList.contains('is-active')) {
            postYouTubeCommand(frame, 'playVideo');
        }
    });

    wrapper.appendChild(frame);
    video.replaceWith(wrapper);

    return slide;
}

function findYouTubeFrameBySource(source) {
    var frames = Array.prototype.slice.call(document.querySelectorAll('[data-hero-youtube]'));

    return frames.find(function (frame) {
        return frame.contentWindow === source;
    }) || null;
}

function dispatchHeroMediaEnded(frame) {
    if (!frame || frame.getAttribute('data-hero-ended-dispatched') === 'true') return;

    var slide = frame.closest('[data-hero-slide]');
    if (!slide) return;

    frame.setAttribute('data-hero-ended-dispatched', 'true');
    slide.dispatchEvent(new CustomEvent('hero:media-ended', { bubbles: true }));
}

function handleYouTubeMessage(event) {
    if (
        event.origin !== 'https://www.youtube.com' &&
        event.origin !== 'https://www.youtube-nocookie.com'
    ) {
        return;
    }

    var frame = findYouTubeFrameBySource(event.source);
    if (!frame) return;

    var payload = event.data;

    if (typeof payload === 'string') {
        try {
            payload = JSON.parse(payload);
        } catch (error) {
            return;
        }
    }

    if (!payload || typeof payload !== 'object') return;

    if (payload.event === 'onReady') {
        subscribeToYouTubeState(frame);
        return;
    }

    var state = null;

    if (payload.event === 'onStateChange') {
        state = Number(payload.info);
    } else if (
        payload.event === 'infoDelivery' &&
        payload.info &&
        typeof payload.info === 'object'
    ) {
        if (payload.info.playerState !== undefined) {
            state = Number(payload.info.playerState);
        }

        var duration = Number(payload.info.duration);
        var currentTime = Number(payload.info.currentTime);

        if (
            Number.isFinite(duration) &&
            duration > 0 &&
            Number.isFinite(currentTime) &&
            currentTime >= duration - 0.2
        ) {
            dispatchHeroMediaEnded(frame);
        }
    }

    if (state === 1) {
        frame.removeAttribute('data-hero-ended-dispatched');
    } else if (state === 0) {
        dispatchHeroMediaEnded(frame);
    }
}

function prepareHeroSliderMediaTiming() {
    document.querySelectorAll('[data-hero-slider]').forEach(function (root) {
        if (!root.hasAttribute('data-media-autoplay-interval')) {
            var interval = root.getAttribute('data-autoplay-interval') || '7000';
            root.setAttribute('data-media-autoplay-interval', interval);
        }

        /* The original slider still owns all transitions and manual controls.
         * Its generic timer is parked far in the future; the coordinator below
         * decides when automatic advancement is actually allowed.
         */
        root.setAttribute('data-autoplay-interval', String(HERO_INTERNAL_TIMER_DISABLED_INTERVAL));
    });
}

function initHeroMediaDurationCoordinator() {
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    document.querySelectorAll('[data-hero-slider]').forEach(function (root) {
        var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
        var nextButton = root.querySelector('[data-hero-next]');
        var playbackButton = root.querySelector('[data-hero-playback]');
        var progressBar = root.querySelector('[data-hero-progress]');
        var configuredDuration = parseInt(root.getAttribute('data-media-autoplay-interval'), 10);
        var timer = null;
        var advancing = false;

        if (!slides.length || !nextButton) return;
        if (!Number.isFinite(configuredDuration) || configuredDuration < 4000) {
            configuredDuration = 7000;
        }

        function activeSlide() {
            return slides.find(function (slide) {
                return slide.classList.contains('is-active');
            }) || null;
        }

        function canAdvanceAutomatically() {
            return slides.length > 1 &&
                !document.hidden &&
                !reducedMotion.matches &&
                !heroPlaybackIsPaused(root);
        }

        function clearTimer() {
            if (timer !== null) {
                window.clearTimeout(timer);
                timer = null;
            }
        }

        function resetProgress(showProgress) {
            root.classList.remove('is-autoplaying');
            root.style.setProperty('--hero-autoplay-duration', configuredDuration + 'ms');

            if (progressBar) {
                progressBar.style.animation = 'none';
                void progressBar.offsetWidth;
                progressBar.style.animation = '';
            }

            if (showProgress && canAdvanceAutomatically()) {
                root.classList.add('is-autoplaying');
            }
        }

        function syncActiveMediaPlayback() {
            var slide = activeSlide();
            if (!slide) return;

            var nativeVideo = slide.querySelector('[data-hero-video]');
            var youtubeFrame = slide.querySelector('[data-hero-youtube]');
            var shouldPlay = canAdvanceAutomatically();

            if (nativeVideo) {
                nativeVideo.loop = false;
                nativeVideo.removeAttribute('loop');

                if (shouldPlay) {
                    var playAttempt = nativeVideo.play();
                    if (playAttempt && typeof playAttempt.catch === 'function') {
                        playAttempt.catch(function () { /* Existing fallback UI remains in control. */ });
                    }
                } else {
                    nativeVideo.pause();
                }
            }

            if (youtubeFrame) {
                if (shouldPlay) {
                    postYouTubeCommand(youtubeFrame, 'playVideo');
                } else {
                    postYouTubeCommand(youtubeFrame, 'pauseVideo');
                }
            }
        }

        function advance() {
            if (advancing || !canAdvanceAutomatically()) return;

            advancing = true;
            clearTimer();
            nextButton.click();

            window.setTimeout(function () {
                advancing = false;
                schedule();
            }, 0);
        }

        function schedule() {
            clearTimer();

            var slide = activeSlide();
            if (!slide || !canAdvanceAutomatically()) {
                resetProgress(false);
                syncActiveMediaPlayback();
                return;
            }

            var mediaType = (slide.getAttribute('data-media-type') || 'image').toLowerCase();
            var nativeVideo = slide.querySelector('[data-hero-video]');
            var youtubeFrame = slide.querySelector('[data-hero-youtube]');

            syncActiveMediaPlayback();

            if (mediaType === 'image') {
                resetProgress(true);
                timer = window.setTimeout(advance, configuredDuration);
                return;
            }

            /* Video and any future time-based media wait for their completion
             * event. Unknown non-image media falls back to the normal interval
             * so one malformed item cannot freeze the entire homepage forever.
             */
            resetProgress(false);

            if (!nativeVideo && !youtubeFrame) {
                timer = window.setTimeout(advance, configuredDuration);
            }
        }

        slides.forEach(function (slide) {
            var nativeVideo = slide.querySelector('[data-hero-video]');

            if (nativeVideo) {
                nativeVideo.loop = false;
                nativeVideo.removeAttribute('loop');
                nativeVideo.addEventListener('ended', function () {
                    if (slide === activeSlide()) advance();
                });
            }
        });

        root.addEventListener('hero:media-ended', function (event) {
            var slide = event.target.closest ? event.target.closest('[data-hero-slide]') : null;
            if (slide && slide === activeSlide()) advance();
        });

        if (playbackButton) {
            playbackButton.addEventListener('click', function () {
                window.setTimeout(schedule, 0);
            });
        }

        var observer = new MutationObserver(function (mutations) {
            if (mutations.some(function (mutation) { return mutation.attributeName === 'class'; })) {
                schedule();
            }
        });

        slides.forEach(function (slide) {
            observer.observe(slide, { attributes: true, attributeFilter: ['class'] });
        });

        document.addEventListener('visibilitychange', schedule);

        if (typeof reducedMotion.addEventListener === 'function') {
            reducedMotion.addEventListener('change', schedule);
        } else if (typeof reducedMotion.addListener === 'function') {
            reducedMotion.addListener(schedule);
        }

        window.addEventListener('pagehide', function () {
            clearTimer();
            observer.disconnect();
        }, { once: true });

        schedule();
    });
}

function initYouTubeHeroSlides() {
    var changedSlides = [];

    document.querySelectorAll('[data-hero-video]').forEach(function (video) {
        var slide = replaceYouTubeVideoPlaceholder(video);
        if (slide && changedSlides.indexOf(slide) === -1) changedSlides.push(slide);
    });

    changedSlides.forEach(function (slide) {
        syncYouTubeSlide(slide);

        var observer = new MutationObserver(function (mutations) {
            if (mutations.some(function (mutation) { return mutation.attributeName === 'class'; })) {
                syncYouTubeSlide(slide);
            }
        });

        observer.observe(slide, { attributes: true, attributeFilter: ['class'] });
    });

    function syncAll() {
        changedSlides.forEach(syncYouTubeSlide);
    }

    document.addEventListener('visibilitychange', syncAll);
    window.addEventListener('pagehide', function () {
        changedSlides.forEach(function (slide) {
            var frame = slide.querySelector('[data-hero-youtube]');
            postYouTubeCommand(frame, 'pauseVideo');
        });
    }, { once: true });
}

prepareHeroSliderMediaTiming();
window.addEventListener('message', handleYouTubeMessage);
initYouTubeHeroSlides();

document.addEventListener('DOMContentLoaded', function () {
    initHeroMediaDurationCoordinator();
});
