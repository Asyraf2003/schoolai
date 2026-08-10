import { clamp, easeOutCubic } from './motion.js';

const EPSILON = 0.00005;
const ENTRY_TRIGGER_RATIO = 1.08;
const REVEAL_DURATION_MS = 900;
const FAST_RESOLVE_PROGRESS = 0.18;

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
    storyProgress,
    timelineTop,
    viewportHeight,
    time,
    shiftAllowed,
) {
    const triggerTop = viewportHeight * ENTRY_TRIGGER_RATIO;
    const firstSample = state.previousTop === null;
    const movingDown = firstSample || timelineTop < state.previousTop - 0.5;
    const movingUp = !firstSample && timelineTop > state.previousTop + 0.5;
    const enteringFromAbove = timelineTop <= triggerTop
        && storyProgress < FAST_RESOLVE_PROGRESS
        && movingDown;

    if (state.phase === 'idle') {
        if (storyProgress >= FAST_RESOLVE_PROGRESS
            || (movingUp && timelineTop <= triggerTop)) {
            resolveWithoutEntry(state);
        } else if (enteringFromAbove) {
            state.phase = 'revealing';
            state.startedAt = time;
            state.instant = false;
        }
    }

    if (state.phase === 'revealing') {
        if (movingUp) {
            resolveWithoutEntry(state);
        } else {
            const elapsed = Math.max(0, time - state.startedAt);
            state.reveal = easeOutCubic(elapsed / REVEAL_DURATION_MS);

            if (state.reveal >= 0.999
                || storyProgress >= FAST_RESOLVE_PROGRESS) {
                state.reveal = 1;
                state.phase = 'revealed';
                state.instant = false;
            }
        }
    }

    state.reveal = clamp(state.reveal);
    state.shifted = shiftAllowed && state.phase === 'revealed';
    state.previousTop = timelineTop;

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
