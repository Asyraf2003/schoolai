const DURATION = 1000;

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

const middleX = (rect) => rect.left + (rect.width / 2);
const middleY = (rect) => rect.top + (rect.height / 2);
const trackTransform = (x) => `translate3d(${x}px, -50%, 0)`;

function initialTrackX(editorial, vision, viewportWidth) {
    const left = Math.min(editorial.left, vision.left);
    const right = Math.max(editorial.right, vision.right);
    return (viewportWidth / 2) - ((left + right) / 2);
}

function itemTrackX(rect, viewportWidth, ratio = 0.54) {
    return (viewportWidth * ratio) - middleX(rect);
}

export function createVisionTimeline(root) {
    const track = root.querySelector('[data-vision-track]');
    const editorial = root.querySelector('[data-vision-editorial]');
    const vision = root.querySelector('[data-vision-panel-kind="vision"]');
    const missions = Array.from(root.querySelectorAll('[data-vision-panel-kind="mission"]'));
    const outro = root.querySelector('[data-vision-outro]');
    const canvas = root.querySelector('[data-vision-canvas]');

    if (!track || !editorial || !vision || missions.length !== 4 || !outro || !canvas) {
        return { setProgress() {}, destroy() {} };
    }

    const width = window.innerWidth;
    const height = window.innerHeight;
    const editorialRect = editorial.getBoundingClientRect();
    const visionRect = vision.getBoundingClientRect();
    const missionRects = missions.map((item) => item.getBoundingClientRect());
    const outroRect = outro.getBoundingClientRect();
    const canvasRect = canvas.getBoundingClientRect();
    const startX = initialTrackX(editorialRect, visionRect, width);
    const missionX = missionRects.map((rect) => itemTrackX(rect, width));
    const outroX = itemTrackX(outroRect, width, 0.5);
    const canvasX = itemTrackX(canvasRect, width, 0.5);
    const openScale = Math.max(width / visionRect.width, height / visionRect.height) * 1.03;
    const openX = (width / 2) - (middleX(visionRect) + startX);
    const openY = (height / 2) - middleY(visionRect);
    const animations = [];

    animations.push(createAnimation(track, [
        { offset: 0, transform: trackTransform(startX) },
        { offset: 0.25, transform: trackTransform(startX), easing: 'cubic-bezier(.22,1,.36,1)' },
        { offset: 0.36, transform: trackTransform(missionX[0]) },
        { offset: 0.48, transform: trackTransform(missionX[1]) },
        { offset: 0.60, transform: trackTransform(missionX[2]) },
        { offset: 0.72, transform: trackTransform(missionX[3]) },
        { offset: 0.84, transform: trackTransform(outroX) },
        { offset: 0.94, transform: trackTransform(canvasX), easing: 'cubic-bezier(.64,0,.36,1)' },
        { offset: 1, transform: trackTransform(canvasX) },
    ]));

    animations.push(createAnimation(vision, [
        { offset: 0, transform: `translate3d(${openX}px, ${openY}px, 0) scale(${openScale})` },
        { offset: 0.12, transform: `translate3d(${openX}px, ${openY}px, 0) scale(${openScale})`, easing: 'cubic-bezier(.22,1,.36,1)' },
        { offset: 0.25, transform: 'translate3d(0, 0, 0) scale(1)' },
        { offset: 1, transform: 'translate3d(0, 0, 0) scale(1)' },
    ]));

    animations.push(createAnimation(outro, [
        { offset: 0, opacity: 0, transform: 'translate3d(0, 18svh, 0)' },
        { offset: 0.72, opacity: 0, transform: 'translate3d(0, 18svh, 0)', easing: 'cubic-bezier(.22,1,.36,1)' },
        { offset: 0.84, opacity: 1, transform: 'translate3d(0, 0, 0)' },
        { offset: 1, opacity: 1, transform: 'translate3d(0, 0, 0)' },
    ]));

    const artOffsets = [[-90, -70], [90, -60], [-80, 80], [85, 65]];
    root.querySelectorAll('[data-vision-art]').forEach((image, index) => {
        const [x, y] = artOffsets[index % artOffsets.length];
        animations.push(createAnimation(image, [
            { offset: 0, opacity: 0.08, transform: `translate3d(${x}px, ${y}px, 0) scale(1.08)` },
            { offset: 0.14, opacity: 0.22, transform: 'translate3d(0, 0, 0) scale(1)', easing: 'cubic-bezier(.22,1,.36,1)' },
            { offset: 1, opacity: 0.18, transform: 'translate3d(0, 0, 0) scale(1)' },
        ]));
    });

    return {
        setProgress(progress) {
            const time = Math.max(0, Math.min(1, progress)) * DURATION;
            animations.forEach((animation) => { animation.currentTime = time; });
        },
        destroy() {
            animations.forEach((animation) => animation.cancel());
        },
    };
}
