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
                nativeGallery: gallery ? (scrollY - gallery.start) / (gallery.end - gallery.start) : null,
                values: Number(values.dataset.cardsProgress),
                span: track.offsetHeight - viewport.offsetHeight,
                words: [...document.querySelectorAll('[data-gallery-word]')].map(w => +getComputedStyle(w).opacity),
                media: [...document.querySelectorAll('[data-gallery-item]')].map(w => +getComputedStyle(w).opacity),
                angles: [...values.querySelectorAll('[data-values-card-inner]')].map(e => parseFloat(e.style.transform.match(/rotateY\(([-\d.]+)/)?.[1] ?? 0)),
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
        await record(page); await burst(page, 45); await page.waitForTimeout(300);
        const values = await stop(page);
        assert.ok(values.some(f => f.faces.every(face => face === 'back')));
        assert.ok(values.some(f => f.faces.some(face => face === 'front') && f.faces.some(face => face === 'back')), 'staggered flip is actually painted');
        assert.ok(values.some(f => f.faces.every(face => face === 'front')), 'all card fronts finish');
        const flip = values.filter(f => f.values > .08 && f.values < .48);
        assert.ok(flip.length >= 5, `flip physical paint samples: ${flip.length}`);
        for (let index = 1; index < values.length; index++) {
            assert.ok(values[index].angles.every((angle, card) => Math.abs(angle - values[index - 1].angles[card]) <= 47), 'painted flip follows <=45deg budget plus native pixel rounding');
        }
        cases.push({ phase: 'values', frames: values });
        for (const [name, start, finish] of [['assembly', null, .52], ['rise', .52, .76], ['exit', .76, .9]]) {
            const p = start ?? +(await page.locator('[data-gallery]').getAttribute('data-gallery-shrink-start'));
            await galleryAt(page, p);
            await record(page);
            for (let index = 0; index < 160; index++) {
                await page.mouse.wheel(0, 10000); await page.waitForTimeout(24);
                if (await page.evaluate(end => window.ScrollTrigger.getById('gallery-v2').progress >= end, finish)) break;
            }
            await page.waitForTimeout(300);
            const frames = await stop(page);
            assert.ok(frames.every(f => f.nativeGallery < 0 || f.nativeGallery > 1 || Math.abs(f.gallery - f.nativeGallery) < .0002), 'GSAP renders delivered native position on the same pacing turn');
            const inside = frames.filter(f => f.gallery > p + .0001 && f.gallery < finish);
            assert.ok(inside.length >= (name === 'rise' ? 4 : 6), `${name} actual intermediate paints ${inside.length}`);
            if (name === 'assembly') assert.ok(inside.some(f => f.words.some(opacity => opacity > 0 && opacity < 1)), 'word opacity actually paints intermediate ink');
            if (name === 'exit') assert.ok(inside.some(f => f.media.every(opacity => opacity > 0 && opacity < 1)), 'all ten media visibly fade before blackout');
            assert.ok(frames.at(-1).gallery >= finish, `${name} completes under aggressive input`);
            const beforeReverse = await page.evaluate(() => scrollY);
            await page.mouse.wheel(0, -10000);
            const reverse = await page.evaluate(() => scrollY);
            assert.ok(reverse < beforeReverse, 'direction reverses on the same input turn');
            await page.waitForTimeout(300);
            const stopped = await page.evaluate(() => scrollY);
            await page.waitForTimeout(220);
            assert.ok(Math.abs(await page.evaluate(() => scrollY) - stopped) <= 1, 'tail ends within 260ms');
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
        await page.mouse.wheel(0, 10000); await page.waitForTimeout(300);
        const stalledAfter = await page.evaluate(() => scrollY);
        assert.ok(stalledAfter > stalledBefore, 'first delivery survives a 200ms paint stall');
        const settled = await page.evaluate(() => scrollY);
        await page.waitForTimeout(200);
        assert.equal(await page.evaluate(() => scrollY), settled, 'stale input never becomes queued autoplay');
        assert.deepEqual(runtime.errors, []);
        await evidence(`scroll-pacing-choreography-${engine}`, { version: runtime.version, cases, stalledBefore, stalledAfter });
    } finally { await runtime.close(); }
});
