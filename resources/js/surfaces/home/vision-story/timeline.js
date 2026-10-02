import { clamp } from './geometry.js';
import { createVisionBackgroundCompositor } from './background-compositor.js';

export function createVisionTimeline(root) {
    const panels = Array.from(root.querySelectorAll('[data-vision-panel]'));
    const visuals = Array.from(root.querySelectorAll('[data-vision-visual]'));
    const media = visuals.map((visual) => visual.querySelector('[data-vision-art]'));
    const background = createVisionBackgroundCompositor(root, panels);
    const transitionCount = Math.max(1, visuals.length - 1);
    let activeIndex = -1;
    let mediaRange = '';
    const clips = [];
    const transforms = [];

    function setActive(index) {
        if (activeIndex === index) return;
        activeIndex = index;
        panels.forEach((panel, panelIndex) => {
            panel.classList.toggle('is-active', panelIndex === index);
        });
    }

    function setProgress(progress) {
        background.setProgress(progress);
        const scaled = clamp(progress) * transitionCount;
        const nextRange = `${Math.floor(scaled)},${Math.ceil(scaled)}`;
        if (mediaRange !== nextRange) {
            mediaRange = nextRange;
            root.dataset.visionMediaRange = mediaRange;
            root.dispatchEvent(new CustomEvent('schoolai:vision-media-range', { bubbles: true }));
        }
        setActive(Math.min(panels.length - 1, Math.floor(scaled + .5)));

        visuals.forEach((visual, index) => {
            const transition = clamp(scaled - index);
            const clip = index >= visuals.length - 1 ? 'inset(0 0 0% 0)'
                : `inset(0 0 ${(transition * 100).toFixed(3)}% 0)`;
            if (clips[index] === clip) return;
            clips[index] = clip;
            visual.style.clipPath = clip;
        });

        media.forEach((element, index) => {
            if (!element) return;
            const y = Math.max(-8, Math.min(8, (scaled - index) * 8));
            const transform = `translate3d(0, ${y.toFixed(3)}%, 0) scale(1.08)`;
            if (transforms[index] === transform) return;
            transforms[index] = transform;
            element.style.transform = transform;
        });
    }

    function destroy() {
        background.destroy();
        delete root.dataset.visionMediaRange;
        panels.forEach((panel) => panel.classList.remove('is-active'));
        visuals.forEach((visual) => visual.style.removeProperty('clip-path'));
        media.forEach((element) => element?.style.removeProperty('transform'));
    }

    return { setProgress, destroy };
}
