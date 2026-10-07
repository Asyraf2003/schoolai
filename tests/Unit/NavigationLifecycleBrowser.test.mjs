import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence } from './ProgramBrowserSupport.mjs';

const widths = [360, 390, 639, 640, 767, 768, 1023, 1024, 1180, 1181, 1279, 1280, 1535, 1536, 1920];
const profiles = [...widths.map(width => [width, width < 640 ? 844 : 900]), [1024, 1400], [1180, 1400], [1181, 1400]];
const settled = page => page.waitForFunction(() => [...document.querySelectorAll('[data-panel]')]
    .every(group => !['closing', 'opening'].includes(group.dataset.motion)));

for (const engine of engines) {
    test(`Navigation tier boundaries, keyboard and finite reversal in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { reducedMotion: 'reduce' });
        const { page } = runtime;
        const cases = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const [width, height] of profiles) {
                    await page.setViewportSize({ width, height });
                    const desktop = width >= 1181 || (width >= 1024 && width > height);
                    await page.waitForFunction(desktop => document.querySelector('[data-header]').dataset.mode === (desktop ? 'desktop' : 'compact'), desktop);
                    if (!desktop) await page.locator('[data-menu-toggle]').click();
                    const trigger = page.locator('[data-panel] > summary').first();
                    await trigger.focus(); await page.keyboard.press('Enter'); await settled(page);
                    assert.equal(await page.locator('[data-panel]').first().getAttribute('data-motion'), 'open');
                    const state = await page.evaluate(() => {
                        const header = document.querySelector('[data-header]');
                        const trigger = header.querySelector('[data-panel] summary');
                        const text = trigger.querySelector('.menu-roll').getBoundingClientRect();
                        const arrow = trigger.querySelector('svg').getBoundingClientRect();
                        return { width: innerWidth, height: innerHeight, mode: header.dataset.mode,
                            overflow: document.documentElement.scrollWidth > innerWidth,
                            headerOverflow: header.scrollWidth > header.clientWidth,
                            arrowDelta: Math.abs(text.y + text.height / 2 - arrow.y - arrow.height / 2),
                            color: getComputedStyle(trigger).color, family: getComputedStyle(trigger).fontFamily,
                            media: getComputedStyle(header.querySelector('.site-header__media')).display,
                        };
                    });
                    assert.equal(state.overflow, false, JSON.stringify({ language, state }));
                    assert.equal(state.headerOverflow, false, JSON.stringify({ language, state }));
                    assert.ok(state.arrowDelta < 1, JSON.stringify({ language, state }));
                    assert.equal(state.color, 'rgb(17, 17, 17)');
                    assert.match(state.family, language === 'ar' ? /Cairo/ : /Inter/);
                    assert.equal(state.media === 'none', width < 768);
                    await page.keyboard.press('Escape'); await settled(page);
                    if (!desktop) await page.waitForFunction(() => document.querySelector('[data-header]').dataset.mobileOpen === 'false');
                    assert.equal(await page.evaluate(() => document.documentElement.hasAttribute('data-landing-menu-open')), false);
                    assert.equal(await (desktop ? trigger : page.locator('[data-menu-toggle]')).evaluate(element => element === document.activeElement), true);
                    await page.evaluate(() => document.activeElement.blur());
                    cases.push({ language, ...state });
                }
            }
            await page.setViewportSize({ width: 1440, height: 900 });
            await page.emulateMedia({ reducedMotion: 'no-preference' });
            const first = page.locator('[data-panel] > summary').nth(0);
            const second = page.locator('[data-panel] > summary').nth(1);
            await first.click();
            await page.waitForFunction(() => document.querySelector('[data-panel]').dataset.motion === 'opening');
            await first.click(); await first.click(); await second.click(); await first.click();
            await settled(page);
            assert.equal(await page.locator('[data-panel][open]').count(), 1);
            assert.equal(await page.locator('[data-panel]').first().getAttribute('data-motion'), 'open');
            assert.equal(await page.locator('[data-panel]').nth(1).locator('.site-header__panel').evaluate(panel => panel.inert), true);
            await page.setViewportSize({ width: 390, height: 844 });
            await page.waitForFunction(() => document.querySelector('[data-header]').dataset.mode === 'compact');
            await settled(page);
            assert.equal(await page.locator('[data-panel][open]').count(), 0);
            assert.equal(await page.evaluate(() => document.documentElement.hasAttribute('data-landing-menu-open')), false);
            assert.deepEqual(runtime.errors, []);
            await evidence(`menu-heading-nav-lifecycle-${engine}`, { version: runtime.version, cases, rapidReversal: true, resizeSettled: true });
        } finally { await runtime.close(); }
    });
}

test('Header field and main label tone agree during actual state changes', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    const samples = [];
    try {
        for (const language of ['en', 'id', 'ar']) {
            await locale(page, language);
            await page.locator('[data-hero]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-header]').dataset.scrolled === 'false');
            await page.evaluate(() => {
                window.navToneFrames = [];
                window.navToneObserver = new MutationObserver(() => {
                    const header = document.querySelector('[data-header]');
                    const field = getComputedStyle(header, '::before');
                    const labels = [...header.querySelectorAll('.site-header__items > li > a, .site-header__group > summary, .site-header__language > summary')];
                    const glyphs = labels.flatMap(label => [...label.querySelectorAll('.menu-roll > span')]);
                    window.navToneFrames.push({ color: getComputedStyle(header).color, background: field.backgroundColor,
                        image: field.backgroundImage, layer: field.zIndex, isolation: getComputedStyle(header).isolation,
                        colors: labels.map(label => getComputedStyle(label).color), glyphColors: glyphs.map(glyph => getComputedStyle(glyph).color),
                        strokes: labels.flatMap(label => [...label.querySelectorAll('svg')].map(icon => getComputedStyle(icon).stroke)), surface: header.dataset.surface,
                        scrolled: header.dataset.scrolled, open: header.dataset.open });
                });
                window.navToneObserver.observe(document.querySelector('[data-header]'), { attributes: true });
            });
            await page.locator('[data-panel] > summary').first().click(); await settled(page);
            await page.keyboard.press('Escape'); await settled(page);
            for (const y of [48, 49, 30, 24, 0]) {
                await page.evaluate(value => scrollTo(0, value), y);
                await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
            }
            await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
            await page.mouse.wheel(0, -120);
            await page.waitForFunction(() => document.querySelector('[data-header]').dataset.scrolled === 'true' && document.querySelector('[data-header]').dataset.concealed === 'false');
            await page.locator('[data-panel] > summary').first().click(); await settled(page);
            await page.waitForFunction(() => [...document.querySelectorAll('.menu-roll > span')].every(span => span.getAnimations().length === 0));
            await page.mouse.move(-20, -20);
            await page.screenshot({ path: `docs2/proof/menu-heading-light-open-${language}.png` });
            await page.locator('[data-hero]').scrollIntoViewIfNeeded();
            await page.waitForFunction(() => document.querySelector('[data-header]').dataset.scrolled === 'false');
            await page.keyboard.press('Escape'); await settled(page);
            const frames = await page.evaluate(() => { window.navToneObserver.disconnect(); return window.navToneFrames; });
            assert.ok(frames.some(frame => frame.open === 'false' && frame.image !== 'none'));
            assert.ok(frames.some(frame => frame.scrolled === 'true'));
            assert.ok(frames.filter(frame => frame.open === 'true').every(frame => frame.surface === 'light'));
            assert.ok(frames.some(frame => frame.open === 'true' && frame.surface === 'light'));
            for (const frame of frames) {
                const expected = frame.background === 'rgb(255, 255, 255)' ? 'rgb(17, 17, 17)' : 'rgb(255, 255, 255)';
                assert.equal(frame.color, expected, JSON.stringify(frame));
                assert.ok(frame.colors.every(color => color === expected), JSON.stringify(frame));
                assert.ok(frame.glyphColors.every(color => color === expected), JSON.stringify(frame));
                assert.ok(frame.strokes.every(color => color === expected), JSON.stringify(frame));
                assert.equal(frame.layer, '-1');
                assert.equal(frame.isolation, 'isolate');
                assert.equal(frame.surface, expected === 'rgb(17, 17, 17)' ? 'light' : 'hero');
            }
            samples.push({ language, frames });
        }
        assert.deepEqual(runtime.errors, []);
        await evidence('menu-heading-tone-states', { version: runtime.version, samples });
    } finally { await runtime.close(); }
});

test('Navigation keeps a readable native fallback without page JavaScript', { skip: !enabled }, async () => {
    const cases = [];
    for (const engine of ['chromium', 'webkit']) {
        const runtime = await session(engine, { javaScriptEnabled: false });
        const { page } = runtime;
        try {
            for (const language of ['en', 'id', 'ar']) {
                await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
                    page.locator(`form[action$="/bahasa/${language}"]`).evaluate(form => form.requestSubmit())]);
                await page.evaluate(() => document.fonts.ready);
                for (const width of [390, 768, 1440]) {
                    await page.setViewportSize({ width, height: 900 });
                    const state = await page.locator('[data-header]').evaluate(header => ({
                        enhanced: header.hasAttribute('data-enhanced'), background: getComputedStyle(header).backgroundColor,
                        color: getComputedStyle(header.querySelector('[data-panel] summary')).color,
                        field: getComputedStyle(header, '::before').content,
                    }));
                    assert.equal(state.enhanced, false);
                    assert.equal(state.field, 'none');
                    assert.equal(state.background, 'rgb(7, 27, 24)');
                    assert.equal(state.color, 'rgb(255, 255, 255)');
                    const trigger = page.locator('[data-panel] summary').first();
                    await trigger.click();
                    assert.equal(await page.locator('[data-panel]').first().evaluate(group => group.open), true);
                    assert.equal(await page.locator('[data-panel]').first().locator('.site-header__links a').first().isVisible(), true);
                    await trigger.click();
                    cases.push({ engine, language, width, ...state });
                }
            }
            assert.deepEqual(runtime.errors, []);
        } finally { await runtime.close(); }
    }
    await evidence('menu-heading-nav-no-js', { cases });
});
