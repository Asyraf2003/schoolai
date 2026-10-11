import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryFits, galleryResize, galleryOpening } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery short screens, resize, native wheel and hidden-page policy in ${engine}`, { skip: !enabled }, async () => {
        const r = await session(engine);
        const cases = [];
        try {
            await galleryReady(r.page);
            for (const width of [360, 639, 640, 767, 768, 1023, 1024, 1279, 1280, 1535, 1536, 2560]) {
                await galleryResize(r.page, width, 500);
                const opening = await galleryOpening(r.page);
                assert.ok(opening.items[0].y >= 0 && opening.items[0].bottom <= 501, 'opening contained on short screen');
                const full = await galleryAt(r.page, .52);
                galleryFits(full);
                await galleryResize(r.page, width, 600);
                const progress = await r.page.locator('[data-gallery]').getAttribute('data-gallery-progress');
                assert.ok(Math.abs(+progress - .52) < .01, `resize preserves scroll progress at ${width}: ${progress}`);
                cases.push({ width, opening, progress });
            }
            await galleryResize(r.page, 1440, 900);
            await galleryAt(r.page, .52);
            await r.page.mouse.wheel(0, -240);
            await r.page.waitForFunction(() => +document.querySelector('[data-gallery]').dataset.galleryProgress < .51);
            await galleryOpening(r.page);
            await r.page.evaluate(() => {
                Object.defineProperty(document, 'hidden', { configurable: true, value: true });
                document.dispatchEvent(new Event('visibilitychange'));
            });
            assert.equal(await r.page.locator('[data-gallery-video][src]').count(), 0);
            await r.page.evaluate(() => {
                delete document.hidden;
                document.dispatchEvent(new Event('visibilitychange'));
            });
            await r.page.waitForFunction(() => document.querySelector('[data-gallery-video][src]'));
            assert.deepEqual(r.errors, []);
            await evidence(`gallery-viewport-${engine}`, { version: r.version, cases, nativeWheel: 'PASS', hiddenPolicy: 'synthetic visibility event PASS' });
        } finally { await r.close(); }
    });
}
