import {
  automaticHeroDirection,
  dotHeroDirection
} from './direction.js';

const HERO_WEBGL_DURATION = 1350;
const HERO_TEXTURE_WAIT = 1200;
const HERO_WEBGL_SETTLE_DURATION = HERO_TEXTURE_WAIT + HERO_WEBGL_DURATION;

export function createHeroTransitionController(options) {
  var root = options.root;
  var slides = options.slides;
  var reducedMotion = options.reducedMotion;
  var renderer = null;
  var rendererPromise = null;
  var warmHandle = 0;
  var disposed = false;

  function isSupported() {
    return !disposed && !document.hidden && !reducedMotion.matches &&
      'WebGLRenderingContext' in window;
  }

  function loadRenderer() {
    if (!isSupported()) return Promise.resolve(null);
    if (renderer) return Promise.resolve(renderer);
    if (rendererPromise) return rendererPromise;

    root.dataset.heroWebgl = 'loading';
    rendererPromise = import('./webgl/renderer.js')
      .then(function (module) {
        if (disposed) return null;
        renderer = module.createHeroWebglRenderer(root);
        root.dataset.heroWebgl = 'ready';
        return renderer;
      })
      .catch(function () {
        root.dataset.heroWebgl = 'failed';
        return null;
      });

    return rendererPromise;
  }

  function warm() {
    if (!isSupported() || renderer || rendererPromise) return;

    if ('requestIdleCallback' in window) {
      warmHandle = window.requestIdleCallback(loadRenderer, { timeout: 1600 });
    } else {
      warmHandle = window.setTimeout(loadRenderer, 240);
    }
  }

  function directionFor(request, currentIndex, nextIndex) {
    if (request && Number.isFinite(request.direction)) return request.direction;
    if (request && request.origin === 'dot') {
      return dotHeroDirection(currentIndex, nextIndex, slides.length);
    }
    return automaticHeroDirection();
  }

  function play(fromIndex, toIndex, request) {
    if (!isSupported()) return false;
    if (!renderer) {
      loadRenderer();
      return false;
    }

    return renderer.play(
      slides[fromIndex],
      slides[toIndex],
      directionFor(request, fromIndex, toIndex),
      HERO_WEBGL_DURATION,
      HERO_TEXTURE_WAIT
    );
  }

  function resize() {
    renderer?.resize();
  }

  function suspend() {
    renderer?.cancel();
  }

  function dispose() {
    disposed = true;
    if ('cancelIdleCallback' in window) window.cancelIdleCallback(warmHandle);
    else window.clearTimeout(warmHandle);
    renderer?.dispose();
    renderer = null;
    rendererPromise = null;
  }

  return {
    dispose,
    duration: HERO_WEBGL_DURATION,
    play,
    resize,
    settleDuration: HERO_WEBGL_SETTLE_DURATION,
    suspend,
    warm
  };
}
