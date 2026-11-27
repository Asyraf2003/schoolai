import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt } from './GalleryBrowserSupport.mjs';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';
import { SCROLL_IDLE_CUTOFF_MS } from '../../resources/js/sections/scroll-pacing-core.js';

async function burst(page, count, delta = 180) {
    for (let index = 0; index < count; index++) {
        await page.mouse.wheel(0, delta);
        await page.waitForTimeout(24);
    }
}

for (const engine of engines) test(`uniform wheel pacing drives Values, Gallery 80% exit, and 100svh Stories in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine);
    const { page } = runtime;
    const cases = [];
    try {
        await locale(page, 'id');
        await galleryReady(page);
        await settledValuesLayout(page);
        await page.mouse.move(420, 470);
        await page.evaluate(() => {
            const track = document.querySelector('[data-values-cards-track]');
            const root = document.querySelector('[data-values]');
            scrollTo(0, track.getBoundingClientRect().top + scrollY - parseFloat(getComputedStyle(root).scrollMarginBlockStart));
        });
        await burst(page, 24, 180);
        await page.waitForTimeout(400);
        const valuesDone = await page.evaluate(() => document.querySelector('[data-values]').dataset.cardsProgress);
        assert.ok(Number(valuesDone) > .2, `Values advanced smoothly (${valuesDone})`);
        cases.push({ phase: 'values', valuesDone });

        const formation = +await page.locator('[data-gallery]').getAttribute('data-gallery-formation');
        const exitPinned = +await page.locator('[data-gallery]').getAttribute('data-gallery-exit-pinned');
        await galleryAt(page, formation);
        for (let index = 0; index < 80; index++) {
            await page.mouse.wheel(0, 180);
            await page.waitForTimeout(24);
            if (await page.evaluate(end => window.ScrollTrigger.getById('gallery-v2').progress >= end, exitPinned)) break;
        }
        await page.waitForTimeout(300);
        const galleryAfter = await page.evaluate(() => ({
            progress: window.ScrollTrigger.getById('gallery-v2').progress,
            testimonialsY: document.querySelector('[data-testimonials]').getBoundingClientRect().top,
        }));
        assert.ok(galleryAfter.progress >= exitPinned, `Gallery reaches 80% exit (${galleryAfter.progress})`);
        cases.push({ phase: 'gallery-exit', galleryAfter });

        const storiesTravel = await page.evaluate(() => parseFloat(getComputedStyle(document.querySelector('[data-stories-sequence]')).getPropertyValue('--stories-travel')));
        assert.ok(storiesTravel >= 3 * 900 - 2, `Stories travel uses >= 100svh per chapter (${storiesTravel})`);

        const stalledBefore = await page.evaluate(() => {
            window.addEventListener('wheel', () => {
                const until = performance.now() + 200;
                while (performance.now() < until) { /* Deliberate test-only paint stall. */ }
            }, { once: true });
            return scrollY;
        });
        await page.mouse.wheel(0, 400);
        await page.waitForTimeout(300);
        const stalledAfter = await page.evaluate(() => scrollY);
        assert.ok(stalledAfter > stalledBefore, 'first delivery survives a 200ms paint stall');
        await page.waitForTimeout(SCROLL_IDLE_CUTOFF_MS);
        const settled = await page.evaluate(() => scrollY);
        await page.waitForTimeout(200);
        assert.equal(await page.evaluate(() => scrollY), settled, 'stale input never becomes queued autoplay');
        assert.deepEqual(runtime.errors, []);
        await evidence(`scroll-pacing-choreography-${engine}`, { version: runtime.version, cases, storiesTravel, stalledBefore, stalledAfter });
    } finally { await runtime.close(); }
});
