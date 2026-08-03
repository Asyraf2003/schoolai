import { mix, phase } from './motion.js';

const ROW_ROTATIONS = [-4, -1.25, 1.25, 4];

function pose(x = 0, y = 0, z = 0, rz = 0, scale = 1, opacity = 1) {
    return { x, y, z, rz, scale, opacity };
}

function mixPose(from, to, amount) {
    return {
        x: mix(from.x, to.x, amount),
        y: mix(from.y, to.y, amount),
        z: mix(from.z, to.z, amount),
        rz: mix(from.rz, to.rz, amount),
        scale: mix(from.scale, to.scale, amount),
        opacity: mix(from.opacity, to.opacity, amount),
    };
}

function leadPose(index, geometry) {
    return pose(
        0,
        geometry.viewportHeight * 0.2,
        -index * 24,
        0,
        0.92,
        index === 0 ? 1 : 0,
    );
}

function deckPose(index, geometry) {
    return pose(
        (index - 1.5) * geometry.cardWidth * 0.042,
        geometry.viewportHeight * 0.2 + index * 5,
        -index * 22,
        (index - 1.5) * 2.2,
        0.92,
        1,
    );
}

function rowPose(index, geometry) {
    return pose(
        (index - 1.5) * geometry.cardWidth * 0.88,
        geometry.viewportHeight * 0.1
            + Math.abs(index - 1.5) * 3,
        index * 2,
        ROW_ROTATIONS[index],
        1,
    );
}

function exitPose(index, geometry) {
    const row = rowPose(index, geometry);

    return {
        ...row,
        y: row.y - geometry.viewportHeight * (1.08 + index * 0.025),
        rz: row.rz + (index - 1.5) * 3,
        opacity: 0,
    };
}

function flipAngle(index, progress) {
    const anticipation = phase(progress, 0.4, 0.48);
    const start = 0.48 + (3 - index) * 0.052;
    const drive = phase(progress, start, start + 0.18);
    const settle = phase(progress, start + 0.18, start + 0.25);
    const prepared = mix(180, 195, anticipation);
    const overshot = mix(prepared, -15, drive);

    return mix(overshot, 0, settle);
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
        y: lead.y + geometry.viewportHeight * 0.18,
        scale: lead.scale * 0.88,
        opacity: 0,
    };
    const deck = deckPose(index, geometry);
    const row = rowPose(index, geometry);
    const exit = exitPose(index, geometry);
    let current = mixPose(hidden, lead, phase(progress, 0.07, 0.19));

    current = mixPose(current, deck, phase(progress, 0.22, 0.33));
    current = mixPose(current, row, phase(progress, 0.31, 0.48));
    current = mixPose(
        current,
        exit,
        phase(progress, 0.9 + (3 - index) * 0.008, 1),
    );
    current.y += momentum * (index + 1) * 5;

    return {
        ...current,
        rx: phase(progress, 0.9, 1) * -5,
        ry: flipAngle(index, progress),
    };
}
