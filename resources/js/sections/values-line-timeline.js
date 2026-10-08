import { createLineProgress } from './values-line-progress.js';

export function createValuesLineTimeline(gsap, ScrollTrigger, scene, path, original) {
    gsap.registerPlugin(ScrollTrigger);
    const box = scene.getBoundingClientRect();
    const svg = scene.querySelector('svg');
    // Dash units must match rendered pixels with non-scaling stroke, even on phones.
    svg.setAttribute('viewBox', `0 0 ${box.width} ${box.height}`);
    let axis = 0;
    path.setAttribute('d', original.replace(/-?\d+(?:\.\d+)?/g, number => {
        const scale = axis++ % 2 === 0 ? box.width / 1920 : box.height / 5400;
        return (Number(number) * scale).toFixed(4);
    }));
    const length = path.getTotalLength();
    const checkpoints = [1, 2, 3, 4].map(screen => {
        const y = box.height / 5 * screen;
        let low = 0;
        let high = length;
        for (let pass = 0; pass < 24; pass++) {
            const middle = (low + high) / 2;
            if (path.getPointAtLength(middle).y < y) low = middle;
            else high = middle;
        }
        return (low + high) / 2;
    });
    gsap.set(path, { strokeDasharray: `${length} ${length + 2}`, strokeDashoffset: length + 1 });
    const timeline = gsap.timeline({
        scrollTrigger: { id: 'values-line', trigger: scene, start: 'top 85%', end: 'bottom bottom',
            scrub: true, invalidateOnRefresh: true },
    });
    timeline.fromTo(path, { strokeDashoffset: length + 1 }, { strokeDashoffset: 0, duration: 1,
        ease: createLineProgress([0, ...checkpoints, length]) });
    // The plugin's public refresh clears its recorded scroll position (3.7.1).
    ScrollTrigger.refresh(); timeline.scrollTrigger.update();
    return timeline;
}
