import { clearPaint } from './motion-painters.js';
import {
    FRAME_MS, createScrollMotion, measureScenes, readScroll,
    resetScrollMotion, sceneFrame, updateScrollMotion,
} from './scroll-progress.js';
import {
    clearStoryScenes, linkStoryTexts,
    paintStoryScene, prepareScenes,
} from './scene-renderer.js';
import { prepareStoryText } from './split-text.js';

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const rtl = document.documentElement.dir === 'rtl';
    const scenes = prepareScenes(root);
    const texts = prepareStoryText(root);
    linkStoryTexts(scenes, texts);

    let frame = 0;
    let observer = null;
    let resizeObserver = null;
    let active = true;
    let destroyed = false;
    let layoutDirty = true;
    let forcePaint = true;
    let snapNext = true;
    let lastTime = 0;
    let targetScroll = readScroll();
    const scrollMotion = createScrollMotion(targetScroll);
    let viewportHeight = window.innerHeight || 1;

    function cancelFrame() {
        if (frame) window.cancelAnimationFrame(frame);
        frame = 0;
        lastTime = 0;
    }

    function requestRender() {
        if (!frame && !destroyed && active && !document.hidden) {
            frame = window.requestAnimationFrame(render);
        }
    }

    function refreshLayout() {
        layoutDirty = false;
        viewportHeight = window.innerHeight || 1;
        measureScenes(scenes);
        forcePaint = true;
    }

    function render(time) {
        frame = 0;
        if (destroyed || !active || document.hidden) return;
        if (layoutDirty) refreshLayout();

        targetScroll = readScroll();
        const delta = lastTime ? time - lastTime : FRAME_MS;
        lastTime = time;
        const snapshot = updateScrollMotion(
            scrollMotion,
            targetScroll,
            delta,
            snapNext || reduced.matches,
        );
        const momentum = reduced.matches ? 0 : snapshot.momentum;

        scenes.forEach((scene) => {
            const timing = reduced.matches
                ? { progress: 1, beat: 0 }
                : sceneFrame(scene, snapshot.visual, viewportHeight);
            paintStoryScene(
                scene,
                { ...timing, momentum },
                rtl,
                reduced.matches,
                forcePaint,
            );
        });

        snapNext = false;
        forcePaint = false;
        if (!snapshot.settled) requestRender();
        else lastTime = 0;
    }

    function onScroll() {
        targetScroll = readScroll();
        requestRender();
    }

    function onResize() {
        layoutDirty = true;
        snapNext = true;
        requestRender();
    }

    function onVisibility() {
        if (document.hidden) {
            cancelFrame();
            return;
        }
        onResize();
    }

    function onMotionChange() {
        snapNext = true;
        forcePaint = true;
        requestRender();
    }

    function onIntersection(entries) {
        const nextActive = entries.some((entry) => entry.isIntersecting);
        if (nextActive === active) return;
        active = nextActive;
        cancelFrame();
        if (!active) return;

        targetScroll = readScroll();
        resetScrollMotion(scrollMotion, targetScroll);
        onResize();
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);
    reduced.addEventListener?.('change', onMotionChange);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '100% 0px 100% 0px',
            threshold: 0.01,
        });
        observer.observe(root);
    }
    if ('ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(onResize);
        resizeObserver.observe(root);
    }

    root.classList.add('is-story-ready');
    requestRender();

    return function destroy() {
        destroyed = true;
        active = false;
        root.classList.remove('is-story-ready');
        texts.forEach((item) => {
            clearPaint(item);
            item.element.style.removeProperty('translate');
        });
        clearStoryScenes(scenes);
        cancelFrame();
        observer?.disconnect();
        resizeObserver?.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        reduced.removeEventListener?.('change', onMotionChange);
    };
}
