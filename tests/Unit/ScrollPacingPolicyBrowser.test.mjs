import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Pacing performs zero heading geometry reads and rechecks nested scroll ownership in ${engine}`, { skip: !enabled }, async () => {
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
                    scrollTo(0, 350);
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
                    return { paced, cachedGeometry, cachedOverflow, empty, nested, explicit };
                } finally {
                    window.getComputedStyle = style;
                    delete heading.getBoundingClientRect;
                    probe.remove();
                }
            });
            assert.ok(result.paced.every(Boolean), JSON.stringify(result));
            assert.equal(result.cachedGeometry, 0);
            assert.equal(result.cachedOverflow, 1);
            assert.equal(result.empty, true);
            assert.equal(result.nested, false);
            assert.equal(result.explicit, false);
            assert.deepEqual(runtime.errors, []);
        } finally { await runtime.close(); }
    });
}
