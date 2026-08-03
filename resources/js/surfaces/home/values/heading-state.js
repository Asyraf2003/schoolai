import { clamp, smooth } from './motion.js';

const EPSILON = 0.00005;
const ENTRY_TRIGGER_RATIO = 0.72;
const REVEAL_DURATION_MS = 1600;

function resetForEntry(state) {
    state.phase = 'idle';
    state.startedAt = 0;
    state.reveal = 0;
    state.shifted = false;
    state.instant = false;
}

function resolveWithoutEntry(state) {
    state.phase = 'revealed';
    state.reveal = 1;
    state.instant = true;
}

export function createHeadingState(progress = 0) {
    const resolved = progress > EPSILON;

    return {
        phase: resolved ? 'revealed' : 'idle',
        startedAt: 0,
        previousTop: null,
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
    time,
    desktop,
) {
    const triggerTop = viewportHeight * ENTRY_TRIGGER_RATIO;
    const beforeSection = target <= EPSILON && rootTop > triggerTop;
    const firstSample = state.previousTop === null;
    const movingDown = firstSample || rootTop < state.previousTop - 0.5;
    const enteringFromTop = rootTop <= triggerTop
        && rootTop > -viewportHeight * 0.15
        && movingDown;

    if (beforeSection) {
        resetForEntry(state);
    } else if (state.phase === 'idle') {
        if (enteringFromTop) {
            state.phase = 'revealing';
            state.startedAt = time;
            state.instant = false;
        } else {
            resolveWithoutEntry(state);
        }
    }

    if (state.phase === 'revealing') {
        const elapsed = Math.max(0, time - state.startedAt);
        state.reveal = smooth(clamp(elapsed / REVEAL_DURATION_MS));

        if (state.reveal >= 0.999) {
            state.reveal = 1;
            state.phase = 'revealed';
        }
    }

    state.shifted = desktop && state.phase === 'revealed';
    state.previousTop = rootTop;

    return {
        reveal: state.reveal,
        shifted: state.shifted,
        instant: state.instant,
        settled: state.phase !== 'revealing',
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
