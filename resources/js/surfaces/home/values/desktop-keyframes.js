import { clamp, easeOutCubic, mix, phase } from './motion.js';

const FAN_ANGLES = [-13, -4.5, 4.5, 13];
const DECK_ANGLES = [-1.8, -.6, .6, 1.8];
const FLIP_START = 0.38;
const FLIP_DURATION = 0.27;
const FLIP_STAGGER = 0.02;
const FLIP_FRONT_PASS = 0.84;
const FLIP_OVERSHOOT = -18;
const BOUNCE_START = 0.868;
const BOUNCE_END = 0.936;
const BOUNCE_STAGGER = 0.004;
const EXIT_START = 0.95;

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

export function bounceOffset(index, progress, cardHeight) {
    const start = BOUNCE_START + index * BOUNCE_STAGGER;
    const end = BOUNCE_END + index * BOUNCE_STAGGER;
    if (progress <= start || progress >= end) return 0;

    const local = clamp((progress - start) / (end - start));
    const amplitude = cardHeight * 0.026 * (1 - local * 0.35);
    return -Math.sin(local * Math.PI * 6) * amplitude;
}

export function exitAmount(progress) {
    return easeOutCubic(phase(progress, EXIT_START, 1));
}
