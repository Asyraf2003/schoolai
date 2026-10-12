import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const paint = page => page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
const metrics = () => {
    const selectors = ['.skip-link', '.hero__title', '.hero__eyebrow', '.hero__description',
        '.site-header__links strong', '.site-header__links small', '.about__headline',
        '.program__type-line', '.gallery__header h2', '.stories__heading', '.stories__feature-copy h3',
        '.stories__feature-copy p', '.site-footer-v2__description', '.site-footer-v2__social a',
        '.site-footer-v2__partners h2', '.site-footer-v2__partner-grid a',
        '.site-footer-v2__next-eyebrow', '.site-footer-v2__next-direction'];
    return {
        locale: document.documentElement.lang, dir: document.documentElement.dir,
        viewport: [innerWidth, innerHeight], overflow: document.documentElement.scrollWidth > innerWidth,
        text: selectors.map(selector => {
            const element = document.querySelector(selector), style = getComputedStyle(element);
            return { selector, font: style.fontFamily, tracking: style.letterSpacing,
                leading: parseFloat(style.lineHeight) / parseFloat(style.fontSize) };
        }),
        arrows: ['.site-footer-v2__next-arrow', '.site-footer-v2__social-arrow'].map(selector => {
            const style = getComputedStyle(document.querySelector(selector));
            return new DOMMatrix(style.transform).m11 * (style.scale === 'none' ? 1 : parseFloat(style.scale));
        }),
        fillOrigin: getComputedStyle(document.querySelector('.site-footer-v2__next-line-fill')).transformOrigin,
        stories: [...document.querySelectorAll('[data-stories-feature]')].map(element => element.querySelector('h3').getAttribute('aria-label')),
    };
};
function verify(state) {
    assert.equal(state.overflow, false, JSON.stringify(state.viewport));
    assert.equal(state.dir, state.locale === 'ar' ? 'rtl' : 'ltr');
    assert.equal(state.stories.length, 4);
    if (state.locale === 'ar') {
        for (const item of state.text) {
            assert.match(item.font, /Cairo/, item.selector);
            assert.equal(item.tracking, 'normal', item.selector);
            assert.ok(item.leading >= 1.25 - 1e-5, `${item.selector}: ${item.leading}`);
        }
        assert.deepEqual(state.arrows, [-1, -1]);
        assert.ok(parseFloat(state.fillOrigin) > 0, 'RTL progress grows from the right');
    } else {
        assert.match(state.text[1].font, /Inter/);
        assert.equal(state.text[1].tracking === 'normal', false, 'Latin display tracking preserved');
        assert.deepEqual(state.arrows, [1, 1]);
        assert.equal(parseFloat(state.fillOrigin), 0);
    }
}

for (const engine of engines) {
    test(`Desktop Arabic typography, direction and locale lifecycle in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { reducedMotion: 'reduce' });
        const { page } = runtime;
        const cells = [];
        try {
            for (const language of ['id', 'en', 'ar']) {
                await locale(page, language);
                for (const width of [1180, 1181, 1279, 1280, 1440, 1535, 1536, 1920]) {
                    await page.setViewportSize({ width, height: 900 }); await paint(page);
                    const state = await page.evaluate(metrics); verify(state); cells.push(state);
                }
            }
            await page.setViewportSize({ width: 1440, height: 900 });
            const summary = page.locator('[data-panel] > summary').first();
            await summary.click();
            const menu = await page.locator('.site-header__panel').first().evaluate(panel => {
                const media = panel.querySelector('figure').getBoundingClientRect();
                const links = panel.querySelector('.site-header__links').getBoundingClientRect();
                return { media: media.x, links: links.x, font: getComputedStyle(panel.querySelector('strong')).fontFamily };
            });
            assert.ok(menu.media > menu.links, 'Arabic menu media follows mirrored grid');
            assert.match(menu.font, /Cairo/);
            await page.keyboard.press('Escape');
            assert.equal(await page.locator('[data-panel]').first().getAttribute('open'), null);
            await page.mouse.move(-20, -20);
            await page.evaluate(() => document.activeElement.blur());
            for (const target of ['[data-stories]', '.stories__feature', '.site-footer-v2__white']) {
                await page.locator(target).first().evaluate(element => scrollTo(0, element.getBoundingClientRect().top + scrollY));
                await paint(page);
                await page.screenshot({ path: `${directory}/desktop-ar-${engine}-${target.replace(/[^a-z]/g, '')}.png` });
            }
            const story = page.locator('.stories__feature-copy').first();
            const bounds = await story.boundingBox();
            assert.ok(bounds.x < 1 && bounds.width < 1440 / 2, 'Arabic article copy stays in accepted left half');
            await page.locator('[data-footer-preview]').first().focus();
            await paint(page); verify(await page.evaluate(metrics));
            const social = await page.locator('.site-footer-v2__social-label').first().evaluate(element => new DOMMatrix(getComputedStyle(element).transform).m41);
            assert.equal(social, 0, 'Reduced motion keeps labels still on keyboard focus');
            await page.locator('[data-footer-next-main]').focus();
            await paint(page); verify(await page.evaluate(metrics));
            await page.screenshot({ path: `${directory}/desktop-ar-${engine}-next.png` });
            await page.locator('.site-footer-v2__description').evaluate(element => element.style.fontSize = `${parseFloat(getComputedStyle(element).fontSize) * 2}px`);
            await paint(page); verify(await page.evaluate(metrics));
            await page.locator('.site-footer-v2__description').evaluate(element => element.style.removeProperty('font-size'));
            await page.setViewportSize({ width: 1440, height: 500 }); await paint(page);
            verify(await page.evaluate(metrics));
            await page.setViewportSize({ width: 1440, height: 900 });
            for (const value of ['id', 'ar', 'en', 'ar']) { await locale(page, value); verify(await page.evaluate(metrics)); }
            await page.emulateMedia({ reducedMotion: 'no-preference' });
            await page.reload(); await page.evaluate(() => document.fonts.ready);
            await paint(page); verify(await page.evaluate(metrics));
            await page.locator('[data-footer-preview]').first().focus();
            await page.waitForFunction(() => new DOMMatrix(getComputedStyle(document.querySelector('.site-footer-v2__social-label')).transform).m41 < -1);
            verify(await page.evaluate(metrics));
            await page.locator('[data-footer-next-main]').focus();
            await paint(page); verify(await page.evaluate(metrics));
            await page.evaluate(() => { document.activeElement.blur(); scrollTo(0, 0); });
            await page.locator('[data-language] > summary').click();
            await Promise.all([page.waitForNavigation({ waitUntil: 'domcontentloaded' }), page.locator('[data-locale="en"]').click()]);
            await page.evaluate(() => document.fonts.ready); verify(await page.evaluate(metrics));
            assert.deepEqual(runtime.errors, []);
            await evidence(`desktop-ar-${engine}`, { version: runtime.version, cells, menu, errors: runtime.errors });
        } finally { await runtime.close(); }
    });
    test(`Desktop Arabic semantic fallback without JavaScript in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine, { javaScriptEnabled: false });
        try {
            await Promise.all([runtime.page.waitForNavigation(), runtime.page.locator('form[action$="/bahasa/ar"]').evaluate(form => form.requestSubmit())]);
            await runtime.page.evaluate(() => document.fonts.ready);
            const state = await runtime.page.evaluate(metrics); verify(state);
            assert.equal(await runtime.page.locator('[data-stories-demo]').count(), 4);
            assert.equal(await runtime.page.locator('[data-stories-heading]').getAttribute('data-heading-state'), null);
        } finally { await runtime.close(); }
    });
}
