import {
    clamp,
    createPausedAnimation,
    DURATION,
    localRange,
    MOVE_END,
    MOVE_START,
    trackTransform,
} from './geometry.js';

export function createVisionTimeline(root) {
    const track = root.querySelector('[data-vision-track]');
    const frame = root.querySelector('[data-vision-image-frame]');
    const stack = root.querySelector('[data-vision-image-stack]');

    if (!track || !frame || !stack) {
        return { setProgress() {}, destroy() {} };
    }

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
    const animations = [
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

    return {
        setProgress(progress) {
            const time = clamp(progress) * DURATION;
            animations.forEach((animation) => {
                animation.currentTime = time;
            });
        },
        destroy() {
            animations.forEach((animation) => animation.cancel());
        },
    };
}
