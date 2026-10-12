import { galleryGeometry } from './gallery-geometry.js';

export function createGalleryTimeline(gsap, ScrollTrigger, root, update) {
    gsap.registerPlugin(ScrollTrigger);
    const stage = root.querySelector('[data-gallery-stage]');
    const composition = root.querySelector('[data-gallery-composition]');
    const preceding = root.previousElementSibling;
    const items = [...root.querySelectorAll('[data-gallery-item]')];
    const words = [...root.querySelectorAll('[data-gallery-word]')];
    const geometry = galleryGeometry(stage.clientWidth, stage.clientHeight, document.documentElement.dir === 'rtl');
    // Arrival only: title-to-video gap equals the opening video's side inset.
    // Media opening Y, shrink poses and ten-item timeline stay unchanged.
    root.style.setProperty('--gallery-opening-gap-extra',
        `${Math.max(0, geometry[0].fromX - geometry[0].fromY).toFixed(3)}px`);
    root.dataset.galleryWidth = String(stage.clientWidth);
    root.dataset.galleryHeight = String(stage.clientHeight);
    const shrinkStart = (root.querySelector('.gallery__header').offsetHeight + stage.clientHeight)
        / root.offsetHeight;
    const revealStart = shrinkStart + (.52 - shrinkStart) * .6;
    const lineFadeEnd = Math.max(1, root.querySelector('[data-gallery-heading]').offsetTop) / root.offsetHeight;
    root.dataset.galleryShrinkStart = String(shrinkStart);
    const timeline = gsap.timeline({ scrollTrigger: {
        id: 'gallery-v2', trigger: root,
        start: 'top bottom',
        end: 'bottom bottom', scrub: true,
        onUpdate: trigger => {
            const entry = Math.min(1, trigger.progress / shrinkStart);
            const dark = entry * entry * (3 - 2 * entry);
            preceding?.toggleAttribute('data-gallery-dark', trigger.progress > 0);
            preceding?.style.setProperty('--gallery-dark-pct', `${(dark * 100).toFixed(3)}%`);
            preceding?.style.setProperty('--gallery-decoration-opacity', (1 - dark).toFixed(5));
            const line = Math.min(1, trigger.progress / lineFadeEnd);
            preceding?.style.setProperty('--gallery-line-opacity', (1 - line * line * (3 - 2 * line)).toFixed(5));
            root.style.setProperty('--gallery-dark-pct', `${(dark * 100).toFixed(3)}%`);
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
    const settledRise = Math.max(0, bottom - stage.clientHeight);
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
