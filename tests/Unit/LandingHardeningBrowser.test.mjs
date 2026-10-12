import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const widths = [360, 390, 639, 640, 767, 768, 1023, 1024, 1180, 1181, 1279, 1280, 1440, 1535, 1536, 1920];
const paint = page => page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
const metrics = () => {
    const selectors = ['.hero__title', '.hero__description', '.about__headline', '.program__heading',
        '.values__heading', '.gallery__header h2', '.stories__heading', '.stories__feature-copy h3',
        '.stories__feature-copy p', '.site-footer-v2__description'];
    const box = element => { const rect = element.getBoundingClientRect(); return { width: rect.width, height: rect.height }; };
    const lead = document.querySelector('[data-gallery-item]');
    const composition = document.querySelector('[data-gallery-composition]');
    return {
        language: document.documentElement.lang, direction: document.documentElement.dir,
        viewport: [innerWidth, innerHeight], overflow: document.documentElement.scrollWidth > innerWidth,
        mode: document.querySelector('[data-header]').dataset.mode,
        poster: getComputedStyle(document.querySelector('.hero__media img')).objectFit,
        columns: getComputedStyle(composition).gridTemplateColumns.split(' ').length,
        lead: box(lead), composition: box(composition),
        texts: selectors.map(selector => {
            const element = document.querySelector(selector), style = getComputedStyle(element);
            return { selector, ...box(element), size: parseFloat(style.fontSize),
                font: style.fontFamily, leading: style.lineHeight };
        }),
    };
};
function fits(state) {
    assert.equal(state.overflow, false, JSON.stringify(state));
    assert.equal(state.direction, state.language === 'ar' ? 'rtl' : 'ltr');
    assert.equal(state.columns, 3);
    assert.ok(Math.abs(state.lead.width - state.composition.width) < 1, 'static Gallery first image spans all columns');
    assert.equal(state.poster, 'cover');
    for (const text of state.texts) {
        assert.ok(text.width > 0 && text.height > 0, text.selector);
        assert.ok(text.width <= state.viewport[0] + 1, `${text.selector} respects available width`);
    }
}
for (const engine of engines) test(`Landing static/reduced layout and keyboard contracts across tiers in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { reducedMotion: 'reduce' });
    const { page } = runtime;
    const cells = [];
    try {
        for (const language of ['id', 'en', 'ar']) {
            await locale(page, language);
            for (const width of widths) {
                await page.setViewportSize({ width, height: 900 }); await paint(page);
                const state = await page.evaluate(metrics); fits(state); cells.push(state);
                assert.equal(state.mode, width >= 1181 || width >= 1024 && width > 900 ? 'desktop' : 'compact');
            }
            await page.setViewportSize({ width: 390, height: 844 }); await paint(page);
            await page.locator('[data-menu-toggle]').click();
            assert.equal(await page.locator('main').evaluate(element => element.inert), true);
            await page.locator('[data-panel] > summary').first().click();
            await page.keyboard.press('Escape');
            await page.waitForFunction(() => document.querySelector('[data-header]').dataset.mobileOpen === 'false');
            assert.equal(await page.locator('main').evaluate(element => element.inert), false);
            await page.locator('[data-menu-toggle]').click();
            await page.locator('[data-language] > summary').click();
            assert.equal(await page.locator('[data-language] dialog').evaluate(element => element.matches(':modal')), true);
            await page.keyboard.press('Escape');
            assert.equal(await page.locator('[data-language] dialog').evaluate(element => element.open), false);
            await page.keyboard.press('Escape');
            await page.locator('[data-menu-toggle]').focus();
            const focus = await page.locator('[data-menu-toggle]').evaluate(element => getComputedStyle(element).outlineStyle);
            assert.notEqual(focus, 'none');
        }
        assert.deepEqual(runtime.errors, []);
        await evidence(`landing-layout-${engine}`, { version: runtime.version, cells });
    } finally { await runtime.close(); }
});
for (const engine of engines.filter(engine => engine !== 'firefox')) test(`Redmi12-like touch/DPR layout matches PC inspection in ${engine}`, { skip: !enabled }, async () => {
    const desktop = await session(engine, { reducedMotion: 'reduce', viewport: { width: 393, height: 873 } });
    const mobile = await session(engine, { reducedMotion: 'reduce', viewport: { width: 393, height: 873 }, hasTouch: true, isMobile: true, deviceScaleFactor: 2.75 });
    const cells = [];
    try {
        for (const language of ['id', 'en', 'ar']) {
            await locale(desktop.page, language); await locale(mobile.page, language);
            const pc = await desktop.page.evaluate(metrics), phone = await mobile.page.evaluate(metrics);
            fits(pc); fits(phone);
            for (let i = 0; i < pc.texts.length; i++) {
                assert.ok(Math.abs(pc.texts[i].width - phone.texts[i].width) < 1, pc.texts[i].selector);
                assert.ok(Math.abs(pc.texts[i].height - phone.texts[i].height) < 1, `${pc.texts[i].selector} avoids phone text inflation`);
                assert.equal(pc.texts[i].size, phone.texts[i].size);
                assert.equal(pc.texts[i].font, phone.texts[i].font);
            }
            await mobile.page.locator('[data-menu-toggle]').tap();
            await mobile.page.locator('[data-panel] > summary').first().tap();
            await mobile.page.locator('[data-menu-toggle]').tap();
            await mobile.page.waitForFunction(() => document.querySelector('[data-header]').dataset.mobileOpen === 'false');
            for (const [width, height] of [[873, 393], [393, 600], [393, 873]]) {
                await mobile.page.setViewportSize({ width, height }); await paint(mobile.page);
                fits(await mobile.page.evaluate(metrics));
            }
            await mobile.page.evaluate(() => { scrollTo(0, 0); document.activeElement?.blur(); });
            await mobile.page.screenshot({ path: `${directory}/landing-${engine}-phone-${language}.png` });
            cells.push({ pc, phone });
        }
        await mobile.page.emulateMedia({ reducedMotion: 'no-preference' });
        await locale(mobile.page, 'en');
        await mobile.page.evaluate(() => {
            window.inputEvents = [];
            for (const type of ['touchstart', 'touchmove', 'touchend']) addEventListener(type, event => {
                inputEvents.push({ type, prevented: event.defaultPrevented, trusted: event.isTrusted });
            }, { passive: true });
            scrollTo(0, 100);
        });
        if (engine === 'chromium') {
            const client = await mobile.context.newCDPSession(mobile.page);
            await client.send('Input.dispatchTouchEvent', { type: 'touchStart', touchPoints: [{ x: 200, y: 650 }] });
            for (let y = 600; y >= 250; y -= 50) {
                await client.send('Input.dispatchTouchEvent', { type: 'touchMove', touchPoints: [{ x: 200, y }] });
                await mobile.page.waitForTimeout(35);
            }
            await client.send('Input.dispatchTouchEvent', { type: 'touchEnd', touchPoints: [] });
            await mobile.page.waitForFunction(() => scrollY > 150);
            const input = await mobile.page.evaluate(() => inputEvents);
            assert.ok(input.some(event => event.type === 'touchmove' && event.trusted));
            assert.ok(input.every(event => !event.prevented), 'real emulated finger gesture remains browser native');
            await evidence('landing-phone-touch', { engine, input, scroll: await mobile.page.evaluate(() => scrollY), emulated: true });
            await client.detach();
        }
        assert.deepEqual(desktop.errors, []); assert.deepEqual(mobile.errors, []);
        await evidence(`landing-phone-parity-${engine}`, { version: mobile.version, cells, emulated: true, actualRedmi12: false });
    } finally { await desktop.close(); await mobile.close(); }
});
for (const engine of engines) test(`Landing remains readable without JavaScript in ${engine}`, { skip: !enabled }, async () => {
    const runtime = await session(engine, { javaScriptEnabled: false });
    try {
        for (const language of ['id', 'en', 'ar']) {
            await Promise.all([runtime.page.waitForNavigation(), runtime.page.locator(`form[action$="/bahasa/${language}"]`).evaluate(form => form.requestSubmit())]);
            await runtime.page.evaluate(() => document.fonts.ready);
            for (const width of [360, 390, 640, 768, 1024, 1280, 1536, 1920]) {
                await runtime.page.setViewportSize({ width, height: 900 }); fits(await runtime.page.evaluate(metrics));
            }
            assert.equal(await runtime.page.locator('[data-stories-demo]').count(), 4);
            assert.equal(await runtime.page.locator('[data-footer-next-main]').getAttribute('href'), `${process.env.PROGRAM_BROWSER_URL}/galeri`);
        }
    } finally { await runtime.close(); }
});
