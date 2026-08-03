import { clamp, mix, phase } from './motion.js';

const ROTATIONS = [-13, -4.5, 4.5, 13];

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

function frontPose(index, geometry) {
    const { mode, cardWidth, cardHeight } = geometry;

    if (mode === 4) {
        return pose(
            (index - 1.5) * cardWidth * 1.07,
            index % 2 ? 3 : -3,
            index * 2,
            0,
            0.92,
        );
    }

    if (mode === 2) {
        const column = index % 2 ? 0.5 : -0.5;
        const row = index < 2 ? -0.5 : 0.5;
        return pose(
            column * cardWidth * 1.08,
            row * cardHeight * 0.72,
            index * 2,
            0,
            0.86,
        );
    }

    return pose(
        (index - 1.5) * cardWidth * 0.035,
        (index - 1.5) * 5,
        -index * 26,
        (index - 1.5) * 1.8,
        0.96 - index * 0.012,
    );
}

function fanPose(index, geometry) {
    const { mode, cardWidth, cardHeight } = geometry;
    const gap = mode === 1 ? 0.25 : mode === 2 ? 0.58 : 0.92;
    const scale = mode === 1 ? 0.64 : mode === 2 ? 0.78 : 0.9;

    return pose(
        (index - 1.5) * cardWidth * gap,
        Math.abs(index - 1.5) * cardHeight * 0.075,
        index * 8,
        ROTATIONS[index],
        scale,
    );
}

function stackPose(index, geometry) {
    const { mode, cardWidth, cardHeight } = geometry;
    const scale = mode === 1 ? 0.78 : mode === 2 ? 0.84 : 0.9;

    return pose(
        (index - 1.5) * cardWidth * 0.028,
        cardHeight * 0.23 + Math.abs(index - 1.5) * 3,
        index * 12,
        (index - 1.5) * 4.6,
        scale,
    );
}

function exitPose(index, geometry) {
    const stacked = stackPose(index, geometry);
    return {
        ...stacked,
        x: stacked.x + (index - 1.5) * geometry.cardWidth * 0.08,
        y: stacked.y + geometry.viewportHeight * 1.15,
        rz: stacked.rz + (index - 1.5) * 4,
        opacity: 0,
    };
}

function flipProgress(index, progress, mode) {
    const first = mode === 1 ? 0.2 : 0.12;
    const order = 3 - index;
    const start = first + order * 0.075;
    return phase(progress, start, start + 0.15);
}

export function cardFrame(index, progress, geometry, momentum) {
    const front = frontPose(index, geometry);
    const enterFrom = {
        ...front,
        y: front.y + geometry.viewportHeight * 0.16,
        scale: front.scale * 0.88,
        opacity: 0,
    };
    const entered = mixPose(enterFrom, front, phase(progress, 0, 0.09));
    const fanned = fanPose(index, geometry);
    const stacked = stackPose(index, geometry);
    const exited = exitPose(index, geometry);
    let current;

    if (geometry.mode === 1) {
        current = mixPose(entered, fanned, phase(progress, 0.08, 0.24));
        current = mixPose(current, stacked, phase(progress, 0.66, 0.84));
    } else {
        current = mixPose(entered, fanned, phase(progress, 0.48, 0.68));
        current = mixPose(current, stacked, phase(progress, 0.68, 0.84));
    }

    current = mixPose(current, exited, phase(progress, 0.84, 1));
    current.y += momentum * (index + 1) * 7;

    return {
        ...current,
        rx: phase(progress, 0.84, 1) * -7,
        ry: flipProgress(index, progress, geometry.mode) * 180,
    };
}

export function storyFrame(progress, viewportHeight, momentum) {
    const enter = phase(progress, 0.5, 0.7);
    const depart = phase(progress, 0.82, 1);
    const headingStart = viewportHeight * 0.46;
    const headingY = mix(headingStart, 0, enter)
        - viewportHeight * 0.34 * depart;
    const enteredScale = mix(1.08, 1, enter);

    return {
        headingY: headingY + momentum * 18,
        headingScale: mix(enteredScale, 0.93, depart),
        headingOpacity: phase(progress, 0.46, 0.6),
        curveY: mix(-viewportHeight * 0.08, viewportHeight * 0.22, progress)
            + momentum * 28,
        curveOpacity: phase(progress, 0.3, 0.5),
        progress: clamp(progress),
    };
}
