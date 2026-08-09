export const FRAME_MS = 1000 / 60;

const SPRING_FREQUENCY = 11.5;
const POSITION_EPSILON = 0.00008;
const VELOCITY_EPSILON = 0.0008;
const MAX_PROGRESS_VELOCITY = 2.8;

export const clamp = (value, min = 0, max = 1) => (
    Math.min(max, Math.max(min, value))
);

export const mix = (from, to, amount) => from + (to - from) * amount;

export function smooth(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

export function easeOutCubic(value) {
    return 1 - Math.pow(1 - clamp(value), 3);
}

export function phase(progress, start, end) {
    return smooth((progress - start) / Math.max(0.0001, end - start));
}

export function readStoryProgress(storyTop, geometry) {
    if (geometry.mode === 4) {
        const travel = Math.max(
            1,
            geometry.timelineHeight - geometry.stageHeight,
        );

        return clamp((geometry.stickyTop - storyTop) / travel);
    }

    const travel = geometry.rootHeight + geometry.viewportHeight;
    return clamp((geometry.viewportHeight - storyTop) / Math.max(1, travel));
}

export function readHandoffProgress(rootTop, viewportHeight) {
    const start = viewportHeight * 1.06;
    const travel = viewportHeight * .78;
    return clamp((start - rootTop) / Math.max(1, travel));
}

export function createScrollMotion(value = 0) {
    return {
        current: value,
        velocity: 0,
    };
}

export function resetScrollMotion(state, value) {
    state.current = value;
    state.velocity = 0;
}

function criticalStep(state, target, deltaSeconds) {
    const displacement = state.current - target;
    const coefficient = state.velocity
        + SPRING_FREQUENCY * displacement;
    const decay = Math.exp(-SPRING_FREQUENCY * deltaSeconds);

    state.current = target
        + (displacement + coefficient * deltaSeconds) * decay;
    state.velocity = (
        state.velocity
        - SPRING_FREQUENCY * coefficient * deltaSeconds
    ) * decay;
}

export function updateScrollMotion(state, target, delta, snap = false) {
    if (snap) resetScrollMotion(state, target);
    else criticalStep(state, target, Math.min(Math.max(delta, 0), 64) / 1000);

    if (Math.abs(target - state.current) <= POSITION_EPSILON
        && Math.abs(state.velocity) <= VELOCITY_EPSILON) {
        resetScrollMotion(state, target);
    }

    state.current = clamp(state.current);
    const settled = state.current === target && state.velocity === 0;

    return {
        visual: state.current,
        momentum: clamp(
            state.velocity / MAX_PROGRESS_VELOCITY,
            -1,
            1,
        ),
        settled,
    };
}
