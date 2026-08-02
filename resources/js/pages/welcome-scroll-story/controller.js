import { prepareStoryText } from './split-text.js';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

function sceneProgress(scene, viewportHeight) {
    const rect = scene.getBoundingClientRect();
    const start = viewportHeight * .55;
    const travel = Math.max(1, rect.height - viewportHeight * .45);
    return clamp((start - rect.top) / travel);
}

function unitProgress(progress, index, total) {
    const spread = total > 24 ? .84 : .72;
    const start = total > 1 ? (index / (total - 1)) * spread : 0;
    return clamp((progress - start) / Math.max(.16, 1 - spread));
}

function paintText(item, progress) {
    if (Math.abs((item.lastProgress ?? -1) - progress) < .0005) return;
    item.lastProgress = progress;
    const total = item.units.length;

    item.units.forEach((unit, index) => {
        const value = unitProgress(progress, index, total);
        unit.style.transform = `scaleY(${Math.max(.001, value)})`;
    });
}

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const texts = prepareStoryText(root);
    const scenes = Array.from(root.querySelectorAll('[data-story-scene]'));
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
                : sceneProgress(scene, viewportHeight);
            progressByScene.set(scene, progress);
            scene.style.setProperty('--scene-progress', progress.toFixed(4));
        });

        texts.forEach((item) => {
            const scene = item.element.closest('[data-story-scene]');
            paintText(item, progressByScene.get(scene) ?? 1);
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
        texts.forEach((item) => {
            delete item.lastProgress;
            item.units.forEach((unit) => unit.style.removeProperty('transform'));
        });
        scenes.forEach((scene) => scene.style.removeProperty('--scene-progress'));
        if (frame) window.cancelAnimationFrame(frame);
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        reduced.removeEventListener?.('change', requestRender);
    };
}
