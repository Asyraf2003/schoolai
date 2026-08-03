import { phase } from './motion.js';

const EPSILON = 0.00005;
const REVEAL_START = 0.018;
const REVEAL_END = 0.13;
const LIFT_TRIGGER = 0.24;

export function createHeadingState(progress = 0) {
    const open = progress >= REVEAL_END;

    return {
        previous: progress,
        reveal: open ? 1 : 0,
        shifted: open,
        lifted: open && progress >= LIFT_TRIGGER,
        instant: open,
    };
}

export function updateHeadingState(
    state,
    target,
    rootTop,
    viewportHeight,
) {
    const movingForward = target > state.previous + EPSILON;
    const beforeSection = target <= EPSILON
        && rootTop > Math.min(96, viewportHeight * 0.12);

    if (beforeSection) {
        state.reveal = 0;
        state.shifted = false;
        state.lifted = false;
        state.instant = false;
    } else if (movingForward) {
        state.reveal = Math.max(
            state.reveal,
            phase(target, REVEAL_START, REVEAL_END),
        );

        if (state.reveal >= 0.999) state.shifted = true;
        if (target >= LIFT_TRIGGER) state.lifted = true;
    } else if (state.reveal > 0 || target >= REVEAL_END) {
        state.reveal = 1;
        state.shifted = true;
        state.lifted = state.lifted || target >= LIFT_TRIGGER;
        state.instant = true;
    }

    state.previous = target;

    return {
        reveal: state.reveal,
        shifted: state.shifted,
        lifted: state.lifted,
        instant: state.instant,
    };
}

export function syncHeadingClasses(root, state) {
    root.classList.toggle('is-values-heading-shifted', state.shifted);
    root.classList.toggle('is-values-heading-lifted', state.lifted);
    root.classList.toggle('is-values-heading-instant', state.instant);
}

export function clearHeadingClasses(root) {
    root.classList.remove(
        'is-values-heading-shifted',
        'is-values-heading-lifted',
        'is-values-heading-instant',
    );
}
