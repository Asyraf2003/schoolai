import { responsiveFlipAngle } from './desktop-keyframes.js';
import { desktopCardFrame } from './desktop-layout.js';
import { clamp, mix, phase } from './motion.js';

const RESPONSIVE_MAX_TILT = 20;
const RESPONSIVE_GAP_RISE_SHARE = .95;

function visibleFraction(index, progress, geometry) {
    const travel = geometry.rootHeight + geometry.viewportHeight;
    const storyTop = geometry.viewportHeight - progress * travel;
    const cardTop = storyTop + geometry.slots[index].rootOffsetY;

    return clamp(
        (geometry.viewportHeight - cardTop) / geometry.cardHeight,
    );
}

function responsiveFlipLocal(visible, mode) {
    const start = mode === 3 ? .16 : .50;
    const end = mode === 3 ? .78 : 1;
    return clamp((visible - start) / Math.max(.0001, end - start));
}

function responsiveTiltLimit(geometry) {
    const maxRise = geometry.rowGap * RESPONSIVE_GAP_RISE_SHARE;
    const gapLimited = Math.atan2(
        maxRise,
        Math.max(1, geometry.cardWidth),
    ) * 180 / Math.PI;

    return Math.min(RESPONSIVE_MAX_TILT, gapLimited);
}

function responsiveTiltAngle(index, local, geometry) {
    const direction = index % 2 === 0 ? -1 : 1;
    const edgeEnvelope = Math.sin(Math.PI * clamp(local));

    return direction
        * responsiveTiltLimit(geometry)
        * edgeEnvelope;
}

function responsiveCardFrame(index, progress, geometry) {
    const visible = visibleFraction(index, progress, geometry);
    const local = responsiveFlipLocal(visible, geometry.mode);

    return {
        x: 0,
        y: 0,
        z: 0,
        rz: responsiveTiltAngle(index, local, geometry),
        ry: responsiveFlipAngle(local),
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

    return responsiveCardFrame(index, targetProgress, geometry);
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
