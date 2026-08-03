import { clamp } from './motion-painters.js';

export const FRAME_MS = 1000 / 60;
export const SCROLL_EPSILON = 0.1;
export const PROGRESS_EPSILON = 0.00005;
export const MOMENTUM_EPSILON = 0.002;

const SCROLL_LERP = 0.08;
const VELOCITY_LERP = 0.12;
const VELOCITY_MAX = 1.5;
const VELOCITY_EPSILON = 0.001;

export const readScroll = () => (
    window.scrollY || window.pageYOffset || 0
);

function adjustedBlend(amount, delta) {
    return 1 - Math.pow(
        1 - amount,
        Math.min(delta, 64) / FRAME_MS,
    );
}

export function frameBlend(delta) {
    return adjustedBlend(SCROLL_LERP, delta);
}

export function createScrollMotion(value = readScroll()) {
    return {
        current: value,
        previous: value,
        velocity: 0,
    };
}

export function resetScrollMotion(state, value = readScroll()) {
    state.current = value;
    state.previous = value;
    state.velocity = 0;
}

export function updateScrollMotion(state, target, delta, snap = false) {
    if (snap) resetScrollMotion(state, target);
    else {
        state.current += (
            target - state.current
        ) * frameBlend(delta);

        const rawVelocity = state.current - state.previous;
        state.velocity += (
            rawVelocity - state.velocity
        ) * adjustedBlend(VELOCITY_LERP, delta);
        state.velocity = clamp(
            state.velocity,
            -VELOCITY_MAX,
            VELOCITY_MAX,
        );

        if (Math.abs(state.velocity) < VELOCITY_EPSILON) {
            state.velocity = 0;
        }

        if (Math.abs(target - state.current) <= SCROLL_EPSILON) {
            state.current = target;
        }
        state.previous = state.current;
    }

    const positionSettled = Math.abs(
        target - state.current,
    ) <= SCROLL_EPSILON;

    return {
        current: state.current,
        momentum: clamp(state.velocity / VELOCITY_MAX, -1, 1),
        settled: positionSettled && state.velocity === 0,
    };
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
