import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Values heading completes autonomously after slow, fast, stopped and reverse scroll in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const lang of ['id', 'en', 'ar']) {
                for (const pattern of ['stopped', 'fast', 'reverse', 'slow']) {
                    await locale(page, lang);
                    await page.evaluate(pattern => {
                        const heading = document.querySelector('[data-values-heading]');
                        const root = document.querySelector('[data-values]');
                        const top = root.getBoundingClientRect().top + scrollY;
                        window.valuesFrames = [];
                        const started = performance.now();
                        const sample = now => {
                            const state = heading.dataset.headingState;
                            const x = new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41;
                            const lines = [...heading.querySelectorAll('[data-values-heading-line]')].map(line => ({
                                y: new DOMMatrix(getComputedStyle(line).transform).m42, opacity: +getComputedStyle(line).opacity,
                            }));
                            window.valuesFrames.push({ time: now - started, state, x, lines,
                                animations: heading.getAnimations({ subtree: true }).map(a => ({ duration: a.effect.getTiming().duration, time: a.currentTime })) });
                            if (state !== 'complete' && now - started < 5000) requestAnimationFrame(sample);
                        };
                        requestAnimationFrame(sample);
                        scrollTo(0, top);
                    }, pattern);
                    // The scroll scenario begins only after native scrolling has
                    // actually triggered the sequence, including on a busy runner.
                    await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.headingState === 'revealing');
                    await page.evaluate(pattern => {
                        if (pattern === 'fast') scrollBy(0, 3600);
                        if (pattern === 'reverse') scrollTo(0, 0);
                        if (pattern === 'slow') {
                            let count = 0;
                            const move = () => { scrollBy(0, 2); if (++count < 120) requestAnimationFrame(move); };
                            requestAnimationFrame(move);
                        }
                    }, pattern);
                    await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.headingState === 'complete').catch(async error => {
                        const state = await page.evaluate(() => ({ hidden: document.hidden, scroll: scrollY,
                            heading: document.querySelector('[data-values-heading]').dataset.headingState,
                            frames: window.valuesFrames?.slice(-3) }));
                        throw new Error(`${lang}/${pattern}: ${error.message}; ${JSON.stringify(state)}`);
                    });
                    const frames = await page.evaluate(() => window.valuesFrames);
                    const shifted = frames.find(f => Math.abs(f.x) > .01);
                    const reveal = frames.find(f => f.state === 'revealing');
                    assert.ok(shifted && reveal);
                    assert.ok(shifted.time - reveal.time >= 850, 'no horizontal movement during the 900ms reveal');
                    assert.ok(frames.filter(f => Math.abs(f.x) > .01).every(f => f.lines.every(l => Math.abs(l.y) < .01 && l.opacity === 1)));
                    assert.ok(frames.filter(f => f.state === 'shifting').every(f => f.animations.every(a => a.duration === 900)));
                    assert.ok(Math.abs(frames.at(-1).x - (lang === 'ar' ? -100.8 : 100.8)) < .1);
                    await page.evaluate(() => scrollTo(0, 0));
                    assert.equal(await page.locator('[data-values-heading]').getAttribute('data-heading-state'), 'complete');
                    cases.push({ lang, pattern, frames });
                }
            }
            await locale(page, 'id');
            await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY));
            await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.headingState === 'revealing');
            await page.evaluate(() => {
                window.dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true }));
            });
            await page.evaluate(() => Promise.all(document.querySelector('[data-values-heading]').getAnimations({ subtree: true }).map(a => a.ready)));
            const paused = await page.evaluate(() => document.querySelector('[data-values-heading]').getAnimations({ subtree: true }).map(a => a.currentTime));
            await page.waitForTimeout(250);
            assert.deepEqual(await page.evaluate(() => document.querySelector('[data-values-heading]').getAnimations({ subtree: true }).map(a => a.currentTime)), paused);
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.headingState === 'complete');
            await locale(page, 'ar');
            await page.evaluate(() => scrollTo(0, document.querySelector('[data-values]').getBoundingClientRect().top + scrollY));
            await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.headingState === 'revealing');
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await page.waitForFunction(() => document.querySelector('[data-values-heading]').dataset.headingState === 'static');
            assert.equal(await page.locator('[data-values-heading]').evaluate(h => h.getAnimations({ subtree: true }).length), 0);
            await page.emulateMedia({ reducedMotion: 'no-preference' });
            assert.equal(await page.locator('[data-values-heading]').getAttribute('data-heading-state'), 'static', 'preference changes never restart an interrupted sequence');
            assert.deepEqual(runtime.errors, []);
            await evidence(`values-heading-lifecycle-${engine}`, { version: runtime.version, cases, bfcache: 'synthetic pause/resume PASS', reduced: 'PASS' });
        } finally { await runtime.close(); }
    });
}
