import test from 'node:test';
import assert from 'node:assert/strict';
import { enabled, engines, session, locale, geometry, fits, evidence, directory } from './ProgramBrowserSupport.mjs';

for (const engine of engines) {
    test(`Program background, reference corners and gentle hover in ${engine}`, { skip: !enabled }, async () => {
        const runtime = await session(engine);
        const { page } = runtime;
        const records = [];
        try {
            for (const language of ['en', 'id', 'ar']) {
                await locale(page, language);
                for (const width of [390, 768, 1440]) {
                    await page.setViewportSize({ width, height: width < 640 ? 844 : 900 });
                    await page.locator('[data-program-card]').nth(3).scrollIntoViewIfNeeded();
                    await page.waitForFunction(() => document.querySelectorAll('[data-program-card][data-revealed="true"]').length >= 4);
                    await page.waitForFunction(() => [...document.querySelectorAll('[data-program-card]')].slice(0, 4)
                        .every(card => getComputedStyle(card.querySelector('.program__card-reveal')).opacity === '1'));
                    const idle = await page.evaluate(() => {
                        const home = document.querySelector('[data-program-type-home]');
                        const viewport = document.querySelector('[data-program-type-viewport]');
                        const line = viewport.querySelector('[data-program-type-line]');
                        const header = document.querySelector('[data-program-header]').getBoundingClientRect();
                        const cards = document.querySelector('[data-program-cards]').getBoundingClientRect();
                        return { dialogOpen: document.querySelector('[data-program-dialog]').open,
                            homeOwnsType: home.contains(viewport), opacity: Number(getComputedStyle(line).opacity),
                            viewport: viewport.getBoundingClientRect().toJSON(), mask: getComputedStyle(home).maskImage,
                            root: document.querySelector('[data-program]').getBoundingClientRect().toJSON(),
                            corner: parseFloat(getComputedStyle(document.querySelector('.program__image-wrap')).borderRadius),
                            headerInset: document.documentElement.dir === 'rtl' ? cards.right - header.right : header.left - cards.left,
                            hasScrollEnd: 'onscrollend' in document };
                    });
                    assert.equal(idle.dialogOpen, false);
                    assert.equal(idle.homeOwnsType, true);
                    assert.ok(idle.opacity > 0 && idle.opacity < .25);
                    const expectedTop = Math.min(Math.max(0, idle.root.top), idle.root.bottom - idle.viewport.height);
                    assert.ok(Math.abs(idle.viewport.top - expectedTop) <= 1, 'background follows Program boundaries without covering About');
                    assert.match(idle.mask, /linear-gradient/);
                    assert.ok(idle.corner >= 8 && idle.corner <= 12);
                    assert.ok(idle.headerInset >= 0 && idle.headerInset < width * .05);
                    fits(await page.evaluate(geometry));
                    const trigger = page.locator('[data-program-open]').nth(3);
                    await trigger.hover();
                    await page.waitForFunction(() => Number(new DOMMatrix(getComputedStyle(document.querySelectorAll('.program__caption')[3]).transform).m11) > 1.02);
                    const hover = await trigger.evaluate(element => ({
                        decoration: getComputedStyle(element.querySelector('.program__name')).textDecorationLine,
                        textScale: new DOMMatrix(getComputedStyle(element.querySelector('.program__caption')).transform).m11,
                        imageScale: new DOMMatrix(getComputedStyle(element.querySelector('img')).transform).m11,
                    }));
                    assert.equal(hover.decoration, 'none');
                    assert.ok(hover.imageScale > 1 && hover.imageScale < 1.03);
                    assert.ok(hover.textScale < 1.04);
                    await page.mouse.move(0, 0);
                    await page.waitForFunction(() => getComputedStyle(document.querySelectorAll('.program__caption')[3]).transform === 'none');
                    records.push({ language, width, idle, hover });
                    if (engine === 'chromium' && language === 'en' && width === 1440) await page.screenshot({ path: `${directory}/program-feedback-field.png` });
                    if (engine === 'chromium' && language === 'en' && width === 390) await page.screenshot({ path: `${directory}/program-feedback-phone.png` });
                    if (engine === 'chromium' && language === 'ar' && width === 1440) await page.screenshot({ path: `${directory}/program-feedback-ar.png` });
                }
            }
            assert.deepEqual(runtime.errors, []);
            await evidence(`program-feedback-visual-${engine}`, { version: runtime.version, records });
        } finally { await runtime.close(); }
    });
}

test('Program detail color fades during kinetic open and close', { skip: !enabled }, async () => {
    const runtime = await session('chromium');
    const { page } = runtime;
    try {
        await page.locator('[data-program-open]').first().click();
        const partial = await page.waitForFunction(() => {
            const dialog = document.querySelector('[data-program-dialog]');
            const opacity = Number(getComputedStyle(dialog, '::before').opacity);
            return dialog.dataset.phase === 'detail' && opacity > .05 && opacity < .95 ? opacity : false;
        });
        assert.ok(await partial.jsonValue());
        await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'detail');
        await page.locator('[data-program-back]:visible').click();
        await page.waitForFunction(() => {
            const dialog = document.querySelector('[data-program-dialog]');
            const opacity = Number(getComputedStyle(dialog, '::before').opacity);
            return dialog.dataset.phase === 'opening' && opacity > .05 && opacity < .95;
        });
        await page.waitForFunction(() => document.querySelector('[data-program]').dataset.programState === 'idle');
        await evidence('program-feedback-color', { openingAndClosingFade: true });
    } finally { await runtime.close(); }
});
