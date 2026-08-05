import {
    clamp,
    createPausedAnimation,
    DURATION,
    localRange,
    MOVE_END,
    MOVE_START,
    rangeProgress,
    setAnimationProgress,
    trackTransform,
} from './geometry.js';
import {
    createTypographyEntrance,
    prepareTypography,
} from './typography.js';

function liftDistance() {
    return Math.min(104, Math.max(56, window.innerHeight * 0.1));
}

function createLiftAnimation(element) {
    const distance = liftDistance();
    return createPausedAnimation(element, [
        {
            opacity: 0.28,
            transform: `translate3d(0, ${distance}px, 0)`,
        },
        {
            opacity: 1,
            transform: 'translate3d(0, 0, 0)',
        },
    ], 'cubic-bezier(.16,1,.3,1)');
}

function targetState(element, trigger, trackRect, endMove) {
    if (!element || !trigger) return null;
    return {
        animation: createLiftAnimation(element),
        range: localRange(
            trigger,
            trackRect,
            endMove,
            window.innerHeight,
            false,
        ),
    };
}

function syncTarget(target, progress, direction) {
    if (!target) return;
    if (direction === 'up') {
        setAnimationProgress(target.animation, 1);
        return;
    }
    if (direction === 'initial') {
        setAnimationProgress(
            target.animation,
            progress < target.range[0] ? 0 : 1,
        );
        return;
    }
    setAnimationProgress(
        target.animation,
        rangeProgress(progress, target.range),
    );
}

export function prepareVisionTypography(root) {
    prepareTypography(root);
}

export function createVisionTimeline(root) {
    const track = root.querySelector('[data-vision-track]');
    const frame = root.querySelector('[data-vision-image-frame]');
    const stack = root.querySelector('[data-vision-image-stack]');
    const vision = root.querySelector('[data-vision-copy="vision"]');
    const mission = root.querySelector('[data-vision-copy="mission"]');
    const programTitle = root.querySelector('[data-vision-program] h2');
    const visionTitle = vision?.querySelector('.vision-paper__kicker');
    const missionTitle = mission?.querySelector('.vision-paper__kicker');

    if (!track || !frame || !stack) {
        return {
            setProgress() {},
            playTypography() {},
            showTypography() {},
            resetTypography() {},
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
    const storyAnimations = [
        createPausedAnimation(track, [
            { offset: 0, transform: trackTransform(0, horizontal) },
            { offset: MOVE_START, transform: trackTransform(0, horizontal) },
            { offset: MOVE_END, transform: trackTransform(endMove, horizontal) },
            { offset: 1, transform: trackTransform(endMove, horizontal) },
        ]),
        createPausedAnimation(stack, [
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
    const compactTargets = horizontal ? [] : [
        targetState(vision, visionTitle, trackRect, endMove),
        targetState(mission, missionTitle, trackRect, endMove),
        targetState(programTitle, programTitle, trackRect, endMove),
    ].filter(Boolean);
    const visionRange = !horizontal && visionTitle
        ? localRange(visionTitle, trackRect, endMove, viewportSize, false)
        : [0, 1];
    const missionRange = !horizontal && missionTitle
        ? localRange(missionTitle, trackRect, endMove, viewportSize, false)
        : [0, 1];

    function syncCompactTypography(progress, direction) {
        if (horizontal) return;
        if (direction === 'up') {
            typography.finish();
            return;
        }
        if (direction === 'initial') {
            if (progress < visionRange[0]) typography.resetVision();
            else typography.finishVision();
            if (progress < missionRange[0]) typography.resetMission();
            else typography.finishMission();
            return;
        }
        typography.setVisionProgress(rangeProgress(progress, visionRange));
        typography.setMissionProgress(rangeProgress(progress, missionRange));
    }

    return {
        setProgress(progress, direction = 'initial') {
            const normalized = clamp(progress);
            const time = normalized * DURATION;
            storyAnimations.forEach((animation) => {
                animation.currentTime = time;
            });
            compactTargets.forEach((target) => {
                syncTarget(target, normalized, direction);
            });
            syncCompactTypography(normalized, direction);
        },
        playTypography: () => typography.play(),
        showTypography: () => typography.finish(),
        resetTypography: () => typography.reset(),
        destroy() {
            storyAnimations.forEach((animation) => animation.cancel());
            compactTargets.forEach(({ animation }) => animation.cancel());
            typography.destroy();
        },
    };
}
