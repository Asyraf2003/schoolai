import { galleryGeometry } from './gallery-geometry.js';

export function createGalleryTimeline(gsap, ScrollTrigger, root, update) {
    gsap.registerPlugin(ScrollTrigger);
    const stage = root.querySelector('[data-gallery-stage]');
    const composition = root.querySelector('[data-gallery-composition]');
    const preceding = root.previousElementSibling;
    const items = [...root.querySelectorAll('[data-gallery-item]')];
    const words = [...root.querySelectorAll('[data-gallery-word]')];
    const geometry = galleryGeometry(stage.clientWidth, stage.clientHeight, document.documentElement.dir === 'rtl');
    root.dataset.galleryWidth = String(stage.clientWidth);
    root.dataset.galleryHeight = String(stage.clientHeight);
    const shrinkStart = (root.querySelector('.gallery__header').offsetHeight + stage.clientHeight * .7)
        / (root.offsetHeight - stage.clientHeight * .3);
    const revealStart = shrinkStart + (.52 - shrinkStart) * .6;
    root.dataset.galleryShrinkStart = String(shrinkStart);
    const timeline = gsap.timeline({ scrollTrigger: {
        id: 'gallery-v2', trigger: root,
        start: 'top 70%',
        end: 'bottom bottom', scrub: true,
        onUpdate: trigger => {
            preceding?.toggleAttribute('data-gallery-dark', trigger.progress > 0);
            root.dataset.galleryProgress = trigger.progress.toFixed(4); update(trigger.progress);
        },
    } });
    items.forEach((item, index) => {
        const g = geometry[index];
        item.dataset.column = g.column;
        item.style.aspectRatio = String(g.width / g.height);
        gsap.set(item, { x: g.fromX, y: g.fromY, scale: index === 0 ? g.openingScale : g.scale, opacity: 1, force3D: true });
        timeline.to(item, { x: g.x, y: g.y, scale: g.scale, duration: .52 - shrinkStart, ease: 'power1.inOut' }, shrinkStart);
        timeline.to(item, { opacity: 0, duration: .14, ease: 'none' }, .76);
    });
    const bottom = Math.max(...geometry.map(g => g.y + g.height));
    const settledRise = bottom - stage.clientHeight;
    timeline.fromTo(composition, { y: 0, filter: 'blur(0px)' }, { y: -settledRise, duration: .24, ease: 'none' }, .52);
    timeline.to(composition, { y: -settledRise - stage.clientHeight * .5, filter: 'blur(14px)', duration: .14, ease: 'none' }, .76);
    words.forEach((word, index) => {
        const order = index / Math.max(1, words.length - 1);
        gsap.set(word, { opacity: 0, filter: 'blur(16px)' });
        timeline.to(word, { opacity: 1, duration: .10, ease: 'none' }, revealStart + order * .016);
        timeline.to(word, { filter: 'blur(0px)', duration: .52 - revealStart, ease: 'none' }, revealStart + order * .22);
        timeline.to(word, { opacity: 0, filter: 'blur(14px)', duration: .14, ease: 'none' }, .76);
    });
    timeline.to({}, { duration: .10 }, .90);
    timeline.scrollTrigger.refresh();
    timeline.scrollTrigger.update();
    return timeline;
}
