import {
    exitAmount,
    fanAngle,
    fanArc,
    flipAngle,
    flipLocal,
    uprightAmount,
} from './desktop-keyframes.js';
import { mix, phase } from './motion.js';

const SPLIT_START_PROGRESS = 0.006;
const STACK_REVEAL_PROGRESS = 0.04;
const CENTER_COLLISION_PROGRESS = 0.096;
const FAN_PEAK_PROGRESS = 0.118;
const WIDEN_SETTLE_PROGRESS = 0.228;
const CARD_SCALE = 1;

function pose(x, y, z, rz, scale, ry = 180) {
    return { x, y, z, rz, scale, ry, floatY: 0 };
}

function mixPose(from, to, amount) {
    return {
        x: mix(from.x, to.x, amount),
        y: mix(from.y, to.y, amount),
        z: mix(from.z, to.z, amount),
        rz: mix(from.rz, to.rz, amount),
        scale: mix(from.scale, to.scale, amount),
        ry: mix(from.ry, to.ry, amount),
        floatY: 0,
    };
}

function centeredOffsets(index, geometry) {
    const slot = geometry.slots[index];
    return {
        x: geometry.stageCenterX - slot.centerX,
        y: geometry.stageCenterY - slot.centerY,
    };
}

function hiddenPose(geometry, center) {
    return pose(
        center.x,
        center.y + geometry.cardHeight * 0.012,
        0,
        0,
        CARD_SCALE,
    );
}

function deckPose(geometry, center) {
    return pose(
        center.x,
        center.y + geometry.cardHeight * 0.008,
        0,
        0,
        CARD_SCALE,
    );
}

function splitPose(index, geometry, center) {
    const centeredIndex = index - 1.5;
    return pose(
        center.x + centeredIndex * geometry.cardWidth * 0.052,
        center.y,
        0,
        0,
        CARD_SCALE,
    );
}

function fanPose(index, geometry, center) {
    return pose(
        center.x * 0.3,
        center.y + fanArc(index, geometry.cardHeight),
        -index * 3,
        fanAngle(index),
        CARD_SCALE,
    );
}

function preFlipPose(index) {
    return pose(0, 0, 0, fanAngle(index), CARD_SCALE);
}

export function desktopCardFrame(
    index,
    progress,
    geometry,
    momentum,
) {
    const center = centeredOffsets(index, geometry);
    const hidden = hiddenPose(geometry, center);
    const deck = deckPose(geometry, center);
    const split = splitPose(index, geometry, center);
    const fan = fanPose(index, geometry, center);
    const preFlip = preFlipPose(index);
    let current = mixPose(
        hidden,
        deck,
        phase(progress, 0, CENTER_COLLISION_PROGRESS),
    );

    // Tahap 1: segera setelah card mulai menjauh dari heading, buka stack
    // sedikit saja agar empat kartu terbaca tanpa gelombang/rotasi.
    current = mixPose(
        current,
        split,
        phase(progress, SPLIT_START_PROGRESS, STACK_REVEAL_PROGRESS),
    );

    // Tahap 2: mulai fan saat heading masih terlihat. Gelombang, pelebaran,
    // dan flip kemudian berjalan overlap, bukan menunggu card selesai naik.
    current = mixPose(
        current,
        fan,
        phase(progress, STACK_REVEAL_PROGRESS, FAN_PEAK_PROGRESS),
    );
    current = mixPose(
        current,
        preFlip,
        phase(progress, CENTER_COLLISION_PROGRESS, WIDEN_SETTLE_PROGRESS),
    );

    const localFlip = flipLocal(index, progress);
    const upright = uprightAmount(localFlip);
    const leave = exitAmount(progress);
    current.ry = flipAngle(localFlip);
    current.rz = mix(current.rz, 0, upright);
    current.x = mix(current.x, 0, upright);
    current.y = mix(current.y, 0, upright)
        - leave * (geometry.stageHeight + geometry.cardHeight * 0.72);
    current.z = mix(current.z, 0, upright);
    current.floatY = 0;

    return current;
}
