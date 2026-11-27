import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const preview = () => {
    const canvas = document.querySelector('.site-footer-v2__white').getBoundingClientRect();
    const image = document.querySelector('[data-footer-visual-image]');
    const style = getComputedStyle(image), rect = image.getBoundingClientRect();
    return { locale: document.documentElement.lang, x: rect.x - canvas.x, width: rect.width,
        canvasWidth: canvas.width, opacity: +style.opacity, filter: style.filter,
        mask: style.maskImage, nextPanels: document.querySelectorAll('[data-footer-next-shell], [data-footer-next-main]').length };
};

for (const engine of engines) {
    test(`Footer Maps preview and clean end without auto-gallery in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const samples = [];
        try {
            for (const language of ['id', 'en', 'ar']) {
                await locale(page, language);
                await page.locator('[data-footer-preview="maps"]').focus();
                await page.waitForFunction(() => {
                    const image = document.querySelector('[data-footer-visual-image]');
                    return image.complete && image.naturalWidth > 0 && getComputedStyle(image).opacity === '1';
                });
                const map = await page.evaluate(preview);
                assert.equal(map.nextPanels, 0);
                assert.equal(map.filter, 'none'); assert.equal(map.mask, 'none'); assert.equal(map.opacity, 1);
                assert.ok(Math.abs(map.width - map.canvasWidth / 2) <= 1);
                assert.ok(Math.abs(map.x - (language === 'ar' ? 0 : map.canvasWidth / 2)) <= 1);
                await page.screenshot({ path: `${directory}/footer-map-${engine}-${language}.png` });
                await page.mouse.move(900, 500);
                await page.mouse.wheel(0, 3000);
                await page.waitForTimeout(300);
                assert.equal(new URL(page.url()).pathname, '/', 'scrolling footer never auto-navigates');
                samples.push({ map });
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`footer-input-${engine}`, { version: runtime.version, samples, errors: runtime.errors });
        } finally { await runtime.close(); }
    });
}
