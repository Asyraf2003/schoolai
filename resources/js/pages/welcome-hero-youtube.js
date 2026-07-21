/* Temporary YouTube hero support for locale-backed fallback slides.
 * The hero data contract can later be populated from the database without
 * changing the slider presentation layer.
 */

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

function postYouTubeCommand(frame, command, args) {
    if (!frame || !frame.contentWindow || frame.getAttribute('data-hydrated') !== 'true') return;

    frame.contentWindow.postMessage(JSON.stringify({
        event: 'command',
        func: command,
        args: Array.isArray(args) ? args : [],
    }), '*');
}

function hydrateYouTubeFrame(frame) {
    if (!frame || frame.getAttribute('data-hydrated') === 'true') return;

    var source = frame.getAttribute('data-src');
    if (!source) return;

    frame.src = source;
    frame.removeAttribute('data-src');
    frame.setAttribute('data-hydrated', 'true');
}

function syncYouTubeSlide(slide) {
    var frame = slide.querySelector('[data-hero-youtube]');
    if (!frame) return;

    var isActive = slide.classList.contains('is-active');

    if (isActive) {
        hydrateYouTubeFrame(frame);

        if (document.hidden || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            postYouTubeCommand(frame, 'pauseVideo');
            return;
        }

        postYouTubeCommand(frame, 'playVideo');
        return;
    }

    postYouTubeCommand(frame, 'pauseVideo');
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
    frame.setAttribute('data-src', sourceUrl);
    frame.setAttribute('title', slide ? (slide.getAttribute('data-slide-title') || 'Hero video') : 'Hero video');
    frame.setAttribute('tabindex', '-1');
    frame.setAttribute('aria-hidden', 'true');
    frame.setAttribute('loading', 'lazy');
    frame.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
    frame.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture');

    frame.addEventListener('load', function () {
        if (slide && slide.classList.contains('is-active')) {
            postYouTubeCommand(frame, 'playVideo');
        }
    });

    wrapper.appendChild(frame);
    video.replaceWith(wrapper);

    return slide;
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

initYouTubeHeroSlides();
