export const DURATION = 1000;
export const MOVE_START = 0.04;
export const MOVE_END = 0.96;
export const clamp = (value) => Math.max(0, Math.min(1, value));

export function createPausedAnimation(element, keyframes, easing = 'linear') {
    const animation = element.animate(keyframes, {
        duration: DURATION,
        fill: 'both',
        easing,
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

export function localRange(
    element,
    trackRect,
    endMove,
    viewportSize,
    horizontal,
) {
    const rect = element.getBoundingClientRect();
    const localStart = horizontal
        ? rect.left - trackRect.left
        : rect.top - trackRect.top;
    const size = horizontal ? rect.width : rect.height;
    const enterMove = viewportSize - localStart;
    const centerMove = (viewportSize / 2) - (localStart + (size / 2));
    return orderedOffsets(enterMove, centerMove, endMove);
}

export function rangeProgress(progress, range) {
    return clamp(
        (progress - range[0]) / Math.max(0.0001, range[1] - range[0]),
    );
}

export function trackTransform(move, horizontal) {
    return horizontal
        ? `translate3d(${move}px, 0, 0)`
        : `translate3d(0, ${move}px, 0)`;
}

export function setAnimationProgress(animation, progress) {
    animation.currentTime = clamp(progress) * DURATION;
}
