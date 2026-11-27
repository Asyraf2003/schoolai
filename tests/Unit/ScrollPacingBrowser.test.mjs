import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';
import { settledValuesLayout } from './ValuesBrowserSupport.mjs';
import { SCROLL_IDLE_CUTOFF_MS, SCROLL_INPUT_GAIN } from '../../resources/js/sections/scroll-pacing-core.js';

for (const engine of engines) {
    test(`homepage wheel scroll delivers uniform 0.85x speed and smooth exponential inertia in ${engine}`,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            const { page } = runtime;
            const result = [];
            try {
                for (const lang of ['id', 'en', 'ar']) {
                    await locale(page, lang);
                    await settledValuesLayout(page);
                    await page.mouse.move(420, 470);
                    const sectionDistances = [];
                    for (const selector of ['[data-hero]', '[data-values]', '[data-gallery]', '[data-testimonials]', '[data-stories]']) {
                        await page.evaluate(sel => {
                            const node = document.querySelector(sel);
                            const offset = sel === '[data-gallery]' ? node.offsetHeight * .45 : 180;
                            scrollTo(0, Math.max(0, node.getBoundingClientRect().top + scrollY + offset));
                        }, selector);
                        await page.waitForTimeout(80);
                        const before = await page.evaluate(() => scrollY);
                        await page.mouse.wheel(0, 200);
                        await page.waitForTimeout(SCROLL_IDLE_CUTOFF_MS + 100);
                        const after = await page.evaluate(() => scrollY);
                        const moved = after - before;
                        assert.ok(Math.abs(moved - 200 * SCROLL_INPUT_GAIN) < 6, `${lang}/${selector} moved ${moved}`);
                        sectionDistances.push({ selector, moved });
                    }
                    const spread = Math.max(...sectionDistances.map(s => s.moved)) - Math.min(...sectionDistances.map(s => s.moved));
                    assert.ok(spread < 6, `uniform top-to-bottom distance spread=${spread}`);

                    await page.mouse.wheel(0, -400);
                    await page.waitForTimeout(280);
                    const reversed = await page.evaluate(() => scrollY);
                    assert.ok(reversed < (await page.evaluate(() => scrollY)) + 1);

                    await page.emulateMedia({ reducedMotion: 'reduce' });
                    await settledValuesLayout(page);
                    const reducedBefore = await page.evaluate(() => {
                        scrollTo(0, 350);
                        window.reducedWheelPrevented = null;
                        window.addEventListener('wheel', event => { window.reducedWheelPrevented = event.defaultPrevented; }, { once: true });
                        return scrollY;
                    });
                    await page.mouse.wheel(0, 750);
                    await page.waitForFunction(before => scrollY - before > 160, reducedBefore);
                    const reducedAfter = await page.evaluate(() => scrollY);
                    assert.equal(await page.evaluate(() => window.reducedWheelPrevented), false);
                    await page.emulateMedia({ reducedMotion: 'no-preference' });
                    await settledValuesLayout(page);
                    result.push({ lang, sectionDistances, reduced: reducedAfter - reducedBefore });
                }
                await page.evaluate(() => scrollTo(0, 350));
                await settledValuesLayout(page);
                await page.mouse.move(700, 400);
                await page.evaluate(() => {
                    window.glideSample = new Promise(resolve => {
                        let glideStart;
                        window.addEventListener('wheel', () => { glideStart = scrollY; }, { capture: true, once: true });
                        window.addEventListener('wheel', () => {
                            const impulseEnd = scrollY;
                            const glideFrames = [];
                            const sample = () => {
                                glideFrames.push(scrollY);
                                if (glideFrames.length === 8) resolve({ glideStart, impulseEnd, glideFrames });
                                else requestAnimationFrame(sample);
                            };
                            requestAnimationFrame(sample);
                        }, { once: true });
                    });
                });
                await page.mouse.wheel(0, 200);
                const { glideStart, impulseEnd, glideFrames } = await page.evaluate(() => window.glideSample);
                assert.ok(glideFrames.at(-1) > impulseEnd, 'released input smoothly decays via inertia');
                const deltas = glideFrames.slice(1).map((y, idx) => y - glideFrames[idx]);
                assert.ok(deltas.every((d, i) => !i || d <= deltas[i - 1] + 0.5), 'inertia decelerates smoothly');
                await page.waitForTimeout(SCROLL_IDLE_CUTOFF_MS + 100);
                const total = (await page.evaluate(() => scrollY)) - glideStart;
                assert.ok(Math.abs(total - 200 * SCROLL_INPUT_GAIN) < 5, `glide settles at 0.85x (${total})`);

                await page.evaluate(() => {
                    scrollTo(0, 350);
                    document.body.insertAdjacentHTML('beforeend', '<div id="pacing-scrollbox" data-scroll-native style="position:fixed;top:100px;left:100px;width:240px;height:100px;overflow:auto;z-index:999999;background:white"><div style="height:900px"></div></div>');
                });
                await page.mouse.move(190, 145);
                const nativeBefore = await page.evaluate(() => scrollY);
                await page.mouse.wheel(0, 520);
                await page.waitForTimeout(160);
                const nested = await page.evaluate(() => ({
                    inner: document.getElementById('pacing-scrollbox').scrollTop,
                    outer: scrollY,
                }));
                assert.ok(nested.inner > 150, 'inner scrolling remains browser-native');
                assert.ok(Math.abs(nested.outer - nativeBefore) < 2, 'nested scroll never hijacked');
                assert.deepEqual(runtime.errors, []);
                await evidence(`scroll-pacing-${engine}`, { version: runtime.version, result, nested, glideTotal: total });
            } finally { await runtime.close(); }
        });
}
