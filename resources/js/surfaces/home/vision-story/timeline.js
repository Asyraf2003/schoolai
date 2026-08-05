const DURATION = 1000;
const MOVE_START = 0.04;
const MOVE_END = 0.96;

const clamp = (value) => Math.max(0, Math.min(1, value));
const middleX = (rect) => rect.left + (rect.width / 2);
const middleY = (rect) => rect.top + (rect.height / 2);

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

function masterOffset(trackFraction) {
    return MOVE_START + (clamp(trackFraction) * (MOVE_END - MOVE_START));
}

function orderedOffsets(firstMove, secondMove, endMove) {
    if (endMove === 0) return [0.25, 0.4];

    const first = masterOffset(firstMove / endMove);
    const second = masterOffset(secondMove / endMove);
    const start = clamp(Math.min(first, second));
    const end = clamp(Math.max(start + 0.04, Math.max(first, second)));

    return [start, end];
}

function frameSwapOffsets(frameRect, endMove, viewportSize, horizontal) {
    const fullyVisibleMove = horizontal
        ? viewportSize - frameRect.right
        : viewportSize - frameRect.bottom;
    const centeredMove = horizontal
        ? (viewportSize / 2) - middleX(frameRect)
        : (viewportSize / 2) - middleY(frameRect);

    return orderedOffsets(fullyVisibleMove, centeredMove, endMove);
}

function missionEnterOffsets(missionRect, endMove, viewportHeight) {
    const enterMove = (viewportHeight * 0.82) - missionRect.top;
    const centeredMove = (viewportHeight / 2) - middleY(missionRect);

    return orderedOffsets(enterMove, centeredMove, endMove);
}

function trackTransform(move, horizontal) {
    return horizontal
        ? `translate3d(${move}px, 0, 0)`
        : `translate3d(0, ${move}px, 0)`;
}

export function createVisionTimeline(root) {
    const track = root.querySelector('[data-vision-track]');
    const frame = root.querySelector('[data-vision-image-frame]');
    const stack = root.querySelector('[data-vision-image-stack]');
    const mission = root.querySelector('[data-vision-copy="mission"]');

    if (!track || !frame || !stack) {
        return { setProgress() {}, destroy() {} };
    }

    const horizontal = window.matchMedia('(min-width: 1024px)').matches;
    const viewportSize = horizontal ? window.innerWidth : window.innerHeight;
    const trackSize = horizontal ? track.scrollWidth : track.scrollHeight;
    const endMove = Math.min(0, viewportSize - trackSize);
    const frameRect = frame.getBoundingClientRect();
    const [swapStart, swapEnd] = frameSwapOffsets(
        frameRect,
        endMove,
        viewportSize,
        horizontal,
    );
    const animations = [];

    animations.push(createAnimation(track, [
        { offset: 0, transform: trackTransform(0, horizontal) },
        { offset: MOVE_START, transform: trackTransform(0, horizontal) },
        { offset: MOVE_END, transform: trackTransform(endMove, horizontal) },
        { offset: 1, transform: trackTransform(endMove, horizontal) },
    ]));

    animations.push(createAnimation(stack, [
        { offset: 0, transform: 'translate3d(0, 0, 0)' },
        { offset: swapStart, transform: 'translate3d(0, 0, 0)' },
        {
            offset: swapEnd,
            transform: 'translate3d(0, -50%, 0)',
            easing: 'cubic-bezier(.22,1,.36,1)',
        },
        { offset: 1, transform: 'translate3d(0, -50%, 0)' },
    ]));

    if (!horizontal && mission) {
        const missionRect = mission.getBoundingClientRect();
        const [enterStart, enterEnd] = missionEnterOffsets(
            missionRect,
            endMove,
            window.innerHeight,
        );
        const fromX = document.documentElement.dir === 'rtl' ? '-18vw' : '18vw';

        animations.push(createAnimation(mission, [
            { offset: 0, opacity: 0, transform: `translate3d(${fromX}, 0, 0)` },
            { offset: enterStart, opacity: 0, transform: `translate3d(${fromX}, 0, 0)` },
            {
                offset: enterEnd,
                opacity: 1,
                transform: 'translate3d(0, 0, 0)',
                easing: 'cubic-bezier(.22,1,.36,1)',
            },
            { offset: 1, opacity: 1, transform: 'translate3d(0, 0, 0)' },
        ]));
    }

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
