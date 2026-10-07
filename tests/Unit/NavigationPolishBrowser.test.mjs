import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, evidence, directory } from './ProgramBrowserSupport.mjs';

const settled = page => page.waitForFunction(() => [...document.querySelectorAll('[data-panel]')]
    .every(group => ['closed', 'open'].includes(group.dataset.motion)));
const rollSettled = page => page.waitForFunction(() => [...document.querySelectorAll('.menu-roll > span')]
    .every(span => span.getAnimations().length === 0));
const tone = () => {
    const header = document.querySelector('[data-header]');
    const field = getComputedStyle(header, '::before');
    return { scrolled: header.dataset.scrolled, surface: header.dataset.surface,
        color: getComputedStyle(header.querySelector('[data-panel] summary')).color,
        background: field.background, layer: field.zIndex, content: field.content };
};

for (const engine of engines) {
    test(`Navigation tone, label geometry,720ms panels and retained media in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const cases = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const width of [390, 768, 1440]) {
                    await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                    await page.locator('[data-hero]').scrollIntoViewIfNeeded();
                    await page.waitForFunction(() => document.querySelector('[data-header]').dataset.scrolled === 'false');
                    const desktop = await page.locator('[data-header]').getAttribute('data-mode') === 'desktop';
                    if (width >= 768) await page.waitForFunction(() => [...document.querySelectorAll('.site-header__media')].every(frame => frame.dataset.mediaState === 'ready'));
                    if (!desktop) await page.locator('[data-menu-toggle]').click();
                    await rollSettled(page);
                    const trigger = page.locator('[data-panel] > summary').first();
                    const font = await trigger.evaluate(element => getComputedStyle(element).fontFamily);
                    assert.match(font, language === 'ar' ? /Cairo/ : /Inter/);
                    const urls = await page.locator('[data-menu-media]').evaluateAll(images => images.map(image => image.src));
                    const counts = urls.map(url => runtime.requests.filter(request => request === url).length);
                    const image = await page.locator('[data-menu-media]').first().elementHandle();
                    await trigger.click();
                    await page.waitForFunction(() => document.querySelector('[data-panel]').dataset.motion === 'opening');
                    const durations = await page.locator('[data-panel]').first().evaluate(group => {
                        const target = document.querySelector('[data-header]').dataset.mode === 'desktop' ? group.querySelector('.site-header__panel') : group;
                        return target.getAnimations().map(animation => animation.effect.getTiming().duration);
                    });
                    assert.deepEqual(durations, [720]);
                    await settled(page); await rollSettled(page);
                    const opened = await page.evaluate(tone);
                    assert.notEqual(opened.content, 'none');
                    assert.equal(opened.layer, '-1', 'field is beneath the content in its isolated Header');
                    assert.equal(opened.surface, 'light');
                    assert.equal(opened.color, 'rgb(17, 17, 17)');
                    assert.match(opened.background, /rgb\(255, 255, 255\)/);
                    if (width >= 768) assert.equal(await image.evaluate(img => img.complete && img.naturalWidth > 0 && img.parentElement.dataset.ready === 'true'), true);
                    const alignment = await trigger.evaluate(summary => {
                        const textBox = summary.querySelector('.menu-roll').getBoundingClientRect();
                        const arrow = summary.querySelector('svg').getBoundingClientRect();
                        return { textCenter: textBox.y + textBox.height / 2, arrowCenter: arrow.y + arrow.height / 2 };
                    });
                    assert.ok(Math.abs(alignment.textCenter - alignment.arrowCenter) < 1, JSON.stringify({ language, width, alignment }));
                    await trigger.click(); await settled(page);
                    await trigger.click(); await settled(page);
                    assert.equal(await image.evaluate(img => img === document.querySelector('[data-menu-media]')), true);
                    if (width >= 768) assert.deepEqual(urls.map(url => runtime.requests.filter(request => request === url).length), counts, 'repeat opening never refetches menu media');
                    if (engine === 'chromium' && width === 1440) await page.screenshot({ path: `${directory}/menu-heading-hero-open-${language}.png` });
                    await page.keyboard.press('Escape'); await settled(page);
                    if (!desktop) await page.waitForFunction(() => document.querySelector('[data-header]').dataset.mobileOpen === 'false');
                    if (desktop) {
                        await page.locator('[data-program-header]').scrollIntoViewIfNeeded();
                        await page.mouse.wheel(0, -120);
                        await page.waitForFunction(() => document.querySelector('[data-header]').dataset.scrolled === 'true' && document.querySelector('[data-header]').dataset.concealed === 'false');
                        await trigger.click(); await settled(page);
                        const section = await page.evaluate(tone);
                        assert.equal(section.color, 'rgb(17, 17, 17)');
                        assert.equal(section.surface, 'light');
                        assert.match(section.background, /rgb\(255, 255, 255\)/);
                        await page.keyboard.press('Escape'); await settled(page);
                        cases.push({ language, width, font, alignment, durations, opened, section, counts });
                    } else cases.push({ language, width, font, alignment, durations, opened, counts });
                    assert.equal(await page.evaluate(() => document.documentElement.scrollWidth > innerWidth), false);
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`menu-heading-navigation-${engine}`, { version: runtime.version, cases });
        } finally { await runtime.close(); }
    });
}
