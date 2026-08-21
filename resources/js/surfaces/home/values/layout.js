import { responsiveFlipAngle } from './desktop-keyframes.js';
import { desktopCardFrame } from './desktop-layout.js';
import { clamp, mix, phase } from './motion.js';

const TABLET_EDGE_ANGLE = 100;
const TABLET_FRONT_TILT = 10;

function visibleFraction(index, progress, geometry) {
    const travel = geometry.rootHeight + geometry.viewportHeight;
    const storyTop = geometry.viewportHeight - progress * travel;
    const cardTop = storyTop + geometry.slots[index].rootOffsetY;

    return clamp(
        (geometry.viewportHeight - cardTop) / geometry.cardHeight,
    );
}

function tabletRailAngle(visible) {
    const edge = phase(visible, 0.16, 0.44);
    const front = phase(visible, 0.44, 0.60);
    const settle = phase(visible, 0.60, 0.78);
    let angle = mix(180, TABLET_EDGE_ANGLE, edge);

    angle = mix(angle, TABLET_FRONT_TILT, front);
    return mix(angle, 0, settle);
}

function tabletRailY(progress, targetProgress, geometry) {
    const travel = geometry.rootHeight + geometry.viewportHeight;
    return (clamp(targetProgress) - clamp(progress)) * travel;
}

function responsiveCardFrame(
    index,
    progress,
    geometry,
    targetProgress,
) {
    const visible = visibleFraction(index, progress, geometry);

    if (geometry.mode === 3) {
        return {
            x: 0,
            y: tabletRailY(progress, targetProgress, geometry),
            z: 0,
            rz: 0,
            ry: tabletRailAngle(visible),
            scale: 1,
            floatY: 0,
        };
    }

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

export function cardFrame(
    index,
    progress,
    geometry,
    momentum,
    targetProgress = progress,
) {
    if (geometry.mode === 4) {
        return desktopCardFrame(index, progress, geometry, momentum);
    }

    return responsiveCardFrame(
        index,
        progress,
        geometry,
        targetProgress,
    );
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
        surfaceDetail: phase(progress, 0.04, 0.18),
        progress: clamp(progress),
    };
}
