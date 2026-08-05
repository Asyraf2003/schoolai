import {
    createTypographyEntrance,
    prepareTypography,
} from './typography.js';

const DURATION = 1000;
const MOVE_START = 0.04;
const MOVE_END = 0.96;
const clamp = (value) => Math.max(0, Math.min(1, value));

function createAnimation(element, keyframes) {
    const animation = element.animate(keyframes, {
        duration: DURATION,
        fill: 'both',
        easing: 'linear',
    });
    animation.pause();
    animation.currentTime = 0;
    return animation;
}

function masterOffset(move, endMove) {
    if (endMove === 0) return MOVE_START;
    return MOVE_START + (
        clamp(move / endMove) * (MOVE_END - MOVE_START)
    );
}

function orderedOffsets(firstMove, secondMove, endMove) {
    if (endMove === 0) return [0.25, 0.4];

    const first = masterOffset(firstMove, endMove);
    const second = masterOffset(secondMove, endMove);
    const start = clamp(Math.min(first, second));
    const end = clamp(Math.max(start + 0.04, Math.max(first, second)));
    return [start, end];
}

function localRange(element, trackRect, endMove, viewportSize, horizontal) {
    const rect = element.getBoundingClientRect();
    const localStart = horizontal
        ? rect.left - trackRect.left
        : rect.top - trackRect.top;
    const size = horizontal ? rect.width : rect.height;
    const enterMove = viewportSize - localStart;
    const centerMove = (viewportSize / 2) - (localStart + (size / 2));
    return orderedOffsets(enterMove, centerMove, endMove);
}

function rangeProgress(progress, range) {
    return clamp(
        (progress - range[0]) / Math.max(0.0001, range[1] - range[0]),
    );
}

function trackTransform(move, horizontal) {
    return horizontal
        ? `translate3d(${move}px, 0, 0)`
        : `translate3d(0, ${move}px, 0)`;
}

export function prepareVisionTypography(root) {
    prepareTypography(root);
}

export function createVisionTimeline(root) {
    const track = root.querySelector('[data-vision-track]');
    const frame = root.querySelector('[data-vision-image-frame]');
    const stack = root.querySelector('[data-vision-image-stack]');
    const missionTitle = root.querySelector(
        '[data-vision-copy="mission"] .vision-paper__kicker',
    );

    if (!track || !frame || !stack) {
        return {
            setProgress() {},
            playTypography() {},
            showTypography() {},
            resetTypography() {},
            playVisionTypography() {},
            showVisionTypography() {},
            resetVisionTypography() {},
            destroy() {},
        };
    }

    const typography = createTypographyEntrance(root);
    const horizontal = window.matchMedia('(min-width: 1181px)').matches;
    const viewportSize = horizontal ? window.innerWidth : window.innerHeight;
    const trackSize = horizontal ? track.scrollWidth : track.scrollHeight;
    const endMove = Math.min(0, viewportSize - trackSize);
    const trackRect = track.getBoundingClientRect();
    const frameRange = localRange(
        frame,
        trackRect,
        endMove,
        viewportSize,
        horizontal,
    );
    const missionRange = !horizontal && missionTitle
        ? localRange(
            missionTitle,
            trackRect,
            endMove,
            window.innerHeight,
            false,
        )
        : [0, 1];
    const animations = [
        createAnimation(track, [
            { offset: 0, transform: trackTransform(0, horizontal) },
            { offset: MOVE_START, transform: trackTransform(0, horizontal) },
            { offset: MOVE_END, transform: trackTransform(endMove, horizontal) },
            { offset: 1, transform: trackTransform(endMove, horizontal) },
        ]),
        createAnimation(stack, [
            { offset: 0, transform: 'translate3d(0, 0, 0)' },
            { offset: frameRange[0], transform: 'translate3d(0, 0, 0)' },
            {
                offset: frameRange[1],
                transform: 'translate3d(0, -50%, 0)',
                easing: 'cubic-bezier(.22,1,.36,1)',
            },
            { offset: 1, transform: 'translate3d(0, -50%, 0)' },
        ]),
    ];
    let missionLockedFinal = false;

    function syncCompactMission(progress, direction) {
        if (horizontal) return;

        if (direction === 'up') {
            typography.finishMission();
            missionLockedFinal = true;
            return;
        }

        if (direction === 'initial') {
            if (progress < missionRange[0]) {
                typography.resetMission();
                missionLockedFinal = false;
            } else {
                typography.finishMission();
                missionLockedFinal = true;
            }
            return;
        }

        if (direction === 'down' && progress < missionRange[0]) {
            typography.resetMission();
            missionLockedFinal = false;
            return;
        }

        if (!missionLockedFinal && direction === 'down') {
            typography.setMissionProgress(
                rangeProgress(progress, missionRange),
            );
        }
    }

    return {
        setProgress(progress, direction = 'initial') {
            const normalized = clamp(progress);
            const time = normalized * DURATION;
            animations.forEach((animation) => {
                animation.currentTime = time;
            });
            syncCompactMission(normalized, direction);
        },
        playTypography: () => typography.play(),
        showTypography: () => typography.finish(),
        resetTypography: () => typography.reset(),
        playVisionTypography: () => typography.playVision(),
        showVisionTypography: () => typography.finishVision(),
        resetVisionTypography: () => typography.resetVision(),
        destroy() {
            animations.forEach((animation) => animation.cancel());
            typography.destroy();
        },
    };
}
