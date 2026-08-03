import { clearPaint, clamp, paintScene, paintText } from './motion-painters.js';
import { prepareStoryText } from './split-text.js';

const EPSILON = 0.0005;
const DAMPING_MS = 150;

function sceneProgress(scene, viewportHeight) {
    const rect = scene.getBoundingClientRect();
    const start = viewportHeight * .55;
    const travel = Math.max(1, rect.height - viewportHeight * .45);
    return clamp((start - rect.top) / travel);
}

function damp(current, target, delta) {
    const blend = 1 - Math.exp(-Math.min(delta, 64) / DAMPING_MS);
    return current + (target - current) * blend;
}

function prepareScenes(root) {
    const elements = Array.from(root.querySelectorAll('[data-story-scene]'));

    return elements.map((element, index) => {
        const next = elements[index + 1];
        const color = element.dataset.storyColor || '#061d4f';
        const nextColor = next?.dataset.storyColor || color;

        element.style.setProperty('--scene-color', color);
        element.style.setProperty('--next-scene-color', nextColor);

        return {
            element,
            motion: element.dataset.storyArtMotion || 'none',
            arts: Array.from(element.querySelectorAll('[data-story-scene-art]')),
            target: 0,
            current: 0,
            painted: null,
        };
    });
}

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const rtl = document.documentElement.dir === 'rtl';
    const scenes = prepareScenes(root);
    const texts = prepareStoryText(root).map((item) => ({
        ...item,
        scene: item.element.closest('[data-story-scene]'),
    }));
    let frame = 0;
    let observer = null;
    let active = true;
    let destroyed = false;
    let needsMeasure = true;
    let forcePaint = true;
    let snapNext = true;
    let lastTime = 0;

    function cancelFrame() {
        if (frame) window.cancelAnimationFrame(frame);
        frame = 0;
        lastTime = 0;
    }

    function measureTargets() {
        needsMeasure = false;
        const viewportHeight = window.innerHeight || 1;

        scenes.forEach((scene) => {
            scene.target = reduced.matches
                ? 1
                : sceneProgress(scene.element, viewportHeight);
        });
    }

    function render(time) {
        frame = 0;
        if (destroyed || !active || document.hidden) return;

        const delta = lastTime ? time - lastTime : 16.67;
        lastTime = time;
        if (needsMeasure) measureTargets();

        const progressByScene = new Map();
        const shouldSnap = snapNext || reduced.matches;
        let moving = false;

        scenes.forEach((scene) => {
            const next = shouldSnap
                ? scene.target
                : damp(scene.current, scene.target, delta);
            const settled = Math.abs(scene.target - next) <= EPSILON;

            scene.current = settled ? scene.target : next;
            moving ||= !settled;
            progressByScene.set(scene.element, scene.current);

            if (
                forcePaint
                || scene.painted === null
                || Math.abs(scene.painted - scene.current) > EPSILON
            ) {
                paintScene(scene, scene.current, rtl, reduced.matches);
                scene.painted = scene.current;
            }
        });

        texts.forEach((item) => {
            paintText(item, progressByScene.get(item.scene) ?? 1, rtl);
        });

        snapNext = false;
        forcePaint = false;
        if (moving || needsMeasure) requestRender();
        else lastTime = 0;
    }

    function requestRender() {
        if (!frame && !destroyed && active && !document.hidden) {
            frame = window.requestAnimationFrame(render);
        }
    }

    function requestMeasure(repaint = false) {
        needsMeasure = true;
        forcePaint ||= repaint;
        requestRender();
    }

    function onScroll() {
        requestMeasure();
    }

    function onResize() {
        requestMeasure(true);
    }

    function onVisibility() {
        if (document.hidden) {
            cancelFrame();
            return;
        }
        snapNext = true;
        requestMeasure(true);
    }

    function onMotionChange() {
        snapNext = true;
        requestMeasure(true);
    }

    function onIntersection(entries) {
        const nextActive = entries.some((entry) => entry.isIntersecting);
        if (nextActive === active) return;

        active = nextActive;
        cancelFrame();
        if (!active) return;

        snapNext = true;
        requestMeasure(true);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    document.addEventListener('visibilitychange', onVisibility);
    reduced.addEventListener?.('change', onMotionChange);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '120% 0px 120% 0px',
            threshold: 0.01,
        });
        observer.observe(root);
    }

    root.classList.add('is-story-ready');
    requestRender();

    return function destroy() {
        destroyed = true;
        active = false;
        root.classList.remove('is-story-ready');
        texts.forEach(clearPaint);
        scenes.forEach((scene) => {
            scene.arts.forEach((art) => {
                art.style.removeProperty('transform');
            });
            scene.element.style.removeProperty('--scene-progress');
            scene.element.style.removeProperty('--scene-transition');
            scene.element.style.removeProperty('--scene-color');
            scene.element.style.removeProperty('--next-scene-color');
        });
        cancelFrame();
        observer?.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        reduced.removeEventListener?.('change', onMotionChange);
    };
}
