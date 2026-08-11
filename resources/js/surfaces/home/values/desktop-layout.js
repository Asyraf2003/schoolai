import {
    deckAngle,
    exitAmount,
    fanAngle,
    fanArc,
    flipAngle,
    flipLocal,
    stackNudge,
    uprightAmount,
} from './desktop-keyframes.js';
import { mix, phase } from './motion.js';

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

function hiddenPose(index, geometry, center) {
    return pose(
        center.x + stackNudge(index, geometry.cardWidth),
        center.y + geometry.cardHeight * 0.03,
        -index * 14,
        deckAngle(index),
        0.92,
    );
}

function deckPose(index, geometry, center) {
    return pose(
        center.x + stackNudge(index, geometry.cardWidth),
        center.y + geometry.cardHeight * 0.02 + index * 2,
        -index * 14,
        deckAngle(index),
        0.97,
    );
}

function fanPose(index, geometry, center) {
    return pose(
        center.x * 0.3,
        center.y + fanArc(index, geometry.cardHeight),
        -index * 3,
        fanAngle(index),
        1,
    );
}

function preFlipPose(index) {
    return pose(0, 0, 0, fanAngle(index), 1);
}

export function desktopCardFrame(
    index,
    progress,
    geometry,
    momentum,
) {
    const center = centeredOffsets(index, geometry);
    const hidden = hiddenPose(index, geometry, center);
    const deck = deckPose(index, geometry, center);
    const fan = fanPose(index, geometry, center);
    const preFlip = preFlipPose(index);
    let current = mixPose(
        hidden,
        deck,
        phase(progress, 0, 0.096),
    );

    current = mixPose(current, fan, phase(progress, 0.072, 0.216));
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
