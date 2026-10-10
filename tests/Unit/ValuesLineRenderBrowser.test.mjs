import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, directory } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test('The three Values SVG lines paint drawn segments but hide future segments in ' + engine,
        { skip: !enabled }, async () => {
            const runtime = await session(engine);
            try {
                const page = runtime.page;
                await page.evaluate(() => {
                    const root = document.querySelector('[data-values]');
                    scrollTo(0, root.getBoundingClientRect().top + scrollY - innerHeight);
                });
                await page.waitForFunction(() => !!window.ScrollTrigger?.getById('values-line'));
                for (const width of [390, 1440]) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.waitForFunction(w => document.querySelector('[data-values-line] svg').viewBox.baseVal.width === w, width);
                    for (const progress of [.2, .45, .72]) {
                        await page.evaluate(p => {
                            const trigger = window.ScrollTrigger.getById('values-line');
                            scrollTo(0, Math.round(trigger.start + (trigger.end - trigger.start) * p));
                        }, progress);
                        await page.waitForFunction(p => Math.abs(window.ScrollTrigger.getById('values-line').progress - p) < .0015, progress);
                        const result = await page.locator('[data-values-line] svg').evaluate(svg => {
                            return [...svg.querySelectorAll('path')].map(path => {
                                const length = path.getTotalLength();
                                const style = getComputedStyle(path);
                                return {
                                    stroke: style.stroke, fill: style.fill,
                                    dash: parseFloat(style.strokeDasharray),
                                    offset: parseFloat(style.strokeDashoffset),
                                    length,
                                };
                            });
                        });
                        assert.equal(result.length, 3);
                        for (const item of result) {
                            assert.equal(item.stroke, 'rgb(255, 255, 255)');
                            assert.equal(item.fill, 'none');
                            assert.ok(item.dash >= item.length - .01);
                            assert.ok(item.offset >= -.01 && item.offset <= item.length + 2);
                        }
                    }
                    await page.screenshot({ path: `${directory}/values-three-line-paint-${engine}-${width}.png` });
                }
                assert.deepEqual(runtime.errors, []);
            } finally { await runtime.close(); }
        });
}
