import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, session, locale, field, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

test('Current Program overview retains staircase and localized heading across widths', { skip: !enabled }, async () => {
    const runtime = await session('chromium', { reducedMotion: 'reduce' });
    const { page } = runtime;
    const cases = [];
    try {
        for (const language of ['en', 'id', 'ar']) {
            await locale(page, language);
            for (const width of [390, 768, 1440]) {
                await page.setViewportSize({ width, height: 900 });
                await field(page);
                const state = await page.evaluate(geometry);
                fits(state);
                assert.equal(state.columns, width >= 768 ? 4 : 2);
                assert.equal(state.cards.length, 8);
                if (width >= 768) for (let index = 1; index < 4; index++) assert.ok(state.cards[index].bottom < state.cards[index - 1].bottom);
                await page.evaluate(() => document.activeElement?.blur());
                await page.mouse.move(-20, -20);
                await page.locator('[data-program]').screenshot({ path: `${directory}/program-third-field-${language}-${width}.png` });
                cases.push({ language, width, ...state });
                if (width === 1440) {
                    await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                    await page.waitForFunction(() => document.querySelector('[data-header]').dataset.scrolled === 'false');
                    await page.locator('[data-panel] > summary').first().click();
                    await page.waitForFunction(() => document.querySelector('[data-panel]').dataset.motion === 'open');
                    await page.mouse.move(20, 850);
                    await page.screenshot({ path: `${directory}/program-third-hero-open-${language}.png` });
                    await page.keyboard.press('Escape');
                }
            }
        }
        assert.deepEqual(runtime.errors, []);
        await evidence('program-third-field-review', { version: runtime.version, motion: 'reduced for assembled overview', cases });
    } finally { await runtime.close(); }
});
