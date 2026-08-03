import { clearPaint, paintScene, paintText } from './motion-painters.js';
import {
    FRAME_MS, PROGRESS_EPSILON, SCROLL_EPSILON, frameBlend,
    measureScenes, readScroll, sceneProgress,
} from './scroll-progress.js';
import { prepareStoryText } from './split-text.js';

function prepareScenes(root) {
    const elements = Array.from(root.querySelectorAll('[data-story-scene]'));
    return elements.map((element, index) => {
        const color = element.dataset.storyColor || '#061d4f';
        const nextColor = elements[index + 1]?.dataset.storyColor || color;
        element.style.setProperty('--scene-color', color);
        element.style.setProperty('--next-scene-color', nextColor);
        return {
            element,
            motion: element.dataset.storyArtMotion || 'none',
            arts: Array.from(element.querySelectorAll('[data-story-scene-art]')),
            texts: [],
            top: 0,
            height: 1,
            painted: null,
        };
    });
}

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const rtl = document.documentElement.dir === 'rtl';
    const scenes = prepareScenes(root);
    const sceneByElement = new Map(
        scenes.map((scene) => [scene.element, scene]),
    );
    const texts = prepareStoryText(root);

    texts.forEach((item) => {
        const owner = item.element.closest('[data-story-scene]');
        sceneByElement.get(owner)?.texts.push(item);
    });

    let frame = 0;
    let observer = null;
    let resizeObserver = null;
    let active = true;
    let destroyed = false;
    let layoutDirty = true;
    let forcePaint = true;
    let snapNext = true;
    let lastTime = 0;
    let currentScroll = readScroll();
    let targetScroll = currentScroll;
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

    function paint(scene, progress) {
        const unchanged = scene.painted !== null
            && Math.abs(scene.painted - progress) <= PROGRESS_EPSILON;
        if (!forcePaint && unchanged) return;
        paintScene(scene, progress, rtl, reduced.matches);
        scene.texts.forEach((item) => paintText(item, progress, rtl));
        scene.painted = progress;
    }

    function render(time) {
        frame = 0;
        if (destroyed || !active || document.hidden) return;
        if (layoutDirty) refreshLayout();

        targetScroll = readScroll();
        const delta = lastTime ? time - lastTime : FRAME_MS;
        lastTime = time;

        if (snapNext || reduced.matches) currentScroll = targetScroll;
        else {
            currentScroll += (
                targetScroll - currentScroll
            ) * frameBlend(delta);
        }

        const settled = Math.abs(
            targetScroll - currentScroll,
        ) <= SCROLL_EPSILON;
        if (settled) currentScroll = targetScroll;

        scenes.forEach((scene) => {
            const progress = reduced.matches
                ? 1
                : sceneProgress(scene, currentScroll, viewportHeight);
            paint(scene, progress);
        });

        snapNext = false;
        forcePaint = false;
        if (!settled) requestRender();
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
        currentScroll = readScroll();
        targetScroll = currentScroll;
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
        texts.forEach(clearPaint);
        scenes.forEach((scene) => {
            scene.arts.forEach((art) => art.style.removeProperty('transform'));
            scene.element.style.removeProperty('--scene-progress');
            scene.element.style.removeProperty('--scene-transition');
            scene.element.style.removeProperty('--scene-color');
            scene.element.style.removeProperty('--next-scene-color');
        });
        cancelFrame();
        observer?.disconnect();
        resizeObserver?.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        reduced.removeEventListener?.('change', onMotionChange);
    };
}
