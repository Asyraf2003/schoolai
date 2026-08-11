import {
    deckAngle,
    exitAmount,
    fanAngle,
    fanArc,
    flipAngle,
    flipLocal,
    stackNudge,
    uprightAmount,
} from './desktop-keyframes.js';
import { mix, phase } from './motion.js';

const TITLE_DECK_GAP_PX = 76;
const CENTER_RELEASE_DISTANCE_RATIO = 0.34;

function pose(x, y, z, rz, scale, ry = 180) {
    return { x, y, z, rz, scale, ry, floatY: 0 };
}

function mixPose(from, to, amount) {
    return {
        x: mix(from.x, to.x, amount),
        y: mix(from.y, to.y, amount),
        z: mix(from.z, to.z, amount),
        rz: mix(from.rz, to.rz, amount),
        scale: mix(from.scale, to.scale, amount),
        ry: mix(from.ry, to.ry, amount),
        floatY: 0,
    };
}

function centeredOffsets(index, geometry) {
    const slot = geometry.slots[index];
    return {
        x: geometry.stageCenterX - slot.centerX,
        y: geometry.stageCenterY - slot.centerY,
    };
}

function titleDeckOffset(index, geometry, extra = 0) {
    const slot = geometry.slots[index];
    return geometry.headingTitleBottomOffset
        + TITLE_DECK_GAP_PX
        + extra
        - slot.rootOffsetY;
}

function hiddenPose(index, geometry, center) {
    return pose(
        center.x + stackNudge(index, geometry.cardWidth),
        titleDeckOffset(index, geometry),
        -index * 14,
        deckAngle(index),
        0.92,
    );
}

function deckPose(index, geometry, center) {
    return pose(
        center.x + stackNudge(index, geometry.cardWidth),
        titleDeckOffset(index, geometry, index * 2),
        -index * 14,
        deckAngle(index),
        0.97,
    );
}

function fanPose(index, geometry, center) {
    return pose(
        center.x * 0.3,
        center.y + fanArc(index, geometry.cardHeight),
        -index * 3,
        fanAngle(index),
        1,
    );
}

function preFlipPose(index) {
    return pose(0, 0, 0, fanAngle(index), 1);
}

function centerRelease(index, geometry, hidden, fan) {
    const scrollY = window.scrollY || 0;
    const stageCenterY = geometry.stickyTop + geometry.stageHeight / 2;
    const titleDeckCenterY = geometry.rootDocumentTop
        + geometry.headingTitleBottomOffset
        + TITLE_DECK_GAP_PX
        + geometry.cardHeight / 2
        - scrollY;

    if (titleDeckCenterY > stageCenterY) return null;

    const slot = geometry.slots[index];
    const slotCenterY = geometry.rootDocumentTop
        + slot.rootOffsetY
        + geometry.cardHeight / 2
        - scrollY;
    const distance = Math.max(
        1,
        geometry.cardHeight * CENTER_RELEASE_DISTANCE_RATIO,
    );
    const amount = phase(stageCenterY - titleDeckCenterY, 0, distance);

    return {
        amount,
        pose: pose(
            mix(hidden.x, fan.x, amount),
            stageCenterY - slotCenterY
                + fanArc(index, geometry.cardHeight) * amount,
            mix(hidden.z, fan.z, amount),
            mix(hidden.rz, fan.rz, amount),
            mix(hidden.scale, fan.scale, amount),
        ),
    };
}

export function desktopCardFrame(
    index,
    progress,
    geometry,
    momentum,
) {
    const center = centeredOffsets(index, geometry);
    const hidden = hiddenPose(index, geometry, center);
    const deck = deckPose(index, geometry, center);
    const fan = fanPose(index, geometry, center);
    const preFlip = preFlipPose(index);
    const release = centerRelease(index, geometry, hidden, fan);
    let current = mixPose(
        hidden,
        deck,
        phase(progress, 0, 0.096),
    );

    if (release && progress <= 0) {
        current = release.pose;
    } else {
        current = mixPose(
            current,
            fan,
            Math.max(
                release?.amount ?? 0,
                phase(progress, 0.072, 0.216),
            ),
        );
    }

    current = mixPose(current, preFlip, phase(progress, 0.18, 0.252));

    const localFlip = flipLocal(index, progress);
    const upright = uprightAmount(localFlip);
    const leave = exitAmount(progress);
    current.ry = flipAngle(localFlip);
    current.rz = mix(current.rz, 0, upright);
    current.x = mix(current.x, 0, upright);
    current.y = mix(current.y, 0, upright)
        - leave * (geometry.stageHeight + geometry.cardHeight * 0.72);
    current.z = mix(current.z, 0, upright);
    current.floatY = 0;

    return current;
}
