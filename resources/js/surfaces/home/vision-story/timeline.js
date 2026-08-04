const DURATION = 1000;

function createPausedAnimation(element, keyframes) {
    const animation = element.animate(keyframes, {
        duration: DURATION,
        fill: 'both',
        easing: 'linear',
    });

    animation.pause();
    animation.currentTime = 0;
    return animation;
}

function panelTransform({ rtl, x = 0, y = 0, scale = 1, rotate = 0 }) {
    const center = rtl ? 'translate(50%, -50%)' : 'translate(-50%, -50%)';
    return `${center} translate3d(${x}px, ${y}px, 0) scale(${scale}) rotate(${rotate}deg)`;
}

function missionFrames(index, geometry) {
    const windows = [
        [0.28, 0.43, 0.48, 0.59],
        [0.42, 0.56, 0.61, 0.72],
        [0.55, 0.69, 0.74, 0.85],
        [0.68, 0.82, 0.94, 1],
    ];
    const [start, dock, exitStart, exitEnd] = windows[index];
    const arc = start + ((dock - start) * 0.45);
    const { rtl, direction, width, height, dockX, dockY, dockScale } = geometry;

    return [
        {
            offset: 0,
            opacity: 0,
            transform: panelTransform({ rtl, x: direction * width * 0.78, y: height * 0.72, scale: 0.4, rotate: direction * 12 }),
        },
        {
            offset: start,
            opacity: 0,
            transform: panelTransform({ rtl, x: direction * width * 0.78, y: height * 0.72, scale: 0.4, rotate: direction * 12 }),
            easing: 'cubic-bezier(.22,1,.36,1)',
        },
        {
            offset: arc,
            opacity: 1,
            transform: panelTransform({ rtl, x: direction * width * 0.56, y: -height * 0.14, scale: dockScale * 0.88, rotate: direction * -7 }),
            easing: 'cubic-bezier(.2,.75,.3,1)',
        },
        {
            offset: dock,
            opacity: 1,
            transform: panelTransform({ rtl, x: dockX, y: dockY, scale: dockScale, rotate: 0 }),
        },
        {
            offset: exitStart,
            opacity: 1,
            transform: panelTransform({ rtl, x: dockX, y: dockY, scale: dockScale, rotate: 0 }),
            easing: 'cubic-bezier(.64,0,.36,1)',
        },
        {
            offset: exitEnd,
            opacity: index === 3 ? 0.7 : 0,
            transform: panelTransform({ rtl, x: direction * width * -1.18, y: dockY, scale: dockScale, rotate: 0 }),
        },
        {
            offset: 1,
            opacity: index === 3 ? 0.7 : 0,
            transform: panelTransform({ rtl, x: direction * width * -1.18, y: dockY, scale: dockScale, rotate: 0 }),
        },
    ];
}

export function createVisionTimeline(root) {
    const rtl = document.documentElement.dir === 'rtl';
    const mobile = window.matchMedia('(max-width: 1023px)').matches;
    const direction = rtl ? -1 : 1;
    const width = window.innerWidth;
    const height = window.innerHeight;
    const dockScale = mobile ? 0.74 : 0.56;
    const dockX = mobile ? 0 : direction * width * 0.24;
    const dockY = mobile ? height * 0.14 : 0;
    const geometry = { rtl, mobile, direction, width, height, dockScale, dockX, dockY };
    const animations = [];
    const panels = Array.from(root.querySelectorAll('[data-vision-panel]'));
    const editorial = root.querySelector('[data-vision-editorial]');
    const divider = root.querySelector('[data-vision-divider]');
    const visionPanel = panels[0];

    animations.push(createPausedAnimation(visionPanel, [
        { offset: 0, opacity: 1, transform: panelTransform({ rtl }) },
        { offset: 0.17, opacity: 1, transform: panelTransform({ rtl }), easing: 'cubic-bezier(.22,1,.36,1)' },
        { offset: 0.3, opacity: 1, transform: panelTransform({ rtl, x: dockX, y: dockY, scale: dockScale }) },
        { offset: 0.38, opacity: 1, transform: panelTransform({ rtl, x: dockX, y: dockY, scale: dockScale }), easing: 'cubic-bezier(.64,0,.36,1)' },
        { offset: 0.5, opacity: 0, transform: panelTransform({ rtl, x: direction * width * -1.18, y: dockY, scale: dockScale }) },
        { offset: 1, opacity: 0, transform: panelTransform({ rtl, x: direction * width * -1.18, y: dockY, scale: dockScale }) },
    ]));

    panels.slice(1).forEach((panel, index) => {
        animations.push(createPausedAnimation(panel, missionFrames(index, geometry)));
    });

    if (editorial) {
        const startTransform = mobile
            ? 'translate3d(0, -20px, 0)'
            : `translate3d(${direction * -36}px, -50%, 0)`;
        const endTransform = mobile ? 'translate3d(0, 0, 0)' : 'translate3d(0, -50%, 0)';
        animations.push(createPausedAnimation(editorial, [
            { offset: 0, opacity: 0, transform: startTransform },
            { offset: 0.18, opacity: 0, transform: startTransform, easing: 'cubic-bezier(.22,1,.36,1)' },
            { offset: 0.3, opacity: 1, transform: endTransform },
            { offset: 0.9, opacity: 1, transform: endTransform },
            { offset: 1, opacity: 0, transform: endTransform },
        ]));
    }

    if (divider) {
        const transform = mobile ? 'scaleX' : 'scaleY';
        animations.push(createPausedAnimation(divider, [
            { offset: 0, opacity: 0, transform: `${transform}(0)` },
            { offset: 0.2, opacity: 0, transform: `${transform}(0)` },
            { offset: 0.32, opacity: 1, transform: `${transform}(1)` },
            { offset: 0.92, opacity: 1, transform: `${transform}(1)` },
            { offset: 1, opacity: 0, transform: `${transform}(0)` },
        ]));
    }

    const artOffsets = [[-90, -70], [90, -60], [-80, 80], [85, 65]];
    root.querySelectorAll('[data-vision-art]').forEach((image, index) => {
        const [x, y] = artOffsets[index % artOffsets.length];
        animations.push(createPausedAnimation(image, [
            { offset: 0, opacity: 0.05, transform: `translate3d(${x}px, ${y}px, 0) scale(1.08)` },
            { offset: 0.2, opacity: 0.22, transform: 'translate3d(0, 0, 0) scale(1)', easing: 'cubic-bezier(.22,1,.36,1)' },
            { offset: 0.5, opacity: 0.16, transform: `translate3d(${x * -0.08}px, ${y * -0.08}px, 0) scale(.98)` },
            { offset: 1, opacity: 0.12, transform: `translate3d(${x * 0.06}px, ${y * 0.06}px, 0) scale(.96)` },
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
