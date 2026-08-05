const DURATION = 1000;
const MOVE_START = 0.04;
const MOVE_END = 0.96;

const clamp = (value) => Math.max(0, Math.min(1, value));
const middleX = (rect) => rect.left + (rect.width / 2);

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

function swapOffsets(frameRect, endX, viewportWidth) {
    if (endX === 0) return [0.25, 0.4];

    const fullyVisibleX = viewportWidth - frameRect.right;
    const centeredX = (viewportWidth / 2) - middleX(frameRect);
    const first = masterOffset(fullyVisibleX / endX);
    const second = masterOffset(centeredX / endX);
    const start = clamp(Math.min(first, second));
    const end = clamp(Math.max(start + 0.04, Math.max(first, second)));

    return [start, end];
}

export function createVisionTimeline(root) {
    const track = root.querySelector('[data-vision-track]');
    const frame = root.querySelector('[data-vision-image-frame]');
    const stack = root.querySelector('[data-vision-image-stack]');

    if (!track || !frame || !stack) {
        return { setProgress() {}, destroy() {} };
    }

    const viewportWidth = window.innerWidth;
    const endX = Math.min(0, viewportWidth - track.scrollWidth);
    const frameRect = frame.getBoundingClientRect();
    const [swapStart, swapEnd] = swapOffsets(frameRect, endX, viewportWidth);
    const animations = [];

    animations.push(createAnimation(track, [
        { offset: 0, transform: 'translate3d(0, 0, 0)' },
        { offset: MOVE_START, transform: 'translate3d(0, 0, 0)' },
        { offset: MOVE_END, transform: `translate3d(${endX}px, 0, 0)` },
        { offset: 1, transform: `translate3d(${endX}px, 0, 0)` },
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
