import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, phase, field, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

const widths = [360, 390, 520, 639, 640, 700, 767, 768, 900, 1023, 1024, 1180, 1181, 1279, 1280, 1440, 1535, 1536, 1920];
for (const engine of engines) {
    test(`Program fluid geometry, localized detail and reduced motion in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { reducedMotion: 'reduce' });
        const { page } = runtime;
        const cells = [];
        const details = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const width of widths) {
                    await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                    await field(page);
                    const state = await page.evaluate(geometry);
                    fits(state);
                    assert.equal(state.columns, width >= 768 ? 4 : 2);
                    if (width >= 768) {
                        for (let index = 1; index < 4; index++) {
                            assert.ok(state.cards[index].bottom < state.cards[index - 1].bottom, 'ascending staircase');
                            assert.ok(language === 'ar' ? state.cards[index].x < state.cards[index - 1].x : state.cards[index].x > state.cards[index - 1].x, 'logical semantic order');
                        }
                    }
                    const visible = await page.locator('[data-program-open]').evaluateAll(items => items.every(item => getComputedStyle(item.firstElementChild).opacity === '1'));
                    assert.equal(visible, true);
                    cells.push({ language, width, ...state });
                    if ([390, 768, 1024, 1440, 1920].includes(width)) {
                        for (let index = 0; index < 8; index++) {
                            const trigger = page.locator('[data-program-open]').nth(index);
                            await trigger.click();
                            await phase(page, 'detail');
                            const detail = await page.evaluate(geometry);
                            fits(detail);
                            assert.equal(await page.locator('[data-program-dialog]').evaluate(dialog => dialog.open), true);
                            assert.equal(await page.locator('[data-program-detail-stage] article').count(), 1);
                            details.push({ language, width, index, ...detail.detail });
                            await page.keyboard.press('Escape');
                            await phase(page, 'idle');
                            assert.equal(await trigger.evaluate(element => element === document.activeElement), true);
                            assert.equal(await page.locator('[data-program-dialog]').evaluate(dialog => dialog.open), false);
                        }
                    }
                    if (engine === 'chromium' && ((language === 'en' && [390, 768, 1440].includes(width)) || (language === 'ar' && width === 1440))) {
                        await field(page);
                        await page.evaluate(() => document.activeElement?.blur());
                        await page.mouse.move(-20, -20);
                        await page.locator('[data-program]').screenshot({ path: `${directory}/program-${language}-${width}.png` });
                        if (language === 'en' && width === 1440) {
                            await page.locator('[data-program-header]').screenshot({ path: `${directory}/program-heading.png` });
                            await page.locator('[data-program-cards]').screenshot({ path: `${directory}/program-desktop-field.png` });
                        }
                    }
                }
                console.log(`${engine}: ${language} geometry and 40 selected details checked`);
            }
            assert.equal(runtime.requests.some(request => /program-(gsap|kinetic)|gsap@/.test(request)), false, 'reduced motion never prepares detail animation');
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-responsive-${engine}`, { version: runtime.version, motion: 'reduced', cells, details, errors: runtime.errors, requests: runtime.requests });
        } finally { await runtime.close(); }
    });
}
