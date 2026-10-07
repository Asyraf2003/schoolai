import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Program ID reveals both lines before its weighted1200ms slide in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const width of [390, 768, 1440]) {
                    await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                    await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                    await page.waitForFunction(() => [...document.querySelectorAll('[data-program-heading-line]')]
                        .every(line => getComputedStyle(line).opacity === '0'));
                    await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                    await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'true');
                    const before = await page.evaluate(() => {
                        const clip = document.querySelector('.program__heading-clip:last-child');
                        return { x: new DOMMatrix(getComputedStyle(clip).transform).m41, scroll: scrollY };
                    });
                    await page.waitForFunction(() => [...document.querySelectorAll('[data-program-heading-line]')]
                        .every(line => Math.abs(new DOMMatrix(getComputedStyle(line).transform).m42) < .1 && getComputedStyle(line).opacity === '1'));
                    const middle = await page.locator('.program__heading-clip').last().evaluate(clip => new DOMMatrix(getComputedStyle(clip).transform).m41);
                    if (language === 'id') assert.ok(Math.abs(middle) < .5, 'both lines finish revealing before horizontal travel');
                    await page.waitForFunction(() => document.querySelector('.program__heading-clip:last-child').getAnimations().length === 0);
                    const after = await page.evaluate(() => ({
                        x: new DOMMatrix(getComputedStyle(document.querySelector('.program__heading-clip:last-child')).transform).m41,
                        firstX: new DOMMatrix(getComputedStyle(document.querySelector('.program__heading-clip')).transform).m41,
                        scroll: scrollY,
                    }));
                    const expected = { 390: 9.75, 768: 19.2, 1440: 46.8 }[width] * ({ id: 4, en: 2, ar: 0 }[language]);
                    assert.ok(Math.abs(after.x - expected) < .01, 'owner distance factors ID4/EN2/AR0');
                    if (language !== 'ar') assert.ok(Math.abs(after.x) >= Math.abs(before.x), 'inward pose forms during the section entrance');
                    else assert.equal(middle, 0, 'Arabic has no horizontal animation');
                    const duration = await page.locator('.program__heading-clip').last().evaluate(clip => getComputedStyle(clip).transitionDuration);
                    assert.equal(duration, language === 'id' ? '1.2s' : '0.76s');
                    const vertical = await page.locator('[data-program-heading-line]').evaluateAll(lines => lines.map(line => ({
                        duration: getComputedStyle(line).transitionDuration,
                        easing: getComputedStyle(line).transitionTimingFunction,
                    })));
                    assert.ok(vertical.every(line => line.duration === '0.76s, 0.76s'));
                    assert.ok(vertical.every(line => line.easing === 'cubic-bezier(0.22, 1, 0.36, 1), cubic-bezier(0.22, 1, 0.36, 1)'));
                    assert.equal(await page.locator('.program__heading-clip').last().evaluate(clip => getComputedStyle(clip).transitionTimingFunction),
                        language === 'id' ? 'cubic-bezier(0.82, 0, 0.55, 1)' : 'cubic-bezier(0.22, 1, 0.36, 1)');
                    assert.equal(await page.locator('.program__heading-clip').last().evaluate(clip => getComputedStyle(clip).transitionDelay), language === 'id' ? '0.76s' : '0s');
                    if (language !== 'ar') assert.equal(after.firstX, 0);
                    assert.equal(after.scroll, before.scroll, 'autonomous CSS motion does not own scroll');
                    fits(await page.evaluate(geometry));
                    cases.push({ language, width, before, middle, after, horizontalDuration: duration, vertical });
                    if (engine === 'chromium' && width === 1440) await page.screenshot({ path: `${directory}/menu-heading-heading-${language}.png` });
                    await page.emulateMedia({ reducedMotion: 'reduce' });
                    assert.equal(await page.locator('.program__heading-clip').last().evaluate(clip => clip.getAnimations().length), 0);
                    await page.emulateMedia({ reducedMotion: 'no-preference' });
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`menu-heading-heading-${engine}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}

test('Program heading keeps a readable final pose without page JavaScript', { skip: !enabled }, async () => {
    const cases = [];
    for (const engine of engines.filter(engine => ['chromium', 'webkit'].includes(engine))) {
        const runtime = await session(engine, { javaScriptEnabled: false });
        const { page } = runtime;
        try {
            for (const language of ['en', 'id', 'ar']) {
                await Promise.all([
                    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
                    page.locator(`form[action$="/bahasa/${language}"]`).evaluate(form => form.requestSubmit()),
                ]);
                await page.evaluate(() => document.fonts.ready);
                for (const width of [390, 768, 1440]) {
                    await page.setViewportSize({ width, height: 900 });
                    const state = await page.locator('[data-program-heading]').evaluate(heading => ({
                        locale: document.documentElement.lang, width: innerWidth,
                        opacity: getComputedStyle(heading.querySelector('span > span')).opacity,
                        shift: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41,
                        overflow: document.documentElement.scrollWidth > innerWidth,
                    }));
                    assert.equal(state.locale, language);
                    assert.equal(state.opacity, '1');
                    assert.equal(state.overflow, false);
                    assert.ok(language === 'ar' ? state.shift === 0 : state.shift > 0);
                    cases.push({ engine, ...state });
                }
            }
            assert.deepEqual(runtime.errors, []);
        } finally { await runtime.close(); }
    }
    await evidence('menu-heading-heading-no-js', { cases });
});

test('Reduced-motion heading updates its final geometry across all tier boundaries', { skip: !enabled }, async () => {
    const widths = [360, 390, 639, 640, 767, 768, 1023, 1024, 1180, 1181, 1279, 1280, 1535, 1536, 1920];
    const cases = [];
    for (const engine of engines) {
        const runtime = await session(engine, { reducedMotion: 'reduce' });
        const { page } = runtime;
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                const shifts = [];
                for (const width of widths) {
                    await page.setViewportSize({ width, height: 900 });
                    await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
                    const state = await page.locator('[data-program-heading]').evaluate(heading => ({
                        width: innerWidth, shift: new DOMMatrix(getComputedStyle(heading.lastElementChild).transform).m41,
                        animations: heading.lastElementChild.getAnimations().length,
                        duration: getComputedStyle(heading.lastElementChild).transitionDuration,
                    }));
                    assert.equal(state.width, width);
                    assert.equal(state.animations, 0);
                    assert.equal(state.duration, '0s');
                    assert.ok(language === 'ar' ? state.shift === 0 : state.shift > 0);
                    fits(await page.evaluate(geometry));
                    shifts.push(Math.abs(state.shift));
                    cases.push({ engine, language, ...state });
                }
                if (language !== 'ar') assert.ok(shifts.at(-1) > shifts[0], 'responsive final pose is not frozen at the prior viewport');
                else assert.ok(shifts.every(shift => shift === 0));
            }
            assert.deepEqual(runtime.errors, []);
        } finally { await runtime.close(); }
    }
    await evidence('menu-heading-heading-tiers', { cases });
});

test('Leaving during the heading entrance cancels the delayed shift and permits reentry', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    const cases = [];
    try {
        for (const language of ['en', 'id', 'ar']) {
            await locale(page, language);
            await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'true');
            await page.locator('[data-hero]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'false');
            await page.waitForFunction(() => document.querySelector('.program__heading-clip:last-child').getAnimations().length === 0);
            assert.equal(await page.locator('.program__heading-clip').last().evaluate(clip => new DOMMatrix(getComputedStyle(clip).transform).m41), 0);
            await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-program-heading]').dataset.revealed === 'true');
            await page.waitForFunction(() => document.querySelector('.program__heading-clip:last-child').getAnimations().length === 0);
            const shift = await page.locator('.program__heading-clip').last().evaluate(clip => new DOMMatrix(getComputedStyle(clip).transform).m41);
            assert.ok(language === 'ar' ? shift === 0 : shift > 0);
            cases.push({ language, shift });
        }
        assert.deepEqual(runtime.errors, []);
        await evidence('menu-heading-heading-reentry', { cases });
    } finally { await runtime.close(); }
});
