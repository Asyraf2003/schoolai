import { bindGalleryRouteExit } from '../../../components/gallery-route-transition.js';
import { readGalleryData } from './data.js';
import { DepthGalleryEngine } from './engine.js';
import {
    createGalleryLifecycle,
    GalleryLifecycleState,
} from './lifecycle.js';
import { loadThreeRuntime } from './three-runtime.js';

function createDepthGallery(root) {
    const config = readGalleryData(root);
    const canvas = root.querySelector('[data-depth-gallery-canvas]');
    const fallback = root.querySelector('[data-depth-gallery-fallback]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = createGalleryLifecycle(root, canvas, fallback);
    let engine = null;
    let observer = null;
    let activationFrame = 0;
    let inView = false;
    let routeTransition = null;

    function applyFallbackState(disposeEngine = true) {
        if (activationFrame) cancelAnimationFrame(activationFrame);
        activationFrame = 0;
        if (disposeEngine) {
            const failedEngine = engine;
            engine = null;
            failedEngine?.dispose();
        }
        lifecycle.staticReady();
    }

    function activateEngine() {
        if (!lifecycle.is(GalleryLifecycleState.Prepared)) return;
        lifecycle.transition(GalleryLifecycleState.EnhancementReady);
        activationFrame = requestAnimationFrame(() => {
            activationFrame = 0;
            if (
                lifecycle.is(GalleryLifecycleState.Disposed)
                || !engine
                || !engine.activate()
            ) {
                applyFallbackState();
                return;
            }
            if (inView && !document.hidden) {
                lifecycle.transition(GalleryLifecycleState.Active);
                engine.start();
            } else {
                engine.stop();
                lifecycle.transition(GalleryLifecycleState.Suspended);
            }
        });
    }

    async function initialize() {
        if (
            !lifecycle.is(GalleryLifecycleState.StaticReady)
            || reducedMotion.matches
            || !canvas
            || config.length < 2
        ) return;

        lifecycle.transition(GalleryLifecycleState.Fetching);
        try {
            const THREE = await loadThreeRuntime();
            if (
                !lifecycle.is(GalleryLifecycleState.Fetching)
                || reducedMotion.matches
            ) return;

            const candidate = new DepthGalleryEngine(
                THREE,
                root,
                config,
                applyFallbackState,
            );
            engine = candidate;
            const success = await candidate.init();
            if (
                !success
                || engine !== candidate
                || reducedMotion.matches
                || !lifecycle.is(GalleryLifecycleState.Fetching)
            ) {
                candidate.dispose();
                if (engine === candidate) {
                    engine = null;
                    applyFallbackState(false);
                }
                return;
            }
            if (!lifecycle.transition(GalleryLifecycleState.Prepared)) {
                applyFallbackState();
                return;
            }
            activateEngine();
        } catch (error) {
            console.warn('Depth gallery initialization failed', error);
            applyFallbackState();
        }
    }

    function start() {
        if (!engine || !inView || document.hidden) return;
        if (lifecycle.is(GalleryLifecycleState.Suspended)) {
            if (!engine.activate()) {
                applyFallbackState();
                return;
            }
            lifecycle.transition(GalleryLifecycleState.Active);
        }
        if (lifecycle.is(GalleryLifecycleState.Active)) engine.start();
    }

    function stop() {
        engine?.stop();
    }

    function suspend() {
        stop();
        if (
            lifecycle.is(GalleryLifecycleState.Prepared)
            || lifecycle.is(GalleryLifecycleState.EnhancementReady)
            || lifecycle.is(GalleryLifecycleState.Active)
        ) lifecycle.transition(GalleryLifecycleState.Suspended);
    }

    const onVisibility = () => {
        if (document.hidden) suspend();
        else start();
    };
    const onMotionChange = () => {
        if (reducedMotion.matches) applyFallbackState();
        else if (inView) initialize();
    };
    const onPageHide = (event) => {
        if (event.persisted) suspend();
        else destroy();
    };
    const onPageShow = (event) => {
        if (event.persisted) routeTransition?.restore();
        start();
    };

    function destroy() {
        if (lifecycle.is(GalleryLifecycleState.Disposed)) return;
        if (activationFrame) cancelAnimationFrame(activationFrame);
        activationFrame = 0;
        observer?.disconnect();
        engine?.dispose();
        engine = null;
        document.removeEventListener('visibilitychange', onVisibility);
        reducedMotion.removeEventListener?.('change', onMotionChange);
        window.removeEventListener('pagehide', onPageHide);
        window.removeEventListener('pageshow', onPageShow);
        routeTransition?.destroy();
        lifecycle.dispose();
    }

    applyFallbackState(false);
    routeTransition = bindGalleryRouteExit(root, () => ({ stop: suspend }));
    document.addEventListener('visibilitychange', onVisibility);
    reducedMotion.addEventListener?.('change', onMotionChange);
    window.addEventListener('pagehide', onPageHide);
    window.addEventListener('pageshow', onPageShow);

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            inView = entry.isIntersecting;
            if (inView) {
                initialize();
                start();
            } else {
                suspend();
            }
        });
    }, { rootMargin: '35% 0px 35% 0px', threshold: 0.01 });
    observer.observe(root);

    return { destroy };
}

export function mountHomepageDepthGallery() {
    const root = document.querySelector('[data-depth-gallery]');
    if (!root || root.dataset.depthMounted === '1') return;
    root.dataset.depthMounted = '1';
    createDepthGallery(root);
}
