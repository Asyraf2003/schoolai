import { galleryGeometry } from './gallery-geometry.js';

export function createGalleryTimeline(gsap, ScrollTrigger, root, update) {
    gsap.registerPlugin(ScrollTrigger);
    const stage = root.querySelector('[data-gallery-stage]');
    const composition = root.querySelector('[data-gallery-composition]');
    const statement = root.querySelector('[data-gallery-statement]');
    const preceding = root.previousElementSibling;
    const items = [...root.querySelectorAll('[data-gallery-item]')];
    const words = [...root.querySelectorAll('[data-gallery-word]')];
    const width = stage.clientWidth;
    const height = stage.clientHeight;
    const geometry = galleryGeometry(width, height, document.documentElement.dir === 'rtl', items.length);
    const lead = geometry[0];
    const baseWidth = width * 26 / 30;
    const baseHeight = baseWidth * .5625;
    const openingHeight = baseHeight * lead.openingScale;
    root.style.setProperty('--gallery-opening-gap-extra',
        `${Math.max(0, lead.fromX - lead.fromY).toFixed(3)}px`);
    const headerHeight = root.querySelector('.gallery__header').offsetHeight;
    const prePinPx = headerHeight + height;
    const holdBeforePinPx = Math.max(0, (height - openingHeight) / 2 - lead.fromY);
    const shrinkStartPx = prePinPx - holdBeforePinPx;
    const assemblySpanPx = Math.round(height * 1.15);
    const assemblyEndPx = prePinPx + assemblySpanPx;
    const bottom = Math.max(...geometry.map(g => g.y + g.height));
    const settledRise = Math.max(0, bottom - height);
    const riseEndPx = assemblyEndPx + settledRise;
    const exitPx = Math.round(height * .9);
    // Unpin at 80% exit blur/fade so Testimonials immediately rises 1:1 from below.
    const exitPinnedPx = exitPx * .8;
    const exitTailPx = exitPx - exitPinnedPx;
    const exitPinnedEndPx = riseEndPx + exitPinnedPx;
    const totalTimelinePx = riseEndPx + exitPx;
    root.style.setProperty('--gallery-total-height', `${Math.round(exitPinnedEndPx)}px`);
    root.dataset.galleryWidth = String(width);
    root.dataset.galleryHeight = String(height);
    const t = px => px / 1000;
    const headingTop = Math.max(1, root.querySelector('[data-gallery-heading]').offsetTop);
    const whiteEndProgress = Math.max(headingTop, Math.min(shrinkStartPx, headingTop + height * .28)) / totalTimelinePx;
    const lineFadeProgress = headingTop / totalTimelinePx;
    root.dataset.galleryShrinkStart = String(shrinkStartPx / totalTimelinePx);
    root.dataset.galleryPinStart = String(prePinPx / totalTimelinePx);
    root.dataset.galleryFormation = String(assemblyEndPx / totalTimelinePx);
    root.dataset.galleryExitStart = String(riseEndPx / totalTimelinePx);
    root.dataset.galleryExitPinned = String(exitPinnedEndPx / totalTimelinePx);
    root.dataset.galleryMediaCutoff = String((riseEndPx + exitPx * .85) / totalTimelinePx);
    const timeline = gsap.timeline({ scrollTrigger: {
        id: 'gallery-v2', trigger: root,
        start: 'top bottom',
        end: () => `+=${totalTimelinePx}`,
        scrub: true,
        onUpdate: trigger => {
            const entry = Math.min(1, trigger.progress / whiteEndProgress);
            const white = entry * entry * (3 - 2 * entry);
            preceding?.toggleAttribute('data-gallery-white', trigger.progress > 0);
            preceding?.style.setProperty('--gallery-white-pct', `${(white * 100).toFixed(3)}%`);
            preceding?.style.setProperty('--gallery-decoration-opacity', (1 - white).toFixed(5));
            const line = Math.min(1, trigger.progress / lineFadeProgress);
            preceding?.style.setProperty('--gallery-line-opacity', (1 - line * line * (3 - 2 * line)).toFixed(5));
            root.style.setProperty('--gallery-white-pct', `${(white * 100).toFixed(3)}%`);
            root.dataset.galleryProgress = trigger.progress.toFixed(4);
            update(trigger.progress);
        },
    } });
    items.forEach((item, index) => {
        const g = geometry[index];
        item.dataset.column = g.column;
        item.style.aspectRatio = String(g.width / g.height);
        if (index === 0) {
            gsap.set(item, {
                x: (width - baseWidth) / 2, y: g.fromY + (openingHeight - baseHeight) / 2,
                scale: g.openingScale, transformOrigin: 'center', opacity: 1,
            });
            if (holdBeforePinPx) timeline.to(item, { y: (height - baseHeight) / 2, duration: t(holdBeforePinPx), ease: 'none' }, t(shrinkStartPx));
            timeline.to(item, { scale: g.scale, duration: t(assemblyEndPx - shrinkStartPx), ease: 'power1.inOut' }, t(shrinkStartPx));
        } else {
            gsap.set(item, { x: g.fromX, y: g.fromY, scale: g.scale, opacity: 1, force3D: false });
            timeline.to(item, { x: g.x, y: g.y, duration: t(assemblySpanPx), ease: 'power1.inOut' }, t(prePinPx));
        }
        timeline.to(item, { opacity: 0, duration: t(exitPx), ease: 'none' }, t(riseEndPx));
    });
    gsap.set(composition, { y: 0, filter: 'blur(0px)' });
    if (settledRise > 0) timeline.to(composition, { y: -settledRise, duration: t(settledRise), ease: 'none' }, t(assemblyEndPx));
    timeline.to(composition, { y: -settledRise - exitPinnedPx, filter: 'blur(11.2px)', duration: t(exitPinnedPx), ease: 'none' }, t(riseEndPx));
    timeline.to(composition, { filter: 'blur(14px)', duration: t(exitTailPx), ease: 'none' }, t(exitPinnedEndPx));
    const wordSpanPx = Math.min(assemblySpanPx * .55, height * .6);
    const revealStartPx = Math.max(shrinkStartPx, Math.min(assemblyEndPx - wordSpanPx * .7, riseEndPx - wordSpanPx * 1.3));
    const revealBudgetPx = Math.max(1, riseEndPx - revealStartPx);
    const mistInPx = Math.min(wordSpanPx * .4, revealBudgetPx * .4);
    const opacityStaggerPx = Math.min(wordSpanPx * .12, revealBudgetPx * .12);
    const opacityDurPx = Math.min(wordSpanPx * .25, revealBudgetPx * .25);
    const blurStaggerPx = Math.min(wordSpanPx * .45, revealBudgetPx * .35);
    gsap.set(statement, { '--gallery-statement-mist': 0 });
    timeline.to(statement, { '--gallery-statement-mist': 1, duration: t(mistInPx), ease: 'none' }, t(revealStartPx));
    timeline.to(statement, { '--gallery-statement-mist': 0, duration: t(exitPx), ease: 'none' }, t(riseEndPx));
    words.forEach((word, index) => {
        const order = index / Math.max(1, words.length - 1);
        const blurStart = revealStartPx + order * blurStaggerPx;
        const blurDur = Math.max(1, Math.min(wordSpanPx * .85, riseEndPx - blurStart));
        gsap.set(word, { opacity: 0, filter: 'blur(16px)' });
        timeline.to(word, { opacity: 1, duration: t(opacityDurPx), ease: 'none' }, t(revealStartPx + order * opacityStaggerPx));
        timeline.to(word, { filter: 'blur(0px)', duration: t(blurDur), ease: 'none' }, t(blurStart));
        timeline.to(word, { opacity: 0, filter: 'blur(14px)', duration: t(exitPx), ease: 'none' }, t(riseEndPx));
    });
    timeline.scrollTrigger.refresh();
    timeline.scrollTrigger.update();
    return timeline;
}
