import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test('Program heading finishes reveal before its independent same-speed inward slide in ' + engine,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            try {
                for (const lang of ['id', 'en', 'ar']) {
                    await locale(runtime.page, lang);
                    for (const width of [390, 1440]) {
                        const page = runtime.page;
                        await page.setViewportSize({ width, height: 900 });
                        // This heading is intentionally one-shot: reload for every viewport.
                        await page.reload({ waitUntil: 'domcontentloaded' });
                        await page.evaluate(() => scrollTo(0, 0));
                        await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.headingState === 'idle');
                        const amount = await page.evaluate(() => {
                            const root = document.querySelector('[data-program-heading]');
                            const last = root.lastElementChild;
                            const sign = parseFloat(getComputedStyle(document.querySelector('[data-program]')).getPropertyValue('--program-heading-sign'));
                            const shift = parseFloat(getComputedStyle(document.querySelector('[data-program]')).getPropertyValue('--program-heading-nudge'));
                            return { two: root.children.length === 2, sign, shift };
                        });
                        await page.evaluate(() => {
                            const heading = document.querySelector('[data-program-heading]');
                            scrollTo(0, heading.getBoundingClientRect().top + scrollY - innerHeight * .35);
                        });
                        await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.headingState === 'revealing');
                        const reveal = await page.locator('[data-program-heading]').evaluate(heading => {
                            const times = heading.getAnimations({ subtree: true }).map(a => a.effect.getTiming().duration);
                            return { times, x: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41 };
                        });
                        assert.ok(reveal.times.every(t => t === 760));
                        if (amount.two) assert.ok(Math.abs(reveal.x) < .1);
                        await page.evaluate(() => scrollTo(0, 0)); // reverse must not reset sequence
                        await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.headingState === 'complete');
                        const result = await page.locator('[data-program-heading]').evaluate(heading => ({
                            state: heading.dataset.headingState,
                            x: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41,
                            firstX: new DOMMatrix(getComputedStyle(heading.firstElementChild).transform).m41,
                            lines: [...heading.querySelectorAll('[data-program-heading-line]')].map(el => ({
                                opacity: getComputedStyle(el).opacity,
                                y: new DOMMatrix(getComputedStyle(el).transform).m42,
                            })),
                        }));
                        assert.ok(result.lines.every(line => line.opacity === '1' && Math.abs(line.y) < .01));
                        assert.equal(result.firstX, 0);
                        if (amount.two) {
                            assert.ok(result.x * (lang === 'ar' ? -1 : 1) > 1, lang + ': lower line moves inward');
                        } else assert.equal(result.x, 0, 'single Arabic line never shifts');
                        assert.equal(result.state, 'complete');
                    }
                }
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        });
}
