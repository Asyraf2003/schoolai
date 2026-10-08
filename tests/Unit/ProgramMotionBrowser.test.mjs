import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Program ID waits for complete lines, then releases its weighted slide in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        try {
            await locale(page, 'id');
            await page.locator('[data-hero]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'false');
            await page.waitForFunction(() => [...document.querySelectorAll('[data-program-heading-line]')].every(line => getComputedStyle(line).opacity === '0'));
            await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'true');
            const frames = await page.locator('[data-program-heading]').evaluate(heading => {
                const animations = heading.getAnimations({ subtree: true });
                animations.forEach(animation => animation.pause());
                const clip = heading.lastElementChild;
                const samples = [];
                for (const time of [0, 380, 760, 1060, 1360, 1660, 1960]) {
                    animations.forEach(animation => { animation.currentTime = time; });
                    samples.push({ time, x: new DOMMatrix(getComputedStyle(clip).transform).m41,
                        lines: [...heading.querySelectorAll('[data-program-heading-line]')].map(line => ({
                            y: new DOMMatrix(getComputedStyle(line).transform).m42, opacity: Number(getComputedStyle(line).opacity),
                        })) });
                }
                return samples;
            });
            assert.equal(frames[0].x, 0);
            assert.equal(frames[1].x, 0, 'no horizontal movement while the split is revealing');
            assert.equal(frames[2].x, 0, 'the complete two-line pose precedes horizontal movement');
            assert.ok(frames[2].lines.every(line => Math.abs(line.y) < .01 && line.opacity === 1));
            const distance = frames.at(-1).x;
            assert.ok(Math.abs(distance - 187.2) < .01);
            assert.ok(frames[3].x / distance < .1, 'first quarter remains weighted');
            assert.ok(frames[4].x / distance < .3, 'first half holds most of the travel');
            assert.ok(frames[5].x - frames[4].x > 3 * (frames[3].x - frames[2].x), 'release accelerates clearly');
            await page.screenshot({ path: `${directory}/program-motion-id-final.png` });
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-motion-sequence-${engine}`, { version: runtime.version, frames });
        } finally { await runtime.close(); }
    });

    test(`Program background alternates rows at twice the speed with its idle lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        const tiers = [];
        try {
            // Exercise the existing usable detail fallback without requiring a CDN.
            await page.route('**/gsap@3.7.1/**', route => route.abort());
            for (const language of ['id', 'en', 'ar']) {
                await locale(page, language);
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programBackgroundActive === 'true');
                let drift;
                for (const width of [360, 640, 768, 1024, 1280, 1440, 1536]) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.waitForTimeout(60);
                    drift = await page.locator('[data-program-type]').evaluate(type => {
                        const animations = type.getAnimations({ subtree: true });
                        const states = animations.map(animation => animation.playState);
                        const durations = animations.map(animation => animation.effect.getTiming().duration);
                        animations.forEach(animation => { animation.currentTime = 1125; });
                        const first = [...type.children].map(line => parseFloat(getComputedStyle(line).translate));
                        animations.forEach(animation => { animation.currentTime = 3375; });
                        const last = [...type.children].map(line => parseFloat(getComputedStyle(line).translate));
                        return { count: animations.length, states, durations, first, last, lines: type.children.length,
                            planes: document.querySelectorAll('[data-program-type]').length,
                            overflow: document.documentElement.scrollWidth > innerWidth };
                    });
                    assert.equal(drift.planes, 1, 'one shared Program/Values background plane');
                    assert.equal(drift.count, 20, 'CSS drives the existing rows');
                    assert.equal(drift.lines, 20);
                    assert.ok(drift.states.every(state => state === 'running'));
                    assert.ok(drift.durations.every(duration => duration === 4500), 'same travel in half the baseline time');
                    assert.ok(drift.first.every((first, i) => (drift.last[i] - first) * (i % 2 ? -1 : 1) > Math.min(30, width * .02)),
                        'neighboring rows travel in opposite physical directions');
                    assert.equal(drift.overflow, false);
                    tiers.push({ language, width, drift });
                }
                await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programBackgroundActive === 'false');
                assert.ok(await page.locator('[data-program-type]').evaluate(type => type.getAnimations({ subtree: true }).every(a => a.playState === 'paused')));
                await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programBackgroundActive === 'true');
                // A synthetic visibility event isolates this adapter's hidden-tab policy.
                await page.evaluate(() => {
                    Object.defineProperty(document, 'hidden', { configurable: true, value: true });
                    document.dispatchEvent(new Event('visibilitychange'));
                });
                assert.ok(await page.locator('[data-program-type]').evaluate(type => type.getAnimations({ subtree: true }).every(a => a.playState === 'paused')));
                await page.evaluate(() => { delete document.hidden; document.dispatchEvent(new Event('visibilitychange')); });
                await page.locator('[data-program-open]').first().click();
                await page.waitForFunction(() => document.querySelector('[data-program-dialog]').open);
                assert.equal(await page.locator('[data-program-type]').evaluate(type => ({
                    animations: type.getAnimations({ subtree: true }).length,
                    staticRows: [...type.children].every(line => getComputedStyle(line).translate === 'none'),
                })).then(state => state.animations === 0 && state.staticRows), true, 'ambient drift cannot alter detail choreography');
                await page.keyboard.press('Escape');
                await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'idle');
                await page.emulateMedia({ reducedMotion: 'reduce' });
                assert.equal(await page.locator('[data-program-type]').evaluate(type => type.getAnimations({ subtree: true }).length), 0);
                await page.emulateMedia({ reducedMotion: 'no-preference' });
                cases.push({ language, drift, offscreenPaused: true, syntheticHiddenPaused: true, detailStatic: true, reducedStatic: true });
            }
            assert.equal(runtime.requests.some(url => /program-(kinetic|gsap)|gsap@/.test(url)), true, 'detail uses its existing intent-only adapter');
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-motion-background-${engine}`, { version: runtime.version, cases, tiers });
        } finally { await runtime.close(); }
    });
}
