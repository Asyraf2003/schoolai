import {
    exitAmount,
    fanAngle,
    fanArc,
    flipAngle,
    flipLocal,
    uprightAmount,
} from './desktop-keyframes.js';
import { mix, phase } from './motion.js';

const CENTER_COLLISION_PROGRESS = 0.096;
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
    const fan = fanPose(index, geometry, center);
    const preFlip = preFlipPose(index);
    let current = mixPose(
        hidden,
        deck,
        phase(progress, 0, CENTER_COLLISION_PROGRESS),
    );

    current = mixPose(
        current,
        fan,
        phase(progress, CENTER_COLLISION_PROGRESS, 0.216),
    );
    current = mixPose(current, preFlip, phase(progress, 0.18, 0.252));

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
