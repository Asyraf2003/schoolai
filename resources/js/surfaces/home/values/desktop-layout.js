import { measuredDesktopPose } from './desktop-keyframes.js';
import { clamp, mix, phase } from './motion.js';

function pose(
    x = 0,
    y = 0,
    z = 0,
    rz = 0,
    scale = 1,
    opacity = 1,
    ry = 180,
) {
    return { x, y, z, rx: 0, ry, rz, scale, opacity };
}

function mixPose(from, to, amount) {
    return {
        x: mix(from.x, to.x, amount),
        y: mix(from.y, to.y, amount),
        z: mix(from.z, to.z, amount),
        rx: mix(from.rx, to.rx, amount),
        ry: mix(from.ry, to.ry, amount),
        rz: mix(from.rz, to.rz, amount),
        scale: mix(from.scale, to.scale, amount),
        opacity: mix(from.opacity, to.opacity, amount),
    };
}

function leadY(geometry) {
    return geometry.viewportHeight * 0.78 - geometry.cardHeight * 0.5;
}

function deckY(geometry) {
    return geometry.viewportHeight * 0.6 - geometry.cardHeight * 0.5;
}

function centerY(geometry) {
    return geometry.viewportHeight * 0.56 - geometry.cardHeight * 0.5;
}

function leadPose(index, geometry) {
    return pose(
        0,
        leadY(geometry),
        -index * 24,
        0,
        0.96,
        index === 0 ? 1 : 0,
    );
}

function deckPose(index, geometry) {
    return pose(
        (index - 1.5) * geometry.cardWidth * 0.038,
        deckY(geometry) + index * 4,
        -index * 22,
        (index - 1.5) * 1.9,
        0.96,
        1,
    );
}

export function desktopCardFrame(
    index,
    progress,
    geometry,
    momentum,
) {
    const lead = leadPose(index, geometry);
    const hidden = {
        ...lead,
        y: lead.y + geometry.viewportHeight * 0.16,
        scale: lead.scale * 0.88,
        opacity: 0,
    };
    const deck = deckPose(index, geometry);
    const measuredAmount = clamp((progress - 0.38) / 0.5);
    const measured = measuredDesktopPose(
        index,
        measuredAmount,
        geometry,
        centerY(geometry),
    );
    const fan = measuredDesktopPose(index, 0, geometry, centerY(geometry));
    let current = mixPose(hidden, lead, phase(progress, 0.06, 0.16));

    current = mixPose(current, deck, phase(progress, 0.18, 0.29));
    current = mixPose(current, fan, phase(progress, 0.28, 0.38));
    current = mixPose(current, measured, phase(progress, 0.36, 0.4));
    current.y += momentum * 6;

    return current;
}
