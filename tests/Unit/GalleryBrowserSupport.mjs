import assert from 'node:assert/strict';

export async function galleryReady(page) {
    await page.evaluate(() => {
        const root = document.querySelector('[data-gallery]');
        scrollTo(0, root.getBoundingClientRect().top + scrollY - innerHeight);
    });
    await page.waitForFunction(() => !!window.ScrollTrigger?.getById('gallery-v2'));
    await page.evaluate(() => document.fonts.ready);
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
    return page.evaluate(galleryState);
}
export function galleryState() {
    const rect = e => { const r = e.getBoundingClientRect(); return { x: r.x, y: r.y, width: r.width, height: r.height, bottom: r.bottom }; };
    return {
        width: innerWidth, height: innerHeight, overflow: document.documentElement.scrollWidth > innerWidth,
        items: [...document.querySelectorAll('[data-gallery-item]')].map(e => ({ ...rect(e), opacity: +getComputedStyle(e).opacity,
            column: e.dataset.column, image: e.querySelector('img').naturalWidth })),
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
    assert.ok(state.videos.filter(v => v.playing).length <= 2);
    assert.ok(state.videos.filter(v => v.hydrated).length <= 2);
}
