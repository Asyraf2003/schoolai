import { clamp } from './motion-painters.js';

export const FRAME_MS = 1000 / 60;
export const SCROLL_EPSILON = 0.1;
export const PROGRESS_EPSILON = 0.0005;

const SCROLL_LERP = 0.08;

export const readScroll = () => (
    window.scrollY || window.pageYOffset || 0
);

export function frameBlend(delta) {
    return 1 - Math.pow(
        1 - SCROLL_LERP,
        Math.min(delta, 64) / FRAME_MS,
    );
}

export function measureScenes(scenes) {
    const pageY = readScroll();

    scenes.forEach((scene) => {
        const rect = scene.element.getBoundingClientRect();
        scene.top = rect.top + pageY;
        scene.height = Math.max(1, rect.height);
    });
}

export function sceneProgress(scene, scrollY, viewportHeight) {
    const start = viewportHeight * 0.55;
    const travel = Math.max(
        1,
        scene.height - viewportHeight * 0.45,
    );

    return clamp((start - (scene.top - scrollY)) / travel);
}
