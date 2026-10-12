import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const paint = page => page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
const progress = page => page.locator('[data-footer-next]').evaluate(panel => Number(panel.style.getPropertyValue('--footer-progress')));
const step = async (page, pixels) => {
    await page.mouse.wheel(0, pixels);
    await page.waitForTimeout(30);
    return progress(page);
};
const preview = () => {
    const canvas = document.querySelector('.site-footer-v2__white').getBoundingClientRect();
    const image = document.querySelector('[data-footer-visual-image]');
    const style = getComputedStyle(image), rect = image.getBoundingClientRect();
    return { locale: document.documentElement.lang, x: rect.x - canvas.x, width: rect.width,
        canvasWidth: canvas.width, opacity: +style.opacity, filter: style.filter,
        mask: style.maskImage, decoded: image.complete && image.naturalWidth > 0 };
};

for (const engine of engines) {
    test(`Footer Maps side and direct finger input in ${engine}`, { skip: !enabled }, async () => {
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
                assert.equal(map.filter, 'none'); assert.equal(map.mask, 'none'); assert.equal(map.opacity, 1);
                assert.ok(Math.abs(map.width - map.canvasWidth / 2) <= 1);
                assert.ok(Math.abs(map.x - (language === 'ar' ? 0 : map.canvasWidth / 2)) <= 1, 'Maps mirrors position, not photograph');
                await page.screenshot({ path: `${directory}/footer-map-${engine}-${language}.png` });
                await page.evaluate(() => {
                    document.activeElement.blur();
                    const white = document.querySelector('.site-footer-v2__white');
                    scrollTo(0, white.getBoundingClientRect().top + scrollY + white.offsetHeight - innerHeight - 100);
                });
                await paint(page);
                await page.mouse.move(900, 500);
                await page.mouse.wheel(0, 3000);
                await page.waitForFunction(() => document.querySelector('[data-footer-v2]').dataset.footerPhase === 'white-held');
                await page.waitForTimeout(300);
                await page.mouse.wheel(0, 1200);
                await page.waitForFunction(() => document.querySelector('[data-footer-v2]').dataset.footerPhase === 'blue-held');
                assert.equal(await progress(page), 0, 'entering blue does not spend a gesture tail on progress');
                await page.waitForTimeout(300);
                const small = await step(page, 42);
                const larger = await step(page, 84);
                assert.ok(Math.abs(small - .05) <= .0001, `42px fills5%: ${small}`);
                assert.ok(Math.abs(larger - .15) <= .0001, `84px adds10%: ${larger}`);
                const tiny = await step(page, 2.1);
                assert.ok(Math.abs(tiny - .1525) <= .0001, `tiny packets remain proportional: ${tiny}`);
                const visible = await page.locator('.site-footer-v2__next-line-fill').evaluate(element => {
                    const style = getComputedStyle(element);
                    return { scale: new DOMMatrix(style.transform).m11, transition: style.transitionDuration };
                });
                assert.ok(Math.abs(visible.scale - tiny) <= .0001, 'painted fill follows delivered input without another easing clock');
                assert.equal(visible.transition, '0s');
                await page.waitForFunction(() => Number(document.querySelector('[data-footer-next]').style.getPropertyValue('--footer-progress')) === 0);
                assert.equal(await page.locator('[data-footer-v2]').getAttribute('data-footer-phase'), 'blue-held');
                assert.ok(new URL(page.url()).pathname === '/', 'idle decay never navigates');
                await page.mouse.wheel(0, 1000);
                await page.waitForURL('**/galeri');
                samples.push({ map, small, larger, tiny, visible, destination: page.url() });
                await page.goto(process.env.PROGRAM_BROWSER_URL, { waitUntil: 'domcontentloaded' });
            }
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await locale(page, 'ar');
            await page.locator('[data-footer-preview="maps"]').focus(); await paint(page);
            const reduced = await page.evaluate(preview);
            assert.equal(reduced.opacity, 1); assert.equal(reduced.filter, 'none');
            await page.locator('[data-footer-next-main]').focus(); await paint(page);
            await page.mouse.move(900, 750);
            await page.mouse.wheel(0, 1000); await paint(page);
            assert.equal(await progress(page), 0, 'reduced motion retains native scrolling and explicit link access');
            assert.equal(new URL(page.url()).pathname, '/');
            assert.deepEqual(runtime.errors, []);
            await evidence(`footer-input-${engine}`, { version: runtime.version, samples, reduced, errors: runtime.errors });
        } finally { await runtime.close(); }
    });
}
