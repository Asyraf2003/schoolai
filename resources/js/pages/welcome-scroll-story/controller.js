import { clearPaint, clamp, paintScene, paintText } from './motion-painters.js';
import { prepareStoryText } from './split-text.js';

function sceneProgress(scene, viewportHeight) {
    const rect = scene.getBoundingClientRect();
    const start = viewportHeight * .55;
    const travel = Math.max(1, rect.height - viewportHeight * .45);
    return clamp((start - rect.top) / travel);
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
        };
    });
}

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const rtl = document.documentElement.dir === 'rtl';
    const texts = prepareStoryText(root);
    const scenes = prepareScenes(root);
    let frame = 0;
    let destroyed = false;

    function render() {
        frame = 0;
        if (destroyed) return;

        const viewportHeight = window.innerHeight || 1;
        const progressByScene = new Map();

        scenes.forEach((scene) => {
            const progress = reduced.matches
                ? 1
                : sceneProgress(scene.element, viewportHeight);
            progressByScene.set(scene.element, progress);
            paintScene(scene, progress, rtl, reduced.matches);
        });

        texts.forEach((item) => {
            const scene = item.element.closest('[data-story-scene]');
            paintText(item, progressByScene.get(scene) ?? 1, rtl);
        });
    }

    function requestRender() {
        if (!frame && !destroyed) frame = window.requestAnimationFrame(render);
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender, { passive: true });
    reduced.addEventListener?.('change', requestRender);
    root.classList.add('is-story-ready');
    requestRender();

    return function destroy() {
        destroyed = true;
        root.classList.remove('is-story-ready');
        texts.forEach(clearPaint);
        scenes.forEach((scene) => {
            scene.arts.forEach((art) => art.style.removeProperty('transform'));
            scene.element.style.removeProperty('--scene-progress');
            scene.element.style.removeProperty('--scene-transition');
            scene.element.style.removeProperty('--scene-color');
            scene.element.style.removeProperty('--next-scene-color');
        });
        if (frame) window.cancelAnimationFrame(frame);
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        reduced.removeEventListener?.('change', requestRender);
    };
}
