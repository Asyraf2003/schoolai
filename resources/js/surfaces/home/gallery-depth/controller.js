import { createGalleryStoryLightbox } from '../../../pages/welcome/gallery-story-lightbox.js';
import { readGalleryData } from './data.js';
import { DepthGalleryEngine } from './engine.js';
import { loadThreeRuntime } from './three-runtime.js';

function createLightboxBindings(root, config) {
    const elements = config.map((item) => item.element);
    const lightbox = createGalleryStoryLightbox(root, elements);
    const cleanups = elements.map((element) => {
        const onClick = (event) => {
            if (!element.getAttribute('data-media-url')) return;
            event.preventDefault();
            lightbox.openStoryMedia(element);
        };
        element.addEventListener('click', onClick);
        return () => element.removeEventListener('click', onClick);
    });
    const onKeyDown = (event) => {
        if (event.key === 'Escape' && lightbox.isOpen()) {
            lightbox.closeStoryMedia();
        }
    };
    document.addEventListener('keydown', onKeyDown);
    return {
        lightbox,
        destroy() {
            cleanups.forEach((cleanup) => cleanup());
            document.removeEventListener('keydown', onKeyDown);
        },
    };
}

function createDepthGallery(root) {
    const config = readGalleryData(root);
    const canvas = root.querySelector('[data-depth-gallery-canvas]');
    const fallback = root.querySelector('[data-depth-gallery-fallback]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const lightboxBinding = createLightboxBindings(root, config);
    let engine = null;
    let observer = null;
    let activationFrame = 0;
    let disposed = false;
    let inView = false;
    let initializing = false;
    let active = false;

    function applyFallbackState(disposeEngine = true) {
        if (activationFrame) cancelAnimationFrame(activationFrame);
        activationFrame = 0;
        active = false;
        initializing = false;
        root.classList.remove('is-depth-ready', 'is-depth-active');
        root.classList.add('is-depth-fallback');
        fallback?.removeAttribute('hidden');
        fallback?.removeAttribute('aria-hidden');
        if (fallback) fallback.inert = false;
        canvas?.setAttribute('aria-hidden', 'true');
        canvas?.setAttribute('tabindex', '-1');
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
            canvas?.setAttribute('aria-hidden', 'false');
            canvas?.setAttribute('tabindex', '0');
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

    const onCanvasOpen = () => {
        if (!active) return;
        const index = Number.parseInt(
            canvas?.dataset.activeGalleryIndex || '-1',
            10,
        );
        if (index >= 0) lightboxBinding.lightbox.openStoryMediaByIndex(index);
    };
    const onCanvasKeyDown = (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        onCanvasOpen();
    };
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
        canvas?.removeEventListener('click', onCanvasOpen);
        canvas?.removeEventListener('keydown', onCanvasKeyDown);
        document.removeEventListener('visibilitychange', onVisibility);
        reducedMotion.removeEventListener?.('change', onMotionChange);
        window.removeEventListener('pagehide', onPageHide);
        window.removeEventListener('pageshow', onPageShow);
        lightboxBinding.destroy();
    }

    applyFallbackState(false);
    canvas?.addEventListener('click', onCanvasOpen);
    canvas?.addEventListener('keydown', onCanvasKeyDown);
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
