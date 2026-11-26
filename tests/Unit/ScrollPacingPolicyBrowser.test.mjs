import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Pacing reuses stable geometry and rechecks nested scroll ownership in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        try {
            const { page } = runtime;
            await page.waitForLoadState('load');
            await page.evaluate(() => document.fonts.ready);
            const result = await page.evaluate(() => {
                const root = document.querySelector('main');
                const heading = root.querySelector('[data-program-heading]');
                const probe = document.createElement('div');
                const child = document.createElement('div');
                probe.style.cssText = 'position:fixed;width:200px;height:100px;overflow:hidden';
                probe.append(child);
                document.body.append(probe);
                let geometryReads = 0;
                let overflowReads = 0;
                const rect = heading.getBoundingClientRect.bind(heading);
                heading.getBoundingClientRect = () => { geometryReads++; return rect(); };
                const style = window.getComputedStyle;
                window.getComputedStyle = function (node, ...args) {
                    if (node === probe) overflowReads++;
                    return style.call(this, node, ...args);
                };
                const wheel = deltaY => !child.dispatchEvent(new WheelEvent('wheel', { deltaY, bubbles: true, cancelable: true, composed: true }));
                try {
                    // Alternating deltas force synchronous delivery every turn.
                    scrollTo(0, 350);
                    window.dispatchEvent(new Event('resize'));
                    const before = geometryReads;
                    const paced = Array.from({ length: 10 }, (_, index) => wheel(index % 2 ? -4 : 4));
                    const cachedGeometry = geometryReads - before;
                    const cachedOverflow = overflowReads;
                    probe.style.overflowY = 'auto';
                    const empty = wheel(4);
                    child.style.height = '300px';
                    const nested = wheel(4);
                    probe.setAttribute('data-scroll-native', '');
                    child.style.height = '0px';
                    const explicit = wheel(4);
                    probe.removeAttribute('data-scroll-native');
                    window.dispatchEvent(new Event('resize'));
                    const beforeResizeWheel = geometryReads;
                    const afterResize = wheel(4);
                    return { paced, cachedGeometry, cachedOverflow, empty, nested, explicit, afterResize,
                        resizedGeometry: geometryReads - beforeResizeWheel };
                } finally {
                    window.getComputedStyle = style;
                    delete heading.getBoundingClientRect;
                    probe.remove();
                }
            });
            assert.ok(result.paced.every(Boolean), JSON.stringify(result));
            assert.equal(result.cachedGeometry, 1);
            assert.equal(result.cachedOverflow, 1);
            assert.equal(result.empty, true);
            assert.equal(result.nested, false);
            assert.equal(result.explicit, false);
            assert.equal(result.afterResize, true);
            assert.equal(result.resizedGeometry, 1);
            assert.deepEqual(runtime.errors, []);
        } finally { await runtime.close(); }
    });
}
