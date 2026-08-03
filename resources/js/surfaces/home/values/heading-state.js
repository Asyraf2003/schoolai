import { phase, smooth } from './motion.js';

const EPSILON = 0.00005;
const REVEAL_START = 0.012;
const REVEAL_END = 0.2;

export function createHeadingState(progress = 0) {
    const resolved = progress > EPSILON;

    return {
        previous: progress,
        reveal: resolved ? 1 : 0,
        shifted: resolved,
        instant: resolved,
    };
}

export function updateHeadingState(
    state,
    target,
    rootTop,
    viewportHeight,
) {
    const delta = target - state.previous;
    const movingForward = delta > EPSILON;
    const movingBackward = delta < -EPSILON;
    const beforeSection = target <= EPSILON
        && rootTop > Math.min(96, viewportHeight * 0.12);

    if (beforeSection) {
        state.reveal = 0;
        state.shifted = false;
        state.instant = false;
    } else if (movingForward) {
        const reveal = smooth(phase(target, REVEAL_START, REVEAL_END));
        state.reveal = Math.max(state.reveal, reveal);

        if (state.reveal >= 0.999) state.shifted = true;
    } else if (movingBackward && state.reveal > 0) {
        state.reveal = 1;
        state.shifted = true;
        state.instant = true;
    }

    state.previous = target;

    return {
        reveal: state.reveal,
        shifted: state.shifted,
        instant: state.instant,
    };
}

export function syncHeadingClasses(root, state) {
    root.classList.toggle('is-values-heading-shifted', state.shifted);
    root.classList.toggle('is-values-heading-instant', state.instant);
}

export function clearHeadingClasses(root) {
    root.classList.remove(
        'is-values-heading-shifted',
        'is-values-heading-instant',
    );
}
