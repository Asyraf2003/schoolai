import { clamp, mix, phase } from './motion.js';

const ROW_ROTATIONS = [-5, -1.5, 1.5, 5];

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
        geometry.cardHeight * 0.19,
        -index * 22,
        0,
        geometry.mode === 1 ? 0.78 : 0.86,
        index === 0 ? 1 : 0,
    );
}

function deckPose(index, geometry) {
    const spread = geometry.mode === 1 ? 0.028 : 0.045;
    const scale = geometry.mode === 1 ? 0.78 : 0.86;

    return pose(
        (index - 1.5) * geometry.cardWidth * spread,
        geometry.cardHeight * 0.18 + index * 5,
        -index * 20,
        (index - 1.5) * 2.4,
        scale,
        1,
    );
}

function rowPose(index, geometry) {
    const { mode, cardWidth, cardHeight } = geometry;

    if (mode === 4) {
        return pose(
            (index - 1.5) * cardWidth * 1.06,
            cardHeight * 0.03 + Math.abs(index - 1.5) * 3,
            index * 2,
            ROW_ROTATIONS[index],
            0.92,
        );
    }

    if (mode === 2) {
        const column = index % 2 ? 0.5 : -0.5;
        const row = index < 2 ? -0.5 : 0.5;
        return pose(
            column * cardWidth * 1.08,
            row * cardHeight * 0.73,
            index * 2,
            ROW_ROTATIONS[index] * 0.45,
            0.86,
        );
    }

    return pose(
        (index - 1.5) * cardWidth * 0.25,
        Math.abs(index - 1.5) * cardHeight * 0.045,
        index * 4,
        ROW_ROTATIONS[index] * 1.25,
        0.67,
    );
}

function exitPose(index, geometry) {
    const row = rowPose(index, geometry);
    return {
        ...row,
        x: row.x + (index - 1.5) * geometry.cardWidth * 0.05,
        y: row.y - geometry.viewportHeight * (1.08 + index * 0.025),
        rz: row.rz + (index - 1.5) * 3,
        opacity: 0,
    };
}

function flipProgress(index, progress) {
    const order = 3 - index;
    const start = 0.48 + order * 0.045;
    return phase(progress, start, start + 0.15);
}

export function cardFrame(index, progress, geometry, momentum) {
    const lead = leadPose(index, geometry);
    const hidden = {
        ...lead,
        y: lead.y + geometry.viewportHeight * 0.22,
        scale: lead.scale * 0.88,
        opacity: 0,
    };
    const deck = deckPose(index, geometry);
    const row = rowPose(index, geometry);
    const exit = exitPose(index, geometry);
    let current = mixPose(hidden, lead, phase(progress, 0.13, 0.21));

    current = mixPose(current, deck, phase(progress, 0.2, 0.3));
    current = mixPose(current, row, phase(progress, 0.3, 0.46));

    const exitStart = 0.82 + (3 - index) * 0.008;
    current = mixPose(
        current,
        exit,
        phase(progress, exitStart, Math.min(1, exitStart + 0.16)),
    );
    current.y += momentum * (index + 1) * 6;

    return {
        ...current,
        rx: phase(progress, 0.82, 1) * -5,
        ry: flipProgress(index, progress) * 180,
    };
}

export function storyFrame(progress, geometry, momentum) {
    const storyGeometry = typeof geometry === 'number'
        ? { viewportHeight: geometry, titleLineTwoShift: 0 }
        : geometry;
    const titleEnter = phase(progress, 0.02, 0.13);
    const titleLeave = phase(progress, 0.35, 0.45);
    const titleOpen = titleEnter * (1 - titleLeave);
    const headingEnter = phase(progress, 0.025, 0.1);
    const copyEnter = phase(progress, 0.15, 0.23);
    const copyLeave = phase(progress, 0.34, 0.44);
    const lineTwoShift = phase(progress, 0.11, 0.21)
        * (1 - titleLeave);
    const trailProgress = phase(progress, 0.04, 0.95);
    const trailLeave = phase(progress, 0.9, 1);

    return {
        lineOneY: mix(0.41, 0, titleOpen),
        lineTwoY: mix(-0.41, 0, titleOpen),
        lineTwoX: storyGeometry.titleLineTwoShift * lineTwoShift,
        headingOpacity: headingEnter * (1 - titleLeave),
        copyOpacity: copyEnter * (1 - copyLeave),
        copyY: mix(28, 0, copyEnter) + momentum * 6,
        trailProgress,
        trailOpacity: phase(progress, 0.02, 0.1) * (1 - trailLeave),
        trailY: mix(
            storyGeometry.viewportHeight * 0.08,
            -storyGeometry.viewportHeight * 0.06,
            progress,
        ) + momentum * 22,
        progress: clamp(progress),
    };
}
