export const FRAME_MS = 1000 / 60;

const POSITION_BLEND = 0.075;
const VELOCITY_BLEND = 0.08;
const POSITION_EPSILON = 0.00002;
const VELOCITY_EPSILON = 0.000006;
const VELOCITY_MAX = 0.012;
const VELOCITY_COAST = 5;

export const clamp = (value, min = 0, max = 1) => (
    Math.min(max, Math.max(min, value))
);

export const mix = (from, to, amount) => from + (to - from) * amount;

export function smooth(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

export function phase(progress, start, end) {
    return smooth((progress - start) / Math.max(0.0001, end - start));
}

function adjustedBlend(amount, delta) {
    return 1 - Math.pow(
        1 - amount,
        Math.min(delta, 64) / FRAME_MS,
    );
}

export function readStoryProgress(root, viewportHeight) {
    const rect = root.getBoundingClientRect();
    const travel = Math.max(1, root.offsetHeight - viewportHeight);
    return clamp(-rect.top / travel);
}

export function createScrollMotion(value = 0) {
    return {
        current: value,
        previous: value,
        velocity: 0,
    };
}

export function resetScrollMotion(state, value) {
    state.current = value;
    state.previous = value;
    state.velocity = 0;
}

export function updateScrollMotion(state, target, delta, snap = false) {
    if (snap) resetScrollMotion(state, target);
    else {
        state.current += (
            target - state.current
        ) * adjustedBlend(POSITION_BLEND, delta);

        const rawVelocity = state.current - state.previous;
        state.velocity += (
            rawVelocity - state.velocity
        ) * adjustedBlend(VELOCITY_BLEND, delta);
        state.velocity = clamp(
            state.velocity,
            -VELOCITY_MAX,
            VELOCITY_MAX,
        );

        if (Math.abs(target - state.current) <= POSITION_EPSILON) {
            state.current = target;
        }
        if (Math.abs(state.velocity) <= VELOCITY_EPSILON) {
            state.velocity = 0;
        }
        state.previous = state.current;
    }

    const settled = Math.abs(target - state.current) <= POSITION_EPSILON
        && state.velocity === 0;

    return {
        current: state.current,
        visual: clamp(state.current + state.velocity * VELOCITY_COAST),
        momentum: clamp(state.velocity / VELOCITY_MAX, -1, 1),
        settled,
    };
}
