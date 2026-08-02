import { prepareStoryText } from './split-text.js';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

function sceneProgress(scene, viewportHeight) {
    const rect = scene.getBoundingClientRect();
    const start = viewportHeight * .55;
    const travel = Math.max(1, rect.height - viewportHeight * .45);
    return clamp((start - rect.top) / travel);
}

function rootProgress(root, viewportHeight) {
    const rect = root.getBoundingClientRect();
    const travel = Math.max(1, rect.height - viewportHeight);
    return clamp(-rect.top / travel);
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

function paintBackground(layers, progress) {
    const centered = progress - .5;
    const width = window.innerWidth || 1;
    const height = window.innerHeight || 1;
    const direction = document.documentElement.dir === 'rtl' ? -1 : 1;

    layers.forEach((layer, index) => {
        const depth = Number(layer.dataset.storyDepth || index + 1);
        const horizontalSign = index % 2 ? -1 : 1;
        const verticalSign = index < 2 ? 1 : -1;
        const x = centered * width * (.012 + depth * .01) * horizontalSign * direction;
        const y = centered * height * (.018 + depth * .008) * verticalSign;
        const rotation = centered * (depth % 2 ? 8 : -7);
        const scale = 1 + Math.abs(centered) * .055 + depth * .008;

        layer.style.transform = `translate3d(${x}px, ${y}px, 0) rotate(${rotation}deg) scale(${scale})`;
    });
}

export function createStoryController(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const texts = prepareStoryText(root);
    const scenes = Array.from(root.querySelectorAll('[data-story-scene]'));
    const backgroundLayers = Array.from(root.querySelectorAll('[data-story-bg-layer]'));
    let frame = 0;
    let destroyed = false;
    let lastBackgroundProgress = -1;

    function render() {
        frame = 0;
        if (destroyed) return;

        const viewportHeight = window.innerHeight || 1;
        const progressByScene = new Map();

        scenes.forEach((scene) => {
            const progress = reduced.matches ? 1 : sceneProgress(scene, viewportHeight);
            progressByScene.set(scene, progress);
            scene.style.setProperty('--scene-progress', progress.toFixed(4));
        });

        texts.forEach((item) => {
            const scene = item.element.closest('[data-story-scene]');
            paintText(item, progressByScene.get(scene) ?? 1);
        });

        const backgroundProgress = reduced.matches ? .5 : rootProgress(root, viewportHeight);
        if (Math.abs(lastBackgroundProgress - backgroundProgress) >= .0005) {
            lastBackgroundProgress = backgroundProgress;
            paintBackground(backgroundLayers, backgroundProgress);
        }
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
        backgroundLayers.forEach((layer) => layer.style.removeProperty('transform'));
        scenes.forEach((scene) => scene.style.removeProperty('--scene-progress'));
        if (frame) window.cancelAnimationFrame(frame);
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', requestRender);
        reduced.removeEventListener?.('change', requestRender);
    };
}
