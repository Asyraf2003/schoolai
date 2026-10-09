// Each stroke has its own path and dash; a single scrubbed GSAP timeline
// synchronizes drawing to its vertical band in the accepted 5-screen field.
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
        gsap.set(path, { strokeDasharray: `${length} ${length + 2}`, strokeDashoffset: length + 1 });
        return { path, length, start: Number(path.dataset.start) / 5400,
            duration: (Number(path.dataset.end) - Number(path.dataset.start)) / 5400 };
    });
    const timeline = gsap.timeline({
        scrollTrigger: { id: 'values-line', trigger: scene, start: 'top 85%', end: 'bottom bottom',
            scrub: true, invalidateOnRefresh: true },
    });
    // A silent one-second anchor preserves total duration after the last stroke.
    timeline.to({ progress: 0 }, { progress: 1, duration: 1, ease: 'none' }, 0);
    segments.forEach(({ path, length, start, duration }) => {
        timeline.fromTo(path, { strokeDashoffset: length + 1 },
            { strokeDashoffset: 0, duration, ease: 'none', immediateRender: false }, start);
    });
    ScrollTrigger.refresh(); timeline.scrollTrigger.update();
    return timeline;
}
