import { flipAngle } from './desktop-keyframes.js';
import { desktopCardFrame } from './desktop-layout.js';
import { clamp, easeOutCubic, mix, phase } from './motion.js';

function visibleFraction(index, progress, geometry) {
    const travel = geometry.rootHeight + geometry.viewportHeight;
    const storyTop = geometry.viewportHeight - progress * travel;
    const cardTop = storyTop + geometry.slots[index].rootOffsetY;

    return clamp(
        (geometry.viewportHeight - cardTop) / geometry.cardHeight,
    );
}

function responsiveCardFrame(index, progress, geometry) {
    const visible = visibleFraction(index, progress, geometry);
    const enter = easeOutCubic(phase(visible, 0.04, 0.5));
    const flip = clamp((visible - 0.5) / 0.5);

    return {
        x: 0,
        y: mix(36, 0, enter),
        z: 0,
        rz: 0,
        ry: flipAngle(flip),
        scale: mix(0.96, 1, enter),
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
    const headingLeave = desktop ? phase(progress, 0.12, 0.28) : 0;
    const copyEnter = phase(reveal, 0.58, 1);
    const copyLeave = desktop ? phase(progress, 0.1, 0.22) : 0;
    const trailLeave = phase(progress, 0.97, 1);

    return {
        lineOneY: mix(108, 0, reveal),
        lineTwoY: mix(-108, 0, reveal),
        headingOpacity: desktop ? 1 - phase(progress, 0.18, 0.3) : 1,
        headingY: desktop
            ? mix(0, -geometry.stageHeight * 0.72, headingLeave)
            : 0,
        copyOpacity: copyEnter * (1 - copyLeave),
        copyY: mix(24, 0, copyEnter),
        floatActive: progress >= 0.12,
        trailProgress: desktop ? phase(progress, 0.06, 0.96) : 0,
        trailOpacity: desktop
            ? phase(progress, 0.08, 0.16) * (1 - trailLeave)
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
