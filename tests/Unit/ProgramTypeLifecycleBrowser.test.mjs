import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, field, phase, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Dense Arabic type keeps the kinetic lifecycle usable in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { hasTouch: true });
        const { page } = runtime;
        const cases = [];
        try {
            await locale(page, 'ar');
            assert.equal(runtime.requests.some(url => /program-(gsap|kinetic)|gsap@/.test(url)), false);
            for (const width of [1440, 768, 390]) {
                await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                await field(page);
                const trigger = page.locator('[data-program-open]').nth(2);
                await trigger.scrollIntoViewIfNeeded();
                const plane = await page.locator('[data-program-type-viewport]').elementHandle();
                const before = await page.evaluate(geometry);
                const scroll = await page.evaluate(() => scrollY);
                if (width < 1024) await trigger.tap();
                else await trigger.click();
                await phase(page, 'opening');
                await page.waitForFunction(() => getComputedStyle(document.querySelector('[data-program-type]')).transform !== 'none');
                const moving = await page.locator('[data-program-type]').evaluate(type => getComputedStyle(type).transform);
                await phase(page, 'detail');
                assert.equal(await page.locator('[data-program]').getAttribute('data-program-animation'), 'kinetic');
                assert.equal(await plane.evaluate(element => element.closest('dialog')?.open), true);
                const detail = await page.evaluate(geometry);
                fits(detail);
                await page.keyboard.press('Tab');
                assert.equal(await page.locator('[data-program-back]:visible').evaluate(back => back === document.activeElement), true);
                await page.waitForFunction(() => {
                    const image = document.querySelector('[data-program-detail-stage] img');
                    return image.complete && image.naturalWidth > 0;
                });
                if (engine === 'chromium') await page.screenshot({ path: `${directory}/program-third-detail-ar-${width}.png` });
                if (width === 1440) await page.locator('[data-program-back]:visible').click();
                else await page.keyboard.press('Escape');
                await phase(page, 'idle');
                assert.equal(await trigger.evaluate(element => element === document.activeElement), true);
                assert.equal(await page.evaluate(() => scrollY), scroll);
                assert.equal(await plane.evaluate(element => element.parentElement.hasAttribute('data-program-type-home')), true);
                assert.deepEqual((await page.evaluate(geometry)).anchors, before.anchors);
                const reset = await page.locator('[data-program-type]').evaluate(type => {
                    const css = getComputedStyle(type.firstElementChild);
                    return { count: type.children.length, leading: parseFloat(css.lineHeight) / parseFloat(css.fontSize),
                        animations: type.getAnimations({ subtree: true }).length, transform: type.style.transform,
                        inlineLines: [...type.children].some(line => line.style.transform || line.style.opacity) };
                });
                assert.equal(reset.count, 20);
                assert.ok(Math.abs(reset.leading - .75) < .001);
                assert.equal(reset.animations, 20, 'idle CSS row drift returns after detail');
                assert.equal(reset.transform, '');
                assert.equal(reset.inlineLines, false);
                await page.mouse.move(width / 2, 450);
                await page.mouse.wheel(0, 80);
                await page.waitForFunction(previous => scrollY > previous, scroll);
                const forward = await page.evaluate(() => scrollY);
                await page.mouse.wheel(0, -80);
                await page.waitForFunction(previous => scrollY < previous, forward);
                cases.push({ width, moving, detail, reset, nativeScroll: true });
            }
            await page.emulateMedia({ reducedMotion: 'reduce' });
            await page.locator('[data-program-open]').first().tap();
            await phase(page, 'detail');
            await page.setViewportSize({ width: 360, height: 740 });
            fits(await page.evaluate(geometry));
            assert.equal(await page.locator('[data-program-dialog]').evaluate(dialog => dialog.getAnimations({ subtree: true }).length), 0);
            await page.keyboard.press('Escape'); await phase(page, 'idle');
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-third-type-lifecycle-${engine}`, { version: runtime.version, cases, reducedResize: true, errors: runtime.errors });
        } finally { await runtime.close(); }
    });
}
