import { bindGalleryRouteExit } from '../../../components/gallery-route-transition.js';
import { readGalleryData } from './data.js';
import { DepthGalleryEngine } from './engine.js';
import { loadThreeRuntime } from './three-runtime.js';

function createDepthGallery(root) {
    const config = readGalleryData(root);
    const canvas = root.querySelector('[data-depth-gallery-canvas]');
    const fallback = root.querySelector('[data-depth-gallery-fallback]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let engine = null;
    let observer = null;
    let activationFrame = 0;
    let disposed = false;
    let inView = false;
    let initializing = false;
    let active = false;

    const routeTransition = bindGalleryRouteExit(
        root,
        () => engine,
    );

    function applyFallbackState(disposeEngine = true) {
        if (activationFrame) cancelAnimationFrame(activationFrame);
        activationFrame = 0;
        active = false;
        initializing = false;
        root.classList.remove(
            'is-depth-ready',
            'is-depth-active',
            'is-depth-end-ready',
            'is-depth-leaving',
        );
        root.classList.add('is-depth-fallback');
        fallback?.removeAttribute('hidden');
        fallback?.removeAttribute('aria-hidden');
        if (fallback) fallback.inert = false;
        canvas?.setAttribute('aria-hidden', 'true');
        canvas?.removeAttribute('aria-busy');
        if (!disposeEngine) return;
        const failedEngine = engine;
        engine = null;
        failedEngine?.dispose();
    }

    function activateEngine() {
        root.classList.remove('is-depth-fallback');
        root.classList.add('is-depth-ready');
        activationFrame = requestAnimationFrame(() => {
            activationFrame = 0;
            if (disposed || !engine || !engine.activate()) {
                applyFallbackState();
                return;
            }
            active = true;
            root.classList.add('is-depth-active');
            fallback?.setAttribute('hidden', '');
            fallback?.setAttribute('aria-hidden', 'true');
            if (fallback) fallback.inert = true;
            canvas?.removeAttribute('aria-busy');
            if (inView && !document.hidden) engine.start();
        });
    }

    async function initialize() {
        if (
            disposed
            || active
            || initializing
            || engine
            || reducedMotion.matches
            || !canvas
            || config.length < 2
        ) return;

        initializing = true;
        canvas.setAttribute('aria-busy', 'true');
        try {
            const THREE = await loadThreeRuntime();
            if (disposed) return;
            engine = new DepthGalleryEngine(
                THREE,
                root,
                config,
                applyFallbackState,
            );
            const success = await engine.init();
            initializing = false;
            if (!success || disposed) {
                applyFallbackState();
                return;
            }
            activateEngine();
        } catch (error) {
            console.warn('Depth gallery initialization failed', error);
            applyFallbackState();
        }
    }

    const onVisibility = () => {
        if (document.hidden) engine?.stop();
        else if (inView && active) engine?.start();
    };
    const onMotionChange = () => {
        if (reducedMotion.matches) applyFallbackState();
        else if (inView) initialize();
    };
    const onPageHide = (event) => {
        if (event.persisted) engine?.stop();
        else destroy();
    };
    const onPageShow = () => {
        if (inView && active) engine?.start();
    };

    function destroy() {
        if (disposed) return;
        disposed = true;
        if (activationFrame) cancelAnimationFrame(activationFrame);
        observer?.disconnect();
        engine?.dispose();
        engine = null;
        document.removeEventListener('visibilitychange', onVisibility);
        reducedMotion.removeEventListener?.('change', onMotionChange);
        window.removeEventListener('pagehide', onPageHide);
        window.removeEventListener('pageshow', onPageShow);
        routeTransition.destroy();
    }

    applyFallbackState(false);
    document.addEventListener('visibilitychange', onVisibility);
    reducedMotion.addEventListener?.('change', onMotionChange);
    window.addEventListener('pagehide', onPageHide);
    window.addEventListener('pageshow', onPageShow);

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            inView = entry.isIntersecting;
            if (inView) {
                initialize();
                if (active) engine?.start();
            } else {
                engine?.stop();
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
