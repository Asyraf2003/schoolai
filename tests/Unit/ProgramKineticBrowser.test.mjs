import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, phase, field, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Program kinetic open/Back/Escape and native flow in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const records = [];
        try {
            assert.equal(runtime.requests.some(url => /program-(gsap|kinetic)|gsap@/.test(url)), false, 'no animation preparation during initial load');
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const width of [1440, 768, 390]) {
                    await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                    await field(page);
                    const trigger = page.locator('[data-program-open]').nth(2);
                    await trigger.scrollIntoViewIfNeeded();
                    const before = await page.evaluate(geometry);
                    const typePlane = await page.locator('[data-program-type-viewport]').elementHandle();
                    assert.equal(await typePlane.evaluate(element => element.parentElement.hasAttribute('data-program-type-home')), true);
                    const scroll = await page.evaluate(() => scrollY);
                    await trigger.click();
                    await phase(page, 'opening');
                    await page.waitForFunction(() => {
                        const value = getComputedStyle(document.querySelector('[data-program-type]')).transform;
                        return value !== 'none' && value !== 'matrix(1, 0, 0, 1, 0, 0)';
                    });
                    const kinetic = await page.evaluate(() => ({ type: getComputedStyle(document.querySelector('[data-program-type]')).transform,
                        opacity: [...document.querySelectorAll('[data-program-type-line]')].map(line => Number(getComputedStyle(line).opacity)),
                        cards: [...document.querySelectorAll('[data-program-card]')].map(card => Number(getComputedStyle(card).opacity)) }));
                    assert.equal(await page.locator('[data-program-dialog]').evaluate(dialog => dialog.open), true);
                    assert.equal(await typePlane.evaluate(element => element.closest('dialog')?.open), true, 'same type DOM enters the modal');
                    if (engine === 'chromium' && language === 'en' && width === 1440) await page.screenshot({ path: `${directory}/program-kinetic-open.png` });
                    await phase(page, 'detail');
                    assert.equal(await page.locator('[data-program]').getAttribute('data-program-animation'), 'kinetic');
                    fits(await page.evaluate(geometry));
                    assert.equal(await page.locator('[data-program-back]:visible').evaluate(back => back === document.activeElement), true);
                    await page.keyboard.press('Tab');
                    assert.equal(await page.locator('[data-program-back]:visible').evaluate(back => back === document.activeElement), true);
                    await page.keyboard.press('Shift+Tab');
                    assert.equal(await page.locator('[data-program-back]:visible').evaluate(back => back === document.activeElement), true);
                    await page.evaluate(() => document.querySelector('[data-program-open]').focus());
                    assert.equal(await page.locator('[data-program-back]:visible').evaluate(back => back === document.activeElement), true, 'native modal makes background inert');
                    if (engine === 'chromium' && language === 'en' && width === 1440) {
                        await page.waitForFunction(() => document.querySelector('[data-program-detail-stage] img').complete && document.querySelector('[data-program-detail-stage] img').naturalWidth > 0);
                        await page.screenshot({ path: `${directory}/program-detail-open.png` });
                    }
                    if (width === 1440) await page.locator('[data-program-back]:visible').click();
                    else await page.keyboard.press('Escape');
                    await phase(page, 'idle');
                    assert.equal(await typePlane.evaluate(element => element.parentElement.hasAttribute('data-program-type-home')), true, 'same type DOM returns home');
                    assert.equal(await page.locator('[data-program-type-viewport]').count(), 1);
                    assert.equal(await trigger.evaluate(element => element === document.activeElement), true);
                    assert.equal(await page.evaluate(() => scrollY), scroll, 'close does not force document scroll');
                    const after = await page.evaluate(geometry);
                    assert.deepEqual(after.anchors, before.anchors, 'future anchors stay layout-owned');
                    assert.equal(await page.evaluate(() => [...document.querySelectorAll('[data-program-card]')].every(card => !card.style.transform && !card.style.opacity)), true);
                    records.push({ language, width, kinetic, restored: after });
                    if (language === 'en' && width === 1440) {
                        await page.mouse.move(width / 2, 450);
                        await page.mouse.wheel(0, 120);
                        await page.waitForFunction(previous => scrollY > previous, scroll);
                        const forward = await page.evaluate(() => scrollY);
                        await page.mouse.wheel(0, -120);
                        await page.waitForFunction(previous => scrollY < previous, forward);
                        records.at(-1).nativeWheelAfterClose = true;
                    }
                    console.log(`${engine}: ${language}/${width} kinetic open and close`);
                }
            }
            // Interrupt opening and closing rather than waiting out stale timelines.
            const trigger = page.locator('[data-program-open]').first();
            await trigger.click(); await phase(page, 'opening');
            await page.keyboard.press('Escape'); await phase(page, 'idle');
            await trigger.click(); await phase(page, 'detail');
            await page.locator('[data-program-back]:visible').click(); await phase(page, 'closing');
            await page.emulateMedia({ reducedMotion: 'reduce' }); await phase(page, 'idle');
            await trigger.click(); await phase(page, 'detail');
            await page.setViewportSize({ width: 1024, height: 480 });
            fits(await page.evaluate(geometry));
            await page.keyboard.press('Escape'); await phase(page, 'idle');
            await page.emulateMedia({ reducedMotion: 'no-preference' });
            await trigger.click(); await phase(page, 'detail');
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true })));
            await phase(page, 'idle');
            await page.evaluate(() => window.dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true })));
            await trigger.click(); await phase(page, 'detail');
            await page.keyboard.press('Escape'); await phase(page, 'idle');
            assert.equal(await page.locator('[data-program-card] > [data-program-detail]').count(), 8);
            assert.equal(await page.locator('script[data-program-gsap]').count(), 1);
            await trigger.click(); await phase(page, 'detail');
            await locale(page, 'en'); await phase(page, 'idle');
            assert.equal(await page.evaluate(() => document.documentElement.hasAttribute('data-program-detail-open')), false);
            await locale(page, 'ar'); await locale(page, 'id');
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-feedback-kinetic-${engine}`, { version: runtime.version, motion: 'normal', records, interruption: 'Escape opening, reduce while closing, resize open, persisted page events', errors: runtime.errors });
        } finally { await runtime.close(); }
    });
}

test('Mission texture fades into a modest ordinary-flow Program entry', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    try {
        await page.locator('[data-about-story="mission"]').scrollIntoViewIfNeeded();
        await page.waitForFunction(() => document.querySelector('[data-about]').dataset.activeStory === 'mission');
        await page.waitForFunction(() => getComputedStyle(document.querySelector('[data-about]')).backgroundColor === 'rgb(212, 238, 213)');
        await page.evaluate(() => { const entry = document.querySelector('[data-program-story-start]'); scrollBy(0, entry.getBoundingClientRect().top - innerHeight * .55); });
        const seam = await page.evaluate(() => ({
            height: document.querySelector('[data-program-story-start]').getBoundingClientRect().height,
            viewport: innerHeight, aboutMask: getComputedStyle(document.querySelector('[data-about]'), '::before').maskImage,
            base: getComputedStyle(document.querySelector('[data-about]')).backgroundColor,
            entry: getComputedStyle(document.querySelector('[data-program-story-start]')).backgroundImage,
            overflow: document.documentElement.scrollWidth > innerWidth,
        }));
        assert.ok(seam.height / seam.viewport >= .12 && seam.height / seam.viewport <= .24);
        assert.match(seam.aboutMask, /linear-gradient/);
        assert.equal(seam.overflow, false);
        await page.waitForFunction(() => [...document.querySelectorAll('[data-program-heading-line]')]
            .every(line => getComputedStyle(line).opacity === '1' && getComputedStyle(line).transform === 'none'));
        await page.mouse.move(-20, -20);
        await page.screenshot({ path: `${directory}/program-third-mission-entry.png` });
        await evidence('program-third-seam', { seam, version: runtime.version });
    } finally { await runtime.close(); }
});
