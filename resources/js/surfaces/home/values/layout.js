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

function desktopLead(index, geometry) {
    return pose(
        0,
        geometry.viewportHeight * 0.2,
        -index * 22,
        0,
        0.86,
        index === 0 ? 1 : 0,
    );
}

function desktopDeck(index, geometry) {
    return pose(
        (index - 1.5) * geometry.cardWidth * 0.045,
        geometry.viewportHeight * 0.2 + index * 5,
        -index * 20,
        (index - 1.5) * 2.4,
        0.86,
        1,
    );
}

function desktopRow(index, geometry) {
    return pose(
        (index - 1.5) * geometry.cardWidth * 1.06,
        geometry.viewportHeight * 0.1
            + Math.abs(index - 1.5) * 3,
        index * 2,
        ROW_ROTATIONS[index],
        0.92,
    );
}

function desktopFlip(index, progress) {
    const start = 0.5 + (3 - index) * 0.045;
    return phase(progress, start, start + 0.15);
}

function desktopFrame(index, progress, geometry, momentum) {
    const lead = desktopLead(index, geometry);
    const hidden = {
        ...lead,
        y: lead.y + geometry.viewportHeight * 0.18,
        scale: lead.scale * 0.88,
        opacity: 0,
    };
    const deck = desktopDeck(index, geometry);
    const row = desktopRow(index, geometry);
    const exit = {
        ...row,
        y: row.y - geometry.viewportHeight * (1.08 + index * 0.025),
        rz: row.rz + (index - 1.5) * 3,
        opacity: 0,
    };
    let current = mixPose(hidden, lead, phase(progress, 0.07, 0.18));

    current = mixPose(current, deck, phase(progress, 0.22, 0.32));
    current = mixPose(current, row, phase(progress, 0.32, 0.48));
    current = mixPose(
        current,
        exit,
        phase(progress, 0.84 + (3 - index) * 0.008, 1),
    );
    current.y += momentum * (index + 1) * 5;

    return {
        ...current,
        rx: phase(progress, 0.84, 1) * -5,
        ry: mix(180, 0, desktopFlip(index, progress)),
    };
}

function tabletPose(index, geometry) {
    const column = index % 2 ? 0.5 : -0.5;
    const row = index < 2 ? -0.5 : 0.5;

    return pose(
        column * geometry.cardWidth * 1.08,
        geometry.viewportHeight * 0.12
            + row * geometry.cardHeight * 1.04,
        index * 2,
        ROW_ROTATIONS[index] * 0.35,
        0.86,
    );
}

function tabletFrame(index, progress, geometry) {
    const current = tabletPose(index, geometry);
    const pair = index >= 2 ? 0 : 1;
    const start = 0.3 + pair * 0.18;
    const flip = phase(progress, start, start + 0.24);

    return {
        ...current,
        opacity: phase(progress, 0.12, 0.18),
        rx: 0,
        ry: mix(180, 0, flip),
    };
}

function phoneFrame(index, progress, geometry) {
    const start = 0.16;
    const slot = 0.19;
    const local = clamp((progress - start - index * slot) / slot);
    const fadeIn = phase(local, 0, 0.08);
    const fadeOut = index === 3 ? 1 : 1 - phase(local, 0.9, 1);

    return {
        ...pose(
            0,
            geometry.viewportHeight * 0.2,
            index,
            0,
            0.78,
            fadeIn * fadeOut,
        ),
        rx: 0,
        ry: mix(180, 0, phase(local, 0.18, 0.68)),
    };
}

export function cardFrame(index, progress, geometry, momentum) {
    if (geometry.mode === 4) {
        return desktopFrame(index, progress, geometry, momentum);
    }

    if (geometry.mode === 2) {
        return tabletFrame(index, progress, geometry);
    }

    return phoneFrame(index, progress, geometry);
}

export function storyFrame(
    progress,
    geometry,
    momentum,
    headingState,
) {
    const reveal = headingState?.reveal ?? phase(progress, 0.018, 0.13);
    const copyEnter = phase(reveal, 0.76, 1);
    const desktop = geometry.mode === 4;
    const trailLeave = phase(progress, 0.92, 1);

    return {
        lineOneY: mix(108, 0, reveal),
        lineTwoY: mix(-108, 0, reveal),
        headingOpacity: 1,
        copyOpacity: copyEnter,
        copyY: mix(24, 0, copyEnter),
        trailProgress: desktop ? phase(progress, 0.04, 0.95) : 0,
        trailOpacity: desktop
            ? phase(progress, 0.04, 0.11) * (1 - trailLeave)
            : 0,
        trailY: desktop
            ? mix(
                geometry.viewportHeight * 0.08,
                -geometry.viewportHeight * 0.06,
                progress,
            ) + momentum * 20
            : 0,
        progress: clamp(progress),
    };
}
