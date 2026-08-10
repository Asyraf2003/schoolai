import { responsiveFlipAngle } from './desktop-keyframes.js';
import { desktopCardFrame } from './desktop-layout.js';
import { clamp, mix, phase } from './motion.js';

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
    const flip = clamp((visible - 0.5) / 0.5);

    return {
        x: 0,
        y: 0,
        z: 0,
        rz: 0,
        ry: responsiveFlipAngle(flip),
        scale: 1,
        floatY: 0,
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
    const copyEnter = phase(reveal, 0.58, 1);

    return {
        lineOneY: mix(108, 0, reveal),
        lineTwoY: mix(-108, 0, reveal),
        headingOpacity: 1,
        headingY: 0,
        copyOpacity: copyEnter,
        copyY: mix(24, 0, copyEnter),
        progress: clamp(progress),
    };
}
