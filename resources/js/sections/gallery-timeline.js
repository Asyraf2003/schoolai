import { galleryGeometry } from './gallery-geometry.js';

export function createGalleryTimeline(gsap, ScrollTrigger, root, update) {
    gsap.registerPlugin(ScrollTrigger);
    const stage = root.querySelector('[data-gallery-stage]');
    const composition = root.querySelector('[data-gallery-composition]');
    const items = [...root.querySelectorAll('[data-gallery-item]')];
    const geometry = galleryGeometry(stage.clientWidth, stage.clientHeight, document.documentElement.dir === 'rtl');
    const timeline = gsap.timeline({ scrollTrigger: {
        id: 'gallery-v2', trigger: root, start: 'top top', end: 'bottom bottom', scrub: true,
        onUpdate: trigger => { root.dataset.galleryProgress = trigger.progress.toFixed(4); update(trigger.progress); },
    } });
    items.forEach((item, index) => {
        const g = geometry[index];
        item.dataset.column = g.column;
        item.style.aspectRatio = String(g.width / g.height);
        gsap.set(item, { x: g.fromX, y: g.fromY, scale: index === 0 ? g.openingScale : g.scale * 1.35, opacity: index === 0 ? 1 : 0, force3D: true });
        const entry = index === 0 ? .12 : .17 + (index % 4) * .035;
        timeline.to(item, { x: g.x, y: g.y, scale: g.scale, opacity: 1, duration: .46 - entry, ease: 'power1.inOut' }, entry);
        timeline.to(item, { opacity: 0, duration: .16, ease: 'none' }, .76 + g.row * .025);
    });
    const bottom = Math.max(...geometry.map(g => g.y + g.height));
    timeline.fromTo(composition, { y: 0 }, { y: -Math.max(stage.clientHeight * .12, bottom - stage.clientHeight * .85), duration: .30, ease: 'none' }, .46);
    timeline.to(composition, { y: -Math.max(bottom, stage.clientHeight), duration: .24, ease: 'none' }, .76);
    timeline.to(root.querySelector('.gallery__header h2'), { opacity: 0, duration: .12 }, .12);
    timeline.scrollTrigger.refresh();
    timeline.scrollTrigger.update();
    return timeline;
}
