import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, phase, geometry, fits, evidence } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Program text expansion, short height and heading semantics in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { reducedMotion: 'reduce' });
        const { page } = runtime;
        const cells = [];
        const protectedGaps = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const width of [390, 768, 1440]) {
                    await page.setViewportSize({ width, height: 480 });
                    await page.evaluate(() => { document.documentElement.style.fontSize = '200%'; });
                    const trigger = page.locator('[data-program-open]').nth(1);
                    await trigger.focus(); await page.keyboard.press('Enter'); await phase(page, 'detail');
                    fits(await page.evaluate(geometry), 'program');
                    const snapshot = await page.locator('[data-program-dialog]').ariaSnapshot();
                    assert.match(snapshot, /heading/);
                    assert.match(snapshot, /button/);
                    assert.equal(await page.locator('[data-program-back]:visible').isVisible(), true);
                    await page.keyboard.press('Escape'); await phase(page, 'idle');
                    const state = await page.evaluate(geometry);
                    fits(state, 'program');
                    if (state.overflow) {
                        const gap = await page.evaluate(() => {
                            const root = document.querySelector('[data-program]');
                            const documentWidth = document.documentElement.scrollWidth;
                            root.hidden = true;
                            const withoutProgram = document.documentElement.scrollWidth;
                            root.hidden = false;
                            const labels = [...document.querySelectorAll('.about__label')]
                                .map(label => ({ text: label.textContent.trim(), right: label.getBoundingClientRect().right }));
                            return { documentWidth, withoutProgram, labels, viewport: innerWidth };
                        });
                        assert.equal(gap.documentWidth, gap.withoutProgram, 'overflow exists without Program');
                        assert.ok(gap.labels.some(label => label.right > gap.viewport), 'protected About label owns the overflow');
                        protectedGaps.push({ language, width, ...gap });
                    }
                    cells.push({ language, width, expanded: true, ...state });
                    await page.evaluate(() => document.documentElement.style.removeProperty('font-size'));
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-third-access-${engine}`, { version: runtime.version, cases: cells, protectedGaps,
                note: 'Program passes 200% root font expansion. Whole-page zoom has a protected About label gap; no physical zoom or screen-reader certification.' });
        } finally { await runtime.close(); }
    });
}

test('native touch and orientation retain readable Program detail', { skip: !enabled }, async () => {
    const runtime = await session('chromium', { hasTouch: true, viewport: { width: 390, height: 844 } });
    const { page } = runtime;
    try {
        const trigger = page.locator('[data-program-open]').first();
        await trigger.tap(); await phase(page, 'detail');
        fits(await page.evaluate(geometry));
        await page.setViewportSize({ width: 844, height: 390 });
        fits(await page.evaluate(geometry));
        await page.locator('[data-program-back]:visible').tap(); await phase(page, 'idle');
        assert.equal(await trigger.evaluate(element => element === document.activeElement), true);
        await evidence('program-touch', { emulatedTouch: true, orientations: [[390, 844], [844, 390]], state: await page.evaluate(geometry) });
        assert.deepEqual(runtime.errors, []);
    } finally { await runtime.close(); }
});
