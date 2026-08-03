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
    let disposed = false;
    let inView = false;
    let initialized = false;

    const setFallback = () => {
        engine?.dispose();
        engine = null;
        initialized = false;
        root.classList.remove('is-depth-ready');
        root.classList.add('is-depth-fallback');
        fallback?.removeAttribute('hidden');
        fallback?.removeAttribute('aria-hidden');
        if (fallback) fallback.inert = false;
        canvas?.removeAttribute('aria-busy');
    };

    const setReady = () => {
        initialized = true;
        root.classList.remove('is-depth-fallback');
        root.classList.add('is-depth-ready');
        fallback?.setAttribute('hidden', '');
        fallback?.setAttribute('aria-hidden', 'true');
        if (fallback) fallback.inert = true;
        canvas?.removeAttribute('aria-busy');
        if (inView && !document.hidden) engine?.start();
    };

    const initialize = async () => {
        if (disposed || initialized || engine || reducedMotion.matches) return;
        if (!canvas || config.length < 2) return;
        canvas.setAttribute('aria-busy', 'true');
        try {
            const THREE = await loadThreeRuntime();
            if (disposed) return;
            engine = new DepthGalleryEngine(THREE, root, config, setFallback);
            const success = await engine.init();
            if (!success || disposed) {
                setFallback();
                return;
            }
            setReady();
        } catch (error) {
            console.warn('Depth gallery initialization failed', error);
            setFallback();
        }
    };

    const onCanvasOpen = () => {
        const index = Number.parseInt(canvas?.dataset.activeGalleryIndex || '-1', 10);
        if (index >= 0) lightboxBinding.lightbox.openStoryMediaByIndex(index);
    };
    const onCanvasKeyDown = (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;
        event.preventDefault();
        onCanvasOpen();
    };
    const onVisibility = () => {
        if (document.hidden) engine?.stop();
        else if (inView) engine?.start();
    };
    const onPageHide = (event) => {
        if (event.persisted) engine?.stop();
        else destroy();
    };
    const onPageShow = () => {
        if (inView) engine?.start();
    };

    function destroy() {
        if (disposed) return;
        disposed = true;
        observer?.disconnect();
        engine?.dispose();
        engine = null;
        canvas?.removeEventListener('click', onCanvasOpen);
        canvas?.removeEventListener('keydown', onCanvasKeyDown);
        document.removeEventListener('visibilitychange', onVisibility);
        window.removeEventListener('pagehide', onPageHide);
        window.removeEventListener('pageshow', onPageShow);
        lightboxBinding.destroy();
        setFallback();
    }

    canvas?.addEventListener('click', onCanvasOpen);
    canvas?.addEventListener('keydown', onCanvasKeyDown);
    document.addEventListener('visibilitychange', onVisibility);
    window.addEventListener('pagehide', onPageHide);
    window.addEventListener('pageshow', onPageShow);

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            inView = entry.isIntersecting;
            if (inView) {
                initialize();
                engine?.start();
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
