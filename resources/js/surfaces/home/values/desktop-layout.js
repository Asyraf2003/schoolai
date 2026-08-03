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

function pose(x, y, z, rz, scale, ry = 180) {
    return { x, y, z, rz, scale, ry };
}

function mixPose(from, to, amount) {
    return {
        x: mix(from.x, to.x, amount),
        y: mix(from.y, to.y, amount),
        z: mix(from.z, to.z, amount),
        rz: mix(from.rz, to.rz, amount),
        scale: mix(from.scale, to.scale, amount),
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
        center.y + geometry.cardHeight * 0.62,
        -index * 14,
        deckAngle(index),
        0.92,
    );
}

function deckPose(index, geometry, center) {
    return pose(
        center.x + stackNudge(index, geometry.cardWidth),
        center.y + geometry.cardHeight * 0.18 + index * 2,
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
        phase(progress, 0, 0.16),
    );

    current = mixPose(current, fan, phase(progress, 0.12, 0.36));
    current = mixPose(current, preFlip, phase(progress, 0.30, 0.42));

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
