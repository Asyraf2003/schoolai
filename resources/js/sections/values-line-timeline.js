// A single scrubbed progress drives three independent dash lengths. Explicit
// painting avoids lazy/inactive tween states diverging across SVG engines.
export function createValuesLineTimeline(gsap, ScrollTrigger, scene, paths, originals) {
    gsap.registerPlugin(ScrollTrigger);
    const box = scene.getBoundingClientRect();
    const svg = scene.querySelector('svg');
    svg.setAttribute('viewBox', `0 0 ${box.width} ${box.height}`);
    const segments = paths.map((path, index) => {
        let axis = 0;
        path.setAttribute('d', originals[index].replace(/-?\d+(?:\.\d+)?/g, number => {
            const scale = axis++ % 2 === 0 ? box.width / 1920 : box.height / 5400;
            return (Number(number) * scale).toFixed(4);
        }));
        const length = path.getTotalLength();
        gsap.set(path, { strokeDasharray: `${length} ${length + 2}` });
        return { path, length, start: Number(path.dataset.start) / 5400,
            duration: (Number(path.dataset.end) - Number(path.dataset.start)) / 5400 };
    });
    const clip = value => Math.max(0, Math.min(1, value));
    const paint = progress => segments.forEach(({ path, length, start, duration }) => {
        const visible = clip((progress - start) / duration);
        path.style.strokeDashoffset = ((length + 1) * (1 - visible)).toFixed(4);
    });
    const progress = { value: 0 };
    paint(0);
    const timeline = gsap.timeline({
        scrollTrigger: { id: 'values-line', trigger: scene, start: 'top 85%', end: 'bottom bottom',
            scrub: true, invalidateOnRefresh: true },
    });
    timeline.to(progress, { value: 1, duration: 1, ease: 'none',
        onUpdate: () => paint(progress.value) }, 0);
    ScrollTrigger.refresh(); timeline.scrollTrigger.update();
    paint(timeline.progress());
    return timeline;
}
