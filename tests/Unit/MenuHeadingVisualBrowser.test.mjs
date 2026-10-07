import test from 'node:test';
import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';
import { headingInk, menuState, startMenuFrames, whitePixels } from './MenuHeadingVisualSupport.mjs';

const stage = process.env.PROGRAM_VISUAL_PHASE ?? 'after';
const settled = page => page.waitForFunction(() => [...document.querySelectorAll('[data-panel]')].every(group => !['opening', 'closing'].includes(group.dataset.motion)));

for (const engine of engines) {
    test(`Bounded menu surface and heading ink ${stage} in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const menus = [], headings = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                await page.setViewportSize({ width: 1440, height: 900 });
                await page.waitForFunction(() => [...document.querySelectorAll('.site-header__media')].every(frame => frame.dataset.mediaState === 'ready'));
                const trigger = page.locator('[data-panel] > summary').first();
                const states = [];
                const capture = async name => {
                    const state = await page.evaluate(menuState);
                    const pixels = state.panelTop === null ? null : whitePixels(await page.screenshot({
                        clip: { x: 0, y: state.bottom - 3, width: 1440, height: 6 }, animations: 'allow',
                    }));
                    if (stage !== 'before') {
                        const light = name.includes('open') || name.startsWith('scrolled');
                        assert.ok(state.colors.every(color => color === (light ? 'rgb(17, 17, 17)' : 'rgb(255, 255, 255)')), JSON.stringify({ name, state }));
                        assert.equal(state.surface, light ? 'light' : 'hero');
                        assert.equal(state.height, 72); assert.equal(state.logo, 38, 'current1440px logo scale retained');
                        if (pixels) {
                            assert.equal(state.panelTop, state.bottom, 'panel begins at actual Header bottom');
                            assert.equal(state.panelWidth, 1440);
                            assert.equal(state.panelFill, state.headerFill);
                            assert.equal(pixels.white, pixels.total, 'painted6px join has no transparent/colored pixel');
                            assert.equal(state.fieldTransform, 'none'); assert.equal(state.panelTransform, 'none');
                        }
                    }
                    states.push({ name, state, pixels });
                    if (engine === 'chromium') await page.screenshot({ path: `${directory}/menu-heading-${stage}-${language}-${name}.png` });
                };
                await capture('hero-closed');
                await trigger.hover(); await capture('hero-hover');
                await page.evaluate(startMenuFrames);
                await trigger.click(); await settled(page);
                await page.mouse.move(-20, -20); await capture('hero-open');
                await page.keyboard.press('Escape'); await settled(page); await capture('hero-close');
                const motion = await page.evaluate(() => { window.menuProofRunning = false; return window.menuProofFrames; });
                if (stage !== 'before') for (const frame of motion) {
                    assert.equal(frame.color, frame.fill === 'rgb(255, 255, 255)' ? 'rgb(17, 17, 17)' : 'rgb(255, 255, 255)');
                    if (frame.panelTop !== null) {
                        assert.equal(frame.panelTop, frame.bottom); assert.equal(frame.top, 0);
                        assert.equal(frame.fill, 'rgb(255, 255, 255)');
                        assert.equal(frame.panelTransform, 'none'); assert.equal(frame.fieldTransform, 'none');
                    }
                }
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.mouse.wheel(0, -100);
                await page.waitForFunction(() => document.querySelector('[data-header]').dataset.concealed === 'false');
                await capture('scrolled');
                await trigger.click(); await settled(page); await page.mouse.move(-20, -20); await capture('scrolled-open');
                await page.mouse.move(1400, 800); await page.mouse.wheel(0, 300);
                await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
                assert.equal(await page.locator('[data-header]').getAttribute('data-concealed'), 'false');
                await page.keyboard.press('Escape'); await settled(page);
                await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                await trigger.click(); await trigger.click(); await trigger.click(); await settled(page);
                await page.mouse.move(-20, -20); await capture('rapid-open');
                await page.keyboard.press('Escape'); await settled(page);
                menus.push({ language, states, motion });
                if (stage === 'menu') continue;
                await page.emulateMedia({ reducedMotion: 'reduce' });
                for (const width of [390, 768, 1440, 1920]) {
                    await page.setViewportSize({ width, height: 900 });
                    const ink = await page.evaluate(headingInk);
                    assert.equal(ink.overflow, false);
                    if (stage === 'after') {
                        const before = JSON.parse(await fs.readFile(`${directory}/menu-heading-before-chromium.json`, 'utf8'))
                            .headings.find(item => item.language === language && item.width === width);
                        for (let index = 0; index < ink.lines.length; index++) {
                            assert.ok(Math.abs(ink.lines[index].size - before.lines[index].size) < .1, 'same type scale within engine subpixel serialization');
                            assert.equal(ink.lines[index].weight, before.lines[index].weight);
                            assert.equal(ink.lines[index].family.replaceAll('"', ''), before.lines[index].family.replaceAll('"', ''));
                        }
                        if (language !== 'ar') assert.ok(ink.gap / before.gap > .28 && ink.gap / before.gap < .39, JSON.stringify({ ink, before }));
                        else assert.ok(Math.abs(parseFloat(ink.lines[0].leading) / ink.lines[0].size - 1.35) < .001);
                        assert.ok(ink.lines.every(line => line.top >= line.maskTop - 1 && line.bottom <= line.maskBottom + 1), 'resting glyph ink fits the reveal mask');
                    }
                    headings.push(ink);
                }
                await page.setViewportSize({ width: 1440, height: 900 });
                await page.emulateMedia({ reducedMotion: 'no-preference' });
                await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'false');
                await page.evaluate(() => { const heading = document.querySelector('[data-program-heading]'); scrollBy(0, heading.getBoundingClientRect().top - innerHeight * .94); });
                await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'false');
                if (engine === 'chromium') await page.screenshot({ path: `${directory}/menu-heading-${stage}-${language}-hidden.png` });
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'true');
                const duration = await page.evaluate(headingInk);
                if (stage === 'after') {
                    assert.equal(duration.lines[0].duration, '0.76s, 0.76s');
                    assert.equal(duration.lines.at(-1).shiftDelay, '0s');
                }
                if (engine === 'chromium') await page.screenshot({ path: `${directory}/menu-heading-${stage}-${language}-entering.png`, animations: 'allow' });
                await page.waitForFunction(() => document.querySelector('[data-program-heading]').getAnimations({ subtree: true }).length === 0);
                assert.equal(await page.locator('[data-panel][open]').count(), 0, 'no menu surface persists behind Program');
                if (engine === 'chromium') await page.screenshot({ path: `${directory}/menu-heading-${stage}-${language}-settled.png` });
                headings.push({ language, width: 1440, normalTiming: duration.lines.map(line => ({ duration: line.duration, shiftDuration: line.shiftDuration, shiftDelay: line.shiftDelay })) });
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`menu-heading-${stage}-${engine}`, { version: runtime.version, menus, headings });
        } finally { await runtime.close(); }
    });
}
