import { clamp, easeOutCubic, mix, phase } from './motion.js';

const FAN_ANGLES = [-13, -4.5, 4.5, 13];
const DECK_ANGLES = [-1.8, -.6, .6, 1.8];
const FLIP_START = 0.44;
const FLIP_DURATION = 0.28;
const FLIP_STAGGER = 0.075;
const FLIP_OVERSHOOT = -8;

function easeInOutCubic(value) {
    const progress = clamp(value);
    return progress < 0.5
        ? 4 * progress * progress * progress
        : 1 - Math.pow(-2 * progress + 2, 3) / 2;
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
    if (local < 0.82) {
        return mix(
            180,
            FLIP_OVERSHOOT,
            easeInOutCubic(local / 0.82),
        );
    }

    return mix(
        FLIP_OVERSHOOT,
        0,
        easeOutCubic((local - 0.82) / 0.18),
    );
}

export function uprightAmount(local) {
    return phase(local, 0.12, 0.88);
}

export function fanArc(index, cardHeight) {
    return Math.abs(index - 1.5) * cardHeight * 0.035;
}

export function stackNudge(index, cardWidth) {
    return (index - 1.5) * cardWidth * 0.018;
}
