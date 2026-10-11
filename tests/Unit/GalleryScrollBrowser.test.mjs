import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryFits } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery opening, formation, travel and testimonial handoff in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const lang of ['en', 'id', 'ar']) {
                await locale(page, lang);
                assert.equal(await page.locator('[data-gallery-video][src]').count(), 0, 'no video transfer before gallery proximity');
                await galleryReady(page);
                for (const width of [360, 390, 640, 768, 1024, 1280, 1440, 1536, 1920]) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.waitForTimeout(350); // ScrollTrigger's resize refresh must settle before native scroll.
                    const opening = await galleryAt(page, .05);
                    assert.ok(Math.abs(opening.items[0].width - Math.min(width * .8, 900 * .72 / .5625)) < 1);
                    assert.equal(opening.items[0].opacity, 1);
                    assert.ok(opening.items.slice(1).every(i => i.opacity === 0));
                    const entering = await galleryAt(page, .32);
                    assert.ok(entering.items[0].width < opening.items[0].width);
                    const full = await galleryAt(page, .46);
                    galleryFits(full);
                    assert.ok(full.items.every(i => i.opacity === 1));
                    assert.equal(full.triggers, 1);
                    if (width >= 768) {
                        assert.deepEqual(['left', 'center', 'right'].map(c => full.items.filter(i => i.column === c).length), [3, 4, 3]);
                        assert.ok(Math.abs(full.items[0].x + full.items[0].width / 2 - width / 2) < 1);
                        assert.ok(full.items[0].y < opening.items[0].y);
                    }
                    const traveling = await galleryAt(page, .65);
                    galleryFits(traveling);
                    assert.ok(traveling.items.every((i, n) => i.y < full.items[n].y - 10));
                    const exit = await galleryAt(page, .86);
                    assert.ok(exit.items.every(i => i.opacity < 1));
                    const end = await galleryAt(page, 1);
                    assert.ok(end.items.every(i => i.opacity < .01));
                    assert.ok(end.testimonial.y < 900 && end.testimonial.y >= 0);
                    const reverse = await galleryAt(page, .46);
                    assert.ok(reverse.items.every((i, n) => Math.abs(i.x - full.items[n].x) < 1 && Math.abs(i.y - full.items[n].y) < 2));
                    if ([390, 1440].includes(width)) for (const [name, progress] of [['opening', .05], ['entering', .35], ['formation', .55], ['exit', .86], ['handoff', 1]]) {
                        await galleryAt(page, progress);
                        await page.screenshot({ path: `${directory}/gallery-${engine}-${lang}-${width}-${name}.png` });
                    }
                    cases.push({ lang, width, opening, full, traveling, exit, end });
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`gallery-scroll-${engine}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}
