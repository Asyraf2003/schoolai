import { clamp } from './geometry.js';
import { createVisionBackgroundCompositor } from './background-compositor.js';

export function createVisionTimeline(root) {
    const panels = Array.from(root.querySelectorAll('[data-vision-panel]'));
    const visuals = Array.from(root.querySelectorAll('[data-vision-visual]'));
    const images = visuals.map((visual) => visual.querySelector('img'));
    const background = createVisionBackgroundCompositor(root, panels);
    const transitionCount = Math.max(1, visuals.length - 1);
    let activeIndex = -1;

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
        setActive(Math.min(panels.length - 1, Math.floor(scaled + .5)));

        visuals.forEach((visual, index) => {
            if (index >= visuals.length - 1) {
                visual.style.clipPath = 'inset(0 0 0% 0)';
                return;
            }

            const transition = clamp(scaled - index);
            visual.style.clipPath = `inset(0 0 ${(transition * 100).toFixed(3)}% 0)`;
        });

        images.forEach((image, index) => {
            if (!image) return;
            const y = Math.max(-8, Math.min(8, (scaled - index) * 8));
            image.style.transform = `translate3d(0, ${y.toFixed(3)}%, 0) scale(1.08)`;
        });
    }

    function destroy() {
        background.destroy();
        panels.forEach((panel) => panel.classList.remove('is-active'));
        visuals.forEach((visual) => visual.style.removeProperty('clip-path'));
        images.forEach((image) => image?.style.removeProperty('transform'));
    }

    return { setProgress, destroy };
}
