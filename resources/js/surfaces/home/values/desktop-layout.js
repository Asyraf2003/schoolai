import {
    deckAngle,
    fanAngle,
    fanArc,
    flipAngle,
    flipLocal,
    stackNudge,
    uprightAmount,
} from './desktop-keyframes.js';
import { mix, phase } from './motion.js';

function pose(x, y, z, rz, scale, opacity, ry = 180) {
    return { x, y, z, rz, scale, opacity, ry };
}

function mixPose(from, to, amount) {
    return {
        x: mix(from.x, to.x, amount),
        y: mix(from.y, to.y, amount),
        z: mix(from.z, to.z, amount),
        rz: mix(from.rz, to.rz, amount),
        scale: mix(from.scale, to.scale, amount),
        opacity: mix(from.opacity, to.opacity, amount),
        ry: mix(from.ry, to.ry, amount),
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
        center.y + geometry.cardHeight * 0.34,
        -index * 14,
        deckAngle(index),
        0.9,
        0,
    );
}

function deckPose(index, geometry, center) {
    return pose(
        center.x + stackNudge(index, geometry.cardWidth),
        center.y + geometry.cardHeight * 0.14 + index * 2,
        -index * 14,
        deckAngle(index),
        0.96,
        1,
    );
}

function fanPose(index, geometry, center) {
    return pose(
        center.x * 0.3,
        center.y + fanArc(index, geometry.cardHeight),
        -index * 3,
        fanAngle(index),
        1,
        1,
    );
}

function preFlipPose(index) {
    return pose(0, 0, 0, fanAngle(index), 1, 1);
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
    const revealStart = 0.05 + index * 0.012;
    const revealEnd = 0.14 + index * 0.012;
    let current = mixPose(
        hidden,
        deck,
        phase(progress, revealStart, revealEnd),
    );

    current = mixPose(current, fan, phase(progress, 0.18, 0.38));
    current = mixPose(current, preFlip, phase(progress, 0.34, 0.46));

    const localFlip = flipLocal(index, progress);
    const upright = uprightAmount(localFlip);
    current.ry = flipAngle(localFlip);
    current.rz = mix(current.rz, 0, upright);
    current.x = mix(current.x, 0, upright);
    current.y = mix(current.y, 0, upright)
        + momentum * geometry.cardHeight * 0.012;
    current.z = mix(current.z, 0, upright);

    return current;
}
