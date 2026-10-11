export const FRAME_MS = 1000 / 60;

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

export function updateScrollMotion(state, target) {
    resetScrollMotion(state, clamp(target));
    return { visual: state.current, momentum: 0, settled: true };
}
