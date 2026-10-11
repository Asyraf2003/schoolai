import assert from 'node:assert/strict';

export async function galleryResize(page, width, height) {
    await page.setViewportSize({ width, height });
    await page.waitForFunction(([w, h]) => {
        const root = document.querySelector('[data-gallery]');
        return +root.dataset.galleryWidth === w && +root.dataset.galleryHeight === h;
    }, [width, height]);
    await page.waitForTimeout(350);
}

export async function galleryReady(page) {
    await page.evaluate(() => {
        const root = document.querySelector('[data-gallery]');
        scrollTo(0, root.getBoundingClientRect().top + scrollY - innerHeight);
    });
    await page.waitForFunction(() => !!window.ScrollTrigger?.getById('gallery-v2'));
    await page.evaluate(() => document.fonts.ready);
}
export async function galleryOpening(page) {
    return galleryAt(page, +(await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start')));
}
export async function galleryAt(page, progress) {
    await page.evaluate(p => {
        window.ScrollTrigger.refresh();
        const t = window.ScrollTrigger.getById('gallery-v2');
        scrollTo(0, Math.round(t.start + (t.end - t.start) * p));
        t.update();
    }, progress);
    try {
        await page.waitForFunction(p => Math.abs(window.ScrollTrigger.getById('gallery-v2').progress - p) < .002, progress);
    } catch (error) {
        const state = await page.evaluate(() => {
            const t = window.ScrollTrigger.getById('gallery-v2');
            return { scroll: scrollY, start: t?.start, end: t?.end, progress: t?.progress, viewport: [innerWidth, innerHeight] };
        });
        throw new Error(`Gallery requested ${progress}: ${JSON.stringify(state)}`, { cause: error });
    }
    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
    await page.waitForFunction(() => [...document.querySelectorAll('[data-gallery-poster]')].every(image => {
        const item = image.parentElement;
        const r = item.getBoundingClientRect();
        const visible = r.bottom > 0 && r.top < innerHeight && r.right > 0 && r.left < innerWidth && +getComputedStyle(item).opacity > .15;
        return !visible || image.complete && image.naturalWidth > 0;
    }));
    return page.evaluate(galleryState);
}
export function galleryState() {
    const rect = e => { const r = e.getBoundingClientRect(); return { x: r.x, y: r.y, width: r.width, height: r.height, bottom: r.bottom }; };
    const heading = document.querySelector('[data-gallery-heading]');
    const cardViewport = document.querySelector('[data-values-cards-viewport]');
    const cardClipTop = getComputedStyle(cardViewport).position === 'sticky' ? Math.max(0, cardViewport.getBoundingClientRect().top) : 0;
    const valuesRemaining = Math.max(...[...document.querySelectorAll('[data-values-card-inner]')].map(card => {
        const r = card.getBoundingClientRect();
        return Math.max(0, r.bottom - cardClipTop) / Math.max(1, r.height);
    }));
    const lastStrand = [...document.querySelectorAll('[data-values-line-path]')].at(-1);
    const filterBlur = e => parseFloat(getComputedStyle(e).filter.match(/[\d.]+/)?.[0] ?? 0);
    const pixel = document.createElement('canvas').getContext('2d');
    pixel.fillStyle = getComputedStyle(document.querySelector('[data-program-values]')).backgroundColor;
    pixel.fillRect(0, 0, 1, 1);
    return {
        valuesRemaining, width: innerWidth, height: innerHeight, overflow: document.documentElement.scrollWidth > innerWidth,
        heading: { ...rect(heading), color: getComputedStyle(heading).color, size: getComputedStyle(heading).fontSize,
            visibility: getComputedStyle(heading).visibility,
            valuesSize: getComputedStyle(document.querySelector('[data-values-heading]')).fontSize },
        header: rect(document.querySelector('.gallery__header')),
        background: getComputedStyle(document.querySelector('[data-program-values]')).backgroundColor,
        backgroundRGB: [...pixel.getImageData(0, 0, 1, 1).data].slice(0, 3),
        typeVisible: getComputedStyle(document.querySelector('.program__type-home')).visibility,
        lastDraw: 1 - parseFloat(getComputedStyle(lastStrand).strokeDashoffset) / lastStrand.getTotalLength(),
        blur: filterBlur(document.querySelector('[data-gallery-composition]')),
        items: [...document.querySelectorAll('[data-gallery-item]')].map(e => ({ ...rect(e), opacity: +getComputedStyle(e).opacity,
            column: e.dataset.column, image: e.querySelector('img').naturalWidth })),
        words: [...document.querySelectorAll('[data-gallery-word]')].map(word => ({ opacity: +getComputedStyle(word).opacity, blur: filterBlur(word) })),
        videos: [...document.querySelectorAll('[data-gallery-video]')].map(v => ({ playing: !v.paused, hydrated: v.hasAttribute('src'), time: v.currentTime })),
        testimonial: rect(document.querySelector('[data-testimonials]')),
        triggers: window.ScrollTrigger?.getAll().filter(t => t.vars.id === 'gallery-v2').length ?? 0,
    };
}
export function galleryFits(state) {
    assert.equal(state.overflow, false);
    assert.equal(state.items.length, 10);
    for (let a = 0; a < 10; a++) for (let b = a + 1; b < 10; b++) {
        const x = state.items[a], y = state.items[b];
        assert.ok(x.x + x.width <= y.x + 1 || y.x + y.width <= x.x + 1
            || x.bottom <= y.y + 1 || y.bottom <= x.y + 1, `overlap ${a}/${b}`);
    }
    assert.ok(state.items.every(i => i.x >= -1 && i.x + i.width <= state.width + 1));
    assert.ok(state.items.filter(i => i.bottom > 0 && i.y < state.height && i.opacity > .15).every(i => i.image > 0), 'visible media retain decoded posters');
    assert.ok(state.videos.filter(v => v.playing).length <= 2);
    assert.ok(state.videos.filter(v => v.hydrated).length <= 2);
}
