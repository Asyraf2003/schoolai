import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt, galleryFits } from './GalleryBrowserSupport.mjs';

for (const engine of engines) {
    test(`Gallery short screens, resize, keyboard and hidden-page policy in ${engine}`, { skip: !enabled }, async () => {
        const r = await session(engine);
        const cases = [];
        try {
            await galleryReady(r.page);
            for (const width of [360, 639, 640, 767, 768, 1023, 1024, 1279, 1280, 1535, 1536, 2560]) {
                await r.page.setViewportSize({ width, height: 500 });
                await r.page.waitForTimeout(350);
                const opening = await galleryAt(r.page, .05);
                assert.ok(opening.items[0].y >= 0 && opening.items[0].bottom <= 501, 'opening contained on short screen');
                const full = await galleryAt(r.page, .46);
                galleryFits(full);
                await r.page.setViewportSize({ width, height: 600 });
                await r.page.waitForTimeout(350);
                const progress = await r.page.locator('[data-gallery]').getAttribute('data-gallery-progress');
                assert.ok(Math.abs(+progress - .46) < .01, `resize preserves scroll progress at ${width}: ${progress}`);
                cases.push({ width, opening, progress });
            }
            await r.page.setViewportSize({ width: 1440, height: 900 });
            await r.page.waitForTimeout(350);
            await galleryAt(r.page, .05);
            const button = r.page.locator('[data-gallery-pause]');
            await button.focus();
            await r.page.keyboard.press('Space');
            assert.equal(await button.getAttribute('aria-pressed'), 'true');
            assert.equal(await r.page.locator('[data-gallery-video][src]').count(), 0);
            await r.page.keyboard.press('Space');
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
            await evidence(`gallery-viewport-${engine}`, { version: r.version, cases, keyboard: 'PASS', hiddenPolicy: 'synthetic visibility event PASS' });
        } finally { await r.close(); }
    });
}
