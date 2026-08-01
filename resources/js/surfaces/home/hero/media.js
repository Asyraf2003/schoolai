export function createHeroMediaController(options) {
  var slides = options.slides;
  var onVideoEnded = options.onVideoEnded;
  var controller = new AbortController();
  var signal = controller.signal;

  function markReady(slide) {
    slide.classList.remove('has-media-error');
    slide.classList.add('has-media-ready');
  }

  function markError(slide) {
    slide.classList.remove('has-media-ready');
    slide.classList.add('has-media-error');
  }

  function hydrateImage(image) {
    if (!image || !image.hasAttribute('data-src')) return;
    image.src = image.getAttribute('data-src');
    image.removeAttribute('data-src');
  }

  function hydrateVideo(video) {
    if (!video || video.dataset.hydrated === 'true') return;

    var changed = false;
    video.querySelectorAll('source[data-src]').forEach(function (source) {
      source.src = source.getAttribute('data-src');
      source.removeAttribute('data-src');
      changed = true;
    });

    if (!changed) return;
    video.dataset.hydrated = 'true';
    video.preload = 'metadata';
    video.load();
  }

  function hydrate(slide, allowVideo) {
    if (!slide) return;
    hydrateImage(slide.querySelector('[data-hero-image]'));
    if (allowVideo) hydrateVideo(slide.querySelector('[data-hero-video]'));
  }

  function prepare() {
    slides.forEach(function (slide, index) {
      var image = slide.querySelector('[data-hero-image]');
      var poster = slide.querySelector('[data-hero-poster]');
      var video = slide.querySelector('[data-hero-video]');

      if (image) {
        image.addEventListener('load', function () { markReady(slide); }, { signal: signal });
        image.addEventListener('error', function () {
          var fallback = image.getAttribute('data-fallback-src');
          var fallbackApplied = image.dataset.fallbackApplied === 'true';

          if (fallback && !fallbackApplied && image.src !== fallback) {
            image.dataset.fallbackApplied = 'true';
            image.src = fallback;
            return;
          }

          markError(slide);
        }, { signal: signal });

        if (image.complete && image.naturalWidth > 0) markReady(slide);
      }

      if (poster) {
        poster.addEventListener('error', function () {
          var fallback = poster.getAttribute('data-fallback-src');
          var fallbackApplied = poster.dataset.fallbackApplied === 'true';

          if (fallback && !fallbackApplied && poster.src !== fallback) {
            poster.dataset.fallbackApplied = 'true';
            poster.src = fallback;
            return;
          }

          markError(slide);
        }, { signal: signal });
      }

      if (!video) return;
      video.loop = false;
      video.removeAttribute('loop');
      video.addEventListener('playing', function () { markReady(slide); }, { signal: signal });
      video.addEventListener('error', function () { markError(slide); }, { signal: signal });
      video.addEventListener('ended', function () { onVideoEnded(index); }, { signal: signal });
      video.querySelectorAll('source').forEach(function (source) {
        source.addEventListener('error', function () { markError(slide); }, { signal: signal });
      });
    });
  }

  function activeVideo(index) {
    var slide = slides[index];
    return slide ? slide.querySelector('[data-hero-video]') : null;
  }

  function isFailed(index) {
    var slide = slides[index];
    return Boolean(slide && slide.classList.contains('has-media-error'));
  }

  function sync(activeIndex, shouldPlay) {
    slides.forEach(function (slide, index) {
      var video = slide.querySelector('[data-hero-video]');
      if (!video) return;

      if (index !== activeIndex) {
        video.pause();
        try { video.currentTime = 0; } catch (error) { /* Metadata may not exist. */ }
        return;
      }

      hydrate(slide, true);
      if (!shouldPlay || isFailed(index)) {
        video.pause();
        return;
      }

      var attempt = video.play();
      if (attempt && typeof attempt.catch === 'function') {
        attempt.catch(function () { markError(slide); });
      }
    });
  }

  function prefetchNext(index) {
    if (slides.length < 2) return;
    var next = slides[(index + 1) % slides.length];
    if (next && !next.querySelector('[data-hero-video]')) hydrate(next, false);
  }

  function suspend() {
    slides.forEach(function (slide) {
      var video = slide.querySelector('[data-hero-video]');
      if (video) video.pause();
    });
  }

  function dispose() {
    suspend();
    controller.abort();
  }

  return { activeVideo, dispose, hydrate, isFailed, prefetchNext, prepare, suspend, sync };
}
