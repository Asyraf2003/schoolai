import { createGalleryStoryLightbox } from '../../../pages/welcome/gallery-story-lightbox.js';
import { DepthGalleryRenderer } from './renderer.js';
import {
    blendPalette,
    clearDepthItems,
    readPalettes,
    sceneProgress,
    updateDepthItems,
} from './scene.js';

function bindLightbox(root, cards) {
    const lightbox = createGalleryStoryLightbox(root, cards);

    cards.forEach((card) => {
        card.addEventListener('click', (event) => {
            if (!card.getAttribute('data-media-url')) return;
            event.preventDefault();
            lightbox.openStoryMedia(card);
        });
    });

    const onKeyDown = (event) => {
        if (event.key === 'Escape' && lightbox.isOpen()) lightbox.closeStoryMedia();
    };
    document.addEventListener('keydown', onKeyDown);
    return () => document.removeEventListener('keydown', onKeyDown);
}

function createDepthGallery(root) {
    const journey = root.querySelector('[data-depth-gallery-journey]');
    const viewport = root.querySelector('[data-depth-gallery-viewport]');
    const canvas = root.querySelector('[data-depth-gallery-canvas]');
    const progressBar = root.querySelector('[data-depth-gallery-progress]');
    const items = Array.from(root.querySelectorAll('[data-depth-gallery-item]'));
    const cards = Array.from(root.querySelectorAll('[data-depth-gallery-card]'));
    const cleanupLightbox = bindLightbox(root, cards);
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    if (!journey || !viewport || !canvas || items.length < 2 || reducedMotion.matches) {
        return { destroy: cleanupLightbox };
    }

    const palettes = readPalettes(cards);
    const pointer = { x: 0, y: 0 };
    let renderer = null;
    let observer = null;
    let frame = 0;
    let active = false;
    let inView = false;
    let disposed = false;
    let previousProgress = 0;
    let lastRenderTime = 0;

    const failRenderer = () => {
        root.classList.add('is-depth-fallback');
        renderer?.dispose();
        renderer = null;
    };

    const ensureRenderer = () => {
        if (renderer || disposed) return;
        const candidate = new DepthGalleryRenderer(canvas, failRenderer);
        if (!candidate.init()) {
            candidate.dispose();
            failRenderer();
            return;
        }
        renderer = candidate;
    };

    const render = (time) => {
        frame = 0;
        if (!active || disposed || document.hidden) return;
        if (time - lastRenderTime < 30) {
            frame = requestAnimationFrame(render);
            return;
        }
        lastRenderTime = time;
        const progress = sceneProgress(journey, viewport);
        const camera = progress * (items.length - 1);
        const velocity = progress - previousProgress;
        previousProgress = progress;
        updateDepthItems(items, camera, viewport.clientWidth, pointer);
        progressBar?.style.setProperty('transform', `scaleX(${progress.toFixed(5)})`);
        renderer?.resize();
        renderer?.render(blendPalette(palettes, camera), time, velocity);
        frame = requestAnimationFrame(render);
    };

    const start = () => {
        if (active || disposed || !inView || document.hidden) return;
        active = true;
        ensureRenderer();
        if (!frame) frame = requestAnimationFrame(render);
    };

    const stop = () => {
        active = false;
        if (frame) cancelAnimationFrame(frame);
        frame = 0;
    };

    const onPointerMove = (event) => {
        const rect = viewport.getBoundingClientRect();
        if (!rect.width || !rect.height) return;
        pointer.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        pointer.y = -(((event.clientY - rect.top) / rect.height) * 2 - 1);
        renderer?.setPointer(pointer.x, pointer.y);
    };

    const onPointerLeave = () => {
        pointer.x = 0;
        pointer.y = 0;
        renderer?.setPointer(0, 0);
    };

    const onVisibility = () => document.hidden ? stop() : start();
    const onPageHide = (event) => event.persisted ? stop() : destroy();
    const onPageShow = () => start();

    function destroy() {
        if (disposed) return;
        disposed = true;
        stop();
        observer?.disconnect();
        viewport.removeEventListener('pointermove', onPointerMove);
        viewport.removeEventListener('pointerleave', onPointerLeave);
        document.removeEventListener('visibilitychange', onVisibility);
        window.removeEventListener('pagehide', onPageHide);
        window.removeEventListener('pageshow', onPageShow);
        renderer?.dispose();
        cleanupLightbox();
        clearDepthItems(items);
        progressBar?.removeAttribute('style');
        root.classList.remove('is-depth-ready');
    }

    root.classList.add('is-depth-ready');
    viewport.addEventListener('pointermove', onPointerMove, { passive: true });
    viewport.addEventListener('pointerleave', onPointerLeave, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);
    window.addEventListener('pagehide', onPageHide);
    window.addEventListener('pageshow', onPageShow);

    observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            inView = entry.isIntersecting;
            inView ? start() : stop();
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
