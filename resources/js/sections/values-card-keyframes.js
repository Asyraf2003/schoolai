import { clamp, easeOutCubic, mix, phase } from './values-card-motion.js';

const FAN_ANGLES = [-13, -4.5, 4.5, 13];
const DECK_ANGLES = [-1.8, -.6, .6, 1.8];
const FLIP_START = 0.052;
const FLIP_DURATION = 0.22;
const FLIP_STAGGER = 0.012;
const FLIP_FRONT_PASS = 0.84;
const FLIP_OVERSHOOT = -18;
const LAST_CARD_INDEX = 3;
const ALL_FRONT_PROGRESS = FLIP_START
    + LAST_CARD_INDEX * FLIP_STAGGER
    + FLIP_DURATION;

const EXIT_START = 0.895;
export const VALUES_HOLD_RETENTION = 0.4;
export const VALUES_FULL_FRONT = ALL_FRONT_PROGRESS;
export const VALUES_OLD_EXIT = EXIT_START;
const COMPRESSED_EXIT = ALL_FRONT_PROGRESS
    + VALUES_HOLD_RETENTION * (EXIT_START - ALL_FRONT_PROGRESS);
const COMPRESSED_DISTANCE = COMPRESSED_EXIT + (1 - EXIT_START);

// Shorten only the fully readable desktop hold. Flip and exit use the
// original pose curves and retain nearly the same real scroll travel.
export function valuesSourceProgress(compactedProgress) {
    const traveled = clamp(compactedProgress) * COMPRESSED_DISTANCE;
    if (traveled <= ALL_FRONT_PROGRESS) return traveled;
    if (traveled <= COMPRESSED_EXIT) {
        return ALL_FRONT_PROGRESS
            + (traveled - ALL_FRONT_PROGRESS) / VALUES_HOLD_RETENTION;
    }
    return EXIT_START + traveled - COMPRESSED_EXIT;
}

// Travel exactly one physical scroll pixel for each pixel of upward exit.
// The sticky viewport supplies the continuation when the track releases.
export function valuesExitScrollOffset(compactedProgress, scrollSpanPx) {
    return Math.max(0, clamp(compactedProgress)
        - valuesCompactedProgress(EXIT_START)) * Math.max(0, scrollSpanPx);
}

// Inverse for matching exact OLD choreography phases in browser regression.
export function valuesCompactedProgress(sourceProgress) {
    const progress = clamp(sourceProgress);
    if (progress <= ALL_FRONT_PROGRESS) return progress / COMPRESSED_DISTANCE;
    if (progress <= EXIT_START) {
        return (ALL_FRONT_PROGRESS
            + (progress - ALL_FRONT_PROGRESS) * VALUES_HOLD_RETENTION) / COMPRESSED_DISTANCE;
    }
    return (COMPRESSED_EXIT + progress - EXIT_START) / COMPRESSED_DISTANCE;
}

function smootherStep(value) {
    const progress = clamp(value);
    return progress * progress * progress
        * (progress * (progress * 6 - 15) + 10);
}

export function fanAngle(index) {
    return FAN_ANGLES[index] ?? 0;
}

export function deckAngle(index) {
    return DECK_ANGLES[index] ?? 0;
}

export function flipLocal(index, progress) {
    const start = FLIP_START + index * FLIP_STAGGER;
    return clamp((progress - start) / FLIP_DURATION);
}

export function flipAngle(local) {
    if (local <= 0) return 180;
    if (local >= 1) return 0;
    if (local < FLIP_FRONT_PASS) {
        return mix(
            180,
            FLIP_OVERSHOOT,
            smootherStep(local / FLIP_FRONT_PASS),
        );
    }

    return mix(
        FLIP_OVERSHOOT,
        0,
        easeOutCubic(
            (local - FLIP_FRONT_PASS) / (1 - FLIP_FRONT_PASS),
        ),
    );
}

export function responsiveFlipAngle(local) {
    return mix(180, 0, smootherStep(local));
}

export function uprightAmount(local) {
    return phase(local, 0.3, 0.74);
}

export function fanArc(index, cardHeight) {
    return Math.abs(index - 1.5) * cardHeight * 0.035;
}

export function stackNudge(index, cardWidth) {
    return (index - 1.5) * cardWidth * 0.018;
}

export function exitAmount(progress) {
    return easeOutCubic(phase(progress, EXIT_START, 1));
}
