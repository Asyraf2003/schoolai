import { clamp, easeOutCubic } from './motion.js';

const EPSILON = 0.00005;
const ENTRY_TRIGGER_RATIO = 1.08;
const RESET_RATIO = 1.14;
const REVEAL_DURATION_MS = 900;

function hideAndArm(state) {
    state.phase = 'idle';
    state.startedAt = 0;
    state.reveal = 0;
    state.shifted = false;
    state.instant = false;
    state.armed = true;
}

function resolveWithoutEntry(state) {
    state.phase = 'revealed';
    state.reveal = 1;
    state.instant = true;
    state.armed = false;
}

function startDownwardReveal(state, time) {
    state.phase = 'revealing';
    state.startedAt = time;
    state.reveal = 0;
    state.instant = false;
    state.armed = false;
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
        armed: !resolved,
    };
}

export function updateHeadingState(
    state,
    storyProgress,
    headingTop,
    viewportHeight,
    time,
    shiftAllowed,
) {
    const triggerTop = viewportHeight * ENTRY_TRIGGER_RATIO;
    const resetTop = viewportHeight * RESET_RATIO;
    const firstSample = state.previousTop === null;
    const movingDown = !firstSample && headingTop < state.previousTop - 0.5;
    const movingUp = !firstSample && headingTop > state.previousTop + 0.5;

    if (firstSample) {
        if (headingTop > resetTop) {
            hideAndArm(state);
        } else if (storyProgress > EPSILON) {
            resolveWithoutEntry(state);
        } else {
            startDownwardReveal(state, time);
        }
    } else if (movingUp) {
        if (headingTop > resetTop) {
            hideAndArm(state);
        } else if (state.phase !== 'revealed') {
            resolveWithoutEntry(state);
        }
    }

    const crossedDownward = !firstSample
        && state.armed
        && movingDown
        && state.previousTop > triggerTop
        && headingTop <= triggerTop;

    if (crossedDownward) startDownwardReveal(state, time);

    if (state.phase === 'revealing') {
        if (movingUp) {
            resolveWithoutEntry(state);
        } else {
            const elapsed = Math.max(0, time - state.startedAt);
            state.reveal = easeOutCubic(elapsed / REVEAL_DURATION_MS);

            if (state.reveal >= 0.999) {
                state.reveal = 1;
                state.phase = 'revealed';
                state.instant = false;
            }
        }
    }

    state.reveal = clamp(state.reveal);
    state.shifted = shiftAllowed && state.phase === 'revealed';
    state.previousTop = headingTop;

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
