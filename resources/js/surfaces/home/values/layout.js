import { desktopCardFrame } from './desktop-layout.js';
import { clamp, mix, phase } from './motion.js';

const ROW_ROTATIONS = [-5, -1.5, 1.5, 5];

function pose(x = 0, y = 0, z = 0, rz = 0, scale = 1, opacity = 1) {
    return { x, y, z, rz, scale, opacity };
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
        return desktopCardFrame(index, progress, geometry, momentum);
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
    const reveal = headingState?.reveal ?? 1;
    const copyEnter = phase(reveal, 0.76, 1);
    const desktop = geometry.mode === 4;
    const copyLeave = desktop ? phase(progress, 0.18, 0.34) : 0;
    const trailLeave = phase(progress, 0.92, 1);
    const headingY = desktop
        ? mix(0, -geometry.viewportHeight * 0.72, phase(progress, 0.18, 0.36))
        : 0;

    return {
        lineOneY: mix(108, 0, reveal),
        lineTwoY: mix(-108, 0, reveal),
        headingOpacity: 1,
        headingY,
        copyOpacity: copyEnter * (1 - copyLeave),
        copyY: mix(24, 0, copyEnter),
        trailProgress: desktop ? phase(progress, 0.18, 0.96) : 0,
        trailOpacity: desktop
            ? phase(progress, 0.18, 0.26) * (1 - trailLeave)
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
