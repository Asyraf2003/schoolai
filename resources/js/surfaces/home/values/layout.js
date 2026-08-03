import { desktopCardFrame } from './desktop-layout.js';
import { clamp, easeOutCubic, mix, phase } from './motion.js';

function responsiveEntryRange(index, geometry) {
    const travel = geometry.rootHeight + geometry.viewportHeight;
    const start = (
        geometry.slots[index].rootOffsetY
        + geometry.viewportHeight * 0.08
    ) / travel;
    const end = (
        geometry.slots[index].rootOffsetY
        + geometry.viewportHeight * 0.45
    ) / travel;

    return { start, end, span: Math.max(0.0001, end - start) };
}

function responsiveCardFrame(index, progress, geometry) {
    const range = responsiveEntryRange(index, geometry);
    const pairDelay = geometry.mode === 2 && index % 2
        ? range.span * 0.12
        : 0;
    const local = clamp(
        (progress - range.start - pairDelay) / range.span,
    );
    const enter = easeOutCubic(phase(local, 0, 0.62));
    const flip = phase(local, 0.12, 0.9);

    return {
        x: 0,
        y: mix(34, 0, enter),
        z: 0,
        rz: 0,
        ry: mix(180, 0, flip),
        scale: mix(0.96, 1, enter),
        opacity: phase(local, 0, 0.24),
    };
}

export function cardFrame(index, progress, geometry, momentum) {
    if (geometry.mode === 4) {
        return desktopCardFrame(index, progress, geometry, momentum);
    }

    return responsiveCardFrame(index, progress, geometry);
}

export function storyFrame(
    progress,
    geometry,
    momentum,
    headingState,
) {
    const reveal = headingState?.reveal ?? 1;
    const desktop = geometry.mode === 4;
    const headingLeave = desktop ? phase(progress, 0.2, 0.38) : 0;
    const copyEnter = phase(reveal, 0.62, 1);
    const copyLeave = desktop ? phase(progress, 0.18, 0.34) : 0;
    const trailLeave = phase(progress, 0.93, 0.99);

    return {
        lineOneX: mix(104 * geometry.directionSign, 0, reveal),
        lineTwoX: mix(-104 * geometry.directionSign, 0, reveal),
        headingOpacity: desktop ? 1 - phase(progress, 0.3, 0.42) : 1,
        headingY: desktop
            ? mix(0, -geometry.stageHeight * 0.72, headingLeave)
            : 0,
        copyOpacity: copyEnter * (1 - copyLeave),
        copyY: mix(24, 0, copyEnter),
        cardsOpacity: desktop ? 1 - phase(progress, 0.94, 1) : 1,
        trailProgress: desktop ? phase(progress, 0.1, 0.92) : 0,
        trailOpacity: desktop
            ? phase(progress, 0.1, 0.2) * (1 - trailLeave)
            : 0,
        trailY: desktop
            ? mix(
                geometry.cardHeight * 0.12,
                -geometry.cardHeight * 0.08,
                progress,
            ) + momentum * 14
            : 0,
        progress: clamp(progress),
    };
}
