const clamp = value => Math.max(0, Math.min(1, value));
const smooth = value => { const p = clamp(value); return p * p * (3 - 2 * p); };

// Previous copy bottom leaves the top exactly as NEXT h3 hits vertical center.
export function storiesCheckpoints(heights, stageHeight, mobile = false, titleOffsets = []) {
    const bottom = Math.min(80, Math.max(32, stageHeight * .06));
    const anchors = heights.map((h, i) => i === 0 && mobile
        ? stageHeight - bottom - h / 2 : stageHeight / 2 - (titleOffsets[i] ?? 0));
    const centers = [0];
    for (let i = 1; i < heights.length; i++) {
        centers.push(centers[i - 1] + anchors[i - 1] + heights[i - 1] / 2);
    }
    const last = heights.length - 1;
    // Release sticky as the final heading reaches center; media and title then rise together.
    return { anchors, centers, travel: centers[last] };
}

export function storiesCopyPose(scroll, centerAt, anchor, copyHeight, stageHeight, titleOffset = 0) {
    const y = anchor + centerAt - scroll;
    const enters = clamp((stageHeight - (y - copyHeight / 2))
        / Math.max(1, stageHeight / 2 + titleOffset + copyHeight / 2));
    return { y: y - stageHeight / 2, enters };
}

export function storiesWordPose(entry, index, total) {
    const delay = .25 * index / Math.max(1, total - 1);
    const reveal = clamp((entry - delay) / (1 - delay));
    return { opacity: clamp(reveal * 2.6), blur: 16 * (1 - smooth(reveal)) };
}

export function storiesMediaPose(scroll, centers, stageHeight, mobile) {
    const current = centers.reduce((active, at, i) => scroll >= at ? i : active, 0);
    return {
        current,
        layers: centers.map((at, i) => ({
            opacity: i === current ? 1 : 0,
            // Incoming media finishes its upward cover at the heading-center checkpoint.
            y: mobile && i > 0
                ? 100 * (1 - smooth((scroll - at + stageHeight * .28) / (stageHeight * .28))) : 0,
        })),
    };
}
