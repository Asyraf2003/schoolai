import { paintScene, paintText } from './motion-painters.js';
import {
    MOMENTUM_EPSILON,
    PROGRESS_EPSILON,
} from './scroll-progress.js';

export function prepareScenes(root) {
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
            paintedMomentum: null,
            paintedBeat: null,
        };
    });
}

export function linkStoryTexts(scenes, texts) {
    const sceneByElement = new Map(
        scenes.map((scene) => [scene.element, scene]),
    );

    texts.forEach((item) => {
        const owner = item.element.closest('[data-story-scene]');
        sceneByElement.get(owner)?.texts.push(item);
    });
}

export function paintStoryScene(scene, state, rtl, reduced, forcePaint) {
    const progressChanged = scene.painted === null
        || Math.abs(scene.painted - state.progress) > PROGRESS_EPSILON;
    const momentumChanged = scene.paintedMomentum === null
        || Math.abs(scene.paintedMomentum - state.momentum) > MOMENTUM_EPSILON;
    const beatChanged = scene.paintedBeat === null
        || Math.abs(scene.paintedBeat - state.beat) > MOMENTUM_EPSILON;

    if (!forcePaint && !progressChanged && !momentumChanged && !beatChanged) {
        return;
    }

    paintScene(scene, state.progress, rtl, reduced);
    const beatOffset = state.beat * 6;

    scene.arts.forEach((art, index) => {
        const inertia = state.momentum * (index + 1) * 4;
        const offset = inertia + beatOffset * (1 + index * 0.08);
        art.style.translate = `0 ${offset.toFixed(2)}px`;
    });
    scene.texts.forEach((item) => {
        item.element.style.translate = `0 ${beatOffset.toFixed(2)}px`;
    });

    if (forcePaint || progressChanged) {
        scene.texts.forEach((item) => {
            delete item.lastProgress;
            paintText(item, state.progress, rtl);
        });
    }

    scene.painted = state.progress;
    scene.paintedMomentum = state.momentum;
    scene.paintedBeat = state.beat;
}

export function clearStoryScenes(scenes) {
    scenes.forEach((scene) => {
        scene.arts.forEach((art) => {
            art.style.removeProperty('transform');
            art.style.removeProperty('translate');
        });
        scene.element.style.removeProperty('--scene-progress');
        scene.element.style.removeProperty('--scene-transition');
        scene.element.style.removeProperty('--scene-color');
        scene.element.style.removeProperty('--next-scene-color');
    });
}
