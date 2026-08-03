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

function cardTopAt(centerRatio, geometry) {
    return geometry.viewportHeight * centerRatio - geometry.cardHeight * 0.5;
}

function leadPose(index, geometry) {
    return pose(
        0,
        cardTopAt(0.86, geometry),
        -index * 24,
        0,
        0.96,
        index === 0 ? 1 : 0,
    );
}

function deckPose(index, geometry) {
    return pose(
        (index - 1.5) * geometry.cardWidth * 0.038,
        cardTopAt(0.63, geometry) + index * 4,
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
        y: lead.y + geometry.viewportHeight * 0.14,
        scale: lead.scale * 0.88,
        opacity: 0,
    };
    const deck = deckPose(index, geometry);
    const baseY = cardTopAt(0.535, geometry);
    const measuredAmount = clamp((progress - 0.34) / 0.52);
    const measured = measuredDesktopPose(
        index,
        measuredAmount,
        geometry,
        baseY,
    );
    const fan = measuredDesktopPose(index, 0, geometry, baseY);
    let current = mixPose(hidden, lead, phase(progress, 0.03, 0.12));

    current = mixPose(current, deck, phase(progress, 0.12, 0.24));
    current = mixPose(current, fan, phase(progress, 0.22, 0.34));
    current = mixPose(current, measured, phase(progress, 0.32, 0.36));
    current.y += momentum * 6;

    return current;
}
