import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { galleryReady, galleryAt } from './GalleryBrowserSupport.mjs';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';

async function record(page) {
    await page.evaluate(() => {
        window.pacingFrames = [];
        window.recordPacing = true;
        const sample = now => {
            if (!window.recordPacing) return;
            const values = document.querySelector('[data-values]');
            const track = values.querySelector('[data-values-cards-track]');
            const viewport = values.querySelector('[data-values-cards-viewport]');
            const gallery = window.ScrollTrigger?.getById('gallery-v2');
            window.pacingFrames.push({ now, y: scrollY, gallery: gallery?.progress,
                values: Number(values.dataset.cardsProgress),
                span: track.offsetHeight - viewport.offsetHeight,
                faces: [...values.querySelectorAll('[data-values-card]')].map(c => c.dataset.cardFace),
            });
            requestAnimationFrame(sample);
        };
        requestAnimationFrame(sample);
    });
}
async function stop(page) {
    return page.evaluate(() => { window.recordPacing = false; return window.pacingFrames; });
}
async function burst(page, count, direction = 1) {
    for (let index = 0; index < count; index++) {
        await page.mouse.wheel(0, direction * 10000);
        await page.waitForTimeout(24);
    }
}

for (const engine of engines) test(`aggressive wheel preserves original flip and Gallery phases in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine);
    const { page } = runtime;
    const cases = [];
    try {
        await locale(page, 'id'); await galleryReady(page);
        await settledValuesLayout(page);
        await page.mouse.move(420, 470);
        await page.evaluate(() => {
            const track = document.querySelector('[data-values-cards-track]');
            const root = document.querySelector('[data-values]');
            scrollTo(0, track.getBoundingClientRect().top + scrollY - parseFloat(getComputedStyle(root).scrollMarginBlockStart));
        });
        await record(page); await burst(page, 45); await page.waitForTimeout(250);
        const values = await stop(page);
        assert.ok(values.some(f => f.faces.every(face => face === 'back')));
        assert.ok(values.some(f => f.faces.some(face => face === 'front') && f.faces.some(face => face === 'back')), 'staggered flip is actually painted');
        assert.ok(values.some(f => f.faces.every(face => face === 'front')), 'all card fronts finish');
        const flip = values.filter(f => f.values > .08 && f.values < .48);
        assert.ok(flip.length >= 12, `flip physical paint samples: ${flip.length}`);
        cases.push({ phase: 'values', frames: values });
        for (const [name, start, finish] of [['assembly', null, .52], ['rise', .52, .76], ['exit', .76, .9]]) {
            const p = start ?? +(await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start'));
            await galleryAt(page, p);
            await record(page);
            for (let index = 0; index < 160; index++) {
                await page.mouse.wheel(0, 10000); await page.waitForTimeout(24);
                if (await page.evaluate(end => window.ScrollTrigger.getById('gallery-v2').progress >= end, finish)) break;
            }
            await page.waitForTimeout(250);
            const frames = await stop(page);
            const inside = frames.filter(f => f.gallery > p + .0001 && f.gallery < finish);
            assert.ok(inside.length >= 12, `${name} actual intermediate paints ${inside.length}`);
            assert.ok(frames.at(-1).gallery >= finish, `${name} completes under aggressive input`);
            const beforeReverse = await page.evaluate(() => scrollY);
            await page.mouse.wheel(0, -10000);
            const reverse = await page.evaluate(() => scrollY);
            assert.ok(reverse < beforeReverse, 'direction reverses on the same input turn');
            await page.waitForTimeout(250);
            const stopped = await page.evaluate(() => scrollY);
            await page.waitForTimeout(220);
            assert.ok(Math.abs(await page.evaluate(() => scrollY) - stopped) <= 1, 'tail ends within 180ms');
            cases.push({ phase: name, frames, beforeReverse, reverse, stopped });
        }
        // Original WebKit failure: accepted wheel expired before delayed RAF.
        const stalledBefore = await page.evaluate(() => {
            window.addEventListener('wheel', () => {
                const until = performance.now() + 200;
                while (performance.now() < until) { /* Deliberate test-only paint stall. */ }
            }, { once: true });
            return scrollY;
        });
        await page.mouse.wheel(0, 10000); await page.waitForTimeout(250);
        const stalledAfter = await page.evaluate(() => scrollY);
        assert.ok(stalledAfter > stalledBefore, 'first delivery survives a 200ms paint stall');
        const settled = await page.evaluate(() => scrollY);
        await page.waitForTimeout(200);
        assert.equal(await page.evaluate(() => scrollY), settled, 'stale input never becomes queued autoplay');
        assert.deepEqual(runtime.errors, []);
        await evidence(`scroll-pacing-choreography-${engine}`, { version: runtime.version, cases, stalledBefore, stalledAfter });
    } finally { await runtime.close(); }
});
